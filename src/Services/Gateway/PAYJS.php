<?php

namespace App\Services\Gateway;

use App\Services\Auth;
use App\Models\Order;
use App\Models\Setting;
use Slim\Http\ServerRequest;
use Slim\Http\Response;

class PAYJS
{
    private $appSecret;
    private $gatewayUri;
    /**
     * 签名初始化
     * @param merKey    签名密钥
     */
    public function __construct($appSecret)
    {
        $this->appSecret = Setting::obtain('payjs_key');
        $this->gatewayUri = 'https://payjs.cn/api/';
    }
    /**
     * @name    准备签名/验签字符串
     */
    public function prepareSign($data)
    {
        $data['mchid'] = Setting::obtain('payjs_mchid');
        $data = array_filter($data);
        ksort($data);
        return http_build_query($data);
    }
    /**
     * @name    生成签名
     * @param sourceData
     * @return    签名数据
     */
    public function sign($data)
    {
        return strtoupper(md5(urldecode($data) . '&key=' . $this->appSecret));
    }
    /*
     * @name    验证签名
     * @param   signData 签名数据
     * @param   sourceData 原数据
     * @return
     */
    public function verify($data, $signature)
    {
        $mySign = $this->sign($data);
        return $mySign === $signature;
    }
    public function post($data, $type = 'pay')
    {
        if ($type == 'pay') {
            $this->gatewayUri .= 'cashier';
        } elseif ($type == 'refund') {
            $this->gatewayUri .= 'refund';
        } else {
            $this->gatewayUri .= 'check';
        }
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $this->gatewayUri);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        $data = curl_exec($curl);
        curl_close($curl);
        return $data;
    }
    public function ZeroPay($type, $price, $shopinfo, $order_id=0)
    {
        if ( isset($shopinfo['telegram']) ) {
            $user = $shopinfo['telegram']['user'];
        } else {
            $user = Auth::getUser();
        }

        if ($order_id === 0) {
            $pl             = new Order();
            $pl->userid     = $user->id;
            $pl->total      = $price;
            $pl->datetime   = time();
            $pl->tradeno    = self::generateGuid();
            if ($shopinfo) {
                if ( isset($shopinfo['telegram']) ) {
                    unset($shopinfo['telegram']['user']);
                }
                $pl->shop   = json_encode($shopinfo);
            }
            $pl->save();
        } else {
            $pl = Order::find($order_id);
            if ($pl->status === 1){
                return ['ret' => 0, 'msg' => "该订单已交易完成"];
            }
        }

        $data = [
            'mchid'         => Setting::obtain('payjs_mchid'),
            'out_trade_no'  => $pl->tradeno,
            'total_fee'     => (float) $pl->total * 100,
            'notify_url'    => Setting::obtain('website_url') . '/payment/notify/payjs',
            'callback_url'  => Setting::obtain('website_url') . '/payment/return?tradeno='.$pl->tradeno,
        ];
        $params         = $this->prepareSign($data);
        $data['sign']   = $this->sign($params);
        $url            = 'https://payjs.cn/api/cashier?' . http_build_query($data);

        return ['ret' => 1, 'url' => $url, 'tradeno' => $pl->tradeno, 'type' => 'qrcode'];
    }
    public function query($tradeNo)
    {
        $data['payjs_order_id'] = $tradeNo;
        $params = $this->prepareSign($data);
        $data['sign'] = $this->sign($params);
        return json_decode($this->post($data, $type = 'query'), true);
    }
    public function notify(ServerRequest $request, Response $response, array $args)
    {
        $data = $_POST;

        if ($data['return_code'] == 1) {
            // 验证签名
            $in_sign = $data['sign'];
            unset($data['sign']);
            $data = array_filter($data);
            ksort($data);
            $sign = strtoupper(md5(urldecode(http_build_query($data) . '&key=' . $this->appSecret)));
            $resultVerify = $sign ? true : false;

            //$str_to_sign = $this->prepareSign($data);
            //$resultVerify = $this->verify($str_to_sign, $request->getParam('sign'));

            if ($resultVerify) {
                // 验重
                $p = Order::where('tradeno', '=', $data['out_trade_no'])->first();
                $money = $p->total;
                if ($p->status != 1) {
                    $this->postPayment($data['out_trade_no'], '微信支付');
                    echo 'SUCCESS';
                } else {
                    echo 'ERROR';
                }
            } else {
                echo 'FAIL2';
            }
        } else {
            echo 'FAIL1';
        }
    }
    public function refund($merchantTradeNo)
    {
        $data['payjs_order_id'] = $merchantTradeNo;
        $params = $this->prepareSign($data);
        $data['sign'] = $this->sign($params);
        return $this->post($data, 'refund');
    }
    public function getStatus(ServerRequest $request, Response $response, array $args)
    {
        $return = [];
        $p = Order::where('tradeno', $_POST['pid'])->first();
        $return['ret'] = 1;
        $return['result'] = $p->status;
        return json_encode($return);
    }
}

<?php

declare(strict_types=1);

use App\Controllers\ZeroController;
use App\Controllers\HomeController;
use Slim\App as SlimApp;
use App\Middleware\{Guest, Admin, Auth, WebAPI};
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (SlimApp $app) {
    // Home
    $app->get('/',          App\Controllers\HomeController::class . ':index');
    $app->get('/404',       App\Controllers\HomeController::class . ':page404');
    $app->get('/405',       App\Controllers\HomeController::class . ':page405');
    $app->get('/500',       App\Controllers\HomeController::class . ':page500');

    $app->post('/notify',               App\Controllers\HomeController::class . ':notify');

    // Telegram
    $app->post('/telegram_callback',    App\Controllers\HomeController::class . ':telegram');

    // User Center
    $app->group('/user', function (Group $group) {
        $group->get('',                          App\Controllers\UserController::class . ':index');
        $group->get('/',                         App\Controllers\UserController::class . ':index');

        $group->post('/getusertrafficinfo',      App\Controllers\UserController::class . ':getUserTrafficUsage');
        $group->get('/tutorial',                 App\Controllers\UserController::class . ':tutorial');
        $group->get('/referral',                 App\Controllers\UserController::class . ':referral');
        $group->get('/profile',                  App\Controllers\UserController::class . ':profile');
        $group->get('/record',                   App\Controllers\UserController::class . ':record');
        $group->get('/ban',                      App\Controllers\UserController::class . ':ban');
        // 订单系统
        $group->get('/order',                           App\Controllers\OrderController::class . ':order');
        $group->get('/order/{no}',                      App\Controllers\OrderController::class . ':orderDetails');
        $group->post('/order/create_order/{type}',       App\Controllers\OrderController::class . ':createOrder');
        $group->post('/order/pay_order',                App\Controllers\OrderController::class . ':processOrder');
        $group->post('/verify_coupon',            App\Controllers\OrderController::class . ':verifyCoupon');

        $group->get('/disable',                  App\Controllers\UserController::class . ':disable');
        $group->get('/node',                     App\Controllers\User\NodeController::class . ':node');

        $group->get('/product',                  App\Controllers\User\ProductController::class . ':product');
        $group->post('/product/getinfo',      App\Controllers\User\ProductController::class . ':getProductInfo');
        $group->post('/product/renewal',      App\Controllers\User\ProductController::class . ':renewalProduct');

        $group->get('/ticket',                   App\Controllers\User\TicketController::class . ':ticketIndex');
        $group->post('/ticket/create',            App\Controllers\User\TicketController::class . ':createTicket');
        $group->get('/ticket/view/{id}',         App\Controllers\User\TicketController::class . ':ticketViewIndex');
        $group->put('/ticket/update',              App\Controllers\User\TicketController::class . ':updateTicket');
        
        $group->post('/update_profile/{type}',    App\Controllers\UserController::class . ':updateProfile');
        $group->post('/send',                    App\Controllers\AuthController::class . ':sendVerify');
        $group->post('/mail',                    App\Controllers\UserController::class . ':updateMail');
        $group->post('/enable_notify',           App\Controllers\UserController::class . ':enableNotify');
        $group->get('/trafficlog',               App\Controllers\UserController::class . ':trafficLog');
        $group->post('/kill',                    App\Controllers\UserController::class . ':handleKill');
        $group->get('/logout',                   App\Controllers\UserController::class . ':logout');
        
        

        // getUserAllURL
        $group->get('/getUserAllURL',            App\Controllers\UserController::class . ':getUserAllURL');

        $group->post('/code/f2fpay',             App\Services\Payment::class . ':purchase');


        # Zero
        
        $group->get('/nodeinfo/{id}',            App\Controllers\ZeroController::class . ':nodeInfo');
        $group->get('/money',                    App\Controllers\ZeroController::class . ':getmoney');
        $group->get('/ajax_data/table/{name}',   App\Controllers\ZeroController::class . ':ajaxDatatable');
        $group->get('/ajax_data/chart/{name}',   App\Controllers\ZeroController::class . ':ajaxDataChart');
        $group->delete('/ajax_data/delete',      App\Controllers\ZeroController::class . ':ajaxDatatableDelete');
        $group->post('/withdraw_commission',          App\Controllers\ZeroController::class . ':withdrawCommission');
        $group->post('/withdraw_account_setting',     App\Controllers\ZeroController::class . ':withdrawAccountSettings');

        // Agent
        $group->get('/agent/ajax_data/table/{name}',        App\Zero\Agent::class . ':ajaxDatatable');
        $group->get('/agent/ajax_data/chart/{name}',        App\Zero\Agent::class . ':ajaxChart');
        $group->post('/agent/withdraw_commission',          App\Zero\Agent::class . ':withdraw');
        $group->post('/agent/withdraw_account_setting',     App\Zero\Agent::class . ':withdrawAccountSettings');
        $group->post('/agent_data/process/{name}',          App\Zero\Agent::class . ':ajaxDatatableProcess');
    })->add(new Auth());

    $app->group('/payment', function (Group $group) {
        //Reconstructed Payment System
        $group->get('/return',                  App\Services\Payment::class . ':return')->add(new Auth());
        $group->get('/notify',                  App\Services\Payment::class . ':notify');
        $group->post('/notify',                 App\Services\Payment::class . ':notify');
        $group->get('/notify/{type}',           App\Services\Payment::class . ':notify');
        $group->post('/notify/{type}',          App\Services\Payment::class . ':notify');
        $group->get('/notify/{type}/{method}',  App\Services\Payment::class . ':notify');
        $group->post('/notify/{type}/{method}', App\Services\Payment::class . ':notify');
        $group->post('/status',                 App\Services\Payment::class . ':getStatus');
    });

    // Auth
    $app->group('/auth', function (Group $group) {
        $group->get('/signin',           App\Controllers\AuthController::class . ':signInIndex');
        $group->post('/signin',           App\Controllers\AuthController::class . ':signInHandle');
        $group->get('/signup',           App\Controllers\AuthController::class . ':signUpIndex');
        $group->post('/signup',        App\Controllers\AuthController::class . ':signUpHandle');
        $group->post('/send',            App\Controllers\AuthController::class . ':sendVerify');
        $group->get('/logout',           App\Controllers\AuthController::class . ':logout');

    })->add(new Guest());

    // Password
    $app->group('/password', function (Group $group) {
        $group->get('/reset',            App\Controllers\PasswordController::class . ':reset');
        $group->post('/reset',           App\Controllers\PasswordController::class . ':handleReset');
        $group->get('/token/{token}',    App\Controllers\PasswordController::class . ':token');
        $group->post('/token/{token}',   App\Controllers\PasswordController::class . ':handleToken');
    })->add(new Guest());

    // Admin
    $app->group('/admin', function (Group $group) {
        $group->get('',                          App\Controllers\AdminController::class . ':index');
        $group->get('/',                         App\Controllers\AdminController::class . ':index');

        // Node Mange
        $group->get('/node',                        App\Controllers\Admin\NodeController::class . ':index');
        $group->get('/node/create',                 App\Controllers\Admin\NodeController::class . ':createNodeIndex');
        $group->post('/node/create',                App\Controllers\Admin\NodeController::class . ':createNode');
        $group->get('/node/update/{id}',            App\Controllers\Admin\NodeController::class . ':updateNodeIndex');
        $group->put('/node/update',                 App\Controllers\Admin\NodeController::class . ':updateNode');
        $group->delete('/node/delete',              App\Controllers\Admin\NodeController::class . ':deleteNode');
        $group->post('/node/ajax',                  App\Controllers\Admin\NodeController::class . ':nodeAjax');
        $group->put('/node/update/status',          App\Controllers\Admin\NodeController::class . ':updateNodeStatus');

        //ticket
        $group->get('/ticket',                       App\Controllers\Admin\TicketController::class . ':ticketIndex');
        $group->post('/ticket/create',               App\Controllers\Admin\TicketController::class . ':createTicket');
        $group->get('/ticket/view/{id}',             App\Controllers\Admin\TicketController::class . ':ticketViewIndex');
        $group->put('/ticket/update',                App\Controllers\Admin\TicketController::class . ':updateTicket');
        $group->post('/ticket/ajax',                 App\Controllers\Admin\TicketController::class . ':ticketAjax');
        $group->delete('/ticket/delete',             App\Controllers\Admin\TicketController::class . ':deleteTicket');
        $group->put('/ticket/close',                 App\Controllers\Admin\TicketController::class . ':closeTicket');

        // Product Mange
        $group->get('/product',                     App\Controllers\Admin\ProductController::class . ':index');
        $group->post('/product/ajax',               App\Controllers\Admin\ProductController::class . ':productAjax');
        $group->get('/product/create',              App\Controllers\Admin\ProductController::class . ':createProductIndex');
        $group->post('/product/create',                    App\Controllers\Admin\ProductController::class . ':createProduct');
        $group->get('/product/update/{id}',           App\Controllers\Admin\ProductController::class . ':updateProductIndex');
        $group->post('/product/update',                App\Controllers\Admin\ProductController::class . ':updateProduct');
        $group->delete('/product/delete',                  App\Controllers\Admin\ProductController::class . ':deleteProduct');
        $group->put('/product/update/status',      App\Controllers\Admin\ProductController::class . ':updateProductStatus');
        $group->post('/product/getinfo',      App\Controllers\Admin\ProductController::class . ':getProductInfo');

        // order
        $group->get('/order',                   App\Controllers\Admin\OrderController::class . ':index');
        $group->post('/order/ajax',             App\Controllers\Admin\OrderController::class . ':ajaxOrder');
        $group->delete('/order/delete',         App\Controllers\Admin\OrderController::class . ':deleteOrder');
        $group->put('/order/complete',       App\Controllers\Admin\OrderController::class . ':completeOrder');
        
        // news
        $group->get('/news',             App\Controllers\Admin\AnnController::class . ':index');
        $group->post('/news/create',      App\Controllers\Admin\AnnController::class . ':createNews');
        $group->put('/news/update',        App\Controllers\Admin\AnnController::class . ':updateNews');
        $group->delete('/news/delete',          App\Controllers\Admin\AnnController::class . ':deleteNews');
        $group->post('/news/ajax',       App\Controllers\Admin\AnnController::class . ':ajax');
        $group->post('/news/request',       App\Controllers\Admin\AnnController::class . ':requestNews');

        // Detect Mange
        $group->get('/ban',                      App\Controllers\Admin\BanController::class . ':index');
        $group->post('/ban/rule/create',         App\Controllers\Admin\BanController::class . ':createBanRule');
        $group->put('/ban/rule/update',          App\Controllers\Admin\BanController::class . ':updateBanRule');
        $group->post('/ban/detect/record/ajax',  App\Controllers\Admin\BanController::class . ':detectRuleRecordAjax');
        $group->post('/ban/rule/ajax',           App\Controllers\Admin\BanController::class . ':banRuleAjax');
        $group->post('/ban/record/ajax',         App\Controllers\Admin\BanController::class . ':banRecordAjax');
        $group->post('/ban/rule/request',          App\Controllers\Admin\BanController::class . ':requestBanRule');
        $group->delete('/ban/rule/delete',          App\Controllers\Admin\BanController::class . ':deleteBanRule');

        // record Mange
        $group->get('/record',                    App\Controllers\Admin\RecordController::class . ':recordIndex');
        $group->post('/record/ajax/{type}',        App\Controllers\Admin\RecordController::class . ':recordAjax');


        // User Mange
        $group->get('/user',                     App\Controllers\Admin\UserController::class . ':index');
        $group->get('/user/update/{id}',           App\Controllers\Admin\UserController::class . ':updateUserIndex');
        $group->put('/user/update',                App\Controllers\Admin\UserController::class . ':updateUser');
        $group->delete('/user/delete',                  App\Controllers\Admin\UserController::class . ':deleteUser');
        $group->post('/user/ajax',               App\Controllers\Admin\UserController::class . ':ajax');
        $group->post('/user/create',             App\Controllers\Admin\UserController::class . ':createNewUser');
        $group->post('/user/buy',                App\Controllers\Admin\UserController::class . ':buy');
        $group->put('/user/update/status/{type}', App\Controllers\Admin\UserController::class . ':updateUserStatus');


        $group->get('/coupon',                   App\Controllers\AdminController::class . ':coupon');
        $group->post('/coupon',                  App\Controllers\AdminController::class . ':addCoupon');
        $group->post('/coupon/ajax',             App\Controllers\AdminController::class . ':ajaxCoupon');

        $group->get('/profile',                  App\Controllers\AdminController::class . ':profile');
        $group->get('/invite',                   App\Controllers\AdminController::class . ':invite');
        $group->post('/invite',                  App\Controllers\AdminController::class . ':addInvite');
        $group->post('/chginvite',               App\Controllers\AdminController::class . ':chgInvite');
        $group->get('/sys',                      App\Controllers\AdminController::class . ':sys');
        $group->get('/logout',                   App\Controllers\AdminController::class . ':logout');
        $group->post('/payback/ajax',            App\Controllers\AdminController::class . ':ajaxPayBack');
       
        // 设置中心
        $group->get('/setting',                  App\Controllers\Admin\SettingController::class . ':index');
        $group->post('/setting',                 App\Controllers\Admin\SettingController::class . ':save');
        $group->post('/setting/email',           App\Controllers\Admin\SettingController::class . ':test');
        $group->post('/setting/payment',         App\Controllers\Admin\SettingController::class . ':payment');

        // admin 增加收入和新用户统计
        $group->post('/ajax_data/chart/{name}',        App\Controllers\AdminController::class . ':AjaxDataChart');

        // Agent
        $group->group('/agent', function (Group $group) {
            $group->get('/withdraw',             App\Controllers\Admin\AgentController::class . ':index');
            $group->put('/withdraw/update',   App\Controllers\Admin\AgentController::class . ':updateWithdrawCommission');
            $group->post('/withdraw/ajax',           App\Controllers\Admin\AgentController::class . ':withdrawAjax');
            $group->post('/commission/ajax',           App\Controllers\Admin\AgentController::class . ':commissionAjax');
        });
    })->add(new Admin());

    // webapi
    $app->group('/mod_mu', function (Group $group) {
        $group->get('/nodes/{id}/info',      App\Controllers\WebAPI\NodeController::class . ':getInfo');
        $group->get('/users',                App\Controllers\WebAPI\UserController::class . ':index');
        $group->post('/users/traffic',       App\Controllers\WebAPI\UserController::class . ':addTraffic');
        $group->post('/users/aliveip',       App\Controllers\WebAPI\UserController::class . ':addAliveIp');
        $group->post('/users/detectlog',     App\Controllers\WebAPI\UserController::class . ':addDetectLog');
        $group->post('/nodes/{id}/info',     App\Controllers\WebAPI\NodeController::class . ':info');
        $group->get('/nodes',                App\Controllers\WebAPI\NodeController::class . ':getAllInfo');
        $group->post('/nodes/config',        App\Controllers\WebAPI\NodeController::class . ':getConfig');
        $group->get('/func/ping',            App\Controllers\WebAPI\FuncController::class . ':ping');
        $group->get('/func/detect_rules',    App\Controllers\WebAPI\FuncController::class . ':getDetectLogs');
    })->add(new WebAPI());

    $app->group('/link', function (Group $group) {
        $group->get('/{token}',          App\Controllers\LinkController::class . ':GetContent');
    });

    $app->group('/user', function (Group $group) {
        $group->post('/doiam',           App\Services\Payment::class . ':purchase');
    })->add(new Auth());

    $app->group('/doiam', function (Group $group) {
        $group->post('/callback/{type}', App\Services\Payment::class . ':notify');
        $group->get('/return/alipay',    App\Services\Payment::class . ':returnHTML');
        $group->post('/status',          App\Services\Payment::class . ':getStatus');
    });
    
};
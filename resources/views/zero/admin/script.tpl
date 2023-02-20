<script src="/theme/zero/assets/plugins/global/plugins.bundle.js"></script>
<script src="/theme/zero/assets/js/scripts.bundle.js"></script>
<script src="/theme/zero/assets/plugins/custom/datatables/datatables.bundle.js"></script>
<script>
    // get result 
function getResult(titles, texts, icons) {
    Swal.fire({
        title: titles,
        text: texts,
        icon: icons,
        buttonsStyling: false,
        confirmButtonText: "OK",
        customClass: {
            confirmButton: "btn btn-primary"
        }
    });
}
$(document).ready(function (){
    $("a.menu-link[href='"+window.location.pathname+"']").addClass('active');
});
</script>
<script>
    function KTAdminDelete(type, id){
        switch (type) {
            case 'product':
                $.ajax({
                    type: "DELETE",
                    url: "/admin/product/delete",
                    dataType: "json",
                    data: {
                        id
                    },
                    success: function(data){
                        if (data.ret === 1){
                            getResult(data.msg, '', 'success');
                            table_1.ajax.reload();
                        }else{
                            getResult('发生错误', '', 'error');
                        }
                    }
                });
                break;
            case 'node':
                $.ajax({
                    type: "DELETE",
                    url: "/admin/node/delete",
                    dataType: "json",
                    data: {
                        id
                    },
                    success: function(data){
                        if (data.ret === 1){
                            getResult(data.msg, '', 'success');
                            table_1.ajax.reload();
                        }else{
                            getResult('发生错误', '', 'error');
                        }
                    }
                });
                break;
            case 'user':
                $.ajax({
                    type: "DELETE",
                    url: "/admin/user/delete",
                    dataType: "json",
                    data: {
                        id
                    },
                    success: function(data){
                        if (data.ret === 1){
                            getResult(data.msg, '', 'success');
                            table_1.ajax.reload();
                        }else{
                            getResult('发生错误', '', 'error');
                        }
                    }
                });
                break;
            case 'ban_rule':
                $.ajax({
                    type: "DELETE",
                    url: "/admin/ban/rule/delete",
                    dataType: "json",
                    data: {
                        id
                    },
                    success: function(data){
                        if (data.ret === 1){
                            getResult(data.msg, '', 'success');
                            table_1.ajax.reload();
                        }else{
                            getResult('发生错误', '', 'error');
                        }
                    }
                });
                break;
            case 'news':
                $.ajax({
                    type: "DELETE",
                    url: "/admin/news/delete",
                    dataType: "json",
                    data: {
                        id
                    },
                    success: function(data){
                        if (data.ret === 1){
                            getResult(data.msg, '', 'success');
                            table_1.ajax.reload();
                        }else{
                            getResult('发生错误', '', 'error');
                        }
                    }
                });
                break;
        }
    }
</script>
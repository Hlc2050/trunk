$(function () {
    window.onload=function(){
        var pop_time=$("#pop_time").val();
        if(pop_time>=0) {
            setTimeout("$('.finish-task-success').css('display', 'block')", pop_time * 1000);//1000毫秒后弹出根据自己的需要设置时间
        }
    };

    //点击聊天窗和点击发送
    $('.fx-content').on('click', function () {
        $('.finish-task-success').css('display', 'none')
        $('.open-kefu-success').css('display', 'block')
        $('.open-wechat-success').css('display', 'block')
    });
    $('.finish-task-status').on('click', function () {
        $('.finish-task-success').css('display', 'none')
        $('.open-kefu-success').css('display', 'block')
        $('.open-wechat-success').css('display', 'block')
    });
    // 关闭弹窗
    $('.close').on('click', function () {
        $('.finish-task-success').css('display', 'none')
        $('.open-kefu-success').css('display', 'block')
    });

    // 打开客服
    $('.open-kefu-toast').on('click', function () {
        // $('.open-kefu-toast').css('display', 'none')
        $('.open-wechat-success').css('display', 'block')

    });

});
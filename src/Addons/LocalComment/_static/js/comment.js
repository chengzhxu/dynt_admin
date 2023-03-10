/**
 * Created by Administrator on 15-5-7.
 */

$(function () {
    $.post(U('Weibo/Index/atWhoJson'),{},function(res){
        atwho_config = {
            at: "@",
            data: res,
            tpl: "<li data-value='@${nickname}'><img class='avatar-img' style='width:2em;margin-right: 0.6em' src='${avatar32}'/>${nickname}</li>",
            show_the_at: true,
            search_key: 'search_key',
            start_with_space: false
        };
        $('.comments textarea').atwho(atwho_config);
    },'json')
});

$(function(){
    bind_local_comment();
})

var bind_local_comment = function(){
    bind_reply_local_comment()
    bind_send_local_comment()
    bind_delete_local_comment();
}
var bind_reply_local_comment = function(){
    $('[data-role="reply_local_comment"]').unbind('click');
    $('[data-role="reply_local_comment"]').click(function(){
        var $this = $(this);
        var nickname = $this.attr('data-nickname');
        var $textarea = $this.closest('.comments').find('textarea');
        $textarea.focus();
        if(nickname != ''){
            $textarea.val('回复 @' + nickname + ' ：');
        }else{
            $textarea.val('');
        }
    })
}


var bind_send_local_comment = function(){
    $('[data-role="send_local_comment"]').unbind('click');
    $('[data-role="send_local_comment"]').click(function(e){
        e.preventDefault();
        toast.showLoading();
        var $this = $(this);
        var $textarea = $this.closest('.comments').find('textarea');
        var url = $this.attr('data-url');
        var path = $this.attr('data-path');
        var content = $textarea.val();
        $('#submit-comment').attr('disabled','disabled');
        $.post(url, {content: content,path:path}, function (res) {
            if(res.status){
                var $list = $this.closest('.comments').find('section');
               var  $totalCount = $this.closest('.comments').find('.total_count');
                $totalCount.text(parseInt( $totalCount.text())+1);
                if(local_comment_order == 0){
                    $list.prepend(res.data);
                }else{
                    var count = parseInt($totalCount.text());
                    path = path.split('/');
                    local_comment_page(path[0],path[1],path[2], Math.ceil(count / local_comment_count));
                }

                toast.success(res.info);
                $textarea.val('');
                bind_local_comment();
            }else{
                handleAjax(res);
            }
            toast.hideLoading();
        },'json');
    })
}



var bind_delete_local_comment = function () {

    $('[data-role="delete_local_comment"]').unbind('click');
    $('[data-role="delete_local_comment"]').click(function () {

        var $this = $(this);
        var id = $this.attr('data-id');
        var url = $this.closest('.comments').attr('data-del-url');
        $.post(url, {id: id}, function (msg) {
            if (msg.status) {
                bind_local_comment();
                $this.closest('.comment').fadeOut()
                toast.success(msg.info, '温馨提示');
            } else {
                toast.error(msg.info, '温馨提示');
            }
        }, 'json');

    })


}

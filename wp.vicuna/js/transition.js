(function($){
    $('#transition').click(function(){
        if(!confirm(transitionConfirm)) return false;
        $.post('admin-ajax.php?action=vicuna_transition', {nonce:$('#_wpnonce').val()}, function(res){
            if(res !== 'ok') alert(res);
            alert(transitionFinished);
            location.href = location.href;
        });
    });
})(jQuery);
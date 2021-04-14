$(document).on('click','.tdt__channels span',function(e){
    var src = $(this).attr('data-tdtsrc');
    if(src === undefined)return;
    var that = $(this);
    
    $.ajax({
        type: 'POST',
        url: '/ajax',
        data: 'DEBUG=0&action=tdt/channel-selected&src='+src,
        success: function(data){
            reload_component( that.closest('.component').attr('data-component') );
            $('.tdt__channels').animate({left: '-=1000'});
        }
    });
});

$(document).on('click','.tdt__open-channels',function(e){
    $('.tdt__channels').animate({left: '+=1000'});

});
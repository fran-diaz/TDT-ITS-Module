$(document).on('click','.tdt__channels span',function(e){
    var src = $(this).attr('data-tdtsrc');
    if(src === undefined)return;
    var component = $(this).closest('.component').data('component');
    var json = JSON.stringify({'tdt': {'channel-selected': src}});
    var that = $(this);
    
    $.ajax({
        type: 'POST',
        url: '/ajax',
        data: 'DEBUG=0&action=components/alter-component&c='+component+'&json='+json, //
        success: function(data){
            if( component !== undefined ) {
                reload_component(component);
                $('.tdt__channels').animate({left: '-=1000'});
            }
        }
    });
});

$(document).on('click','.tdt__open-channels',function(e){
    $('.tdt__channels').animate({left: '+=1000'});

});
jQuery(document).ready( function( $ ){
    $(document).find('.grel-last-ajax').each(function( ) {
        var datas = {
            action: 'load_widget',
        };
        var selector ="#" + $(this).attr("id");
        jQuery.ajax({
            url: GVData.ajaxurl,
            type: 'POST',
            data: datas,
            cache: false,
            dataType: 'json',
            success: function(responce){
                $( selector ).replaceWith(responce.container);
            }
        });
      
    });
    if (GVData.current_page){
       
    var data = {
        action: 'set_cookie_data_ajax',
        current_page_id: GVData.current_page,
    };
    jQuery.ajax({
        url: GVData.ajaxurl,
        type: 'POST',
        data: data,
        cache: false,
        dataType: 'json',
        success: function (result) {
            console.log(result);
        }
    });
}

} );
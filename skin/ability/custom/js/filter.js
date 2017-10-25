$(document).ready(function() {

    $('.filter_by_price').live('click',function() {

        var from_price = $.trim($('#from_price').val());
        var to_price   = $.trim($('#to_price').val());

        if(isNaN(from_price) || isNaN(to_price))
            return false;

        if(from_price > to_price) {
            func_save_filter_options("action=1003&from_price="+to_price+"&to_price="+from_price);
            return false;
        }

        from_price = (from_price != '') ? from_price : '' ;
        to_price   = (to_price != '')   ? to_price   : '' ;

        func_save_filter_options("action=1003&from_price="+from_price+"&to_price="+to_price);
    });

    $('#choose-more').live('click',function(event) {

        event.preventDefault();

        $(this).colorbox({
            transition: "elastic",
            speed: 350,
            innerWidth: "600",
            innerHeight: "400",
            opacity: 0.3,
            preloading: true,
            inline: true,
            overlayClose: false,
            href: '#choose-more-'+this.className+'-content',
            open: true,
            returnFocus: true
        });

        $(this).removeClass('cboxElement');
    });

    $('.filter_by_field').live('click',function() {

        var field    = this.name;
        var formName = document.getElementById(field);
        var len      = formName.elements.length;
        var ids      = new Array();
        var j        = 0;

        for(var i=0;i<len;i++) {

            if(formName.elements[i].type == 'checkbox' && formName.elements[i].checked) {

                ids[j] = formName.elements[i].id;
                j++;
            }
        }

        if(ids.length >= 1)
            var value = ids.toString();
        else
            var value = 0;

        func_save_filter_options("action=1004&field="+field+"&value="+value);
    });
    
    $('.clear_filters').live('click',function() {

        func_save_filter_options("action=1018");
    });

});

function func_save_filter_options(string)
{
    $.ajax({
        async: true,
        cache: false,
        complete: function(jqXHR, textStatus){

            //alert('Complete : '+textStatus);
        },
        data: string,
        dataType: "json",
        error: function(jqXHR, textStatus, errorThrown){

            //alert('Error : '+textStatus+' '+errorThrown);
        },
        success: function(response, textStatus, jqXHR){

            //alert('Success : '+response+' '+textStatus);
            if(response.success == '1') {

                location.reload();
            }
        },
        timeout: 30000,//timeout to 30 seconds
        type: "GET",
        url: "ajax_call.php"
    });
}
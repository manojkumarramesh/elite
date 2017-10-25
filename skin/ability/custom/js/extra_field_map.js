$(document).ready(function() {

    $('#categories').change(function() {

        $('.span-1, .span-2, .span-3').hide('slow');
        var res = $(this).val();
        $('#categoryid').val(res);
    });

    $('#cat_1').change(function() {

        var res = $(this).val();
        $('#categoryid').val(res);
        if(res != 0) {

            $.ajax({
                async: true,
                cache: false,
                data: "action=fetch_category&product_type_id="+res,
                success: function(response){

                    $('#cat_2').html(response);
                    $('#cat_3').html('');
                    $('#cat_4').html('');
                },
                timeout: 30000,//timeout to 30 seconds
                type: "GET",
                url: "../ajax_call.php"
            });
        } else {

            $('#cat_2').html('');
            $('#cat_3').html('');
            $('#cat_4').html('');
        }
    });
    
    $('#cat_2').change(function() {
        
        var res = $(this).val();
        $('#categoryid').val(res);
        if(res != 0) {

            $.ajax({
                async: true,
                cache: false,
                data: "action=fetch_category&product_type_id="+res,
                success: function(response){

                    $('#cat_3').html(response);
                    $('#cat_4').html('');
                },
                timeout: 30000,//timeout to 30 seconds
                type: "GET",
                url: "../ajax_call.php"
            });
        }
        else {

            $('#cat_3').html('');
            $('#cat_4').html('');
        }
    });

    $('#cat_3').change(function() {

        var res = $(this).val();
        $('#categoryid').val(res);
        if(res != 0) {

            $.ajax({
                async: true,
                cache: false,
                data: "action=fetch_category&product_type_id="+res,
                success: function(response){

                    $('#cat_4').html(response);
                },
                timeout: 30000,//timeout to 30 seconds
                type: "GET",
                url: "../ajax_call.php"
            });
        }
        else {

            $('#cat_4').html('');
        }
    });
    
    $('#cat_4').change(function() {

        var res = $(this).val();
        $('#categoryid').val(res);
    });
    
    $('#button-search').live('click',function() {
      
        /*if($('#cat_1').val() == '0') {

            alert("Select Product Type");
            $('#cat_1').focus();
            $('.span-1, .span-2, .span-3').hide('slow');
        }
        else if($('#cat_2').val() == '0') {

            alert("Select a Category");
            $('#cat_2').focus();
            $('.span-1, .span-2, .span-3').hide('slow');
        }*/

        var categoryid = $.trim($('#categoryid').val());

        if(categoryid == '0' || categoryid == '') {

            alert("Select a Category");
            $('#categories').focus();
            $('.span-1, .span-2, .span-3').hide('slow');
        } else {

            $.ajax({
                async: true,
                cache: false,
                data: 'mode=search&categoryid='+categoryid,
                dataType: "json",
                success: function(response){

                    if(response.success == '1') {

                        $('#source').html('');
                        $('#source').html(response.left);

                        $('#destination').html('');
                        $('#destination').html(response.right);

                        $('.span-1, .span-2, .span-3').show('slow');
                    }
                },
                timeout: 30000,//timeout to 30 seconds
                type: "GET",
                url: "extra_field_map.php"
            });
        }
    });

    $('#move_right').click(function() {

        changeevent('source','destination',' :selected');
    });

    $('#move_left').click(function() {

        changeevent('destination','source',' :selected');
    });

    $('#move_right_all').click(function() {

        changeevent('source','destination',' option');
    });

    $('#move_left_all').click(function() {

        changeevent('destination','source',' option');
    });

    $('#save_fields').live('click',function() {

        var categoryid = $.trim($('#categoryid').val());

        /*if($('#cat_1').val() == '0' || $('#cat_2').val() == '0' || categoryid == '0') {
            alert('Select Category');
            return false;
        }*/

        if(categoryid == '0' || categoryid == '') {

            alert('Select Category');
            return false;
        }

        var r_len = document.rightform.destination.length;
        var ids   = new Array();
        var j     = 0;

        for(var x=0;x<r_len;x++) {

            ids[j] = document.rightform.destination[x].value;
            j++;
        }

        if(ids.length >= 1) {

            var fields = ids.toString();
        }
        else {

            var fields = 0;
        }
        
        $.ajax({
            async: true,
            cache: false,
            data: 'mode=update&categoryid='+categoryid+'&fields='+fields,
            dataType: "json",
            success: function(response){

                if(response.success == '1') {

                    alert(response.message);
                }
            },
            timeout: 30000,//timeout to 30 seconds
            type: "GET",
            url: "extra_field_map.php"
        });
    });
   
});

function changeevent(srcid, desid, flg)
{
    $('#'+srcid+flg).each(function(i, selected) {

        if($(selected).val() != '') {

            $('#'+desid).append('<option value="'+$(selected).val()+'">'+$(selected).text()+'</option>');
            $("#"+srcid+"  option[value="+$(selected).val()+"]").remove();
        }
    });
}
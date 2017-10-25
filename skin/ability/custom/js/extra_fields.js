$(document).ready(function() {
    
    //Select Product Type
    $('#ef_producttype').change(function(){
        
        var res = $(this).val();
        if(res != 0) {
            
            $.ajax({
                type: "POST",
                url: "../ajax_call.php",
                async: true,
                data: "action=fetch_category&product_type_id="+res,
                success: function(result){

                    var data = $.trim(result);
                    if(data == '404') {
                      location.href = 'index.php';
                    } else {                        
                      $('#categoryid').val(res);
                      $('#ef_subcategoryid').html(result);
                      $('#ef_subsubcategoryid').html('');
                      $('#ef_subsubsubcategoryid').html('');
                      
                      $('#htn_maincat_id').val(res);
                    }
                }
            });
            
        } else {

            $('#ef_subcategoryid').html('');
            $('#ef_subsubcategoryid').html('');
            $('#ef_subsubsubcategoryid').html('');
            $('#htn_maincat_id').val(res);
        }
    });
    
    //Select SubCategory Based on Product
    $('#ef_subcategoryid').change(function(){
        
        var res = $(this).val();
        if(res != 0) {
          
            $.ajax({
                type: "POST",
                url: "../ajax_call.php",
                async: true,
                data: "action=fetch_category&product_type_id="+res,
                success: function(result){

                    var data = $.trim(result);
                    if(data == '404') {
                      location.href = 'index.php';
                    } else {
                      $('#categoryid').val(res);
                      $('#ef_subsubcategoryid').html(result);
                      $('#button-next-provider').show();
                      $('#button-next-admin').show();
                      
                      $('#htn_subcat_id').val(res);
                    }
                }
            });
        } else {
          
            //$('.elect').hide();
            $('#ef_subsubcategoryid').html('');
            $('#ef_subsubsubcategoryid').html('');
            $('#htn_subcat_id').val(res);
        }
    });
    
    //Select Subsubcategory Based on Subcategory
    $('#ef_subsubcategoryid').change(function(){
        
        var res = $(this).val();
        if(res != 0) {
          
            $.ajax({
                type: "POST",
                url: "../ajax_call.php",
                async: true,
                data: "action=fetch_category&product_type_id="+res,
                success: function(result){

                    var data = $.trim(result);
                    if(data == '404') {
                      location.href = 'index.php';
                    } else {
                      $('#categoryid').val(res);
                      $('#ef_subsubsubcategoryid').html(result);
                      
                      $('#htn_subsubcat_id').val(res);
                    }
                }
            });
        } else {
            $('#ef_subsubsubcategoryid').html('');
            $('#htn_subsubcat_id').val(res);
        }
    });
    
    //Select Subsubsubcategory Based on Subsubcategory
    $('#ef_subsubsubcategoryid').change(function(){
        
        var res = $(this).val();
        if(res != 0) {
              $('#categoryid').val(res);
        }
    });
    
});


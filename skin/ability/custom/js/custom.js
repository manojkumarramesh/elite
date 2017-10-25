var var_product_type='', ph_flag=0;
var pattern1 = /^\d{1,4}$/;
var pattern2 = /^\d{6,13}$/;

var products_title = new Array();
products_title[1]    = 'Product Name';//Products
products_title[329]  = 'Service Name';//Services
products_title[2378] = 'Product Name';//Motors
products_title[4352] = 'Title Name';//Real Estate
products_title[4351] = 'Title Name';//Tickets
products_title[4365] = 'Title Name';//Travel
products_title[4366] = 'Product Name';//Wholesale
products_title[4367] = 'Title Name';//Sourcing

function DaysInMonth(Year,Month) {
       return 32 - new Date(Year, Month, 32).getDate();
}
function updateDays(Change,formName,YearName,MonthName,DayName){
	frmName = document.forms[formName];
	SelectedYear = frmName[YearName].value;
	SelectedMonth= frmName[MonthName].value;
	DaySelect = frmName[DayName];
	SelectedDays = frmName[DayName].value;

	if(SelectedYear==0) {SelectedYear = new Date().getFullYear();}
	if(SelectedMonth>0) {

		if((Change=='year' &&  SelectedMonth==2) || Change=='month') {

			var Days = DaysInMonth(SelectedYear,SelectedMonth-1);
			DaySelect.length = 0;
			DaySelect.length = Days;
			var key= 0;

			while(key <= Days)
			{
				if(key==0){DaySelect[key] = new Option("-Date-",key);key++;}
				else
				{
					DaySelect[key] = new Option(key,key);
					key++;
				}
			}

			if(SelectedDays>Days) {
				DaySelect.selectedIndex = Days;
			}
			else{
				DaySelect.selectedIndex = SelectedDays;
			}
		}
	}
}

$(document).ready(function() {

    //CONTACT_VENDOR
    $('.submit-btn').click(function() {

        if($('#firstname').val() == '') {

            alert('Missing : First Name');
            $('#firstname').focus();
        } else if($('#lastname').val() == '') {

            alert('Missing : Last Name');
            $('#lastname').focus();
        } else if($('#phone').val() == '') {

            alert('Missing : Phone');
            $('#phone').focus();
        } else if($('#email').val() == '') {

            alert('Missing : Email');
            $('#email').focus();
        } else if($('#message_body').val() == '') {

            alert('Missing : Message');
            $('#message_body').focus();
        } else {

            var txtvemail = $.trim($('#vendor-email').val());
            var txtf      = $.trim($('#firstname').val());
            var txtl      = $.trim($('#lastname').val());
            var txtp      = $.trim($('#phone').val());
            var txte      = $.trim($('#email').val());
            var txtm      = $.trim($('#message_body').val());

            $.ajax({
                async: true,
                data: "action=1013&to="+txtvemail+"&firstname="+txtf+"&lastname="+txtl+"&phone="+txtp+"&email="+txte+"&body="+txtm,
                dataType: "json",
                success: function(response){

                    if(response.success == '1') {
                        alert('Mail Sent');
                    } else {
                        alert('Mail sending failed. Please try again after sometime.');
                    }
                    $('.vendor-contact').colorbox.close();
                },
                timeout: 30000,//timeout to 30 seconds
                type: "POST",
                url: "ajax_call.php"
            });
        }
    });
   
    //seller_profile.tpl - Show 'Do you want to sell your products at Bvira?' on document load;
    $("#sell-or-not").show();
    
    //seller_profile.tpl - Do you want to sell your products at Bvira?
    $('#additional_values_3').change(function(){
        
        var res = $(this).val();
        if(res == 'Yes') {
            $.post('seller_profile.php',{action:'mk_dir'},function(result) {
                //$('#error').html(result);
            });
            $('#submit-1').fadeOut("slow");
            $('#select-1-yes').fadeIn("slow");
        } else if(res == 'No') {
            $.post('seller_profile.php',{action:'rm_dir'},function(result) {
                //$('#error').html(result);
            });
            $('#select-1-yes').fadeOut("slow");
            $('#submit-1').fadeIn();
        } else {
            alert('Do you want to sell your products at Bvira? is empty!');
            $('#select-1-yes').fadeOut("slow");
        }
    });
    
    //seller_profile.tpl
    $('#submit-1').live('click',function(event){
        
        event.preventDefault();
        var res = $('#additional_values_3').val();
        if(res == 'No') {
            location.href = 'home.php';
        } else if(res == '0') {
            alert('Do you want to sell your products at Bvira? is empty!');
            $('#select-1-yes').fadeOut("slow");
        }
    });
    
    //seller_add_profile.tpl - Seller Type
    $('#additional_values_4').change(function(){
        
        var res = $(this).val();
        var id = res.toLowerCase().replace(' ', '-');
        $('.seller-type').fadeOut("slow");
        $('#'+id).html('');
        if(id != '0') {
            $('#sellertype-error').show();
            if(id == 'business') {
                $.post('seller_profile.php',{action:'show_business_user'},function(res) {
                    var result = $.trim(res);
                    $('#business').html(result);
                    $('#individual-seller').html('');
                    $('#sellertype-error').hide();
                });
                $('.mf-paid').show();
                $('.mf-free').show();
                $('input[name="additional_values[52]"][value="1"]').attr('checked','checked');
                $("#additional_values_54").val("0");
                $('.produpload').show();
                //$("#additional_values_52").val("0");
                //$('.prod_membership').show();
                $('.prod_auto_payment').show();
            } else if(id == 'individual-seller') {
                $.post('seller_profile.php',{action:'show_individual_seller'},function(res) {
                    var result = $.trim(res);
                    $('#individual-seller').html(result);
                    $('#business').html('');
                    $('#sellertype-error').hide();
                });
                $('.mf-free').show();
                $('.mf-paid').hide();
                $('input[name="additional_values[52]"][value="1"]').attr('checked','checked');
                $("#additional_values_54").val("1");
                $('.produpload').hide();
                //$("#additional_values_52").val("1");
                //$('.prod_membership').hide();
                $('#additional_values_47').val('');
                $('#additional_values_48').val('');
                $('#additional_values_49').val('');
                $('#additional_values_50').val('/');
                $('#expiry_month').val('');
                $('#expiry_year').val('');
                $('#additional_values_51').val('');
                $('#verify_creditcard').val('0');
                $('.prod_auto_payment').hide();
            }
            $('#'+id).fadeIn();
        }
    });
    
    //seller_add_profile.tpl - Do you accept return?
    $('#additional_values_38').change(function(){
        
        var res = $(this).val();
        if(res == 'Yes') {
            $('.refund-policy').fadeIn("slow");
        } else if(res == 'No' || res == '0') {
            $('.refund-policy').fadeOut("slow");
        }
    });
    
    //seller_add_profile.tpl - Tax Setting
    /*$('#additional_values_43').change(function(){
        
        var res = $(this).val();
        if(res == 'Yes') {
            $('.tax-setting').fadeIn("slow");
            if($('#additional_values_44').val()=='0') {
                var tax_country = $('#additional_values_23').val();
                $('#additional_values_44').val(tax_country);
                get_states(tax_country, 'additional_values_45', 'country-3');
            }
        } else if(res == 'No' || res == '0') {
            $('.tax-setting').fadeOut("slow");
        }
    });*/

    var save = 0;
    var flag1 = 0;
    var flag2 = 0;
    var flag3 = 0;
    var flag4 = 0;
    var flag5 = 0;
    var flag6 = 0;
    var flag7 = 0;
    
    //submitting seller registration form without validation
    $('#submit-3').live('click',function(event) {

        if($('#additional_values_4').val() == '0') {
            alert('Choose your seller type');
            $('#additional_values_4').focus();
            return;
        } 
        if(typeof $('#additional_values_5:checked').val() === "undefined") {
            alert('Choose the product types that you would sell at Bvira');
            $('#additional_values_5').focus();
            return;
        }
        if($('#additional_values_4').val() == 'Business') {
            if($('#additional_values_15').val() == '') {
                alert('Business Name is empty!');
                $('#additional_values_15').focus();
                return;
            }
            if($('#additional_values_49').val() != '') {
                cc_validation = validateccdetails();
                if(cc_validation==0) return;
            }    
        }
        if ($('#mobile_1').val() == '') {
            alert('Country Code is empty!');
            $('#mobile_1').focus();
            return;
        }  
        if ($('#mobile_2').val() == '') {
            alert('Mobile Number is empty!');
            $('#mobile_2').focus();
            return;
        }
        if (($('#mobile_1').val() != '') && (!(pattern1.test($('#mobile_1').val())))) {
            alert('Country Code is invalid!');
            $('#mobile_1').focus();
            return;
        }
        if (($('#mobile_2').val() != '') && (!(pattern2.test($('#mobile_2').val())))) {
            alert('Mobile Number is invalid!');
            $('#mobile_2').focus();
            return;
        }
        if ($('#additional_values_35').val() != '') {
            var paypal_validation = validatepaypaldetails();
            if(paypal_validation==0) return;
                if($('#verify_paypal').val() == '0' || $('#verify_paypal').val() == '') {
                    alert('PayPal account information is not verified!');
                    $('#verify-paypal').focus();
                    return;
                }
        }
        if( $('#dobYear').val()!=0 && $('#dobMonth').val()!=0 && $('#dobDay').val()!=0) {    
            var dob = $('#dobYear').val()+'-'+$('#dobMonth').val()+'-'+$('#dobDay').val();
            $('#additional_values_8').val(dob);
        } 
        document.sellerform.submit();
    });
    
    //seller_edit_profile.tpl - Submit
    $('#submit-2').live('click',function(event) {

        event.preventDefault();
        if(save == 0) {
            if($('#additional_values_4').val() == '0') {
                alert('Choose your seller type');
                $('#additional_values_4').focus();
            } else if(typeof $('#additional_values_5:checked').val() === "undefined") {
                alert('Choose the product types that you would sell at Bvira');
                $('#additional_values_5').focus();
            } else if($('#additional_values_4').val() != '0' && $('#additional_values_4').val() == 'Business' && flag1 == 0) {
                flag1 = validatebusinesssellerdetails();
                if (flag1 == 1) {
                  $("#submit-2").trigger("click");
                }
            } else if($('#additional_values_4').val() != '0' && $('#additional_values_4').val() == 'Individual Seller' && flag2 == 0) {
                flag2 = validateindividualsellerdetails();
                if (flag2 == 1) {
                  $("#submit-2").trigger("click");
                }
            } /*else if($('#additional_values_26').val() == '') {
                alert('Contact Person First Name is empty!');
                $('#additional_values_26').focus();
            } else if($('#additional_values_27').val() == '') {
                alert('Contact Person Last Name is empty!');
                $('#additional_values_27').focus();
            } else if($('#additional_values_28').val() == '') {
                alert('Primary phone is empty!');
                $('#additional_values_28').focus();
            } else if ($('#additional_values_28').val() != '' && isNaN($('#additional_values_28').val())) {
                alert('Primary phone is invalid!');
                $('#additional_values_28').focus();
            }*/ else if($('#additional_values_34').val() == '0') {
                alert('Choose Paypal Option!');
                $('#additional_values_34').focus();
            } else if($('#additional_values_34').val() != '0' && $('#additional_values_34').val() == 30 && flag3 == 0) {
                flag3 = validatepaypaldetails();
                if (flag3 == 1) {
                  $("#submit-2").trigger("click");
                }
            } else if($('#verify_paypal').val() == '0' || $('#verify_paypal').val() == '') {
                alert('PayPal account information is not verified!');
                $('#verify-paypal').focus();
            } else if($('#additional_values_38').val() == '0') {
                alert('Choose an option for Do you accept return?');
                $('#additional_values_38').focus();
            } else if($('#additional_values_38').val() != '0' && $('#additional_values_38').val() == 'Yes' && flag4 == 0) {
                flag4 = validatereturndetails();
                if (flag4 == 1) {
                  $("#submit-2").trigger("click");
                }
            } else if (typeof $('#additional_values_55:checked').val() === "undefined") {
                alert('choose the regions for Where you could ship your products?');
                $('#additional_values_55').focus();
            } else if($('#shipping-method').val() == '0') {
                alert('Choose your shipping method');
                $('#additional_values_42').focus();
            } else if($('#additional_values_43').val() == '0') {
                alert('Choose an option for Do you charge Tax?');
                $('#additional_values_43').focus();
            } else if($('#additional_values_43').val() != '0' && $('#additional_values_43').val() == 'Yes' && flag5 == 0) {
                flag5 = validatetaxdetails();
                if (flag5 == 1) {
                  $("#submit-2").trigger("click");
                }
            } else if ($('#additional_values_54').val() == '0') {
                alert('Choose an option for How would you upload your products to Bvira?');
                $('#additional_values_54').focus();
            } /*else if ($('#additional_values_58').val() == '0') {
                alert('Choose your preferred currency');
                $('#additional_values_58').focus();
            } else if ($('#additional_values_59').val() == '0') {
                alert('Choose your preferred language');
                $('#additional_values_59').focus();
            } else if (typeof $('#additional_values_56:checked').val() === 'undefined') {
                alert('Choose an option for Bvira Selling Modules!');
                $('#additional_values_56').focus();
            } else if($('#additional_values_56:checked').val() == '2' && flag6 == 0) {
                flag6 = validatebargaindetails();
                if (flag6 == 1) {
                  $("#submit-2").trigger("click");
                }
            } else if(typeof $('#additional_values_52:checked').val() === "undefined") {
                alert('Choose your membership plan!');
                $('#additional_values_52').focus();
            }*/ else if($('#additional_values_49').val() != '' && $('#additional_values_4').val() != '0' && $('#additional_values_4').val() == 'Business' && flag7==0) {
                flag7 = validateccdetails();
                if (flag7 == 1) {
                  $("#submit-2").trigger("click");
                }
            }
            else {
                save = 1;
                document.sellerform.submit();
                document.getElementById("sellerform").submit();
                $('#sellerform').submit();
            }
        } else {
            document.sellerform.submit();
            document.getElementById("sellerform").submit();
            $('#sellerform').submit();
        }
    });
    
    //seller_edit_profile.tpl - Apply Changes
    $('#submit-21').live('click',function(event) {

        event.preventDefault();
        if(save == 0) {
            if($('#verify_paypal').val() == '0' || $('#verify_paypal').val() == '') {
                alert('PayPal account information is not verified!');
                $('#verify-paypal').focus();
            } else if($('#additional_values_38').val() != '0' && $('#additional_values_38').val() == 'Yes' && flag4 == 0) {
                flag4 = validatereturndetails();
                if(flag4 == 1) {
                    $("#submit-21").trigger("click");
                }
            } else if($('#additional_values_43').val() != '0' && $('#additional_values_43').val() == 'Yes' && flag5 == 0) {
                flag5 = validatetaxdetails();
                if (flag5 == 1) {
                  $("#submit-21").trigger("click");
                }
            } /*else if($('#additional_values_49').val() != '' && $('#additional_values_4').val() != '0' && $('#additional_values_4').val() == 'Business' && flag7==0) {
                flag7 = validateccdetails();
                if (flag7 == 1) {
                  $("#submit-21").trigger("click");
                }
            }*/ else {
                save = 1;
                document.sellerform.submit();
                document.getElementById("sellerform").submit();
                $('#sellerform').submit();
            }
        } else {
            document.sellerform.submit();
            document.getElementById("sellerform").submit();
            $('#sellerform').submit();
        }
    });
    
    //seller_add_profile.tpl - Update the combined credit card expiration month and yeara to the hidden field
    $('#expiry_month, #expiry_year').change(function() {
      
         $('#additional_values_50').val($('#expiry_month').val()+'/'+$('#expiry_year').val());
    });
    
    //seller_add_profile.tpl - Product types that you would sell at Bvira
    /* commented by Karthic as this is not necessary
    $('.cbox5').click(function() {

        if (this.checked == true) {
             $("#product-types").val("1");
             var_product_type += this.value+',';
        } else {
            var size = $('input[name=additional_values[5]]:checked').size();    //alert(size);
            if (size == 1) {
                $('#additional_values_5').attr('checked', true);
                var_product_type += $('#additional_values_5').val()+',';
            }
        }
    });
    */
    //seller_add_profile.tpl - Shipping
    $('.radio-1').click(function() {

        if (this.checked == true) {
            $("#shipping-method").val("1");
        }
    });
    
    //seller_add_profile.tpl - verify paypal account
    $('#verify-paypal').live('click',function(){
        
        var flag = validatepaypaldetails();
        if (flag == '1') {
            $('#paypal-error').html('Please wait ...');
            $('#paypal-error').css({position: 'absolute', left: '33%', color: 'blue'});
            $('#paypal-error').show();
            var pid = $('#additional_values_35').val();
            var pfn = $('#additional_values_36').val();
            var pln = $('#additional_values_37').val();
            $.post('seller_profile.php',{action:'verify_paypal', paypal_id: pid, paypal_fname: pfn, paypal_lname: pln},function(res) {
                var result = $.trim(res);
                if (result != 'SUCCESS') {
                    var loading = 'Not Verified';
                    $('#verify_paypal').val('0');
                    $('#paypal-error').css({position: 'absolute', left: '33%', color: 'red'});
                } else {
                    var loading = 'Verified';
                    $('#verify_paypal').val('1');
                    $('#paypal-error').css({position: 'absolute', left: '33%', color: 'blue'});
                }
                $('#paypal-error').html(loading);
                $('#paypal-error').attr('class', 'loading');
            });
        }
    });
    
    //seller_add_profile.tpl - verify creditcard details
    $('#verify-creditcard').live('click',function(){
        alert("To verify your creditcard, we place a hold on USD 1.00 on your creditcard through paypal. Once your card details are verified, the hold on your card is cancelled immediately. Your card is not charged for this verification process.");
        $('#creditcard-error').html('Please wait ...');
        //$('#creditcard-error').attr('style', 'color: blue;');
        $('#creditcard-error').css({position: 'absolute', left: '89%', color: 'blue'});
        $('#creditcard-error').show();

        var cctype = $('#additional_values_48').val();    
        var ccno = $('#additional_values_49').val();
        var expmon = $('#expiry_month').val();
        var expyr = $('#expiry_year').val();
        var ccv = $('#additional_values_51').val();
        
        $.post('seller_profile.php',{action:'verify_creditcard', cctype:cctype, ccno: ccno, expmon: expmon, expyr: expyr, ccv:ccv},function(res) {
                
        var result = $.trim(res);
        //alert(result);
        if (result == 'Failure') {
                    
            var loading = 'Creditcard Validation Failed. Please correct your creditcard details.';
            $('#verify_creditcard').val('0');
            $('#creditcard-error').attr('style', 'color: red;');
        } else if(result == 'Success') {
                    
            var loading = 'Creditcard Verified';
            $('#verify_creditcard').val('1');
            $('#creditcard-error').attr('style', 'color: blue;');
        } else {
            var loading = 'Error occurred. Please verify your creditcard details again';
            $('#verify_creditcard').val('1');
            $('#creditcard-error').attr('style', 'color: blue;');
        }
        $('#creditcard-error').html(loading);
        $('#creditcard-error').attr('class', 'loading');
        });
        
    });
    
    
    //Seller Profile - Bvira Selling Modules
    $('.cls_bvira_sale_module').change(function(){
        
        var res = $(this).val();
        if(res == 2) {
            
            $('#bvira-bargain').fadeIn("slow");
        } else if(res == 1 || res == 0) {
            
            $('#bvira-bargain').fadeOut("slow");
        }
    });

    $('#producttype').change(function(){
        
        var res = $(this).val();
        if(res != 0) {
            
            //To assign product or title name
            $('.products_title').html(products_title[res]);
            
            $('#button-next-provider').hide();
            $('#button-next-admin').hide();

            //To Show UPC and MPN fields only for Category - Products.
            if(res == 1)
                $('.elect').show();
            else
                $('.elect').hide();

            if(res == 329 || res == 4351 || res == 4365) {
             
                $('.product-condition').hide();
                $('.condition-new').hide();
                $('.shipping').hide();
                $('.real-time').hide();
            } else {
                
                $('.product-condition').show();
                $('.shipping').show();

                if($('#shipping_id').val() == '2')
                    $('.real-time').show();
            }
          
            $.ajax({
                type: "GET",
                url: "../ajax_call.php",
                async: true,
                data: "action=fetch_category&product_type_id="+res,
                success: function(response){

                    $('#categoryid').val(res);
                    $('#subcategoryid').html(response);
                    $('#subsubcategoryid').html('');
                    $('#subsubsubcategoryid').html('');
                    $('#subsubsubsubcategoryid').html('');
                }
            });
        } else {

            $('#subcategoryid').html('');
            $('#subsubcategoryid').html('');
            $('#subsubsubcategoryid').html('');
            $('#subsubsubsubcategoryid').html('');
        }
    });

    $('#subcategoryid').change(function(){
        
        var res = $(this).val();
        if(res != 0) {
            
            $('#button-next-provider').hide();
            $('#button-next-admin').hide();

            /*
            if(res == '98')
                $('.elect').show();
            else
                $('.elect').hide();
            */
          
            $.ajax({
                type: "GET",
                url: "../ajax_call.php",
                async: true,
                data: "action=fetch_category&product_type_id="+res,
                success: function(response){

                    $('#categoryid').val(res);
                    $('#subsubcategoryid').html(response);
                    $('#subsubsubcategoryid').html('');
                    $('#subsubsubsubcategoryid').html('');
                    $('#button-next-provider').show();
                    $('#button-next-admin').show();
                }
            });
        } else {
          
            $('#subsubcategoryid').html('');
            $('#subsubsubcategoryid').html('');
            $('#subsubsubsubcategoryid').html('');
        }
    });

    $('#subsubcategoryid').change(function(){
        
        var res = $(this).val();
        if(res != 0) {
          
            $.ajax({
                type: "GET",
                url: "../ajax_call.php",
                async: true,
                data: "action=fetch_category&product_type_id="+res,
                success: function(response){

                    $('#categoryid').val(res);
                    $('#subsubsubcategoryid').html(response);
                    $('#subsubsubsubcategoryid').html('');
                }
            });
        } else {

            $('#subsubsubcategoryid').html('');
            $('#subsubsubsubcategoryid').html('');
        }
    });

    $('#subsubsubcategoryid').change(function(){
        
        var res = $(this).val();
        if(res != 0) {
          
            $.ajax({
                type: "GET",
                url: "../ajax_call.php",
                async: true,
                data: "action=fetch_category&product_type_id="+res,
                success: function(response){

                    $('#categoryid').val(res);
                    $('#subsubsubsubcategoryid').html(response);
                }
            });
        } else {

            $('#subsubsubsubcategoryid').html('');
        }
    });

    $('#subsubsubsubcategoryid').change(function(){

        var res = $(this).val();
        if(res != 0)
            $('#categoryid').val(res);
    });

    $('#product').keyup(function(){
        
        var res         = $(this).val();
        var categoryid  = $('#categoryid').val();
        //var producttype = $('#producttype').val();

        if(categoryid == '' || categoryid == '0') {
            alert("Select Category");
            return false;
        }

        $('#product').autocomplete({

            source: "../ajax_call.php?action=1014&name="+res+"&categoryid="+categoryid,
            minLength: 3,
            select: function(event,ui) {

                var str = ui.item.value;
                $('#clean_url').val(str);
                $('#keywords').val(str);
                $('#title_tag').val(str);
                $('#meta_keywords').val(str);
                $('#meta_description').val(str);
                fetchvpc(str);
            }
        });
    });

    $('#upc_ean_gtin_product_id, #mpn_model_number').keyup(function(){
        
        var value = $.trim(this.value);
        var name = $.trim(this.name);
        var categoryid = $('#categoryid').val();

        if(categoryid == '' || categoryid == '0') {
            alert("Select Category");
            return false;
        }

        $('#'+name).autocomplete({

            source: "../ajax_call.php?action=1015&"+name+"="+value+"&categoryid="+categoryid,
            minLength: 2,
            select: function(event,ui) {

                $('#upc_ean_gtin_product_id').val(ui.item.upc_ean_gtin_product_id);
                $('#mpn_model_number').val(ui.item.mpn_model_number);
                $('#brand_manufacturer_name').val(ui.item.brand_manufacturer_name);
                $('#product').val(ui.item.product);
                $('#clean_url').val(ui.item.product);
                $('#keywords').val(ui.item.product);
                $('#title_tag').val(ui.item.product);
                $('#meta_keywords').val(ui.item.product);
                $('#meta_description').val(ui.item.product);
            }
        });
    });

    $('#condition').change(function() {
        
        var res = $(this).val();
        var name = $('#product').val();
        var categoryid = $('#categoryid').val();
        
        if(name == '') {
            
            alert("Missing : Product / Service / Title Name");
            $('#product').focus();
            $("#condition option[value='0']").attr("selected", true);
            return false;
        }
        
        var vendorid = $('#provider').attr('class');
        if(vendorid == '' || vendorid == 'InputWidth') {

            vendorid = $('#provider').val();
        }
        
        if(res == '2') {
            
            $('.condition-new').fadeIn("slow");
        }
        else if(res != '2' || res == 0) {

            $('.condition-new').fadeOut("slow");
        }

        if(res == '1' || res == '2' || res == '3' || res == '4' || res == '5') {

            var query_string = "../ajax_call.php?action=fetch_vpc&name="+name+"&condition="+res+"&categoryid="+categoryid+"&provider="+vendorid;
            $.getJSON(query_string,function(result){

                if(result.product_is_exists == '1') {

                    alert('Product Already Exist(s).');
                    $("#condition option[value='0']").attr("selected", true);
                    $('#condition').focus();
                }
                else {

                    $('#productcode').val(result.productcode);
                    $('#vendorproductcode').val(result.vendorproductcode);
                }
            });
        }
        else if(res == 0) {
            
            $('#productcode').val('');
            $('#vendorproductcode').val('');
        }
    });

    $('#button-next-admin').live('click',function(event) {
      
        if($('#provider').val() == '0') {

            alert("Select Provider");
            $('#provider').focus();
        } else if($('#producttype').val() == '0') {

            alert("The required field 'Select Product Type' is empty!");
            $('#producttype').focus();
        } else if($('#subcategoryid').val() == '0') {

            alert("The required field 'Select a Category' is empty!");
            $('#subcategoryid').focus();
        } else {

            $('#div-one').slideUp("slow");
            $('#div-two').fadeIn(800);
            $('#upc_ean_gtin_product_id').val('');
            $('#mpn_model_number').val('');
            $('#brand_manufacturer_name').val('');
            $('#product').val('');
            $('#clean_url').val('');
            $("#condition option[value='0']").attr("selected", true);
            $('#productcode').val('');
            $('#vendorproductcode').val('');
            formdetails();
            fetch_extra_fields();
        }
    });

    $('#button-back-admin, #button-back-provider').live('click',function(event) {

        $('#div-two').fadeOut();
        $('#div-one').fadeIn(800);
    });

    $('#list_price').change(function() {

        var list_price = $.trim($('#list_price').val());
        
        if(list_price != '') {
            
            $('#vendor_list_price').val(list_price);
        } else {
            
            $(this).val('0.00');
            $('#vendor_list_price').val('0.00');
        }
    });

    $('#price').change(function() {

        var listprice = $.trim($('#list_price').val());
        var price = $.trim($('#price').val());
        
        if(price != '') {
            
            $('#vendor_price').val(price);

            if(listprice == '0.00' || listprice != '0.00' && price != '0.00' && parseFloat(price) > parseFloat(listprice)) {
            
                alert("Sale Price should be less than List Price");
                $(this).val('0.00');
                $('#vendor_price').val('0.00');
            }
        } else {
            
            $(this).val('0.00');
            $('#vendor_price').val('0.00');
        }
    });

    $('#lowest_possible_price').change(function() {

        var listprice = $.trim($('#list_price').val());
        var saleprice = $.trim($('#price').val());
        var lowest_possible_price = $.trim($('#lowest_possible_price').val());
        
        if(lowest_possible_price != '') {
            
            $('#vendor_lowest_possible_price').val(lowest_possible_price);

            if(listprice == '0.00' || listprice != '0.00' && lowest_possible_price != '0.00' && parseFloat(lowest_possible_price) >= parseFloat(listprice)) {
            
                alert("Lowest Possible Price should be less than List Price");
                $(this).val('0.00');
                $('#vendor_lowest_possible_price').val('0.00');
            } else if(saleprice == '0.00' || saleprice != '0.00' && lowest_possible_price != '0.00' && parseFloat(lowest_possible_price) >= parseFloat(saleprice)) {
            
                alert("Lowest Possible Price should be less than Sale Price");
                $(this).val('0.00');
                $('#vendor_lowest_possible_price').val('0.00');
            }
        } else {
            
            $(this).val('0.00');
            $('#vendor_lowest_possible_price').val('0.00');
        }
    });

    $('#vendor_cost').change(function() {

        var list_price = $.trim($('#list_price').val());
        var saleprice = $.trim($('#price').val());
        var lowest_possible_price = $.trim($('#lowest_possible_price').val());
        var vendor_cost = $.trim($('#vendor_cost').val());
        var avail = $('.lowest').attr('style');
        
        if(vendor_cost != '') {
            
            $('#vendor_vendor_cost').val(vendor_cost);

            if(list_price == '0.00' || list_price != '0.00' && vendor_cost != '0.00' && parseFloat(vendor_cost) >= parseFloat(list_price)) {
            
                alert("Seller Cost should be less than List Price");
                $(this).val('0.00');
                $('#vendor_vendor_cost').val('0.00');
            } else if(saleprice == '0.00' || saleprice != '0.00' && vendor_cost != '0.00' && parseFloat(vendor_cost) >= parseFloat(saleprice)) {

                alert("Seller Cost should be less than Sale Price");
                $(this).val('0.00');
                $('#vendor_vendor_cost').val('0.00');
            } else if(avail == undefined) {

                if(lowest_possible_price == '0.00' || lowest_possible_price != '0.00' && vendor_cost != '0.00' && parseFloat(vendor_cost) >= parseFloat(lowest_possible_price)) {

                    alert("Seller Cost should be less than Lowest Possible Price");
                    $(this).val('0.00');
                    $('#vendor_vendor_cost').val('0.00');
                }
            }
        } else {
            
            $(this).val('0.00');
            $('#vendor_vendor_cost').val('0.00');
        }
    });

    $('#map_price').change(function() {

        var vendor_cost = $.trim($('#map_price').val());
        
        if(vendor_cost != '') {
            
            //
        } else {
            
            $(this).val('0.00');
        }
    });

    $('#provider').change(function() {
        
        var prov = $(this).val();
        $.ajax({
            type: "POST",
            url: "../ajax_call.php",
            async: true,
            data: "action=fetch_product_type&provider_id="+prov,
            success: function(result){

                var data = $.trim(result);
                $('#producttype').html(data);
            }
        });
    });

    $('#quantity_avail').change(function() {

        $('#avail').val(this.value);
    });

    $('.radio1').click(function() {

        if (this.checked == true) {
            
            $('#quantity_type').val(this.value);
        }
    });

    $('#button-next-provider').live('click',function(event) {

        var prov = $('#provider').attr('class');
        if(prov == '0' || prov == '') {

            alert("The required field 'Provider' is empty!");
        } else if($('#producttype').val() == '0') {

            alert("The required field 'Select Product Type' is empty!");
            $('#producttype').focus();
        } else if($('#subcategoryid').val() == '0') {

            alert("The required field 'Select a Category' is empty!");
            $('#subcategoryid').focus();
        } else {

            $('#div-one').slideUp("slow");
            $('#div-two').fadeIn(800);
            $('#upc_ean_gtin_product_id').val('');
            $('#mpn_model_number').val('');
            $('#brand_manufacturer_name').val('');
            $('#product').val('');
            $('#clean_url').val('');
            $("#condition option[value='0']").attr("selected", true);
            $('#productcode').val('');
            $('#vendorproductcode').val('');
            formdetails();
            fetch_extra_fields();
        }
    });
    
    //
    $('#brand_manufacturer_name').keyup(function(){
        
        var value = $.trim(this.value);
        if(value != '') {

            $('#brand_manufacturer_name').autocomplete({

                source: "../ajax_call.php?action=fetch_manufacturers&manufacturer="+value,
                minLength: 3,
                select: function(event,ui) {

                    //alert(ui.item.manufacturerid);    alert(ui.item.value);
                    $("#manufacturerid option[value='"+ui.item.manufacturerid+"']").attr("selected", true);
                }
            });
        } else {

            $("#manufacturerid option[value='']").attr("selected", true);
        }
    });
    
    $("#system_message").hide();
    
    /*
    checkEmailUserCookie();

    $("#closesection").click(function(){
        
        $("#system_message").fadeOut("slow");
        var emailUser=getCookie("emailuser");
        var d = new Date();
        document.cookie = 'emailuser='+emailUser+';expires=' + d.toGMTString() +
";" + ";";
  });
    */
    
    $('#list_price, #price, #lowest_possible_price, #vendor_cost, #map_price')
                                                            .focusin(function() {

        if(this.value == '0.00') {

            this.value = '';
        }
    });
    
    $('#list_price, #price, #lowest_possible_price, #vendor_cost, #map_price')
                                                           .focusout(function() {

        if(this.value == '') {

            this.value = '0.00';
        }
    });

    $("li.ui-state-default").hover(
        function () {
            $(this).addClass("ui-state-hover");
        },
        function () {
            $(this).removeClass("ui-state-hover");
        }
    );
    
    //Login form stay signed in checkbox
    $('.stay_signed_in').click(function() {

        if(this.checked == true) {

            $('input[name=stay_signed_in]').attr('checked', false);
            $(this).attr('checked', true);
        }
    });

    //vendor store home page
    $('#shop-search').focusout(function() {

        if(this.value == '') {

            this.value = 'Search in this shop...';
        }
    });

    //vendor store home page
    $('#shop-search').focusin(function() {

        if(this.value == 'Search in this shop...') {

            this.value = '';
        }
    });

    //Seller store search
    $('.vendor-shop').live('click',function() {

        store_search();
    });

    //Seller store search
    $('#shop-search').keypress(function(event) {

        if (event.keyCode == '13') {

            event.preventDefault();
            store_search();
        }
    });

    //Product variant fields - edit
    $('.variant_val').live('click', function(event) {

        event.preventDefault();
        var id          = $(this).attr("href").substring(1);
        var clas        = $('#label_efields_'+id).html();
        var productid   = $('#productid').val();

        if(clas == '' || id == '' || productid == '') {
            
            return false;
        }

        $.ajax({
            async: true,
            data: "action=1008&class="+clas+"&productid="+productid,
            dataType: "json",
            type: "GET",
            url: "../ajax_call.php",
            success: function(result) {

                if(result) {
                    location.href = 'product_modify.php?productid='+productid+'&classid='+result.classid+'&section=options#modify_class';
                }
            }
        });
    });
    
    //Product variant fields
    $('.variant_field').live('click', function() {

        if(this.checked) {
            
            this.value = 1;
            var id = this.id;
            var label = $('#label_'+id).html();
            var field = "variant_"+id;
            $('#'+field).val('1');
        }
        else {
            
            this.value = 0;
            var id = this.id;
            var field = "variant_"+id;
            $('#'+field).val('');
        }
    });

    //change product category
    $('#terms-update').live('click',function() {
      
        if($('#provider').val() == '0') {
            alert("Select Provider");
            $('#provider').focus();
        } else if($('#producttype').val() == '0') {
            alert("The required field 'Select Product Type' is empty!");
            $('#producttype').focus();
        } else if($('#subcategoryid').val() == '0') {
            alert("The required field 'Select a Category' is empty!");
            $('#subcategoryid').focus();
        } else {

            var catid = $('#categoryid').val();
            var len   = document.processproductform.elements.length;
            var ids   = new Array();
            var j     = 0;

            for(var x=0;x<len;x++) {
                if(document.processproductform.elements[x].type == 'checkbox' && document.processproductform.elements[x].checked) {
                    ids[j] = document.processproductform.elements[x].id;
                    j++;
                }
            }
            
            if(catid == '' || ids == '') {
                return false;
            }
            
            var producttype         = $('#producttype').val();
            var subcategoryid       = $('#subcategoryid').val();
            var subsubcategoryid    = $('#subsubcategoryid').val();
            var subsubsubcategoryid = $('#subsubsubcategoryid').val();
            var category_ids        = producttype+','+subcategoryid+','+subsubcategoryid+','+subsubsubcategoryid;

            $.ajax({
                async: true,
                data: "action=1000&productids="+ids+"&categoryid="+catid+"&category_ids="+category_ids,
                dataType: "json",
                type: "POST",
                url: "../ajax_call.php",
                success: function(result) {
                    if(result == 1000) {
                        alert("Updated Successfully");
                        $(this).colorbox.close();
                    } else {
                        alert("Updated Failed");
                    }
                }
            });
        }
    });

    //change sale type for products
    $('#ch_sale_module_update').live('click',function() {

        if($('#provider').val() == '0') {
            alert("Select Provider");
            $('#provider').focus();
        } else if($('#saletype').val() == '') {
            alert("The required field 'Sale Module' is empty!");
            $('#saletype').focus();
        } else {

            var checked = []
                $("input[class='serprodid']:checked").each(function ()
                {
                    checked.push(parseInt($(this).val()));
                });


            $.ajax({
                async: true,
                data: "action=update_product_sale_type&productids="+checked+"&saletype="+$('#saletype').val(),
                type: "POST",
                url: "../ajax_call.php",
                success: function(result) {
                    if(result == 'sale type updated') {
                        alert("Updated Successfully");
                        $(this).colorbox.close();
                    } else {
                        alert("Updated Failed");
                    }
                }
            });
        }
    });

});

var i = 1;

//
function placestate(state)
{
    $("#state[type=text]").val(state);
    $("#state option[value='"+state+"']").attr("selected", true);
    if(i == 1 || i == 2) {
        setTimeout('placestate(state)', 5000);
        i++;
    }
}

//
function checkFields(field)
{
    if (field.value != '') {
        if (field.name == 'firstname') {
            //alert('yes');
        }
    }
}

//
function checkEmailAddress(field, empty_err)
{
    var err = false;
    if (!field) {
        return true;
    }
    if (field.value.length == 0) {
        if (empty_err != 'Y') {
            return true;
        } else {
            err = true;
        }
    }
    if(!err && field.value.replace(/^\s+/g, '').replace(/\s+$/g, '').search(email_validation_regexp) == -1) {
        err = true;
    }
    if (err) {
        alert(txt_email_invalid);
        $('#'+field).focus();
        field.select();
    }
    return !err;
}

//seller_add_profile.tpl
function get_states(obj, field, loader,default_state)
{
    $('#'+loader).show();
    $.post('seller_profile.php',{action:'fetch_state', country: obj,ssvalue:default_state},function(res) {
        var result = $.trim(res);
        if(result != '') {
            if(field != 'state') {
                var name = field.substring(18);
                var input = "<select id='"+field+"' name='additional_values["+name+"]' >";
            } else {
                var input = "<select id='"+field+"' name='"+field+"' >";
            }
            input += "<option value='0'>-- Select --</option>";
            input += "</select>";
            $('#'+field).parent().html(input);
            $('#'+field).html(result);
            $('#'+loader).hide();
        } else {
            if(field != 'state') {
                var name = field.substring(18);
                var input = "<input type='text' id='"+field+"' name='additional_values["+name+"]' value='' />";
            } else {
                var input = "<input type='text' id='"+field+"' name='"+field+"' value='' />";
            }
            $('#'+field).parent().html(input);
            $('#'+loader).hide();
        }
    });
}

//seller_add_profile.tpl
function get_tax_states(obj, field, loader, stateid)
{
    $('#'+loader).show();
    $.post('seller_profile.php',{action:'fetch_state', country: obj},function(res) {
        var result = $.trim(res);
        if(result != '') {
            var input = "<select class='taxstate' id='"+field+"' name='"+stateid+"'>";
            input += "<option value='0'>-- Select --</option>";
            input += "</select>";
            $('#'+field).parent().html(input);
            $('#'+field).html(result);
            $('#'+loader).hide();
        } else {
            var input = "<input class='taxstate' type='text' id='"+field+"' name='"+stateid+"' />";
            $('#'+field).parent().html(input);
            $('#'+loader).hide();
        }
        return 1;
    });
}

//seller+add+profile.tpl - For setting preferred currency based on selected country
function get_currency(country_code)
{
    $('#currency-error').show();
    $.get('register.php',{action:'fetch_currency',country:country_code},function(result){
        result = $.trim(result);
        var currency = 'USD';
        if(result != '') {
          currency = result;   
        }
        $("#currency").val(currency);
        $('#currency-error').hide();
            
    });
    
}

//seller_add_profile.tpl
function uploadfiles(obj, input, field)
{
    $('#'+input+'-error').html('Please wait ...');
    $('#'+input+'-error').show();
    $.post('seller_profile.php',{action:'mk_dir'},function(result) {
        //$('#error').html(result);
    });
    var providerid = $('input[name=admin_interface_provider_id]').val(); //for file upload from admin interface
    var rand = new Date().getTime();
    var tmpAction = $('#sellerform').attr("action");
    $('#sellerform').attr("action", "seller_profile.php?action=upload_file&id="+rand+'&field='+input+'&userid='+providerid);
    $('#sellerform').attr("target", "postframe_"+input);
    $('#sellerform').submit();
    $('#postframe_'+input).load(function(){
        iframeContents = $('#postframe_'+input).contents().find('body').html();
        int = input;
        fld = field;
        uploadedxml(int, fld, iframeContents);
    });
    $('#sellerform').attr("action", tmpAction);
    $('#sellerform').removeAttr('target');
}

//seller_add_profile.tpl
function uploadedxml(input, field, iframeContents)
{
    if(iframeContents != '') {
        $('#'+input+'-error').html(iframeContents);
        $('#'+input+'-error').show();
        var res = $('#'+input+'-error').find('span').attr('class');
        if(res) {
            $('#'+field).val(res);
            $('#'+input+'-error').hide();
            $('#'+input+'-class').hide();
            $('#'+input+'-class-success').show();
        } else {
            $('#'+field).val('');
        }
    } else {
        $('#'+input+'-error').html('Failed');
        $('#'+input+'-error').show();
    }
}

function validatebusinesssellerdetails()
{
    var flag = 0;
    if ($('#additional_values_15').val() == '') {
        alert('Business Name is empty!');
        $('#additional_values_15').focus();
    } /*else if ($('#f_additional_values_8').val() == '') {
        alert('Date of Birth is empty!');
        $('#f_additional_values_8').focus();
    }*/ else if ($('#dobMonth').val() == 0) {
        alert('Month is empty in Date of Birth!');
        $('#dobMonth').focus();
    } else if ($('#dobDay').val() == 0) {
        alert('Date is empty in Date of Birth!');
        $('#dobDay').focus();
    } else if ($('#dobYear').val() == 0) {
        alert('Year is empty in Date of Birth!');
        $('#dobYear').focus();
    } else if ($('#mobile_1').val() == '') {
        alert('Country Code is empty!');
        $('#mobile_1').focus();
    } else if ($('#mobile_2').val() == '') {
        alert('Mobile Number is empty!');
        $('#mobile_2').focus();
    } else if (($('#mobile_1').val() != '') && (!(pattern1.test($('#mobile_1').val())))) {
        alert('Country Code is invalid!');
        $('#mobile_1').focus();
    } else if (($('#mobile_2').val() != '') && (!(pattern2.test($('#mobile_2').val())))) {
        alert('Mobile Number is invalid!');
        $('#mobile_2').focus();
    } /*else if ($('#additional_values_10').val() == '0') {
        alert('Choose your secret question 1');
        $('#additional_values_10').focus();
    } else if ($('#additional_values_11').val() == '') {
        alert('Answer 1 is empty!');
        $('#additional_values_11').focus();
    } else if ($('#additional_values_12').val() == '0') {
        alert('Choose your secret question 2');
        $('#additional_values_12').focus();
    } else if ($('#additional_values_13').val() == '') {
        alert('Answer 2 is empty!');
        $('#additional_values_13').focus();
    } /*else if ($('#additional_values_16').val() == '') {
        alert('Upload Business Licence document');
        $('#file3').focus();
    }*/ else if($('#additional_values_20').val() == '') {
        alert('Business Address is empty!');
        $('#additional_values_20').focus();
    } else if($('#additional_values_22').val() == '') {
        alert('Business City is empty!');
        $('#additional_values_22').focus();
    } else if($('#additional_values_23').val() == '0') {
        alert('Business Country is empty!');
        $('#additional_values_23').focus();
    } else if($('#additional_values_24').val() == '0' || $('#additional_values_24').val() == '') {
        alert('Business State is empty!');
        $('#additional_values_24').focus();
    } else if($('#additional_values_25').val() == '') {
        alert('Business Zipcode is empty!');
        $('#additional_values_25').focus();
    } /*else if ($('#additional_values_25').val() != '' && isNaN($('#additional_values_25').val())) {
        alert('Business Zipcode is invalid!');
        $('#additional_values_25').focus();
    } */else if($('#prod_ship_addr').val()=='') {
        alert('Shipping source address is empty');
        $('#prod_ship_addr').focus();
    } else if($('#address').val() == '') {
        alert('Shipping Source Address is empty!');
        $('#address').focus();
    } else if($('#city').val() == '') {
        alert('Shipping Source City is empty!');
        $('#city').focus();
    } else if($('#country').val() == '0') {
        alert('Shipping Source Country is empty!');
        $('#country').focus();
    } else if($('#state').val() == '0' || $('#state').val() == '') {
        alert('Shipping Source State is empty!');
        $('#state').focus();
    } else if($('#zipcode').val() == '') {
        alert('Shipping Source Zipcode is empty!');
        $('#zipcode').focus();
    } else {
        var flag = 1;
    }
    var dob = $('#dobYear').val()+'-'+$('#dobMonth').val()+'-'+$('#dobDay').val();
    $('#additional_values_8').val(dob);
    return flag;
}

function validateindividualsellerdetails()
{
    var flag = 0;
    /*if ($('#f_additional_values_8').val() == '') {
        alert('Date of Birth is empty!');
        $('#f_additional_values_8').focus();
    }*/if ($('#dobMonth').val() == 0) {
        alert('Month is empty in Date of Birth!');
        $('#dobMonth').focus();
    } else if ($('#dobDay').val() == 0) {
        alert('Date is empty in Date of Birth!');
        $('#dobDay').focus();
    } else if ($('#dobYear').val() == 0) {
        alert('Year is empty in Date of Birth!');
        $('#dobYear').focus();
    } else if ($('#mobile_1').val() == '') {
        alert('Country Code is empty!');
        $('#mobile_1').focus();
    } else if ($('#mobile_2').val() == '') {
        alert('Mobile number is empty!');
        $('#mobile_2').focus();
    } else if (!(pattern1.test($('#mobile_1').val()))) {
        alert('Country Code is invalid!');
        $('#mobile_1').focus();
    } else if (!(pattern2.test($('#mobile_2').val()))) {
        alert('Mobile Number is invalid!');
        $('#mobile_2').focus();
    }/* else if ($('#additional_values_10').val() == '0') {
        alert('Choose your secret question 1');
        $('#additional_values_10').focus();
    } else if ($('#additional_values_11').val() == '') {
        alert('Answer 1 is empty!');
        $('#additional_values_11').focus();
    } else if ($('#additional_values_12').val() == '0') {
        alert('Choose your secret question 2');
        $('#additional_values_12').focus();
    } else if ($('#additional_values_13').val() == '') {
        alert('Answer 2 is empty!');
        $('#additional_values_13').focus();
    } */else if($('#address').val() == '') {
        alert('Shipping Source Address is empty!');
        $('#address').focus();
    } else if($('#city').val() == '') {
        alert('Shipping Source City is empty!');
        $('#city').focus();
    } else if($('#country').val() == '0') {
        alert('Shipping Source Country is empty!');
        $('#country').focus();
    } else if($('#state').val() == '0' || $('#state').val() == '') {
        alert('Shipping Source State is empty!');
        $('#state').focus();
    } else if($('#zipcode').val() == '') {
        alert('Shipping Source Zipcode is empty!');
        $('#zipcode').focus();
    } /*else if ($('#zipcode').val() != '' && isNaN($('#zipcode').val())) {
        alert('Zipcode is invalid!');
        $('#zipcode').focus();
    } */else {
        var flag = 1;
    }
    var dob = $('#dobYear').val()+'-'+$('#dobMonth').val()+'-'+$('#dobDay').val();
    $('#additional_values_8').val(dob);
    return flag;
}

function validatepaypaldetails()
{
    var flag = 0;
    if ($('#additional_values_34').checked == false) {
        alert('Paypal is empty!');
        $('#additional_values_34').focus();
    } else if ($('#additional_values_35').val() == '') {
        alert('Paypal Id is empty!');
        $('#additional_values_35').focus();
    } else if($('#additional_values_36').val() == '') {
        alert('Enter your first name provided with Paypal');
        $('#additional_values_36').focus();
    } else if($('#additional_values_37').val() == '') {
        alert('Enter your last name provided with Paypal');
        $('#additional_values_37').focus();
    } else {
        var flag = 1;
    }
    return flag;
}

function validatereturndetails()
{
    var flag = 0;
    if ($('#additional_values_39').val() == '0') {
        alert('Choose an option for Buyer should contact within');
        $('#additional_values_39').focus();
    } else if ($('#additional_values_40').val() == '0') {
        alert('choose an option for Refund will be given as');
        $('#additional_values_40').focus();
    } else {
        var flag = 1;
    }
    return flag;
}

function validatetaxdetails()
{
    var flag = 1;
    var prop_cnt = 0;
    var prod_types_id;
    prop_cnt = $('#prop_type_cnt_id').val();
        
    for($i=0;$i<prop_cnt; $i++) {
        prod_types_id = $('#prop_type_id_'+$i).val();
        
        if($('#prod_'+prod_types_id).css('display')=='block') {
            if($('#additional_values_46_'+$i).val()=='') {
                alert('Tax is empty. Enter 0, if tax is not applicable');
                $('#additional_values_46_'+$i).focus();
                return flag=0;
            } else if($('#additional_values_46_'+$i).val()!=0) {
                if(($('#additional_values_44_'+$i).val()=='') || ($('#additional_values_44_'+$i).val()==0)) {
                    alert('Choose tax country');
                    $('#additional_values_44_'+$i).focus();
                    return flag=0;
                } else if($('#additional_values_45_'+$i).val()=='' || $('#additional_values_45_'+$i).val()==0) {
                    alert('Choose tax state');
                    $('#additional_values_45_'+$i).focus();
                    return flag=0;
                }
            }
        } else {
            $('#additional_values_46_'+$i).val('');
        }
    }

    return flag;
}

function validatebargaindetails()
{
    var flag = 0;
    if ($('#additional_values_57').val() == '0') {
        alert('Choose an option for Do you want to actively participate in Bargain?');
        $('#additional_values_57').focus();
    } else {
        var flag = 1;
    }
    return flag;
}

//bargain_now.tpl
function redirect_to_bargain(formname) {

    var nameofform = eval("document."+formname.name);

    bprodid = nameofform.bprodid.value;
    bfeatured = nameofform.bfeatured.value;

    if(nameofform.product_page.value==1) {
        if(document.getElementById('product_avail_input').value == '') {
            alert('Enter the quantity');
            document.getElementById('product_avail_input').focus();
            return false;
        }

        /*if(isNaN(document.getElementById('product_avail_input').value)) {
            alert('Enter the quantity as integer');
            document.getElementById('product_avail_input').focus();
            return false;
        }

        if(document.getElementById('product_avail_input').value<=0) {
            alert('Enter the quantity as positive integer');
            document.getElementById('product_avail_input').focus();
            return false;
        }*/

        var is_make_deal_variant_emtpy = false;
        $('select').each(function() {
            var product_options = $(this).attr('name').substring(0,15);
            if(product_options == 'product_options'){
                if($('#'+$(this).attr('id')+' option:selected').val() == ''){
                    $('#'+$(this).attr('id')).css('border-color','#FF0000');
                    is_make_deal_variant_emtpy = true;
                }
            }
        });
        if(is_make_deal_variant_emtpy){
            alert('Select the required options');
            return false;
        }
    } else {
        if(nameofform.product_page.value==0) {
        if(!(check_quantity(bprodid,bfeatured))) {
            return false;
        }
        }
    }
    
    if(nameofform.cus_bar_amnt.value=='') {
        alert('Enter your price');
        nameofform.cus_bar_amnt.focus();
        return false;
    }

    if(isNaN(nameofform.cus_bar_amnt.value)) {
        alert('Enter your price as integer');
        nameofform.cus_bar_amnt.focus();
        return false;
    }

    if(nameofform.cus_bar_amnt.value<=0) {
        alert('Enter your price as positive integer');
        nameofform.cus_bar_amnt.focus();
        return false;
    }

    nameofform.action = "bargain_session.php";
    nameofform.submit();
}

function formdetails()
{
    var prov = $('#provider').attr('class');
    if(prov == ''|| prov == 'InputWidth') {
        prov = $('#provider').val();
    }
    var producttype = $('#producttype').val();
    if(prov != 0) {
        $.getJSON("../ajax_call.php?action=fetch_fields&provider_id="+prov+"&producttype="+producttype,function(result) {
            $('#return_time').val(result.return_time);
            $('.curren').html(result.currency);
            $('#currency').val(result.currency);
            $('#saleid').val(result.saleid);
            if(result.saleid == '0') {
                $('.lowest').hide();
            }
            $('#sale_module').html(result.sale_module);
            $('#shippin').html(result.shippin);
            $('#shipping_id').val(result.shipping);
            if(result.shipping == '2') {
                if(producttype != 329 && producttype != 4351 && producttype != 4365) {
                    $('.real-time').show();
                } else {
                    $('.real-time').hide();
                }
                $('.fixed-price').hide();
            } else if(result.shipping == '3') {
                $('.fixed-price').show();
                $('.real-time').hide();
                var countries = result.ships_to.split(',');
                var country_name = result.country_name.split(',');
                var content = "";
                content += "<table>";
                for(i=0;i<countries.length;i++) {
                    content += "<tr>";
                    content += "<td><label>"+country_name[i]+"</label></td>";
                    content += "<td> * <input class='fixedprices' id='fixedprices_"+i+"' type='text' name='fixedprices["+countries[i]+"]' size='18' value='0.00' style='text-align:right;' /> "+result.currency+"</td>";
                    content += "</tr>";
                }
                content += "</table>";
                $('#multiple-fixedprice').html(content);
            } else {
                $('.real-time').hide();
                $('.fixed-price').hide();
            }
            if(result.tax == 'Yes') {
                //$('.tax').show();
                $('#taxcountry').val(result.taxcountry);
                $('#tax-country').html(result.taxcountry);
                $('#taxstate').val(result.taxstate);
                $('#tax-state').html(result.tax_state);
                $('#taxpercent').val(result.taxpercent);
                $('#tax-percent').html(result.taxpercent);
            } else {
                $('.tax').hide();
            }
            if(result.forsale_status == 'Q') {
                $("#forsale option[value='N']").attr("selected", true);
            }
        });
    } else {
        alert("The required field 'Provider' is empty!");
        return false;
    }
}

function fetchvpc(str)
{
    var producttype = $('#producttype').val();
    if(producttype == 329 || producttype == 4351 || producttype == 4365) {
     
        var categoryid = $('#categoryid').val();
        var vendorid   = $('#provider').attr('class');

        if(vendorid == '' || vendorid == 'InputWidth')
            vendorid = $('#provider').val();

        var query_string = "../ajax_call.php?action=fetch_vpc&name="+str+"&categoryid="+categoryid+"&provider="+vendorid;
        $.getJSON(query_string,function(result){

            $('#productcode').val(result.productcode);
            $('#vendorproductcode').val(result.vendorproductcode);
        });
    }
    $('#clean_url').val(str);
    $('#keywords').val(str);
    $('#title_tag').val(str);
    $('#meta_keywords').val(str);
    $('#meta_description').val(str);
}

function displayTaxSetting(sel_prod_typeid)
{
    if($('#prod_'+sel_prod_typeid).css('display')=='none'){
       $('#prod_'+sel_prod_typeid).show();
    } else {
       $('#prod_'+sel_prod_typeid).hide();
    }

}

function getTaxSettings() {
    var checked_count,checked_product_types, container, prod_type_data='';
    
    checked_count = $("input[name='additional_values[5][]']:checked").length;
    var_product_type='';
    $("input[name='additional_values[5][]']:checked").each(function(index) {
        var_product_type += $(this).val()+",";
    });
    
    
    if((checked_count>0) && ($("#additional_values_43").val()=='Yes')) {
        $('#taxset-error').css({position: 'absolute', left: '88%', color: 'blue'});
        $('#taxset-error').show();
        $.ajax({
            type : 'POST',
            url : 'seller_profile.php',
            data : 'action=tax_setting&count='+checked_count+'&product_type_str='+var_product_type,
            success : function(response) {
                    if(response!='') {
                        if($("#additional_values_43").val()=='Yes') {
                            //selected product types element creation
                            $('#selected_prodtype_div').show();
                            container = $('div#selected_prodtype');
                            var i = 1;
                            prod_type_data = '<table style="width:300px; margin-top:25px;"><tr>';
                            $("input:[name='additional_values[5][]']").each(
                            function() {
                                
                                if (this.checked) {
                                    prod_type_data +='<td>';
                                    prod_type_data += '<input type="checkbox" id="sel_prod_type" onclick="displayTaxSetting(this.value);" value="'+ this.value +'" style="float:left;width:13px;height:13px;border:none;" />';
                                    prod_type_data += $('#'+this.value).html()+' ';
                                    prod_type_data += '</td>';
                                    if(i%4==0) {prod_type_data += '</tr><tr>';}
                                    i++;
                                }
                            });
                            prod_type_data += '</tr></table>';
                            $(container).append(prod_type_data);
                            //selected product types element creation

                            $("#multiple-tax").html(response);
                            $('.taxcountry').val($('#country').val());
                            var country_len = $('.taxcountry').length;
                            for(i=0; i<country_len; i++) {
                            get_tax_states($('#country').val(), 'additional_values_45_'+i, 'country-3_'+i, 'state_'+i);
                            }
                            setTimeout("$('.taxstate').val($('#state').val());",5000);
                            //alert($('#state').val());
                            //$('.taxstate').val($('#state').val());
                        } else {
                            $('#selected_prodtype_div').hide();
                            $("#multiple-tax").html('');
                            $("#selected_prodtype").html('');
                        }
                        $('#taxset-error').hide();
                    }
                }
        }); 
    } else {
        $('#selected_prodtype_div').hide();
        $("#multiple-tax").html('');
        $("#selected_prodtype").html('');
    }
}

function getEditTaxSettings() {
    checked_count = $("input[name='additional_values[5][]']:checked").length;
    var_product_type='';
    $("input[name='additional_values[5][]']:checked").each(function(index) {
        var_product_type += $(this).val()+",";
    });

    if((checked_count>0) && ($("#additional_values_43").val()=='Yes')) {
        userid = $("#userval").val();
        $('#taxset-error').show();
        $.ajax({
            type : 'POST',
            url : 'seller_profile.php',
            data : 'action=edit_tax_setting&count='+checked_count+'&user='+userid+'&product_type_str='+var_product_type,
            success : function(response) {
                    if(response!='') {
                        if($("#additional_values_43").val()=='Yes') {
                            $("#multiple-tax").html(response);
                            var country_len = $('.taxcountry').length;
                            for(i=0; i<country_len; i++) {
                                get_tax_states($('#additional_values_44_'+i).val(), 'additional_values_45_'+i, 'country-3_'+i, 'state_'+i);
                            }

                            //setTimeout("$('#additional_values_45_1').val('Hai');",500);
                            for(i=0; i<country_len; i++) {
                                    setTimeout("$('#additional_values_45_"+i+"').val($('#state_hide_"+i+"').val())",5000);
                            }

                        } else {
                            $("#multiple-tax").html('');
                        }
                        $('#taxset-error').hide();
                    }
                }
        });
    } else {
        $("#multiple-tax").html('');
    }
}

function check_www_ship_product() {

    if($('.www').is(':checked')) {
        $('.continent').removeAttr('checked');
        $("input.continent").attr("disabled", true);
        $('.country').removeAttr('checked');
        $("input.country").attr("disabled", true);
    } else {
        $("input.continent").removeAttr("disabled");
        $("input.country").removeAttr("disabled");
    }
}

function check_continent_ship_product() {

    if($('.continent').is(':checked')) {
        $('.www').removeAttr('checked');
        $("input.www").attr("disabled", true);
        $('.country').removeAttr('checked');
        $("input.country").attr("disabled", true);
    } else {
        $("input.www").removeAttr("disabled");
        $("input.country").removeAttr("disabled");
    }
}

function check_country_ship_product() {

    if($('.country').is(':checked')) {
        $('.www').removeAttr('checked');
        $("input.www").attr("disabled", true);
        $('.continent').removeAttr('checked');
        $("input.continent").attr("disabled", true);
    } else {
        $("input.www").removeAttr("disabled");
        $("input.continent").removeAttr("disabled");
    }
}

function rand_str()
{
    var chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
    var string_length = 5;
    var randomstring = '';
    for(var i=0; i<string_length; i++) {
        var rnum = Math.floor(Math.random() * chars.length);
        randomstring += chars.substring(rnum,rnum+1);
    }
    return randomstring;
}

function checkRequiredFields()
{
    var producttype = $.trim($('#producttype').val());
    if(producttype != '329' && producttype != '4351' && producttype != '4365') {
        if($('#condition').val() == '0') {
            alert("Missing : Condition");
            $('#condition').focus();
            return false;
        }
    }
    var listprice = $.trim($('#list_price').val());
    if(listprice == '' || listprice == '0.00') {
        alert("Missing : List Price");
        $('#list_price').focus();
        return false;
    }
    var shipping_id = $.trim($('#shipping_id').val());
    if(shipping_id == '2') {
        if($('#weight').val() == '') {
            alert("Missing : Weight");
            $('#weight').focus();
            return false;
        }
        if($('#length').val() == '' || $('#width').val() == '' || $('#height').val() == '') {
             alert("Missing : Shipping box dimensions");
            return false;
        }
    } else if(shipping_id == '3') {
        var len = $(".fixedprices").length;
        for(i=0;i<len;i++) {
            if($('#fixedprices_'+i).val() == '') {
                alert("Missing : Fixed Price");
                $('#fixedprices_'+i).focus();
                return false;
            }
        }
    }
}

//Do you ship your products from different address?
function show_ship_adress() {

    if($('#prod_ship_addr').val()=='Yes') {
        $('.prod_bill_addr').show();
    } else {
        var addr = $.trim($('#additional_values_20').val());
        $('#address').val(addr);
        var addr2 = $.trim($('#additional_values_21').val());
        $('#address_2').val(addr2);
        var cit = $.trim($('#additional_values_22').val());
        $('#city').val(cit);
        var country = $.trim($('#additional_values_23').val());
        state = $.trim($('#additional_values_24').val());
        $('#country').val(country);
        get_states(country, 'state', 'country-2');
        setTimeout('placestate(state)', 5000);
        var zip = $.trim($('#additional_values_25').val());
        $('#zipcode').val(zip);
        $('.prod_bill_addr').hide();
    }
};

function validateccdetails()
{
    flag7 = 0;
    if($('#additional_values_47').val() == ''){
        alert('Card Holder Name is empty!');
        $('#additional_values_47').focus();
    } else if ($('#additional_values_47').val() != '' && !isNaN($('#additional_values_47').val())) {
        alert('Card Holder Name is invalid!');
        $('#additional_values_47').focus();
    } else if($('#additional_values_48').val() == ''){
        alert('Card Type is empty!');
        $('#additional_values_48').focus();
    } else if($('#additional_values_49').val() == '') {
        alert('Card Number is empty!');
        $('#additional_values_49').focus();
    } else if ($('#additional_values_49').length>0 && $('#additional_values_49').val() != '' && isNaN($('#additional_values_49').val())) {
        alert('Card Number is invalid!');
        $('#additional_values_49').focus();
    } else if($('#expiry_month').val() == '') {
        alert('Choose your card expiration month');
        $('expiry_month').focus();
    } else if($('#expiry_year').val() == '') {
        alert('Choose your card expiration year');
        $('expiry_year').focus();
    } else if($('#additional_values_51').val() == '') {
        alert('CCV is empty!');
        $('additional_values_51').focus();
    } else if ($('#additional_values_51').length>0 && $('#additional_values_51').val() != '' && isNaN($('#additional_values_51').val())) {
        alert('CCV is invalid!');
        $('#additional_values_51').focus();
    } else if ($('#verify_creditcard').val()!='1') {
        alert('Please verify your creditcard details');
        $("#verify-creditcard").focus();
    } else {
        flag7 = 1;
    }
    return flag7;
}

function triggercategory(str)
{
    var result = str.split(',');

    $("#producttype option[value='"+result[0]+"']").attr("selected", true);
    $("#producttype").trigger("change");

    setTimeout(function(){
        $("#subcategoryid option[value='"+result[1]+"']").attr("selected", true);
        $("#subcategoryid").trigger("change");
    },3000);

    if(result[2]) {
        setTimeout(function(){
            $("#subsubcategoryid option[value='"+result[2]+"']").attr("selected", true);
            $("#subsubcategoryid").trigger("change");
        },6000);
    }

    if(result[3]) {
        setTimeout(function(){
            $("#subsubsubcategoryid option[value='"+result[3]+"']").attr("selected", true);
            $("#subsubsubcategoryid").trigger("change");
        },9000);
    }

    if(result[4]) {
        setTimeout(function(){
            $("#subsubsubsubcategoryid option[value='"+result[4]+"']").attr("selected", true);
            $("#subsubsubsubcategoryid").trigger("change");
        },12000);
    }

    setTimeout(function(){
        fetch_extra_fields();
    },15000);

    setTimeout(function(){
        fetch_extra_fields_values();
    },18000);
    
    setTimeout(function(){
        $('#div-one').removeAttr('class');
        $('#div-two').removeAttr('class');
    },19000);
}

function changppeverify() {
    $('#verify_paypal').val('0');
}

function changecceverify() {
    $('#verify_creditcard').val('0');
}

function fetch_extra_fields()
{
    var categoryid = $('#categoryid').val();
    var string     = 'action=1009&categoryid='+categoryid;

    $.ajax({
        async: true,
        data: string,
        dataType: "json",
        success: function(response){

            if(response.success == '1') {

                $('#extra_fields').html(response.content);
            } else {

                $('#extra_fields').html('');
            }
        },
        timeout: 30000,//timeout to 30 seconds
        type: "GET",
        url: "../ajax_call.php"
    });
}

var make = '';
var make_name = '';
var make_value = '';
function fetch_dropdown_level_1(obj)
{
    var name = obj.value;
    if(name != '0') {
        if(make == '') {
            var query_string = "../ajax_call.php?action=1011";
            $.getJSON(query_string,function(result) {
                make = result.model;
                make_name = result.make_name;
                make_value = result.make_value;
                get_dropdown_list(name);
            });
        } else {
            get_dropdown_list(name);
        }
    }
}

function get_dropdown_list(name)
{
    var html = "";
    for(j=0;j<make_name.length;j++) {
        if(name == make_name[j]) {
            var str = make_value[j].split(',');
            html += "<select name='efields[32]' id='efields_32'>";
            for(k=0;k<str.length;k++) {
                html += "<option value='"+str[k]+"'>"+str[k]+"</option>";
            }
            html += "</select>";
        }
    }
    if(html == '') {
        html += "<input type='text' name='efields[32]' id='efields_32' value='' />";
    }
    $('#dropdown_level_1').html(html);
}

function fetch_extra_fields_values()
{
    var productid = $('#productid').val();
    var query_string = '../ajax_call.php?action=1010&productid='+productid;

    $.getJSON(query_string, function(result) {

        if(result) {

            for(i=0;i<result.id.length;i++) {

                if(result.type[i] == 'textbox') {

                    $('#efields_'+result.id[i]).val(result.value[i]);
                } else if(result.type[i] == 'variant') {

                    var txt = "<a href=#"+result.id[i]+" class=variant_val>Edit</a>";
                    var fld = 'variant_edit_'+result.id[i];
                    $('#'+fld).html(txt);
                    $('.dialog-tools-table').css('visibility', 'visible');
                } else if(result.type[i] == 'dropdown') {

                    $("#efields_"+result.id[i]+" option[value='"+result.value[i]+"']").attr("selected", true);
                    $('#efields_'+result.id[i]).trigger("change");
                } else if(result.type[i] == 'dropdown_level_1') {

                    var id = result.id[i];
                    var value = result.value[i];
                    setTimeout(function() {
                        $("#efields_"+id+" option[value='"+value+"']").attr("selected", true);
                    }, 5000);
                }
            }
        }
    });
}

function same_as_business_address(obj)
{
    if (obj.checked == true) {
        var address = $.trim($('#additional_values_20').val());
        $('#address').val(address);
        var address_2 = $.trim($('#additional_values_21').val());
        $('#address_2').val(address_2);
        var city = $.trim($('#additional_values_22').val());
        $('#city').val(city);
        var country = $.trim($('#additional_values_23').val());
        $('#country').val(country);
        state = $.trim($('#additional_values_24').val());
        get_states(country, 'state', 'country-2');
        setTimeout('placestate(state)', 5000);
        var zip = $.trim($('#additional_values_25').val());
        $('#zipcode').val(zip);
    }
}

/*Home page visitor email grabing function begin here*/
function getCookie(c_name) {

    var i,x,y,ARRcookies=document.cookie.split(";");

    for (i=0;i<ARRcookies.length;i++) {
    x=ARRcookies[i].substr(0,ARRcookies[i].indexOf("="));
    y=ARRcookies[i].substr(ARRcookies[i].indexOf("=")+1);
    x=x.replace(/^\s+|\s+$/g,"");
    if (x==c_name)
        {
        return unescape(y);
        }
    }
}

function checkEmailUserCookie(){
   var emailUser=getCookie("emailuser");
   if (emailUser!=null && emailUser!="") {
   if(emailUser == 'back'){
        $('.message_inner').append('<p>It seems you are already subscribed. Welcome back!</p>');
   }if(emailUser == 'new'){
        $('.message_inner').append('<p>Thank you for subscribing!</p>');
   }
    
    $("#system_message").fadeIn("slow");
    
  }
}
/*Home page visitor email grabing function end here*/

function func_contact_vendor()
{
    $('.vendor-contact').colorbox({
        innerWidth:"500", innerHeight:"300", inline:true, transition: "elastic", speed: 350,opacity: 0.3, preloading: true, overlayClose: false, href:"#vendor-contact-form"       
    });
}

function showHideSubCat(val,id){
    if(val == 1){         
        $('#bvira_sub_cat_container_'+id).show();
        $('#bvira_subsub_cat_container_'+id).show();
        $('#bvira_subsusub_cat_container_'+id).show();        
    }
    if(val == 2){
        $('#bvira_sub_cat_container_'+id).hide();
        $('#bvira_subsub_cat_container_'+id).hide();
        $('#bvira_subsusub_cat_container_'+id).hide();                                    
    }    
}
function showMappedValue(id){
    var cat_value           =   $('#sel_cat_'+id+' option:selected').text();    
    var sub_cat_value       =   $('#sel_subcat_'+id+' option:selected').text();
    var subsub_cat_value    =   $('#sel_subsubcat_'+id+' option:selected').text();        
    var subsubsub_cat_value =   $('#sel_subsubsubcat_'+id+' option:selected').text();        
    var mapped_cat_txt      =   '';
        
    if($('#sel_cat_'+id+' option:selected').val() != 0 && !isNaN($('#sel_cat_'+id+' option:selected').val())){
        mapped_cat_txt = cat_value;
    }
    if($('#sel_subcat_'+id+' option:selected').val() != 0  && !isNaN($('#sel_subcat_'+id+' option:selected').val())){
        mapped_cat_txt += ' -> '+sub_cat_value;
    }
    if($('#sel_subsubcat_'+id+' option:selected').val() != 0  && !isNaN($('#sel_subsubcat_'+id+' option:selected').val())){
        mapped_cat_txt += ' -> '+subsub_cat_value;
    }
    if($('#sel_subsubsubcat_'+id+' option:selected').val() != 0  && !isNaN($('#sel_subsubsubcat_'+id+' option:selected').val())){
        mapped_cat_txt += ' -> '+subsubsub_cat_value;
    }        
    $('#mapped_cat_txt_'+id).html(mapped_cat_txt);                    
}

function showBviraCat(id){    
    $('div[id^="cat_map_container_"]').hide();
    $('#cat_map_container_'+id).show();
    $('#right_container').slideUp(300).delay(800).fadeIn(400);
    $('#chk_vendor_cat_'+id).attr('checked',true);
}

function loadSubCategory(sel_container,id,sel_value,pre_select_value){
    var selected_val        =   sel_value;
    var sel_container_id       =   sel_container+id;    
    showHideSubCat(1,id);                    
    
    if(selected_val == 0){
        if(sel_container == 'sel_subcat_'){    
            $('#sel_subcat_'+id).html('<option vlaue="0">--- Select---</option>');
            $('#sel_subsubcat_'+id).html('<option vlaue="0">--- Select---</option>');
            $('#sel_subsubsubcat_'+id).html('<option vlaue="0">--- Select---</option>');            
        }  
        if(sel_container == 'sel_subsubcat_'){            
            $('#sel_subsubcat_'+id).html('<option vlaue="0">--- Select---</option>');
            $('#sel_subsubsubcat_'+id).html('<option vlaue="0">--- Select---</option>');            
        }  
        if(sel_container == 'sel_subsubsubcat_'){
            $('#sel_subsubsubcat_'+id).html('<option vlaue="0">--- Select---</option>');            
        }
        return false;
    }    
        
    $.ajax({
        type: "POST",
        url: "../ajax_call.php?fetch_category_parents",
        async: true,
        data: "action=fetch_category&product_type_id="+selected_val,
        success: function(result){

            var data = $.trim(result);
            if(data == '404') {                
                $('#sel_subcat_'+id).html('<option vlaue="0">--Select--</option>');                
                $('#sel_subsubcat_'+id).html('<option vlaue="0">--Select--</option>');
                $('#sel_subsubsubcat_'+id).html('<option vlaue="0">--Select--</option>');
            } else {
                $('#'+sel_container_id).html(result);                                
                if(pre_select_value != 0 && !isNaN(pre_select_value)){
                    $('#'+sel_container_id).val(pre_select_value);                
                    showMappedValue(id);
                }
            }
        }
    });    
}

function showSampleDataPreview(){
    var delimiter       = $('input:radio[name=delimiter]:checked').val();
    var header_avail    = $('input:radio[name=is_first_row_header]:checked').val();   
    var other_delimiter = $('#other_text_bx').val();
    
    $.ajax({
    type: "POST",
    url: "multi_prod_upload_settings.php",
    async: true,
    data: "action=showpreview&delimiter="+delimiter+'&is_first_row_header='+header_avail+'&other_text_bx='+other_delimiter,
    success: function(result){

        var data = $.trim(result);
        if(data == '404') {                
            
        }
        else if(data == 'error'){
            $('#sample_data_preview_container').html('Invaid input');
        }
        else {
            $('#sample_data_preview_container').html(result);
        }
    }
});    
}

// To Load the Popupbox
function loadPopupBox()
{
    $('#popup_box').fadeIn("slow");
}

// TO Unload the Popupbox
function unloadPopupBox()
{
    $('#popup_box').fadeOut("slow");
}

//
function fetch_resently_viewed_products(id)
{
    var productid = $.trim(id);
    if(productid == ''){
        return false;
    }
    $.ajax({
        async: true,
        cache: false,
        complete: function(jqXHR, textStatus){

            //alert('Complete : '+textStatus);
        },
        data: "action=rezently_viewed&product_id="+productid,
        dataType: "json",
        error: function(jqXHR, textStatus, errorThrown){

            //alert('Error : '+textStatus+' '+errorThrown);
        },
        success: function(response, textStatus, jqXHR){

            //alert('Success : '+response+' '+textStatus);
            if(response.html) {
                $('#prod_details_rv').html(response.html);
                $('div#prod_recently_viewed div#top-links').hide();
                $('div#prod_recently_viewed ul#pdblck').attr('class', 'row_view grid_view');
            }
        },
        timeout: 30000,//timeout to 30 seconds
        type: "GET",
        url: "custom_prod_details_rv.php"
    });
    $('div#prod_other_product div#top-links').hide();
    $('div#prod_recently_sold div#top-links').hide();
}

function showStatisticMailList(param){    
    $.blockUI({message: ''});
    $.ajax({
    type: "POST",
    url: "mail_list_statistic_report.php",
    async: true,
    data: param,
    success: function(result){
        $.unblockUI({
             onUnblock: function() {  }
         });
        var data = $.trim(result);
        if(data == '404') {                
            
        }
        else if(data == 'error'){
            $('#mail_list_container').html('Invaid input');
        }
        else {
            $('#mail_list_container').html(result);
        }
    }
});    
}

function func_change_products_category()
{
    $('.terms').colorbox({innerWidth:"600", innerHeight:"300", inline:true, href:"#terms"});
}

function func_change_products_salemodule()
{
    $('.ch_sale_module').colorbox({innerWidth:"600", innerHeight:"300", inline:true, href:"#ch_sale_module"});
}

function SendValueToParent()
   {
        var amodepass = $("#amode_password").val();
      if(amodepass!=''){
         $.ajax({
             type: "POST",
             url: "home.php",
             async: true,
             data: "action=amodepass&ampwd="+amodepass,
             success: function(result){
                    var data = $.trim(result);
                    if(data == 'selleradminset') {
                      location.href = 'home.php';
                      window.close();   
                    } else {
                       location.href = 'home.php';
                       window.close();
                    }
                }
      });

              } else{
                  $(".amode_password_error").html('Please enter your admin password');
                  return false;
              }

    } 
    
function func_get_product_category()
{
    var categoryid = $.trim($('#categoryid').val());
    if(categoryid != '') {

        $('#div-one').attr('class', 'jquery-lightbox-overlay jquery-lightbox');
        $('#div-two').attr('class', 'jquery-lightbox-overlay jquery-lightbox');

        $.ajax({
            async: true,
            data: "action=1012&categoryid="+categoryid,
            dataType: "json",
            success: function(response){

                if(response.success == '0') {
                    alert('Page Load Error');
                    return false;
                }
                triggercategory(response.content);
            },
            type: "GET",
            url: "../ajax_call.php"
        });
    } else {

        alert('Page Load Error');
        return false;
    }
}
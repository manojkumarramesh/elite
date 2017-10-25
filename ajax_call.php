<?php

/*----- Include Files -----*/
require_once 'auth.php';
require_once 'include/classes/class.product.php';

/*----- x-cart load default functions -----*/
x_load('user', 'core', 'files');

/*----- Constant Definition for switch case ----- */
define('GET_CATEGORY'                       ,   'fetch_category');
define('GET_PRODUCT_NAME'                   ,   '1014');
define('GET_PRODUCT_TYPES'                  ,   'fetch_product_type');
define('GET_PRODUCT_SALE_TYPES'             ,   'fetch_product_sale_type');
define('UPDATE_PRODUCT_SALE_TYPES'          ,   'update_product_sale_type');
define('GET_FORM_FIELDS'                    ,   'fetch_fields');
define('GET_VPC_CODE'                       ,   'fetch_vpc');
define('GET_PRODUCT_DETAILS'                ,   '1015');
define('GET_PARENTS'                        ,   '1012');
define('VISITOR_EMAIL'                      ,   'home_email_grab');
define('GET_XMALL_PRODUCT_TYPE'             ,   'fetch_xmall_product_type');
define('GET_PRODUCT_EXTRA_FIELDS'           ,   '1009');
define('GET_DROPDOWN_LEVEL_1'               ,   '1011');
define('GET_PRODUCT_EXTRA_FIELDS_VALUES'    ,   '1010');
define('GET_MANUFACTURERS'                  ,   'fetch_manufacturers');
define('GET_STORE'                          ,   'fetch_store');
define('DEL_FAQ_QUES'                       ,   'del_faq_ques');
define('UPDATE_FAQ_QUES'                    ,   'update_faq_ques');
define('FAQ_FEEDBACK'                       ,   'faq_feedback');
define('GET_PRODUCT_OPTIONS'                ,   '1008');
define('GET_CHANGE_PRODUCTS_CATEGORY'       ,   '1000');
define('GET_RV_PRODUCTS'                    ,   'fetch_rv');
define('FILTER_BY_PRICE'                    ,   '1003');
define('FILTER_BY_FIELD'                    ,   '1004');
define('CLEAR_FILTERS'                      ,   '1018');
define('CONTACT_VENDOR'                     ,   '1013');

/*----- action request -----*/
$actions    =   isset($_POST['action']) ? $_POST['action'] : $_GET['action'];

switch($actions)
{
    case GET_CATEGORY:

        $data   =   Product::func_fetch_categories($product_type_id);

        $result =   "<option value='0' selected='selected'>--- SELECT ---</option>";
        if(empty($data) === false && is_array($data)) {

            foreach($data as $key => $value) {

                $result .=  "<option value='".$value['categoryid']."'>".$value['category']."</option>";
            }
        }

        echo $result;
        break;

    case GET_PRODUCT_NAME:

        $sql    =   "SELECT
                        DISTINCT(products.product) AS value
                     FROM
                        $sql_tbl[products] AS products
                     INNER JOIN
                        $sql_tbl[products_categories] AS products_categories
                     ON
                        products_categories.productid = products.productid
                     WHERE
                        products.product LIKE '%".$name."%'
                     AND
                        products_categories.categoryid = ".$categoryid."
                     AND
                        products.forsale = 'Y' LIMIT 0, 10 ";
        $data   =   func_query($sql);

        if(empty($data) === false && is_array($data)) {

            foreach($data as $key => $value) {

                $result[] = $value['value'];
            }
        }

        echo json_encode($result);
        break;

    case GET_PRODUCT_TYPES:

        $id     =   "'5'";
        $data   =   Product::func_fetch_register_field_values($provider_id, $id);

        if(empty($data) === false) {

            $sql2   =   "SELECT
                            categories.categoryid,
                            categories.category
                        FROM
                            $sql_tbl[categories] AS categories
                        WHERE
                            categories.parentid = '0'
                        AND
                            categories.categoryid IN (".$data[0]['value'].")";
            $data2  =   func_query($sql2);
        }

        $result =   "<option value='0' selected='selected'>--- SELECT ---</option>";

        if(empty($data2) === false && is_array($data2)) {

            foreach($data2 as $key => $value) {

                $result .=  "<option value='".$value['categoryid']."'>".$value['category']."</option>";
            }
        }

        echo $result;
        break;

    case GET_PRODUCT_SALE_TYPES:
        
        $sql2   =   "SELECT
                        saleid, salename
                    FROM
                        $sql_tbl[sale_module]
                    WHERE
                        avail = 'Y'";
        $data2  =   func_query($sql2);

        $result =   "<option value='' selected='selected'>--- SELECT ---</option>";

        if(empty($data2) === false && is_array($data2)) {

            foreach($data2 as $key => $value) {

                $result .=  "<option value='".$value['saleid']."'>".$value['salename']."</option>";
            }
        }

        echo $result;
        break;

    case UPDATE_PRODUCT_SALE_TYPES:
        $saleid_productids  =   $_POST['productids'];
        $saleid             =   $_POST['saletype'];
        db_query("UPDATE $sql_tbl[products] SET
                    saleid=$saleid
                WHERE
                    productid IN($saleid_productids)");

        echo 'sale type updated';
        break;

    case GET_FORM_FIELDS:

        $id     =   "'39','42','43','55','56'";
        $data   =   Product::func_fetch_register_field_values($provider_id, $id);

        $result['return_time']  =   $data[0]['value'];
        $result['shipping']     =   $data[1]['value'];
        $result['tax']          =   $data[2]['value'];
        $result['ships_to']     =   $data[3]['value'];
        $result['saleid']       =   $data[4]['value'];

        if(empty($data[3]['value']) === false) {

            $country_list   =   explode(",", $data[3]['value']);
            if(is_array($country_list)) {

                foreach($country_list as $key => $value) {

                    if(array_key_exists($value, $continents)) {
                    
                        $country_name   .=   $continents[$value].",";
                    }
                    else {

                        $countries      .=   "'country_".$value."',";
                    }
                }
                $countries    =   substr($countries, 0, -1);

                if(empty($countries) === false) {

                    $data7  =   Product::func_fetch_countries($countries);
                }

                if(is_array($data7)) {

                    foreach($data7 as $key => $value) {

                        $country_name   .=   $value['value'].",";
                    }
                }
            }
            $country_name               =   substr($country_name, 0, -1);
            $result['country_name']     =   $country_name;
        }

        $data2  =   Product::func_fetch_sale_module($data[4]['value']);

        $result['sale_module']  =   $data2[0]['value'];

        $data3  =   Product::func_fetch_tax_settings($provider_id, $producttype);

        $result['taxcountry']   =   $data3[0]['tax_country'];
        $result['taxstate']     =   $data3[0]['tax_state'];
        $result['taxpercent']   =   $data3[0]['tax_percentage'];

        $data4  =   Product::func_fetch_shipping_type($data[1]['value']);

        $result['shippin']      =   $data4[0]['shipping_type'];

        $sql5   =   "SELECT
                        states.state,
                        states.country_code
                     FROM
                        $sql_tbl[states] AS states
                     WHERE
                        states.code = '".$data3[0]['tax_state']."'
                     AND
                        states.country_code = '".$data3[0]['tax_country']."'";
        $data5  =   func_query($sql5);

        $result['tax_state']    =   $data5[0]['state'];

        $sql6   =   "SELECT
                        customers.currency,
                        customers.status
                     FROM
                        $sql_tbl[customers] AS customers
                     WHERE
                        customers.id = '".$provider_id."'";
        $data6  =   func_query($sql6);

        $result['currency']         =   'USD';
        $result['forsale_status']   =   $data6[0]['status'];
        
        echo json_encode($result);
        break;

    case GET_VPC_CODE:

        $sql    =   "SELECT
                        products.productcode
                     FROM
                        $sql_tbl[products] AS products
                     INNER JOIN
                        $sql_tbl[products_categories] AS products_categories
                     ON
                        (products_categories.productid = products.productid)
                     WHERE
                        products.product = '".$name."'
                     AND
                        products_categories.categoryid = ".$categoryid."
                     AND
                        products.forsale = 'Y'";
        $data   =   func_query($sql);

        if(empty($data) === false && is_array($data)) {

            foreach($data as $key => $value) {

                if($value['productcode'] != '') {

                    $code[] =   $value['productcode'];
                }
            }
        }

        if(count($code) == 0 || (empty($code))) {

            $sku_prefix     =   'BVA';
            $productid      =   func_query("SELECT
                                                MAX(productid) AS max
                                            FROM
                                                $sql_tbl[products]");
            $plus           =   1;
            $productcode    =   $sku_prefix.($productid[0]['max'] + $plus);
        }
        else {
        
            $productcode    =   $code[0];
        }
        
        if(empty($condition) === false) {
            
            $cond   =   " AND products.condition = ".$condition;
        }

        $sql2   =   "SELECT
                        products.vendorproductcode
                     FROM
                        $sql_tbl[products] AS products
                     INNER JOIN
                        $sql_tbl[products_categories] AS products_categories
                     ON
                        (products_categories.productid = products.productid)
                     WHERE
                        products.product = '".$name."'
                     AND
                        products_categories.categoryid = ".$categoryid."
                     ".$cond."
                     AND
                        products.provider = ".$provider."
                     AND
                        products.forsale = 'Y'";
        $data2  =   func_query($sql2);

        if(empty($data2) === false && is_array($data2)) {

            foreach($data2 as $key2 => $value2) {

                if($value2['vendorproductcode'] != '') {

                    $code2[]    =   $value2['vendorproductcode'];
                }
            }
        }

        define('PRODUCT_NEW'                        ,   '1');
        define('PRODUCT_USED'                       ,   '2');
        define('PRODUCT_REFURBISHED'                ,   '3');
        define('PRODUCT_OTHERS'                     ,   '4');
        define('PRODUCT_CONDITION_NOT_APPLICABLE'   ,   '5');

        if(count($code2) == 0
                    || (empty($code2))
                    || $condition == PRODUCT_USED
                    || $condition == PRODUCT_REFURBISHED
                    || $condition == PRODUCT_OTHERS
                    || $condition == PRODUCT_CONDITION_NOT_APPLICABLE) {

            $code_prefix        =   'VPC';
            $productid          =   func_query("SELECT
                                                    MAX(productid) AS max
                                                FROM
                                                    $sql_tbl[products]");
            $plus               =   1;
            $vendorproductcode  =   $code_prefix.($productid[0]['max'] + $plus);
            $product_is_exists  =   0;
        }
        else {

            $vendorproductcode  =   $code2[0];
            $product_is_exists  =   1;
        }

        $result['productcode']          =   $productcode;
        $result['vendorproductcode']    =   $vendorproductcode;
        $result['product_is_exists']    =   $product_is_exists;

        echo json_encode($result);
        break;

    case GET_PRODUCT_DETAILS:

        if($upc_ean_gtin_product_id) {
            
            $distinct   =   "DISTINCT
                                products.upc_ean_gtin_product_id as value,
                                products.mpn_model_number";
            $condition  =   "products.upc_ean_gtin_product_id = ".$upc_ean_gtin_product_id;
        }
        else if($mpn_model_number) {
            
            $distinct   =   "DISTINCT
                                products.mpn_model_number as value,
                                products.upc_ean_gtin_product_id";
            $condition  =   "products.mpn_model_number = ".$mpn_model_number;
        }

        $sql    =   "SELECT
                        ".$distinct.",
                        products.brand_manufacturer_name,
                        products.product
                     FROM
                        $sql_tbl[products_categories] AS products_categories
                     LEFT JOIN
                        $sql_tbl[products] AS products
                     ON
                        (products_categories.productid = products.productid)
                     WHERE
                        ".$condition."
                     AND
                        products_categories.categoryid = ".$categoryid."
                     AND
                        products.forsale = 'Y'
                     LIMIT 0,10";
        $data   =   func_query($sql);

        echo json_encode($data);
        break;

    case GET_PARENTS:

        $result =   array();
        $ids    =   array();

        $result['success'] = 0;

        $sql    =   "SELECT
                        parent.categoryid
                     FROM
                        xcart_categories AS node,
                        xcart_categories AS parent
                     WHERE
                        node.lpos
                     BETWEEN
                        parent.lpos
                     AND
                        parent.rpos
                     AND
                        node.categoryid = ".$categoryid."
                     ORDER BY
                        parent.lpos";
        $data   =   func_query($sql);

        if(empty($data) === false) {

            $result['success'] = 1;

            foreach($data as $value) {

                $ids[] = $value['categoryid'];
            }

            $result['content'] = implode(',', $ids);
        }

        echo json_encode($result);
        break;

     case VISITOR_EMAIL;

         $sql       =   "SELECT
                            email
                         FROM
                            $sql_tbl[newslist_subscription]
                         WHERE
                            email = '$id'";
         $emailcnt  =   func_query_column($sql);

         if(count($emailcnt) > 0) {
            echo '1';
         }
         else {
             db_query("INSERT INTO
                        $sql_tbl[newslist_subscription]
                        (`listid`,`email`,`to_be_sent`,`since_date`,`set_mailer`)
                       VALUES
                        ('','$id','','".XC_TIME."','0')");
             echo '0';
         }
     
         break;

    case GET_XMALL_PRODUCT_TYPE:

        $sql    =   "SELECT
                        register_field_values.value AS value
                     FROM
                        $sql_tbl[customers] AS customers
                     LEFT JOIN
                        $sql_tbl[register_field_values] AS register_field_values
                     ON
                        (customers.id = register_field_values.userid)
                     WHERE
                        customers.username = '$username'
                     AND
                        customers.usertype = 'P'
                     AND
                        register_field_values.fieldid = 5";
        $data   =   func_query($sql);

        if(empty($data) === false) {

            $sql2   =   "SELECT
                            categories.categoryid AS categoryid,
                            categories.category AS category
                         FROM
                            $sql_tbl[categories] AS categories
                         WHERE
                            categories.categoryid IN ( ".$data[0]['value'].")";
            $data2  =   func_query($sql2);

            if(empty($data2) === false && is_array($data2)) {

                $content    =   "";
                $content    .=  "<ul style='text-align: justify; padding-left: 45%;'>";
                foreach($data2 as $key => $value) {

                    $content    .=  "<li class='".$value['categoryid']."'>".$value['category']."</li>";
                }
                $content        .=  "</ul>";

                $result['html']     =   $content;
            }
            else {

                $result['html']     =   "<ul style='text-align: justify; padding-left: 45%;'><li>No Record(s) Found.</li></ul>";
            }
        }
        else {

            $result['html']     =   "<ul style='text-align: justify; padding-left: 45%;'><li>No Record(s) Found.</li></ul>";
        }

        echo json_encode($result);
        break;

    case GET_PRODUCT_EXTRA_FIELDS:

        $result =   array();

        $result['success'] = 0;

        $sql    =   "SELECT
                        extra_fields.fieldid,
                        extra_fields.field,
                        extra_fields.type,
                        extra_fields.value
                     FROM
                        $sql_tbl[extra_fields] AS extra_fields
                     INNER JOIN
                        $sql_tbl[extra_field_mapping] AS extra_field_mapping
                     ON
                        (extra_field_mapping.fieldid = extra_fields.fieldid)
                     WHERE
                        extra_field_mapping.categoryid = ".$categoryid."
                     AND
                        extra_fields.active = 'Y'
                     ORDER BY
                        extra_fields.orderby";
        $data   =   func_query($sql);

        if(empty($data) === false) {

            $result['success'] = 1;

            $smarty->assign('extra_fields_data', $data);
            $result['content'] = func_display('main/extra_fields.tpl', $smarty, false);
        }

        echo json_encode($result);
        break;

    case GET_DROPDOWN_LEVEL_1:

        $sql    =   "SELECT
                        extra_fields.value as value
                     FROM
                        $sql_tbl[extra_fields] AS extra_fields
                     WHERE
                        extra_fields.fieldid = 32";
        $data   =   func_query($sql);

        $elements   =   explode(';', $data[0]['value']);

        foreach($elements as $key => $value) {

            if($value != '') {

                $name           =   explode(':', $value);
                $make_name[]    =   trim($name[0]);
                $make_value[]   =   trim($name[1]);
            }
        }

        $result['model']        =   1;
        $result['make_name']    =   $make_name;
        $result['make_value']   =   $make_value;

        echo json_encode($result);
        break;

    case GET_PRODUCT_EXTRA_FIELDS_VALUES:

        $sql    =   "SELECT
                        extra_fields.fieldid,
                        extra_fields.type,
                        extra_field_values.value
                     FROM
                        $sql_tbl[extra_field_values] AS extra_field_values
                     LEFT JOIN
                        $sql_tbl[extra_fields] AS extra_fields
                     ON
                        (extra_fields.fieldid = extra_field_values.fieldid)
                     WHERE
                        extra_fields.active = 'Y'
                     AND
                        extra_field_values.productid = ".$productid."
                     ORDER BY
                        extra_fields.orderby";
        $data   =   func_query($sql);

        if(empty($data) === false && is_array($data)) {

            foreach($data as $key => $value) {

                $result['id'][]     = trim($value['fieldid']);
                $result['type'][]   = trim($value['type']);
                $result['value'][]  = trim($value['value']);
            }
        }

        echo json_encode($result);
        break;

    case GET_MANUFACTURERS:

        $result =   Product::func_fetch_manufacturers($manufacturer);

        echo json_encode($result);
        break;

    case GET_STORE:

        $result['query'] = urlencode(Product::func_strip_special_chars($query));

        echo json_encode($result);
        break;

    case UPDATE_FAQ_QUES:
           $sql    = "SELECT
                       $sql_tbl[faq_detail].faq_question,
                       $sql_tbl[faq_detail].faq_answer
                       FROM
                       $sql_tbl[faq_detail]
                       WHERE
                       faq_id = '$ques_id'";

        $value =   func_query($sql);
        $result['question'][] = $value[0]['faq_question'];
        $result['answer'][] = $value[0]['faq_answer'];
        echo json_encode($result);
       break;
    
    case DEL_FAQ_QUES:
        $sql    = "DELETE FROM
                   $sql_tbl[faq_detail]
                   WHERE
                   faq_id = '$ques_id'";

         $result =   func_query($sql);
         //echo $result;
       break;
   
   case FAQ_FEEDBACK:

       db_query("INSERT INTO
                        $sql_tbl[faq_feedback]
                        (`id`,`faq_id`,`faq_rating`,`comments`,`source_ip`,`email`,`feedback_date`)
                       VALUES
                        ('','$id','$fdback','$comments','$CLIENT_IP','$email','".XC_TIME."')");

                echo 'Thank you for your feedback';
   break;

    case GET_PRODUCT_OPTIONS:

        $sql    =   "SELECT
                        classes.classid
                     FROM
                        $sql_tbl[classes] AS classes
                     WHERE
                        classes.class = '".addslashes($class)."'
                     AND
                        classes.productid = ".$productid;
        $result =   func_query_first($sql, USE_SQL_DATA_CACHE);

        echo json_encode($result);
        break;
    
    case GET_CHANGE_PRODUCTS_CATEGORY:

        $productids =   explode(',', $productids);
        
        foreach($productids as $key => $value) {
            
            $this_data  =   array(
                                'categoryid' => $categoryid
                            );
            func_array2update('products_categories', $this_data, "productid = '".$value."'");
        }
        
        $sql    =   "SELECT
                        DISTINCT(extra_fields.field)
                     FROM
                        $sql_tbl[extra_fields] AS extra_fields
                     WHERE
                        extra_fields.type = 'variant'
                     AND
                        extra_fields.active = 'Y'
                     AND
                        extra_fields.categoryid IN (".$category_ids.")";
        $result =   func_query($sql);
        
        if(empty($result)) {

            return false;
        }
        
        foreach($productids as $key => $value) {

            foreach($result as $k => $v) {

                $if_exists = func_query_first_cell("select count(*) from $sql_tbl[classes] where class = '".$v['field']."' and productid = '".$value."'");

                if(empty($if_exists)) {

                    $this_data  =   array(
                                        'productid'     =>  $value,
                                        'class'         =>  $v['field'],
                                        'classtext'     =>  $v['field'],
                                        'avail'         =>  'Y',
                                        'orderby'       =>  '0',
                                        'is_modifier'   =>  ''
                                    );
                    func_array2insert('classes', $this_data, true);
                }
            }
        }

        echo 1000;
        break;

        case GET_RV_PRODUCTS:
            
            $sql    =  "SELECT temp.productid, temp.product, temp.updated,
                       temp.provider,temp.saleid, temp.image_path,
                       temp.image_type, temp.image_x, temp.image_y,
                      temp.image_size,temp.filename, temp.price, temp.date FROM
                (
                   SELECT
                DISTINCT($sql_tbl[products].productid),
                $sql_tbl[products].product,
                $sql_tbl[products].provider,
                $sql_tbl[products].saleid,
                $sql_tbl[images_PL].image_path,
                $sql_tbl[images_PL].image_type,
                $sql_tbl[images_PL].image_x,
                $sql_tbl[images_PL].image_y,
                $sql_tbl[images_PL].image_size,
                $sql_tbl[images_PL].filename,
                $sql_tbl[pricing].price,
                $sql_tbl[stats_shop].date,
                FROM_UNIXTIME($sql_tbl[stats_shop].date) AS updated
                FROM
                $sql_tbl[stats_shop]
                INNER JOIN
                $sql_tbl[products]
                ON
                    ($sql_tbl[products].productid = $sql_tbl[stats_shop].id)
                JOIN
                $sql_tbl[images_PL]
                ON
                ($sql_tbl[images_PL].id = $sql_tbl[products].productid)
                JOIN
                    $sql_tbl[pricing]
                 ON
                    ($sql_tbl[pricing].productid = $sql_tbl[products].productid)
                WHERE
                $sql_tbl[stats_shop].action='V'
                ORDER BY
                $sql_tbl[stats_shop].date DESC
                LIMIT 50
                ) as temp
                GROUP BY temp.productid
                ORDER BY temp.updated DESC";
            $listarr =   func_query($sql);
            
            $content = "";
              for($i=0; $i<count($listarr); $i++){
                  $content .= '<li class="newArrivalsLI" id="'.$listarr[$i]['productid'].'">
                                    <div class="na_prodblock">
                                        <div class="na_thumbcol" style="width: 140px; overflow: hidden;">
                                        <a href="product.php?productid='.$listarr[$i]['productid'].'" title="'.$listarr[$i]['product'].'">';
                  
                 // $content .= '<img src="image.php?type=PL&id='.$listarr[$i]['productid'].'" />' ;
                  $content .= '</a>
                            </div>';
                  $content .= '<div style="text-align:center;">
                                  <a href="product.php?productid='.$listarr[$i]['productid'].'" title="'.$listarr[$i]['product'].'">';
                  $content .= $listarr[$i]['product'];
                  $content .= '</a>
                                <span style="color:#ff0000; font-size: 1.2em;">'.$listarr[$i]['price'].'</span>
                            </div>
                           <div class="clearing"></div>
                        </div>
                    </li>';
              }
        $result['last'] = $listarr[0]['date'];
        $result['html'] = $content;
        echo json_encode($result);
     break;

    case FILTER_BY_PRICE:

        if(empty($XCART_SESSION_VARS['filter_by_from_price']) === false) {

            x_session_unregister('filter_by_from_price');
        }

        if(empty($from_price) === false) {

            x_session_register('filter_by_from_price', $from_price);
        }

        if(empty($XCART_SESSION_VARS['filter_by_to_price']) === false) {

            x_session_unregister('filter_by_to_price');
        }

        if(empty($to_price) === false) {

            x_session_register('filter_by_to_price', $to_price);
        }

        $result['success'] = 1;

        echo json_encode($result);
        break;
        
    case FILTER_BY_FIELD:

        if(isset($value) && empty($value) === false) {

            if(empty($XCART_SESSION_VARS['filter_by_'.$field]) === false) {

                x_session_unregister('filter_by_'.$field);
            }
            x_session_register('filter_by_'.$field, $value);
        }
        else {

            x_session_unregister('filter_by_'.$field);
        }

        $result['success'] = 1;

        echo json_encode($result);
        break;

    case CONTACT_VENDOR:

        x_load('crypt','mail','user','templater','pages','core');

        $to                 = $_POST['to']      = stripslashes($_POST['to']);
        $body               = $_POST['body']    = stripslashes($_POST['body']);
        $from               = $_POST['email']   = stripslashes($_POST['email']);
        $contact            = $_POST;
        $contact['titleid'] = func_detect_title($contact['title']);
        $result['success']  = 0;

        //For email as login option, replace username with email
        if($config['email_as_login'] == 'Y') {

            $contact['username'] = $contact['email'];
        }
        
        $contact            = func_stripslashes($contact);
        $mail_smarty->assign('contact', $contact);

        if(func_send_mail($to, 'mail/vendor_contactus_subj.tpl', 'mail/vendor_contact.tpl', $from, false)) {

            $result['success'] = 1;
        }

        echo json_encode($result);
        break;

    case CLEAR_FILTERS:

        x_load('product');
        func_clear_filters();

        $result['success'] = 1;

        echo json_encode($result);
        break;

    default:

        echo 404;
        break;
}
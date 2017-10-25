<?php

class Product {

    /**
     *
     * To fetch Vendor Products Information
     *
     * @param   providerid
     * @return  array
     */
    public function func_fetch_vendor_total_products($providerid)
    {
        global $sql_tbl;

        $sql    =   "SELECT
                        COUNT(products.productid) AS total,
                        SUM(products.sales_stats) AS sold,
                        SUM(products.views_stats) AS viewed
                     FROM
                        $sql_tbl[products] AS products
                     INNER JOIN
                        $sql_tbl[products_categories] AS products_categories
                     ON
                        products_categories.productid = products.productid
                     INNER JOIN
                        $sql_tbl[categories] AS categories
                     ON
                        categories.categoryid = products_categories.categoryid
                     AND
                        categories.avail = 'Y'
                     WHERE
                        products.provider = ".$providerid."
                     AND
                        products.forsale = 'Y'";
        $data   =   func_query_first($sql, USE_SQL_DATA_CACHE);

        if(empty($data)) {

            return false;
        }
        
        $result['total']    =   $data['total'];
        $result['sold']     =   $data['sold'];
        $result['viewed']   =   $data['viewed'];

        return json_encode($result);
    }

    /**
     *
     * To fetch Vendor Feedback Information
     *
     * @param   providerid
     * @return  
     */
    public function func_fetch_vendor_feedback($providerid)
    {
        global $sql_tbl, $vendor_data;

        $sql    =   "SELECT
                        DISTINCT(products.productid),
                        product_votes.vote_value
                     FROM
                        $sql_tbl[product_votes] AS product_votes
                     JOIN
                        $sql_tbl[products] AS products
                     ON
                        products.productid = product_votes.productid
                     WHERE
                        products.provider = ".$providerid;
        $data   =   func_query($sql, USE_SQL_DATA_CACHE);

        if(empty($data)) {

            return false;
        }
        
        $vote_count     =   count($data);
        $product_count  =   $vendor_data->total;
        $result         =   round(($vote_count / $product_count) * 100);

        return $result;
    }

    /**
     *
     * To fetch Vendor Products
     *
     * @param   providerid
     * @return  
     */
    public function func_fetch_vendor_products($providerid)
    {
        global $sql_tbl, $active_modules, $xmall_store_info;

        #for xmall issue - starts
        if(empty($active_modules['BCSE_Xmall']) === false && 
                        empty($xmall_store_info) === false) {

            $where_provider_condition   =   " AND
                                                $sql_tbl[categories].categoryid
                                            IN
                                         (".$xmall_store_info['subcategory'].")";
        }
        #for xmall issue - ends

        $sql    =   "SELECT
                        $sql_tbl[products].productid,
                        $sql_tbl[products].product,                        
                        $sql_tbl[images_T].image_path,
                        $sql_tbl[images_T].image_x,
                        $sql_tbl[images_T].image_y,
                        $sql_tbl[customers].provider_cust_id,
                        $sql_tbl[customers].username,
                        $sql_tbl[xmall_stores].provider,
                        $sql_tbl[xmall_stores].directory
                     FROM
                        $sql_tbl[products]                     
                     INNER JOIN
                        $sql_tbl[images_T]
                     ON
                        ($sql_tbl[products].productid = $sql_tbl[images_T].id)
                     INNER JOIN
                        $sql_tbl[customers]
                     ON
                        ($sql_tbl[products].provider = $sql_tbl[customers].id)   
                     INNER JOIN
                        $sql_tbl[xmall_stores]
                     ON
                        ($sql_tbl[customers].username = 
                                                 $sql_tbl[xmall_stores].provider)                              
                     WHERE
                        $sql_tbl[products].provider = ".$providerid."
                     AND
                        $sql_tbl[products].forsale = 'Y'                     
                     ".$where_provider_condition."
                     ORDER BY
                        $sql_tbl[products].productid DESC LIMIT 3";
        $result =   func_query($sql);

        return $result;
    }

    /**
     *
     * To fetch MID Image
     *
     * @param   
     * @return  
     */
    public function func_fetch_other_images_mid($productid)
    {
        global $sql_tbl;

        $sql    =   "SELECT
                        $sql_tbl[images_MID].id, 
                        $sql_tbl[images_MID].image_path,
                        $sql_tbl[images_MID].image_x,
                        $sql_tbl[images_MID].image_y,
                        $sql_tbl[images_MID].filename,
                        $sql_tbl[images_MID].image_type
                     FROM
                        $sql_tbl[images_MID]
                     LEFT JOIN
                        $sql_tbl[products]
                     ON
                        ($sql_tbl[products].productid = $sql_tbl[images_MID].id)
                     WHERE
                        $sql_tbl[products].productid = ".$productid." ORDER BY id DESC";
        $result =   func_query($sql);

        return $result;
    }

    /**
     *
     * To fetch T Image
     *
     * @param   
     * @return  
     */
    public function func_fetch_other_images_t($productid)
    {
        global $sql_tbl;

        $sql    =   "SELECT
                        $sql_tbl[images_T].id,
                        $sql_tbl[images_T].image_path,
                        $sql_tbl[images_T].image_x,
                        $sql_tbl[images_T].image_y,
                        $sql_tbl[images_T].filename,
                        $sql_tbl[images_T].image_type
                     FROM
                        $sql_tbl[products]
                     LEFT JOIN
                        $sql_tbl[images_T]
                     ON
                        ($sql_tbl[products].productid = $sql_tbl[images_T].id)
                     WHERE
                        $sql_tbl[products].productid = ".$productid." 
                    ORDER BY $sql_tbl[images_T].id DESC";
        $result =   func_query($sql);

        return $result;
    }

    /**
     *
     * To fetch other Images
     *
     * @param   
     * @return  
     */
    public function func_fetch_other_imagest($productid)
    {
        global $sql_tbl;
        
       $sql     = "SELECT pr.productid,p.image_path pimgpath,p.id pid,p.image_x px,p.image_y py ,p.image_type pimgtype,
                           mid.image_path mimgpath,mid.id mid,mid.image_x mx,mid.image_y my,mid.image_type mimgtype,
                           t.image_path timgpath,t.id tid,t.image_x tx,t.image_y ty,t.image_type timgtype
                    FROM `xcart_images_P` p
                    INNER JOIN xcart_products pr on p.id = pr.productid 
                    LEFT JOIN xcart_images_MID mid ON mid.id =  p.id and mid.pos = p.pos
                    LEFT JOIN xcart_images_T t ON t.id =  p.id and t.pos = p.pos
                    WHERE p.id=".$productid;
      
        $result1 =   func_query($sql);
       return ($result1);
    }
    
    /**
     *
     * To fetch Vendor Information
     *
     * @param   providerid
     * @return  
     */
    public function func_fetch_vendor_info($providerid)
    {
        global $sql_tbl, $continents;

        $id     =   "'34','38','39','55'";   
        $data   =   self::func_fetch_register_field_values($providerid, $id);

        $payment_method_data    = func_query_first_cell("select display_name 
                                    from $sql_tbl[payment_methods] a 
                                    join
                                         $sql_tbl[seller_payment_methods] b
                                    on a.paymentid=b.payment_id 
                                    and b.seller_id=$providerid   
                                    
                ");
        
        if(empty($payment_method_data)  === false) {
            $result['payment_method']   =   $payment_method_data;
        }
        
        /*if(empty($data[0]['value']) === false) {

            $result['payment_method']   =   $data[0]['value'];
        }*/

        if(empty($data[3]['value']) === false) {

            $country_list   =   explode(',', $data[3]['value']);
            if(is_array($country_list)) {

                foreach($country_list as $key => $value) {

                    if(array_key_exists($value, $continents)) {
                    
                        $country_name   .=   $continents[$value]."<br/>";
                    }
                    else {

                        $countries      .=  "'country_".$value."',";
                    }
                }
                $countries  =   substr($countries, 0, -1);

                if(empty($countries) === false) {

                    $data2  =   self::func_fetch_countries($countries);
                }

                if(is_array($data2)) {

                    foreach($data2 as $key => $value) {

                        $country_name    .=   $value['value']."<br/>";
                    }
                }

                $result['ship_to']  =   $country_name;
            }
        }
        
        if(empty($data[1]['value']) === false) {
            if($data[1]['value']    ==  'Yes'){
                if(empty($data[2]['value'])    === false) {
                    $result['vendor_return_time']   =   $data[2]['value'];
                }    
            } else {
                $result['vendor_return_time']   =   'Not Accepted';
            }
        }

        return $result;
    }

    /**
     *
     * To fetch Parent CategoryId
     *
     * @param   categoryid
     * @return  
     */
    public function func_fetch_parent_categoryid($categoryid)
    {
        if(empty($categoryid) === false) {

            $sql        =   "SELECT
                                parent.categoryid,
                                parent.parentid
                             FROM
                                xcart_categories AS parent,
                                xcart_categories AS child
                             WHERE
                                child.parentid = parent.categoryid
                             AND
                                child.categoryid = ".$categoryid;
            $result     =   func_query($sql);
        }

        if(empty($result) === false) {

            if($result[0]['parentid'] == '0') {

                return $result[0]['categoryid'];
            }
            else {

                return self::func_fetch_parent_categoryid($result[0]['categoryid']);
            }
        }
        else {

            return $categoryid;
        }
    }

    /**
     *
     * To fetch products count
     * for buy-now, bargain
     * @param   
     * @return  array
     */
    public function func_fetch_products_count()
    {
        global $sql_tbl, $config, $product_query;

        if($config['General']['show_outofstock_products'] != 'Y') {

            $condition  =   " AND $sql_tbl[products].avail > 0 ";
        }

        $sql    =   $product_query."
                    AND
                        $sql_tbl[products].saleid = 0
                    ".$condition."
                    GROUP BY
                        $sql_tbl[products].productid";
        $data   =   func_query($sql);

        $sql1   =   $product_query."
                    AND
                        $sql_tbl[products].saleid = 1
                    AND
                        $sql_tbl[products].lowest_possible_price > 0
                    AND
                        $sql_tbl[products].lowest_possible_price < $sql_tbl[products].list_price
                    ".$condition."
                    GROUP BY
                        $sql_tbl[products].productid";
        $data1  =   func_query($sql1);

        $total_buy_now  =   (empty($data) === false)  ? count($data)  : 0;
        $total_bargain  =   (empty($data1) === false) ? count($data1) : 0;
        $total_products =   $total_buy_now + $total_bargain;

        $result['total_products']   =   $total_products;
        $result['total_buy_now']    =   $total_buy_now;
        $result['total_bargain']    =   $total_bargain;

        return json_encode($result);
    }

    /**
     *
     * To fetch Manufacturers Name
     *
     * @param   $manufacturer
     * @return  array
     */
    public function func_fetch_manufacturers($manufacturer)
    {
        global $sql_tbl;

        $sql    =   "SELECT
                        manufacturers.manufacturerid,
                        manufacturers.manufacturer as value
                     FROM
                        $sql_tbl[manufacturers] AS manufacturers
                     WHERE
                        manufacturers.manufacturer
                     LIKE
                        '%".$manufacturer."%'
                     AND
                        manufacturers.avail = 'Y'
                     ORDER BY
                        manufacturers.manufacturer
                     LIMIT 0,10";
        $result =   func_query($sql);

        return $result;
    }

    /**
     *
     * To fetch Providers registration field values
     *
     * @param   $providerid, $fieldid
     * @return  array
     */
    public function func_fetch_register_field_values($providerid, $fieldid)
    {
        global $sql_tbl;

        $sql    =   "SELECT
                        register_field_values.value as value
                     FROM
                        $sql_tbl[register_fields] AS register_fields
                     LEFT JOIN
                        $sql_tbl[register_field_values] AS register_field_values
                     ON
                        (register_fields.fieldid = register_field_values.fieldid
                     AND
                        register_field_values.userid = ".$providerid.")
                     WHERE
                        register_fields.section = 'A'
                     AND
                        register_fields.fieldid IN (".$fieldid.")
                     ORDER BY
                        register_fields.fieldid";
        $result =   func_query($sql);

        return $result;
    }

     /**
     *
     * To fetch Sale Module
     *
     * @param   $saleid
     * @return  array
     */
    public function func_fetch_sale_module($saleid)
    {
        global $sql_tbl;

        $sql    =   "SELECT
                        sale_module.salename as value
                     FROM
                        $sql_tbl[sale_module] AS sale_module
                     WHERE
                        sale_module.saleid = ".$saleid;
        $result =   func_query($sql);

        return $result;
    }

    /**
     *
     * To fetch Tax Settings
     *
     * @param   $providerid, $producttype
     * @return  array
     */
    public function func_fetch_tax_settings($providerid, $producttype)
    {
        global $sql_tbl;

        $sql    =   "SELECT
                        tax_settings.*
                     FROM
                        $sql_tbl[tax_settings] AS tax_settings
                     WHERE
                        tax_settings.seller_id = ".$providerid."
                     AND
                        tax_settings.product_type_id = ".$producttype;
        $result =   func_query($sql);

        return $result;
    }

    /**
     *
     * To fetch Shipping Type
     *
     * @param   $id
     * @return  array
     */
    public function func_fetch_shipping_type($id)
    {
        global $sql_tbl;

        $sql    =   "SELECT
                        shipping_types.shipping_type
                     FROM
                        $sql_tbl[shipping_types] AS shipping_types
                     WHERE
                        shipping_types.id = ".$id;
        $result =   func_query($sql);

        return $result;
    }

    /**
     *
     * To fetch 
     *
     * @param   $parentid
     * @return  array
     */
    public function func_fetch_categories($parentid)
    {
        global $sql_tbl;

        $sql    =   "SELECT
                        categories.categoryid,
                        categories.category
                     FROM
                        $sql_tbl[categories] AS categories
                     WHERE
                        categories.parentid = ".$parentid."
                     AND
                        categories.avail = 'Y'";
        $result =   func_query($sql);

        return $result;
    }

    /**
     *
     * To fetch Country lists
     *
     * @param   $countries
     * @return  array
     */
    public function func_fetch_countries($countries)
    {
        global $sql_tbl;

         $sql   =   "SELECT
                        languages.value
                     FROM
                        $sql_tbl[languages] AS languages
                     WHERE
                        languages.name IN (".$countries.")
                     AND
                        languages.topic = 'Countries'";
        $result =   func_query($sql);

        return $result;
    }

    /**
     *
     * To fetch Drop Shippers lists
     *
     * @param   
     * @return  array
     */
    public function func_fetch_drop_shippers()
    {
        global $sql_tbl;

         $sql   =   "SELECT
                        $sql_tbl[dropshipper].id,
                        $sql_tbl[dropshipper].name
                     FROM
                        $sql_tbl[dropshipper]
                     ORDER BY
                        $sql_tbl[dropshipper].name asc";
        $result =   func_query($sql);

        return $result;
    }

    /**
     *
     * To strip special characters
     *
     * @param   $string
     * @return  
     */
    public function func_strip_special_chars($string)
    {
        $string = preg_replace("/[^A-Za-z0-9]+/"," ",$string);
        return $string;
    }

    /**
     *
     * To calculate fixed price during checkout process
     *
     * @param   $providerid, $product_ids
     * @return  $result
     */
    public function func_calculate_shipping_fixed_price($providerid, $product_ids)
    {
        global $sql_tbl;

         $sql   =   "SELECT
                        $sql_tbl[shipping_fixedprice].productid,
                        $sql_tbl[shipping_fixedprice].country_code,
                        $sql_tbl[shipping_fixedprice].fixedprice,
                        $sql_tbl[shipping_fixedprice].vendor_fixedprice
                     FROM
                        $sql_tbl[shipping_fixedprice]
                     WHERE
                        $sql_tbl[shipping_fixedprice].provider = ".$providerid."
                     AND
                        $sql_tbl[shipping_fixedprice].productid IN (".$product_ids.")";
        $result =   func_query($sql);

        return $result;
    }
    
    /**
     *
     * To fetch Shipping Types used in bvira
     *
     * @param   
     * @return  array
     */
    public function func_fetch_bvira_shipping_types()
    {
        global $sql_tbl;

        $sql    =   "SELECT
                        $sql_tbl[shipping_types].id,
                        $sql_tbl[shipping_types].shipping_type
                     FROM
                        $sql_tbl[shipping_types]";
        $result =   func_query($sql);

        return $result;
    }

    /**
     *
     * To fetch seller store child categories
     *
     * @param   $providerid, categoryid
     * @return  array
     */
    public function func_store_child_categories($providerid, $categoryid)
    {
        global $sql_tbl;

        $sql    =   "SELECT
                        seller_categories.categoryid,
                        categories.category,
                        seller_categories.product_count as total
                    FROM
                        $sql_tbl[seller_categories] AS seller_categories
                    INNER JOIN
                        $sql_tbl[categories] AS categories
                    ON
                        categories.categoryid = seller_categories.categoryid
                    WHERE
                        seller_categories.provider = ".$providerid."
                    AND
                        categories.parentid = ".$categoryid."
                    ORDER BY
                        categories.categoryid";
        $result =   func_query($sql, USE_SQL_DATA_CACHE);

        return json_encode($result);
    }

    /**
     *
     * To fetch seller store categories
     *
     * @param   $providerid
     * @return  array
     */
    public function func_store_categories($providerid)
    {
        global $sql_tbl;
        
        /*if($providerid == 2) {

            $condition  =   "AND categories.categoryid IN (1)";
        }

        $sql    =   "SELECT
                        categories.categoryid,
                        categories.category,
                        COUNT(products.productid) as total
                     FROM
                        $sql_tbl[products] AS products
                     JOIN
                        $sql_tbl[products_categories] AS products_categories
                     ON
                        (products_categories.productid = products.productid)
                     JOIN
                        $sql_tbl[categories] AS categories
                     ON
                        (categories.categoryid = products_categories.categoryid)
                     AND
                        categories.avail = 'Y'
                     WHERE
                        products.provider = ".$providerid."
                     AND
                        products.forsale = 'Y'
                        ".$condition."
                     GROUP BY
                        categories.categoryid
                     ORDER BY
                        categories.categoryid";*/
        $sql    =   "SELECT
                        seller_categories.categoryid,
                        categories.category,
                        seller_categories.product_count as total
                     FROM
                        $sql_tbl[seller_categories] AS seller_categories
                     INNER JOIN
                        $sql_tbl[categories] AS categories
                     ON
                        categories.categoryid = seller_categories.categoryid
                     AND
                        categories.avail = 'Y'
                     WHERE
                        seller_categories.provider = ".$providerid."
                     ORDER BY
                        seller_categories.categoryid";
        $result =   func_query($sql, USE_SQL_DATA_CACHE);

        return $result;
    }

}
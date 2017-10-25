<?php

/**
 * Filter by interface
 *
 * @category   X-Cart
 * @subpackage Customer interface
 * @author     Manoj <manoj@elitehour.com>
 */

//ini_set("display_errors", 1);

final class Filter {

    public function __construct()
    {
        global $sql_tbl, $smarty, $XCART_SESSION_VARS;

        $result =   db_query("CREATE TEMPORARY TABLE IF NOT EXISTS `bvira_filter_by_tmp` 
                               (`productid` int(11) NOT NULL, PRIMARY KEY (`productid`)) 
                                ENGINE=MyISAM DEFAULT CHARSET=latin1");

        $sql    =   base64_decode($XCART_SESSION_VARS['product_search_query']);
        $result =   db_query("INSERT INTO bvira_filter_by_tmp ".$sql);

        $this->sql_tbl      =   $sql_tbl;
        $this->smarty       =   $smarty;
        $this->cat          =   (int)    $XCART_SESSION_VARS['filter_by_cat'];
        $this->from_price   =   (float)  $XCART_SESSION_VARS['filter_by_from_price'];
        $this->to_price     =   (float)  $XCART_SESSION_VARS['filter_by_to_price'];
        $this->brand        =   (string) $XCART_SESSION_VARS['filter_by_brand'];
        $this->provider     =   (string) $XCART_SESSION_VARS['filter_by_provider'];
        $this->gender       =   (string) $XCART_SESSION_VARS['filter_by_gender'];
        $this->type         =   (string) $XCART_SESSION_VARS['filter_by_type'];
        $this->color        =   (string) $XCART_SESSION_VARS['filter_by_color'];
        $this->size         =   (string) $XCART_SESSION_VARS['filter_by_size'];
        $this->occasion     =   (string) $XCART_SESSION_VARS['filter_by_occasion'];
        $this->material     =   (string) $XCART_SESSION_VARS['filter_by_material'];
        $this->made_in      =   (string) $XCART_SESSION_VARS['filter_by_made_in'];

        self::func_filter_by_options();
    }

    private function func_filter_by_options()
    {
        self::func_filter_by_price();

        self::func_filter_by_brand();

        self::func_filter_by_provider();

        self::func_filter_by_extra_fields();
    }

    private function func_filter_by_price()
    {
        $this->smarty->assign('from_price', $this->from_price);
        $this->smarty->assign('to_price', $this->to_price);
    }

    private function func_filter_by_brand()
    {
        $sql    =   "SELECT
                        manufacturers.manufacturerid,
                        manufacturers.manufacturer,
                        COUNT(manufacturers.manufacturerid) AS total
                     FROM
                        ".$this->sql_tbl['manufacturers']." AS manufacturers
                     INNER JOIN
                        ".$this->sql_tbl['products']." AS products
                     ON
                        (products.manufacturerid = manufacturers.manufacturerid
                     AND
                        manufacturers.avail = 'Y')
                     INNER JOIN
                        ".$this->sql_tbl['filter_by_tmp']." AS filter_by_tmp
                     ON
                        (filter_by_tmp.productid = products.productid)
                     GROUP BY
                        manufacturers.manufacturerid
                     ORDER BY
                        manufacturers.manufacturer";
        $result =   func_query($sql);

        if(empty($result) === false && isset($this->brand) && empty($this->brand) === false) {

            $brand_array    =   explode(',', $this->brand);

            foreach($result as $key => $value) {

                if(in_array($value['manufacturerid'], $brand_array)) {

                    $result[$key]['checkbox'] = 'checked';
                    $this->smarty->assign('clear_filter', 1);
                }
            }
        }

        $this->smarty->assign('filter_by_brand', $result);
    }

    private function func_filter_by_provider()
    {
        $sql    =   "SELECT
                        customers.id,
                        customers.firstname,
                        customers.lastname,
                        register_field_values.value AS business_name,
                        COUNT(customers.id) AS total
                     FROM
                        ".$this->sql_tbl['customers']." AS customers
                     INNER JOIN
                        ".$this->sql_tbl['products']." AS products
                     ON
                        (products.provider = customers.id)
                     INNER JOIN
                        ".$this->sql_tbl['filter_by_tmp']." AS filter_by_tmp
                     ON
                        (filter_by_tmp.productid = products.productid)
                     LEFT JOIN
                        ".$this->sql_tbl['register_field_values']." AS register_field_values
                     ON 
                        (customers.id = register_field_values.userid
                     AND
                        register_field_values.fieldid = 15)
                     GROUP BY
                        customers.id
                     ORDER BY
                        business_name, customers.firstname, customers.lastname";
        $result =   func_query($sql);

        if(empty($result) === false && isset($this->provider) && empty($this->provider) === false) {

            $provider_array =   explode(',', $this->provider);

            foreach($result as $key => $value) {

                if(in_array($value['id'], $provider_array)) {

                    $result[$key]['checkbox'] = 'checked';
                    $this->smarty->assign('clear_filter', 1);
                }
            }
        }

        $this->smarty->assign('filter_by_provider', $result);
    }

    private function func_filter_by_extra_fields()
    {
        $sql    =   "SELECT
                        DISTINCT(extra_fields.fieldid),
                        extra_fields.field,
                        extra_fields.type,
                        extra_fields.value
                     FROM
                        ".$this->sql_tbl['extra_fields']." AS extra_fields
                     INNER JOIN
                        ".$this->sql_tbl['extra_field_mapping']." AS extra_field_mapping
                     ON
                        (extra_field_mapping.fieldid = extra_fields.fieldid)
                     WHERE
                        extra_field_mapping.categoryid = ".$this->cat."
                     AND
                        extra_fields.active = 'Y'
                     AND
                        extra_fields.is_filter = 'Y'
                     ORDER BY
                        extra_fields.fieldid";
        $result =   func_query($sql);

        if(empty($result)) {

            return false;
        }

        $data   =   array();

        foreach($result as $value) {

            $field = strtolower($value['field']);
            $field = str_replace(' ', '_', $field);

            if($value['type'] == 'dropdown' && empty($value['value']) === false) {

                $data[$field] = self::func_filter_by_dropdown_value($value['field'], $value['fieldid'], $value['value']);
            }

            if($value['type'] == 'variant') {

                $data[$field] = self::func_filter_by_variant_value($value['field']);
            }

            if($value['type'] == 'textbox') {

                $data[$field] = self::func_filter_by_textbox_value($value['field'], $value['fieldid']);
            }
        }

        if(empty($data) === false) {

            foreach($data as $key => $value) {

                $key = strtolower($key);

                if(empty($key) === false && isset($this->$key) && empty($this->$key) === false) {

                    $this->smarty->assign('selected_'.$key, $this->$key);
                    $this->smarty->assign('clear_filter', 1);
                }
            }
        }

        $this->smarty->assign('filter_by_extra_field', $data);
    }

    private function func_filter_by_dropdown_value($field, $fieldid, $value)
    {
        $value  =   explode(';', $value);

        $sql    =   "SELECT
                        TRIM(extra_field_values.value) AS value,
                        COUNT(extra_field_values.value) AS total
                     FROM
                        ".$this->sql_tbl['extra_field_values']." AS extra_field_values
                     INNER JOIN
                        ".$this->sql_tbl['filter_by_tmp']." AS filter_by_tmp
                     ON
                        (filter_by_tmp.productid = extra_field_values.productid)
                     WHERE
                        extra_field_values.fieldid = ".$fieldid."
                     AND
                        (value LIKE '%".implode("%' OR value LIKE '%", $value)."%')
                     GROUP BY
                        value
                     ORDER BY
                        value";
        $result =   func_query($sql);

        if(empty($result)) {

            return false;
        }

        return $data[$field] = $result;
    }

    private function func_filter_by_variant_value($field)
    {
        $sql    =   "SELECT
                        TRIM(class_options.option_name) AS value,
                        COUNT(class_options.option_name) AS total
                     FROM
                        ".$this->sql_tbl['classes']." AS classes
                     INNER JOIN
                        ".$this->sql_tbl['class_options']." AS class_options
                     ON
                        (classes.classid = class_options.classid
                     AND
                        classes.class LIKE '%".$field."%')
                     INNER JOIN
                        ".$this->sql_tbl['filter_by_tmp']." AS filter_by_tmp
                     ON
                        (filter_by_tmp.productid = classes.productid)
                     GROUP BY
                        value
                     ORDER BY
                        value";
        $result =   func_query($sql);

        if(empty($result)) {

            return false;
        }

        return $data[$field] = $result;
    }

    private function func_filter_by_textbox_value($field, $fieldid)
    {
        $sql    =   "SELECT
                        TRIM(extra_field_values.value) AS value,
                        COUNT(extra_field_values.value) AS total
                     FROM
                        ".$this->sql_tbl['extra_field_values']." AS extra_field_values
                     INNER JOIN
                        ".$this->sql_tbl['filter_by_tmp']." AS filter_by_tmp
                     ON
                        (filter_by_tmp.productid = extra_field_values.productid)
                     WHERE
                        extra_field_values.fieldid = ".$fieldid."
                     GROUP BY
                        value
                     ORDER BY
                        value";
        $result =   func_query($sql);

        if(empty($result)) {

            return false;
        }

        return $data[$field] = $result;
    }

    public function __destruct()
    {
        db_query("DROP TABLE IF EXISTS bvira_filter_by_tmp");

        unset($this->cat);
        unset($this->from_price);
        unset($this->to_price);
        unset($this->brand);
        unset($this->provider);
        unset($this->gender);
        unset($this->type);
        unset($this->color);
        unset($this->size);
        unset($this->occasion);
        unset($this->material);
        unset($this->made_in);
    }

}

new Filter();
<?php

/**
 * Filter by products interface
 *
 * @category   X-Cart
 * @subpackage Customer interface
 * @author     Manoj <manoj@elitehour.com>
 */

ini_set("display_errors", 1);

if(empty($XCART_SESSION_VARS['filter_by_cat']) === false && $XCART_SESSION_VARS['filter_by_cat'] != $cat) {

    x_session_unregister('filter_by_cat');
    x_session_unregister('filter_by_from_price');
    x_session_unregister('filter_by_to_price');
    x_session_unregister('filter_by_brand');
    x_session_unregister('filter_by_provider');
    x_session_unregister('filter_by_gender');
    x_session_unregister('filter_by_type');
    x_session_unregister('filter_by_color');
    x_session_unregister('filter_by_size');
    x_session_unregister('filter_by_occasion');
    x_session_unregister('filter_by_material');
}

x_session_register('filter_by_cat', $cat);

final class Filter {

    public function __construct()
    {
        global $sql_tbl, $smarty, $products, $cat, $XCART_SESSION_VARS;
        
        if(empty($cat)) {

            return false;
        }

        $this->sql_tbl      =   $sql_tbl;
        $this->smarty       =   $smarty;
        $this->products     =   (array)  $products;
        $this->cat          =   (int)    $cat;
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

        self::func_filter_by_options();
    }

    private function func_filter_by_options()
    {
        $this->smarty->assign('task', 'filter_by');

        self::func_filter_by_price();

        self::func_filter_by_brand();

        self::func_filter_by_provider();

        self::func_filter_by_extra_fields();
    }

    private function func_filter_by_price()
    {
        $this->smarty->assign('filter_by_price', 1);
        $this->smarty->assign('from_price', $this->from_price);
        $this->smarty->assign('to_price', $this->to_price);
    }

    private function func_filter_by_brand()
    {
        $sql    =   "SELECT
                        DISTINCT(manufacturers.manufacturerid),
                        manufacturers.manufacturer
                     FROM
                        ".$this->sql_tbl['manufacturers']." AS manufacturers
                     INNER JOIN
                        ".$this->sql_tbl['products']." AS products
                     ON
                        (manufacturers.manufacturerid = products.manufacturerid)
                     INNER JOIN
                        ".$this->sql_tbl['products_categories']." AS products_categories
                     ON
                        (products_categories.productid = products.productid)
                     WHERE
                        products_categories.categoryid = ".$this->cat."
                     AND
                        manufacturers.avail = 'Y'
                     ORDER BY
                        manufacturers.manufacturer";
        $result =   func_query($sql, USE_SQL_DATA_CACHE);

        if(empty($result)) {

            return false;
        }

        if(empty($result) === false && isset($this->brand) && empty($this->brand) === false) {

            $brand_array = explode(',', $this->brand);

            foreach($result as $key => $value) {

                if(in_array($value['manufacturerid'], $brand_array)) {

                    $result[$key]['checkbox'] = 'checked';
                }
            }
        }

        $this->smarty->assign('filter_by_brand', $result);
    }

    private function func_filter_by_provider()
    {
        $sql    =   "SELECT
                        DISTINCT(customers.id),
                        customers.firstname,
                        customers.lastname,
                        register_field_values.value as business_name 
                     FROM
                        ".$this->sql_tbl['customers']." AS customers
                     LEFT JOIN
                        ".$this->sql_tbl['register_field_values']." AS register_field_values
                     ON 
                        (customers.id = register_field_values.userid
                     AND
                        register_field_values.fieldid = 15)
                     INNER JOIN
                        ".$this->sql_tbl['products']." AS products
                     ON
                        (customers.id = products.provider)
                     INNER JOIN
                        ".$this->sql_tbl['products_categories']." AS products_categories
                     ON
                        (products_categories.productid = products.productid)
                     WHERE
                        products_categories.categoryid = ".$this->cat."
                     AND
                        customers.usertype = 'P'
                     ORDER BY
                        business_name, customers.firstname, customers.lastname";
        $result =   func_query($sql, USE_SQL_DATA_CACHE);

        if(empty($result)) {

            return false;
        }

        if(empty($result) === false && isset($this->provider) && empty($this->provider) === false) {

            $provider_array = explode(',', $this->provider);

            foreach($result as $key => $value) {

                if(in_array($value['id'], $provider_array)) {

                    $result[$key]['checkbox'] = 'checked';
                }
            }
        }

        $this->smarty->assign('filter_by_provider', $result);
    }

    private function func_filter_by_variant($field)
    {
        $sql    =   "SELECT
                        DISTINCT(TRIM(class_options.option_name)) AS option_name
                     FROM
                        ".$this->sql_tbl['classes']." AS classes
                     INNER JOIN
                        ".$this->sql_tbl['class_options']." AS class_options
                     ON
                        (classes.classid = class_options.classid)
                     WHERE
                        classes.class LIKE '".addslashes($field)."'
                     ORDER BY
                        class_options.option_name";
        $result =   func_query($sql, USE_SQL_DATA_CACHE);

        if(empty($result)) {

            return false;
        }

        foreach($result as $value) {

            if(empty($value['option_name']) === false) {

                $option[] = trim($value['option_name']);
            }
        }

        return $data[$field] = $option;
    }

    private function func_filter_by_extra_field_values($field, $fieldid)
    {
        $sql    =   "SELECT
                        DISTINCT(TRIM(extra_field_values.value)) AS value
                     FROM
                        ".$this->sql_tbl['extra_field_values']." AS extra_field_values
                     WHERE
                        extra_field_values.fieldid = ".$fieldid."
                     ORDER BY
                        extra_field_values.value";
        $result =   func_query($sql, USE_SQL_DATA_CACHE);

        if(empty($result)) {

            return false;
        }

        foreach($result as $value) {

            if(empty($value['value']) === false) {

                $option[] = trim($value['value']);
            }
        }

        return $data[$field] = $option;
    }

    private function func_filter_by_extra_fields()
    {
        $sql    =   "SELECT
                        extra_fields.fieldid,
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
        $result =   func_query($sql, USE_SQL_DATA_CACHE);

        if(empty($result)) {

            return false;
        }

        $data   =   array();

        foreach($result as $value) {

            if($value['type'] == 'dropdown' && empty($value['value']) === false) {

                $data[$value['field']] = explode(';', $value['value']);
            }

            if($value['type'] == 'variant') {

                $data[$value['field']] = self::func_filter_by_variant($value['field']);
            }

            if($value['type'] == 'textbox') {

                $data[$value['field']] = self::func_filter_by_extra_field_values($value['field'], $value['fieldid']);
            }
        }

        if(empty($data)) {

            return false;
        }

        foreach($data as $key => $value) {

            $key = strtolower($key);

            if(empty($key) === false && isset($this->$key) && empty($this->$key) === false) {

                $this->smarty->assign('selected_'.$key, $this->$key);
            }
        }

        $this->smarty->assign('filter_by_extra_field', $data);
    }

}

new Filter();
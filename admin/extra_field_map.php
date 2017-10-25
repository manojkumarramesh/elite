<?php

/**
 * Mapping Extra fields to Category interface
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Provder interface
 * @author     Manoj <manoj@elitehour.com>
 */

require_once './auth.php';
require_once $xcart_dir.'/include/security.php';

ini_set("display_errors", 1);

if(file_exists($xcart_dir.'/modules/gold_display.php') && is_readable($xcart_dir.'/modules/gold_display.php')) {

    include $xcart_dir.'/modules/gold_display.php';
}

final class Mapping {
    
    public function __construct()
    {
        global $sql_tbl, $smarty, $mode, $categoryid, $fields;
        
        $this->sql_tbl  =   $sql_tbl;
        $this->smarty   =   $smarty;
        $this->mode     =   (string) $mode;
        $this->cat      =   (int)    $categoryid;
        $this->fields   =   (string) $fields;
        
        self::func_index();

        $function_call  =   array(
                                "search" => "func_search",
                                "update" => "func_update"
                            );

        if(array_key_exists($this->mode, $function_call)) {

            echo self::$function_call[$this->mode]();
            return false;
        }
        
        $this->smarty->assign('main', 'extra_field_map');
        func_display('admin/home.tpl', $this->smarty);
    }

    protected function func_index()
    {
        $sql    =   "SELECT  
                        categories.categoryid,
                        categories.parentid,
                        categories.category
                     FROM
                        ".$this->sql_tbl['categories']." AS categories
                     WHERE
                        categories.parentid = '0'
                     AND
                        categories.avail = 'Y'
                     ORDER BY 
                        categories.categoryid";
        $result =   func_query($sql, USE_SQL_DATA_CACHE);
        $this->smarty->assign('categories_array', $result);
    }

    protected function func_search()
    {
        $left   =   "";  
        $right  =   "";
        $where  =   "";
        $ids    =   array();
        $result =   array();

        $sql    =   "SELECT
                        extra_fields.fieldid,
                        extra_fields.field
                     FROM
                        ".$this->sql_tbl['extra_field_mapping']." AS extra_field_mapping
                     INNER JOIN
                        ".$this->sql_tbl['extra_fields']." AS extra_fields
                     ON
                        extra_field_mapping.fieldid = extra_fields.fieldid
                     WHERE
                        extra_field_mapping.categoryid = ".$this->cat."
                     ORDER BY
                        extra_fields.fieldid";
        $data   =   func_query($sql, USE_SQL_DATA_CACHE);

        if(empty($data) === false) {

            foreach($data as $value) {

                $right  .= "<option value='".$value['fieldid']."'>".$value['field']."</option>";
                $ids[]   = $value['fieldid'];
            }
        }

        if(empty($ids) === false) {

            $where  =   " WHERE extra_fields.fieldid NOT IN (".implode(',', $ids).") ";
        }

        $sql    =   "SELECT
                        extra_fields.fieldid,
                        extra_fields.field
                     FROM
                        ".$this->sql_tbl['extra_fields']." AS extra_fields
                        ".$where."
                     ORDER BY
                        extra_fields.fieldid";
        $data   =   func_query($sql, USE_SQL_DATA_CACHE);

        foreach($data as $value) {

            $left .= "<option value='".$value['fieldid']."'>".$value['field']."</option>";
        }

        $result['success']  =   1;
        $result['left']     =   $left;
        $result['right']    =   $right;

        return json_encode($result);
    }

    protected function func_update()
    {
        $result =   array();

        db_query("DELETE FROM ".$this->sql_tbl['extra_field_mapping']." WHERE categoryid = ".$this->cat."");

        if(empty($this->fields) === false) {

            $posted_data    =   explode(',', $this->fields);

            foreach($posted_data as $value)
            {
                $query_data =   array(
                                    'categoryid' => $this->cat,
                                    'fieldid'    => trim($value)
                                );
                func_array2insert('extra_field_mapping', $query_data, true);
            }
        }

        $result['success'] = 1;
        $result['message'] = 'Updated Successfully';

        return json_encode($result);
    }

}

new Mapping();

//echo "<pre>"; var_dump($mapping); die;
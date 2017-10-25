<?php

require_once dirname(__FILE__) . '/../admin/auth.php';

ini_set("display_errors", 1);

ini_set("max_execution_time", 0);

set_time_limit(0);

final class Variants_Generator {

    public function __construct()
    {
        global $sql_tbl;

        $this->sql_tbl  =   $sql_tbl;

        self::func_index();
    }

    protected function func_index()
    {
        $sql        =   "SELECT
                            products.productid,
                            products.productcode
                         FROM
                            ".$this->sql_tbl['products']." AS products
                         INNER JOIN
                            ".$this->sql_tbl['classes']." AS classes
                         ON
                            (products.productid = classes.productid)
                         GROUP BY
                            products.productid
                         ORDER BY
                            products.productid";
        $products   =   func_query($sql);

        if(empty($products))
            return false;

        $count      =   1;

        foreach($products as $key => $value) {

            $class_options  = self::func_get_class_options($value['productid']);

            if(!empty($class_options))
                $products[$key]['classes'] = $class_options;
            else
                unset($products[$key]);

            $count++;

            if($count == 50) {

                sleep(2);
                $count = 1;
            }
        }

        debug_array($products);
    }

    protected function func_get_class_options($productid)
    {
        $sql        =   "SELECT
                            classes.classid
                         FROM
                            ".$this->sql_tbl['classes']." AS classes
                         WHERE
                            classes.productid = ".$productid."
                         ORDER BY
                            classes.classid";
        $classes    =   func_query($sql);

        if(!empty($classes)) {

            foreach($classes as $value) {

                $sql1       =   "SELECT
                                    class_options.optionid
                                 FROM
                                    ".$this->sql_tbl['classes']." AS classes
                                 INNER JOIN
                                    ".$this->sql_tbl['class_options']." AS class_options
                                 ON
                                    (classes.classid = class_options.classid)
                                 WHERE
                                    classes.productid = ".$productid."
                                 AND
                                    classes.classid = ".$value['classid']."
                                 ORDER BY
                                    class_options.optionid";
                $options    =   func_query($sql1);

                if(!empty($options)) {

                    $option =   array();

                    foreach($options as $value1) {

                        $option[] = $value1['optionid'];
                    }

                    $class[$value['classid']]['class_options'] = $option;
                }
            }
        }

        if(empty($class))
            return false;
        else
            return $class;
    }

    public function __destruct()
    {
        unset($this->sql_tbl);
    }

}

new Variants_Generator();
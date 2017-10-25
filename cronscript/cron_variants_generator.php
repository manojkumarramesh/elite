<?php

require_once dirname(__FILE__) . '/../admin/auth.php';

ini_set("display_errors", 1);

ini_set("max_execution_time", 0);

set_time_limit(0);

final class Generator {

    public function func_index()
    {
        $sql    =   "SELECT
                        productid,
                        productcode,
                        group_concat(options SEPARATOR '#')
                     FROM
                        (
                            SELECT
                                a.productid,
                                a.productcode,
                                b.classid,
                                b.class,
                                group_concat(c.optionid) AS options,
                                group_concat(c.option_name)
                            FROM
                                xcart_products a
                            INNER JOIN
                                xcart_classes b
                            ON
                                a.productid = b.productid
                            INNER JOIN
                                xcart_class_options c
                            ON
                                b.classid = c.classid
                            GROUP BY
                                b.classid
                        ) as temp
                    GROUP BY
                        productid";
        $result =   func_query($sql);

        if(empty($result))
            return false;

        $count  =   1;

        foreach($result as $value) {

            $options    =   explode('#', $value["group_concat(options SEPARATOR '#')"]);

            if(!empty($options)) {

                for($i=0;$i<count($options);$i++) {

                    $variants[] = explode(',', $options[$i]);
                }

                $combos     =   self::combinations($variants, 0);

                unset($variants);

                 if(!empty($combos)) {

                    $k  =   11;

                    for($j=0;$j<count($combos);$j++) {

                        $data       =   array(
                                            'productid'   => $value['productid'],
                                            'avail'       => '1000',
                                            'weight'      => '0.00',
                                            'productcode' => $value['productcode'].$k
                                        );
                        $variantid  =   func_array2insert('variants', $data, true);

                        $k++;

                        if(!empty($variantid)) {

                            $items  =   explode(',', $combos[$j]);

                            if(!empty($items)) {

                                foreach($items as $value1) {

                                    $data1  =   array(
                                                    'optionid'  => $value1,
                                                    'variantid' => $variantid
                                                );
                                    func_array2insert('variant_items', $data1, true);

                                    unset($data1);
                                }
                            }
                            unset($items);
                        }
                        unset($data);
                        unset($variantid);
                    }
                }
                unset($combos);
            }
            unset($options);

            $count++;

            if($count == 10) {

                sleep(2);
                $count = 1;
            }
        }
    }

    public function combinations($arr, $n)
    {
        $res = array();

        foreach($arr[$n] as $item) {

            if($n == count($arr) - 1) {

                $res[]  =   $item;
            }
            else {

                $combs  =   self::combinations($arr, $n + 1);

                foreach($combs as $comb) {

                    $res[]  =   $item.','.$comb;
                }
            }
        }

        return $res;
    }

}

Generator::func_index();
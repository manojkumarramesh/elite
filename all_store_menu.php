<?php

require_once 'auth.php';

//ini_set("display_errors", 1);

function func_check_child_exists($categoryid)
{
    $sql    =   "SELECT
                    categoryid,
                    category
                 FROM
                    xcart_categories
                 WHERE
                    parentid = ".$categoryid."
                 AND
                    avail = 'Y'";
    $result =   func_query($sql, USE_SQL_DATA_CACHE);

    if(empty($result)) {

        return 0;
    }
    else {

        return 1;
    }
}

function func_fetch_childs($categoryid)
{
    $sql    =   "SELECT
                    categoryid,
                    category
                 FROM
                    xcart_categories
                 WHERE
                    parentid = ".$categoryid."
                 AND
                    avail = 'Y'";
    $result =   func_query($sql, USE_SQL_DATA_CACHE);

    if(empty($result)) {

        return false;
    }

    foreach($result as $key => $value)
    {
        $array[$key]['categoryid'] = $value['categoryid'];
        $array[$key]['category']   = $value['category'];
        $array[$key]['total']      = 0;

        $is_exists_child           = func_check_child_exists($value['categoryid']);

        if(empty($is_exists_child) === false) {

            $array[$key]['childs'] = func_fetch_childs($value['categoryid']);
        }
    }

    return $array;
}

$sql    =   "SELECT
                categoryid,
                category
             FROM
                xcart_categories
             WHERE
                parentid = 0
             AND
                avail = 'Y'";
$result =   func_query($sql, USE_SQL_DATA_CACHE);

foreach($result as $key => $value)
{
    $array[$key]['categoryid'] = $value['categoryid'];
    $array[$key]['category']   = $value['category'];
    $array[$key]['total']      = 0;

    $is_exists_child           = func_check_child_exists($value['categoryid']);

    if(empty($is_exists_child) === false) {

        $array[$key]['childs'] = func_fetch_childs($value['categoryid']);
    }
}

echo $data = json_encode($array);

$fh = fopen($xcart_dir.'/data.txt', 'w');
fwrite($fh, $data);
fclose($fh);
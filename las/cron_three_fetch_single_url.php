<?php

//ini_set('display_errors', 1);
ini_set('memory_limit', '512M');
ini_set('max_execution_time', 0);

include('core.class.php');
require dirname(__FILE__) . '/../admin/auth.php';

$finder = new BviraFinder();
$finder->init();

$result = func_query("SELECT * FROM las_vendor_category_url");

if(empty($result))
{
    echo "Empty : las_vendor_category_url";
    die;
}

foreach($result as $value)
{
    $pages        = $finder->get_single_url_page($value['url']);

    $products_url = array();

    for($i=0;$i<count($pages);$i++)
    {
        $products_url = array_merge($products_url, $finder->get_single_url($pages[$i]));
    }

    if(!empty($products_url))
    {
        foreach($products_url as $value1)
        {
            func_array2insert('las_products_url', array('vendor_id' => $value['vendor_id'], 'vendor_cat_id' => $value['vendor_category_id'], 'url' => $value1['url']));
        }
    }
}
<?php

//ini_set('display_errors', 1);
ini_set('max_execution_time', 0);

include('core.class.php');
require dirname(__FILE__) . '/../admin/auth.php';

$finder = new BviraFinder();

$finder->init();

$url    = "https://www.lashowroom.com";

$stores = $finder->get_store_url($url);

if(empty($stores))
{
    echo "Empty : stores data";
    die;
}

foreach($stores as $vendor_details => $value)
{
    $data   =   array(
                    'vendor_name' => addslashes($vendor_details),
                    'url'         => $value['url']
                );
    func_array2insert('las_vendor_details', $data, true);
}
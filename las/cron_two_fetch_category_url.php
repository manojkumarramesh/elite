<?php

//ini_set('display_errors', 1);
ini_set('max_execution_time', 0);

include('core.class.php');
require dirname(__FILE__) . '/../admin/auth.php';

$finder = new BviraFinder();
$finder->init();

function func_check_exist_type_category($type, $category)
{
    $sql    =   "SELECT id
                 FROM las_vendor_categories
                 WHERE type = '".addslashes($type)."'
                 AND category = '".addslashes($category)."'";
    $result =   func_query_first_cell($sql);

    return $result;
}

$vendors = func_query("SELECT * FROM las_vendor_details WHERE status = 1");

if(empty($vendors))
{
    echo "Empty : Vendors";
    die;
}

$count = 0;

foreach($vendors as $value)
{
    $categories = $finder->get_categories_url($value['url']);

    if($categories['no-parent-category'])
    {
        foreach($categories['no-parent-category'] as $category => $url)
        {
            $if_exists = func_check_exist_type_category('no-parent-category', $category);

            if(empty($if_exists))
            {
                $vendor_category_id = func_array2insert('las_vendor_categories', array('type' => 'no-parent-category', 'category' => addslashes($category)));

                if(!empty($vendor_category_id))
                {
                    func_array2insert('las_vendor_category_url', array('vendor_id' => $value['id'], 'vendor_category_id' => $vendor_category_id, 'url' => $url));
                }
            }
        }
    }
    else
    {
        foreach($categories as $type => $value1)
        {
            foreach($value1 as $category => $url)
            {
                $if_exists = func_check_exist_type_category($type, $category);

                if(empty($if_exists))
                {
                    $vendor_category_id = func_array2insert('las_vendor_categories', array('type' => addslashes($type), 'category' => addslashes($category)));

                    if(!empty($vendor_category_id))
                    {
                        func_array2insert('las_vendor_category_url', array('vendor_id' => $value['id'], 'vendor_category_id' => $vendor_category_id, 'url' => $url));
                    }
                }
            }
        }
    }

    $count++;

    if($count == 10)
    {
        $count = 0;
        sleep(5);
    }
}
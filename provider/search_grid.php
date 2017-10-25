<?php

/**
 * Products search grid interface
 *
 * @category   X-Cart
 * @subpackage Provider interface
 * @author     Manoj <manoj@elitehour.com>
 * @modified   December-26-2012
 */

require_once './auth.php';
require_once $xcart_dir.'/include/security.php';
require_once $xcart_dir.'/include/search_grid.php';

//ini_set("display_errors", 1);

$provider_id = $GLOBALS['logged_userid'];

final class Search_Grid extends Search {

    public function __construct()
    {
        global $sql_tbl, $smarty, $mode, $wholesaler_id, $provider_id, $posted_data, $page, $setlive;

        $this->sql_tbl          =   $sql_tbl;
        $this->smarty           =   $smarty;
        $this->posted_data      =   (array) $posted_data;
        $this->mode             =   (string) $mode;
        $this->setlive          =   (string) $setlive;
        $this->wholesaler_id    =   (int) $wholesaler_id;
        $this->provider_id      =   (int) $provider_id;
        $this->page             =   (int) $page;

        parent::func_index();

        $function_call          =   array(
                                        "search"   => "func_search",
                                        "update"   => "func_update",
                                        "generate" => "func_generate_retail_packs"
                                    );

        if(array_key_exists($this->mode, $function_call)) {

            parent::$function_call[$this->mode]();
        }

        $location[] = array('Products - Grid View', 'search_grid.php');
        $this->smarty->assign('location', $location);
        $this->smarty->assign('main', 'search_grid');

        func_display('provider/home.tpl', $this->smarty);
    }

    public function __destruct()
    {
        unset($this->sql_tbl);
        unset($this->smarty);
        unset($this->posted_data);
        unset($this->mode);
        unset($this->setlive);
        unset($this->wholesaler_id);
        unset($this->provider_id);
        unset($this->page);
    }

}

new Search_Grid();
<?php
namespace App\Base;

class BaseController
{
    public $plugin_path;
    public $plugin_url;
    public $managers = array();

    public function __construct()
    {
        $this->plugin_path = plugin_dir_path( dirname( __FILE__, 2 ) );
		$this->plugin_url = plugin_dir_url( dirname( __FILE__, 2 ) );

        $this->managers = array (
            "total",
            "exclude",
            "include_rubrics",
            "cookie_live",
            "path_to_tpl",
            "thumbnails",
        );
    }
}

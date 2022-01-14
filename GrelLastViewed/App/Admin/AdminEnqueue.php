<?php
namespace App\Admin;

use App\Base\BaseController;

Class AdminEnqueue extends BaseController
{
    public function register()
    {
    add_action('admin_enqueue_scripts', 
        array(
            $this,
            'getAdminStyles'
        )
    );
    add_action('admin_footer', 
        array(
            $this,
            'getAdminScripts'
        )
    );
  
    
   
    }
    function getAdminScripts()
 {
     $scripturl = $this->plugin_path . '/assets/js/admin-main.js';
     echo '"<script type="text/javascript" src="'. $scripturl . '"></script>"';
 }
 function getAdminStyles()
 {
     $stylesheeturl =  $this->plugin_path . '/assets/css/style.css';
     wp_enqueue_style('admin-styles', $stylesheeturl);
 }
}
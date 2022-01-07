<?php

/**
 * Plugin Name: MyLastViewed
 * Description: sdjksdjsk
 * Version: 1.0
 * Author: WPShout
 * Author URI: ''''
*/

require_once dirname(__FILE__) . '/LastViewedByGrel.php';
require_once dirname( __FILE__ ) . '/class-config.php';



register_activation_hook($file, 'grel_viewed_install');
register_deactivation_hook($file, 'grel_viewed_uninstall');


function grel_viewed_install()
{
    
}
function grel_viewed_uninstall()
{

}



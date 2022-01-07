<?php

/**
 * Plugin Name: Dog-tooth: Last viewed
 * Description: Плагин вывода недавно просмотренных записей и страниц
 * Version: 1.0
 * Author: WPShout
 * Author URI: ''''
*/

$classes = array("config", "lastviewed", "admin", "template");

foreach ($classes as $class)
{
    $file = '/class-'.$class.'.php';
   
    require_once dirname( __FILE__ ) . $file;
}


register_activation_hook($file, 'grel_viewed_install');
register_deactivation_hook($file, 'grel_viewed_uninstall');


function grel_viewed_install()
{
    
}
function grel_viewed_uninstall()
{

}



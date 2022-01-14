<?php

/**
 * Plugin Name: Dog-tooth: Last viewed
 * Description: Плагин вывода недавно просмотренных записей и страниц
 * Version: 1.0
 * Author: Dog-tooth
 * Author URI: ''''
*/

defined( 'ABSPATH' ) or die();
require_once ABSPATH.'wp-includes/class-wp-widget.php';
if ( file_exists( dirname( __FILE__ ) . '/vendor/autoload.php' ) ) {
	require_once dirname( __FILE__ ) . '/vendor/autoload.php';
}
function activateLastViewed() {
	App\Base\Activation::activate();
}
//register_activation_hook( );
function deactivateLastViewed() {
	App\Base\Deactivation::deactivate();
}

// if (class_exists('App\\Init'))
// {
    App\Init::registerservices();
//}
//register_deactivation_hook( );

/**
 * Initialize all the core classes of the plugin
 */
// if ( class_exists( 'Inc\\Init' ) ) {
// 	//Inc\Init::register_services();
// }
// if (file_exists( dirname( __FILE__ )))

// $classes = array(
//     "config", 
//     "lastviewed", 
//     "admin", 
//     "template"
// );
// foreach ($classes as $class)
// {
//     $file = '/class-'.$class.'.php';
   
//     require_once dirname( __FILE__ ) . $file;
// }


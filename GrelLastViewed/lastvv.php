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
//register_deactivation_hook( );

 if (class_exists('App\\Init'))
 {
    App\Init::registerservices();
 }


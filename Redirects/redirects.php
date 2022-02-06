<?php

/**
 * Plugin Name: Dog-tooth: Redirects
 * Description: Плагин редиректов
 * Version: 1.0
 * Author: Dog-tooth
 * Author URI: ''''
*/

defined( 'ABSPATH' ) or die();
if ( file_exists( dirname( __FILE__ ) . '/vendor/autoload.php' ) ) {
	require_once dirname( __FILE__ ) . '/vendor/autoload.php';
}
function activateDTRedirects() {
	App\Base\Activation::activate();
}
//register_activation_hook( );
function deactivateDTRedirects() {
	App\Base\Deactivation::deactivate();
}

if (class_exists('App\\Init'))
{
    App\Init::registerservices();
}
//register_deactivation_hook( );

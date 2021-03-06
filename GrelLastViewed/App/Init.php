<?php
namespace App;
final class Init 
{
    
    public static function getservices()
    {
        return [
            Admin\Admin::class,
            Admin\AdminEnqueue::class,
            Base\LastViewed::class
        ];
    }
    public static function registerservices()
    {
        foreach ( self::getservices() as $class ) {
			$service = self::makeinstance( $class );
			if ( method_exists( $service, 'register' ) ) {
				$service->register();
			}
		}
    }
    private static function makeinstance($current)
    {
        $service = new $current;
        return $service;
    }
}

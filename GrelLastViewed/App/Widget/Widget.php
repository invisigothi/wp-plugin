<?
namespace App\Widget;

use App\Base\LastViewed;
use App\Config\Config;
class Widget extends \WP_Widget
{
    public function __construct()
    {
        $widget_options = array(
            'classname' => LastViewed::class,
            'description' => Config::GREL_WIDGET_DESCRIPTION,
        );
        parent::__construct('LastViewed', 'Last Viewed By Grel', $widget_options);
    }
}

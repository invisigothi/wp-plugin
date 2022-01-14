<?
namespace App\Template;

class Template 
{
    public $templatedata = array();
    
    public function __construct(array $params)
    {
        if (isset($params))
        {
            $this->templatedata = $params['data']; 
            $this->initTemplates($params['template']);

        }
    }
     function initTemplates($template)
    {

            // if ( $overridden_template = locate_template($template) ) {
            //     load_template( $overridden_template );
            //     return true;
            // } else {
               // include dirname( __FILE__ ) . '/templates/' . $template;
          //      include dirname( __FILE__ ). '/' .$template;
               // return true;
            //}
    }

    function getTemplate()
    {
     //   return $this->templatedata;
    //    return self::$templatedata;
    }


    public function CheckTemplates()
    {
        $settings = get_option('grel_settings');
        if ($settings['path_to_tpl'] !== '')
        {
            return true;
        }
        return false;
    }
}

<?
namespace App\Admin;

use App\Base\BaseController;
use App\Config\Config;
class Admin extends BaseController
{
public function register()
 {
    // add_action('admin_menu', array(
    //     $this,
    //     'Redirect_plugin'
    //     )
    // );
    // add_action('admin_init', array($this, 'plugin_settings'));
 }
 function Redirect_plugin() {
    // add_options_page('Dog-tooth: redirects', 
    // 'Dog-tooth:Last viewed', 8, __FILE__, 
    // array(
    //     $this,
    //     'DT_redirect_options'
    // ));
  }

function DT_redirect_options() {
//     if (!current_user_can('manage_options'))  {
//     wp_die( __('У вас нет прав доступа на эту страницу.') );
//     }
//      include $this->plugin_path . '/form/admin-form.php';
  }


}
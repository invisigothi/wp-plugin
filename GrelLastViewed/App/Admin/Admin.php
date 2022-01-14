<?
namespace App\Admin;

use App\Base\BaseController;
use App\Config\Config;
Class Admin extends BaseController
{
public function register()
 {
     
    add_action('admin_menu', array(
        $this,
        'grel_viewed_menu'
        )
    );
    add_action('admin_init', array($this, 'plugin_settings'));
 }
 
 function grel_viewed_menu() {
    add_options_page('Dog-tooth:Last viewed Options', 
    'Dog-tooth:Last viewed', 8, __FILE__, 
    array(
        $this,
        'grel_viewed_options'
    ));
  }

function grel_viewed_options() {
    if (!current_user_can('manage_options'))  {
    wp_die( __('У вас нет прав доступа на эту страницу.') );
    }
   include $this->plugin_path . 'lang/languages.php';
     include $this->plugin_path . '/form/admin-form.php';
  }

  function getAllPages()
  {
    global $wpdb;
    $allSitePages = get_pages();
    $pagesInfo = array();
      foreach ($allSitePages as $page)
      {
          $pagesInfo[] = array(
              "id" => $page->ID,
              "title" => $page->post_title,
          );
      }
    return $pagesInfo;
  }
 
  function plugin_settings(){
    include  $this->plugin_path . 'lang/languages.php';
    register_setting( 'option_group', 'grel_settings', 
        array(
            $this,
            'sanitize_callback'
        )
    );

    $lang = Config::CheckLang();

    add_settings_section( 'section_id', $mess[$lang]['settings'] , '', 'main_settings_page' );
    foreach ($this->managers as $manager)
    {
         add_settings_field($manager, $mess[$lang][$manager], array($this,'fill_'.$manager),'main_settings_page','section_id' );
    }
}
function fill_path_to_tpl()
{
    $val = get_option('grel_settings');
    $val = $val ? $val['path_to_tpl'] : null;
    ?>
    <input type="text" name="grel_settings[path_to_tpl]">
    <?
}

function fill_thumbnails()
{
    $val = get_option('grel_settings');
    $val = $val ? $val['thumbnails'] : null;
    ?>
    <div class="toggleWrapper">
        <input type="checkbox" id="toggle1" class="mobileToggle" name="grel_settings[thumbnails]" value="1" <?php checked( 1, $val ) ?> />
        <label for="toggle1"></label>
    </div>
    <?php 
}
function fill_cookie_live()
{
    $val = get_option('grel_settings');
    $val = $val ? $val['cookie_live'] : null;
    ?>
    <input type="text" name="grel_settings[cookie_live]" value="<?echo esc_attr($val)?>">
    <?
}
function fill_exclude()
{
    $val = get_option('grel_settings');
    $val = $val ? $val['exclude_ids'] : null;
    $allPages = $this->getAllPages();
?>
<select class="admin__excluded" multiple="multiple" name="grel_settings[exclude_ids][]">
<?php foreach ($allPages as $page) : ?>
                    <option 
                    <? 
                    if (is_array($val)){
                    if(array_search($page['id'], $val) >=0 && array_search($page['id'], $val) !== false )
                    {
                        echo 'selected';
                    }
                }?>
                     value="<?=$page["id"]; ?>" ><?=$page["title"] ?>
                    </option>
                <?php endforeach; ?>
               
</select>
    <? 
}
function fill_total(){
    $val = get_option('grel_settings');
    $val = $val ? $val['total'] : null;
    ?>
    <input type="text" name="grel_settings[total]" value="<?php echo esc_attr( $val ) ?>" />
    <?php
}
function fill_include_rubrics(){
    $val = get_option('grel_settings');
    $val = $val ? $val['include_rubrics'] : null;
    ?>
        <input type="checkbox" name="grel_settings[include_rubrics]" value="1" <?php checked( 1, $val ) ?> />
    <?php
}
## Очистка данных
function sanitize_callback( $options ){
    foreach( $options as $name => &$val ){
        if( $name == 'total' )
            $val = strip_tags( $val );
        if( $name == 'include_rubrics' )
            $val = intval( $val );
        if ($name == 'exclude_ids')
          // $val =  $val['exclude_ids'];
        if( $name == 'cookie_live' )
            $val = strip_tags($val);
        if ($name == 'thumbnails')
            $val = intval($val);
        if ($name == 'path_to_tpl')
            $val = strip_tags('path_to_tpl');
    }
    return $options;
}
}
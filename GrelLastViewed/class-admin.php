<?
Class Admin
{
public function register()
 {
    add_action('admin_enqueue_scripts', 
        array(
            $this,
            'getAdminStyles'
        )
    );
    add_action('admin_footer', 
        array(
            $this,
            'getAdminScripts'
        )
    );
    add_action('admin_menu', array(
        $this,
        'grel_viewed_menu'
        )
    );
    add_action('admin_init', array($this, 'plugin_settings'));
 }
 function getAdminScripts()
 {
     $scripturl = plugins_url('/assets/js/admin-main.js', __FILE__);
     echo '"<script type="text/javascript" src="'. $scripturl . '"></script>"';
 }
 function getAdminStyles()
 {
     $stylesheeturl =  plugins_url('/assets/css/style.css', __FILE__);
     wp_enqueue_style('admin-styles', $stylesheeturl);
 }
 function grel_viewed_menu() {
    add_options_page('Grel Viewed Options', 
    'Grel Viewed', 8, __FILE__, 
    array(
        $this,
        'grel_viewed_options'
    ));
  }

function grel_viewed_options() {
    if (!current_user_can('manage_options'))  {
    wp_die( __('У вас нет прав доступа на эту страницу.') );
    }
    include 'lang/languages.php';
    include 'form/admin-form.php';
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
    include 'lang/languages.php';
    register_setting( 'option_group', 'grel_settings', 
        array(
            $this,
            'sanitize_callback'
        )
    );
    $lang = Config::CheckLang();
    add_settings_section( 'section_id', $mess[$lang]['settings'] , '', 'main_settings_page' );
    add_settings_field('total', $mess[$lang]['total'], 
        array(
            $this,
            'fill_total'
    ), 
    'main_settings_page',
    'section_id' );

    add_settings_field('exclude',  $mess[$lang]['exclude'], 
        array(
            $this,
            'fill_exclude'
        ), 
    'main_settings_page', 
    'section_id' );
    
    add_settings_field('include_rubrics',  $mess[$lang]['include_rubrics'], 
        array(
            $this, 
            'fill_include_rubrics' 
    ), 
    'main_settings_page', 
    'section_id' );

    add_settings_field('cookie_live', $mess[$lang]['cookie_live'],
    array(
        $this,
        'fill_cookie_live'
    ),
    'main_settings_page',
    'section_id');

    add_settings_field('thumbnails', $mess[$lang]['thumbnails'],
    array(
        $this,
        'fill_thumbnails'
    ),
    'main_settings_page',
    'section_id');
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
                    <? if (array_search($page['id'], $val) >=0 && array_search($page['id'], $val) !== false )
                    {
                        echo 'selected';
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
    foreach( $options as $name => & $val ){
        if( $name == 'total' )
            $val = strip_tags( $val );
        if( $name == 'include_rubrics' )
            $val = intval( $val );
        if ($name == 'exclude_ids')
           $val =  $val['exclude_ids'];
        if( $name == 'cookie_live' )
            $val = strip_tags($val);
        if ($name == 'thumbnails')
            $val = intval($val);
    }
    return $options;
}
}
<?php
class Config
{
    const GREL_LANG = 'ru';
    const GREL_WIDGET_NAME = 'GREL LAST VIEWED';
    const GREL_WIDGET_DESCRIPTION = 'Виджет для вывода недавно просмотренных страниц и записей';
    const GREL_COOKIE_PREFIX = 'grel_';
    const DEFAULT_COOKIE_LIVE = 3600;
    const DEFAULT_MAX_PAGE = 15;
}

class LastViewedByGrel extends WP_Widget
{
    private $args;
    private $currentPostId;
    private $jsvars = array();
    private $viewedlist;

    public function __construct()
    {
        $widget_options = array(
            'classname' => 'LastViewedByGrel',
            'description' => Config::GREL_WIDGET_DESCRIPTION,
        );
        parent::__construct('LastViewedByGrel', 'Last Viewed By Grel', $widget_options);
        add_action('admin_init', array($this, 'plugin_settings'));

        //подключение скриптов
        add_action('wp', array(
            $this,
            'init'
        ));
        add_action('wp_enqueue_scripts', array(
            $this,
            'GrelViewedAssets'
        ));
        add_action('admin_enqueue_scripts', 
        array($this,'AdminStyles'));
        add_action('admin_footer', 
        array($this,'getAdminScripts')
        );
        //аякс
        if (wp_doing_ajax())
        {
            add_action('wp_ajax_set_cookie_data_ajax', array(
                $this,
                'func_set_cookie_data_ajax'
            ), 99);
            add_action('wp_ajax_nopriv_set_cookie_data_ajax', array(
                $this,
                'func_set_cookie_data_ajax'
            ), 99); 
            add_action('wp_ajax_load_widget', array(
                $this, 
                'load_widget_ajax'
            ), 99);
            add_action('wp_ajax_nopriv_load_widget', array(
                $this, 
                'load_widget_ajax'
            ), 99);
        }
        //шорткоды
        add_shortcode('grel_lastviewed', array($this, 'grelshortCode_lastViewed'));

        add_action('admin_menu', array(
            $this,
            'grel_viewed_menu'
            )
        );
     
    }

    function init()
    {
        global $post;
        if (is_singular())
        {
            $this->currentPostId = $post->ID;
        }
        $val = get_option('grel_settings');
        if (is_category() && $val['include_rubrics'] == 1)
        {
            $category = get_queried_object();
            $this->currentPostId = $category->term_id;
        }
        if (!$this->currentPostId) 
        return;
        if (isset($val['cookie_live']))
        {
            $cookielive = intval($val['cookie_live']);
        }else{
            $cookielive = Config::DEFAULT_COOKIE_LIVE;
        }
        $this->jsvars = array(
            'ajaxurl' => admin_url('/admin-ajax.php') ,
            'current_page' => $this->currentPostId,
            'cookie_prefix' => Config::GREL_COOKIE_PREFIX,
            'expire_cookie_date' => time() + $cookielive,
        );
        $cookiename = Config::GREL_COOKIE_PREFIX . 'widget';
        if (!isset($_COOKIE[$cookiename]))
        {
            $list[] = $this->currentPostId;
            $imploded = implode(',', $list);
            setcookie($cookiename, $imploded, time() + $cookielive, '/');
        }
        else
        {
            $currentCookies = $_COOKIE[$cookiename];
            $explodedCookies = explode(',', $currentCookies);
            if (!in_array($this->currentPostId, $explodedCookies))
            {
                $imploded = $this->generatenewCookie($this->currentPostId);
                setcookie($cookiename, $imploded, time() + $cookielive, '/');
            }
        }
        $postlist = $this->getViewedList($PostsFromObject = true, 'page');
        $cats = $this->getViewedList($PostsFromObject = true, 'cat');
        if (isset($cats))
        {
            $newPostlist = array_merge($postlist, $cats);
        }else{
            $newPostlist = $postlist;
        }
        $test = $this->load_widget_ajax($newPostlist); 
       
    }
    function grelshortCode_lastViewed($atts){
        $params =  shortcode_atts( 
            array(
                'widget_title' => Config::GREL_WIDGET_NAME,
            ),
            $atts
        );
        $container = '<h2>' . $params['widget_title'] . '</h2>
        <div id="grel-last-viewed-1" class="grel-last-ajax" data-id="grel-last-viewed-1"></div>';
        return $container;
    }
    function GrelViewedAssets()
    {
        $script_url = plugins_url('/js/main.js', __FILE__);
        wp_enqueue_script('main', $script_url, array(
            'jquery'
        ));
        wp_localize_script('main', 'GVData', $this->jsvars);
    }

    function getAdminScripts()
    {
        $scripturl = plugins_url('/js/admin-main.js', __FILE__);
        echo '"<script type="text/javascript" src="'. $scripturl . '"></script>"';
    }

    function AdminStyles()
    {
        $stylesheeturl =  plugins_url('/css/style.css', __FILE__);
        wp_enqueue_style('admin-styles', $stylesheeturl);
    }

    function func_set_cookie_data_ajax()
    {
        $phpcookies = $this->generatenewCookie($_POST["current_page_id"]);
        echo json_encode($phpcookies);
        
        wp_die();
    }

    function load_widget_ajax()
    {
        $PostsFromObject = true;
        $postlist = $this->getViewedList($PostsFromObject, 'page');
        $cats = $this->getViewedList($PostsFromObject, 'cat');
        if (isset($cats))
        {
            $newPostlist = array_merge($postlist, $cats);
        }else{
            $newPostlist = $postlist;
        }
        $widgetwrapper = $this->set_widget_wrap($newPostlist);
        if (strlen($widgetwrapper) > 0)
        {
            $result['status'] = 'success';
            $result['container'] = $widgetwrapper;
        }else{
            $result['status'] = 'error or empty';
            $result['container'] = '';
        }
        if (wp_doing_ajax()){
            echo json_encode($result);
            wp_die();
        }else{
            return $widgetwrapper;
        }
       
    }

    function set_widget_wrap($arr)
    {
        $container = '<div id="grel-last-viewed-1" class="grel-last-ajax" data-id="grel-last-viewed-1">';
        if (count($arr) == 0)
         {
            $container .= '</div>';
         }else{
             $settings = get_option('grel_settings');
             $i = 0;
            foreach ($arr as $key => $val)
            {
                 if (count($settings['exclude_ids']) > 0)
                 {
                     if (in_array($val['id'], $settings['exclude_ids']))
                     {
                        continue;
                     }
                 }
                if (isset($val['post_img']))
                {
                    $container .= '<img src="'.$val['post_img'].'">';
                }
                $i++;
                $container .= '<p><a href="'.$val["post_link"].'">' . $val["post_title"] . '</a></p>';
                if (isset($settings['total']) && intval($settings['total']) === $i)
                {
                    break;
                }
            }
            $container .= '</div>';
        }
     return $container;
    }

    function getCookieList($name)
    {
        $cookieVal = isset($_COOKIE[$name]) ? $_COOKIE[$name] : '';
        return explode(',', $cookieVal);
    }

    function generatenewCookie($current)
    {
        $cookiename = Config::GREL_COOKIE_PREFIX . 'widget';
        $oldCookies = $this->getCookieList($cookiename);
        if (isset($oldCookies))
        {
            $newCookies = array_diff($oldCookies, array(
                $current
            ));
        }
        else
        {
            $newCookies = array();
        }
        $newCookies[] = $current;
        $imploded = implode(",", $newCookies);
        return $imploded;
    }

    public function widget($args, $instance)
    {
        $title = apply_filters('widget_title', $instance['title']);
        echo $args['before_widget'] . $args['before_title'] . $title . $args['after_title']; ?>
        <?php echo $args['after_widget'];
    }

    public function form($instance)
    {
        //include ('Form/form.php');
        return true;
    }

    public function update($new_instance, $old_instance)
    {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        return $instance;
    }

    function getViewedList($PostsFromObject, $posttype)
    {
        $viewedList = $this->getCookieList(Config::GREL_COOKIE_PREFIX . 'widget');
        if (count($viewedList) > 0)
         { 
            $val = get_option('grel_settings');
        switch($posttype)
        {
            case 'cat':
                    $categories = get_categories(
                        [
                            'include'=>array_reverse($viewedList),
                        ]
                    );
            break;
            case 'page':
                $othersettings = array();
                $args = array(
                    'post_type'=>'page',
                    'post__in' => array_reverse($viewedList) ,
                    'post_status' => 'publish',
                );
                
                $query = new WP_Query(array_merge($args, $othersettings));
            break;
        }
    }
           
        if ($PostsFromObject && $posttype == 'page')
        {
            $resultPages = array();
            $val = get_option('grel_settings');
            foreach ($query->posts as $post)
            {
                $resultPages[] = array(
                    "id" => $post->ID,
                    "post_title" => $post->post_title,
                    "post_link" => get_permalink($post->ID),
                    "post_img" => $val['thumbnails'] == 1 ? get_the_post_thumbnail($post->ID) : '',
                );
            }
            return $resultPages;
        }
        if ($PostsFromObject && $val['include_rubrics'] == 1 && $posttype == 'cat')
        {
            $resultCats = array();
            foreach ($categories as $cat)
            {
                $resultCats[] = array(
                    "id" => $cat->term_id,
                    "post_title" => $cat->cat_name,
                    "post_link" => get_category_link($cat->term_id),
                    "post_img" =>  '',
                );
            }
            return $resultCats;
        }
        
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
        include 'Form/admin-form.php';
        
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
        $lang = $this->CheckLang();
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

  
        <!-- <textarea name="grel_settings[exclude_ids]" value="<?//echo esc_attr($val)?>"></textarea> -->
        <? 
    }
    //количество выводимых
    function fill_total(){
        $val = get_option('grel_settings');
        $val = $val ? $val['total'] : null;
        ?>
        <input type="text" name="grel_settings[total]" value="<?php echo esc_attr( $val ) ?>" />
        <?php
    }
    //включить рубрики
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
                //$val = strip_tags($val);
            if( $name == 'cookie_live' )
                $val = strip_tags($val);
            if ($name == 'thumbnails')
                $val = intval($val);
        }
    
        //die(print_r( $options )); // Array ( [input] => aaaa [checkbox] => 1 )
    
        return $options;
    }

    function CheckLang()
    {
    if ($_GET['lang'] === 'RU' || !isset($_GET['lang'])){
            $currentLang = 'RU';
    }else{
            $currentLang = 'EN';
    }
    return $currentLang;
    }

}

add_action('widgets_init', function ()
{
    register_widget('LastViewedByGrel');
});
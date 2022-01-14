<?php
namespace App\Base;

use App\Base\BaseController;
use App\Config\Config;
use App\Admin\Admin;

class LastViewed extends BaseController
{
    private $args;
    private $currentPostId;
    private $jsvars = array();
    private $viewedlist;
    public $url;

    public function __construct()
    {
        // $widget_options = array(
        //     'classname' => 'LastViewed',
        //     'description' => Config::GREL_WIDGET_DESCRIPTION,
        // );
        // $admin = new Admin();
        // $admin->register();
       // parent::__construct('LastViewed', 'Last Viewed By Grel', $widget_options);
        //подключение скриптов
      
        add_action('wp', array(
            $this,
            'init'
        ));
        //аякс
        add_action('wp_enqueue_scripts', array(
            $this,
            'GrelViewedAssets'
        ));
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
        $cookielive = $this->getCookieLive($val['cookie_live']);
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
        $postlist = $this->getViewedList($ArrayFromObject = true, 'page');
        $cats = $this->getViewedList($ArrayFromObject = true, 'cat');
        if (isset($cats))
        {
            $newPostlist = array_merge($postlist, $cats);
        }else{
            $newPostlist = $postlist;
        }
        $loadwidget = $this->load_widget_ajax($newPostlist);
    }

    function getCookieLive($time)
    {
        if (!empty($time))
        {
            $cookielive = intval($time);
        }else{
            $cookielive = Config::DEFAULT_COOKIE_LIVE;
        }
        return $cookielive;
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
       $basecontroller = new BaseController();
        $script_url = $basecontroller->plugin_url . 'assets/js/main.js';
        wp_enqueue_script('main', $script_url, array(
            'jquery'
        ));
        wp_localize_script('main', 'GVData', $this->jsvars);
    }
 
    function func_set_cookie_data_ajax()
    {
        $phpcookies = $this->generatenewCookie($_POST["current_page_id"]);
        echo json_encode($phpcookies);
        wp_die();
    }

    function load_widget_ajax()
    {
        $ArrayFromObject = true;
        $postlist = $this->getViewedList($ArrayFromObject, 'page');
        $cats = $this->getViewedList($ArrayFromObject, 'cat');
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
            foreach ($arr as $key => $val)
            {
                 if (isset($setting['exclude_ids'])  && count($settings['exclude_ids']) > 0)
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

    // public function widget($args, $instance)
    // {
    //     $title = apply_filters('widget_title', $instance['title']);
        // echo $args['before_widget'] . $args['before_title'] . $title . $args['after_title'];
        /*php// echo $args['after_widget']; */
    // }

    // public function form($instance)
    // {
    //     //include ('Form/form.php');
    //    // return true;
    // }

    // public function update($new_instance, $old_instance)
    // {
    //     $instance = $old_instance;
    //     $instance['title'] = strip_tags($new_instance['title']);
    //     return $instance;
    // }

    function getViewedList($ArrayFromObject, $posttype)
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
                    $args = array(
                        'post_type'=>'page',
                        'post__in' => array_reverse($viewedList) ,
                        'post_status' => 'publish',
                    );
                    $query = new \WP_Query($args);
                break;
            }
    }
           
        if ($ArrayFromObject && $posttype == 'page')
        {
            $resultPages = array();
            $val = get_option('grel_settings');
            foreach ($query->posts as $post)
            {
                $resultPages[] = array(
                    "id" => $post->ID,
                    "post_title" => $post->post_title,
                    "post_link" => get_permalink($post->ID),
                    "post_img" => '',
                    //"post_img" => $val['thumbnails'] == 1 ? get_the_post_thumbnail($post->ID) : '',
                );
            }
            return $resultPages;
        }
        if ($ArrayFromObject && $val['include_rubrics'] == 1 && $posttype == 'cat')
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
   
}
// add_action('widgets_init', function ()
// {
//     register_widget('LastViewed');
// });
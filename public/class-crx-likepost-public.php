<?php

require "widgets/crx-like-post-widget.php";

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://erkankeskin.com.tr
 * @since      1.0.0
 *
 * @package    Crx_Likepost
 * @subpackage Crx_Likepost/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Crx_Likepost
 * @subpackage Crx_Likepost/public
 * @author     Erkan Keskin <info@erkankeskin.com.tr>
 */
class Crx_Likepost_Public
{

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    public $templates;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of the plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        
        //add like button
        add_filter('the_content', [$this, 'add_like_post_button']);

        // run like button
        add_action('wp_ajax_nopriv_liked_action', [$this, 'liked_action']);
        add_action('wp_ajax_liked_action', [$this, 'liked_action']);
        
        //add likepost widget
        add_action( 'widgets_init', [$this, 'crx_likepost_widget'] );

        $this->templates = array(
            plugin_dir_url(__FILE__) .'partials/crx-likepost-public-display.php'=>'Taglist Page'
        );

        add_filter( 'theme_page_templates', [$this, 'crxlikepost_page_template'] );
        add_filter( 'page_template', [$this, 'change_page_template_for_likeposts_list'] );
        add_filter( 'template_include', [$this, 'load_template'] );
        
    }

    public function change_page_template_for_likeposts_list($page_template){
        $page_template = plugin_dir_path( __FILE__ ) . 'partials/crx-likepost-public-display.php';
        return $page_template;
    }

    public function crxlikepost_page_template($templates) {
        $templates = array_merge($templates, $this->templates);
        return $templates;
    }

    public function load_template($template) {
        return $template;
        //var_dump($template);
    }
    
    public function crx_likepost_widget() {
        register_widget( 'CRX_Likepost_Widget' );
    }

    public function get_like_data($post_id)
    {
        global $wpdb;
        $liked = 0;
        $r = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}likepost_datas WHERE user_ip='".$_SERVER["REMOTE_ADDR"]."' AND post_id=".$post_id);

        if (!empty($r)) {
            $liked = 1;
        }

        return $liked;
    }

    private function get_like_count_this_post($post_id)
    {
        global $wpdb;
        $r = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}posts WHERE ID='$post_id' LIMIT 1");
        return $r->crx_post_like;
    }

    private function run_like_post($like)
    {
        global $wpdb;
        $count = $this->get_like_count_this_post($_POST['post_id']);
        if ($like === 0) {
            // like data add
            $table_datas = $wpdb->prefix.'likepost_datas';
            $table_posts = $wpdb->prefix.'posts';
            $data = array('user_ip'=>$_SERVER['REMOTE_ADDR'],'post_id'=>$_POST['post_id']);
            $format = array('%s', '%d');
            $wpdb->insert($table_datas, $data, $format);

            if ($wpdb->insert_id !== false) {
                $updated = $wpdb->query('UPDATE '.$table_posts.' SET crx_post_like='.($count+1).' WHERE ID='.$_POST['post_id']);
                
                if ($updated !== false) {
                    return array(
                        'status'=>'ok',
                        'type'=>'p',
                        'new_count'=>($count+1)
                    );
                } else {
                    return array(
                        'status'=>'error',
                        'type'=>'p'
                    );
                }
            }
        } else {
            // unlike

            $table_datas = $wpdb->prefix.'likepost_datas';
            $table_posts = $wpdb->prefix.'posts';
            $data = array('user_ip'=>$_SERVER['REMOTE_ADDR'],'post_id'=>$_POST['post_id']);
            $format = array('%s', '%d');
            $r = $wpdb->delete($table_datas, $data, $format);

            if ($r !== false) {
                $updated = $wpdb->query('UPDATE '.$table_posts.' SET crx_post_like='.($count-1).' WHERE ID='.$_POST['post_id']);
                
                if ($updated !== false) {
                    return array(
                        'status'=>'ok',
                        'type'=>'n',
                        'new_count'=>($count-1)
                    );
                } else {
                    return array(
                        'status'=>'error',
                        'type'=>'n'
                    );
                }
            }
        }
    }

    public function add_like_post_button($title)
    {
        global $post;
        
        //$r = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}likepost_datas WHERE user_ip='".$_SERVER["REMOTE_ADDR"]."' AND post_id=".$post->ID);
        $like_count = $this->get_like_count_this_post($post->ID);
        
        $icon_class='fa fa-thumbs-o-up';

        if ($this->get_like_data($post->ID) > 0) {
            $icon_class = 'fa fa-thumbs-up';
        }

        $title = sprintf('%s <div id="like-'.$post->ID.'" class="crx-like-button" data-liked="'.$this->get_like_data($post->ID).'"><i class="'.$icon_class.'" aria-hidden="true"></i></div>', $title);
        $title .= '<span class="like-count-notification">'.$like_count.'</span>';

        return $title;
    }

    public function liked_action()
    {
        // Make your response and echo it.
        $liked = $this->get_like_data($_POST['post_id']);
        $r = $this->run_like_post($liked);

        echo json_encode($r);
        //172.21.0.1
        // Don't forget to stop execution afterward.
        wp_die();
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Crx_Likepost_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Crx_Likepost_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/crx-likepost-public.css', array(), $this->version, 'all');
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Crx_Likepost_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Crx_Likepost_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/crx-likepost-public.js', array( 'jquery' ), $this->version, false);
        wp_localize_script($this->plugin_name, 'like_ajax_obj', array( 'ajax_url' => admin_url('admin-ajax.php') ));
    }
}

<?php
/**
 * Plugin Name: BlocksInform
 * Plugin URI: https://doc.blocksinform.com/wordpress-plugin
 * Description: BlocksInform plugin define best post according social activity and provide visitor exchange feature to the website
 * Version: 1.3
 * Author: BlocksInform
 */

define ("BLOCKISNFORM_XPATH_MARKER","/");
define ("BLOCKISNFORM_JS_INDICATOR","{JS}");
define ("BLOCKISNFORM_JS_MARKER","{");
define ("BLOCKSINFORM_CONTENT_FORMAT_STRING",'string');
define ("BLOCKSINFORM_CONTENT_FORMAT_SCRIPT",'script');
define ("BLOCKSINFORM_CONTENT_FORMAT_HTML",'html');
define ("BLOCKSINFORM_PLUGIN_VERSION","1.0.1");


include_once('widget.php');

if (!class_exists('BlocksInformWP')) {
    class BlocksInformWP
    {
        //save internal data
        public $data = array();
        public $_is_widget_on_page;
        public $_is_head_script_loaded = false;


        function __construct()
        {
            global $wpdb;

            //initialize plugin constant
            DEFINE('BlocksInformWP', true);

            $this->_is_widget_on_page = false;
            $this->_is_head_script_loaded = false;

            $this->plugin_name = plugin_basename(__FILE__);
            $this->plugin_directory = plugin_dir_path(__FILE__);
            $this->plugin_url = trailingslashit(WP_PLUGIN_URL . '/' . dirname(plugin_basename(__FILE__)));
            $this->settings = $wpdb->get_row("select * from ".$wpdb->prefix."_blocksinform_settings limit 1");

            $this->tbl_blocks_settings = $wpdb->prefix . '_blocksinform_settings';

            //activation function
            register_activation_hook($this->plugin_name, array(&$this, 'activate'));

            // Enable sidebar widgets
            if($this->settings != NULL && !empty($this->settings->publisher_id)){
                //register BlocksInform widget
                add_action('widgets_init',
                function(){
                    return register_widget("WP_Widget_BlocksInform");
                    }
                );
            }

            if (is_admin()) {
                //add menu for plugin
                add_action( 'admin_menu', array(&$this, 'admin_generate_menu') );
                add_filter('plugin_action_links', array(&$this, 'plugin_action_links'), 10, 2 );
            } else {
                if($this->settings != NULL){
                    wp_enqueue_script('blocksinform-script-init', plugin_dir_url(__FILE__ ) . '/js/blocksinform_loader_script.js', array(), '1.0.0', true );
                    
                    wp_localize_script( 'blocksinform-script-init', 'pub_vars', array(
                        'pub_id'=> esc_html($this->settings->publisher_id)
                    ) );

                    add_action('dynamic_sidebar_before', array(&$this, 'load_sidebar_blocksinform_content'),10,2);
                    add_filter('the_content', array(&$this, 'load_blocksinform_content'));

                }
            }
        }




        function plugin_action_links($links, $file) {
            static $this_plugin;

            if (!$this_plugin) {
                $this_plugin = plugin_basename(__FILE__);
            }

            if ($file == $this_plugin) {
                $settings_link = '<a href="' . get_bloginfo('wpurl') . '/wp-admin/options-general.php?page=blocksinform_widget">Settings</a>';
                array_unshift($links, $settings_link);
            }

            return $links;
        }

        private function should_show_content_widget(){
            $retVal = (trim($this->settings->publisher_id) != '')  && $this->settings->below_enabled;
            return $retVal;
        }

        private function should_show_sidebar_widget(){
            $retVal = ((trim($this->settings->publisher_id) != '') && $this->settings->sidebar_enabled);
            return $retVal;
        }

        // Determine if a blocksinform widget should be added somewhere on the current page (content or sidebar)
        function is_widget_on_page(){
            return  $this->should_show_content_widget() || $this->should_show_sidebar_widget();
        }

        function get_page_type(){

            $page_type='article';
            if (is_front_page()){
            $page_type='home';
            }else if (is_category() || is_archive() || is_search()){
                $page_type='category';
            }
            return $page_type;
        }

        function load_blocksinform_content($content)
        {
           
            if ($this->should_show_content_widget())
            {
                $snippet_code = "<div id='blocksinform-below-article'></div>";
              //  $blocksinform_content[BLOCKSINFORM_CONTENT_FORMAT_SCRIPT][] = $firstWidgetScript;
                if ( is_single() && ! is_admin() ) {
                    return  $this->prefix_insert_after_n_paragraph( $snippet_code, 3, $content );
                }
            }
                // // Adding sidebar widget
                // if($this->settings->sidebar_enabled){

	            //     $secondWidgetParams = array('{{CONTAINER}}' => 'blocksinform-sidebar',
	            //                                '{{PLACEMENT}}' =>  'sidebar-article');
	            //     $secondWidgetScript = new JavaScriptWrapper("blocksinform_widget_script.js",$secondWidgetParams);

                //     $blocksinform_content[BLOCKSINFORM_CONTENT_FORMAT_HTML][] = "<div id='blocksinform-sidebar'></div>";
                //     $blocksinform_content[BLOCKSINFORM_CONTENT_FORMAT_SCRIPT][] = $secondWidgetScript;
                // }

             //todo location website

            return $content;
        }

        function load_sidebar_blocksinform_content( $index, $bool )
        {
            if ( 'sidebar-1' == $index ) {
                echo "<div id='blocksinform-sidebar'></div>";
            }
        }

        function prefix_insert_after_n_paragraph( $insertion, $paragraph_id, $content ) {
            $closing_p = '</p>';
            $paragraphs = explode( $closing_p, $content );
            foreach ($paragraphs as $index => $paragraph) {
                // Only add closing tag to non-empty paragraphs
                if ( trim( $paragraph ) ) {
                    // Adding closing markup now, rather than at implode, means insertion
                    // is outside of the paragraph markup, and not just inside of it.
                    $paragraphs[$index] .= $closing_p;
                }
        
                // + 1 allows for considering the first paragraph as #1, not #0.
                if ( $paragraph_id == $index + 1 ) {
                    $paragraphs[$index] .= $insertion;
                }
            }
            return implode( '', $paragraphs );
        }

        function admin_generate_menu(){
            global $current_user;
            add_menu_page('BlocksInform', 'BlocksInform', 'manage_options', 'blocksinform_widget', array(&$this, 'admin_blocksinform_settings'), $this->plugin_url.'img/blocksinform_icon.png', 110);
        }

        function admin_blocksinform_settings(){
            global $wpdb;
            $settings = $wpdb->get_row("select * from ".$wpdb->prefix."_blocksinform_settings limit 1");
            $blocksinform_errors = array();
            if($_SERVER['REQUEST_METHOD'] == 'POST'){

                if(trim($_POST['publisher_id']) == ''){
                    $blocksinform_errors[] = "Please add a 'Publisher ID' in order to apply changes to your widgets";
                }
               
                if(count($blocksinform_errors) == 0){

                    $data = array(
                        "publisher_id" => sanitize_text_field(trim($_POST['publisher_id'])),
                        "below_enabled" => isset($_POST['below_enabled']) ? true : false,
                        "sidebar_enabled" => isset($_POST['sidebar_enabled']) ? true : false,
                    );

                    //var_dump($settings);
                    if($settings == NULL){
                        $wpdb->insert($this->tbl_blocks_settings, $data, null, null);
                    } else {
                        $wpdb->update($this->tbl_blocks_settings, $data, array('id' => $settings->id));
                    }
                }
                $settings = $wpdb->get_row("select * from ".$wpdb->prefix."_blocksinform_settings limit 1");
            }


            include_once('settings.php');
        }

        function is_location_string_valid($location_string){
            // TODO:: validate the location string
            return true;
        }
        function activate(){
            global $wpdb;

            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

            $settings_table = $this->tbl_blocks_settings;

            //check mysql version
            if (function_exists('mysql_get_server_info') && version_compare(mysql_get_server_info(), '4.1.0', '>=')) {
                if (!empty($wpdb->charset))
                    $charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
                if (!empty($wpdb->collate))
                    $charset_collate .= " COLLATE $wpdb->collate";
            }

            //settings table structure
            $sql_table_settings = "
                CREATE TABLE `" . $wpdb->prefix . "_blocksinform_settings` (
                    `id` INT NOT NULL AUTO_INCREMENT ,
                    `publisher_id` VARCHAR(255) DEFAULT NULL,
                    `below_enabled` TINYINT(1) NOT NULL DEFAULT FALSE,
                    `sidebar_enabled` TINYINT(1) NOT NULL DEFAULT FALSE,
                    PRIMARY KEY (`id`)
                )" . $charset_collate . ";";

                // create/update the table
                dbDelta($sql_table_settings);
        }
    }
}

global $blocksinformWP;
$blocksinformWP = new BlocksInformWP();

//

<?php
define ("BLOCKSINFORM_WIDGET_BASE_ID","blocksinform");
define ("BLOCKSINFORM_CONTAINER_PREFIX","inner-");
class WP_Widget_BlocksInform extends WP_Widget {

    private static $counter;
    function __construct() {


        $widget_ops = array('classname' => 'widget_blocksinform', 'description' => __( "A BlocksInform widget for your site.") );
        parent::__construct( BLOCKSINFORM_WIDGET_BASE_ID, _x( 'BlocksInform Widget', 'BlocksInform Widget' ), $widget_ops );
        $this->plugin_directory = plugin_dir_path(__FILE__);
    }

    function get_id($tag){

        $start_pos = strpos($tag,"id=");

        $container_id = null;
        if ($start_pos !== false){

            $end_pos = strpos($tag," ",$start_pos);
            $extracted_id = str_replace(array('"',"'"),"",substr($tag,$start_pos+3,$end_pos-$start_pos-3));
            if ($extracted_id != ""){
                $container_id = $extracted_id;
            }
        }
        return $container_id;
    }

    function widget( $args, $instance ) {
        if (!isset(WP_Widget_BlocksInform::$counter)){
            WP_Widget_BlocksInform::$counter = 1;
        }
        else{
            WP_Widget_BlocksInform::$counter = WP_Widget_BlocksInform::$counter + 1;
        }
       
        extract($args);

        /** This filter is documented in wp-includes/default-widgets.php */
        $title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );

        $placement=$args["id"]."-0";

        echo $before_widget;

        if ( $title ){
            echo $before_title . $title . $after_title;
        }

        $container = $this->get_id($before_widget);

        if ($container == null){
            // widget container (adding a div)
            $container = BLOCKSINFORM_CONTAINER_PREFIX.$this->id;
            echo '<div id="'.esc_html($container).'"></div>';
        }
    }

    function form( $instance ) {
        ?>
        <p>
            <label >
                <input class="widefat"  type="text" />
            </label>
        </p>
    <?php
    }

    function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        return $instance;
    }

}

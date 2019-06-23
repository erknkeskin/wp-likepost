<?php
// Creating the widget 
class CRX_Likepost_Widget extends WP_Widget {
 
    function __construct() {
        parent::__construct(
        
        'CRX_Likepost_Widget', 
        
        __('CRXLikepost Widget', 'crx_widget_domain'), 
        
        array( 'description' => __( 'Top like posts', 'crx_widget_domain' ), ) );

    }
 
    public function widget( $args, $instance ) {
        $title = apply_filters( 'widget_title', $instance['title'] );
        
        echo $args['before_widget'];
        if ( ! empty( $title ) )
            echo $args['before_title'] . $title . $args['after_title'];
        
        // This is where you run the code and display the output
        //echo __( 'Aloooo!', 'crx_widget_domain' );
        global $wpdb;

        $q = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}terms AS t1 LEFT JOIN {$wpdb->prefix}term_taxonomy AS t2 ON t1.term_id = t2.term_id WHERE taxonomy='post_tag' ORDER BY t2.count DESC LIMIT 10");
        echo '<div class="crx-widget-list"><ul>';
            foreach ($q as $row):
                echo '<li><a target="_blank" href="'.site_url().'/tag/'.$row->slug.'">'.$row->name.'<span>('.$row->count.')</span></a></li>';
            endforeach;
        echo '</ul></div>';
    }
         
    public function form( $instance ) {
        if ( isset( $instance[ 'title' ] ) ) {
            $title = $instance[ 'title' ];
        }
        else {
            $title = __( 'New title', 'crx_widget_domain' );
        }
    ?>
    <p>
        <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
        <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
    </p>
    <?php 
    }
     
    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        return $instance;
    }
}

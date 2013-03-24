<?php
/*
Plugin Name: FRC Blue Alliance
Plugin URI: https://github.com/MechaMonarchs/wordpressBlueAlliance
Version: 0.0.2
Author: Damien MechaMonarchs (FRC Team 2896)
Description: Integrates feeds from The Blue Alliance, a statistics site for FIRST Robotics
*/
class TBA_Widget extends WP_Widget {

	public function __construct() {
		// widget actual processes
		parent::__construct(
		    'tba_widget', //Base ID
		    'TBA_Widget', //Name
		    array( 'description' => __( 'The Blue Alliance integration', 'text_domain' ), ) //Arguments
		    );
	}
    /**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		// outputs the content of the widget
		extract($args);
		$title = apply_filters( 'widget_title', $instance['title']);

		echo $before_widget;
		if (!empty($title))
		echo $before_title.$title.$after_title;
		echo __('Hello, World!', 'text_domain');
		echo $after_widget;
	}
    /**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */

    public function update( $new_instance, $old_instance ) {
    // processes widget options to be saved
        $instance = array();
        $instance['title'] = strip_tags($new_instance['title']);

        return $instance;
    }

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
 	public function form( $instance ) {
		// outputs the options form on admin
		if( isset($instance['title'])){
		    $title = $instance['title'];
		}
		else{
		    $title = __('New Title', 'text_domain');
		}
		?>
<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
</p>
    <?
	}


}
add_action( 'widgets_init', create_function( '', 'register_widget( "foo_widget" );' ) );
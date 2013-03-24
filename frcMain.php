<?php
/*
Plugin Name: FRC Blue Alliance
Plugin URI: https://github.com/MechaMonarchs/wordpressBlueAlliance
Version: 0.0.2
Author: Damien MechaMonarchs (FRC Team 2896)
Description: Integrates feeds from The Blue Alliance, a statistics site for FIRST Robotics
*/
class TBA_Widget extends WP_Widget
{
    function TBA_Widget()
    {
        $widget_ops = array('classname' =>'TBA_Widget', 'description' => 'Displays score feeds from The BLue Alliance');
        $this->WP_Widget('TBA_Widget', 'The Blue Alliance integration', $widget_ops);
    }

    function form($instance)
    {
        $instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
        $title = $instance['title'];
?>
<p><label for="<?php echo $this->get_field_id('title'); ?>">Title: <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo attribute_escape($title); ?>" /></label></p>
<?
    }

    function update($new_instance, $old_instance)
    {
        $instance = $old_instance;
        $instance['title'] = $new_instance['title'];
        return $instance;
    }

    function widget($args, $instance)
    {
        extract($args, EXTR_SKIP);

        echo $before_widget;
        $title = empty($instance['titile']) ? '' : apply_filters('widget_title', @instance['title']);
        if (!empty($title))
            echo $before_title.$title.$after_title;;

        // WIDGET CODE GOES HERE
            echo"<h1>This is a widget!</h1>";

            echo $after_widget;
        }
    }
    add_action('widgets_init', create_function('', 'return register_widget("TBA_Widget");') );?>
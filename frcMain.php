<?php
/*
Plugin Name: FRC Blue Alliance
Plugin URI: https://github.com/MechaMonarchs/wordpressFRCFMS
Version: 0.0.3
Author: Damien MechaMonarchs (FRC Team 2896)
Description: Integrates data from FRCFMS, a score reporting site for FIRST Robotics
*/
class FMS_Widget extends WP_Widget
{
  function TBA_Parser($team, $event)
   {
        $data = file_get_contents('https://search.twitter.com/search.json?q=%23'.$event.'%20from:frcfms');
        $data = json_decode($data);
        $results = count($data -> results);
        for($i = 0; $i <= $results; $i++){
            $frcfms = $data -> results[$i] -> text;
            $frcfms = preg_split("(#FRC\w* |TY | MC | RF | BF | RA | BA | RC | BC | RFP | BFP | RAS | BAS | RTS | BTS )", $frcfms);
            if (in_array($team))
                $i = $results + 1;
        }
        return array(
            "match" => $frcfms[3],
            "red" => $frcfms[4],
            "blue" => $frcfms[5],
            "rAlliance" => explode(" ",$frcfms[6]),
            "bAlliance" => explode(" ",$frcfms[7]),
            );
   }
  function FMS_Widget()
  {
    $widget_ops = array('classname' => 'FMS_Widget', 'description' => 'Integrates match feeds from the @FRCFMS feed' );
    $this->WP_Widget('FMS_Widget', 'FMS Feed', $widget_ops);
  }

  function form($instance)
  {
    $instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
    $title = $instance['title'];
?>
  <p><label for="<?php echo $this->get_field_id('title'); ?>">Title: <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo attribute_escape($title); ?>" /></label></p>
  <p><label for="<?php echo $this->get_field_id('team'); ?>">Team: <input class="widefat" id="<?php echo $this->get_field_id('team'); ?>" name="<?php echo $this->get_field_name('team'); ?>" type="text" value="<?php echo attribute_escape($team); ?>" /></label></p>
  <p><label for="<?php echo $this->get_field_id('event'); ?>">Event: <input class="widefat" id="<?php echo $this->get_field_id('event'); ?>" name="<?php echo $this->get_field_name('event'); ?>" type="text" value="<?php echo attribute_escape($event); ?>" /></label></p>
<?php
  }

  function update($new_instance, $old_instance)
  {
    $instance = $old_instance;
    $instance['title'] = $new_instance['title'];
    $instance['team'] = $new_instance['team'];
    $instance['event'] = $new_instance['event'];
    return $instance;
  }

  function widget($args, $instance)
  {
    extract($args, EXTR_SKIP);

    echo $before_widget;
    $title = empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);
    $team = empty($instance['team']) ? ' ' : apply_filters('widget_title', $instance['team']);
    $event = empty($instance['event']) ? ' ' : apply_filters('widget_title', $instance['event']);

    if (!empty($title))
      echo $before_title . $title . $after_title;;

    // WIDGET CODE GOES HERE
    $match = $this->TBA_Parser($instance['team'], $instance['event']);
    echo $match['match'];
    echo $match['red'];
    echo $match['blue'];
    echo $match['rAlliance'];
    echo $match['bAlliance'];
    echo $after_widget;
  }

}
add_action( 'widgets_init', create_function('', 'return register_widget("FMS_Widget");') );?>
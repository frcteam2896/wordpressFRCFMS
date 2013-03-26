<?php
/*
Plugin Name: FRCFMS widget
Plugin URI: https://github.com/MechaMonarchs/wordpressFRCFMS
Version: 0.1.0
Author: Damien MechaMonarchs (FRC Team 2896)
Description: Integrates data from FRCFMS, a score reporting site for FIRST Robotics
*/
class FMS_Widget extends WP_Widget
{
  function TBA_Parser($team, $event){
        $data = file_get_contents('https://search.twitter.com/search.json?q=%23'.$event.'%20from:frcfms');
        $data = json_decode($data);
        $n = count($data -> results);
        for($i = 0; $i < $n; $i++){
            $frcfms = $data -> results[$i] -> text;
            $frcfms = preg_split("(#FRC\w* |TY | MC | RF | BF | RA | BA | RC | BC | RFP | BFP | RAS | BAS | RTS | BTS )", $frcfms);
            if (in_array($team, $frcfms))
                $i = $n + 1;
        }
        return array(
            'type' => $frcfms[2],
            'match' => $frcfms[3],
            'red' => $frcfms[4],
            'blue' => $frcfms[5],
            'rAlliance' => explode(" ",$frcfms[6]),
            'bAlliance' => explode(" ",$frcfms[7]),
            );
   }
  function FMS_Widget()
  {
    $widget_ops = array('classname' => 'FMS_Widget', 'description' => 'Integrates match feeds from the @FRCFMS feed' );
    $this->WP_Widget('FMS_Widget', 'FMS Feed', $widget_ops);
  }

  function form($instance)
  {
    $instance = wp_parse_args( (array) $instance, array( 'title' => '', 'team' => '', 'event' => '' ) );
    $title = $instance['title'];
    $team = $instance['team'];
    $event = $instance['event'];
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
    $patt = '/\b'.$team.'\b/';
    $match["rAlliance"] = preg_replace($patt,'<b>$0</b>', $match["rAlliance"]);
    $match["bAlliance"] = preg_replace($patt,'<b>$0</b>', $match["bAlliance"]);
    echo($match["match"].$match["type"].'<br/>');
    echo('<br/>Red:<br/>'.$match["rAlliance"][0].'<br/>');
    echo($match["rAlliance"][1].'<br/>');
    echo($match["rAlliance"][2].'<br/>');
    echo('Score:'.$match["red"].'<br/>');
    echo('<br/>Blue:</br>'.$match["bAlliance"][0].'<br/>');
    echo($match["bAlliance"][1].'<br/>');
    echo($match["bAlliance"][2].'<br/>');
    echo('Score:'.$match["Blue"].'<br/>');
    echo $after_widget;
  }

}
add_action( 'widgets_init', create_function('', 'return register_widget("FMS_Widget");') );?>
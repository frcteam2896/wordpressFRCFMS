<?php
/*
Plugin Name: FRCFMS widget
Plugin URI: https://github.com/MechaMonarchs/wordpressFRCFMS
Version: 0.1.1(b)
Author: Damien MechaMonarchs (FRC Team 2896)
Description: Integrates data from FRCFMS, a score reporting site for FIRST Robotics.  Includes shortcode in format [FMS team=#### event=FRC(event code) data=(type, match, red, blue, rAlliance, bAlliance')] where "type" is the match type (practice, qualifier, or elimination), "match" is the match number, "red" is the red alliance's score, "blue" is the blue alliance's score, "rAlliance" is the teams in the Red Alliance, and "bAlliance" is the teams in the Blue Alliance.
*/
class FMS_Widget extends WP_Widget
{
  function TBA_Parser($team, $event){
        $data = file_get_contents('https://search.twitter.com/search.json?q=%23'.$event.'%20from:frcfms');
        if (empty($data->results))
            return(NULL);
        $data = json_decode($data);
        $n = count($data -> results);
        for($i = 0; $i < $n; $i++){
            $frcfms = $data -> results[$i] -> text;
            if (preg_match('/'.$team.'/',$frcfms))
                $i = $n + 1;
        }
        $frcfms = preg_split("(#FRC\w* |TY | MC | RF | BF | RA | BA | RC | BC | RFP | BFP | RAS | BAS | RTS | BTS )", $frcfms);
        $frcfms[6] = explode(" ",$frcfms[6]);
        $frcfms[7] = explode(" ",$frcfms[7]);
        $patt = '/\b'.$team.'\b/';
        $frcfms[6] = preg_replace($patt,'<b>$0</b>',$frcfms[6]);
        $frcfms[7] = preg_replace($patt,'<b>$0</b>',$frcfms[7]);
        return array(
            'type' => $frcfms[2],
            'match' => $frcfms[3],
            'red' => $frcfms[4],
            'blue' => $frcfms[5],
            'rAlliance' => $frcfms[6],
            'bAlliance' => $frcfms[7],
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
    if($match==NULL)
        echo("Could not find match data.  Has the event started yet?");
    else{
        echo($match["match"].$match["type"].'<br/>');
        echo('<div class="fmsred"><br/>Red:<br/>'.$match["rAlliance"][0].'<br/>');
        echo($match["rAlliance"][1].'<br/>');
        echo($match["rAlliance"][2].'<br/>');
        echo('<div class="sred">Score:'.$match["red"].'<br/></div><div>');
        echo('<div class="fmsblue"><br/>Blue:</br>'.$match["bAlliance"][0].'<br/>');
        echo($match["bAlliance"][1].'<br/>');
        echo($match["bAlliance"][2].'<br/>');
        echo('<div class="sblue">Score:'.$match["Blue"].'<br/></div></div>');
        echo $after_widget;
    }
  }
}
add_action( 'widgets_init', create_function('', 'return register_widget("FMS_Widget");') );?>
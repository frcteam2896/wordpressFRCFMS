<?php
/*
Plugin Name: FRCFMS widget
Plugin URI: https://github.com/MechaMonarchs/wordpressFRCFMS
Version: 0.1.4
Author: Damien MechaMonarchs (FRC Team 2896)
Description: Integrates data from FRCFMS, a score reporting site for FIRST Robotics.
*/
class FMS_Widget extends WP_Widget
{
  function TBA_Parser($team, $event){
      #Initialize variables
      $m = 0;
      #Fetches data from Twitter API
        $data = file_get_contents('https://search.twitter.com/search.json?q=%23frc'.$event.'%20from:frcfms');
        $data = json_decode($data);
      #Event error handling
        if (empty($data->results))
            return(array(NULL,0));
      #Parses event data to find matches with selected teams in them
        $n = count($data -> results);
        for($i = 0; $i < $n; $i++){
            $frcfms = $data -> results[$i] -> text;
            if (preg_match('/'.$team.'/',$frcfms)){
                $return[$m++]=$frcfms;
        }
      #Match error handling
        if ($return = NULL)
            return(array(NULL,1));
      #Returns raw twitter data as an array item for each match
        return $return;
  }
  #Splits specified match returned by TBA_Parse function into match data
function FMS_Split($frcfms, $team, $match){
  #Selects chosen match
    $frcfms = $frcfms[$match];
  #Splits match data by delimiters
    $frcfms = preg_split("(#FRC\w* |TY | MC | RF | BF | RA | BA | RC | BC | RFP | BFP | RAS | BAS | RTS | BTS )", $frcfms);
  #Splits team data into subarrays
    $frcfms[6] = \explode(" ",$frcfms[6]);
    $frcfms[7] = \explode(" ",$frcfms[7]);
  #Bolds selected team
    $patt = '/\b'.$team.'\b/';
    $frcfms[6] = preg_replace($patt,'<b>$0</b>',$frcfms[6]);
    $frcfms[7] = preg_replace($patt,'<b>$0</b>',$frcfms[7]);
  #Returns match data as an array
    return array(
        'type' => $frcfms[2],
        'match' => $frcfms[3],
        'red' => $frcfms[4],
        'blue' => $frcfms[5],
        'rAlliance' => $frcfms[6],
        'bAlliance' => $frcfms[7],
        );
}
#Widget metadata  
function FMS_Widget()
  {
    $widget_ops = array('classname' => 'FMS_Widget', 'description' => 'Integrates match feeds from the @FRCFMS feed' );
    $this->WP_Widget('FMS_Widget', 'FMS Feed', $widget_ops);
  }
    #Returns widget settings form
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
    #Updates widget settings
    function update($new_instance, $old_instance)
    {
      $instance = $old_instance;
      $instance['title'] = $new_instance['title'];
      $instance['team'] = $new_instance['team'];
      $instance['event'] = $new_instance['event'];
      return $instance;
    }

  #Renders widget
  function widget($args, $instance)
  {
    extract($args, EXTR_SKIP);

    #Pre-widget crap
    echo $before_widget;
    
    #Moves instance variables to local ones
    $title = empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);
    $team = empty($instance['team']) ? ' ' : apply_filters('widget_title', $instance['team']);
    $event = empty($instance['event']) ? ' ' : apply_filters('widget_title', $instance['event']);
    #Renders title
    if (!empty($title))
      echo $before_title . $title . $after_title;;
    
    #Pull and parse data from the FMS feed
    $frcfms = $this->TBA_Parser($team, $event);
    $match = $this->FMS_Split($frcfms, $team, -1);
    #General error handling
    if($match[0]!=NULL){ ?>
        <h4>Match:<? echo($match["match"].$match["type"]);?></h4>
        <ul class="fms">
                <li>Red:
                    <ul class="red">
                        <li><? echo($match["rAlliance"][0]);?></li>
                        <li><? echo($match["rAlliance"][1]);?></li>
                        <li><? echo($match["rAlliance"][2]);?></li>
                        <li class="score">Score:<? echo($match["red"]);?></li>
                    </ul>
                </li>
                <li>Blue
                    <ul class="blue">
                        <li><? echo($match["bAlliance"][0]);?></li>
                        <li><? echo($match["bAlliance"][1]);?></li>
                        <li><? echo($match["bAlliance"][2]);?></li>
                        <li class="score">Score:<? echo($match["blue"]);?><li>
                    </ul>
                </li>
            </li>

        <? echo $after_widget;
    }
    #General error handling
    else{
        if($match[1]==0)
            echo"No matches have started yet, please check back later";
        elseif ($match[1]==1) 
           echo"This team has not played any matches yet, please check back later";
    }
  }
}
#Inserts plugin stylesheets into webpage head
function FMS_style() {
        wp_register_style( 'FMS-style', plugins_url('style.css', __FILE__) );
        wp_enqueue_style( 'FMS-style' );
}
#Enqueues(inserts) widget data
add_action( 'wp_enqueue_scripts', 'FMS_style' );
add_action( 'widgets_init', create_function('', 'return register_widget("FMS_Widget");') );?>
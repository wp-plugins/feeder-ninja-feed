<?php
/*
 * Plugin Name: Feeder Ninja feeds
 * Plugin URI: https://www.feederninja.com
 * Description: Add beautiful <strong>RSS & Social feeds</strong> to your Wordpress website. We're supporting RSS, Facebook, Twitter, YouTube, Vimeo, Thumblr, Pinterest and Wordpress feeds!
 * Version: 1.0.2
 * Author: Common Ninja
 * Author URI: http://commoninja.com/
 * License: GPLv2 or later
 */

/**
 * Register the Widget
 */
add_action( 'widgets_init', create_function( '', 'register_widget("feederninja_widget");' ) );

/**
 * Create the widget class and extend from the WP_Widget
 */
 class FeederNinja_Widget extends WP_Widget {

	/**
	 * Set the widget defaults
	 */
	private $widget_title = "Feeder Ninja feeds";
	private $feed_guid = "7564656dc11643eb8ac657f42afc43c1";
	private $height = "500";

	private $skip = array();

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {

		parent::__construct(
			'FeederNinja_Widget',// Base ID
			'Feeder Ninja ', // Name
			array(
				'classname'		=>	'FeederNinja_Widget',
				'description'	=>	__('Add a Feeder Ninja feed widget to your website.', 'framework')
			)
		);

	} // end constructor

	public function curPageURL() {
		$pageURL = 'https://';
		if ($_SERVER["SERVER_PORT"] != "80") {
			$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
		} else {
			$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		}
		return $pageURL;
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
		extract( $args );

		/* Our variables from the widget settings. */
		$this->widget_title = apply_filters('widget_title', $instance['title'] );

		$this->show_stream = ($instance['show_stream'] == "1" ?  True : False);
		$this->feed_guid = $instance['feed_guid'];
		$this->height = $instance['height'];

		/* Before widget (defined by themes). */
		echo $before_widget;

		/* Display the widget title if one was input (before and after defined by themes). */
		if ( $this->widget_title )
			echo $before_title . $this->widget_title . $after_title;

		/* Render Feeder Ninja */
        $this->render( array('feed_guid' => $this->feed_guid,
                             'height' => $this->height) );

		/* After widget (defined by themes). */
		echo $after_widget;
	}

    public function render( $args, $return=false ) {
        $r = wp_parse_args( $args
                            , array('feed_guid' => '7564656dc11643eb8ac657f42afc43c1'
                                    ,'height' => '0'
                                    ) );

        if ( empty( $r['height'] ) )
            $r['height'] = '500px';
        else
            $r['height'] .= 'px';

        $params = '';

        ?>

        <?php
        if  ( true ){

        	// accept both a url feed_guid and a regular feed_guid
        	if( strrpos($r['feed_guid'], "/") !== FALSE ){
        		$feed_guid_tmp = explode("/", $r['feed_guid']);
        		$r['feed_guid'] = $feed_guid_tmp[count($feed_guid_tmp) - 1];
        	}

			$siteUrl = $this->curPageURL();
            $html =	'<div class="feederninja_widget">' .
					'<iframe style="width:100%;height:' . $r['height'] . ';border:none;margin:0;padding:0;" ' .
            		'src="https://www.feederninja.com/wordpress/feed/' . $r['feed_guid'] . '?fnurl=' . $siteUrl . '" ' .
            		'frameborder="0"></iframe>' .
					'</div>';
			if( $return ) {
				/** A WP shortcode should return the content, not echo it **/
				return $html;
			} else {
				echo $html;
			}       
        }
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
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		
		$instance['title'] = strip_tags( $new_instance['title'] );
		
		/* Strip tags for title and name to remove HTML (important for text inputs). */
		$instance['feed_guid'] = strip_tags( $new_instance['feed_guid'] );
		$instance['height'] = strip_tags( $new_instance['height'] );

		return $instance;
	}

	/**
	 * Create the form for the Widget admin
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	function form( $instance ) {

		/* Set up some default widget settings. */
		$defaults = array(
			'feed_guid' => $this->feed_guid,
			'height' => $this->height
		);

		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

        <h4 style="border-bottom: solid 1px #CCC;">Feeder Ninja Settings</h4>
		
		<!-- Title: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title', 'wp_widget_plugin'); ?> </label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $instance['title']; ?>" />
		</p>		
		
		<!-- Page name: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'feed_guid' ); ?>"><?php _e('Feed ID', 'framework') ?> </label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'feed_guid' ); ?>" name="<?php echo $this->get_field_name( 'feed_guid' ); ?>" value="<?php echo $instance['feed_guid']; ?>" />
            <br><small style="display: block; text-align: right; margin: 3px 5px 0;"><a href="https://www.feederninja.com/wordpress" target="_blank">what's this?</a></small>
		</p>

		<!-- Height: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'height' ); ?>"><?php _e('Height', 'framework') ?> </label>
			<input type="text" id="<?php echo $this->get_field_id( 'height' ); ?>" name="<?php echo $this->get_field_name( 'height' ); ?>" value="<?php echo $instance['height']; ?>" style="width: 40px" /> px
		</p>

	<?php
	}
 }


/**
 * Display the Feeder Ninja feed
 *
 * The list of arguments is below:
 *     'feed_guid' (string) - Feed guid
 *     'height' (int) - height of the iframe
 *
 * @param string|array $args Optional. Override the defaults.
 */ 
function feederninja_embed( $args ) {
    $rm_widget = new FeederNinja_Widget();
    $rm_widget->render( $args );
}

/**
 * Shortcode to diplay Feeder Ninja in your site.
 * 
 * The list of arguments is below:
 *     'feed_guid' (string) - Feed guid
 *     'height' (int) - height of the iframe
 *                    Default: 500
 */
function feederninja_shortcode( $atts ) {
    extract( shortcode_atts( array(
        'feed_guid' => '7564656dc11643eb8ac657f42afc43c1',
        'height' => '500'
    ), $atts ) );

    $rm_widget = new FeederNinja_Widget();
    
    return $rm_widget->render( array(
        'feed_guid' => $feed_guid,
        'height' => $height
    ), true );
}
add_shortcode( 'feederninja', 'feederninja_shortcode' );

?>

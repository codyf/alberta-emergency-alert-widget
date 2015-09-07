<?php
/*
	Plugin name: Alberta Emergency Alert Widget
	Plugin URI: http://demand.cr
	Author: Demand Creativity
	Author URI: http://demand.cr
	Version: 1.0.1
	Description: Display the Alberta Emergency Alert status in a widget

	-----

	Copyright (C) 2015  Cody Foss
    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.

	-----

	All images and logos related to the Alberta Emergency Alert program are
	not subject to this license and remain property of their respective owners.

*/


class AEAW_Widget extends WP_Widget {

	public function __construct() {
		$widget_ops = array(
			'description' => __('Entries from the Alberta Emergency Alerts feed')
		);
		$control_ops = array(
			'width' => 400,
			'height' => 200
		);

		parent::__construct(
			'aeaw',
			__('Alberta Emergency Alerts'),
			$widget_ops,
			$control_ops
		);
	}

	/*
	 * Widget options form
	 */
 	public function form( $instance ) {

		// default values
		if ( empty($instance) ) {
			$instance = array(
				'title' => '',
				'items' => 10,
				'error' => false,
				'small_logo' => 0,
				'show_title' => 1,
				'show_summary' => 0,
				'show_author' => 0,
				'show_date' => 0,
				'link_back_to_aea' => 1
			);
		}

		$instance['number'] = $this->number;

		$default_inputs = array(
			'title' => true,
			'items' => true,
			'small_logo' => true,
			'show_title' => true,
			'show_summary' => true,
			'show_author' => true,
			'show_date' => true,
			'link_back_to_aea' => true
		 );
		$inputs = wp_parse_args( $instance, $default_inputs );

		extract( $inputs, EXTR_SKIP);

		// set values for form
		$number = esc_attr( $number );
		$title  = esc_attr( $title );
		$items  = intVal($items);
		$show_title = intVal($show_title);
		$show_summary = intVal($show_summary);
		$show_author = intVal($show_author);
		$show_date = intVal($show_date);
		$link_back_to_aea = intVal($link_back_to_aea);

		// if the number of items to show is out of regular bounds,
		// set default number of 10
		if ( $items < 1 || 20 < $items ){
			$items  = 10;
		}

		// START: Widget options form
		if ( !empty($error) ) {
			echo '<p class="widget-error"><strong>' . sprintf( __('RSS Error: %s'), $error) . '</strong></p>';
		}

		if ( isset($inputs['title']) ) :
	?>
		<p>
			<label for="aeaw-title-<?php echo $number; ?>"><?php _e('Rename the feed (optional):'); ?></label>
			<input class="widefat" id="aeaw-title-<?php echo $number; ?>" name="widget-aeaw[<?php echo $number; ?>][title]" type="text" value="<?php echo $title; ?>" /><br />
			<small><?php echo _e('If left empty, defaults to what the feed provides (\'Alberta Emergency Alert - Current\')') ?></small>
		</p>
	<?php
		endif;

		if ( isset($inputs['items']) ) :
	?>
		<p><label for="aeaw-items-<?php echo $number; ?>"><?php _e('How many items would you like to display?'); ?></label>
		<select id="aeaw-items-<?php echo $number; ?>" name="widget-aeaw[<?php echo $number; ?>][items]">
	<?php
			for ( $i = 1; $i <= 20; ++$i )
				echo "<option value='$i' " . ( $items == $i ? "selected='selected'" : '' ) . ">$i</option>";
	?>
		</select></p>
	<?php
		endif;

		if ( isset($inputs['small_logo']) ) :
	?>
		<p><input id="aeaw-show-small_logo-<?php echo $number; ?>" name="widget-aeaw[<?php echo $number; ?>][small_logo]" type="checkbox" value="1" <?php if ( $small_logo ) echo 'checked="checked"'; ?>/>
		<label for="aeaw-show-small_logo-<?php echo $number; ?>"><?php _e('Use the small logo?'); ?></label></p>
	<?php
		endif;

		if ( isset($inputs['show_title']) ) :
	?>
		<p><input id="aeaw-show-title-<?php echo $number; ?>" name="widget-aeaw[<?php echo $number; ?>][show_title]" type="checkbox" value="1" <?php if ( $show_title ) echo 'checked="checked"'; ?>/>
		<label for="aeaw-show-title-<?php echo $number; ?>"><?php _e('Display the title?'); ?></label></p>
	<?php
		endif;

		if ( isset($inputs['show_summary']) ) :
	?>
		<p><input id="aeaw-show-summary-<?php echo $number; ?>" name="widget-aeaw[<?php echo $number; ?>][show_summary]" type="checkbox" value="1" <?php if ( $show_summary ) echo 'checked="checked"'; ?>/>
		<label for="aeaw-show-summary-<?php echo $number; ?>"><?php _e('Display the description for each item?'); ?></label></p>
	<?php
		endif;

		if ( isset($inputs['show_author']) ) :
	?>
		<p><input id="aeaw-show-author-<?php echo $number; ?>" name="widget-aeaw[<?php echo $number; ?>][show_author]" type="checkbox" value="1" <?php if ( $show_author ) echo 'checked="checked"'; ?>/>
		<label for="aeaw-show-author-<?php echo $number; ?>"><?php _e('Display the author for the item (if available)?'); ?></label></p>
	<?php
		endif;

		if ( isset($inputs['show_date']) ) :
	?>
		<p><input id="aeaw-show-date-<?php echo $number; ?>" name="widget-aeaw[<?php echo $number; ?>][show_date]" type="checkbox" value="1" <?php if ( $show_date ) echo 'checked="checked"'; ?>/>
		<label for="aeaw-show-date-<?php echo $number; ?>"><?php _e('Display item date?'); ?></label></p>
	<?php
		endif;

		if ( isset($inputs['show_date']) ) :
	?>
		<p><input id="aeaw-link-back-<?php echo $number; ?>" name="widget-aeaw[<?php echo $number; ?>][link_back_to_aea]" type="checkbox" value="1" <?php if ( $link_back_to_aea ) echo 'checked="checked"'; ?>/>
		<label for="aeaw-link-back-<?php echo $number; ?>"><?php _e('Link logo back to AEA website?'); ?></label></p>
	<?php
		endif;

		foreach ( array_keys($default_inputs) as $input ) :

			if ( 'hidden' === $inputs[$input] ) :
				$id = str_replace( '_', '-', $input );
	?>
		<input type="hidden" id="aeaw-<?php echo $id; ?>-<?php echo $number; ?>" name="widget-aeaw[<?php echo $number; ?>][<?php echo $input; ?>]" value="<?php echo $$input; ?>" />
	<?php
			endif;

		endforeach;

		// END: Widget options form
	}

	/*
	 * Save widget options
	 */
	public function update( $new_instance, $old_instance ) {

		$new_instance['items'] = intVal($new_instance['items']);
		$new_instance['title'] = trim(strip_tags( $new_instance['title'] ));
		$new_instance['small_logo'] = isset($new_instance['small_logo']) ? intVal($new_instance['small_logo']) : 0;
		$new_instance['show_title'] = isset($new_instance['show_title']) ? intVal($new_instance['show_title']) : 0;
		$new_instance['show_summary'] = isset($new_instance['show_summary']) ? intVal($new_instance['show_summary']) : 0;
		$new_instance['show_author'] = isset($new_instance['show_author']) ? intVal($new_instance['show_author']) :0;
		$new_instance['show_date'] = isset($new_instance['show_date']) ? intVal($new_instance['show_date']) : 0;
		$new_instance['link_back_to_aea'] = isset($new_instance['link_back_to_aea']) ? intVal($new_instance['link_back_to_aea']) : 0;

		if ( $new_instance['items'] < 1 || 20 < $new_instance['items'] ){
			$new_instance['items'] = 10;
		}

		$instance = wp_parse_args($new_instance, $old_instance);

		return $instance;

	}

	/*
	 * Public html for widget
	 */
	public function widget( $args, $instance ) {
		if ( isset($instance['error']) && $instance['error'] )
			return;

		extract($instance);
		extract($args, EXTR_SKIP);

		$url = "http://www.emergencyalert.alberta.ca/aeapublic/feed.atom";
		// $url = "http://practice.activatealert.alberta.ca/aeapractice/feed.atom"; // test feed

		// get the feed
		$rss = fetch_feed($url);

		$title = $instance['title'];
		$desc = '';
		$link = '';

		// if there was a problem getting the feed...
		if ( ! is_wp_error($rss) ) {

			// try to print out something meaningful
			$desc = esc_attr(strip_tags(html_entity_decode($rss->get_description(), ENT_QUOTES, get_option('blog_charset'))));
			if ( empty($title) ) {
				$title = esc_html(strip_tags($rss->get_title()));
			}
			$link = esc_url(strip_tags($rss->get_permalink()));
			while ( stristr($link, 'http') != $link ){
				$link = substr($link, 1);
			}
		}

		if ( empty($title) ){
			$title = empty($desc) ? __('Alberta Emergency Alert Feed') : $desc;
		}

		// allow any WP plugins to apply filters to the title
		$title = apply_filters('widget_title', $title, $instance, $this->id_base);
		$url = esc_url(strip_tags($url));

		// START: Print out widget
		echo $before_widget;

		// if the option was set to link to the AEA website
		if($link_back_to_aea) {
			echo '<a href="http://www.emergencyalert.alberta.ca/" target="_blank">';
		}

		// if the option was set to use the smaller logo
		if($small_logo){
			echo '<span class="aeaw-widget-aeaw-logo"><img src="'.plugins_url().'/alberta-emergency-alert-widget/images/aeaw-logo-small.png" /></span>';
		} else {
			echo '<span class="aeaw-widget-aeaw-logo"><img src="'.plugins_url().'/alberta-emergency-alert-widget/images/aeaw-logo.png" /></span>';
		}

		// if the option was set to link to the AEA website
		if($link_back_to_aea) {
			echo '</a>';
		}

		// title
		if($show_title){
			if ( $title )
				echo $before_title . $title . $after_title;
		}

		// print out actual alert items (if there are any)
		$this->print_alert_items( $rss, $instance );

		echo $after_widget;

		// cleanup
		if ( ! is_wp_error($rss) ) {
			$rss->__destruct();
		}
		unset($rss);
	}

	/*
	 * Parse RSS feed and print out items
	 */
	function print_alert_items( $rss, $args = array() ) {

		// double check that we're getting what we expect
		if ( is_array($rss) && isset($rss['url']) ) {
			$args = $rss;
			$rss = fetch_feed($rss['url']);
		} elseif ( !is_object($rss) ) {
			return;
		}

		// if $rss is a wordpress error, something went wrong...
		if ( is_wp_error($rss) ) {

			// if the person viewing the site is logged in and is an admin, print out an error for them
			if ( is_admin() || current_user_can('manage_options') )
				echo '<p>' . sprintf( __('<strong>RSS Error</strong>: %s'), $rss->get_error_message() ) . '</p>';
			return;
		}

		// default arguments
		$default_args = array( 'show_author' => 0, 'show_date' => 0, 'show_summary' => 0 );
		$args = wp_parse_args( $args, $default_args );
		extract( $args, EXTR_SKIP );

		// get item options ready
		$items = intVal($items);
		$show_summary  = intVal($show_summary);
		$show_author   = intVal($show_author);
		$show_date     = intVal($show_date);

		if ( $items < 1 || 20 < $items ){
			$items = 10;
		}

		// if there are no items in the feed...
		if ( !$rss->get_item_quantity() ) {
			echo '<ul><li class="no_alert">' . __( 'There are no alerts at this time.' ) . '</li></ul>';
			$rss->__destruct();
			unset($rss);
			return;
		}

		// there are items in the feed, so let's loop through them
		echo '<ul>';
		foreach ( $rss->get_items(0, $items) as $item ) {

			$link = $item->get_link();
			while ( stristr($link, 'http') != $link ) {
				$link = substr($link, 1);
			}
			$link = esc_url(strip_tags($link));
			$title = esc_attr(strip_tags($item->get_title()));
			$title_parts = explode(" - ", $title);
			if(sizeof($title_parts) >= 2) {
				$title = $title_parts[0]." - ".$title_parts[1];
			}
			// assign CSS classes based on the severity of the alert
			if(stristr($title, "CRITICAL ALERT") !== False){
				$item_class = "critical_alert";
			} elseif(stristr($title, "INFORMATION ALERT") !== False) {
				$item_class = "information_alert";
			} else {
				$item_class = "no_alert";
			}

			// if the title is empty, set it to something intelligent
			if ( empty($title) ) {
				$title = __('AEA Alert');
			}

			// remove any new lines
			$desc = str_replace( array("\n", "\r"), ' ', esc_attr( strip_tags( html_entity_decode( $item->get_description(), ENT_QUOTES, get_option('blog_charset') ) ) ) );

			// get an excerpt
			$desc = wp_html_excerpt( $desc, 360 );

			// replace silly language
			$desc = str_replace("This is an Alberta Emergency Alert. ", "", $desc);

			// break alert into array based on sentences
			$desc_array = explode(". ", $desc);

			// if there are more than two sentences, we'll only use the first two
			if(sizeof($desc_array) > 2) {
				$desc = $desc_array[0].". ".$desc_array[1].". ";
			}

			// reduce the verbage further
			$desc = str_replace(". This alert is in effect for: ", " for ", $desc);

			// escape any html
			$desc = esc_html( $desc );

			if ( $link != '' ) {
				$desc .= "<a href='" . $link . "'>Read more.</a> ";
			}

			if ( $show_summary ) {
				$summary = "<span class='aeaw-widget-summary'>" . $desc . "</span>";
			} else {
				$summary = '';
			}

			$date = '';
			if ( $show_date ) {
				$date = $item->get_date( 'U' );

				if ( $date ) {
					$date = ' <span class="aeaw-widget-date">' . date_i18n( get_option( 'date_format' ), $date ) . '</span>';
				}
			}

			$author = '';
			if ( $show_author ) {
				$author = $item->get_author();
				if ( is_object($author) ) {
					$author = $author->get_name();
					$author = ' <cite>' . esc_html( strip_tags( $author ) ) . '</cite>';
				}
			}

			if ( $link == '' ) {
				echo "<li class='$item_class'>$title{$date}{$summary}{$author}</li>";
			} else {
				echo "<li class='$item_class'><a class='aeaw-widget-link' href='$link'>$title</a>$date $summary {$author}</li>";
			}
		}
		echo '</ul>';
		$rss->__destruct();
		unset($rss);
	}


}


function aeaw_register_widgets() {
	register_widget( 'AEAW_Widget' );
}

add_action( 'widgets_init', 'aeaw_register_widgets' );

wp_enqueue_style('aeaw-style', plugins_url().'/alberta-emergency-alert-widget/css/style.css');

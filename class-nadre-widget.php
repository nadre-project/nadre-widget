<?php
/*
Plugin Name: NADRE Widget
Plugin URI: http://wordpress.org/extend/plugins/#
Description: NADRE plugin
Author: PI4
Version: 1.0.0
*/

/**
 * @package NADRE_Widget
 * @version 1.0
 */
class NADRE_Widget extends WP_Widget {

	/**
	 * Sets up the widgets name etc
	 *
	 * @link https://developer.wordpress.org/reference/classes/wp_widget/__construct/
	 * @see https://developer.wordpress.org/reference/functions/wp_register_sidebar_widget/
	 *
	 */
	function __construct() {
		$widget_ops = array(
			'classname'   => 'nadre_widget',
			'description' => 'NADRE Widget',
		);
		parent::__construct( 'nadre_widget', 'NADRE Widget', $widget_ops );
	}

	/**
	 * Outputs the content of the widget on front-end
	 *
	 * @param array $args Widget arguments
	 * @param array $instance
	 *
	 * @link https://developer.wordpress.org/reference/classes/wp_widget/widget/
	 */
	public function widget( $args, $instance ) { }

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 *
	 * @link https://developer.wordpress.org/reference/classes/wp_widget/form/
	 */
	public function form( $instance ) { }

	/**
	 * Processing widget options on save
	 *
	 * @param array $new_instance The new options
	 * @param array $old_instance The previous options
	 *
	 * @link https://developer.wordpress.org/reference/classes/wp_widget/update/
	 */
	public function update( $new_instance, $old_instance ) { }
}


// register NADRE_Widget
add_action( 'widgets_init', function() {
	register_widget( 'NADRE_Widget' );
} );

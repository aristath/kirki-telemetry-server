<?php
/**
 * Plugin Name:   Kirki Telemetry Server
 * Plugin URI:    http://aristath.github.io/kirki
 * Description:   Gathering anonymous data from sites using the Kirki plugin and providing some useful stats and insights.
 * Author:        Ari Stathopoulos
 * Author URI:    http://aristath.github.io
 * Version:       1.0
 * Text Domain:   kirki-ts
 *
 * GitHub Plugin URI: aristath/kirki-telemetry-server
 * GitHub Plugin URI: https://github.com/aristath/kirki-telemetry-server
 *
 * @package     Kirki Telemetry Server
 * @author      Ari Stathopoulos
 * @copyright   Copyright (c) 2019, Aristeides Stathopoulos
 * @license     https://opensource.org/licenses/GPL-2.0
 * @since       1.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once __DIR__ . '/inc/log.php';
require_once __DIR__ . '/inc/get-data.php';
new Kirki_Telemetry_Server\Log();

// phpcs:disable
class Kirki_Telemetry_Stats_Widget extends \WP_Widget {
	function __construct() {
		parent::__construct( false, 'Kirki Telemetry Stats' );
	}
	function widget( $args, $instance ) {
		include_once __DIR__ . '/inc/template.php';
	}
	function update( $new_instance, $old_instance ) {}
	function form( $instance ) {}
}
add_action( 'widgets_init', function() {
	register_widget( 'Kirki_Telemetry_Stats_Widget' );
} );
// phpcs:enable
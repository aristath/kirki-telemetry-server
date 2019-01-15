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

namespace Kirki_Telemetry_Server;

use Kirki_Telemetry_Server\Log;
use Kirki_Telemetry_Server\Draw;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once __DIR__ . '/inc/log.php';
require_once __DIR__ . '/inc/get-data.php';
new Log();

add_action(
	'wp',
	function() {
		if ( 'Kirki Telemetry Statistics' === get_the_title() ) {
			add_filter(
				'the_content',
				function() {
					\ob_start();
					include_once __DIR__ . '/inc/template.php';
					return \ob_get_clean();
				}
			);
		}
	}
);

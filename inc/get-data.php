<?php
/**
 * Get data.
 *
 * @package     Kirki Telemetry Server
 * @author      Ari Stathopoulos
 * @copyright   Copyright (c) 2019, Aristeides Stathopoulos
 * @license     https://opensource.org/licenses/GPL-2.0
 * @since       1.0
 */

namespace Kirki_Telemetry_Server;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Log data in the db.
 *
 * @since 1.0
 */
class Get_Data {

	/**
	 * Prefix for our settings.
	 *
	 * @access private
	 * @since 1.0
	 * @var string
	 */
	private $option_prefix = 'kirki_telemetry_data';

	/**
	 * The PHP version.
	 *
	 * @access private
	 * @since 1.0
	 * @var string
	 */
	private $php_version;

	/**
	 * The theme name.
	 *
	 * @access private
	 * @since 1.0
	 * @var string
	 */
	private $theme_name;

	/**
	 * The theme author.
	 *
	 * @access private
	 * @since 1.0
	 * @var string
	 */
	private $theme_author;

	/**
	 * The theme URI.
	 *
	 * @access private
	 * @since 1.0
	 * @var string
	 */
	private $theme_uri;

	/**
	 * The theme version.
	 *
	 * @access private
	 * @since 1.0
	 * @var string
	 */
	private $theme_version;

	/**
	 * Field-types.
	 *
	 * @access private
	 * @since 1.0
	 * @var array
	 */
	private $field_types;

	/**
	 * A single instance of this object.
	 *
	 * @static
	 * @access private
	 * @var Get_Data
	 */
	private static $instance;

	/**
	 * Constructor.
	 *
	 * @access private
	 * @since 1.0
	 */
	private function __construct() {
		$fields = [
			'php_version',
			'theme_name',
			'theme_author',
			'theme_uri',
			'theme_version',
			'field_types',
		];
		foreach ( $fields as $field ) {
			$this->$field = get_option( $this->option_prefix . '_' . $field, [] );
		}
	}

	/**
	 * Get a single instance of this object.
	 *
	 * @static
	 * @access public
	 * @since 1.0
	 * @return Get_Data
	 */
	public static function get_instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Gets the value of a setting for a field.
	 *
	 * @access public
	 * @since 1.0
	 * @param string $setting The setting we want to get.
	 * @return array
	 */
	public function get_settings( $setting ) {
		return $this->$setting;
	}
}

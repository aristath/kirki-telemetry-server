<?php
/**
 * Log data.
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
class Log {

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
	 * Field-types.
	 * 
	 * @access private
	 * @since 1.0
	 * @var array
	 */
	private $field_types;

	/**
	 * Constructor.
	 * 
	 * @access public
	 * @since 1.0
	 */
	public function __construct() {
		add_action( 'wp', [ $this, 'init' ] );
	}

	/**
	 * Things to do, places to see.
	 *
	 * @access public
	 * @since 1.0
	 * @return void
	 */
	public function init() {

		// Early exit if this is not a request we want to log.
		if ( ! isset( $_POST['action'] ) || 'kirki-stats' !== sanitize_text_field( wp_unslash( $_POST['action'] ) ) ) {
			return;
		}

		// Get data from request.
		$this->get_data_from_request();

		// Save data.
		$this->save_data();		
	}

	/**
	 * Get data from the request and set as object properties.
	 * 
	 * @access private
	 * @since 1.0
	 * @return void
	 */
	private function get_data_from_request() {

		$data_to_collect = [
			'siteID'        => 'site_id',
			'phpVer'        => 'php_version',
			'themeName'     => 'theme_name',
			'themeAuthor'   => 'theme_author',
			'themeURI'      => 'theme_uri',
			'theme_version' => 'theme_version',
		];

		foreach ( $data_to_collect as $key => $property ) {			
			if ( isset( $_POST[ $key ] ) ) {
				$this->$property = sanitize_text_field( wp_unslash( $_POST[ $key ] ) );
			}
		}

		// Set field_types.
		if ( isset( $_POST['fieldTypes'] ) ) {
			$field_types = wp_unslash( $_POST['fieldTypes'] );
			foreach ( $field_types as $type ) {
				$this->field_types[] = sanitize_text_field( $type );
			}
		}
	}

	/**
	 * Save data in our db.
	 * 
	 * @access private
	 * @since 1.0
	 * @return void
	 */
	private function save_data() {

		// We want to log data per-month.
		$date_key = date( 'Y' ) . date( 'm' );

		// Log data for PHP version and theme.
		foreach ( [ 'php_version', 'theme_name', 'theme_author', 'theme_uri' ] as $data_key ) {

			// Skip if no data defined.
			if ( ! $this->$data_key ) {
				continue;
			}

			$option_name = $this->option_prefix . '_' . $data_key;

			// Get existing data.
			$data = get_option( $option_name, [
				$date_key => [],
			] );

			// Make sure we have a value before increasing the counter.
			if ( ! isset( $data[ $date_key ][ $this->$data_key ] ) ) {
				$data[ $date_key ][ $this->$data_key ] = 0;
			}

			// Increase the count.
			$data[ $date_key ][ $this->$data_key ]++;

			// Save option.
			update_option( $option_name, $data );
		}

		// Log data for field-types.
		if ( $this->field_types && ! empty( $this->field_types ) && is_array( $this->field_types ) ) {
			$option_name = $this->option_prefix . '_field_types';

			// Get existing data.
			$data = get_option( $option_name, [
				'all'     => [
					$date_key => [],
				],
				'singles' => [
					$date_key => [],
				],
			] );

			$added_singles = [];
			foreach ( $this->field_types as $type ) {

				// Make sure we have a value before increasing the counter.
				if ( ! isset( $data['all'][ $date_key ][ $type ] ) ) {
					$data['all'][ $date_key ][ $type ] = 0;
				}
				if ( ! isset( $data['singles'][ $date_key ][ $type ] ) ) {
					$data['singles'][ $date_key ][ $type ] = 0;
				}

				// Increase the counts.
				$data['all'][ $date_key ][ $type ]++;
				if ( ! in_array( $type, $added_singles, true ) ) {
					$data['singles'][ $date_key ][ $type ]++;
					$added_singles[] = $type;
				}
			}

			// Save option.
			update_option( $option_name, $data );
		}
	}
}

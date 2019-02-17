<?php
/**
 * Template file for the charts.
 *
 * @package     Kirki Telemetry Server
 * @author      Ari Stathopoulos
 * @copyright   Copyright (c) 2019, Aristeides Stathopoulos
 * @license     https://opensource.org/licenses/GPL-2.0
 * @since       1.0
 */

namespace Kirki_Telemetry_Server;

use Kirki_Telemetry_Server\Get_Data;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Renders stats for a specific option.
 *
 * @since 1.0
 * @param array  $data_from_option The data we want to process.
 * @param string $option           Used as an ID.
 * @param string $label            The label for this graph.
 */
function kirki_telemetry_stats_lines( $data_from_option, $option, $label ) {

	$labels = array_keys( $data_from_option );

	// Get all options from the data.
	$all_options_from_data = [];
	foreach ( $data_from_option as $data ) {
		$all_options_from_data = array_unique( array_merge( $all_options_from_data, array_keys( $data ) ) );
	}
	sort( $all_options_from_data );

	// Datasets.
	$datasets = [];
	foreach ( $all_options_from_data as $choice ) {
		if ( 'fields_all' === $option || 'fields_single' === $option ) {
			if ( 0 === strpos( $choice, 'kirki-' ) ) {
				$choice = str_replace( 'kirki-', '', $choice );
			}
		}
		$dataset = [
			'label'       => $choice,
			'fill'        => false,
			'data'        => [],
			'borderColor' => '#' . str_pad( dechex( mt_rand( 0, 255 ) ), 2, '0', STR_PAD_LEFT ) . str_pad( dechex( mt_rand( 0, 255 ) ), 2, '0', STR_PAD_LEFT ) . str_pad( dechex( mt_rand( 0, 255 ) ), 2, '0', STR_PAD_LEFT ),
		];
		foreach ( $data_from_option as $data ) {
			$dataset['data'][] = ( isset( $data[ $choice ] ) ) ? $data[ $choice ] : 0;
		}
		$datasets[] = $dataset;
	}
	?>
	<canvas id="line-chart-<?php echo esc_attr( $option ); ?>"></canvas>
	<script>
	kirkiStatsDrawChartLines(
		document.getElementById( 'line-chart-<?php echo esc_attr( $option ); ?>' ),
		<?php echo wp_json_encode( $labels ); ?>,
		<?php echo wp_json_encode( $datasets ); ?>,
		'<?php echo $label; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>'
	);
	</script>

	<div style="background: rgba(0,0,0,.1);line-height:1.1;padding:1em;">
		<code style="font-size:12px;background:none;"><?php echo esc_html( wp_json_encode( $data_from_option ) ); ?></code>
	</div>
	<?php
}

?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.3/Chart.bundle.js" integrity="sha256-o8aByMEvaNTcBsw94EfRLbBrJBI+c3mjna/j4LrfyJ8=" crossorigin="anonymous"></script>
<script>
var kirkiStatsDrawChartLines = function( el, labels, datasets, text ) {
	new Chart( el, {
		type: 'line',
		data: {
			labels: labels,
			datasets: datasets
		},
		options: {
			title: {
				display: true,
				text: text
			},
			legend: {
				display: false
			}
		}
	} );
};
</script>

<div class="wrapper">
	<div>
		<p>Monthly statistics - PHP Versions.</p>
		<div><?php kirki_telemetry_stats_lines( Get_Data::get_instance()->get_settings( 'php_version' ), 'php_version', 'PHP Versions' ); ?></div>
	</div>
	<div>
		<p>Monthly statistics - Theme Name</p>
		<div><?php kirki_telemetry_stats_lines( Get_Data::get_instance()->get_settings( 'theme_name' ), 'theme_name', 'Theme Name' ); ?></div>
	</div>
	<div>
		<p>Monthly statistics - All field-types used. Duplicates are not removed from themes, so if a theme uses 10 color fields then all 10 will be registered. This helps us understand which fields are the most-used ones.</p>
		<div><?php kirki_telemetry_stats_lines( Get_Data::get_instance()->get_settings( 'field_types' )['all'], 'fields_all', 'Fields - All' ); ?></div>
	</div>
	<div>
		<p>Monthly statistics - All field-types used. Duplicates are removed, so if a theme uses 10 color fields then only 1 will be registered. This helps us understand which fields are necessary to most theme developers.</p>
		<div><?php kirki_telemetry_stats_lines( Get_Data::get_instance()->get_settings( 'field_types' )['singles'], 'fields_single', 'Fields - Single' ); ?></div>
	</div>
	<div><?php // kirki_telemetry_stats_lines( Get_Data::get_instance()->get_settings( 'theme_author' ), 'theme_author', 'Theme Author' ); ?></div>
	<div><?php // kirki_telemetry_stats_lines( Get_Data::get_instance()->get_settings( 'theme_uri' ), 'theme_uri', 'Theme URI' ); ?></div>
</div>

<style>
.wrapper {
	display: grid;
	grid-template-columns: repeat(auto-fit, minmax(700px, 1fr));
	grid-gap: 50px;
}
</style>

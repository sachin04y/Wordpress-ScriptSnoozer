<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

if ( ! class_exists( 'SZZZ_Action' ) ) {
	/**
	 *
	 * @class SZZZ_Action
	 */
	class SZZZ_Action {

		private $localized_data = [
			'styles'  => [],
			'scripts' => [],
		];

		function __construct() {

			$action = get_option( 'szzz_conf__action', true );

			( 'on' === $action ) && ( ! $this->options() ) && ( $this->handle_lazify() );

			add_action( 'lazify_data_on_done', 'SZZZ_Action::lazify_enqueue_scripts' );
		}

		public static function lazify_enqueue_scripts() {

			add_action( 'wp_print_footer_scripts', function() {

				require_once SZZZ_DIR . '/includes/lazify_script.php';
			} );
		}

		private function options() {

			$option_resources = get_option( 'szzz_conf__resource_set', array() );

			$parsed_resources   = array();
			$ordered_keys_index = [ 'handler', 'DOM_selector', 'observed_selector' ];
			foreach ( $option_resources as $type => $resource_string ) {

				preg_match_all( '/\[(.*?)\]/', $resource_string, $matches_set );

				foreach ( $matches_set[1] as $data_string ) {

					$opts_arr = explode( '|', trim( $data_string ) );
					$opts_set = [];

					foreach ( $opts_arr as $index => $data ) {
						( isset( $ordered_keys_index[ $index ] ) ) && ( $opts_set[ $ordered_keys_index[ $index ] ] = trim( $data ) );
					}

					$parsed_resources[ $type ][] = $opts_set;
				}

			}

			$this->options = (object) array(
				'monitoring' => 'on',
				'status'     => 'on',
				'resources'  => $parsed_resources,
			);
		}

		private function handle_lazify() {

			add_action( 'wp_enqueue_scripts', function() {

				global $wp_scripts;
				global $wp_styles;

				$lazify_resources     = array();

				$registered_resources = array(

					'styles'  => $wp_styles->registered,
					'scripts' => $wp_scripts->registered,
					'dequeue' => function( $type = null, $handler = null ) {

						if ( is_null( $type ) || empty( $type ) || is_null( $handler ) || empty( $handler ) ) {
							return false;
						}

						( 'styles' === $type ) && wp_dequeue_style( $handler ) || ( 'scripts' === $type ) && wp_dequeue_script( $handler );
					},
				);

				foreach ( $this->options->resources as $type => $data_set ) {

					foreach ( $data_set as $set ) {

						( isset( $registered_resources[ $type ][ $set['handler'] ] ) ) && ( $this->add_to_localize_bag( array_merge( $set, [ 'source' => $registered_resources[ $type ][ $set['handler'] ]->src ] ), $type ) );
						$registered_resources['dequeue']( $type, $set['handler'] );
					}
				}

				$this->localize_resources();

			}, 10000 );
		}

		private function add_to_localize_bag( $data = array(), $type = null ) {

			if ( is_null( $type ) ) {
				return false;
			}

			//Check source
			if ( ! filter_var( $data['source'], FILTER_VALIDATE_URL ) ) {
				$data['source'] = site_url( $data['source'] );
			}

			( isset( $this->localized_data[ $type ] ) ) && array_push( $this->localized_data[ $type ], $data );
		}

		private function localize_resources() {

			$prepared_data = apply_filters( 'lazify_data', $this->localized_data );

			do_action( 'lazify_data_on_ready', $prepared_data );
			// echo "<pre>";
			$sels = array_column( $prepared_data['styles'], 'DOM_selector' );
			$sels = array_merge( $sels, array_column( $prepared_data['scripts'], 'DOM_selector' ) );

			$inline_style = '';
			// foreach
			foreach( $sels as $sel ) {
				$s = preg_replace('/\.|\#|\s|\,|\+|\*|\~|\=|\>|\[|\]/', '_', $sel );
				$inline_style .= '<style type="text/css" id="szzzLazy_style_' . $s . '">';
				$inline_style .= $sel . '{visibility:hidden}';
				$inline_style .= '</style>' . PHP_EOL;
			}

			add_action( 'wp_head', function() use ( $prepared_data, $inline_style ) {

				$javascript_string = 'window.szzzLazifyData = ' . wp_json_encode( $prepared_data ) . ';';

				echo "<script type='text/javascript'>\n";
				echo "/* <![CDATA[ */\n";
				echo $javascript_string;  //xss ok.
				echo "\n/* ]]> */\n";
				echo "</script>\n";
				echo $inline_style;
			} );

			do_action( 'lazify_data_on_done', $prepared_data );
		}

	}

	add_action( 'init', function() {
		new SZZZ_Action();
	} );

}

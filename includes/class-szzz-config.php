<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

if ( ! class_exists( 'SZZZ_Config' ) ) {
	/**
	 *
	 * @class SZZZ_Config
	 */
	class SZZZ_Config {

		function __construct() {

			add_action( 'admin_init', array( $this, 'register_settings' ) );
			add_action( 'admin_menu', array( $this, 'setting_page' ) );
		}

		public function register_settings() {

			register_setting( 'szzz_config', 'szzz_conf__monitor' );
			register_setting( 'szzz_config', 'szzz_conf__action' );
			register_setting( 'szzz_config', 'szzz_conf__resource_set' );
		}

		public function setting_page() {

			add_options_page( 'Lazify Resources', 'Lazify Resources', 'manage_options', 'script_snoozer_settings', array( $this, 'setting_view' ) );
		}

		public function setting_view() {

			global $wp_scripts;
			?>
			<div class="wrap" id="lazify">
				<blockquote class="update-nag notice">
					<p>Do you know it's also important for SEO to have your images optimized?</p>
				</blockquote>
				<h2>Lazify Resources</h2>
				<p class="description">On-demand loading of resources.</p>
				<form name="form1" method="post" action="options.php">

					<?php settings_fields( 'szzz_config' ); ?>

					<table class="form-table">
						<tbody>
						<?php
						$monitoring = get_option( 'szzz_conf__monitor' );
						$action     = get_option( 'szzz_conf__action' );
						$resources  = get_option( 'szzz_conf__resource_set' );
						$styles     = isset( $resources['styles'] ) ? $resources['styles'] : false;
						$scripts    = isset( $resources['scripts'] ) ? $resources['scripts'] : false;
						?>
						<tr class="lazify_active">
							<th><label>Resource Monitor</label></th>
							<td>
								<label><input type="radio" name="szzz_conf__monitor" <?php checked( 'on', $monitoring ); ?> value="on">On</label>
								<label><input type="radio" <?php checked( 'off', $monitoring ); ?> name="szzz_conf__monitor" value="off">Off</label>
							</td>
						</tr>
						<tr class="lazify_active">
							<th><label for="resource-set__styles">Styles</label></th>
							<td>
								<textarea id="resource-set__styles" rows="5" cols="50" name="szzz_conf__resource_set[styles]"><?php echo $styles; ?></textarea>
							</td>
						</tr>
						<tr class="lazify_active">
							<th><label for="resource-set__scripts">Scripts</label></th>
							<td>
								<textarea id="resource-set__scripts" rows="5" cols="50" name="szzz_conf__resource_set[scripts]"><?php echo $scripts; ?></textarea>
							</td>
						</tr>
						<tr class="lazify_active">
							<th><label>Enable</label></th>
							<td>
								<label><input type="radio" name="szzz_conf__action" <?php checked( 'on', $action ); ?> value="on">On</label>
								<label><input type="radio" name="szzz_conf__action"  <?php checked( 'off', $action ); ?>  value="off">Off</label>
							</td>
						</tr>
						</tbody>
					</table>
					<p class="submit">
						<input type="submit" name="submit" class="button button-primary" value="<?php esc_attr_e( 'Save Changes' ); ?>" />
					</p>
				</form>
			</div>
			<?php
		}
	}

	new SZZZ_Config();
}

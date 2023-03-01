<?php
/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://tymber.me
 * @since      1.0.0
 *
 * @package    Wp_Tymber_Shop
 * @subpackage Wp_Tymber_Shop/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Wp_Tymber_Shop
 * @subpackage Wp_Tymber_Shop/includes
 * @author     Tymber <dev@tymber.me>
 */
class Wp_Tymber_Shop_I18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'wp-tymber-shop',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}

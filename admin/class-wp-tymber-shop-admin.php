<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://tymber.me
 * @since      1.0.0
 *
 * @package    Wp_Tymber_Shop
 * @subpackage Wp_Tymber_Shop/admin
 * @author     Tymber <dev@tymber.me>
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * @package    Wp_Tymber_Shop
 * @subpackage Wp_Tymber_Shop/admin
 * @author     Tymber <dev@tymber.me>
 * @since      1.0.0
 */
class Wp_Tymber_Shop_Admin
{

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of this plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version)
	{

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}


	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wp_Tymber_Shop_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wp_Tymber_Shop_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style($this->plugin_name, WP_TYMBER_SHOP_URL . '/admin/css/wp-tymber-shop-admin.css', array(), $this->version, 'all');
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wp_Tymber_Shop_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wp_Tymber_Shop_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_enqueue_media();

		wp_enqueue_script($this->plugin_name, WP_TYMBER_SHOP_URL . 'admin/js/wp-tymber-shop-admin.js?1', array('jquery', 'wp-util'), $this->version, true);
	}

	/**
	 * Loads the Carbon Fields
	 *
	 * @since 2.2.0
	 */
	function crb_load()
	{
		\Carbon_Fields\Carbon_Fields::boot();
	}

	/**
	 * Show admin notices
	 *
	 * @since 2.2.0
	 */
	function admin_notices()
	{
		$notice = Wp_Tymber_Shop_Notices::get_instance();
		$notices = $notice::get_notices();

		if (!empty($notices) && count($notices) > 0) {
			foreach ($notices as $warn) {
				$message = $warn['message'] ?? '';
				$type = $warn['type'] ?? 'error';

				if (!empty($message)) {
					echo '<div class="notice notice-' . $type . '"><p>' . $message . '</p></div>';
				}
			}
		} else {
			return;
		}
	}
}

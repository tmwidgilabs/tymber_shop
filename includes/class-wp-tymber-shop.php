<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://tymber.me
 * @since      1.0.0
 *
 * @package    Wp_Tymber_Shop
 * @subpackage Wp_Tymber_Shop/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Wp_Tymber_Shop
 * @subpackage Wp_Tymber_Shop/includes
 * @author     Tymber <dev@tymber.me>
 */
class Wp_Tymber_Shop
{

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Wp_Tymber_Shop_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct()
	{
		if (defined('WP_TYMBER_SHOP_VERSION')) {
			$this->version = WP_TYMBER_SHOP_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'wp-tymber-shop';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_routes_hooks();
		$this->define_public_hooks();
		$this->define_api_route();
	}

	public function sentry_run()
	{
		\Sentry\init(
			array(
				'dsn'         => 'https://d28f9c8db4444c438c24c68389eabd72@o378721.ingest.sentry.io/6364113',
				'environment' => wp_tymber_shop_get_env() ?? 'production',
			)
		);
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Wp_Tymber_Shop_Loader. Orchestrates the hooks of the plugin.
	 * - Wp_Tymber_Shop_I18n. Defines internationalization functionality.
	 * - Wp_Tymber_Shop_Admin. Defines all hooks for the admin area.
	 * - Wp_Tymber_Shop_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies()
	{

		$autoload = WP_TYMBER_SHOP_DIR . 'vendor/autoload.php';
		if (file_exists($autoload)) {
			require_once $autoload;
		}

		$this->sentry_run();

		/**
		 * The class responsible for the notice function using singleton pattern
		 */
		require_once WP_TYMBER_SHOP_DIR . 'includes/class-wp-tymber-shop-notices.php';

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once WP_TYMBER_SHOP_DIR . 'includes/class-wp-tymber-shop-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once WP_TYMBER_SHOP_DIR . 'includes/class-wp-tymber-shop-i18n.php';

		/**
		 * The class responsible for defining all routes for the plugin.
		 */
		require_once WP_TYMBER_SHOP_DIR . 'includes/class-wp-tymber-shop-routes.php';

		/**
		 * The class responsible for defining all routes for the plugin.
		 */
		require_once WP_TYMBER_SHOP_DIR . 'includes/class-wp-tymber-shop-utils.php';

		/**
		 * The class responsible for defining the server request.
		 */
		require_once WP_TYMBER_SHOP_DIR . 'includes/class-wp-tymber-shop-request.php';

		/**
		 * The class responsible for the api.
		 * */
		require_once WP_TYMBER_SHOP_DIR . 'includes/class-wp-tymber-shop-api.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once WP_TYMBER_SHOP_DIR . 'admin/class-wp-tymber-shop-admin.php';

		/**
		 * The class responsible for defining plugins setting
		 */
		require_once WP_TYMBER_SHOP_DIR . 'admin/class-wp-tymber-shop-settings.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once WP_TYMBER_SHOP_DIR . 'public/class-wp-tymber-shop-public.php';


		$this->loader = new Wp_Tymber_Shop_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Wp_Tymber_Shop_I18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale()
	{

		$plugin_i18n = new Wp_Tymber_Shop_I18n();

		$this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks()
	{

		$plugin_admin    = new Wp_Tymber_Shop_Admin($this->get_plugin_name(), $this->get_version());
		$plugin_settings = new Wp_Tymber_Shop_Settings($this->get_plugin_name(), $this->get_version());

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'after_setup_theme', $plugin_admin, 'crb_load' );
		$this->loader->add_action( 'carbon_fields_register_fields', $plugin_settings, 'plugin_menu' );
		$this->loader->add_action( 'admin_notices', $plugin_admin, 'admin_notices' );
		$this->loader->add_filter( 'carbon_fields_should_save_field_value', $plugin_settings, 'should_save_field', 10, 3 );

		// this will run when zip file is updated.
		$this->loader->add_action( 'add_option__tymber-zip_field', $plugin_settings, 'maybe_unzip', 10, 2 );
		$this->loader->add_action( 'update_option__tymber-zip_field', $plugin_settings, 'maybe_unzip', 10, 2 );
		$this->loader->add_filter( 'tymber_settings_sanitize_tymber_zip_file', $plugin_settings, 'sanitize_file' );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks()
	{
		$plugin_public = new Wp_Tymber_Shop_Public($this->get_plugin_name(), $this->get_version());
	}

	/**
	 * Register all of the hooks related to the routes functionality
	 * of the plugin.
	 *
	 * @since    2.0.0
	 * @access   private
	 */
	private function define_routes_hooks()
	{
		$plugin_routes = new Wp_Tymber_Shop_Routes();
		$this->loader->add_action('template_redirect', $plugin_routes, 'add_scripts_routes');
		$this->loader->add_action('template_redirect', $plugin_routes, 'add_resources_routes');
		$this->loader->add_action('template_redirect', $plugin_routes, 'add_sitemaps_routes');
	}

	/**
	 * Register all of the hooks related to the api functionality
	 *
	 * @since    3.0.0
	 * @access   public
	 */
	public function define_api_route()
	{
		$api = new WP_Tymber_Shop_Api;
		$this->loader->add_action('rest_api_init', $api, 'register_routes');
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run()
	{
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name()
	{
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Wp_Tymber_Shop_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader()
	{
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version()
	{
		return $this->version;
	}
}

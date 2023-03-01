<?php

/**
 * Tymber WordPress plugin that allows you to embed our shop on your website
 *
 * @link              https://tymber.me
 * @since             1.0.0
 * @package           Wp_Tymber_Shop
 *
 * @wordpress-plugin
 * Plugin Name:       WP Tymber Shop
 * Plugin URI:        https://tymber.me/shop/
 * Description:       Tymber Shop - advanced online shopping experience for cannabis pick-ups & deliveries...
 * Version:           3.2.0
 * Author:            Tymber
 * Author URI:        https://tymber.me
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wp-tymber-shop
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('WP_TYMBER_SHOP_VERSION', '3.2.0');

/**
 * The plugin slug.
 */
define('WP_TYMBER_SHOP_DIR', plugin_dir_path(__FILE__));
define('WP_TYMBER_SHOP_URL', plugin_dir_url(__FILE__));

/**
 * The Tymber URL API
 */
define('WP_TYMBER_SHOP_TYMBER_API', get_tymber_url());
define('WP_TYMBER_SHOP_TYMBER_URL', get_tymber_url('', true));

/**
 * Plugin Get plugin env
 *
 * @return string
 * @since 0.1.0
 */
function wp_tymber_shop_get_env()
{
	if (defined('WP_ENV') && !empty(WP_ENV)) {
		switch (WP_ENV) {
			case 'dev':
			case 'development':
			case 'staging':
				return 'development';
			case 'local':
				return 'local';
			case 'production':
			case 'prod':
			default:
				return 'production';
		}
	}

	return 'production';
}

/**
 * Exclude protect.txt if it exists more than two hours
 */
add_action(
	'init',
	function () {
		$protect = WP_TYMBER_SHOP_DIR . '/storage/protect.txt';
		if (file_exists($protect)) {
			$time = filemtime($protect) ?? 0;
			if (time() - $time > 2 * 3600) {
				unlink($protect);
			}
		}
	}
);

/**
 * Get Tymber URL
 */
function get_tymber_url($version = 'v1', $no_api = false)
{
	$url = 'https://tymber-wp-server.flywheelsites.com';

	if (function_exists('wp_tymber_shop_get_env')) {
		$wp_env = wp_tymber_shop_get_env();
		if ('development' === $wp_env) {
			$url = 'https://tymber.dev.widgilabs-sites.com';
		}
		if ('local' === $wp_env) {
			$url = 'http://tymberserver.local';
		}
	}

	if ($no_api) {
		return $url;
	}

	$namespace = '/wp-json/tymber/';
	$api       = $url . $namespace . $version;

	return $api;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wp-tymber-shop-activator.php
 */
function activate_wp_tymber_shop()
{
	require_once WP_TYMBER_SHOP_DIR . 'includes/class-wp-tymber-shop-activator.php';
	Wp_Tymber_Shop_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wp-tymber-shop-deactivator.php
 */
function deactivate_wp_tymber_shop()
{
	require_once WP_TYMBER_SHOP_DIR . 'includes/class-wp-tymber-shop-deactivator.php';
	Wp_Tymber_Shop_Deactivator::deactivate();
}

/**
 * The code that runs during plugin uninstall.
 * This action is documented in includes/class-wp-tymber-shop-deactivator.php
 */
function uninstall_wp_tymber_shop()
{
	$options = array(
		'_tymber-zip_active_version',
		'tymber-zip_active_version',
		'_tymber-api_token',
		'tymber-api_token',
		'_tymber-active_version_html',
		'tymber-active_version_html',
	);
	foreach ($options as $option) {
		delete_option($option);
	}
}

register_activation_hook(__FILE__, 'activate_wp_tymber_shop');
register_deactivation_hook(__FILE__, 'deactivate_wp_tymber_shop');
register_uninstall_hook(__FILE__, 'uninstall_wp_tymber_shop');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require WP_TYMBER_SHOP_DIR . 'includes/class-wp-tymber-shop.php';

/**
 * Better PHP error_log.
 *
 * @since    2.1.0
 */
function tymber_log( $log )
{
	\Sentry\configureScope( function ( \Sentry\State\Scope $scope ): void {
		$scope->setTag( 'client', site_url() );
		$scope->setLevel( \Sentry\Severity::warning() );
	});

	if (empty($log)) {
		return false;
	} elseif (is_array($log) || is_object($log)) {
		\Sentry\captureMessage('[TYMBER PLUGIN DEBUG] ' . print_r($log, true));
	} else {
		\Sentry\captureMessage('[TYMBER PLUGIN DEBUG] ' . $log);
	}
}

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_wp_tymber_shop()
{

	$plugin = new Wp_Tymber_Shop();
	$plugin->run();
}
run_wp_tymber_shop();

/**
 * Auto update plugin
 *
 * @since    3.2.0
 */
require WP_TYMBER_SHOP_DIR .  'plugin-update-checker/plugin-update-checker.php';
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

$update_checker = PucFactory::buildUpdateChecker(
	'https://github.com/tmwidgilabs/tymber_shop.git',
	__FILE__,
	'tymber-shop'
);

// Set the branch that contains the stable release.
$update_checker->setBranch( 'main' );

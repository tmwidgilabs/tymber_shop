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
class Wp_Tymber_Shop_Routes {

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
	public function __construct() {
		$this->plugin_name = 'wp-tymber-shop';
		$this->version     = WP_TYMBER_SHOP_VERSION;
	}

	/**
	 * Get and parse serve.json
	 *
	 * @since    2.0.0
	 */
	public static function get_serve_json() {
		$serve = WP_TYMBER_SHOP_DIR . 'public/app/serve.json';

		if ( file_exists( $serve ) ) {
			$serve = file_get_contents( $serve );
			$json  = json_decode( $serve, true );
			$json  = $json['rewrites'];
			if ( ! empty( $json ) && is_array( $json ) ) {
				$arr = null;
				foreach ( $json as $value ) {
					$value['source'] = explode( '/', $value['source'] );

					foreach ( $value['source'] as $key => $val ) {
						if ( '*' === $val ) {
							$val = ':val' . $key;
						}
						$value['source'][ $key ] = $val;
					}

					$value['source']         = implode( '/', $value['source'] );
					$arr[ $value['source'] ] = $value['destination'];
				}
				return array_unique( $arr );
			}
		}

		return false;
	}

	/**
	 * Add shop routes by serve.json
	 *
	 * @since    2.0.0
	 */
	public function add_shop_routes_by_serve() {
		$serve = self::get_serve_json();

		if ( ! empty( $serve ) && is_array( $serve ) ) {
			foreach ( $serve as $source => $destination ) {
				Routes::map(
					$source,
					function( $params ) use ( $source, $destination ) {
						$destination = WP_TYMBER_SHOP_DIR . 'public/app/' . $destination;
						if ( file_exists( $destination ) && strpos( $destination, '.xml' ) === false ) {
							tymber_log( 'Route Loaded: ' . $source . ' => ' . $destination );
							Routes::load( $destination );
						}

						if ( ! file_exists( $destination ) ) {
							tymber_log( 'Route Not Found: File doesn\'t exists ' . $source . ' => ' . $destination );
						}
					},
				);
			}
		}
	}

	/**
	 * Add shop routes to js and css
	 *
	 * @since    2.0.0
	 */
	public function add_scripts_routes() {
		global $wp;
		$req = $wp->request;
		if ( false !== strpos( $req, 'static' ) && false === strpos( $req, 'wp-content' ) ) {
			$shops = Wp_Tymber_Shop_Settings::get_shops();
			if ( ! empty( $shops ) && is_array( $shops ) ) {
				$link = substr( $req, strpos( $req, '/_next/' ) + 1 );
				foreach ( $shops as $shop => $path ) {
					if ( strpos( $req, $shop ) !== false ) {
						if ( file_exists( WP_TYMBER_SHOP_URL . 'public/app/' . $shop . '/' . $link ) ) {
							tymber_log( 'Redirecting Scripts: ' . $req . ' => ' . WP_TYMBER_SHOP_URL . 'public/app/' . $shop . '/' . $link );
							wp_safe_redirect( WP_TYMBER_SHOP_URL . 'public/app/' . $shop . '/' . $link, 301 );
							exit;
						} else {
							tymber_log( 'ERROR | Redirect Scripts: ' . WP_TYMBER_SHOP_URL . 'public/app/' . $shop . '/' . $link . ' doesn\'t exists.' );
						}
					}
				}
			}
		}
	}

	/**
	 * Add shop routes to resources
	 *
	 * @since    2.0.0
	 */
	public function add_resources_routes() {
		global $wp;
		$req = $wp->request;
		if ( ( false !== strpos( $req, 'png' ) || false !== strpos( $req, 'png' ) || false !== strpos( $req, 'ico' ) ) && false === strpos( $req, 'wp-content' ) ) {
			$shops = Wp_Tymber_Shop_Settings::get_shops();
			if ( ! empty( $shops ) && is_array( $shops ) ) {
				foreach ( $shops as $shop => $path ) {
					$link = substr( $req, strpos( $req, '/' . $shop . '/' ) + 1 );
					if ( strpos( $req, $shop ) !== false ) {
						if ( file_exists( WP_TYMBER_SHOP_DIR . 'public/app/' . $link ) ) {
							tymber_log( 'Redirect Resource: ' . $req . ' => ' . WP_TYMBER_SHOP_URL . 'public/app/' . $link );
							wp_safe_redirect( WP_TYMBER_SHOP_URL . 'public/app/' . $link, 301 );
							exit;
						} else {
							tymber_log( 'ERROR | Redirect Resource: file ' . WP_TYMBER_SHOP_URL . 'public/app/' . $link . ' doesn\'t exists.' );
						}
					}
				}
			}
		}
	}

	/**
	 * Add sitemap shop routes
	 *
	 * @since    2.0.0
	 */
	public function add_sitemaps_routes() {
		global $wp;
		$req = $wp->request;
		if ( false !== strpos( $req, '.xml' ) && false === strpos( $req, 'wp-content' ) ) {
			$serve = self::get_serve_json();
			foreach ( $serve as $source => $destination ) {
				if ( file_exists( WP_TYMBER_SHOP_DIR . 'public/app/' . $destination ) && false !== strpos( $req, $source ) ) {
					tymber_log( 'Redirect Sitemap: ' . $source . ' => ' . WP_TYMBER_SHOP_URL . 'public/app/' . $destination );
					wp_safe_redirect( WP_TYMBER_SHOP_URL . 'public/app/' . $destination, 301 );
					continue;
				}

				if ( ! file_exists( WP_TYMBER_SHOP_DIR . 'public/app/' . $destination ) ) {
					tymber_log( 'ERROR | Redirect Sitemap: File ' . WP_TYMBER_SHOP_URL . 'public/app/' . $destination . 'doesn\'t exist.' );
					continue;
				}
			}
		}
	}
}

<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://tymber.me
 * @since      1.0.0
 *
 * @package    Wp_Tymber_Shop
 * @subpackage Wp_Tymber_Shop/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Wp_Tymber_Shop
 * @subpackage Wp_Tymber_Shop/public
 * @author     Tymber <dev@tymber.me>
 */
class Wp_Tymber_Shop_Public {

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
	 * The Router Class.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $router;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of the plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
		$this->router      = new Wp_Tymber_Shop_Routes();

		/* Add Routes */
		$this->routes();
		// Rank Math SEO plugin
		// add_filter( 'rank_math/sitemap/index', array( $this, 'add_sitemap' ), 11 );

		// Yoast SEO Plugin
		add_filter( 'wpseo_sitemap_index', array( $this, 'add_sitemap' ), 11 );
	}

	/**
	 * Call Routes System.
	 *
	 * @since    1.0.0
	 */
	protected function routes() {
		$this->router->add_shop_routes_by_serve();
	}

	/**
	 * Call Routes System.
	 *
	 * @since    1.0.0
	 * @param    string $xml     sitemap.xml from plugin.
	 */
	public static function add_sitemap( $xml ) {
		$tymber_shops = Wp_Tymber_Shop_Settings::get_shops( 'option' );
		if ( ! empty( $tymber_shops ) ) {
			foreach ( $tymber_shops as $tymber_shop_name => $tymber_shop_path ) {
				$sitemap_file  = 'sitemap_index.xml';
				$file_path_url = WP_TYMBER_SHOP_URL . 'public/app/' . $tymber_shop_name . '/' . $sitemap_file;
				$file_path     = $tymber_shop_path . '/' . $sitemap_file;
				if ( file_exists( $file_path ) ) {
					$last_mod = filemtime( $file_path );
					$xml     .= sprintf(
						'<sitemap>
							<loc>%1$s</loc>
							<lastmod>%2$s</lastmod>
						</sitemap>',
						$file_path_url,
						gmdate( 'Y-m-d H:i:s', $last_mod ) . '+00:00',
					);
				} else {
					tymber_log( 'Sitemap file not found: ' . $file_path );
				}
			}
		}
		return $xml;
	}
}

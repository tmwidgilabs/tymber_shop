<?php

/**
 * The settings-specific functionality of the plugin.
 *
 * @link       https://tymber.me
 * @since      1.0.0
 *
 * @package    Wp_Tymber_Shop
 * @subpackage Wp_Tymber_Shop/settings
 * @author     Tymber <dev@tymber.me>
 */

use Carbon_Fields\Container;
use Carbon_Fields\Field;

/**
 * The settings-specific functionality of the plugin.
 *
 * @package    Wp_Tymber_Shop
 * @subpackage Wp_Tymber_Shop/settings
 * @author     Tymber <dev@tymber.me>
 * @since      1.0.0
 */
class Wp_Tymber_Shop_Settings extends Wp_Tymber_Shop_Request
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

	private $panel;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param    string $plugin_name       The name of this plugin.
	 * @param    string $version           The version of this plugin.
	 */
	public function __construct($plugin_name, $version)
	{
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	public function plugin_menu()
	{
		$api_token     = get_option( '_tymber-api_token' );
		$shop_version  = get_option( 'wp_tymber_shop_version' );
		$show_html     = '';
		$version_class = 'shop_version hide';

		if ( $api_token && $shop_version ) {
			$show_html = '<b>Version</b><br>' . $shop_version;
			$version_class = 'shop_version';
		}

		$this->panel = Container::make(
			'theme_options',
			__('Tymber', 'wp-tymber-shop')
		)->set_icon('data:image/svg+xml;base64,' . base64_encode('<svg width="76" height="74" viewBox="0 0 76 74" fill="none" xmlns="http://www.w3.org/2000/svg">"<path d="M41.882 37.6162C41.2063 39.5222 38.7286 40.3798 36.3635 39.5395C33.9811 38.6991 32.9761 36.4727 33.6519 34.5667C34.3276 32.6608 36.4415 31.9851 38.8152 32.8254C41.1976 33.6657 42.5578 35.7103 41.882 37.6162Z" fill="#417052"/>"<path d="M52.8931 18.9899C54.5911 20.1941 56.0639 21.6495 57.2421 23.3389C60.3089 27.6879 61.3398 33.1631 60.1443 38.7683C58.8968 44.6247 55.3881 49.8313 50.2508 53.4526C41.9167 59.3177 30.9143 59.7422 23.4811 54.4835C21.8351 53.314 20.397 51.8845 19.2534 50.2472C16.1953 45.9068 15.199 40.397 16.4205 34.7485C17.7027 28.8228 21.2547 23.5468 26.392 19.9255C30.5504 16.9887 35.3759 15.4033 40.3053 15.3166C44.9402 15.204 49.4018 16.5209 52.8931 18.9899ZM55.1109 15.4726C46.4996 9.38228 33.9031 9.32163 24.0702 16.2436C11.8636 24.8463 8.35497 41.1767 16.1866 52.2744C17.6854 54.3969 19.4873 56.1815 21.5319 57.6283C30.1865 63.7446 42.9216 63.6926 52.7978 56.736C65.0044 48.1333 68.3658 32.0195 60.5342 20.9045C59.0441 18.7647 57.1814 16.9367 55.1109 15.4726Z" fill="#417052"/>"<path d="M35.3239 25.6782C37.3598 25.6782 39.465 26.1807 41.3709 27.1163C44.429 28.6237 46.8721 31.1534 48.0676 34.047C48.6654 35.5284 49.5057 38.422 48.0936 41.2462C46.6295 44.2177 43.1902 46.0803 39.1011 46.0803C36.9959 46.0803 34.8214 45.5779 32.8635 44.5989C29.7794 43.0915 27.4316 40.6744 26.2621 37.7809C25.2311 35.2945 25.2571 32.7562 26.288 30.6336C27.8128 27.5928 31.2521 25.6782 35.3239 25.6782ZM35.8783 22.2129C30.637 22.2129 25.8029 24.8379 23.5591 29.4294C20.2757 36.0915 23.7064 44.4776 31.2088 48.1595C33.5826 49.3291 36.0949 49.8835 38.538 49.8835C43.7793 49.8835 48.6134 47.3019 50.8399 42.7709C54.1233 36.1089 50.5713 27.5928 43.0776 23.9109C40.7125 22.7673 38.2521 22.2129 35.8783 22.2129Z" fill="#417052"/>"<path d="M37.6283 5.0074C42.2892 5.0074 46.8461 5.98635 51.1431 7.9356C55.5874 9.93683 59.5465 12.8564 62.8992 16.6249C65.8621 20.0902 68.0539 23.9281 69.4054 27.9999C70.7222 31.9417 71.2247 36.0048 70.8868 40.0765C70.549 44.1483 69.4054 48.0814 67.4561 51.7633C65.4463 55.5925 62.6393 59.0405 59.1047 62.0467C56.2198 64.2645 53.023 65.9625 49.5924 67.1148C46.2137 68.2496 42.6964 68.8128 39.1271 68.8128C34.3623 68.8128 29.736 67.8078 25.3871 65.8326C20.8648 63.7794 16.897 60.7905 13.5616 56.944C7.95646 50.074 5.21019 41.8265 5.83395 33.735C6.14583 29.6892 7.28072 25.782 9.21264 22.1521C11.2399 18.3229 14.1074 14.8489 17.746 11.8428C23.5678 7.36382 30.4378 5.0074 37.6283 5.0074ZM37.3684 0C29.4068 0 21.4019 2.57301 14.5579 7.84897C-2.4222 21.8662 -3.23655 43.8018 9.72378 59.6383C17.4688 68.6482 28.1853 73.1704 38.8672 73.1704C46.9414 73.1704 55.007 70.5714 61.7817 65.3561C78.268 51.4515 79.8447 29.23 66.1566 13.2116C58.403 4.50493 47.9117 0 37.3684 0Z" fill="#417052"/></svg>'))
			->set_page_menu_title(__('Tymber Shop', 'wp-tymber-shop'))
			->set_page_file('tymber_shop')
			->add_tab(
				__('General', 'wp-tymber-shop'),
				array(
					Field::make('text', 'tymber-api_token', __('API Token', 'wp-tymber-shop'))
						->set_help_text( __( 'If you don\'t have an API key, please get in touch with our support team', 'wp-tymber-shop' ) ),
					Field::make('html', 'tymber-active_version_html', __('Section Description', 'wp-tymber-shop'))
						->set_html( $show_html )
						->set_label( false )
						->set_classes( $version_class ),
				)
			);
		$this->add_shops_to_panel();
	}

	public function should_save_field($save, $value, $field)
	{
		if ($save) {
			$field_name = $field->get_name();
			if ('_tymber-api_token' === $field_name || 'tymber-api_token' === $field_name) {
				if (!empty($value)) {
					$old_val = carbon_get_theme_option('tymber-api_token');
					if ($old_val !== $value) {
						$save     = false;
						$token    = $value;
						$api      = WP_TYMBER_SHOP_TYMBER_API . '/first-install';
						$response = wp_remote_post(
							$api,
							array(
								'method'      => 'POST',
								'timeout'     => 45,
								'redirection' => 5,
								'httpversion' => '1.0',
								'blocking'    => true,
								'headers'     => array(
									'Content-Type' => 'application/json',
								),
								'body'        => wp_json_encode(
									array(
										'token'    => $token,
										'api_link' => get_rest_url(),
									)
								),
							)
						);

						// Clean admin notices

						$body = json_decode(wp_remote_retrieve_body($response));

						if ( ! $body ) {
							tymber_log( $response->get_error_message() );

							if ( str_contains( $response->get_error_message() , 'Could not resolve host' ) ) {
								Wp_Tymber_Shop_Notices::get_instance()->add_notice(
									__( 'Could not connect with Tymber Server. Please get in touch with our support.', 'wp-tymber-shop' ),
									'error'
								);
							}
							return false;
						}

						$code = wp_remote_retrieve_response_code($response);

						if (!empty($body) && 200 === $code && true === (bool) $body->success) {
							$zip     = $body->data->download ?? '';
							$zip_id  = $body->data->id ?? '';
							$version = $body->data->version ?? '';
							if (!empty($zip)) {
								$download = $this->download($zip, $zip_id, $version);
								if (!is_wp_error($download) && false !== $download) {
									$unzip = $this->maybe_unzip($download);
									print_r($unzip);
									if (!is_wp_error($unzip) && false !== $unzip) {
										update_option('_tymber-zip_active_version', $version);
										$save = true;
									} else {
										if (is_wp_error($unzip)) {
											tymber_log('Problem with unzip: ' . $unzip->get_error_message() );
											Wp_Tymber_Shop_Notices::get_instance()->add_notice(
												__('Problem with unzip. Please get in touch with our support.', 'wp-tymber-shop'),
												'error'
											);
										} else {
											tymber_log('Problem with unzip: ' . print_r($unzip, true) );
											Wp_Tymber_Shop_Notices::get_instance()->add_notice(
												__('Problem with unzip. Please get in touch with our support.', 'wp-tymber-shop'),
												'error'
											);
										}
									}
								} else {
									if (is_wp_error($download)) {
										tymber_log('Problem with download: ' .  $download->get_error_message() );
										Wp_Tymber_Shop_Notices::get_instance()->add_notice(
											__('Problem with download. Please get in touch with our support.', 'wp-tymber-shop'),
											'error'
										);
									} else {
										tymber_log('Problem with download: ' . print_r($download, true) );
										Wp_Tymber_Shop_Notices::get_instance()->add_notice(
											__('Problem with download. Please get in touch with our support.', 'wp-tymber-shop'),
											'error'
										);
									}
								}
							} else {
								tymber_log('Problem with zip: ' . print_r($zip, true) );
								Wp_Tymber_Shop_Notices::get_instance()->add_notice(
									__('Problem with zip. Please get in touch with our support.', 'wp-tymber-shop'),
									'error'
								);
							}
						} else {
							tymber_log('Invalid API token: ' . print_r($body, true) );
							Wp_Tymber_Shop_Notices::get_instance()->add_notice(
								__('Invalid API token. Please get in touch with our support.', 'wp-tymber-shop'),
								'error'
							);
						}
					}
				}

				// Delete the following options from DB if the API token isn't valid
				if ( ! $value && get_option( 'wp_tymber_shop_version' ) ) {
					// Remove files and options
					Wp_Tymber_Shop_Utils::recursive_rmdir( WP_TYMBER_SHOP_DIR . 'storage' );
					Wp_Tymber_Shop_Utils::recursive_rmdir( WP_TYMBER_SHOP_DIR . 'public/app' );
					Wp_Tymber_Shop_Utils::rm_tymber_options();
				}
			}
		}

		return $save;
	}

	public function add_shops_to_panel()
	{
		$shops = self::get_shops('path');
		$token = self::verify_api_token('tymber-api_token');
		if (!empty($shops) && !empty($token)) {
			foreach ($shops as $shop_name => $shop_path) {
				$this->panel->add_tab(
					esc_html(ucwords($shop_name)),
					array(
						Field::make(
							'html',
							'html-' . $shop_name
						)->set_html(
							sprintf(
								'<h2>%s</h2>
								<a class="button button-primary button-large" href="%s" target="_blank">%s</a>',
								esc_html(ucwords($shop_name)),
								self::get_shops_index_url($shop_name),
								__('Go to the shop')
							)
						),
					)
				);
			}
		}
	}

	/**
	 * Function get the shop index url.
	 *
	 * @since    2.0.0
	 * @param    string $shop       Shop name.
	 */
	public static function get_shops_index_url($shop)
	{
		$serve = Wp_Tymber_Shop_Routes::get_serve_json();
		$index = $shop . '/index.html';

		foreach ($serve as $src => $dest) {
			if ($index === $dest) {
				return get_site_url(null, $src);
			}
		}

		return false;
	}

}

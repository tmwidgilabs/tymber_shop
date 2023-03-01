<?php

class Wp_Tymber_Shop_Request extends Wp_Tymber_Shop_Utils
{

	public function verify_api_token($option = 'tymber-api_token')
	{
		if (!function_exists('carbon_get_theme_option')) {
			tymber_log('Verify API Token Error - Carbon Fields Not found');
			return false;
		}

		$api_token = carbon_get_theme_option($option);
		$api_url   = WP_TYMBER_SHOP_TYMBER_API . '/verify-token';

		if (empty($api_token)) {
			tymber_log( 'No API token is defined.' );
			return false;
		}

		$response = wp_remote_post(
			$api_url,
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
						'token'    => $api_token,
						'site_url' => site_url( '/' ),
					)
				),
			)
		);

		$body = json_decode(wp_remote_retrieve_body($response));

		if ( ! $body ) {
			tymber_log( $response->get_error_message() );
			Wp_Tymber_Shop_Notices::get_instance()->add_notice(
				$response->get_error_message(),
				'error'
			);
			return false;
		}

		$code = wp_remote_retrieve_response_code($response);

		if (200 === $code && true === $body->success) {
			return true;
		} else {
			// Logs
			tymber_log( 'Something went wrong!! Tymber plugin couldn\'t connect to Tymber server.' );
			Wp_Tymber_Shop_Notices::get_instance()->add_notice(
				__( 'Something went wrong!! Tymber plugin couldn\'t connect to Tymber server. Please contact our support to help you.', 'wp-tymber-shop' ),
				'error'
			);

			// Remove files and options
			Wp_Tymber_Shop_Utils::recursive_rmdir( WP_TYMBER_SHOP_DIR . 'storage' );
			Wp_Tymber_Shop_Utils::recursive_rmdir( WP_TYMBER_SHOP_DIR . 'public/app' );
			Wp_Tymber_Shop_Utils::rm_tymber_options();
		}

		return false;
	}

	public function check_update()
	{
		$token    = carbon_get_theme_option('tymber-api_token');
		$version  = '1.0.0';
		$api_link = 'https://api.tymber.com/v1/';
		$args     = array(
			'headers'     => array('Content-Type' => 'application/json; charset=utf-8'),
			'body'        => wp_json_encode(
				array(
					'token'    => $token,
					'version'  => $version,
					'api_link' => $api_link,
				),
			),
			'method'      => 'POST',
			'data_format' => 'body',
		);

		$response = wp_remote_post(WP_TYMBER_SHOP_TYMBER_API . '/check-update', $args);
		$code 	 = wp_remote_retrieve_response_code($response);

		if (is_wp_error($response)) {
			tymber_log('Check Update Error - ' . $response->get_error_message());
			return $response;
		}

		if (200 !== $code) {
			tymber_log('Check Update Error - ' . $response->get_error_message());
			return new WP_Error('check_update_error', 'Check Update Error');
		}

		$response = json_decode(wp_remote_retrieve_body($response));
		$success  = $response['success'];
		$data     = $response['data'];

		if (!$success) {
			tymber_log( 'Invalid response: ' . print_r( $data, true ) );
			return new WP_Error('invalid_response', $data);
		}
		return $data;
	}
}

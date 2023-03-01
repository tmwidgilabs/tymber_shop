<?php

class WP_Tymber_Shop_Api extends Wp_Tymber_Shop_Request {
	private $namespace;

	public function __construct( $namespace = 'tymber/v1' ) {
		$this->namespace = $namespace;
	}

	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/edit-version',
			array(
				'methods'  => 'POST',
				'callback' => array( $this, 'edit_zip_version' ),
			)
		);

		register_rest_route(
			$this->namespace,
			'/update-version',
			array(
				'methods'  => 'POST',
				'callback' => array( $this, 'update_version' ),
			)
		);
	}

	public function edit_zip_version( $request ) {
		$token   = $request->get_param( 'token' ) ?? '';
		$version = ! empty( $request->get_param( 'version' ) ) ? json_decode( $request->get_param( 'version' ), true ) : '';
		$zip     = $request->get_param( 'zip' ) ?? '';
		$status  = $request->get_param( 'status' ) ?? '';

		if ( empty( $token ) ) {
			tymber_log( '`token` param missing' );
			wp_send_json_error( '`token` param missing', 403 );
		}
		if ( empty( $version ) ) {
			tymber_log( '`version` param missing' );
			wp_send_json_error( '`version` param missing', 403 );
		}
		if ( empty( $zip ) ) {
			tymber_log( '`zip` param missing' );
			wp_send_json_error( '`zip` param missing', 403 );
		}
		if ( function_exists( 'carbon_get_theme_option' ) ) {
			if ( carbon_get_theme_option( 'tymber-api_token' ) !== $token ) {
				tymber_log( 'Edit Zip Error - Mismatch Token (' . $token . ')' );
				wp_send_json_error( 'Invalid token', 403 );
			}

			$option = get_option( '_tymber-zip_active_versions' ) ?? array();
			if ( empty( $option ) && 'delete' !== $status ) {
				$status = 'new';
			}

			$result = array();
			switch ( $status ) {
				case 'delete':
					unset( $option[ $version['uuid'] ] );
					$result = $option;
					break;
				case 'edit':
					$option[ $version['uuid'] ] = array(
						'title'   => $version['title'],
						'version' => $version['version'],
						'zip'     => $zip,
						'updated' => gmdate( 'Y-m-d H:i:s' ),
					);

					$result = $option;
					break;
				case 'new':
					$new = array(
						$version['uuid'] => array(
							'title'   => $version['title'],
							'version' => $version['version'],
							'zip'     => $zip,
							'updated' => gmdate( 'Y-m-d H:i:s' ),
							'created' => gmdate( 'Y-m-d H:i:s' ),
						),
					);

					$result = array_merge( $option, $new );
					break;
			}

			if ( ! empty( $result ) ) {
				update_option( '_tymber-zip_active_versions', $result );
				wp_send_json_success();
			}

			tymber_log( 'Edit Zip Error - Invalid status' );
			wp_send_json_error( 'Invalid status', 403 );
		}

		tymber_log( 'Edit ZIP Not implemented for token: ' . $token );
		wp_send_json_error( 'Not implemented - carbon error', 501 );
	}

	public function update_version( $request ) {
		$token   = $request->get_param( 'token' ) ?? '';
		$version = $request->get_param( 'version' );
		$zip     = $request->get_param( 'zip' ) ?? '';

		if ( empty( $token ) ) {
			tymber_log( '`token` param missing' );
			wp_send_json_error( '`token` param missing', 403 );
		}
		if ( empty( $version ) ) {
			tymber_log( '`version` param missing' );
			wp_send_json_error( '`version` param missing', 403 );
		}
		if ( empty( $zip ) ) {
			tymber_log( '`zip` param missing' );
			wp_send_json_error( '`zip` param missing', 403 );
		}

		if ( ! function_exists( 'carbon_get_theme_option' ) ) {
			tymber_log( 'Update Version Not implemented - carbon error' );
			wp_send_json_error( 'Not implemented - carbon error', 501 );
		}

		if ( carbon_get_theme_option( 'tymber-api_token' ) !== $token ) {
			tymber_log( 'Invalid token: ' . $token );
			wp_send_json_error( 'Invalid Token', 403 );
		}

		if ( false === strpos( $zip, '.zip' ) ) {
			tymber_log( 'Update Version - Invalid ZIP file' );
			wp_send_json_error( 'Invalid Zip Link', 403 );
		}

		$version  = $request->get_param( 'version' );
		$uuid     = $version['uuid'];
		$ver      = $version['version'];
		$download = $this->download( $zip, $uuid, $ver );
		if ( ! empty( $download ) ) {
			$unzip = $this->maybe_unzip( $download );

			if ( is_wp_error( $unzip ) ) {
				tymber_log( 'Unzip Error - ' . $unzip->get_error_message() );
				wp_send_json_error( $unzip->get_error_message(), 500 );
			}

			update_option( '_tymber-zip_active_version', $ver );
			wp_send_json_success( 'Downloaded and unzipped', 200 );
		}

		tymber_log( 'Update Version - Download faile for token:' . $token );
		wp_send_json_error( 'Download failed', 500 );
	}
}

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
class Wp_Tymber_Shop_Utils
{
	public $dest_dir     = WP_TYMBER_SHOP_DIR . 'public/app';
	public $temp_dir     = WP_TYMBER_SHOP_DIR . 'storage/temp';
	public $temp_app_dir = WP_TYMBER_SHOP_DIR . 'storage/temp/app';
	public $bkp_dir      = WP_TYMBER_SHOP_DIR . 'storage/backup';
	public $protect_file = WP_TYMBER_SHOP_DIR . 'storage/protect.txt';

	public function download($url, $name = '', $version = '')
	{

		if (!empty($version)) {
			$our_version = get_option('wp_tymber_shop_version');
			if ($our_version !== $version) {
				update_option('wp_tymber_shop_version', $version);
			}
		}


		if (esc_url_raw( $url ) === $url) {
			$url = esc_url_raw($url);
		} else {
			$url = WP_TYMBER_SHOP_TYMBER_URL . $url;
		}

		if (!$url) {
			tymber_log( 'Invalid URL' );
			return new WP_Error('invalid_url', 'Invalid URL');
		}

		if (false === strpos($url, '.zip')) {
			tymber_log( 'File is not zip' );
			return new WP_Error('invalid_zip', 'File is not zip');
		}

		$exp    = explode('/', $url);
		$zip_id = end($exp);

		if (empty($name)) {
			$name = $zip_id;
		}

		if (strpos($name, '.zip')) {
			$name = str_replace('.zip', '', $name);
		}

		$temp = $this->temp_dir;

		if (!is_dir($temp)) {
			wp_mkdir_p($temp);
		}

		$zip = $temp . $name . '.zip';
		if (file_exists($zip)) {
			$this->recursive_rmdir($zip);
		}
		$response = copy($url, $zip);


		if (!file_exists($zip)) {
			tymber_log( 'Zip file not found' );
			return new WP_Error('zip_not_found', 'Zip file not found');
		}

		if (is_wp_error($response)) {
			return $response;
		}

		update_option('tymber_shop_zip_id', $zip_id);
		return $zip;
	}

	public function copy_r($temp, $dest)
	{
		if (is_dir($temp)) {
			@mkdir($dest);
			$objects = scandir($temp);
			if (sizeof($objects) > 0) {
				foreach ($objects as $file) {
					if ($file == "." || $file == "..") {
						continue;
					}
					// go on
					if (is_dir($temp . "/" . $file)) {
						$this->copy_r($temp . "/" . $file, $dest . "/" . $file);
					} else {
						copy($temp . "/" . $file, $dest . "/" . $file);
					}
				}
			}
			return true;
		} elseif (is_file($temp)) {
			return copy($temp, $dest);
		} else {
			return false;
		}
	}
	/**
	 * Function that will unzip the stores to public/app.
	 *
	 * @since    2.0.0
	 * @param    string $zip           New zip location.
	 */
	public function maybe_unzip($zip)
	{
		$protect = $this->protect_file;
		if (file_exists($protect)) {
			tymber_log( 'Another Installation in Progress' );
			return new WP_Error('zip_protected', 'Another Installation in Progress');
		}

		if (!empty($zip)) {
			if (file_exists($zip) && is_file($zip)) {
				fopen($protect, 'w');
				tymber_log('Upload System: zip uploaded => ' . $zip);

				require_once ABSPATH . '/wp-admin/includes/file.php';
				WP_Filesystem();

				$dest = $this->dest_dir;
				$temp = $this->temp_app_dir;
				$bkp  = $this->bkp_dir;

				if (is_dir($temp)) {
					$this->recursive_rmdir($temp);
				}

				wp_mkdir_p($temp);
				$unzip = unzip_file($zip, $temp);
				tymber_log('Upload System: Unzipping zip file => ' . $zip);

				if (is_wp_error($unzip)) {
					tymber_log('Upload System: Error unzipping zip file => ' . $zip);
					$this->recursive_rmdir($protect);
					return $unzip;
				}

				if (is_dir($dest)) {
					$backup = $this->backup_app($dest, $bkp);
					tymber_log('Upload System: Backup app folder => ' . $bkp);

					if (is_wp_error($backup)) {
						tymber_log('Upload System: Error backing up app folder => ' . $bkp);
						$this->recursive_rmdir($protect);
						return $backup;
					}

					$this->recursive_rmdir($dest);
				}

				wp_mkdir_p($dest);
				if ($this->copy_r($temp, $dest)) {
					$copy_tmp = $dest;
					$this->recursive_rmdir($temp);
				} else {
					$this->recursive_rmdir($protect);
					tymber_log('Error copying temporary -> Destiny files');
					return new WP_Error('copy_error', 'Error copying temporary -> Destiny files');
				}

				tymber_log('Upload System: Copying files to public/app => ' . print_r($copy_tmp, true));

				if (is_wp_error($copy_tmp)) {
					tymber_log('Upload System: Error copying files to public/app => ' . print_r($copy_tmp, true));
					$this->undo_unzip();
					$this->recursive_rmdir($protect);
					return new WP_Error('copy_error', 'Error copying files to public/app');
				}

				$shops = self::get_shops('path');
				$shops = $this->edit_shops($shops);

				if (is_wp_error($shops)) {
					tymber_log('Upload System: Error editing shops => ' . print_r($shops, true));
					$this->undo_unzip();
					$this->recursive_rmdir($protect);
					return $shops;
				}

				if (is_dir($temp)) {
					$this->recursive_rmdir($temp);
				}

				if (is_dir($this->temp_dir)) {
					$this->recursive_rmdir($this->temp_dir);
				}

				if (file_exists($protect)) {
					$this->recursive_rmdir($protect);
				}

				if (file_exists($zip)) {
					$this->recursive_rmdir($zip);
				}
				return true;
			}
			tymber_log('ERROR Upload System: Zip file not found => ' . $zip);
			return new WP_Error('zip_not_found', 'Zip file not found');
		}

		tymber_log('ERROR Upload System: Zip Upload Field is Empty');
		return new WP_Error('zip_upload_field_empty', 'Zip Upload Field is Empty');
	}

	public function backup_app()
	{
		$dest = $this->dest_dir;
		$bkp  = $this->bkp_dir;

		if (is_dir($dest)) {
			if (is_dir($bkp)) {
				$this->recursive_rmdir($bkp);
			}
			wp_mkdir_p($bkp);
			if ($this->copy_r($dest, $bkp)) {
				$copy_bkp = $bkp;
				$this->recursive_rmdir($dest);
			} else {
				tymber_log('Error copying Dest -> backup files');
				return new WP_Error('copy_error', 'Error copying Dest -> backup files');
			}
			tymber_log('Upload System: Copying old app to backup => ' . $copy_bkp);

			if (!$copy_bkp) {
				tymber_log('Error copying old app to backup');
				return new WP_Error('copy_bkp_error', 'Error copying old app to backup');
			}
			return true;
		}
		return false;
	}

	public function undo_unzip()
	{
		$protect = $this->protect_file;
		$dest    = $this->dest_dir;
		$temp    = $this->temp_app_dir;
		$bkp     = $this->bkp_dir;

		if (is_dir($dest)) {
			$this->recursive_rmdir($dest);
		}

		if (is_file($protect)) {
			$this->recursive_rmdir($protect);
		}

		$copy = copy_dir($bkp, $dest);

		if (is_wp_error($copy)) {
			return $copy;
		}

		$shops = self::get_shops('path');
		if (!empty($shops) && is_array($shops)) {
			$shops = $this->edit_shops($shops);
			if (is_wp_error($shops)) {
				return $shops;
			}

			return true;
		}

		return new WP_Error('undo_unzip_error', 'Undo unzip error');
	}

	public function edit_shops($shops)
	{
		if (is_array($shops) && !empty($shops)) {
			tymber_log('Upload System: Changing Shops Path in HTML');
			foreach ($shops as $shop => $path) {
				self::edit_shops_path($path, '/wp-content/plugins/wp-tymber-shop/public/app/' . $shop . '/');
			}
			update_option('tymber-shops', $shops);
			return $shops;
		}
		return new WP_Error('shops_path_error', 'Shops Edit Path Error');
	}

	/**
	 * Get Shops Name.
	 *
	 * @since    2.0.0
	 */
	public static function get_shops(string $option = 'option')
	{
		$result = false;
		switch ($option) {
			case 'option':
				$opt    = get_option('tymber-shops');
				$result = $opt;
				break;
			case 'path':
				$shops = WP_TYMBER_SHOP_DIR . 'public/app';
				if (is_dir($shops)) {
					$path = scandir($shops);
					if (is_array($path) && !empty($path)) {
						$result = array();
						foreach ($path as $dir) {
							if ('.' !== $dir && '..' !== $dir && is_dir($shops . '/' . $dir)) {
								$result[$dir] = $shops . '/' . $dir;
							}
						}
					}
				}
				break;
			default:
				$result = self::get_shops('option');
		}
		return $result;
	}

	/**
	 * Function that will change basename in html.
	 *
	 * @since    2.0.0
	 * @param    string $path       Path to search html.
	 * @param    string $change     Basename change.
	 */
	public static function edit_shops_path($path, $change)
	{
		$dirs  = new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS);
		$files = new RecursiveIteratorIterator($dirs);

		foreach ($files as $file) {
			if (pathinfo($file, PATHINFO_EXTENSION) === 'html') {
				$str = file_get_contents($file);
				$str = str_replace('TYMBER_SHOP_PATH', $change, $str);
				file_put_contents($file, $str);
				tymber_log('Upload System: Changing Shop Path "' . $change . '" for ' . $file);
			}
		}
	}

	/**
	 * Recursive remove directories.
	 *
	 * @since    2.0.0
	 * @param    string $dir       Directory to remove.
	 */
	public function recursive_rmdir($dir)
	{
		if (is_dir($dir)) {
			$objects = scandir($dir);
			foreach ($objects as $object) {
				if ('.' !== $object && '..' !== $object) {
					if (is_dir($dir . DIRECTORY_SEPARATOR . $object) && !is_link($dir . '/' . $object)) {
						$this->recursive_rmdir($dir . DIRECTORY_SEPARATOR . $object);
					} else {
						unlink($dir . DIRECTORY_SEPARATOR . $object);
					}
				}
			}
			rmdir($dir);
			return true;
		}

		if (is_file($dir)) {
			unlink($dir);
		}

		return false;
	}

	/**
	 * Remove all tymber plugin options
	 *
	 * @since    3.1.1
	 * @param    string $dir       Directory to remove.
	 */
	public function rm_tymber_options() {
		$tymber_options = array(
			'_tymber-zip_active_version',
			'wp_tymber_shop_version',
			'_tymber-api_token',
			'tymber-shops',
			'tymber_shop_zip_id'
		);

		foreach( $tymber_options as $option ) {
			delete_option( $option );
		}
	}
}

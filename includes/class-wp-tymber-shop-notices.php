<?php

class Wp_Tymber_Shop_Notices
{
	private static $instance;

	private function __construct()
	{
	}

	private function __clone()
	{
	}

	private function __wakeup()
	{
	}

	public static function get_instance()
	{
		if (self::$instance === null) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	public static function add_notice($message, $type = 'error')
	{
		$id = 1;
		$notices[] = array(
			'type'    => $type,
			'message' => $message,
		);
		set_transient( 'wp_tymber_shop_notices', $notices, 5 );
	}

	public static function get_notices()
	{
		$notices = get_transient( 'wp_tymber_shop_notices' );
		if ( ! $notices ) {
			$notices = array();
		}
		return $notices;
	}

}

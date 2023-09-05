<?php

namespace inc;

defined( 'ABSPATH' ) || exit();

class TKT_SMS {
	protected string $username;
	protected string $password;
	protected string $phone;
	protected string $message;
	protected string $code;

	public function __construct( $phone, $message, $code ) {
		$this->username = tkt_settings( 'sms-username' );
		$this->password = tkt_settings( 'sms-password' );
		$this->phone    = $phone;
		$this->message  = $message;
		$this->code     = $code;
	}
}
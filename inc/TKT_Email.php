<?php

namespace inc;

defined( 'ABSPATH' ) || exit();

class TKT_Email {

	public function __construct() {

		// Filters the name to associate with the “from” email address.
		add_filter( 'wp_mail_from_name', [ $this, 'from_name' ] );

		add_filter( 'wp_mail_from', [ $this, 'email_from' ] );
		add_filter( 'wp_mail_content_type', [ $this, 'content_type' ] );
	}

	private function from_name(): string {
		$name = tkt_settings( 'from-name' );

		return $name ?: get_bloginfo( 'name' );
	}

	private function email_from(): string {
		$email = tkt_settings( 'email-from' );

		return $email ?: get_bloginfo( 'admin_email' );

	}

	private function content_type(): string {
		return 'text/html';
	}

	public function send( $to, $subject, $text ): void {
		wp_mail( $to, $subject, $text );
	}
}
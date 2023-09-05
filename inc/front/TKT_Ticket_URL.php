<?php

namespace inc\front;

defined( 'ABSPATH' ) || exit();

class TKT_Ticket_URL {


	public static function all(): string {

		// Get My Account url (Dynamic)
		$myAccountId  = get_option( 'woocommerce_myaccount_page_id' );
		$myAccountUrl = get_permalink( $myAccountId );

		return wc_get_endpoint_url( 'tickets', '', $myAccountUrl );
	}

	public static function newTicket(  ): string {
		return add_query_arg(['action' => 'new-ticket'], self::all());
	}

	public static function single( $ticket_id ): string {
		return add_query_arg(['action' => 'single', 'ticket_id' => $ticket_id], self::all());
	}
}
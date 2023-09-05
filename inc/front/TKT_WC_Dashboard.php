<?php

namespace inc\front;

defined( 'ABSPATH' ) || exit();

class TKT_WC_Dashboard {

	public function __construct() {
		// add ticket menu
		add_filter( 'woocommerce_account_menu_items', [ $this, 'tickets_account_menu' ] );
		// add endpoint
		add_action( 'init', [ $this, 'register_tickets_endpoint' ] );
		// create exclusive page
		add_action( 'woocommerce_account_tickets_endpoint', [ $this, 'tickets_endpoint_page' ] );
	}

	public function tickets_account_menu( $menus ): array {

		$temp = $menus['customer-logout'] ?? null;
		unset( $menus['customer-logout'] );
		$menus['tickets'] = 'تیکت پشتیبانی';
		if ( $temp ) {
			$menus['customer-logout'] = $temp;
		}

		return $menus;
	}

	// Add tickets endpoint to wc
	public function register_tickets_endpoint(): void {
		add_rewrite_endpoint( 'tickets', EP_PAGES );
		//rewrite endpoints
		flush_rewrite_rules();
	}


	public function tickets_endpoint_page(): void {
		include_once $this->get_view();
	}

	private function get_view(): string {
		if ( isset( $_GET['action'] ) && $_GET['action'] === 'new-ticket' ) {
			return TKT_VIEW_DIR . 'front/new-ticket.php';
		}

		if ( isset( $_GET['action'] )) {
			if ($_GET['action'] === 'new-ticket' ){
				return TKT_VIEW_DIR . 'front/new-ticket.php';
			}
			if ($_GET['action'] === 'single' && $_GET['ticket_id']){
				return TKT_VIEW_DIR . 'front/single-ticket.php';
			}
		}

		return TKT_VIEW_DIR . 'front/tickets.php';
	}
}
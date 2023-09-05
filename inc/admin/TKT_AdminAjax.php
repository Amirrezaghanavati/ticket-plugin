<?php

namespace inc\admin;

use WP_User_Query;

defined( 'ABSPATH' ) || exit();


class TKT_AdminAjax {

	public function __construct() {
		// The dynamic portion of the hook name, $action, refers to the name of the Ajax action callback being fired.
		add_action( 'wp_ajax_tkt_search_user', [ $this, 'search_users_handler' ] );
	}

	public function search_users_handler(): void {
		$search = $_POST['term'];
		if ( ! $search ) {
			wp_send_json_error();
		}

		// Customize query with sanitize search
		$args       = [
			'search'         => '*' . esc_attr( $search ) . '*',
			'search_columns' => [ 'user_login', 'user_email', 'user_nickname' ]
		];
		$user_query = new WP_User_Query( $args );


		$users  = $user_query->get_results();
		$result = [];
		if ( $users ) {
			foreach ( $users as $user ) {
				$result[] = [ $user->ID, $user->user_login ];
			}
		}

		$this->make_response( $result );
	}


	public function make_response( $response ): void {

		if ( is_array( $response ) ) {
			wp_send_json( $response );
		} else {
			echo $response;
		}

		// everytime after json response
		wp_die();
	}

}
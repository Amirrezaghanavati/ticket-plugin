<?php

namespace inc;

use TKT_DepartmentManager;

defined( 'ABSPATH' ) || exit();

class TKT_Email_Notification {

	public function __construct() {
		add_action( 'tkt_submit_ticket', [ $this, 'submit_ticket' ] );
	}


	public function submit_ticket( $ticket_id ) {

		if ( ! tkt_settings( 'user-submit-email' ) ) {
			return null;
		}

		// Send Email
		$ticket = ( new TKT_TicketManager() )->get( $ticket_id );
		$email - $this->get_email( $ticket );
		$subject = 'تیکت شماره :' . $ticket->ID . 'با موفقیت ثبت شد';

		if ( is_email( $email ) ) {
			$text = $this->get_text( $ticket );
			( new TKT_Email() )->send( $email, $subject, $text );
		}
	}

	private function get_email( $ticket ): string {
		return get_userdata( $ticket->creator_id )->user_email;
	}

	private function get_text( $ticket ): string {
		$text       = tkt_settings( 'user-submit-email-text' );
		$department = ( new TKT_DepartmentManager() )->get_department( $ticket->ID );
		$search     = [
			'{{ticket_id}}',
			'{{title}}',
			'{{department}}',
			'{{status}}',
			'{{priority}}',
			'{{date}}',
		];

		$replace = [
			$ticket->ID,
			$ticket->title,
			$department->name,
			tkt_get_status_name( $ticket->status ),
			tkt_get_priority( $ticket->priority ),
			tkt_to_shamsi( $ticket->create_date )
		];

		return str_replace( $search, $replace, $text );
	}
}
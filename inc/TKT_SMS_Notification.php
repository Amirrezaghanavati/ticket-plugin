<?php

namespace inc;

use TKT_DepartmentManager;

defined( 'ABSPATH' ) || exit();

class TKT_SMS_Notification {


	public function __construct() {
		add_action( 'tkt_submit_ticket', [ $this, 'submit_ticket' ] );
	}

	public function submit_ticket( $ticket_id ) {
		if ( ! tkt_settings( 'user-submit-sms' ) ) {
			return null;
		}
		$this->send( $ticket_id );
	}

	public function send( $ticket_id ) {
		$ticket = ( new TKT_TicketManager() )->get( $ticket_id );
		$phone  = $this->get_phone( $ticket );
		if ( $phone ) {
			// Send sms
			$service = tkt_settings( 'sms-service' );
			$class   = 'TKT_' . ucfirst( $service );
			if ( ! $service || ! class_exists( $class ) ) {
				return null;
			}

			$message = $this->get_message( $ticket );

			( new $class( $phone, $message, tkt_settings( 'user-submit-sms-pattern-code' ) ) )->send();
		}

	}

	private function get_phone( $ticket ): string {
		return get_user_meta( $ticket->creator_id, tkt_settings( 'phone-meta-key' ), true );
	}

	private function get_message( $ticket ): array {
		$pattern       = tkt_settings( 'user-submit-sms-pattern' );
		$pattern       = explode( PHP_EOL, $pattern );
		$department    = ( new TKT_DepartmentManager() )->get_department( $ticket->ID );
		$pattern_array = [];
		foreach ( $patterns as $code ) {

			$code = trim( $code );

			if ( $code === '{{ticket_id}}' ) {
				$pattern_array['ticket_id'] = $ticket->ID;
			}

			if ( $code === '{{title}}' ) {
				$pattern_array['title'] = $ticket->title;
			}

			if ( $code === '{{department}}' ) {
				$pattern_array['department'] = $department->name;
			}

			if ( $code === '{{status}}' ) {
				$pattern_array['status'] = tkt_get_status_name( $ticket->status );
			}

			if ( $code === '{{priority}}' ) {
				$pattern_array['priority'] = tkt_get_priority( $ticket->priority );
			}

			if ( $code === '{{date}}' ) {
				$pattern_array['date'] = tkt_to_shamsi( $ticket->create_date );
			}
		}

		return $pattern_array;

	}
}
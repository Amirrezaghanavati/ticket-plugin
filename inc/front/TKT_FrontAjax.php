<?php

namespace inc\front;

use inc\TKT_ReplyManager;
use inc\TKT_TicketManager;
use inc\TKT_UploadManager;

defined( 'ABSPATH' ) || exit();

class TKT_FrontAjax {

	public function __construct() {
		// Fires authenticated Ajax actions for logged-in users
		add_action( 'wp_ajax_tkt_submit_ticket', [ $this, 'tkt_submit_ticket' ] );
		// Fires non-authenticated Ajax actions for logged-out users
		add_action( 'wp_ajax_nopriv_tkt_submit_ticket', [ $this, 'tkt_submit_ticket' ] );

		add_action( 'wp_ajax_tkt_submit_reply', [ $this, 'tkt_submit_reply' ] );
		add_action( 'wp_ajax_nopriv_tkt_submit_reply', [ $this, 'tkt_submit_reply' ] );
	}

	public function tkt_submit_ticket() {
		if ( ! wp_verify_nonce( $_POST['nonce'], 'tkt_ajax_nonce' ) ) {
			wp_send_json_error();
			exit();
		}

		// Upload file
		$file = $_FILES['file'];

		if ( $file ) {
			$uploadManager = new TKT_UploadManager( $file );
			$uploadResult  = $uploadManager->upload();
		}

		// Create ticket
		$ticket_data                  = [];
		$ticket_data['title']         = ! empty( $_POST['title'] ) ? $_POST['title'] : 'بدون عنوان';
		$ticket_data['body']          = $_POST['body'];
		$ticket_data['creator_id']    = get_current_user_id();
		$ticket_data['status']        = 'open';
		$ticket_data['priority']      = $_POST['priority'];
		$ticket_data['department_id'] = $_POST['child_department'];

		if ( $uploadResult ) { // Create ticket with file


			if ( $uploadResult['success'] === true ) { // Upload file success
				if ( isset( $uploadResult['url'] ) ) {
					$ticket_data['file'] = $uploadResult['url'];
					$ticketManager       = new TKT_TicketManager();
					$responseTicket      = $ticketManager->store( $ticket_data );
					if ( isset( $responseTicket['ticket_id'] ) ) {
						do_action( 'tkt_submit_ticket', $responseTicket['ticket_id']);
						$this->make_response( [ '__success' => true, 'result' => TKT_Ticket_URL::all() ] );
					}
				}
			} else { //Upload file not success
				$this->make_response( [ '__success' => false, 'result' => $uploadResult['message'] ] );
			}

		} else { // Create ticket without file
			$ticketManager  = new TKT_TicketManager();
			$responseTicket = $ticketManager->store( $ticket_data );
			if ( $responseTicket['ticket_id'] ) {
				do_action( 'tkt_submit_ticket', $responseTicket['ticket_id']);
				$this->make_response( [ '__success' => true, 'result' => TKT_Ticket_URL::all() ] );
			}
		}
		$this->make_response( [ '__success' => false, 'result' => $responseTicket ] );

	}

	public function tkt_submit_reply() {


		if ( ! wp_verify_nonce( $_POST['nonce'], 'tkt_ajax_nonce' ) ) {
			wp_send_json_error();
			exit();
		}


		$user_id   = get_current_user_id();
		$ticket_id = $_POST['ticket_id'];


		$ticket_manager = new TKT_TicketManager();
		$singleTicket   = $ticket_manager->find( $ticket_id );


		if ( ! $singleTicket || $singleTicket->status === 'finished' ) {

			$ticket->make_response( [ '__success' => false, 'result' => 'متاسفانه خطایی رخ داده است !' ] );
		}


		$reply_ticket = [
			'body'       => $_POST['body'],
			'creator_id' => $user_id
		];

		if ( isset( $_POST['status'] ) && ! empty( $_POST['status'] ) ) {
			$ticketStatus = $_POST['status'];
		} else {
			$ticketStatus = 'open';
		}

		// Update status ticket
		$ticket_manager->update_status( $ticket_id, $ticketStatus );
//		if ($ticketStatus === 'closed')


		// Check file exists
		$file = $_FILES['file'];

		$reply_manager = new TKT_ReplyManager( $ticket_id );
		if ( $file ) {
			$uploadManager = new TKT_UploadManager( $file );
			$uploadResult  = $uploadManager->upload();
		}
		if ( $uploadResult ) {

			if ( $uploadResult['success'] ) { // Upload file success
				if ( isset( $uploadResult['url'] ) ) {
					$reply_ticket['file'] = $uploadResult['url'];

					// Store reply ticket with file
					$responseTicketReply = $reply_manager->store( $reply_ticket );

					if ( isset( $responseTicketReply['reply_id'] ) ) {
						$ticket_manager->update_reply_date( $ticket_id );


						// make buffer
						$replies = $reply_manager->get();
						ob_start();
						include( TKT_VIEW_DIR . 'front/replies.php' );
						$replies_html_buffer = ob_get_clean();
						$this->make_response( [ '__success'    => true,
						                        'result'       => 'پاسخ شما با موفقیت ثبت شد!',
						                        'replies_html' => $replies_html_buffer,
						                        '__status'     => tkt_get_status_html( $ticketStatus )
						] );
					}
				}
			} else { //Upload file not success
				$this->make_response( [ '__success' => false, 'result' => $uploadResult['message'] ] );
			}
		} else {

			// Store without file
			$responseTicketReply = $reply_manager->store( $reply_ticket );

			if ( isset( $responseTicketReply['reply_id'] ) ) {
				$ticket_manager->update_reply_date( $ticket_id );
				// make buffer
				$replies = $reply_manager->get();
				ob_start();
				include( TKT_VIEW_DIR . 'front/replies.php' );
				$replies_html_buffer = ob_get_clean();
				$this->make_response( [ '__success'    => true,
				                        'result'       => 'پاسخ شما با موفقیت ثبت شد!',
				                        'replies_html' => $replies_html_buffer,
				                        '__status'     => tkt_get_status_html( $ticketStatus )
				] );
			}
		}
		// Error
		$this->make_response( [ '__success' => false, 'result' => $responseTicketReply ] );
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
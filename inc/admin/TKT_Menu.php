<?php

namespace inc\admin;

use BaseMenu;
use inc\TKT_ReplyManager;
use TKT_DepartmentManager;
use TKT_FlashMessage;


defined( 'ABSPATH' ) || exit();

class TKT_Menu extends BaseMenu {

	public ?object $ticketsObj = null;
	private object $wpdb;
	private string $table;
	private ?int $ticket_id = null;
	private string $reply_table;

	public function __construct() {

		global $wpdb;
		$this->wpdb        = $wpdb;
		$this->table       = $wpdb->prefix . 'tkt_tickets';
		$this->ticket_id   = $_GET['id'] ?? null;
		$this->reply_table = $wpdb->prefix . 'tkt_replies';

		$this->pageTitle    = __( 'Ticket Plugin', 'ticket-plugin' );
		$this->menuTitle    = __( 'Ticket Plugin', 'ticket-plugin' );
		$this->menuSlug     = 'support-tickets';
		$this->menuIcon     = '';
		$this->hasSubMenu   = true;
		$this->subMenuItems = [
			'tickets'     => [
				'pageTitle' => __( 'All Tickets', 'ticket-plugin' ),
				'menuTitle' => __( 'All Tickets', 'ticket-plugin' ),
				'menuSlug'  => 'tkt-tickets',
				'callback'  => 'ticketsPage',
				'load'      => [
					'status'   => true,
					'callback' => 'tickets_screen_option'
				]
			],
			'departments' => [
				'pageTitle' => __( 'All Departments', 'ticket-plugin' ),
				'menuTitle' => __( 'All Departments', 'ticket-plugin' ),
				'menuSlug'  => 'tkt-departments',
				'callback'  => 'departmentsPage',
				'load'      => [
					'status' => false
				]
			],
			'new-ticket'  => [
				'pageTitle' => __( 'Submit Ticket', 'ticket-plugin' ),
				'menuTitle' => __( 'Submit Ticket', 'ticket-plugin' ),
				'menuSlug'  => 'tkt-new-ticket',
				'callback'  => 'newTicketPage',
				'load'      => [
					'status' => false
				]
			],
			'edit-ticket' => [
				'pageTitle' => __( 'Edit Ticket', 'ticket-plugin' ),
				'menuTitle' => __( 'Edit Ticket', 'ticket-plugin' ),
				'menuSlug'  => 'tkt-edit-ticket',
				'callback'  => 'editTicketPage',
				'load'      => [
					'status' => false
				]
			],
			'settings'    => [
				'pageTitle' => __( 'Ticket Plugin', 'ticket-plugin' ),
				'menuTitle' => __( 'Settings', 'ticket-plugin' ),
				'menuSlug'  => 'tkt-settings',
				'callback'  => '',
				'load'      => [
					'status' => false
				]
			],

		];

		// Fire construct parent & provide to override
		parent::__construct();
	}

	// This callback function called to output the content for this page.
	public function page() {
		echo 'page';
	}

	// This callback function showed all tickets.
	public function ticketsPage(): void {

		include TKT_VIEW_DIR . 'admin/ticket/main.php';
	}

	public function tickets_screen_option(): void {
		// Add screen option
		$args = [
			'label'   => 'تعداد تیکت در هر صفحه',
			'default' => 20,
			'option'  => 'tickets_per_page', // Input id
		];
		// Register and configure an admin screen option
		add_screen_option( 'per_page', $args );

		// Create an object from tickets list
		$this->ticketsObj = new \inc\admin\TKT_TicketsList();


	}

	public function departmentsPage(): void {
		$manager = new TKT_DepartmentManager();
		$manager->index();
	}


	// Send new ticket
	public function newTicketPage(): void {
		$date    = $_POST;
		$is_edit = false;
		if ( isset( $date['publish'] ) ) {
			if ( ! isset( $date['ticket_nonce'] ) || ! wp_verify_nonce( $date['ticket_nonce'], 'ticket_security' ) ) {
				die( 'Sorry , nonce is not verify' );
			}
			$ids = $this->createTicket( $date );

			if ( $ids ) {
				foreach ( $ids as $id ) {
					TKT_FlashMessage::addMessage( 'تیکت شما با موفقیت ارسال شد' . ' ' . 'شماره تیکت : ' . $id );
				}
			}
		}
		include TKT_VIEW_DIR . 'admin/ticket/new-ticket.php';
	}

	public function editTicketPage(): void {
		$is_edit       = true;
		$reply_manager = new TKT_ReplyManager( $this->ticket_id );
		if ( isset( $_POST['publish'] ) ) {
			if ( ! isset( $_POST['ticket_nonce'] ) && ! wp_verify_nonce( $_POST['ticket_nonce'], 'ticket_security' ) ) {
				die( 'Sorry , nonce is not verify' );
			}

			// Update ticket
			$data   = $_POST;
			$update = $this->updateTicket( $data );

			// Insert new reply
			$is_insert = 0;
			if ( ! empty( $data['tkt-reply-content'] ) ) {
				$replyData = [
					'ticket_id'  => (int) $this->ticket_id,
					'creator_id' => get_current_user_id(),
					'from_admin' => 1,
					'body'       => stripslashes_deep( $data['tkt-reply-content'] ),
					'file'       => $data['reply-file'] ? sanitize_text_field( $data['reply-file'] ) : null,
				];

				$replyFormat = [ '%d', '%d', '%d', '%s', '%s' ];

				$is_insert = $this->replyStore( $replyData, $replyFormat );
				if ( $is_insert ) {
					$this->updateReplyDate();
				}
			}

			$replies = $reply_manager->get();

			// Update replies
			$is_update = 0;
			if ( $replies ) {
				foreach ( $replies as $reply ) {
					if ( ! empty( $data[ 'tkt-reply-content-' . $reply->ID ] ) ) {

						$updateReplyData = [
							'ID'   => $reply->ID,
							'body' => stripslashes_deep( $data[ 'tkt-reply-content-' . $reply->ID ] ),
							'file' => $data[ 'reply-file-' . $reply->ID ] ? sanitize_text_field( $data[ 'reply-file-' . $reply->ID ] ) : null,
						];


						if ( $this->replyUpdate( $updateReplyData ) ) {
							$is_update = 1;
						}
					}// Delete reply
					else if ( $reply_manager->delete( $reply->ID ) ) {
						$is_update = 1;
					}

				}
			}

			// Is update
			if ( $update || $is_insert || $is_update ) {
				TKT_FlashMessage::addMessage( 'تیکت با موفقیت آپدیت شد' . ' ' . 'شماره تیکت : ' . $this->ticket_id );
			}
		}

		$ticket = $this->getTicket();

		$replies = $reply_manager->get();
		include TKT_VIEW_DIR . 'admin/ticket/new-ticket.php';
	}

	public function createTicket( $data ): array {
		// Create Ticket
		$ids = [];
		if ( $data['user-id'] ) {
			foreach ( $data['user-id'] as $user_id ) {
				$insert = $this->wpdb->insert( $this->table, [
					'title'         => sanitize_text_field( $data['title'] ),
					'body'          => stripslashes_deep( $data['tkt-content'] ),
					'status'        => $data['status'],
					'priority'      => $data['priority'],
					'creator_id'    => get_current_user_id(),
					'user_id'       => $user_id,
					'from_admin'    => 1,
					'department_id' => $data['department-id'],
					'file'          => $data['file'] ? sanitize_text_field( $data['file'] ) : null,
				], [ '%s', '%s', '%s', '%s', '%d', '%d', '%d', '%d', '%s' ] );
				if ( $insert ) {
					$ids[] = $this->wpdb->insert_id;
				}
			}
		}

		return $ids;
	}

	public function getTicket() {
		if ( ! $this->ticket_id ) {
			return null;
		}

		return $this->wpdb->get_row( $this->wpdb->prepare( 'SELECT * FROM ' . $this->table . ' WHERE ID = %d', $this->ticket_id ) );

	}

	public function updateTicket( $updateData ) {
		return $this->wpdb->update( $this->table, [
			'title'         => sanitize_text_field( $updateData['title'] ),
			'body'          => stripslashes_deep( $updateData['tkt-content'] ),
			'status'        => $updateData['status'],
			'priority'      => $updateData['priority'],
			'creator_id'    => $updateData['creator-id'] ? (int) $updateData['creator-id'] : null,
			'user_id'       => $updateData['user-id'] ? (int) $updateData['user-id'] : null,
			'department_id' => $updateData['department-id'],
			'create_date'   => sanitize_text_field( tkt_to_base( $updateData['create-date'] ) ),
			'file'          => $updateData['file'] ? sanitize_text_field( $updateData['file'] ) : null,
		], [ 'ID' => $this->ticket_id ], [ '%s', '%s', '%s', '%s', '%d', '%d', '%d', '%s', '%s' ], [ '%d' ] );
	}

	public function replyStore( $replyData, $replyFormat ): int {
		$this->wpdb->insert( $this->reply_table, $replyData, $replyFormat );

		return $this->wpdb->insert_id;
	}

	public function updateReplyDate() {
		return $this->wpdb->query( $this->wpdb->prepare( 'UPDATE ' . $this->table . ' SET reply_date = NOW() WHERE ID = %d', $this->ticket_id ) );
	}

	public function replyUpdate( $replyUpdateData ) {

		return $this->wpdb->update( $this->reply_table,
			[ 'body' => $replyUpdateData['body'], 'file' => $replyUpdateData['file'] ],
			[ 'ID' => $replyUpdateData['ID'] ],
			[ '%s', '%s' ],
			[ '%d' ]
		);

	}


}
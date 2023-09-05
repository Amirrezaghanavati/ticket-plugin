<?php

namespace inc;

defined( 'ABSPATH' ) || exit();

class TKT_TicketManager {

	private $wpdb;
	private string $table;

	public function __construct() {
		global $wpdb;
		$this->wpdb  = $wpdb;
		$this->table = $wpdb->prefix . 'tkt_tickets';
	}

	public function store( $data ): array {

		$errors = [];
		if ( ! $data['department_id'] || $data['department_id'] === 'undefined' ) {
			$errors[] = 'لطفا نوع تیکت را انتخاب کنید !';
		}

		if ( empty( $data['body'] ) ) {
			$errors[] = 'لطفا محتوای تیکت را وارد نمایید !';
		}

		if ( $errors ) {
			return $errors;
		}

		$data = [
			'title'         => sanitize_text_field( $data['title'] ),
			'body'          => stripslashes_deep( $data['body'] ),
			'creator_id'    => $data['creator_id'] ?: null,
			'user_id'       => $data['user_id'] ?: null,
			'department_id' => $data['department_id'],
			'status'        => $data['status'],
			'priority'      => $data['priority'] ?: 'medium',
			'create_date'   => date( 'Y-m-d H:i:s' ),
			'reply_date'    => date( 'Y-m-d H:i:s' ),
			'file'          => $data['file'] ?: null
		];


		$this->wpdb->insert( $this->table, $data, [ '%s', '%s', '%d', '%d', '%d', '%s', '%s', '%s', '%s', '%s' ] );

		$insertID = $this->wpdb->insert_id;


		return [ 'ticket_id' => $insertID ];
	}

	// Get tickets
	public function get( $user_id, $type = null, $status = null, $orderBy = null, $page_num = null ): array {


		if ( ! (int) $user_id ) {
			return [];
		}


		$formats     = [];

		// Type filter
		switch ( $type ) {
			case 'sent':
				$queryFilter = ' creator_id = %d ';
				break;

			case 'received':
				$queryFilter = ' user_id = %d AND from_admin = 1 ';
				break;

			default :
				$queryFilter = ' user_id = %d OR creator_id = %d ';
				$formats[]   = $user_id;
				break;

		}
		$formats[] = $user_id;


		// Status filter
		switch ( $status ) {
			case null:
			case 'all':
				$queryFilter .= '';
				break;

			default:
				$queryFilter .= ' AND status = %s ';
				$formats[]   = $status;
				break;
		}


		// Order by filter
		if ( $orderBy === 'create-date' ) {
			$queryFilter .= ' ORDER BY create_date DESC';
		} else {
			$queryFilter .= ' ORDER BY reply_date DESC';
		}

		// Pagination
		if ( $page_num ) {
			// Paginate

			$per_page    = 5;
			$queryFilter .= ' LIMIT %d';
			$formats[]   = $per_page;

			if ( $page_num !== 1 ) {
				$offset      = ( $page_num - 1 ) * $per_page;
				$queryFilter .= ' OFFSET %d';
				$formats[]   = $offset;
			}
		}

		return $this->wpdb->get_results( $this->wpdb->prepare( 'SELECT * from ' . $this->table . ' WHERE ' . $queryFilter, $formats ) );

	}

	// Count of all tickets
	public function count( $user_id, $type = null, $status = null ): int {
		return count( $this->get( $user_id, $type, $status ) );
	}

	// Get one ticket
	public function find( $ticket_id ) {

		if ( ! (int) $ticket_id ) {
			return null;
		}

		return $this->wpdb->get_row( $this->wpdb->prepare( 'SELECT * from ' . $this->table . ' WHERE ID = %d ', $ticket_id ) );

	}


	public function update_status( $ID, $status ): bool {
		$data  = compact( 'status' );
		$where = compact( 'ID' ); // Ticket_id

		return $this->wpdb->update( $this->table, $data, $where, [ '%s' ], [ '%d' ] );
	}

	public function update_reply_date( $ticket_id ): bool {

		$date = date( "Y-m-d H:i:s" );

		return $this->wpdb->query( $this->wpdb->prepare( "UPDATE " . $this->table . " SET reply_date = '" . $date . "' WHERE ID = %d", $ticket_id ) );

	}

}
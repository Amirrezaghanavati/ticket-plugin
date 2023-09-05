<?php

namespace inc;

defined( 'ABSPATH' ) || exit();

class TKT_ReplyManager {

	private $wpdb;
	private string $table;
	private int $ticket_id;

	public function __construct( $ticket_id ) {
		global $wpdb;
		$this->wpdb      = $wpdb;
		$this->table     = $wpdb->prefix . 'tkt_replies';
		$this->ticket_id = $ticket_id;
	}

	public function store( $data ): array {

		$errors = [];

		if ( empty( $data['body'] ) ) {
			$errors[] = 'لطفا محتوای پاسخ را وارد نمایید !';
		}

		if ( $errors ) {
			return $errors;
		}


		$data = [
			'ticket_id'  => $this->ticket_id,
			'body'       => stripslashes_deep( $data['body'] ),
			'creator_id' => $data['creator_id'] ?: null,
			'file'       => $data['file'] ?: null
		];


		$this->wpdb->insert( $this->table, $data, [ '%d', '%s', '%d', '%s' ] );

		$insertID = $this->wpdb->insert_id;

		return [ 'reply_id' => $insertID ];
	}

	public function get() {
		return $this->wpdb->get_results( $this->wpdb->prepare( 'SELECT * FROM ' . $this->table . ' WHERE ticket_id = %d ORDER BY create_date DESC', $this->ticket_id ) );
	}

	public function delete( $reply_id ) {
		return $this->wpdb->delete( $this->table, [ 'ID' => $reply_id ], [ '%d' ] );
	}

	public function delete_replies( ) {
		return $this->wpdb->delete( $this->table, [ 'ticket_id' => $this->ticket_id ], [ '%d' ] );
	}
}
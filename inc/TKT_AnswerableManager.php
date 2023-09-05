<?php

namespace inc;

defined( 'ABSPATH' ) || exit();

class TKT_AnswerableManager {
	private $wpdb;
	private string $table;

	public function __construct() {
		global $wpdb;
		$this->wpdb  = $wpdb;
		$this->table = $wpdb->prefix . 'tkt_users';
	}

	public function store( $data ) {


		$fields = [
			'department_id' => sanitize_text_field($data['department_id']),
			'user_id'       => sanitize_text_field($data['user_id'])
		];
		$format = [ '%d', '%d' ];

		$this->wpdb->insert( $this->table, $fields, $format );
	}

	public function destroy( $department_id ): void {
		$where  = [ 'department_id' => $department_id ];
		$format = [ '%d' ];
		$this->wpdb->delete( $this->table, $where, $format );
	}


	public function get_by_department( $department_id ): array {
		return $this->wpdb->get_col( $this->wpdb->prepare( 'SELECT user_id from ' . $this->table . ' WHERE department_id = %d', $department_id ) );

	}

}
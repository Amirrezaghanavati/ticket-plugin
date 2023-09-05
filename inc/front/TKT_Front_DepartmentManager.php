<?php

namespace inc\front;

defined( 'ABSPATH' ) || exit();

class TKT_Front_DepartmentManager {

	private $wpdb;
	private string $table;

	public function __construct() {
		global $wpdb;
		$this->wpdb  = $wpdb;
		$this->table = $wpdb->prefix . 'tkt_departments';
	}


	public function get_parent_departments(): array {
		return $this->wpdb->get_results( 'SELECT * FROM ' . $this->table . ' WHERE parent = 0 ORDER BY position' );
	}

	public function get_child_departments( $parent_id ): array {
		return $this->wpdb->get_results( $this->wpdb->prepare( 'SELECT * FROM ' . $this->table . ' WHERE parent = %d ORDER BY position', $parent_id ) );
	}

	public function get_department( $id ) {
		return $this->wpdb->get_row( $this->wpdb->prepare( 'SELECT * from ' . $this->table . ' WHERE ID = %d', $id ) );
	}

}
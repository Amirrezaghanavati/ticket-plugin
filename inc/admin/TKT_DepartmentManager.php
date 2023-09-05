<?php

use inc\TKT_AnswerableManager;

defined( 'ABSPATH' ) || exit();


class TKT_DepartmentManager {

	private $wpdb;
	private string $table;

	public function __construct() {
		global $wpdb;
		$this->wpdb  = $wpdb;
		$this->table = $wpdb->prefix . 'tkt_departments';
	}

	// Show all Departments
	public function index(): void {
		$answerable_manager = new TKT_AnswerableManager();
		$departments        = $this->wpdb->get_results( 'SELECT * from ' . $this->table . ' ORDER BY position' );

		// Create department
		if ( isset( $_GET['action'] ) ) {
			if ( $_GET['action'] === 'create' ) {
				include_once TKT_VIEW_DIR . 'admin/department/create.php';
				if ( $_SERVER['REQUEST_METHOD'] === 'POST' && isset( $_POST['add_department_nonce'] ) ) {
					if ( ! wp_verify_nonce( $_POST['add_department_nonce'], 'add_department' ) ) {
						exit( 'Access denied !!! 403 !!!' );
					}
					$insert = $this->store( $_POST );

					if ( $insert ) {
						// Add user answerable
						if ( isset( $_POST['answerable'] ) ) {
							foreach ( $_POST['answerable'] as $user ) {
								$answerable_manager->store( [ 'department_id' => $insert, 'user_id' => $user ] );
							}
						}

						TKT_FlashMessage::addMessage( 'دپارتمان جدید با موفقیت ثبت شد' );
					} else {
						TKT_FlashMessage::addMessage( 'ثبت دپارتمان جدید با خطا مواجه شد', 1 );
					}

					wp_redirect( admin_url( 'admin.php?page=tkt-departments' ) );
					exit();
				}

				return;
			}

			// Delete Department
			if ( isset( $_GET['id'] ) ) {

				if ( $_GET['action'] === 'delete' ) {
					if ( isset( $_GET['delete_department_nonce'] ) && ! wp_verify_nonce( $_GET['delete_department_nonce'], 'delete_department' ) ) {
						exit( 'Access denied !!! 403 !!!' );
					}
					$delete = $this->destroy( $_GET['id'] );


					if ( $delete ) {

						// Delete user answerable
						$answerable_manager->destroy( $_GET['id'] );
						TKT_FlashMessage::addMessage( 'دپارتمان با موفقیت حذف شد' );

					} else {
						TKT_FlashMessage::addMessage( 'حذف دپارتمان با خطا مواجه شد', 1 );
					}

					wp_redirect( admin_url( 'admin.php?page=tkt-departments' ) );
					exit();
				}


				// edit Department
				if ( $_GET['action'] === 'edit' ) {

					// Get department from url
					$department = $this->get_department( $_GET['id'] );
					// Get user answerable
					$users_answerable = $answerable_manager->get_by_department( $department->ID );
					include_once TKT_VIEW_DIR . 'admin/department/edit.php';

					if ( $_SERVER['REQUEST_METHOD'] === 'POST' && isset( $_POST['update_department_nonce'] ) ) {
						if ( ! wp_verify_nonce( $_POST['update_department_nonce'], 'update_department' ) ) {
							exit( 'Access denied !!! 403 !!!' );
						}

						// Update action
						$flag = false;
						if ( isset( $_POST['answerable'] ) ) {
							$answerable_manager->destroy( $department->ID );
							foreach ( $_POST['answerable'] as $user ) {
								$answerable_manager->store( [
									'department_id' => $department->ID,
									'user_id'       => $user
								] );
							}
							$flag = true;
						}
						$update = $this->update( $_POST );
						if ( $update || $flag ) {
							TKT_FlashMessage::addMessage( 'دپارتمان با موفقیت ویرایش شد' );
						} else {
							TKT_FlashMessage::addMessage( 'ویرایش دپارتمان با خطا مواجه شد', 1 );
						}
						wp_redirect( admin_url( 'admin.php?page=tkt-departments' ) );
						exit();
					}

					return;
				}
			}
		}

		include_once TKT_VIEW_DIR . 'admin/department/index.php';
	}

	// Queries to DB:

	private function store( $data ): ?int {

		if ( $data['name'] ) {
			$data   = [
				'name'        => sanitize_text_field( $data['name'] ),
				'parent'      => $data['parent'] ? (int) $data['parent'] : 0,
				'position'    => $data['position'] ? (int) $data['position'] : 0,
				'description' => $data['description'] ? sanitize_textarea_field( $data['description'] ) : null
			];
			$format = [ '%s', '%d', '%d', '%s' ];
			$insert = $this->wpdb->insert( $this->table, $data, $format );

			return $insert ? $this->wpdb->insert_id : null;
		}

		return null;
	}

	private function destroy( $id ) {
		$where  = [
			'ID' => (int) $id
		];
		$format = [ '%d' ];

		return $this->wpdb->delete( $this->table, $where, $format );
	}

	public function update( $data ) {

		if ( ! empty( $data['name'] ) ) {
			$fields = [
				'name'        => sanitize_text_field( $data['name'] ),
				'parent'      => $data['parent'] ? (int) $data['parent'] : 0,
				'position'    => $data['position'] ? (int) $data['position'] : 0,
				'description' => $data['description'] ? sanitize_textarea_field( $data['description'] ) : null
			];

			$where       = [
				'ID' => (int) $data['department_id']
			];
			$format      = [ '%s', '%d', '%d', '%s' ];
			$whereFormat = [ '%d' ];

			return $this->wpdb->update( $this->table, $fields, $where, $format, $whereFormat );

		}

		return false;
	}

	// get parent Department
	public function get_department( $id ) {
		return $this->wpdb->get_row( $this->wpdb->prepare( 'SELECT * from ' . $this->table . ' WHERE ID = %d', $id ) );
	}

	public function get_parent_departments(): array {
		return $this->wpdb->get_results( 'SELECT * FROM ' . $this->table . ' WHERE parent = 0 ORDER BY position' );
	}

	public function get_child_departments( $parent_id ): array {
		return $this->wpdb->get_results( $this->wpdb->prepare( 'SELECT * FROM ' . $this->table . ' WHERE parent = %d ORDER BY position', $parent_id ) );
	}


}
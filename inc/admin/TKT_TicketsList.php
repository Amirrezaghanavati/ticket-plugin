<?php

namespace inc\admin;

use inc\TKT_ReplyManager;
use TKT_FlashMessage;
use WP_List_Table;

defined( 'ABSPATH' ) || exit();

class TKT_TicketsList extends WP_List_Table {

	private $wpdb;
	private string $table;
	private array $statuses;

	public function __construct() {

		parent::__construct( [
			'singular' => 'ticket',
			'plural'   => 'tickets',
		] );

		global $wpdb;
		$this->wpdb  = $wpdb;
		$this->table = $wpdb->prefix . 'tkt_tickets';

		$this->statuses = tkt_get_status();

	}

	public function get_columns(): array {

		return [
			'cb'            => '<input type="checkbox" />',
			'title'         => 'عنوان',
			'department_id' => 'دپارتمان',
			'creator_id'    => 'ایجاد کننده',
			'status'        => 'وضعیت',
			'priority'      => 'اهمیت',
			'create_date'   => 'تاریخ ایجاد',
			'reply_date'    => 'آخرین پاسخ',
		];
	}

	// Get all tickets
	private function get_tickets( $params = null ) {

		if ( ! $params ) {
			$params = $_GET;
		}
		$filterQuery = ' WHERE 1=1';
		$args        = [];

		if ( isset( $params['department-id'] ) && $params['department-id'] !== '' ) {
			$filterQuery .= ' AND department_id = %d';
			$args[]      = $params['department-id'];
		}

		if ( isset( $params['priority'] ) && $params['priority'] !== '' ) {
			$filterQuery .= ' AND priority = %s';
			$args[]      = $params['priority'];
		}

		if ( isset( $params['status'] ) && $params['status'] !== '' && $params['status'] !== 'all' ) {
			$filterQuery .= ' AND status = %s';
			$args[]      = $params['status'];
		}

		if ( isset( $params['creator-id'] ) && $params['creator-id'] !== '' ) {
			$filterQuery .= ' AND creator_id = %d';
			$args[]      = $params['creator-id'];
		}

		if ( isset( $params['search'] ) && $params['search'] !== '' ) {
			$filterQuery .= " AND title LIKE '%" . $params['search'] . "%'";
		}

		if ( isset( $params['orderby'] ) ) {
			switch ( $params['orderby'] ) {
				case 'create_date':
					$filterQuery .= ' ORDER BY create_date ' . $params['order'];
					break;
				case 'reply_date':
					$filterQuery .= ' ORDER BY reply_date ' . $params['order'];
					break;
				default:
					$filterQuery .= ' ORDER BY reply_date DESC';

			}
		}


		return $this->wpdb->get_results( $this->wpdb->prepare( 'SELECT * FROM ' . $this->table . $filterQuery, $args ), ARRAY_A );
	}

	public function get_ticket_count( $params = null ): int {
		return count( $this->get_tickets( $params ) );
	}

	public function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'department_id':
				return '<a href="admin.php?page=tkt-tickets&department-id=' . $item[ $column_name ] . '">' . get_department_name_html( $item[ $column_name ] ) . '</a>';
				break;

			case 'status':
				return tkt_get_status_html( $item[ $column_name ] );
				break;

			case 'priority':
				return '<a href="admin.php?page=tkt-tickets&priority=' . $item[ $column_name ] . '" class="tkt-priority tkt-priority-' . $item[ $column_name ] . '">' . tkt_get_priority_name( $item[ $column_name ] ) . '</a>';
				break;

			case 'reply_date':
			case 'create_date':
				return tkt_to_shamsi( strtotime( $item[ $column_name ] ) );
				break;
		}
	}

	public function column_cb( $item ) {
		return sprintf( '<input type="checkbox" name="id[]" value="%s">', $item['ID'] );
	}

	public function column_title( $item ): string {
		$title   = '<strong>' . $item['title'] . '</strong>';
		$actions = [
			'id'   => sprintf( '<spna>' . 'آیدی' . ': %d </spna>', absint( $item['ID'] ) ),
			'edit' => sprintf( '<a href="?page=tkt-edit-ticket&id=%s">' . 'ویرایش' . '</a>', absint( $item['ID'] ) ),
		];

		if ( isset( $_GET['status'] ) && $_GET['status'] === 'trash' ) {
			$actions['delete'] = sprintf( '<a href="?page=tkt-tickets&action=delete&id=%s&_nonce=%s">' . 'پاک کردن برای همیشه' . '</a>', absint( $item['ID'] ), wp_create_nonce( 'tkt_delete_ticket' ) );

		} else {
			$actions['trash'] = sprintf( '<a href="?page=tkt-tickets&action=trash&id=%s&_nonce=%s">' . 'زباله دان' . '</a>', absint( $item['ID'] ), wp_create_nonce( 'tkt_trash_ticket' ) );
		}

		return $title . $this->row_actions( $actions );
	}

	public function column_creator_id( $item ): string {
		$user_data = get_userdata( $item['creator_id'] );
		$actions   = [
			'profile' => '<a href="' . get_edit_user_link( $item['creator_id'] ) . '" target="_blank">پروفایل</a>'
		];
		$creator   = '<a href="admin.php?page=tkt-tickets&department-id=' . $item['creator_id'] . '"> ' . $user_data->display_name . '</a>';

		return $creator . $this->row_actions( $actions );
	}

	// Sort columns
	protected function get_sortable_columns(): array {
		return [
			'create_date' => [ 'create_date', false ],
			'reply_date'  => [ 'reply_date', false ],
		];
	}

	public function bulk_action() {
		$action = $this->current_action();
		$action = str_replace( 'bulk-', '', $action );
		$ids    = $_POST['id'] ?? [];
		if ( $ids ) {
			foreach ( $ids as $id ) {
				if ( $action === 'delete' ) {
					// Delete tickets
					try {
						$this->destroyTicket( $id );
						( new TKT_ReplyManager( $id ) )->delete_replies();
					} catch ( \Exception $exception ) {
						echo $exception->getMessage();
					}
				} else {
					// Update tickets
					$this->updateTicketStatus( $action, $id );

				}
			}
			TKT_FlashMessage::addMessage( 'یک عملیات با موفقیت انجام شد' );
		}

	}

	public function trashTicket(): void {
		if ( isset( $_GET['action'], $_GET['id'], $_GET['_nonce'] ) && $_GET['action'] === 'trash' ) {
			if ( ! wp_verify_nonce( $_GET['_nonce'], 'tkt_trash_ticket' ) ) {
				wp_die( 'Sorry your nonce is not correct' );
			}

			$is_update = $this->updateTicketStatus( 'trash', $_GET['id'] );
			if ( $is_update ) {
				TKT_FlashMessage::addMessage( 'تیکت با موفقیت به زباله دان منتقل شد' );
			}
		}
	}

	public function deleteAction(): void {
		if ( isset( $_GET['action'], $_GET['id'], $_GET['_nonce'] ) && $_GET['action'] === 'delete' ) {
			if ( ! wp_verify_nonce( $_GET['_nonce'], 'tkt_delete_ticket' ) ) {
				wp_die( 'Sorry your nonce is not correct' );
			}

			$is_delete = $this->destroyTicket( $_GET['id'] );
			if ( $is_delete ) {
				( new TKT_ReplyManager( $_GET['id'] ) )->delete_replies();
				TKT_FlashMessage::addMessage( 'تیکت با موفقیت حذف شد' );

			}
		}
	}

	public function destroyTicket( $id ) {
		$id = (int) $id;

		return $this->wpdb->delete( $this->table, [ 'ID' => $id ], [ '%d' ] );
	}

	public function updateTicketStatus( $status, $id ) {
		$id = (int) $id;

		return $this->wpdb->update( $this->table, [ 'status' => $status ], [ 'ID' => $id ], [ '%s' ], [ '%d' ] );
	}

	// To show bulk action dropdown
	public function get_bulk_actions(): array {
		$actions = [];
		foreach ( $this->statuses as $status ) {
			$actions[ 'bulk-' . $status['slug'] ] = $status['name'];
		}

		if ( isset( $_GET['status'] ) && $_GET['status'] === 'trash' ) {
			unset( $actions['bulk-trash'] );
			$actions['bulk-delete'] = 'حذف';
		}

		return $actions;
	}

	// Base class for displaying a list of items in an ajaxified HTML table.
	public function prepare_items() {

		// Trash tickets
		$this->trashTicket();

		// Delete tickets
		$this->deleteAction();

		// Bulk action
		$this->bulk_action();


		// This is used to store the raw data you want to display.
		$this->items = $this->get_tickets();

		/* pagination */
		$per_page     = $this->get_items_per_page( 'tickets_per_page', 20 );
		$current_page = $this->get_pagenum();
		$total_items  = $this->get_ticket_count();

		$this->items = array_slice( $this->items, ( ( $current_page - 1 ) * $per_page ), $per_page );

		$this->set_pagination_args( [
			'total_items' => $total_items, // total number of items
			'per_page'    => $per_page, // items to show on a page
		] );
	}


}
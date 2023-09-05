<?php

namespace inc;


defined( 'ABSPATH' ) || exit();

class TKT_DB {

	public function __construct() {
		$this->createTables();
	}

	public function createTables(): void {


		global $wpdb;

		// Ticket plugin tables name
		$departments = $wpdb->prefix . 'tkt_departments';
		$users       = $wpdb->prefix . 'tkt_users';
		$tickets     = $wpdb->prefix . 'tkt_tickets';
		$replies     = $wpdb->prefix . 'tkt_replies';
		$charset     = $wpdb->get_charset_collate();


		$departments_sql =
			"CREATE TABLE IF NOT EXISTS " . $this->setTableName( $departments ) . " (
			`ID` BIGINT(20) NOT NULL AUTO_INCREMENT,
		    `name` VARCHAR(255) NOT NULL,
            `parent` BIGINT(20) NOT NULL DEFAULT '0',
            `position` BIGINT(20) NOT NULL DEFAULT '1',
            `description` VARCHAR(512) DEFAULT NULL,
            PRIMARY KEY (`ID`),
            KEY `parent` (`parent`))
			ENGINE=InnoDB " . $charset . ";";

		$users_sql =
			"CREATE TABLE IF NOT EXISTS " . $this->setTableName( $users ) . " (
			`ID` BIGINT(20) NOT NULL AUTO_INCREMENT,
		    `department_id` BIGINT(20) NOT NULL,
            `user_id` BIGINT(20) NOT NULL,
            PRIMARY KEY (`ID`),
            KEY `department_id` (`department_id`),
			KEY `user_id` (`user_id`))
			ENGINE=InnoDB " . $charset . ";";

		$tickets_sql =
			"CREATE TABLE IF NOT EXISTS " . $this->setTableName( $tickets ) . " (
			`ID` BIGINT(20) NOT NULL AUTO_INCREMENT,
		    `title` VARCHAR(255) NOT NULL,
		    `body` text NOT NULL,
            `creator_id` BIGINT(20) DEFAULT NULL,
            `user_id` BIGINT(20) DEFAULT NULL,
            `user_name` VARCHAR(64) DEFAULT NULL,
            `user_email` VARCHAR(128) DEFAULT NULL,
            `user_phone` VARCHAR(16) DEFAULT NULL,
            `department_id` BIGINT(20) NOT NULL,
            `from_admin` tinyint(1) DEFAULT NULL,
            `status` VARCHAR(64) NOT NULL,
            `priority` VARCHAR(32) NOT NULL,
            `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `reply_date` VARCHAR(19) DEFAULT NULL,
            `voice` VARCHAR(512) DEFAULT NULL,
            `file` text DEFAULT NULL,
            PRIMARY KEY (`ID`),
            KEY `title` (`title`),
            KEY `creator_id` (`creator_id`),
            KEY `user_id` (`user_id`),
            KEY `from_admin` (`from_admin`),
            KEY `department_id` (`department_id`),
            KEY `status` (`status`))
			ENGINE=InnoDB " . $charset . ";";

		$replies_sql =
			"CREATE TABLE IF NOT EXISTS " . $this->setTableName( $replies ) . " (
			`ID` BIGINT(20) NOT NULL AUTO_INCREMENT,
            `ticket_id` BIGINT(20) DEFAULT NULL,
            `creator_id` BIGINT(20) DEFAULT NULL,
            `from_admin` tinyint(1) DEFAULT NULL,
		    `body` text NOT NULL ,
            `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `voice` VARCHAR(512) DEFAULT NULL ,
            `file` text DEFAULT NULL ,
            PRIMARY KEY (`ID`),
            KEY `ticket_id` (`ticket_id`))
			ENGINE=InnoDB " . $charset . ";";


		// Check dbDelta is exists or not
		if ( ! function_exists( 'dbDelta' ) ) {
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		}

		// Create And executing tables
		dbDelta( $departments_sql );
		dbDelta( $users_sql );
		dbDelta( $tickets_sql );
		dbDelta( $replies_sql );
	}

	// Add backtick to table and column name
	protected function setTableName( $table ): string {
		return "`$table`";
	}

}
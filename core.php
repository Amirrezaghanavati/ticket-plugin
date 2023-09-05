<?php


/*
Plugin Name: تیکت پشتیبانی
Plugin URI: http://wordpress.org/plugins/ticekt_supp
Description: افزونه تیکت پشتیبانی
Author: Amir ghanavati
Version: 1.0.0
Requires PHP: 7.2
Author URI: http://amirghanavati.com
Text Domain: ticket-plugin
*/

use inc\admin\TKT_AdminAjax;
use inc\admin\TKT_Menu;
use inc\front\TKT_FrontAjax;
use inc\front\TKT_WC_Dashboard;
use inc\TKT_DB;
use inc\TKT_SMS_Notification;
use inc\TKT_TicketManager;
use inc\TKTAssets;

defined( 'ABSPATH' ) || exit();

// Core class - only one instance allowed
class Core {

	private static ?Core $instance = null; // Use singleton design pattern
	public const MIN_PHP_VERSION = '7.2';

	public static function getInstance(): ?Core {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	private function __construct() {
		// Check user php version
		if ( version_compare( PHP_VERSION, self::MIN_PHP_VERSION, '<' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_php_version_notice' ] );

			return;
		}

		$this->constants();
		$this->init();
	}


	public function constants(): void {

		if ( ! function_exists( 'get_plugin_data' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}


		// Define Constants
		define( 'TKT_BASE_FILE', __FILE__ );
		// System Path
		define( 'TKT_BASE_DIR', trailingslashit( plugin_dir_path( TKT_BASE_FILE ) ) );
		define( 'TKT_ASSETS_DIR', trailingslashit( TKT_BASE_DIR . 'assets' ) );
		define( 'TKT_INC_DIR', trailingslashit( TKT_BASE_DIR . 'inc' ) );
		define( 'TKT_VIEW_DIR', trailingslashit( TKT_BASE_DIR . 'view' ) );
		// Http Path
		define( 'TKT_BASE_URL', trailingslashit( plugin_dir_url( TKT_BASE_FILE ) ) );
		define( 'TKT_ASSETS_URL', trailingslashit( TKT_BASE_URL . 'assets' ) );
		// Version
		define( 'TKT_VERSION', get_plugin_data( TKT_BASE_FILE )['Version'] );
	}

	public function init(): void {

		require_once TKT_BASE_DIR . 'vendor/autoload.php';

		// Code star framework;
		require_once TKT_INC_DIR . 'admin/codestar/codestar-framework.php';
		require_once TKT_INC_DIR . 'admin/TKT_settings.php';

		require_once TKT_INC_DIR . 'functions.php';


		register_activation_hook( TKT_BASE_FILE, [ $this, 'active' ] );
		register_deactivation_hook( TKT_BASE_FILE, [ $this, 'inactive' ] );

		new TKTAssets();

		if ( is_admin() ) {
			new TKT_Menu();
			new TKT_AdminAjax();
		} else {
			new TKT_WC_Dashboard();

		}

		new TKT_FrontAjax();
		new TKT_SMS_Notification();

	}

	public static function active(): void {
		new TKT_DB();
		if ( ! wp_next_scheduled( 'tkt_auto_close' ) ) {
			wp_schedule_event( time(), 'daily', 'tkt_auto_close' );
		}

		add_action( 'tkt_auto_close', [ self::getInstance() , 'tkt_auto_close_event_handler' ] );
	}

	public function inactive(): void {
//		if ( wp_next_scheduled( 'tkt_auto_close' ) ) {
//			wp_scheduled_delete();
//		}

	}

	public function tkt_auto_close_event_handler() {
		$period = tkt_settings( 'auto-close-days' );
		if ( ! tkt_settings( 'auto-close' || ! $period ) ) {
			return null;
		}

		// Auto close
		global $wpdb;
		$ticket_table = $wpdb->prefix . 'tkt_tickets';
		$date         = date( 'Y-m-d H:i:s', strtotime( '-' . $period . 'days' . time() ) );
		$tickets_id   = $wpdb->get_col( "SELECT ID FROM " . $ticket_table . " WHERE status != 'closed' AND reply_date < '" . $date . "'" );
		if ( $tickets ) {
			$ticket_manager = new TKT_TicketManager();
			foreach ( $tickets_id as $ticket_id ) {
				$ticket_manager->update_status( $ticket_id, 'closed' );
			}
		}

	}

	public function admin_php_version_notice(): void {
		include( __DIR__ . '/view/admin/php-version-notice.php' );
	}

}

Core::getInstance();
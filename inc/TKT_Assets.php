<?php

namespace inc;

defined( 'ABSPATH' ) || exit();

class TKTAssets {
	public function __construct() {
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_assets' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'front_assets' ] );
	}

	public function admin_assets(): void {

		// styles
		wp_enqueue_style( 'tkt-select2-style', TKT_ASSETS_URL . 'admin/css/select2.min.css' );
		wp_enqueue_style( 'tkt-admin-style', TKT_ASSETS_URL . 'admin/css/style.css', '', TKT_VERSION );

		// scripts
		// It is used to handle and control the admin media modal
		wp_enqueue_media();
		wp_enqueue_script( 'tkt-select2-script', TKT_ASSETS_URL . 'admin/js/select2.min.js', [ 'jquery' ], '', true );
		wp_enqueue_script( 'tkt-admin-script', TKT_ASSETS_URL . 'admin/js/script.js', [ 'jquery' ], TKT_VERSION, true );
		wp_localize_script( 'tkt-admin-script', 'TKT_DATA', [ 'ajax_url' => admin_url( 'admin-ajax.php' ) ] );


	}

	public function front_assets(): void {

		// styles
		wp_enqueue_style( 'tkt-front-style', TKT_ASSETS_URL . 'front/css/style.css', '', TKT_VERSION );
		wp_enqueue_style('tkt-style-sweetalert2','https://cdn.jsdelivr.net/npm/@sweetalert2/theme-dark@4/dark.css');

		// scripts
		wp_enqueue_script('tkt-sweetalert2', 'https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js', '', '', true);
		wp_register_script( 'tkt-front-myScript', TKT_ASSETS_URL . 'front/js/script.js', [ 'jquery' ] );
		wp_enqueue_script( 'tkt-front-myScript' );
		wp_localize_script( 'tkt-front-myScript', 'TKT_DATA', [
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'nonce'    => wp_create_nonce( 'tkt_ajax_nonce' )
		] );


	}

}
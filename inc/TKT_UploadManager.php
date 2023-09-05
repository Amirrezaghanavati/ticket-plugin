<?php

namespace inc;

defined( 'ABSPATH' ) || exit();

class TKT_UploadManager {

	private array $file;

	public function __construct( $file ) {
		$this->file = $file;
	}

	public function upload(): array {

		// custom dir
		add_filter( 'upload_dir', [ $this, 'custom_upload_dir' ] );

		if ( ! function_exists( 'wp_handle_upload' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}

		$upload_overrides = array( 'test_form' => false );
		$uploadFile       = wp_handle_upload( $this->file, $upload_overrides );
		if ( $uploadFile && ! isset( $uploadFile['error'] ) ) {
			return [ 'success' => true, 'url' => $uploadFile['url'] ];
		}

		return [ 'success' => false, 'message' => $uploadFile['error'] ];
	}

	public function custom_upload_dir( $params ) {
		$year             = date( 'Y', time() );
		$month            = date( 'm', time() );
		$day              = date( 'd', time() );
		$customDir        = "/tkt-uploads/{$year}/{$month}/{$day}";
		$params['subdir'] = $customDir;
		$params['path']   = $params['basedir'] . $customDir;
		$params['url']    = $params['baseurl'] . $customDir;

		return $params;
	}
}
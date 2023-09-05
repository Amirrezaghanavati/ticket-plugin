<?php
session_start();
defined( 'ABSPATH' ) || exit();

class TKT_FlashMessage {

	public const ERROR = 1;
	public const SUCCESS = 2;
	public const WARNNING = 3;
	public const INFO = 4;

	public static function addMessage( $body, $type = self::SUCCESS ): void {

		if ( ! isset( $_SESSION['tkt']['messages'] ) ) {
			$_SESSION['tkt']['messages'] = [];
		}

		$_SESSION['tkt']['messages'][] = [ 'body' => $body, 'type' => $type ];
	}

	public static function showMessage(): void {

		if ( isset( $_SESSION['tkt']['messages'] ) && ! empty( $_SESSION['tkt']['messages'] ) ) {
			foreach ( $_SESSION['tkt']['messages'] as $message ) {
				echo '<div class="notice is-dismissible ' . self::getType( $message['type'] ) . '">';
				echo '<p><strong>' . $message['body'] . '</strong></p>';
				echo '</div>';
			}
			self::removeSession();
		}

	}

	private static function getType( $type ) {

		switch ( $type ) {
			case 2:
				return 'notice-success';
				break;

			case 1:
				return 'notice-error';
				break;

			case 3:
				return 'notice-warning';
				break;

			case 4:
				return 'notice-info';
				break;
		}

	}

	private static function removeSession(): void {
		$_SESSION['tkt']['messages'] = [];
	}
}
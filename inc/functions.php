<?php


use inc\front\TKT_Front_DepartmentManager;

function tkt_settings( $option ) {
	$options = get_option( 'tkt-settings' );

	return $options[ $option ] ?? null;
}

function tkt_to_shamsi( $timestamp ): string {
	return jdate( $timestamp )->format( "Y-m-d H:i" );
}

function tkt_to_base ($timestamp): string {
	return date_i18n("Y-m-d H:i", $timestamp);
}

function tkt_get_status(): array {
	$customStatuses = tkt_settings('statuses');
	$list_status = [
		[
			'slug'  => 'open',
			'name'  => 'باز',
			'color' => tkt_settings('open-color'),
		],
		[
			'slug'  => 'answered',
			'name'  => 'پاسخ داده شده',
			'color' => tkt_settings('answered-color'),
		],
		[
			'slug'  => 'closed',
			'name'  => 'بسته شده',
			'color' => tkt_settings('closed-color'),
		],
		[
			'slug'  => 'finished',
			'name'  => 'پایان یافته',
			'color' => tkt_settings('finished-color'),
		],
	];

	if (is_array($customStatuses)){
		foreach ($customStatuses as $status){
			$list_status [] = [
				'slug'  => $status['status-slug'],
				'name'  => $status['status-title'],
				'color' => $status['status-color']
			];
		}
	}

	if ( is_admin() ) {
		$list_status [] = [
			'slug'  => 'trash',
			'name'  => 'زباله دان',
			'color' => tkt_settings('trash-color'),
		];
	}

	return $list_status;
}

function tkt_get_status_color( $status ) {

	$statuses = tkt_get_status();
	foreach ( $statuses as $item ) {
		if ( $status === $item['slug'] ) {
			return $item['color'];
		}
	}

	return false;
}

function tkt_get_status_name( $status ) {

	$statuses = tkt_get_status();
	foreach ( $statuses as $item ) {
		if ( $status === $item['slug'] ) {
			return $item['name'];
		}
	}

	return false;
}


function tkt_get_file_name( $url ): string {
	$path = parse_url( $url, PHP_URL_PATH );

	return basename( $path ); // file name

}

function tkt_get_priority( $priority ): string {
	switch ( $priority ) {
		case 'low':
			return 'کم';
			break;
		case 'medium':
			return 'متوسط';
			break;
		case 'high':
			return 'زیاد';
			break;
	}
}

function tkt_get_priority_name( $priority ) {
	switch ( $priority ) {
		case 'low':
			return 'کم';
			break;
		case 'medium':
			return 'متوسط';
			break;
		case 'high':
			return 'زیاد';
			break;
	}
}

function tkt_get_status_html( $status ): string {

	$status_name  = tkt_get_status_name( $status );
	$status_color = tkt_get_status_color( $status );

	$style = is_admin() && !wp_doing_ajax() ? 'style=background-color:' . $status_color . ";'" : '';

	return '  <div class="tkt-status" ' . $style . '>
                    <span class="tkt-status-name"> ' . $status_name . '</span>
                    <span class="tkt-status-color"
                          style="background-color: ' . $status_color . '"></span>
                </div>';
}

function get_department_name_html( $department_id ): string {
	$department_manager = new TKT_Front_DepartmentManager();
	$department         = $department_manager->get_department( $department_id );

	return '<span>' . esc_html( $department->name ) . '</span>';

}
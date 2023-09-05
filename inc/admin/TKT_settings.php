<?php

function tkt_user_meta_keys(): array {
	global $wpdb;
	$fields = $wpdb->get_col( 'SELECT DISTINCT meta_key FROM ' . $wpdb->usermeta );
	$array  = [];
	foreach ( $fields as $field ) {
		$array [ $field ] = $field;
	}

	return $array;
}

// Control core classes for avoid errors
if ( class_exists( 'CSF' ) ) {

	//
	// Set a unique slug-like ID
	$prefix = 'tkt-settings';


	//
	// Create options
	CSF::createOptions( $prefix, array(
		'menu_title'      => 'تیکت پشتیبانی',
		'menu_slug'       => 'tkt-settings',
		'menu_hidden'     => true,
		'framework_title' => 'تیکت پشتیبانی'
	) );

	//
	// Create a section
	CSF::createSection( $prefix, array(
			'title'  => 'عمومی',
			'fields' => array(
				array(
					'id'      => 'new-ticket-alert',
					'type'    => 'switcher',
					'title'   => 'وضعیت نمایش پیغام ارسال تیکت',
					'label'   => 'آیا فعال باشد؟ ',
					'default' => true
				),
				array(
					'id'         => 'new-ticket-alert-text',
					'type'       => 'textarea',
					'title'      => 'متن پیغام',
					'default'    => 'متن تستی',
					'dependency' => array( 'new-ticket-alert', '==', 'true' )
				),
				array(
					'id'      => 'auto-close',
					'type'    => 'switcher',
					'title'   => 'بستن خودکار تیکت ها',
				),
				array(
					'id'    => 'auto-close-days',
					'type'  => 'text',
					'title' => 'مدت زمان بر حسب روز',
					'desc' => 'مدت زمانی که از ارسال اخرین پاسخ تیکت خواهد گذشت.',
				),


			)
		)
	);

	CSF::createSection( $prefix, array(
			'title'  => 'سوالات متداول',
			'fields' => array(
				array(
					'id'     => 'faqs',
					'type'   => 'repeater',
					'title'  => 'سوال جدید',
					'fields' => array(
						array(
							'id'    => 'faq-title',
							'type'  => 'text',
							'title' => 'عنوان سوال'
						),
						array(
							'id'    => 'faq-body',
							'type'  => 'textarea',
							'title' => 'توضیح سوال'
						),
					),
				),
			)
		)
	);

	CSF::createSection( $prefix, array(
			'title'  => 'وضعیت ها',
			'fields' => array(
				array(
					'id'      => 'open-color',
					'type'    => 'color',
					'title'   => 'رنگ وضعیت باز',
					'default' => '#d41e11',
				),
				array(
					'id'      => 'answered-color',
					'type'    => 'color',
					'title'   => 'رنگ وضعیت پاسخ داده شده',
					'default' => '#14d402',
				),
				array(
					'id'      => 'closed-color',
					'type'    => 'color',
					'title'   => 'رنگ وضعیت بسته شده',
					'default' => '#eb9234',
				),
				array(
					'id'      => 'finished-color',
					'type'    => 'color',
					'title'   => 'رنگ وضعیت پایان یافته',
					'default' => '#05040d',
				),
				array(
					'id'      => 'trash-color',
					'type'    => 'color',
					'title'   => 'رنگ وضعیت زباله دان',
					'default' => '#05040d',
				),
				array(
					'id'     => 'statuses',
					'type'   => 'repeater',
					'title'  => 'ایجاد وضعیت جدید',
					'fields' => array(

						array(
							'id'    => 'status-title',
							'type'  => 'text',
							'title' => 'عنوان'
						),
						array(
							'id'    => 'status-slug',
							'type'  => 'text',
							'title' => 'نامک'
						),
						array(
							'id'    => 'status-color',
							'type'  => 'color',
							'title' => 'رنگ وضعیت'
						)

					),
				),
			)
		)
	);

	CSF::createSection( $prefix, array(
			'title' => 'پیامک',
			'id'    => 'sms-section'
		)
	);

	CSF::createSection( $prefix, array(
			'title'  => 'انتخاب سامانه پیامکی',
			'parent' => 'sms-section',
			'fields' => array(
				array(
					'id'          => 'phone-meta-key',
					'type'        => 'select',
					'title'       => 'کلید شماره موبایل',
					'placeholder' => 'یک کلید انتخاب نمایید',
					'options'     => 'tkt_user_meta_keys'
				),
				array(
					'id'          => 'sms-service',
					'type'        => 'select',
					'title'       => 'سامانه های پیامکی',
					'placeholder' => 'یک آیتم را انتخاب کنید',
					'options'     => array(
						'melipayamak' => 'ملی پیامک',
					),
					'default'     => 'melipayamak'
				),
				array(
					'id'    => 'sms-username',
					'type'  => 'text',
					'title' => 'نام کاربری سامانه',
				),
				array(
					'id'    => 'sms-password',
					'type'  => 'text',
					'title' => 'رمز عبور سامانه',
				),
			)
		)
	);

	CSF::createSection( $prefix, array(
			'title'  => 'ارسال تیکت',
			'parent' => 'sms-section',
			'fields' => array(
				array(
					'id'    => 'user-submit-sms',
					'type'  => 'switcher',
					'title' => 'فعال سازی',
				),
				array(
					'id'    => 'user-submit-sms-pattern-code',
					'type'  => 'text',
					'title' => 'کد الگو',
				),
				array(
					'id'    => 'user-submit-sms-pattern',
					'type'  => 'textarea',
					'title' => 'الگو',
				),
				array(
					'type'    => 'content',
					'content' => '<p>شناسه تیکت: {{ticket_id}}</p>' .
					             '<p>عنوان : {{title}}</p>' .
					             '<p>دپارتمان : {{department}}</p>' .
					             '<p>وضعیت : {{status}}</p>' .
					             '<p>اهمیت : {{priority}}</p>' .
					             '<p>تاریخ : {{date}}</p>',
				),
			)
		)
	);

	CSF::createSection( $prefix, array(
			'title' => 'ایمیل',
			'id'    => 'email-section'
		)
	);

	CSF::createSection( $prefix, array(
			'title'  => 'تنظیمات ایمیل',
			'parent' => 'email-section',
			'fields' => array(
				array(
					'id'    => 'email-from',
					'type'  => 'text',
					'title' => 'ایمیل ارسال کننده',
				),
				array(
					'id'    => 'from-name',
					'type'  => 'text',
					'title' => 'نام ارسال کننده',
				),
			)
		)
	);

	CSF::createSection( $prefix, array(
			'title'  => 'ارسال ایمیل',
			'parent' => 'email-section',
			'fields' => array(
				array(
					'id'    => 'user-submit-email',
					'type'  => 'switcher',
					'title' => 'فعال سازی',
				),
				array(
					'id'    => 'user-submit-email-text',
					'type'  => 'wp_editor',
					'title' => 'متن ایمیل',
				),
				array(
					'type'    => 'content',
					'content' => '<p>شناسه تیکت: {{ticket_id}}</p>' .
					             '<p>عنوان : {{title}}</p>' .
					             '<p>دپارتمان : {{department}}</p>' .
					             '<p>وضعیت : {{status}}</p>' .
					             '<p>اهمیت : {{priority}}</p>' .
					             '<p>تاریخ : {{date}}</p>',
				),
			)
		)
	);
}
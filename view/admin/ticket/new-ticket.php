<?php
$statuses = tkt_get_status();

$department_manager = new TKT_DepartmentManager();
$parent_departments = $department_manager->get_parent_departments();

?>

<div class="tkt-submit-ticket wrap">
    <h1 class="wp-heading-inline"><?= $is_edit ? 'ویرایش تیکت' : 'ارسال تیکت جدید' ?></h1>
    <a href="?page=tkt-tickets" class="page-title-action">همه تیکت ها</a>
    <hr class="wp-header-end">


	<?php TKT_FlashMessage::showMessage(); ?>


    <form method="post" class="ticket-form">

        <div id="poststuff">
            <div id="post-body" class="metabox-holder columns-2">
                <div id="post-body-content">
                    <div class="tkt-content-inner">
                        <div id="titlediv">
                            <div id="titlewrap">
                                <input type="text" id="title" name="title"
                                       value="<?= $is_edit ? esc_attr( $ticket->title ) : '' ?>"
                                       placeholder="عنوان تیکت" autocomplete="off">
                            </div>
                        </div>
                        <div id="postdivrich">
                            <div id="wp-content-wrap" class="wp-core-ui wp-editor-wrap">
								<?php
								$body = $is_edit ? $ticket->body : null;
								wp_editor( $body, 'tkt-content', [ 'editor_height' => 300 ] );
								?>
                            </div>
                        </div>
                    </div>


                    <div class="postbox" id="tkt-info-div">
                        <h2 class="hndle">
                            <span>اطلاعات تیکت</span>
                        </h2>
                        <div class="inside tkt-clearfix">
                            <ul class="tkt-info-tabs">
                                <li class="tkt-tab-general active">
                                    <a href="#">
                                        <span class="dashicons dashicons-admin-generic"></span>
                                        عمومی
                                    </a>
                                </li>

                            </ul>
                            <div class="tkt-tab-panel" id="tkt-panel-general">
                                <div>
                                    <table>
                                        <tbody>
                                        <tr>
                                            <td>
                                                <label for="tkt-status">وضعیت</label>
                                            </td>
                                            <td>
                                                <select id="tkt-status" name="status">
													<?php foreach ( $statuses as $_status ) : ?>
                                                        <option <?php $is_edit ? selected( $ticket->status, $_status['slug'] ) : '' ?>
                                                                value="<?= esc_attr( $_status['slug'] ) ?>"><?= esc_html( $_status['name'] ) ?></option>
													<?php endforeach; ?>

                                                </select>

                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label for="tkt-priority">اهمیت</label>
                                            </td>
                                            <td>
                                                <select id="tkt-priority" name="priority">
                                                    <option value="low" <?php $is_edit ? selected( $ticket->priority, 'low' ) : null ?>>
                                                        کم
                                                    </option>
                                                    <option value="medium" <?php $is_edit ? selected( $ticket->priority, 'medium' ) : null ?>>
                                                        متوسط
                                                    </option>
                                                    <option value="high" <?php $is_edit ? selected( $ticket->priority, 'high' ) : null ?>>
                                                        زیاد
                                                    </option>
                                                </select>
                                            </td>
                                        </tr>
										<?php if ( $is_edit ) : ?>
											<?php if ( $ticket->creator_id ) : ?>
												<?php $creator_data = get_userdata( $ticket->creator_id ); ?>

                                                <tr>
                                                    <td>
                                                        <label for="tkt-creator-id">ایجاد کننده</label>
                                                    </td>
                                                    <td>
                                                        <select id="tkt-creator-id" name="creator-id">
                                                            <option value="<?= esc_attr( $ticket->creator_id ) ?>"
                                                                    selected><?= esc_html( $creator_data->user_login ) ?></option>
                                                        </select>
                                                        <a href="<?= get_edit_user_link( $ticket->creator_id ) ?>"
                                                           target="_blank">مشاهده پروفایل</a>
                                                    </td>
                                                </tr>

											<?php endif; ?>
										<?php endif; ?>
                                        <tr>
                                            <td>
                                                <label for="tkt-user-id"><?= $is_edit ? 'کاربر' : 'کاربر(ان)' ?></label>
                                            </td>
											<?php if ( $is_edit ) : ?>
												<?php if ( $ticket->user_id ):
													$user_data = get_userdata( $ticket->user_id );
													?>
                                                    <td>

                                                        <select id="tkt-user-id" name="user-id">
                                                            <option value="<?= esc_attr( $ticket->user_id ) ?>"
                                                                    selected><?= esc_html( $user_data->user_login ) ?></option>
                                                        </select>
                                                        <a href="<?= get_edit_user_link( $ticket->user_id ) ?>"
                                                           target="_blank">مشاهده پروفایل</a>
                                                    </td>
                                                <?php else: ?>
                                                    <td>
                                                        <select id="tkt-user-id" name="user-id">
                                                        </select>
                                                    </td>
												<?php endif; ?>
											<?php else: ?>
                                                <td>
                                                    <select id="tkt-user-id" name="user-id[]" multiple>
                                                    </select>
                                                </td>

											<?php endif; ?>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label for="tkt-ath-file">فایل پیوست</label>
                                            </td>
                                            <td>
												<?php
												if ( $is_edit ) : ?>
                                                    <input type="text" class="tkt-upload-file regular-text"
                                                           id="tkt-ath-file" name="file"
                                                           value="">
													<?php if ( $ticket->file ) : ?>
                                                        <a href="<?= esc_attr( $ticket->file ) ?>"
                                                           target="_blank">مشاهده فایل</a>
													<?php endif; ?>
												<?php else : ?>
                                                    <input type="text" class="tkt-upload-file regular-text"
                                                           id="tkt-ath-file" name="file" value="">
												<?php endif; ?>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        </div>
                    </div>


                    <div class="postbox-container" id="tkt-submit-reply-div">
                        <div class="postbox">
                            <h2 class="hndle">
                                <span>ارسال پاسخ</span>
                            </h2>
                            <div class="inside">

								<?php wp_editor( null, 'tkt-reply-content', [ 'editor_height' => 200 ] ); ?>

                                <input type="text" class="tkt-upload-file regular-text" name="reply-file"
                                       placeholder="فایل پیوست">
                            </div>
                        </div>

						<?php if ( $replies ) : ?>
                            <div id="tkt-replies">
								<?php foreach ( $replies as $reply ) : ?>
                                    <div class="tkt-reply tkt-from-admin">
                                        <div class="tkt-meta tkt-clearfix">
											<?php $user_data = get_userdata( $reply->creator_id ); ?>
                                            <a href="" class="tkt-user"
                                               target="_blank">
												<?= $user_data->display_name ?></a>
                                            <span class="tkt-date"
                                                  dir="ltr"><?= tkt_to_shamsi( strtotime( $reply->create_date ) ) ?></span>
                                        </div>
                                        <div class="tkt-content">
											<?= $reply->body ?>
                                        </div>
                                        <div class="tkt-footer tkt-clearfix">
                                            <input type="text" class="tkt-upload-file regular-text"
                                                   name="<?= 'reply-file-' . $reply->ID ?>"
                                                   value=""
                                                   placeholder="فایل پیوست">
											<?php if ( $reply->file ) : ?>
                                                <a href="<?= esc_attr( $reply->file ) ?>" target="_blank">مشاهده
                                                    فایل</a>
											<?php endif; ?>
                                            <div class="tkt-actions">
                                                <a href="#" class="tkt-toggle-edit">ویرایش</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tkt-editor">
										<?php wp_editor( $reply->body, 'tkt-reply-content-' . $reply->ID, [ 'editor_height' => 200 ] ); ?>
                                    </div>
								<?php endforeach; ?>

                            </div>
						<?php endif; ?>


                    </div>


                </div>
                <div id="postbox-container-1" class="postbox-container">
                    <div id="submitdiv" class="postbox">
                        <h2 class="hndle"><span>ارسال</span></h2>
                        <div class="inside">
                            <div id="minor-publishing">
                                <div id="misc-publishing-actions">
                                    <div class="misc-pub-section"></div>
                                    <div class="misc-pub-section">
                                        <input type="checkbox" id="tkt-notification" name="notification" checked>
                                        <label for="tkt-notification">ارسال نوتیفیکیشن</label>
                                    </div>
									<?php if ( $is_edit ) : ?>
                                        <div class="misc-pub-section">
                                            <span class="dashicons dashicons-calendar"></span>

                                            <span dir="ltr">تاریخ</span>
                                            <a href="#" class="tkt-edit-date">ویرایش</a>
                                            <input type="text" class="regular-text" name="create-date"
                                                   value="<?= tkt_to_shamsi(date( 'Y-m-d H:i:s', strtotime( $ticket->create_date ) )) ?>">
                                        </div>
									<?php endif; ?>

                                </div>
                            </div>
                            <div id="major-publishing-actions">
                                <div class="submitbox" id="submitpost">
                                    <div id="publishing-action">
                                        <input type="submit" name="publish" id="publish"
                                               class="button button-primary button-large" value="ارسال">
                                    </div>
                                    <div class="clear"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="postbox" id="tkt-department-div">
                        <h2 class="hndle">
                            <span>دپارتمان</span>
                        </h2>
                        <div class="inside">
                            <div>
								<?php if ( $parent_departments ) : ?>
									<?php foreach ( $parent_departments as $parent_department ) : ?>

                                        <p><?= esc_html( $parent_department->name ) ?></p>

										<?php $child_departments = $department_manager->get_child_departments( $parent_department->ID ) ?>

										<?php if ( $child_departments ) : ?>
											<?php foreach ( $child_departments as $child_department ) : ?>
                                                <label>
                                                    <input <?php $is_edit ? checked( $ticket->department_id, $child_department->ID ) : null ?>
                                                            type="radio" name="department-id"
                                                            value="<?= esc_attr( $child_department->ID ) ?>">
													<?= esc_html( $child_department->name ) ?>
                                                </label>
											<?php endforeach; ?>
										<?php endif; ?>

									<?php endforeach; ?>

								<?php endif; ?>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <br class="clear">
        </div>

		<?php wp_nonce_field( 'ticket_security', 'ticket_nonce' ) ?>
    </form>
</div>
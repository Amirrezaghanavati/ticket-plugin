<?php session_start() ?>
<div class="tkt-departments wrap nosubsub">

    <h1 class="wp-heading-inline">لیست دپارتمان ها</h1>

    <hr class="wp-header-end">

    <a href="<?= esc_url( admin_url( 'admin.php?page=tkt-departments&action=create' ) ) ?>"
       class="button button-primary btn-create">افزودن</a>

    <?php TKT_FlashMessage::showMessage(); ?>

    <div id="ajax-response"></div>
    <div id="col-container" class="wp-clearfix">
        <div id="col">
            <div class="col-wrap">
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                    <tr>
                        <th scope="col" class="manage-column">عنوان</th>
                        <th scope="col" class="manage-column">والد</th>
                        <th scope="col" class="manage-column">کاربران پاسخگو</th>
                        <th scope="col" class="manage-column">موقعیت</th>
                    </tr>
                    </thead>
                    <tbody id="the-list">

					<?php if ( $departments ) : ?>

						<?php foreach ( $departments as $department ): ?>

                            <tr>
                                <td>
                                    <strong><?= esc_html( $department->name ) ?></strong>
                                    <br>
                                    <div class="row-actions">
                                        <span class="edit" ><a  href="<?= esc_url( admin_url( 'admin.php?page=tkt-departments&action=edit&id=' . $department->ID )) ?>">ویرایش</a> | </span>
                                        <span class="delete"><a type="submit" href="<?= esc_url(wp_nonce_url( admin_url( 'admin.php?page=tkt-departments&action=delete&id=' . $department->ID ), 'delete_department','delete_department_nonce')) ?>">حذف</a></span>
                                    </div>
                                </td>
                                <td>
									<?php if ( $department->parent ): ?>
										<?= $this->get_department( $department->parent )->name ?? '-' ?>
									<?php else: echo 'اصلی' ?>
									<?php endif; ?>
                                </td>
                                <td></td>
                                <td>
									<?= esc_html( $department->position ) ?>
                                </td>
                            </tr>

						<?php endforeach; ?>

					<?php endif; ?>

                    </tbody>
                    <tfoot>
                    <tr>
                        <th scope="col" class="manage-column">عنوان</th>
                        <th scope="col" class="manage-column">والد</th>
                        <th scope="col" class="manage-column">کاربران پاسخگو</th>
                        <th scope="col" class="manage-column">موقعیت</th>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
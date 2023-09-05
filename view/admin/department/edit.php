<div class="tkt-departments wrap nosubsub">

    <h1 class="wp-heading-inline">ویرایش دپارتمان</h1>

    <hr class="wp-header-end">
    <div id="ajax-response"></div>
    <div id="col-container" class="wp-clearfix">
        <div id="col">
            <div class="col-wrap">
                <div class="form-wrap">

					<?php TKT_FlashMessage::showMessage(); ?>

                    <form id="‌tkt-add-department" method="post">

						<?php wp_nonce_field( 'update_department', 'update_department_nonce', false ); ?>

                        <input type="hidden" name="department_id" value="<?= esc_attr($department->ID) ?>">

                        <div class="form-field">
                            <label for="department-name">عنوان</label>
                            <input type="text" name="name" id="department-name"
                                   value="<?= esc_attr( $department->name ) ?>">
                        </div>
                        <div class="form-field term-parent-wrap">
                            <label for="department-parent">والد</label>
                            <select name="parent" id="department-parent">
                                <option value="0">بدون والد</option>
	                            <?php if ( $departments ): ?>
		                            <?php foreach ( $departments as $item ): ?>
			                            <?php if ( ! $item->parent && $item->ID !== $department->ID): ?>
                                            <option <?= $department->parent === $item->ID ? 'selected' : '' ?> value="<?= esc_attr( $item->ID ) ?>"><?= esc_html( $item->name ) ?></option>
			                            <?php endif; ?>
		                            <?php endforeach; ?>
	                            <?php endif; ?>
                            </select>
                        </div>
                        <div class="form-field">
                            <label for="department-answerable">کاربران پاسخگو</label>
                            <select id="department-answerable" name="answerable[]" multiple>
                                <?php if ($users_answerable): ?>
                                <?php foreach ($users_answerable as $user_id): ?>
                                    <?php $userdata = get_userdata($user_id); ?>
                                        <option value="<?= $user_id ?>" selected><?= $userdata->user_login ?></option>
	                                <?php endforeach; ?>
                                <?php endif; ?>

                            </select>
                        </div>
                        <div class="form-field">
                            <label for="department-position">موقعیت</label>
                            <input type="number" class="small-text" name="position" id="department-position"
                                   value="<?php echo esc_attr( $department->position ) ?>">
                        </div>
                        <div class="form-field">
                            <label for="department-description">توضیح کوتاه</label>
                            <textarea name="description" id="department-description" rows="5"
                                      cols="40"><?php echo esc_textarea( $department->description ) ?></textarea>
                        </div>
                        <p class="submit">
                            <input type="submit" name="submit" class="button button-primary" value="ویرایش">
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
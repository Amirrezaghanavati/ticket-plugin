<div class="tkt-departments wrap nosubsub">

    <h1 class="wp-heading-inline">ساخت دپارتمان جدید</h1>

    <hr class="wp-header-end">
    <div id="ajax-response"></div>
    <div id="col-container" class="wp-clearfix">

        <div id="col">
            <div class="col-wrap">
                <div class="form-wrap">
                    <h2>دپارتمان جدید</h2>

                    <form id="‌tkt-add-department" method="post" action="">

						<?php wp_nonce_field( 'add_department', 'add_department_nonce', false ) ?>

                        <div class="form-field">
                            <label for="department-name">عنوان</label>
                            <input type="text" name="name" id="department-name">
                        </div>
                        <div class="form-field term-parent-wrap">
                            <label for="department-parent">والد</label>
                            <select name="parent" id="department-parent">
                                <option value="0">بدون والد</option>
								<?php if ( $departments ): ?>
									<?php foreach ( $departments as $department ): ?>
										<?php if ( ! $department->parent ): ?>
                                            <option value="<?= esc_attr( $department->ID ) ?>"><?= esc_html( $department->name ) ?></option>
										<?php endif; ?>
									<?php endforeach; ?>
								<?php endif; ?>
                            </select>
                        </div>
                        <div class="form-field">
                            <label for="department-answerable">کاربران پاسخگو</label>
                            <select id="department-answerable" name="answerable[]" multiple="multiple">

                            </select>
                        </div>
                        <div class="form-field">
                            <label for="department-position">موقعیت</label>
                            <input type="number" class="small-text" name="position" id="department-position">
                        </div>
                        <div class="form-field">
                            <label for="department-description">توضیح کوتاه</label>
                            <textarea name="description" id="department-description" rows="5" cols="40"></textarea>
                        </div>
                        <p class="submit">
                            <input type="submit" name="submit" class="button button-primary" value="افزودن">
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php

// Query strings
$page          = $_REQUEST['page'] ?? null;
$department_id = $_REQUEST['department-id'] ?? null;
$priority      = $_REQUEST['priority'] ?? null;
$creator_id    = $_REQUEST['creator-id'] ?? null;
$search        = $_REQUEST['search'] ?? null;

$department_manager = new TKT_DepartmentManager();
$parent_departments = $department_manager->get_parent_departments();

// Status List
$statuses = tkt_get_status();

?>

<div class="tkt-tickets wrap">
    <h1 class="wp-heading-inline">تیکت ها</h1>
    <a href="?page=tkt-new-ticket" class="page-title-action">ارسال تیکت جدید</a>


    <hr class="wp-header-end">
    <div id="poststuff">
        <div id="post-body" class="metabox-holder columns-2">
			<?php if ( $search ): ?>
                <span class="subtitle" style="background-color: #9ea3a8">نتایج جستجو برای:  <?= $search ?></span>
			<?php endif; ?>
            <div id="post-body-content">
                <div class="meta-box-sortables ui-sortable">
                    <ul class="subsubsub">
                        <li class="all">
                            <a href="admin.php?page=tkt-tickets" class="current">
                                همه
                                <span class="count">(<?= $this->ticketsObj->get_ticket_count(['status' => 'all']) ?>)</span>
                            </a>
                        </li>
						<?php foreach ( $statuses as $status ): ?>
                            <li class="<?= esc_attr( $status['slug'] ) ?>">
                                <a href="admin.php?page=tkt-tickets&status=<?= $status['slug'] ?>" class="current"
                                   style="color:<?= $status['color'] ?>">
									<?= esc_html( $status['name'] ) ?>
                                    <span class="count">(<?= $this->ticketsObj->get_ticket_count(['status' => $status['slug']]) ?>)</span>
                                </a>
                            </li>
						<?php endforeach; ?>
                    </ul>


                    <br class="clear">


                    <form method="get">
                        <div class="filter-box">

                            <input type="hidden" name="page" value="<?= $page ?>">

                            <label>
                                <select name="department-id">
                                    <option value="">تمام دپارتمان ها</option>
									<?php if ( $parent_departments ) : ?>
										<?php foreach ( $parent_departments as $parent ) : ?>
                                            <optgroup label="<?= esc_attr( $parent->name ) ?>">
												<?php $child_departments = $department_manager->get_child_departments( $parent->ID ) ?>
												<?php if ( $child_departments ) : ?>
													<?php foreach ( $child_departments as $child ) : ?>
                                                        <option <?= selected( $department_id, $child->ID ) ?>
                                                                value="<?= esc_attr( $child->ID ) ?>"><?= esc_html( $child->name ) ?></option>
													<?php endforeach; ?>
												<?php endif; ?>
                                            </optgroup>
										<?php endforeach; ?>

									<?php endif; ?>
                                </select>
                            </label>

                            <label>
                                <select name="priority">
                                    <option value="">تمام اولویت ها</option>
                                    <option <?= selected( $priority, 'low' ) ?> value="low">کم</option>
                                    <option <?= selected( $priority, 'medium' ) ?> value="medium">متوسط</option>
                                    <option <?= selected( $priority, 'high' ) ?> value="high">زیاد</option>
                                </select>
                            </label>


                            <select id="tkt-creator-id" name="creator-id">
								<?php
								if ( $creator_id ) {
									$user_data = get_userdata( $creator_id );
									echo '<option value="' . esc_attr( $creator_id ) . '" selected>' . $user_data->user_login . '</option>';
								}
								?>
                            </select>

                            <input type="search" name="search" value="<?= $search ?>" placeholder="جستجو">
                            <input type="submit" id="search-submit" class="button" value="فیلتر">


                        </div>
                    </form>

                    <!-- <form method="post" onsubmit="">
                            <input type="submit" id="delete-all" name="delete-all" class="button" value="خالی کردن زباله دان">
                    </form> -->

                    <form method="post">
						<?php
						$this->ticketsObj->prepare_items();
						$this->ticketsObj->display();
						?>
                    </form>
                </div>
            </div>
        </div>
        <br class="clear">
    </div>
</div>
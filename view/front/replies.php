<div class="tkt-replies">

	<?php if ( $replies ) : ?>
		<?php foreach ( $replies as $reply ) : ?>
            <div class="tkt-ticket tkt-reply-item tkt-from-admin">

				<?php $user_data = get_userdata( $reply->creator_id ); ?>
                <div class="tkt-info">
                    <span class="tkt-creator"><?= $user_data->display_name ?></span>
                    <span class="tkt-date"
                          dir="ltr"><?= tkt_to_shamsi( strtotime( $reply->create_date ) ) ?></span>
                </div>
                <div class="tkt-ticket-content">
					<?= nl2br( $reply->body ) ?>


					<?php if ( $reply->file ) : ?>
                        <div class="tkt-ath-file">

                            <a href="<?= $reply->file ?>" class="tkt-clearfix" target="_blank">
                                <span class="tkt-icon"><img src="<?= TKT_ASSETS_URL . 'front/images/' ?>diamond.svg"
                                                            width="24" height="24" alt="diamond"></span>
                                <span class="tkt-file-name"><?php echo tkt_get_file_name( $reply->file ); ?></span>
                                <img class="tkt-icon-download"
                                     src="<?= TKT_ASSETS_URL . 'front/images/' ?>download.svg" width="18"
                                     height="18" alt="download">
                            </a>

                        </div>
					<?php endif; ?>


                </div>

            </div>

		<?php endforeach; ?>
	<?php endif; ?>

</div>
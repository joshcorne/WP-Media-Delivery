<?php
/**
 * Provide the gallery area for new media posts
 *
 * @link       https://joshcorne.co.uk
 * @since      1.0.0
 *
 * @package    Media_Delivery
 * @subpackage Media_Delivery/admin/partials
 */
?>

<div class="imgs-container">
	<ul class="img-list">
		<?php if ( $have_imgs ) :
			foreach ( $img_ids as $key=>$id ) { ?>
				<li class="image" data-attachment_id="<?php echo esc_attr( $id ); ?>">
					<?php echo wp_get_attachment_image( $id, 'thumbnail' ); ?>
					<input class="img-id" name="img_ids[<?php echo esc_attr( $key ) ?>]" type="hidden" value="<?php echo esc_attr( $id ); ?>" />
					<ul class="actions">
						<li>
							<a class="delete-img" href="#" title="<?php esc_attr_e('Delete image', 'media-delivery') ?>">
								<?php _e('Delete', 'media-delivery') ?>
							</a>
						</li>
					</ul>
				</li>
			<?php }
		endif; ?>
	</ul>
</div>

<p class="hide-if-no-js button-container">
    <a class="upload-img" href="<?php echo esc_url( $upload_link ) ?>">
        <?php _e('Add images', 'media-delivery') ?>
    </a>
</p>

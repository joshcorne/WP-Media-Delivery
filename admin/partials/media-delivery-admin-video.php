<?php
/**
 * Provide the meta sidebar area for the plugin admin
 *
 * @link       https://joshcorne.co.uk
 * @since      1.0.0
 *
 * @package    Media_Delivery
 * @subpackage Media_Delivery/admin/partials
 */
?>

<div class="vid-container">
    <span id="video-loading" class="spinner hidden"></span>
    <span id="vid-embed">
        <?php if ( $your_vid_src ) :
            echo do_shortcode('[video src="'.$your_vid_src.'"]');
        endif; ?>
    </span>
</div>

<p class="hide-if-no-js button-container">
    <a class="upload-vid <?php if ( $your_vid_src  ) { echo 'hidden'; } ?>" 
       href="<?php echo esc_url( $upload_link ) ?>">
        <?php _e('Set video', 'media-delivery') ?>
    </a>
    <a class="delete-vid <?php if ( ! $your_vid_src  ) { echo 'hidden'; } ?>" 
      href="#">
        <?php _e('Remove video', 'media-delivery') ?>
    </a>
</p>

<input class="vid-id" name="vid_id" type="hidden" value="<?php echo esc_attr( $your_vid_id ); ?>" />
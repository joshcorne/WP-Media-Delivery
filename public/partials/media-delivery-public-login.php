<?php

/**
 * This file contains the public facing password form to unlock the post.
 *
 * @link       https://joshcorne.co.uk
 * @since      1.0.0
 *
 * @package    Media_Delivery
 * @subpackage Media_Delivery/public/partials
 */
?>

<noscript>This form requires JavaScript to be enabled.</noscript>
<p><?php esc_html_e( $msg, 'media-delivery'  ) ?></p>
<form class="dual-password-form post-password-form" action="<?php echo esc_url( get_bloginfo( 'wpurl' ) ); ?>/wp-login.php?action=postpass" method="post">
    <p>
        <? _e( 'To view this, please enter your booking reference and surname.', 'media-delivery')?>
    </p>
    <p>
        <label class="pass-label" for="<?php esc_attr_e( $ref_label, 'media-delivery' ) ?>">
            <span class="pass-label-text"><?php _e( "Booking Reference:", 'media-delivery' ) ?></span>
            <input name="booking_ref" id="<?php esc_attr_e( $ref_label, 'media-delivery'  ) ?>" type="text" spellcheck="false" autocomplete="off" />
        </label>
        <label class="pass-label" for="<?php esc_attr_e( $name_label, 'media-delivery'  ) ?>">
            <span class="pass-label-text"><?php _e( "Surname:", 'media-delivery' ) ?></span>
            <input name="surname" id="<?php esc_attr_e( $name_label, 'media-delivery'  ) ?>" type="text" spellcheck="false" autocomplete="off" />
        </label>
        <input type="submit" name="Submit" value="<?php esc_attr_e( "Enter" ) ?>" />
    </p>
</form>
<div class="clear"></div>

<?php

/**
 * This file contains the public facing photos.
 *
 * @link       https://joshcorne.co.uk
 * @since      1.0.0
 *
 * @package    Media_Delivery
 * @subpackage Media_Delivery/public/partials
 */
?>

<div class="media-delivery-public-container" id="media-delivery-public-images">
    <h2>Photos</h2>
    <ul class="media-delivery-img-list">
        <?php foreach ( $images as $img ) { ?>
            <li class="media-delivery-img">
                <a class="media-delivery-img-link" data-lightbox="media-delivery-img-set" href="<?php echo esc_url( wp_get_attachment_image_src( $img, 'large' )[0] ); ?>">
                    <?php echo wp_get_attachment_image( $img, 'thumbnail', array ( "class" => "media-delivery-img") ); ?>
                </a>
            </li>
        <?php } ?>
    </ul>
</div>
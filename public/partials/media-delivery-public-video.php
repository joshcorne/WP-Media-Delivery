<?php

/**
 * This file contains the public facing video.
 *
 * @link       https://joshcorne.co.uk
 * @since      1.0.0
 *
 * @package    Media_Delivery
 * @subpackage Media_Delivery/public/partials
 */
?>

<div class="media-delivery-public-container" id="media-delivery-public-video">
    <h2>Video</h2>
    <?php echo do_shortcode('[video src="'.esc_url($vid_url).'" autoplay="on" preload="auto"]') ?>
</div>
<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://joshcorne.co.uk
 * @since      1.0.0
 *
 * @package    Media_Delivery
 * @subpackage Media_Delivery/public/partials
 */
?>
<div class="media-delivery-container">
<p><button class="media-delivery-dl-btn">Download</button></p>
<p class="notice notice-error notice-alt inline hidden" id="download-error-<?php the_ID(); ?>"></p>

<?php
if ( $video ) {
    include_once 'media-delivery-public-video.php';
}

if ( $images ) {
    include_once 'media-delivery-public-images.php';
}
?>
<div class="clear"></div>
</div>
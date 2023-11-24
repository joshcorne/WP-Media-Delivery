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

<div class="wrap">
    <h2><?php _e('Media Delivery Settings', 'media-delivery') ?></h2>  
    <?php settings_errors(); ?>  
    <form method="POST" action="options.php">  
        <?php 
            settings_fields( 'media_delivery' );
            do_settings_sections( 'media_delivery' ); 
        ?>             
        <?php submit_button(); ?>  
    </form> 
</div>
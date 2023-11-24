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

<div class="submitbox" id="submitpost">
    <?php
        do_action( 'post_submitbox_start' );
    ?>
    <div id="misc-publish-actions">
        <div class="misc-pub-section" id="email-pub-action">
            <p class="notice notice-error notice-alt inline hidden" id="publish-error-msg"></p>
            <select name="send_email">
                <option value=""><?php _e( 'Choose an action...', 'media-delivery' ) ?></option>
                <option value="true"><?php _e( 'Send Email To Customer', 'media-delivery' ) ?></option>
                <option value="false"><?php _e( 'Do Not Send Email', 'media-delivery' ) ?></option>
            </select>
        </div>
    </div>
    <div id="major-publishing-actions">
        <div id="delete-pub-action">
            <?php if ( current_user_can( "delete_post", $post->ID ) ) {
                if ( !EMPTY_TRASH_DAYS ) {
                    $delete_text = __( 'Delete Permanently' );
                } else {
                    $delete_text = __( 'Move to Trash' );
                } ?>
                <a class="submitdelete deletion" href="<?php echo esc_url( get_delete_post_link( $post->ID ) ); ?>">
                    <?php echo $delete_text; ?>
                </a>
            <?php } ?>
        </div>
        <div id="publishing-action">
            <span class="spinner"></span>
            <?php
                if ( !in_array( $post->post_status, array('publish', 'future', 'private') ) || 0 == $post->ID ) {
                    if ( $can_publish ) {
                        submit_button( __( 'Add Media', 'media-delivery' ), 'primary button-large', 'publish', false, array( 'accesskey' => 'p' ) );
                    }
                } else {
                    submit_button( __( 'Update Media', 'media-delivery' ), 'primary button-large', 'publish', false, array( 'accesskey' => 'p' ) );
                } 
            ?>
        </div>
        <div class="clear"></div>
    </div>
</div>
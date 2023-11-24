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
    <h2><?php _e('Customer Media Security', 'media-delivery') ?></h2>
    <div>
        <h3>Apache</h3>
        <p>
            <?php _e('Activating the plugin should write your .htaccess 
            file automatically. At this time, it will fail to write 
            .htaccess on multi-site systems. If you are multi-site or
            it has not worked for any other reason, you will need to 
            populate it with:', 'media-delivery') ?>
        </p>
        <code style="display:block;white-space: pre-wrap;">&lt;IfModule mod_rewrite.c&gt;
    RewriteEngine On
    RewriteRule ^/wp-content/uploads/(customer_media/.+)$ /wp-content/plugins/media-delivery/includes/mac.php?file=$1 [QSD,L]';
&lt;/IfModule&gt;</code>
        <h4>mod_rewrite</h4>
        <p>
            <?php _e('If you do not have mod_rewrite enabled, then you will need to
            enable it. On Linux:', 'media-delivery') ?>
        </p>
        <code style="display:block;white-space: pre-wrap;">sudo a2enmod rewrite</code>
        <p>
            <?php _e('Followed by:', 'media-delivery') ?>
        </p>
        <code style="display:block;white-space: pre-wrap;">sudo service apache2 restart</code>
    </div>
    <div>
        <h3>Nginx</h3>
        <p>
            <?php _e('You need to add the following to your server 
            configuration:', 'media-delivery') ?>
        </p>
        <code style="display:block;white-space: pre-wrap;">location ~* /wp-content/uploads/(customer_media/.+)$ {
    rewrite ^/wp-content/uploads/(customer_media/.+)$ /wp-content/plugins/media-delivery/includes/mac.php?file=$1 last;
}</code>
    </div>
    <div>
        <h3>IIS</h3>
        <p>
            <?php _e('You need to add the following to your server 
            configuration:', 'media-delivery') ?>
        </p>
        <code style="display:block;white-space: pre-wrap;">&lt;rewrite&gt;
    &lt;rules&gt;
        &lt;rule name="RewritetoMAC.aspx"&gt;
            &lt;match url=" ^/wp-content/uploads/(customer_media/.+)$" /&gt;
            &lt;action type="Rewrite" url="/wp-content/plugins/media-delivery/includes/mac.php?file=$1" /&gt;
        &lt;/rule&gt;
    &lt;/rules&gt;
&lt;/rewrite&gt;
</code>
    </div>
    <div>
        <h3><?php _e('Other', 'media-delivery') ?></h3>
        <p>
            <?php _e('You are on your own! Although, there is not 
            specific information here, you are very welcome to email me 
            at ', 'media-delivery') ?>
            <a href="mailto:mediadelivery@joshcorne.co.uk">
                mediadelivery@joshcorne.co.uk
            </a>.
        </p>
    </div>
</div>
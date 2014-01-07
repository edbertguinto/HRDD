<?php
/**
 * Download Shortcode Uninstall
 *
 * @since 1.0
 */

// Delete option
delete_option( 'fds_version' );

// Attempt to delete the download script from wp-content
// If permissions don't allow unlink(), try at least emptying the file
if ( file_exists( WP_CONTENT_DIR . '/force-download.php' ) && ! @unlink( WP_CONTENT_DIR . '/force-download.php' ) )
	@file_put_contents( WP_CONTENT_DIR . '/force-download.php', '' );
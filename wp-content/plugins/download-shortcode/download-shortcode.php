<?php
/**
 * Plugin Name: Force Download Shortcode
 * Plugin URI: http://www.werdswords.com
 * Description: Allows you to wrap uploaded file links in a shortcode that will force a download when clicked
 * Author: Drew Jaynes (DrewAPicture)
 * Author URI: http://www.werdswords.com
 * Version: 1.1
 */

define( 'FDS_VERSION', '1.0' );
define( 'FDS_SCRIPT_PATH', WP_CONTENT_DIR . '/force-download.php' );
define( 'FDS_SCRIPT_BASE', plugin_dir_path( __FILE__ ) . 'inc/force-download.php' );

class Download_Shortcode {
	
	public $version;

	/**
	 * Init
	 *
	 * @since 1.0
	 */
	function __construct() {
		load_plugin_textdomain( 'force_download_shortcode', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

		register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );

		$this->version = get_option( 'fds_version' );
		$this->update_script( $this->version );

		add_shortcode( 'download', array( $this, 'download_shortcode_cb' ) );
		add_action( 'generate_rewrite_rules', array( $this, 'rewrite_urls' ) );
	}

	/**
	 * Update Force Download Script
	 *
	 * In kind of a roundabout way, this method attempts to automatically update the force-download.php
	 * script located in wp-content. If the plugin version is earlier than 1.0, the script doesn't exist
	 * or the script exists but is empty, it attempts to copy the contents of /inc/force-download.php over.
	 * On success, an "updated" notice will be displayed. On failure, an "error" notice will be displayed.
	 * 
	 * @since 1.0
	 *
	 * @uses FDS_VERSION constant Used to compare the plugin version to the fds_version option.
	 * @uses FDS_SCRIPT_PATH constant Used to check if the force-download.php script exists in wp-content.
	 * @uses FDS_SCRIPT_BASE constant The contents of force-download.php originate here.
	 * @return null
	 */
	function update_script( $version = false ) {
		if ( ! $version 
		 || version_compare( $version, FDS_VERSION, '<' )
		 || ( version_compare( $version, FDS_VERSION, '>=' ) && ( ! file_exists( FDS_SCRIPT_PATH ) || 0 == @filesize( FDS_SCRIPT_PATH ) ) )
		 ) {
			$content = file_exists( FDS_SCRIPT_BASE ) ? @file_get_contents( FDS_SCRIPT_BASE ) : '';

			if ( @file_put_contents( FDS_SCRIPT_PATH, $content ) ) {
				add_action( 'admin_notices', array( $this, 'updated_script' ) );
				update_option( 'fds_version', FDS_VERSION );
			} else {
				if ( file_exists( $script ) && 0 != @filesize( FDS_SCRIPT_PATH ) ) {
					add_action( 'admin_notices', array( $this, 'updated_script' ) );
					update_option( 'fds_version', FDS_VERSION );
				} else {
					add_action( 'admin_notices', array( $this, 'outdated_script' ) );
				}
			}
		}
	}
	
	/**
	 * Updated Script Notice
	 *
	 * @since 1.0
	 *
	 * @return null
	 */
	function updated_script(){
		printf( '<div class="updated"><p>%s</p></div>', __( 'The download script in your <code>/wp-content/</code> directory has been updated.', 'force_download_shortcode' ) );
	}

	/**
	 * Outdated Script Notice
	 *
	 * In the event that force-download.php doesn't exist in wp-content, this error notice will be displayed.
	 *
	 * @since 1.0
	 *
	 * @return null
	 */
	function outdated_script() {
		printf( '<div class="error"><p>%s</p></div>', __( 'There is a problem with the download script in your <code>/wp-content/</code> directory. You may need to manually upload it.', 'force_download_shortcode' ) );
	}

	/**
	 * Download Shortcode Callback
	 *
	 * @since 0.1
	 *
	 * @uses global $wp_rewrite to check if 'pretty permalinks' are in use
	 * @param $attr array Array of shortcode attributes
	 * @param $content string A string containing the link supplied to the shortcode
	 * @return the linked shortcode output. Absent a label, the URL (rewritten or not) will be used
	 */
	function download_shortcode_cb( $attr, $content ) {
		if ( empty( $content ) ) {
			return;
		} else {
			// The content_url + upload path
			$content_pre = content_url( '/' );
			// Supplied URL
			$content = esc_url( $content );

			// Remove content_url + upload path from the supplied URL
			$content = str_replace( $content_pre . $this->uploads_path( '/' ), '', $content );

			/**
			 * Disable rewriting even when 'pretty permalinks' are enabled by passing __return_false to the fds_rewrite_urls filter
			 */
			global $wp_rewrite;
			if ( $wp_rewrite->using_mod_rewrite_permalinks() && apply_filters( 'fds_rewrite_urls', true ) ) {
				// If using mod_rewrite, append content path to home_url() + rewrite path (default: 'download')
				$url = home_url( '/' ) . $this->download_path( '/' ) . $content;
			} else {
				// If not using mod_rewrite, append content path to revealed path, which is much less secure
				$url = sprintf( '%1$sforce-download.php?file=%2$s', $content_pre, $this->uploads_path( '/' ) . $content );
			}

			if ( isset( $attr['label'] ) && ! empty( $attr['label'] ) ) {
				// If we have a link label and it's not empty, assign it
				$label = esc_html( $attr['label'] );
			} else {
				// If no label, use the URL derived above
				$label = $url;
			}

			// Class is empty by default, but make it filterable
			$class = apply_filters( 'fds_download_link_class', '' );

			// Return the linked anchor
			return sprintf( '<a href="%1$s" class="%2$s">%3$s</a>', esc_url( $url ), esc_attr( $class ), esc_attr( $label ) );
		}
	}

	/**
	 * Validate a supplied path
	 *
	 * This is used to validate and return a supplied path. The output is used to append
	 * the returned path to a URL used in $this->download_path and $this->uploads_path
	 * 
	 * @since 1.0
	 *
	 * @param $path string (optional) A string containing an optional path to be appended
	 * @return $path string The returned path
	 */
	function append_path( $path = '' ) {
		if ( ! empty( $path ) && is_string( $path ) && strpos( $path, '..' ) === false ) {
			$path = '/' . ltrim( $path, '/' );
		}
		return $path;			
	}

	/**
	 * Download Rewrite Path
	 *
	 * @since 1.0
	 *
	 * @uses apply_filters() Filters the rewrite endpoint for URLs. Default is 'download'
	 * @param $path string (optional) A string that contains an optional path to be appended
	 * @return string The download rewrite path with optional path appended
	 */
	function download_path( $path = '' ) {
		return apply_filters( 'fds_download_rewrite_path', 'download' ) . $this->append_path( $path );
	}

	/**
	 * Rewrite Path
	 *
	 * The path returned by this function sets the directory relative to wp-content that
	 * the shortcode will look for files in and rewrite from if 'pretty permalinks' are enabled.
	 *
	 * @since 1.0
	 *
	 * @uses wp_upload_dir() to return an array of upload directory values including the basedir
	 * @return string Returns an unslashed string containing the upload path. Default is 'uploads'
	 */
	function uploads_path( $path = '' ) {
		$upload_dir = wp_upload_dir();
		$upload_path = str_replace( content_url( '/' ), '', $upload_dir['baseurl'] );

		return apply_filters( 'fds_download_files_directory', $upload_path ) . $this->append_path( $path );
	}

	/**
	 * WordPress in a subdirectory
	 * 
	 * If WordPress is installed in a subdirectory, we need to grab the path following the root domain 
	 * to be able to prepend it when we rewrite the URL.
	 *
	 * @since 1.1
	 *
	 * @return string The subdirectory path of the WordPress root with a trailing slash
	 */
	function optional_subdir() {
		if ( site_url() != home_url() ) {
			return str_replace( home_url( '/' ), '', site_url( '/' ) );
		}
	}
	 
	/**
	 * Rewrite Link Path
	 *
	 * This serves a dual purpose:
	 * 	1) Allows for shorter, easier-to-remember links
	 * 	2) Makes download links more secure by obfuscating the download script endpoint and rewriting
	 * 		them using the path specified by $this->download_path()
	 *
	 * @since 1.0
	 *
	 * @uses $wp_rewrite->add_external_rule() to add a new rewrite rule
	 * @param $wp_rewrite global
	 * @return null
	 */
	function rewrite_urls( $wp_rewrite ) {
		$wp_rewrite->add_external_rule( 
			sprintf( '%s/(.+)$', $this->download_path() ),
			sprintf( '%1$swp-content/force-download.php?file=%2$s/$1', $this->optional_subdir(), $this->uploads_path() )
		);
	}
	
	/**
	 * Plugin Deactivation Routine
	 *
	 * Upon deactivation, this method attempts to remove the force-download.php script in wp-content
	 *
	 * @since 1.0
	 *
	 * @uses unlink() to attempt removing the file from wp-content
	 * @uses file_put_contents() If unlink() is unsuccessful, try overwriting the file as empty.
	 * @return null
	 */
	function deactivate() {
		if ( file_exists( FDS_SCRIPT_PATH ) && ! @unlink( FDS_SCRIPT_PATH ) )
			@file_put_contents( FDS_SCRIPT_PATH, '' );
	}

}

new Download_Shortcode;
?>
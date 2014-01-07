<?php 
/**
 * This script forces download on the specified file-types.
 * It was been slightly modified to provide more security from
 * unauthorized files such as those with a .php extension being
 * downloaded, or force-download.php itself being exposed.
 *
 * Original Author: Louai Munajim
 * Source: http://elouai.com/force-download.php
 * Contributors: Jorg Weske, Rajkumar Singh, Drew Jaynes
 *
 * @since 0.1
 */

$filename = $_GET['file'];

// Check for empty value or shenanigans
if (
 	// From validate_file() in WP core
		false !== strpos( $filename, '..' )
		|| false !== strpos( $filename, './' )
		|| ':' == substr( $filename, 1, 1 )
	// Empty path
		|| $filename == ""
	// Doesn't exist
		|| ! file_exists( $filename )
	// Is a PHP file
		|| strpos( $filename, '.php' ) )
	exit();

// required for IE, otherwise Content-disposition is ignored
if ( ini_get( 'zlib.output_compression' ) )
  ini_set( 'zlib.output_compression', 'Off' );

// addition by Jorg Weske
$file_extension = strtolower( substr( strrchr( $filename, "." ), 1 ) );

switch( $file_extension ) {
  case "pdf": $ctype="application/pdf"; break;
  case "mp4": $ctype="application/octet-stream"; break;
  case "mp3": $ctype="application/octet-stream"; break;
  case "gif": $ctype="image/gif"; break;
  case "png": $ctype="image/png"; break;
  case "jpeg":
  case "jpg": $ctype="image/jpg"; break;
  default: $ctype="application/force-download";
}

header( "Pragma: public" ); // required
header( "Expires: 0" );
header( "Cache-Control: must-revalidate, post-check=0, pre-check=0" );
header( "Cache-Control: private", false ); // required for certain browsers 
header( "Content-Type: $ctype" );
// change, added quotes to allow spaces in filenames, by Rajkumar Singh
header( "Content-Disposition: attachment; filename=\"" . basename($filename) . "\";" );
header( "Content-Transfer-Encoding: binary" );
header( "Content-Length: ". filesize($filename) );
readfile( "$filename" );
	exit();

?>
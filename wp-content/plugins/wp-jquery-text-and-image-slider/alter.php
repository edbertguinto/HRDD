<?php
/**
 * ALTER.php Part of WP jQuery Text and Image Slider
 * Version 1.0
**/

#################################################################################
#																																								#
# Copyright 2012  Hit Reach  (email : info@hitreach.co.uk)											#
#																																								#
# This program is free software; you can redistribute it and/or modify					#
# it under the terms of the GNU General Public License, version 2, as						#
# published by the Free Software Foundation.																		#
# 																																							#
# This program is distributed in the hope that it will be useful,								#
# but WITHOUT ANY WARRANTY; without even the implied warranty of								#
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the									#
# GNU General Public License for more details.																	#
# 																																							#
# You should have received a copy of the GNU General Public License							#
# along with this program; if not, write to the Free Software										#
# Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA		#
#																																								#
#################################################################################
$pathToRoot = "";
$limit = 0;
$stop = 0;
#check root file exists
while( !file_exists($pathToRoot."wp-load.php") && $limit < 99 && $stop == 0 ){$limit++; $pathToRoot .= "../";	if( file_exists("wp-load.php") ){ $stop = 1; }}
if( $limit > 99 and $stop==0 ){	die("Error Finding Required Files, Please contact the author for assistance");}/*get root*/
$referer = $_SERVER['HTTP_REFERER'];
$amp = strpos($referer,"?page=wptis");
$amp = $amp+11;
if($amp != NULL){ $referer = substr($referer,0,$amp);}
require($pathToRoot.'wp-load.php');
if( !isset( $_POST['WPTIS_N0N_SEC'] ) ){ 
	wp_die("Authentication Failed"); 
}
else {	

	if( !function_exists( wp_verify_nonce) ){ 
		wp_die("Authentication Failed"); 
	}
	if ( !wp_verify_nonce( $_POST['WPTIS_N0N_SEC'], 'WPTISN' ) ) { 
		wp_die( "Authentication Failed" ); 
	}
}
if(!class_exists("WPTIS")){wp_die("Plugin Not Found"); }
$id = "";
$validation = "";
$action = "";
$redirectTo = "";
if( isset( $_POST['id'] ) ){ $id = $_POST['id']; } else { wp_die("Authentication Failed"); }
if( isset( $_POST['validation'] ) ){ $validation = $_POST['validation']; } else { wp_die("Authentication Failed"); }
if( isset( $_POST['action'] ) ){ $action = $_POST['action']; } else { wp_die("Authentication Failed"); }
if( isset( $_POST['redirectTo'] ) ){ $redirectTo = $_POST['redirectTo']; }

if( md5 ( sha1 ( NONCE_SALT . md5($id) ) ) != $validation){ wp_die("Authentication Failed"); }
if( !preg_match("/^[\d]*$/", $id) ){ wp_die("Authentication Failed"); }
#################################################################################

global $wpdb;

if($action == "edit_slide" || $action == "add_slide"):
	$name = "";
	$gallery = "";
	$type = "";
	$link = "";
	$showtime = "";
	$content = "";
	if( isset( $_POST['name'] ) ){ $name = $_POST['name']; } else { wp_die("Required Field Missing : Name"); }
	if( isset( $_POST['gallery'] ) ){ $gallery = $_POST['gallery']; } else { wp_die("Required Field Missing : Gallery"); }
	if( isset( $_POST['type'] ) ){ $type = $_POST['type']; } else { wp_die("Required Field Missing : Type"); }
	if( isset( $_POST['link'] ) ){ $link = $_POST['link']; } else { }
	if( isset( $_POST['showtime'] ) ){ $showtime = $_POST['showtime']; } else { wp_die("Required Field Missing : Show Time"); }
	if( isset( $_POST['content'] ) ){ $content = htmlspecialchars($_POST['content']); } else { wp_die("Required Field Missing : Content"); }
	############
	if( !preg_match( "/^[\d]*$/", $gallery ) || !preg_match( "/^[\d]*$/", $showtime ) ){ wp_die("Invalid Entry - Go Back and Try Again"); }else { }
	if($gallery == "-1"||$type == "-1"){ wp_die("Invalid Entry - Go Back and Try Again"); }else {}
	if( strlen($name) < 1 || strlen($content) < 1 ){ wp_die("Invalid Entry - Go Back and Try Again"); }else {}
	############
	if($action == "edit_slide" && $id != "0"){
		$wpdb->update($wpdb->prefix.WPTIS::$table_prefix."slide", array('gallery_id' => $gallery, 'name' => $name, 'type' => $type, 'link'=>$link, 'showtime'=>$showtime, 'content'=>$content), array('id'=>$id), array('%d','%s','%s','%s','%d','%s'), array('%d'));
		header("location:$referer$redirectTo&status=slide_edited&gallery_id=$gallery");
	}
	elseif($action == "add_slide"){
		$wpdb->insert($wpdb->prefix.WPTIS::$table_prefix."slide", array('gallery_id' => $gallery, 'name' => $name, 'type' => $type, 'link'=>$link, 'showtime'=>$showtime, 'content'=>$content), array('%d','%s','%s','%s','%d','%s'));
		$insertid = $wpdb->insert_id;
		header("location:$referer$redirectTo&id=$insertid&status=slide_added&gallery_id=$gallery");
	}
	else{header("location:$referer&WPTIS_mode=error");}
elseif($action == "add_gallery" || $action == "edit_gallery"):
	$name = "";
	$description = "";
	$animation = "";
	$width = "";
	$height = "";
	if( isset( $_POST['name'] ) ){ $name = $_POST['name']; } else { wp_die("Required Field Missing : Name"); }
	if( isset( $_POST['description'] ) ){ $description = $_POST['description']; } else { }
	if( isset( $_POST['animation'] ) ){ $animation = $_POST['animation']; } else { wp_die("Required Field Missing : Animation"); }
	if( isset( $_POST['width'] ) ){ $width = $_POST['width']; } else { wp_die("Required Field Missing : Width"); }
	if( isset( $_POST['height'] ) ){ $height = $_POST['height']; } else { wp_die("Required Field Missing : Height"); }
	############
	if( !preg_match( "/^[\d]*$/", $width ) || !preg_match( "/^[\d]*$/", $height ) ){ wp_die("Invalid Entry - Go Back and Try Again"); }else { }
	if( $animation == "-1" ){ wp_die("Invalid Entry - Go Back and Try Again"); }else {}
	if( strlen( $name ) < 1 ){ wp_die("Invalid Entry - Go Back and Try Again"); }else {}
	############
	if($action == "edit_gallery" && $id != "0"){
		$wpdb->update($wpdb->prefix.WPTIS::$table_prefix."gallery", array('name' => $name, 'description' => $description, 'animation'=>$animation, 'width'=>$width, 'height'=>$height), array('id'=>$id), array('%s','%s','%s','%d','%d'), array('%d'));
		header("location:$referer$redirectTo&status=gallery_edited");
	}
	elseif($action == "add_gallery"){
		$wpdb->insert($wpdb->prefix.WPTIS::$table_prefix."gallery", array('name' => $name, 'description' => $description, 'animation'=>$animation, 'width'=>$width, 'height'=>$height), array('%s','%s','%s','%d','%d'));
		$insertid = $wpdb->insert_id;
		header("location:$referer$redirectTo&id=$insertid&status=gallery_added");
	}
	else{header("location:$referer&WPTIS_mode=error");}
elseif($action == "delete_gallery" || $action == "delete_slide"):
	$sql = "";
	$message = "";
	if($action == "delete_gallery"){
		$sql = "DELETE FROM ".$wpdb->prefix.WPTIS::$table_prefix."gallery ";
		$message = "gallery_deleted";
	}
	elseif($action == "delete_slide"){
		$sql = "DELETE FROM ".$wpdb->prefix.WPTIS::$table_prefix."slide ";
		$message = "slide_deleted";
	}
	else{}
	$sql = $wpdb->prepare($sql."WHERE id=%d",$id);
	$wpdb->query($sql);
	header("location:$referer$redirectTo&status=$message");
elseif($action == "group_delete_gallery" || $action == "group_delete_slide"):
	$sql = "";
	$message = "";
	$group = $_POST['group'];
	if(sizeof($group) == "0"){
		$message = "nothing_selected";
	}
	else{
		if($action == "group_delete_gallery"){
			$sql = "DELETE FROM ".$wpdb->prefix.WPTIS::$table_prefix."gallery ";
			$message = "gallery_group_deleted";
		}
		elseif($action == "group_delete_slide"){
			$sql = "DELETE FROM ".$wpdb->prefix.WPTIS::$table_prefix."slide ";
			$message = "slide_group_deleted";
		}
		else{}
		$group = array_keys($group);
		foreach($group as $row){
			if( !preg_match("/^[\d]*$/", $row) ){ wp_die("You are not allowed to do that"); }
		}
		$group = implode(",",$group);
		$sql = $wpdb->prepare($sql."WHERE id IN ($group)");
		$wpdb->query($sql);
	}
	header("location:$referer$redirectTo&status=$message");
else:
	header("location:$referer&WPTIS_mode=error");
endif;

?>
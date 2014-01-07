<?php
/**
 * Plugin Name: WP jQuery Text and Image Slider
 * Author: Hit Reach
 * Version: 1.2
 * Description: An extensible jQuery text and image slider for WordPress posts, pages and widgets
 * Author URI: http://www.hitreach.co.uk
 * Plugin URI: http://hitr.ch/wptis
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

add_action('init', "loadWPTIS");
function loadWPTIS(){	global $WPTIS; $WPTIS = new WPTIS();}
register_activation_hook(__FILE__,  array("WPTIS", "operation_pre_checks"));
add_shortcode("WPTIS_gallery", array("WPTIS", "component_shortcode"));
add_shortcode("wptis_gallery", array("WPTIS", "component_shortcode"));
define("WPTIS_URL",  WP_PLUGIN_URL . '/' . plugin_basename( dirname(__FILE__) ) . '/');
class WPTIS {
	/* VARIABLES */
	private static $plugin_page_title = "WP jQuery Text and Image Slider";
	private static $plugin_menu_title = "WP jQuery Text and Image Slider";
	private static $database_version = "1";
	private static $option_name = "WPTIS_OPTIONS";
	private static $plugin_version = "1.1";
	public static $table_prefix = "WPTIS_";
	private static $slide_types = array();
	private static $animation_types = array();
	private static $basename = WPTIS_URL;
	private $current_page_title = "";
	public $options = array();
	public $fade_options = array();
	
	/* INIT */
	public function __construct(){
		add_action("admin_menu", array("WPTIS", "operation_admin_menu_register"));
	}	
	
/******************************************************************************************************************************************
 
                                                                  OPERATIONS SECTION

******************************************************************************************************************************************/
	
	/**
	* Check Plugin options
	* Since: Production
	**/
	public function operation_pre_checks(){
		$registered_options = get_option(self::$option_name);
		if(is_serialized($registered_options)){
			$registered_options = unserialize($registered_options);
		}
		if($registered_options["database_version"] != self::$database_version){
			self::operation_update_database();
			$registered_options["database_version"] = self::$database_version;
		}
		if($registered_options["plugin_version"] != self::$plugin_version){
			$registered_options["plugin_version"] = self::$plugin_version;
		}
		if($registered_options["slide_types"] == ""|| !isset($registered_options["slide_types"])){
			$registered_options["slide_types"] = self::$slide_types;
		}
		if( $registered_options["animation_types"] == "" || !isset($registered_options["animation_types"])){
			$registered_options["animation_types"] = self::$animation_types;
		}
		update_option(self::$option_name, $registered_options);
		self::public_register_animation("FADE",WPTIS_URL.'fade/fade.php',WPTIS_URL.'fade/fade.css',WPTIS_URL.'fade/fade.js');
		self::public_register_animation("FADE WITH BULLETS",WPTIS_URL.'fade-bulleted/fade-bulleted.php',WPTIS_URL.'fade-bulleted/fade-bulleted.css',WPTIS_URL.'fade-bulleted/fade-bulleted.js');
		self::public_register_animation("Custom Fade",WPTIS_URL.'custom-fade/custom-fade.php',"",WPTIS_URL.'custom-fade/custom-fade.js.php');
		self::public_register_slide_type("HTML", WPTIS_URL.'processor/html.php');
		self::public_register_slide_type("IMAGE", WPTIS_URL.'processor/image.php');
	}
	
	/**
	* Check DB version for upgrade
	* Since: Production
	**/
	public static function operation_update_database(){
		global $wpdb;
		$gallery = "CREATE TABLE ".$wpdb->prefix.self::$table_prefix."gallery (
			id int NOT NULL AUTO_INCREMENT,
			name text NOT NULL,
			description text NOT NULL,
			animation text NOT NULL,
			width int NOT NULL,
			height int NOT NULL,
			PRIMARY KEY(id)
		);";
		$slide = "CREATE TABLE ".$wpdb->prefix.self::$table_prefix."slide (
			id int NOT NULL AUTO_INCREMENT,
			gallery_id int NOT NULL,
			name text NOT NULL,
			type text NOT NULL,
			link text NOT NULL,
			content text NOT NULL,
			showtime int NOT NULL,
			PRIMARY KEY(id),
			FOREIGN KEY(gallery_id) REFERENCES ".$wpdb->prefix.self::$table_prefix."gallery(id) ON DELETE CASCADE ON UPDATE CASCADE
		);";
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($gallery);
		dbDelta($slide);
	}
	
	/**
	* Loads the plugin options
	* Since: Production
	**/
	public static function operation_load_options(){
		#global call to self object
		global $WPTIS;
		$options = get_option(self::$option_name);
		$fade_options = get_option(self::$option_name."-custom-fade");
		if(is_serialized($options)){
			$options = unserialize($options);
		}
		if(is_serialized($fade_options)){
			$fade_options = unserialize($fade_options);
		}
		$WPTIS->options = $options;
		$WPTIS->fade_options = $fade_options;
	}
	
	/**
	* Registers the admin menus for the plugin
	* Since: Production
	**/
	public function operation_admin_menu_register(){
		$plugin_page = add_menu_page( self::$plugin_page_title, self::$plugin_menu_title, "edit_posts", "wptis", array("WPTIS", "operation_main_admin_menu"), WPTIS_URL."WPTIS.png" );
		add_submenu_page("wptis", "WPTIS - Custom Fade Settings", "Custom Fade", "edit_posts", "WPTIS-custom-fade", array("WPTIS", "operation_custom_fade_submenu"));
		add_submenu_page("wptis", "WPTIS - Plugin Information", "Plugin Information", "edit_posts", "WPTIS-information", array("WPTIS", "operation_information_submenu"));
	}	
	
	/**
	* Main Admin Menu For editing settings
	* Since: Production
	**/
	public function operation_main_admin_menu(){
		if( current_user_can( "edit_posts" ) ){
			self::design_styles();
			self::design_head();
			self::layout_mc();
			self::design_foot();
		}
		else{}
	}
	
	/**
	* Sub Admin Menu for showing plugin information
	* Since: Production
	**/
	public function operation_information_submenu(){
		if( current_user_can( "edit_posts" ) ){
			self::design_styles();
			self::design_head();
			self::layout_information();
			self::design_foot();
		}
		else{}
	}
	
	/**
	* Sub Admin Menu For editing Fade options
	* Since: Production
	**/
	public function operation_custom_fade_submenu(){
		if( current_user_can( "edit_posts" ) ){
			self::design_styles();
			self::design_head();
			self::layout_option_fade();
			self::design_foot();
		}
		else{}
	}
	
/******************************************************************************************************************************************
 
                                                                  LAYOUTS SECTION

******************************************************************************************************************************************/

	private static function layout_mc(){
		#global call to self object
		global $WPTIS;
		$mode = "";
		if( isset($_GET['WPTIS_mode'] ) ){ $mode = $_GET['WPTIS_mode']; }
		switch( $mode ){
			case "" :
				$WPTIS->current_page_title = "All Galleries";
				self::design_subtitle();
				self::content_show_galleries();
				break;
			case "new_gallery" :
				$WPTIS->current_page_title = "Add New Gallery";
				self::design_subtitle();
				self::content_new_gallery();
				break;
			case "edit_gallery" :
				$WPTIS->current_page_title = "Gallery Editor";
				self::design_subtitle();
				self::content_edit_gallery();
				break;
			case "new_slide" :
				$WPTIS->current_page_title = "Add New Slide";
				self::design_subtitle();
				self::content_new_slide();
				break;
			case "edit_slide" :
				$WPTIS->current_page_title = "Slide Editor";
				self::design_subtitle();
				self::content_edit_slide();
				break;
			default:
				echo "<h3 class='error'>Action Not Recognised</h3>";
				break;
		}		
	}
	
	private static function layout_option_fade(){
		#global call to self object
		global $WPTIS;
		$mode = "";
		$WPTIS->current_page_title = "Custom Fade Options";
		self::design_subtitle();
		self::content_fade_options();
	}
	
	private static function layout_information(){
		#global call to self object
		global $WPTIS;
		$mode = "";
		$WPTIS->current_page_title = "Plugin Information";
		self::design_subtitle();
		self::content_information();
	}
	
/******************************************************************************************************************************************
 
                                                                  CONTENT SECTION

******************************************************************************************************************************************/

	/**
	* Show information about the plugin and usage
	* Since: Production
	* Status: 100%
	**/
	private static function content_information(){
		?>
		<p>Thank you for choosing the Reach Image Slider</p>
		<p>Please take a minute to review the plugin on the WordPress Plugin Repository and show your support though the options on the right</p>
		<p>This plugin is extensible, you can register your own slide and animation types to customise the look of the gallery. <strong>From time to time, we may offer additional animation types on our site. Be sure to check back and see what new ones are available!</strong></p>
		<h2>Using This Plugin</h2>
		<h3>Adding a Gallery</h3>
		<p>Adding a new gallery is easy, Go to the <a href='admin.php?page=wptis' target="_blank">gallery overview page</a> and select &quot;Add New Gallery&quot;.</p>
<p>Add the details of your gallery, and select the animation type, then click submit. Simple!</p>
		<h3>Adding a New Slide</h3>
		<p>It is simple to add a new slide, Go to the <a href='admin.php?page=wptis&WPTIS_mode=new_slide' target="_blank">slide addition page</a> and fill in the form making sure to select the Gallery and Slide Type then click submit</p>
		<h3>Gallery options explained</h3>
		<p>The following options are available when adding a new gallery:</p>
<p><strong>Gallery Name</strong>: This is used for reference, However animation types may incorporate it into their design<br />
		<strong>Gallery Description</strong> (optional): A text area to allow you to keep track of what is inside the gallery. animation types may also incorporate this into their design<br />
		<strong>Animation Type</strong>: The type of animation you wish to use for this gallery, initially there are 3 on the system: fade, fade with bullets and a custom fade, but you can add your own later if required<br />
<strong>Gallery Width </strong>:the width of the slides in the gallery in pixels, some animation types use it to keep all slides uniform in width<br />		
<strong>Slide Height</strong>:the height of the slides in the gallery in pixels, some animation types use it to keep all slides uniform in height</p>
		<h3>Slide options explained</h3>
		<p>The following options are available when adding a new slide:</p>
<p><strong>Slide Name</strong>: This is used for reference, however with the WPTIS image type, it also becomes the alt attribute<br />
		<strong>Gallery:</strong> This is the gallery to add the slide to, you need to have added a gallery to add a slide<br />
		<strong>Slide Type</strong>: This is the type of slide, it is used to decode the content intered into a usable slide format, there are 2 default ones, HTML and Image, but you can add your own<br />
<strong>Slide Link </strong>(optional): This is the link to apply to the slide if you want it to go somewhere!<br />		
<strong>Slide Show Time</strong>:	Some	animations allow you to set the length of time (in milliseconds) that the slide is shown on screen before transition begins<br />
<strong>Slide Content: </strong>This is the most inportant field, it is used to add your content to the side, the HTML slide type accepts raw HTML where as the Image slide type only accepts the url to the image.</p>
<h2>Add Your Own Animation To The Plugin</h2>
<p>You can register your own animation type to the plugin by calling the <code>WPTIS::public_register_animation</code> function in the plugin with the following 4 parameters:</p>
<p><strong>NAME</strong>: the name of the animation, its used in the dropdown and also to unregister the animation if required, try and keep it unique<br/>
<strong>PATH TO PHP FILE</strong>: the path to the PHP file that will output the gallery onto the page, it needs to be on the same site as the blog installation.<br/>
<strong>PATH TO CSS FILE</strong> (optional): the path to the CSS file that has the slide designs, it needs to be on the same site as the blog installation.<br/>
<strong>PATH TO JS FILE</strong> (optional): the path to the Javascript file that has the slide animation scripts, it needs to be on the same site as the blog installation.<br/>
The CSS and JS files are optional in case the CSS and JS are included in the PHP file</p>
<p>In the PHP for your animation, the animation must be inside a function called <code>output</code> for it to work.  <code>output</code> also accepts 3 arguments, $ID is the gallery id, $SLIDES is the array of slides, $GALLERY is the information about the gallery.</p>
<p>To remove your animation from the system, call the function <code>WPTIS::public_unregister_animation</code> with the name of the animation as a parameter. This can be used to remove any animation you no longer need on the system.  The core animation types are automatically re-added on plugin activation</p>
<h2>Add Your Own Slide Type To The Plugin</h2>
<p>You can register your own slide types to the system as well, so where we have already added Image and HTML, you may wish add others, or your own formatting of the image/html types.  To add your own type, simply call <code>WPTIS::public_register_slide_type</code> with the following parameters:</p>
<p><strong>NAME</strong>: the name of the slide type, its used in the dropdown and also to unregister the slide type if required, try and keep it unique<br/>
<strong>PROCESSING FILE</strong>: This is the url to the file that processes the slide content into the final slide, the processing function must be called the same as the NAME but with any spaces changed to _'s</p>
<p>The function inside the processing file must be able to accept 2 arguments, $slide is an array of information about the slide and $gallery is an array of information about the gallery.</p>
<p>To remove your slide type from the system, call the function <code>WPTIS::public_unregister_slide_type</code> with the name of the slide type as a parameter. This can be used to remove any slide types you no longer need on the system.  The core slide type types are automatically re-added on plugin activation</p>
<h2>Adding your gallery to a post or page</h2>
<p>To add your gallery to a post or page, simply use the shortcode: <code>[WPTIS_gallery id=XX]</code> where XX is the id of your gallery obtained from the plugin gallery view page.</p>

	<?php
	}

	/**
	* Change the settings for the custom fade animation
	* Since: Production
	* Status: 100%
	**/
	private static function content_fade_options(){
	?>
<p class='description'>Change the options relating to the Custom Fade animation type. <strong>These changes affect all galleries with the custom fade animation type.</strong></p>
		<form action='options.php' method="post" id='options_form'>
			<?php wp_nonce_field('update-options');?>
			<input type="hidden" name="action" value="update" />
			<input type="hidden" name="page_options" value="<?php echo self::$option_name; ?>-custom-fade" />
			<?php $thisoptprefix = self::$option_name . "-custom-fade[custom_fade]";
			global $WPTIS;
			$options = $WPTIS->fade_options;
			$options = $options['custom_fade'];
			if( isset( $options['showbullets'] ) ){ $showbullets = $options['showbullets']; } else{ $showbullets = "1"; }
			if( isset( $options['bulletshape'] ) ){ $bulletshape = $options['bulletshape']; } else{ $bulletshape = "square"; }
			if( isset( $options['bulletposition'] ) ){ $bulletposition = $options['bulletposition']; } else{ $bulletposition = "outside"; }
			if( isset( $options['bulletbg'] ) ){ $bulletbg = $options['bulletbg']; } else{ $bulletbg = "#cccccc"; }
			if( isset( $options['bulletborder'] ) ){ $bulletborder = $options['bulletborder']; } else{ $bulletborder = "#cccccc"; }
			if( isset( $options['bulletactivebg'] ) ){ $bulletactivebg = $options['bulletactivebg']; } else{ $bulletactivebg = "#ffffff"; }
			if( isset( $options['bulletactiveborder'] ) ){ $bulletactiveborder = $options['bulletactiveborder']; } else{ $bulletactiveborder = "#cccccc"; }
			if( isset( $options['bulletsize'] ) ){ $bulletsize = $options['bulletsize']; } else{ $bulletsize = "11"; }
			if( isset( $options['bulletalign'] ) ){ $bulletalign = $options['bulletalign']; } else{ $bulletalign = "left"; }
			if( isset( $options['transitiontime'] ) ){ $transitiontime = $options['transitiontime']; } else{ $transitiontime = "250"; }
			?>
			<table cellpadding='5' cellspacing='3' width='100%'>
				<tr>
					<th width='23%'><label for='<?php echo $thisoptprefix;?>[showbullets]'>Show Navigation Bullets</label></th>
					<td width='20%'><input type='radio' name='<?php echo $thisoptprefix;?>[showbullets]' value='1' <?php if($showbullets == '1'):echo "checked='checked'";endif; ?> />Yes | <input type='radio' name='<?php echo $thisoptprefix;?>[showbullets]' value='0' <?php if($showbullets == '0'):echo "checked='checked'";endif; ?> />No</td>
					<td width='57%'></td>
				</tr>
				<tr>
					<th><label for='<?php echo $thisoptprefix;?>[bulletshape]'>Navigation Bullet Shape</label></th>
					<td><input type='radio' name='<?php echo $thisoptprefix;?>[bulletshape]' value='square' <?php if($bulletshape == 'square'):echo "checked='checked'";endif; ?> />Square | <input type='radio' name='<?php echo $thisoptprefix;?>[bulletshape]' value='circle' <?php if($bulletshape == 'circle'):echo "checked='checked'";endif; ?> />Circle</td>
					<td><em>Circle navigation bullets are only available in browsers that support the CSS3 Border Radius Module</em></td>
				</tr>
				<tr>
					<th><label for='<?php echo $thisoptprefix;?>[bulletposition]'>Navigation Bullet Position</label></th>
					<td><input type='radio' name='<?php echo $thisoptprefix;?>[bulletposition]' value='inside' <?php if($bulletposition == 'inside'):echo "checked='checked'";endif; ?> />Inside | <input type='radio' name='<?php echo $thisoptprefix;?>[bulletposition]' value='outside' <?php if($bulletposition == 'outside'):echo "checked='checked'";endif; ?> />Outside</td>
					<td><em>Position the bullet navigation inside or outside of the box</em></td>
				</tr>
				<tr>
					<th><label for='<?php echo $thisoptprefix;?>[bulletbg]'>Navigation Bullet Color</label></th>
					<td><input type='color' name='<?php echo $thisoptprefix;?>[bulletbg]' value='<?php echo $bulletbg;?>' maxlength="7" size='8' id='nav_b_col' /></td>
					<td><em>Six Digit HEX code with the #</em></td>
				</tr>
				
				<tr>
					<th><label for='<?php echo $thisoptprefix;?>[bulletborder]'>Navigation Bullet Border Color</label></th>
					<td><input type='color' name='<?php echo $thisoptprefix;?>[bulletborder]' value='<?php echo $bulletborder;?>' maxlength="7" size='8' id='nav_b_bor' /></td>
					<td><em>Six Digit HEX code with the #</em></td>
				</tr>
				<tr>
					<th><label for='<?php echo $thisoptprefix;?>[bulletactivebg]'>Active Navigation Bullet Color</label></th>
					<td><input type='color' name='<?php echo $thisoptprefix;?>[bulletactivebg]' value='<?php echo $bulletactivebg;?>' maxlength="7" size='8' id='nav_ba_col' /></td>
					<td><em>Six Digit HEX code with the #</em></td>
				</tr>
				<tr>
					<th><label for='<?php echo $thisoptprefix;?>[bulletactiveborder]'>Active Navigation Bullet Border Color</label></th>
					<td><input type='color' name='<?php echo $thisoptprefix;?>[bulletactiveborder]' value='<?php echo $bulletactiveborder;?>' maxlength="7" size='8' id='nav_ba_bor' /></td>
					<td><em>Six Digit HEX code with the #</em></td>
				</tr>
				<tr>
					<th><label for='<?php echo $thisoptprefix;?>[bulletsize]'>Bullet Size</label></th>
					<td><input type='number' name='<?php echo $thisoptprefix;?>[bulletsize]' value='<?php echo $bulletsize;?>' maxlength="2" size='5' id='nav_b_siz' /></td>
					<td><em>Pixel width if using square bullets, or diameter if using circle bullets</em></td>
				</tr>
				<tr>
					<th><label for='<?php echo $thisoptprefix;?>[bulletalign]'>Navigation Bullet Alignment</label></th>
					<td><input type='radio' name='<?php echo $thisoptprefix;?>[bulletalign]' value='left' <?php if($bulletalign == 'left'):echo "checked='checked'";endif; ?> />Left | <input type='radio' name='<?php echo $thisoptprefix;?>[bulletalign]' value='right' <?php if($bulletalign == 'right'):echo "checked='checked'";endif; ?> />Right</td>
					<td><em>Alignment for the navigation bullets</em></td>
				</tr>
				<tr>
					<th><label for='<?php echo $thisoptprefix;?>[transitiontime]'>Transition Time</label></th>
					<td><input type='number' name='<?php echo $thisoptprefix;?>[transitiontime]' value='<?php echo $transitiontime;?>' maxlength="5" size='5' id='slide_transition' /></td>
					<td><em>The length of time it takes for the slide to fade out or in, in milliseconds</em></td>
				</tr>
			</table>
			<p><input type='submit' class='button-primary' value='Save Settings' /></p>
		</form>
		<script type="text/javascript">
		var colorReg = "^#[0-9a-fA-F]{6}$";
		var colorReg = new RegExp(colorReg);
		var numReg = "^[0-9]{1,2}$";
		var numReg = new RegExp(numReg);
		//colortest  nav_b_col nav_b_bor nav_ba_col nav_ba_bor
		//numtest nav_b_siz
		jQuery(document).ready(function($){
			$("#options_form").submit(function(){
				if( !color_test( $("#nav_b_col").attr("value") ) ){ alert("Invalid Color for Navigation Bullet Color"); return false; }
				if( !color_test( $("#nav_b_bor").attr("value") ) ){ alert("Invalid Color for Navigation Bullet Border Color"); return false; }
				if( !color_test( $("#nav_ba_col").attr("value") ) ){ alert("Invalid Color for Navigation Active Bullet Color"); return false; }
				if( !color_test( $("#nav_ba_bor").attr("value") ) ){ alert("Invalid Color for Navigation Active Bullet Border Color"); return false; }
				if( !numtest( $("#nav_b_siz").attr("value") ) ){ alert("Invalid Number for Navigation Bullet Size"); return false; }
				return true;
			});
			
			function color_test(value){
				if(colorReg.test(value)){
					return true;
				}
				else{
					return false;
				}
			}
			
			function numtest(value){
				if(numReg.test(value) && value > 0 && value < 100){
					return true;
				}
				else{
					return false;
				}
			}
						
		});
		
		</script>
	<?php
		
	}

	/**
	* Show all Galleries in the system
	* Since: Production
	* Status: 100%
	**/
	private static function content_show_galleries(){
		#global call to self object
		global $WPTIS;
		global $wpdb;
		$hrefs = array(
		"id" 					=> "?page=wptis&order=desc&orderby=id",
		"name"				=> "?page=wptis&order=desc&orderby=name",
		"description" => "?page=wptis&order=desc&orderby=description",
		"animation" 	=> "?page=wptis&order=desc&orderby=animation",
		"width" 			=> "?page=wptis&order=desc&orderby=width",
		"height" 			=> "?page=wptis&order=desc&orderby=height",
		"slide" 			=> "?page=wptis&order=desc&orderby=slide"
		);
		$sorts = array(
		"id" 					=> "sortable asc",
		"name"				=> "sortable asc",
		"description" => "sortable asc",
		"animation" 	=> "sortable asc",
		"width" 			=> "sortable asc",
		"height" 			=> "sortable asc",
		"slide" 			=> "sortable asc"
		);
		
		##SORT ORDER DATA
		$orderby = "";
		if( isset( $_GET['order'] ) && isset( $_GET['orderby'] ) ) {
			$order =  $_GET['order'];
			$by =  $_GET['orderby'];
			$sorts[$by] = "sorted ".$order;
			if( $order == "desc" ){
				$hrefs[$by] = "?page=wptis&order=asc&orderby=$by";
			}
			else{
				$hrefs[$by] = "?page=wptis&order=desc&orderby=$by";
			}
			if($by == "id" ||$by == "name" ||$by == "description" ||$by == "animation" ||$by == "width" ||$by == "height"){
				$orderby = "ORDER BY a.$by $order";
			}
			elseif($by == "slide"){
				$orderby = "ORDER BY count $order";
			}
		}
		##QUERY
		$gallery_sql = "SELECT a.*, count(b.id) as 'count' FROM " . $wpdb->prefix . self::$table_prefix . "gallery a LEFT JOIN " . $wpdb->prefix . self::$table_prefix . "slide b ON (a.id = b.gallery_id) GROUP BY a.id $orderby ";
		$result = $wpdb->get_results($gallery_sql);
		$out = "";
		if(sizeof($result) < 1):
			$out = "<tr><td colspan='8' align='center'> <h3 class='warning'>There are no galleries in the system</h3><a href='?page=wptis&WPTIS_mode=new_gallery' class='add_new_button'>Add New Gallery</a></td></tr>";
		else:
		##ORGANISE OUTPUT
			foreach($result as $the_result):
				$out .= "<tr><th scope='row' class='check-column'><input type='checkbox' name='group[".$the_result->id."]' value='1' /></th><td>".$the_result->id."</td><td><a href='?page=wptis&WPTIS_mode=edit_gallery&id=".$the_result->id."' class='button_link'>".$the_result->name."</a></td><td>".$the_result->description."</td><td>".$the_result->animation."</td><td>".$the_result->width."</td><td>".$the_result->height."</td></td><td>".$the_result->count."</td></tr>";
			endforeach;
		endif;
		##DO OUTPUT
		?>
		<form action='<?php echo self::$basename; ?>alter.php' method='post' onsubmit='return confirm("Are you sure you want to delete these slides?\nPress Ok to continue or cancel to go back");'>
		<input type='hidden' name='action' value='group_delete_gallery' />
		<input type='hidden' value='' name='redirectTo' />
		<input type='hidden' value='0' name='id' />
		<input type='hidden' value='<?php echo md5 ( sha1 ( NONCE_SALT . md5( "0" ) ) ); ?>'  name='validation'/>
		<?php wp_nonce_field( 'WPTISN', 'WPTIS_N0N_SEC' ); ?>
		<p><input type='submit' value='Delete Selected Galleries' class='button-secondary' /> | <a href='?page=wptis&WPTIS_mode=new_gallery' class='button_link'><input type='button' value='Add New Gallery' class='button-secondary'/></a></p>
		<table class="widefat fixed" cellspacing="0" style='width:100%'>
			<thead>
				<tr>
					<th scope="col" class="manage-column column-cb check-column" width='3%'><input type="checkbox"></th>
					<th scope="col" class="manage-column column-title <?php echo $sorts["id"] ?>" width='5%'><a href='<?php echo $hrefs["id"] ?>'><span>ID</span> <span class="sorting-indicator"></span></a></th>
					<th scope="col" class="manage-column column-title <?php echo $sorts["name"] ?>" width='20%'><a href='<?php echo $hrefs["name"] ?>'><span>Name</span> <span class="sorting-indicator"></span></a></th>
					<th scope="col" class="manage-column column-title <?php echo $sorts["description"] ?>" width='33%'><a href='<?php echo $hrefs["description"] ?>'><span>Description</span> <span class="sorting-indicator"></span></a></th>
					<th scope="col" class="manage-column column-title <?php echo $sorts["animation"] ?>" width='14%'><a href='<?php echo $hrefs["animation"] ?>'><span>Animation</span> <span class="sorting-indicator"></span></a></th>
					<th scope="col" class="manage-column column-title <?php echo $sorts["width"] ?>" width='8%'><a href='<?php echo $hrefs["width"] ?>'><span>Width</span> <span class="sorting-indicator"></span></a></th>
					<th scope="col" class="manage-column column-title <?php echo $sorts["height"] ?>" width='8%'><a href='<?php echo $hrefs["height"] ?>'><span>Height</span> <span class="sorting-indicator"></span></a></th>
					<th scope="col" class="manage-column column-title <?php echo $sorts["slide"] ?>" width='11%'><a href='<?php echo $hrefs["slide"] ?>'><span>Slide Count</span> <span class="sorting-indicator"></span></a></th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<th scope="col" class="manage-column column-cb check-column"><input type="checkbox"></th>
					<th scope="col" class="manage-column column-title <?php echo $sorts["id"] ?>"><a href='<?php echo $hrefs["id"] ?>'><span>ID</span> <span class="sorting-indicator"></span></a></th>
					<th scope="col" class="manage-column column-title <?php echo $sorts["name"] ?>"><a href='<?php echo $hrefs["name"] ?>'><span>Name</span> <span class="sorting-indicator"></span></a></th>
					<th scope="col" class="manage-column column-title <?php echo $sorts["description"] ?>"><a href='<?php echo $hrefs["description"] ?>'><span>Description</span> <span class="sorting-indicator"></span></a></th>
					<th scope="col" class="manage-column column-title <?php echo $sorts["animation"] ?>"><a href='<?php echo $hrefs["animation"] ?>'><span>Animation</span> <span class="sorting-indicator"></span></a></th>
					<th scope="col" class="manage-column column-title <?php echo $sorts["width"] ?>"><a href='<?php echo $hrefs["width"] ?>'><span>Width</span> <span class="sorting-indicator"></span></a></th>
					<th scope="col" class="manage-column column-title <?php echo $sorts["height"] ?>"><a href='<?php echo $hrefs["height"] ?>'><span>Height</span> <span class="sorting-indicator"></span></a></th>
					<th scope="col" class="manage-column column-title <?php echo $sorts["slide"] ?>"><a href='<?php echo $hrefs["slide"] ?>'><span>Slide Count</span> <span class="sorting-indicator"></span></a></th>
				</tr>
			</tfoot>
			<tbody>
				<?php echo $out; ?>
			</tbody>
		</table>
		<p><input type='submit' value='Delete Selected Galleries' class='button-secondary' /> | <a href='?page=wptis&WPTIS_mode=new_gallery' class='button_link'><input type='button' value='Add New Gallery' class='button-secondary'/></a></p>
		</form>
		<?php
	}
	
	/**
	* Add new gallery into the system
	* Since: Production
	* Status: 100%
	**/
	private static function content_new_gallery(){
		#global call to self object
		global $WPTIS;
		global $wpdb;
		$animations = $WPTIS->options['animation_types'];
		$animations =  array_keys($animations);
		$select_options = "";
		foreach($animations as $animation){
			$select_options .= "<option value='$animation'>$animation</option>\n";
		}
		?>
		<div class='escape_link'><a href='?page=wptis' class='button_link'><input type='button' class='button-secondary' value='Back To All Galleries'/></a></div>
		<form action='<?php echo self::$basename; ?>alter.php' method="post" onsubmit="return validate_form();">
			<?php wp_nonce_field( 'WPTISN', 'WPTIS_N0N_SEC' ); ?>
			<input type='hidden' value='add_gallery' name='action' />
			<input type='hidden' value='0' name='id' />
			<input type='hidden' value='&WPTIS_mode=edit_gallery' name='redirectTo' />
			<input type='hidden' value='<?php echo md5 ( sha1 ( NONCE_SALT . md5("0") ) ); ?>'  name='validation'/>
			<table width='90%' cellpadding='6' cellspacing='3' border='0'>
				<tr>
					<th scope="row" width='25%' align='right'><label for='name' class='required'>Gallery Name</label></th>
					<td width='45%'><input type='text' name='name' id='form_name' /></td>
					<td width='20%'><span id='form_name_validation' class='validation_message'>This Field Is Required</span></td>
				</tr>
				<tr>
					<th scope="row" align='right' valign="top"><label for='description'>Gallery Description</label></th>
					<td><textarea name='description' rows='3' id='form_description'></textarea></td>
					<td><span id='form_description_validation' class='validation_message'>This Field Is Required</span></td>
				</tr>
				<tr>
					<th scope="row" align='right'><label for='animation' class='required'>Animation Type</label></th>
					<td><select name='animation' id='form_animation'><option value='-1'>-- Please Select --</option><?php echo $select_options ?></select></td>
					<td><span id='form_animation_validation' class='validation_message'>This Field Is Required</span></td>
				</tr>
				<tr>
					<th scope="row" align='right'><label for='width' class='required'>Gallery Width</label></th>
					<td><input type='text' name='width' id='form_width' /> 
					px</td>
					<td><span id='form_width_validation' class='validation_message'>This Field Is Required</span></td>
				</tr>
				<tr>
					<th scope="row" align='right'><label for='height' class='required'>Gallery Height</label></th>
					<td><input type='text' name='height' id='form_height' />
					px
					</td>
					<td><span id='form_height_validation' class='validation_message'>This Field Is Required</span></td>
				</tr>
				<tr>
					<td colspan='2' align="center"><input type='submit' value='Add New Gallery' id='form_submit' class='button-primary' disabled="disabled" /></td>
					<td><noscript><span id='form_height_validation' class='validation_message' style='display:block;'>Javascript Is Required</span></noscript></td>
				</tr>
			</table>
		</form>
		<?php
		self::component_gallery_validation();
	}
	
	/**
	* Add new slide into the system
	* Since: Production
	* Status: 100%
	**/
	private static function content_new_slide(){
		#global call to self object
		global $WPTIS;
		global $wpdb;
		$id = "";
		if(isset($_GET['gallery_id'])){
			$id = $_GET['gallery_id'];
		}
		$galleries = $wpdb->get_results("SELECT id, name FROM ".$wpdb->prefix.self::$table_prefix."gallery ");
		$select_options_gallery = "";
		foreach($galleries as $gallery){
			$select_options_gallery .= "<option value='".$gallery->id."'";
			if($id == $gallery->id){$select_options_gallery .= " selected='selected' ";}
			$select_options_gallery .= ">".$gallery->id." - ".$gallery->name."</option>\n";
		}
		$types = $WPTIS->options['slide_types'];
		$types=array_keys($types);
		$select_options_type = "";
		foreach($types as $type){
			$select_options_type .= "<option value='$type'>$type</option>\n";
		}
		?>
		<div class='escape_link'>
			<a href='?page=wptis' class='button_link'><input type='button' class='button-secondary' value='Back To All Galleries'/></a>
			<?php if($id != ""){
				echo "<br /><a href='?page=wptis&WPTIS_mode=edit_gallery&id=$id' class='button_link'><input type='button' class='button-secondary' value='Back To Gallery - $id'/></a>";
			}?>
		</div>
		<form action='<?php echo self::$basename; ?>alter.php' method="post" onsubmit="return validate_form();">
			<?php wp_nonce_field( 'WPTISN', 'WPTIS_N0N_SEC' ); ?>
			<input type='hidden' value='add_slide' name='action' />
			<input type='hidden' value='&WPTIS_mode=edit_slide' name='redirectTo' />
			<input type='hidden' value='0' name='id' />
			<input type='hidden' value='<?php echo md5 ( sha1 ( NONCE_SALT . md5("0") ) ); ?>'  name='validation'/>
			<table width='90%' cellpadding='6' cellspacing='3' border='0'>
				<tr>
					<th scope="row" width='25%' align='right'><label for='name' class='required'>Slide Name</label></th>
					<td width='45%'><input type='text' name='name' id='form_name' /></td>
					<td width='20%'><span id='form_name_validation' class='validation_message'>This Field Is Required</span></td>
				</tr>
				<tr>
					<th scope="row" align='right' valign="top"><label for='gallery' class='required'>Gallery</label></th>
					<td><select name='gallery' id='form_gallery'><option value='-1'>-- Please Select --</option><?php echo $select_options_gallery ?></select></td>
					<td><span id='form_gallery_validation' class='validation_message'>This Field Is Required</span></td>
				</tr>
				<tr>
					<th scope="row" align='right'><label for='type' class='required'>Slide Type</label></th>
					<td><select name='type' id='form_type'><option value='-1'>-- Please Select --</option><?php echo $select_options_type ?></select></td>
					<td><span id='form_type_validation' class='validation_message'>This Field Is Required</span></td>
				</tr>
				<tr>
					<th scope="row" align='right'><label for='link'>Slide Link</label></th>
					<td><input type='text' name='link' id='form_link' /></td>
					<td><span id='form_link_validation' class='validation_message'>This Field Is Required</span></td>
				</tr>
				<tr>
					<th scope="row" align='right'><label for='showtime' class='required'>Slide Show Time</label></th>
					<td><input type='text' name='showtime' id='form_showtime' /></td>
					<td><span id='form_showtime_validation' class='validation_message'>This Field Is Required</span></td>
				</tr>
				<tr>
					<th scope="row" align='right' valign="top"><label for='content' class='required'>Slide Content</label></th>
					<td><textarea name='content' id="form_content" rows='5'></textarea></td>
					<td><span id='form_content_validation' class='validation_message'>This Field Is Required</span></td>
				</tr>
				<tr>
					<td colspan='2' align="center"><input type='submit' value='Add New Slide' id='form_submit' class='button-primary' disabled="disabled" /></td>
					<td><noscript><span id='form_height_validation' class='validation_message' style='display:block;'>Javascript Is Required</span></noscript></td>
				</tr>
			</table>
		</form>
		<?php
		self::component_slide_validation();
	}
	
	/**
	* Edit Existing Gallery
	* Since: Production
	* Status: 100%
	**/
	private static function content_edit_gallery(){
		global $wpdb;
		global $WPTIS;
		$id = "";
		if(isset($_GET['id'])){$id = $_GET['id'];}
		if($id != ""):
			echo "<div class='escape_link'><a href='?page=wptis' class='button_link'><input type='button' class='button-secondary' value='Back To All Galleries'/></a></div>";
?>
<div class='tabs'>
	<a href='javascript:change_tab(0)' id='nav_tab_0' class='tab active'>Gallery Information</a>
	<a href='javascript:change_tab(1)' id='nav_tab_1' class='tab'>In This Gallery</a>
	<a href='javascript:change_tab(2)' id='nav_tab_2' class='tab'>Delete This Gallery</a>
</div>
<?php
			$gallery_query = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".$wpdb->prefix.self::$table_prefix."gallery WHERE id = %d LIMIT 0, 1",$id));
			if( sizeof($gallery_query) == 0 ){
				echo '<h3 class="error">Gallery '.$id.' Not Found</h3>';
				return;
			}
			foreach($gallery_query as $entry):
				$animations = $WPTIS->options['animation_types'];
				$animations =  array_keys($animations);
				$select_options = "";
				foreach($animations as $animation){
					$select_options .= "<option value='$animation'";
					if($animation == $entry->animation){$select_options .= " selected='selected' ";}
					$select_options .= ">$animation</option>\n";
				}?>

				<div class='tab active' id='tab_0'>
					<h3 class='section_title'>Gallery Information</h3>
					<form action='<?php echo self::$basename; ?>alter.php' method="post" onsubmit="return validate_form();">
					<?php wp_nonce_field( 'WPTISN', 'WPTIS_N0N_SEC' ); ?>
					<input type='hidden' value='edit_gallery' name='action' />
					<input type='hidden' value='&WPTIS_mode=edit_gallery&id=<?php echo $entry->id;?>' name='redirectTo' />
					<input type='hidden' value='<?php echo $entry->id;?>' name='id' />
					<input type='hidden' value='<?php echo md5 ( sha1 ( NONCE_SALT . md5( $entry->id ) ) ); ?>'  name='validation'/>
					<table width='90%' cellpadding='6' cellspacing='3' border='0'>
						<tr>
							<th scope="row" width='25%' align='right'><label for='name' class='required'>Gallery Name</label></th>
							<td width='45%'><input type='text' name='name' id='form_name' value='<?php echo $entry->name;?>' /></td>
							<td width='20%'><span id='form_name_validation' class='validation_message'>This Field Is Required</span></td>
						</tr>
						<tr>
							<th scope="row" align='right' valign="top"><label for='description'>Gallery Description</label></th>
							<td><textarea name='description' rows='3' id='form_description'><?php echo $entry->description?></textarea></td>
							<td><span id='form_description_validation' class='validation_message'>This Field Is Required</span></td>
						</tr>
						<tr>
							<th scope="row" align='right'><label for='animation' class='required'>Animation Type</label></th>
							<td><select name='animation' id='form_animation'><option value='-1'>-- Please Select --</option><?php echo $select_options ?></select></td>
							<td><span id='form_animation_validation' class='validation_message'>This Field Is Required</span></td>
						</tr>
						<tr>
							<th scope="row" align='right'>
								<label for='width' class='required'>Gallery Width </label></th>
							<td><input type='text' name='width' id='form_width' value='<?php echo $entry->width;?>' />
								px
							</td>
							<td><span id='form_width_validation' class='validation_message'>This Field Is Required</span></td>
						</tr>
						<tr>
							<th scope="row" align='right'>
								<label for='height' class='required'>Gallery Height</label></th>
							<td><input type='text' name='height' id='form_height' value='<?php echo $entry->height;?>' /> 
							px</td>
							<td><span id='form_height_validation' class='validation_message'>This Field Is Required</span></td>
						</tr>
						<tr>
							<td colspan='2' align="center"><input type='submit' value='Save Changes' id='form_submit' class='button-primary' disabled="disabled" /></td>
							<td><noscript><span id='form_height_validation' class='validation_message' style='display:block;'>Javascript Is Required</span></noscript></td>
						</tr>
					</table>
				</form>
				</div>
			<?php
			endforeach;/*foreach($gallery_query as $entry):*/
			$hrefs = array(
				"id" 					=> "?page=wptis&WPTIS_mode=edit_gallery&id=$id&order=desc&orderby=id",
				"name"				=> "?page=wptis&WPTIS_mode=edit_gallery&id=$id&order=desc&orderby=name",
				"type" 				=> "?page=wptis&WPTIS_mode=edit_gallery&id=$id&order=desc&orderby=type",
				"link" 				=> "?page=wptis&WPTIS_mode=edit_gallery&id=$id&order=desc&orderby=link",
				"content" 		=> "?page=wptis&WPTIS_mode=edit_gallery&id=$id&order=desc&orderby=content",
				"content" 		=> "?page=wptis&WPTIS_mode=edit_gallery&id=$id&order=desc&orderby=showtime"
				);
				$sorts = array(
				"id" 					=> "sortable asc",
				"name"				=> "sortable asc",
				"type"				=> "sortable asc",
				"link" 				=> "sortable asc",
				"content"			=> "sortable asc",
				"showtime"			=> "sortable asc"
				);
				
				$orderby = "";
				if( isset( $_GET['order'] ) && isset( $_GET['orderby'] ) ) {
					$order =  $_GET['order'];
					$by =  $_GET['orderby'];
					$sorts[$by] = "sorted ".$order;
					if( $order == "desc" ){
						$hrefs[$by] = "?page=wptis&WPTIS_mode=edit_gallery&id=$id&order=asc&orderby=$by";
					}
					else{
						$hrefs[$by] = "?page=wptis&WPTIS_mode=edit_gallery&id=$id&order=desc&orderby=$by";
					}
					$orderby = "ORDER BY $by $order";
				}
			$slide_query = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".$wpdb->prefix.self::$table_prefix."slide WHERE gallery_id = %d $orderby",$id));
			
			
			if(sizeof($slide_query) < 1):
				$out = "<tr><td colspan='7' align='center'> <h3 class='warning'>There are no slides in this Gallery</h3><a href='?page=wptis&WPTIS_mode=new_slide&gallery_id=$id' class='add_new_button'>Add New Slide</a></td></tr>";
			else:
				$out = "";
				foreach($slide_query as $slide):
					$out .= "<tr><th scope='row' class='check-column'><input type='checkbox' name='group[".$slide->id."]' value='1' /></th>
					<td>".$slide->id."</td>
					<td><a href='?page=wptis&WPTIS_mode=edit_slide&id=".$slide->id."&gallery_id=$id' class='button_link'>".$slide->name."</a></td>
					<td>".$slide->type."</td>
					<td>".$slide->link."</td>
					<td>".$slide->content."</td>
					<td>".$slide->showtime."</td>";
				endforeach;
			endif;
			?>
			<div class='tab' id='tab_1'>
				<h3 class='section_title'>Slides In This Gallery</h3>
			<form action='<?php echo self::$basename; ?>alter.php' method='post' onsubmit='return confirm("Are you sure you want to delete these slides?\nPress Ok to continue or cancel to go back");'>
			<input type='hidden' name='action' value='group_delete_slide' />
			<input type='hidden' value='&WPTIS_mode=edit_gallery&id=<?php echo $_GET['id']; ?>' name='redirectTo' />
			<input type='hidden' value='0' name='id' />
			<input type='hidden' value='<?php echo md5 ( sha1 ( NONCE_SALT . md5( "0" ) ) ); ?>'  name='validation'/>
			<?php wp_nonce_field( 'WPTISN', 'WPTIS_N0N_SEC' ); ?>
			<p><input type='submit' value='Delete Selected Slides' class='button-secondary' /> | <a href='?page=wptis&WPTIS_mode=new_slide&gallery_id=<?php echo $id; ?>' class='button_link'><input type='button' value='Add New Slide' class='button-secondary'/></a></p>
			<table class="widefat fixed" cellspacing="0" style='width:100%'>
				<thead>
					<tr>
						<th scope="col" class="manage-column column-cb check-column" width='4%'><input type="checkbox"></th>
						<th scope="col" class="manage-column column-title <?php echo $sorts["id"] ?>" width='5%'><a href='<?php echo $hrefs["id"] ?>'><span>ID</span> <span class="sorting-indicator"></span></a></th>
						<th scope="col" class="manage-column column-title <?php echo $sorts["name"] ?>" width='25%'><a href='<?php echo $hrefs["name"] ?>'><span>Name</span> <span class="sorting-indicator"></span></a></th>
						<th scope="col" class="manage-column column-title <?php echo $sorts["type"] ?>" width='15%'><a href='<?php echo $hrefs["type"] ?>'><span>Slide Type</span> <span class="sorting-indicator"></span></a></th>
						<th scope="col" class="manage-column column-title <?php echo $sorts["link"] ?>" width='15%'><a href='<?php echo $hrefs["link"] ?>'><span>Slide Link</span> <span class="sorting-indicator"></span></a></th>
						<th scope="col" class="manage-column column-title <?php echo $sorts["content"] ?>" width='21%'><a href='<?php echo $hrefs["content"] ?>'><span>Slide Content</span> <span class="sorting-indicator"></span></a></th>
						<th scope="col" class="manage-column column-title <?php echo $sorts["showtime"] ?>" width='15%'><a href='<?php echo $hrefs["showtime"] ?>'><span>Slide Show Time</span> <span class="sorting-indicator"></span></a></th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<th scope="col" class="manage-column column-cb check-column" width='8%'><input type="checkbox"></th>
						<th scope="col" class="manage-column column-title <?php echo $sorts["id"] ?>" ><a href='<?php echo $hrefs["id"] ?>'><span>ID</span> <span class="sorting-indicator"></span></a></th>
						<th scope="col" class="manage-column column-title <?php echo $sorts["name"] ?>"><a href='<?php echo $hrefs["name"] ?>'><span>Name</span> <span class="sorting-indicator"></span></a></th>
						<th scope="col" class="manage-column column-title <?php echo $sorts["type"] ?>"><a href='<?php echo $hrefs["type"] ?>'><span>Slide Type</span> <span class="sorting-indicator"></span></a></th>
						<th scope="col" class="manage-column column-title <?php echo $sorts["link"] ?>"><a href='<?php echo $hrefs["link"] ?>'><span>Slide Link</span> <span class="sorting-indicator"></span></a></th>
						<th scope="col" class="manage-column column-title <?php echo $sorts["content"] ?>"><a href='<?php echo $hrefs["content"] ?>'><span>Slide Content</span> <span class="sorting-indicator"></span></a></th>
						<th scope="col" class="manage-column column-title <?php echo $sorts["showtime"] ?>"><a href='<?php echo $hrefs["showtime"] ?>'><span>Slide Show Time</span> <span class="sorting-indicator"></span></a></th>
					</tr>
				</tfoot>
				<tbody>
					<?php echo $out; ?>
				</tbody>
			</table>
			<p><input type='submit' value='Delete Selected Slides' class='button-secondary' /> | <a href='?page=wptis&WPTIS_mode=new_slide&gallery_id=<?php echo $id; ?>' class='button_link'><input type='button' value='Add New Slide' class='button-secondary'/></a></p>
			</form>
			</div>
			<div class='tab' id='tab_2'>
				<h3 class='section_title'>Delete This Gallery</h3>
				<p style='color:red;font-size:16px'>Caution:: Deleting a gallery will remove <strong>ALL</strong> slides from the system.</p>
				<form action='<?php echo self::$basename; ?>alter.php' method="post" onsubmit="return confirm('Are you sure you want to do this?');">
					<?php wp_nonce_field( 'WPTISN', 'WPTIS_N0N_SEC' ); ?>
					<input type='hidden' value='delete_gallery' name='action' />
					<input type='hidden' value='' name='redirectTo' />
					<input type='hidden' value='<?php echo $entry->id;?>' name='id' />
					<input type='hidden' value='<?php echo md5 ( sha1 ( NONCE_SALT . md5($entry->id) ) ); ?>'  name='validation'/>
					<p>To delete this gallery, <input type='submit' class='button-secondary' value='click here'/></p>
				</form>
			</div>
			<?php
			self::component_gallery_validation();
			self::component_tab_js();
		else:
		echo "<h3 class='error'>ID Not Recognised</h3>";
		endif;/*id != ""*/
	}
	
	/**
	* Edit Existing Slide
	* Since: Production
	* Status: 100%
	**/
	private static function content_edit_slide(){
		global $wpdb;
		global $WPTIS;
		$id = "";
		$gallery_id = "";
		if(isset($_GET['id'])){$id = $_GET['id'];}
		if(isset($_GET['gallery_id'])){$gallery_id = $_GET['gallery_id'];}
		if($id != ""):
		?>
			<div class='escape_link'>
				<a href='?page=wptis' class='button_link'><input type='button' class='button-secondary' value='Back To All Galleries'/></a>
				<?php if($gallery_id != ""){?>
					<br /><a href='?page=wptis&WPTIS_mode=edit_gallery&id=<?php echo $gallery_id;?>' class='button_link'><input type='button' class='button-secondary' value='Back To Gallery <?php echo $gallery_id;?>'/></a>
				<?php }?>
			</div>
			<div class='tabs'>
				<a href='javascript:change_tab(0)' id='nav_tab_0' class='tab active'>Slide Information</a>
				<a href='javascript:change_tab(1)' id='nav_tab_1' class='tab'>Delete Slide</a>
			</div>
		<?php
			$slide_query = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".$wpdb->prefix.self::$table_prefix."slide WHERE id = %d LIMIT 0, 1",$id));
			if( sizeof($slide_query) == 0 ){
				echo '<h3 class="error">Slide '.$id.' Not Found</h3>';
				return;
			}
			$entry = $slide_query[0];
			$galleries = $wpdb->get_results("SELECT id, name FROM ".$wpdb->prefix.self::$table_prefix."gallery ");
			$select_options_gallery = "";
			foreach($galleries as $gallery){
				$select_options_gallery .= "<option value='".$gallery->id."'";
				if($entry->gallery_id == $gallery->id){$select_options_gallery .= " selected='selected' ";}
				$select_options_gallery .= ">".$gallery->id." - ".$gallery->name."</option>\n";
			}
			$types = $WPTIS->options['slide_types'];
			$types = array_keys($types);
			$select_options_type = "";
			foreach($types as $type){
				$select_options_type .= "<option value='$type'";
				if($entry->type == $type){$select_options_type .= " selected='selected' ";}
				$select_options_type .= ">$type</option>\n";
			}
			?>
			<div id='tab_0' class='tab active'>
				<h3 class='section_title'>Slide Information</h3>
				<form action='<?php echo self::$basename; ?>alter.php' method="post" onsubmit="return validate_form();">
					<?php wp_nonce_field( 'WPTISN', 'WPTIS_N0N_SEC' ); ?>
					<input type='hidden' value='edit_slide' name='action' />
					<input type='hidden' value='&WPTIS_mode=edit_slide&id=<?php echo $entry->id ?>' name='redirectTo' />
					<input type='hidden' value='<?php echo $entry->id;?>' name='id' />
					<input type='hidden' value='<?php echo md5 ( sha1 ( NONCE_SALT . md5( $entry->id ) ) ); ?>'  name='validation'/>
					<table width='90%' cellpadding='6' cellspacing='3' border='0'>
						<tr>
							<th scope="row" width='25%' align='right'><label for='name' class='required'>Slide Name</label></th>
							<td width='45%'><input type='text' name='name' id='form_name' value='<?php echo $entry->name;?>' /></td>
							<td width='20%'><span id='form_name_validation' class='validation_message'>This Field Is Required</span></td>
						</tr>
						<tr>
							<th scope="row" align='right' valign="top"><label for='gallery' class='required'>Gallery</label></th>
							<td><select name='gallery' id='form_gallery'><option value='-1'>-- Please Select --</option><?php echo $select_options_gallery ?></select></td>
							<td><span id='form_gallery_validation' class='validation_message'>This Field Is Required</span></td>
						</tr>
						<tr>
							<th scope="row" align='right'><label for='type' class='required'>Slide Type</label></th>
							<td><select name='type' id='form_type'><option value='-1'>-- Please Select --</option><?php echo $select_options_type ?></select></td>
							<td><span id='form_type_validation' class='validation_message'>This Field Is Required</span></td>
						</tr>
						<tr>
							<th scope="row" align='right'><label for='link'>Slide Link</label></th>
							<td><input type='text' name='link' id='form_link' value='<?php echo $entry->link;?>' /></td>
							<td><span id='form_link_validation' class='validation_message'>This Field Is Required</span></td>
						</tr>
						<tr>
							<th scope="row" align='right'><label for='showtime' class='required'>Slide Show Time</label></th>
							<td><input type='text' name='showtime' id='form_showtime' value='<?php echo $entry->showtime;?>' /></td>
							<td><span id='form_showtime_validation' class='validation_message'>This Field Is Required</span></td>
						</tr>
						<tr>
							<th scope="row" align='right' valign="top"><label for='content' class='required'>Slide Content</label></th>
							<td><textarea name='content' id="form_content" rows='5'><?php echo $entry->content;?></textarea></td>
							<td><span id='form_content_validation' class='validation_message'>This Field Is Required</span></td>
						</tr>
						<tr>
							<td colspan='2' align="center"><input type='submit' value='Save Changes' id='form_submit' class='button-primary' disabled="disabled" /></td>
							<td><noscript><span id='form_height_validation' class='validation_message' style='display:block;'>Javascript Is Required</span></noscript></td>
						</tr>
					</table>
				</form>
			</div>
			<div id='tab_1' class='tab'>
				<h3 class='section_title'>Delete This Slide</h3>
				<p style='color:red;font-size:16px'>Caution:: Deleted Slides Cannot Be Recovered</p>
				<form action='<?php echo self::$basename; ?>alter.php' method="post" onsubmit="return confirm('Are you sure you want to do this?');">
					<?php wp_nonce_field( 'WPTISN', 'WPTIS_N0N_SEC' ); ?>
					<input type='hidden' value='delete_slide' name='action' />
					<input type='hidden' value='<?php echo $entry->id;?>' name='id' />
					<input type='hidden' value='&WPTIS_mode=edit_gallery&id=<?php echo $entry->gallery_id ?>' name='redirectTo' />
					<input type='hidden' value='<?php echo md5 ( sha1 ( NONCE_SALT . md5($entry->id) ) ); ?>'  name='validation'/>
					<p>To delete this slide, <input type='submit' class='button-secondary' value='click here'/></p>
				</form>
			</div>
			
			<?php
			self::component_slide_validation();
			self::component_tab_js();
		else:
		echo "<h3 class='error'>ID Not Recognised</h3>";
		endif;//id != ""
	}

/******************************************************************************************************************************************
 
                                                                  COMPONENT SECTION

******************************************************************************************************************************************/
	
	/**
	* Jquery validation for gallery forms
	* Since: Production
	**/
	private static function component_tab_js(){
		?>
		<script type="text/javascript">
			var current_tab = 0;
			var blocked = false;
			jQuery(document).ready(function($){
				<?php  if(isset($_GET['tab'])){
						$tab = $_GET['tab'];
						echo "change_tab($tab);";
					} ?>
			});
			
			function change_tab(new_tab){	
				if(blocked == false){
					blocked = true;
					var active_tab = jQuery("#nav_tab_"+current_tab);
					var next_tab = jQuery("#nav_tab_"+new_tab);
					var newt = jQuery("#tab_"+new_tab);
					var new_tab_id = newt.attr("id");
					var current = jQuery("#tab_"+current_tab);
					if(typeof(new_tab_id) != "undefined"){
						active_tab.removeClass("active");
						current.fadeOut(300, function (){
							newt.fadeIn(300, function (){
								next_tab.addClass("active");
								current_tab = new_tab;
								blocked = false;	
							});	
						});
					}
					else{
						blocked = false;
					}
				}
			}
		</script>
		<?php		
	}
	
	/**
	* Jquery validation for gallery forms
	* Since: Production
	**/
	private static function component_gallery_validation(){
		?>
		<script type="text/javascript">var name = "";var name_validation = "";var description = "";var description_validation = "";var animation = "";var animation_validation = "";var width = "";var width_validation = "";var height = "";var height_validation = "";jQuery(document).ready(function($) {$("form #form_submit").removeAttr("disabled");name = $("form #form_name");name_validation = $("form #form_name_validation");description = $("form #form_description");description_validation = $("form #form_description_validation");animation = $("form #form_animation");animation_validation = $("form #form_animation_validation");width = $("form #form_width");width_validation = $("form #form_width_validation");height = $("form #form_height");height_validation = $("form #form_height_validation");});function validate_form(){var error = false;if(name.attr("value").length < 1){name.removeClass("good");name.addClass("error");name_validation.show(100);error = true;}else{name.removeClass("error");name.addClass("good");name_validation.hide(100);}if(description.attr("value").length < 0){description.removeClass("good");description.addClass("error");description_validation.show(100);error = true;}else{description.removeClass("error");description.addClass("good");description_validation.hide(100);}if(width.attr("value").length < 1 || !parseInt(width.attr("value"))){width.removeClass("good");width.addClass("error");width_validation.show(100);error = true;}else{width.removeClass("error");width.addClass("good");width_validation.hide(100);}if(height.attr("value").length < 1 || !parseInt(height.attr("value"))){height.removeClass("good");height.addClass("error");height_validation.show(100);error = true;}else{height.removeClass("error");height.addClass("good");height_validation.hide(100);}if(animation.attr("value") == "-1"){animation.removeClass("good");animation.addClass("error");animation_validation.show(100);error = true;}else{animation.removeClass("error");animation.addClass("good");animation_validation.hide(100);}if(error == true){return false;}else{return true;}}</script>
		<?php		
	}
	
	/**
	* Jquery validation for slide forms
	* Since: Production
	**/
	private static function component_slide_validation(){
		?>
<script type="text/javascript">var name = "";var name_validation = "";var gallery = "";var gallery_validation = "";var type = "";var type_validation = "";var href = "";var content = "";var content_validation = "";var showtime = "";var showtime_validation = "";jQuery(document).ready(function($) {$("form #form_submit").removeAttr("disabled");name = $("form #form_name");name_validation = $("form #form_name_validation");gallery = $("form #form_gallery");gallery_validation = $("form #form_gallery_validation");type = $("form #form_type");type_validation = $("form #form_type_validation");href = $("form #form_link");href_validation = $("form #form_link_validation");content = $("form #form_content");content_validation = $("form #form_content_validation");showtime = $("form #form_showtime");showtime_validation = $("form #form_showtime_validation");});function validate_form(){var error = false;if(name.attr("value").length < 1){name.removeClass("good");name.addClass("error");name_validation.show(100);error = true; }else{name.removeClass("error");name.addClass("good");name_validation.hide(100);}if(content.attr("value").length < 1){content.removeClass("good");content.addClass("error");content_validation.show(100);error = true;}else{content.removeClass("error");content.addClass("good");content_validation.hide(100);}if(href.attr("value").length < 0){href.removeClass("good");href.addClass("error");href_validation.show(100);error = true;}else{href.removeClass("error");href.addClass("good");href_validation.hide(100);}if(showtime.attr("value").length < 1 || !parseInt(showtime.attr("value"))){showtime.removeClass("good");showtime.addClass("error");showtime_validation.show(100);error = true;}else{showtime.removeClass("error");showtime.addClass("good");showtime_validation.hide(100);}if(gallery.attr("value") == "-1"){gallery.removeClass("good");gallery.addClass("error");gallery_validation.show(100);error = true;}else{gallery.removeClass("error");gallery.addClass("good");gallery_validation.hide(100);}if(type.attr("value") == "-1"){type.removeClass("good");type.addClass("error");type_validation.show(100);error = true;}else{type.removeClass("error");type.addClass("good");type_validation.hide(100);}if(error == true){return false;}else{ return true; }}</script>
		<?php		
	}
	
	/**
	* Yellow Status Bar Messages
	* Since: Production
	**/
	private static function component_status_message(){
		if( isset( $_GET['status'] ) ){
			$message = "";
			$status = $_GET['status'];
			switch($status){
				case "gallery_added":
					$message = "Gallery #".$_GET['id']." Added Successfully";
					break;
				case "gallery_edited":
					$message = "Gallery #".$_GET['id']." Edited Successfully";
					break;
				case "slide_added":
					$message = "Slide #".$_GET['id']." Added Successfully";
					break;
				case "slide_edited":
					$message = "Slide #".$_GET['id']." Edited Successfully";
					break;
				case "slide_deleted":
					$message = "Slide Deleted Successfully";
					break;
				case "gallery_deleted":
					$message = "Gallery Deleted Successfully";
					break;
				case "nothing_selected":
					$message = "No Items Selected!";
					break;
				case "gallery_group_deleted":
					$message = "Gallery(s) Deleted";
					break;
				case "slide_group_deleted":
					$message = "Slide(s) Deleted";
					break;
				default:
					$message = "";
					break;
			}
			if($message != ""){
				?>
				<div id='message' class='updated WPTIS_message'>
					<p><?php echo $message;?></p>
				</div>
				<?php
			}
		}
	}
	
	/**
	* Shortcode Processing
	* Since: Production
	**/
	public function component_shortcode($args){
		if(isset($args['id'])){
			$id = $args['id'];
			global $wpdb;
			$gallery_information = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".$wpdb->prefix.self::$table_prefix."gallery WHERE id = %d", $id));
			if(sizeof($gallery_information) > 0){
				self::operation_load_options();
				global $WPTIS;
				$animation = $WPTIS->options['animation_types'];
				$keys = array_keys($animation);
				if(in_array($gallery_information->animation, $keys)){
					$ANIMATION = $animation[$gallery_information->animation];
					//load slides
					$slide_information = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".$wpdb->prefix.self::$table_prefix."slide WHERE gallery_id = %d", $id));
					$SLIDES = array();
					foreach($slide_information as $slide){
						array_push($SLIDES,self::process_slide($slide, $gallery_information));
					}
					return self::process_animation( $id, $ANIMATION, $SLIDES, $gallery_information );
				}
				else{
					return "[Animation Type Not Found]";
				}
			}
			else{
				return "[Gallery Not Found]";
			}
		}
		else{return "[Gallery Not Found]";}
	}

/******************************************************************************************************************************************
 
                                                                 PROCESS SECTION

******************************************************************************************************************************************/

	/**
	* Processes the slide content based on slide type
	* Since: Production
	**/
	private function process_animation($ID, $ANIMATION, $SLIDES, $GALLERY){
		$wpurl = get_bloginfo("wpurl");
		$output = "";
		ob_start();
		
		//process css
		if($ANIMATION['CSS'] != ""){
			$pos = stripos($ANIMATION['CSS'],$wpurl);
			if($pos > -1){ 
				$processor = str_ireplace($wpurl, "", $ANIMATION['CSS']);
				echo "<style type='text/css'>@import url(".$processor.");</style>";
			}
			else{}
		}
		
		//process html
		$pos = stripos($ANIMATION['HTML'],$wpurl);
		if($pos > -1){ 
			$processor = str_ireplace($wpurl."/", "", $ANIMATION['HTML']);
			require_once($processor);
			if(function_exists("output")){
				output($ID, $SLIDES, $GALLERY);
			}
			else{
				return "ERROR - Processsor Not Found 1";
			}
		}
		else{
			return "ERROR - Processsor Not Found 2";
		}
		
		//process JS
		$pos = stripos($ANIMATION['JS'],$wpurl);
		if($pos > -1){ 
			$processor = str_ireplace($wpurl, "", $ANIMATION['JS']);
			wp_enqueue_script("jquery", $in_footer=true );
			wp_enqueue_script("WPTIS_timer", WPTIS_URL."jquery.time.module.js",array('jquery'), "1", true);
			wp_enqueue_script("WPTIS_animation", $processor ,array('jquery', "WPTIS_timer"), "1", true);
		}
		else{}
		
		$output = ob_get_clean();
		ob_end_flush();
		return $output;
	}

	/**
	* Processes the slide content based on slide type
	* Since: Production
	**/
	private function process_slide($slide, $gallery){
		self::operation_load_options();
		global $WPTIS;
		$type = $WPTIS->options['slide_types'];
		$key = array_keys($type);
		
		if(in_array($slide->type, $key)){
			$processor = $type[$slide->type];
			$wpurl = get_bloginfo("wpurl");
			$pos = stripos($processor,$wpurl);
			if($pos > -1){ 
				$processor = str_ireplace($wpurl."/", "", $processor);
				include_once($processor);
				$func = $slide->type;
				$func = str_replace(" ", "_",$func);
				if(function_exists($func)){
					return $func($slide, $gallery);
				}
				else{
					return "ERROR - Processsor Not Found 3 ";
				}
			}
			else{
					return "ERROR - Processsor Not Found 4";
			}
		}
		else{
			return "ERROR - Slide Type Not Recognised";
		}		
	}

/******************************************************************************************************************************************
 
                                                                 ADMIN DESIGN SECTION

******************************************************************************************************************************************/
	
	/**
	* Top Design for Admin Settings
	* Since: Production
	**/
	private static function design_head(){
		self::operation_load_options();
		self::component_status_message();
		echo "<div class='WPTIS_wrap'>";
		echo "<h1 class='title'>".self::$plugin_page_title."</h1>";
	}
	
	/**
	* Sub Title for Admin Settings
	* Since: Production
	**/
	private static function design_subtitle(){
		global $WPTIS;
		if($WPTIS->current_page_title != ""){
			echo "<h2 class='sub_title'>".$WPTIS->current_page_title."</h2>";
		}
	}
		
	/**
	* Bottom Design for Admin Settings
	* Since: Production
	**/
	private static function design_foot(){
		global $WPTIS;
		echo "<p class='footer_text'><strong>".self::$plugin_page_title."</strong> <em>Version ".$WPTIS->options['plugin_version']." by <a href='http://www.hitreach.co.uk/' target='_blank'>Hit Reach&reg; | Web Design and SEO - Scotland</a></em></p>";
		echo "</div>";
		if($_GET['page'] == "WPTIS-information"){
			self::appeal();
		}
	}
	
	/**
	* Page Styles for Admin Settings
	* Since: Production
	**/
	private static function design_styles(){
		?><style type="text/css">
		<!--
		.WPTIS_message{
			margin:10px 10px 30px !important;
			padding:5px 10px !important;
			width:1000px;
			font-weight:bold;
		}
		.WPTIS_wrap, .WPTIS_Appeal{
			float:left;
			width:1000px;
			position:relative;
			min-height:500px;
			padding:10px 10px 30px;
			border:1px #ccc solid;
			margin:10px;
			background:white;
			-webkit-border-radius: 10px;
			-moz-border-radius: 10px;
			border-radius: 10px;
			-webkit-box-shadow: #666 0px 0px 10px;
			-moz-box-shadow: #666 0px 0px 10px;
			box-shadow: #666 0px 0px 10px;
			background: #ffffff;
			background: -webkit-gradient(linear, 0 0, 0 bottom, from(#ffffff), to(#ededed));
			background: -webkit-linear-gradient(#ffffff, #ededed);
			background: -moz-linear-gradient(#ffffff, #ededed);
			background: -ms-linear-gradient(#ffffff, #ededed);
			background: -o-linear-gradient(#ffffff, #ededed);
			background: linear-gradient(#ffffff, #ededed);
			color:#333;
		}
		.WPTIS_wrap h1.title{
			margin:0; 
			font-size:32px;
			line-height:42px;
			color:#7D1315;
		}
		.WPTIS_wrap h2.sub_title{
			margin:0 0 10px 0; 
			padding-bottom:3px;
			font-size:26px;
			line-height:34px;
			color:#172951;
			border-bottom:1px #172951 solid;
		}
		.WPTIS_wrap h3.section_title{
			margin:20px 0 10px; 
			font-size:22px;
			line-height:34px;
			color:#172951;
		}
		.WPTIS_wrap h3.error{
			margin:160px 0 0;
			font-size:20px;
			color:red;
			text-align:center;
		}
		.WPTIS_wrap h3.warning{
			margin:10px 0;
			font-size:16px;
			color:red;
			text-align:center;
		}
		.WPTIS_wrap a.add_new_button{
			margin:10px 0;
			display:block;
			width:120px;
			border:1px #333 solid;
			background:white;
			padding:3px;
		}
		.WPTIS_wrap div.escape_link{
			position:absolute;
			right:0; 
			top:0;
			margin:10px;
		}
		.WPTIS_wrap p.escape_link{
			text-align:right;
			
		}
		.WPTIS_wrap div.escape_link input{
			width:90%;
		}
		.WPTIS_wrap p.escape_link input{
			width:144px;
		}
		.WPTIS_wrap p.footer_text{
			font-size:10px;
			position:absolute;
			bottom:0px;
			right:0px;
			margin:10px;
		}
		.WPTIS_wrap a.button_link{
			text-decoration:none;
		}
		.WPTIS_wrap p.footer_text a{
			color:#333;
		}
		.WPTIS_wrap form input[id^=form][type=text],
		.WPTIS_wrap form textarea[id^=form],
		.WPTIS_wrap form select[id^=form]{
			width:100%;
		}
		.WPTIS_wrap form input[id=form_width],.WPTIS_wrap form input[id=form_height]{
			width:95% !important;
		}
		.WPTIS_wrap form label.required:after{
			content: " *";
			color:red;
			font-weight:bold;
		}
		.WPTIS_wrap form select#form_animation option,
		.WPTIS_wrap form select#form_animation {
			text-transform:capitalize;
		}
		.WPTIS_wrap form span.validation_message{
			display:none;
			font-weight:bold;
			color:red;
			border:1px #f00 solid;
			padding:5px 10px;
			background:white;
			-webkit-border-radius: 5px;
			-moz-border-radius: 5px;
			border-radius: 5px;
		}
		.WPTIS_wrap form .error{
			border:1px #f00 solid;
			-webkit-box-shadow: #f00 0px 0px 3px;
			-moz-box-shadow: #f00 0px 0px 3px;
			box-shadow: #f00 0px 0px 3px;
		}
		.WPTIS_wrap form .good{
			border:1px #090 solid;
			-webkit-box-shadow: #090 0px 0px 3px;
			-moz-box-shadow: #090 0px 0px 3px;
			box-shadow: #090 0px 0px 3px;
		}
		.WPTIS_wrap p.footer_text a:hover{
			text-decoration:none;
		}
		.WPTIS_wrap div.tab{display:none; clear:left;}
		.WPTIS_wrap div.active{display:block;}
		.WPTIS_wrap div.tabs a.tab{text-decoration:none; float:left; display:block; padding:3px 7px; background:#ededed; border:1px #ccc solid; margin:0 0 0 3px;}
		.WPTIS_wrap div.tabs a.tab:first-child{margin-left:0px;}
		.WPTIS_wrap div.tabs a.active{background:#fff;}
		.WPTIS_wrap div.tabs{height:24px; border-bottom:1px #ccc solid;}
		.WPTIS_Appeal{float:left; width:400px;}
		.WPTIS_Appeal ol{margin-left:0px;}
		-->
		</style><?php
	}
	
	/******************************************************************************************************************************************
 
                                                                 PUBLIC SECTION

	******************************************************************************************************************************************/
	
	/**
	* Public function to register new animation types into the system
	* Returns Boolean
	* Since: Production
	**/
	public static function public_register_animation($NAME, $HTML, $CSS="", $JS=""){
		self::operation_load_options();
		global $WPTIS;
		if(sizeof($WPTIS->options["animation_types"]) > 0){
			if( $NAME == "" || $HTML == "" ){ return false;}
			$components = array( "JS"=>$JS, "HTML"=>$HTML, "CSS"=>$CSS );
			$options = $WPTIS->options["animation_types"];
			$options[$NAME] = $components;
			$WPTIS->options["animation_types"] = $options;
			update_option(WPTIS::$option_name, $WPTIS->options);
			return true;			
		}
		else{
			$components = array( "JS"=>$JS, "HTML"=>$HTML, "CSS"=>$CSS );
			$options = $WPTIS->options["animation_types"];
			$options[$NAME] = $components;
			$WPTIS->options["animation_types"] = $options;
			update_option(WPTIS::$option_name, $WPTIS->options);
		}
	}
	
	/**
	* Public function to unregister animation type from the system
	* Returns Boolean
	* Since: Production
	**/
	public static function public_unregister_animation($NAME){
		self::operation_load_options();
		global $WPTIS;
		if(sizeof($WPTIS->options["animation_types"]) > 0){
			$keys = array_keys($WPTIS->options["animation_types"]);
			if( in_array( $NAME,$keys ) ){
				if( $NAME == "" ){return false;}
				$options = $WPTIS->options["animation_types"];
				unset($options[$NAME]);
				$WPTIS->options["animation_types"] = $options;
				update_option(WPTIS::$option_name, $WPTIS->options);
				return true; 			
			}
			else{
				return false;
			}
		}
		else{
			return false;
		}
	}
	
	/**
	* Public function to register new slide types into the system
	* Returns Boolean
	* Since: Production
	**/
	public static function public_register_slide_type($NAME, $PROCESSOR){
		self::operation_load_options();
		global $WPTIS;
		if(sizeof($WPTIS->options["slide_types"]) > 0){
			if( $NAME == "" || $PROCESSOR == "" ){ return false;}
			$options = $WPTIS->options["slide_types"];
			$options[$NAME] = $PROCESSOR;
			$WPTIS->options["slide_types"] = $options;
			update_option(WPTIS::$option_name, $WPTIS->options);
			return true;
		}
		else{
			$options = $WPTIS->options["slide_types"];
			$options[$NAME] = $PROCESSOR;
			$WPTIS->options["slide_types"] = $options;
			update_option(WPTIS::$option_name, $WPTIS->options);
		}
	}
	
	/**
	* Public function to unregister animation type from the system
	* Returns Boolean
	* Since: Production
	**/
	public static function public_unregister_slide_type($NAME){
		self::operation_load_options();
		global $WPTIS;
		if(sizeof($WPTIS->options["slide_types"]) > 0){
			$keys = array_keys($WPTIS->options["slide_types"]);
			if( in_array( $NAME,$keys ) ){
				if( $NAME == "" ){return false;}
				$options = $WPTIS->options["slide_types"];
				unset($options[$NAME]);
				$WPTIS->options["slide_types"] = $options;
				update_option(WPTIS::$option_name, $WPTIS->options);
				return true; 			
			}
			else{
				return false;
			}
		}
		else{
			return false;
		}
	}
	
	/**
	* Add the appeal box to the right hand side of the information page
	* Since: Production
	**/	
	private static function appeal(){
	?>
<div class='WPTIS_Appeal'> 
	<p class='description'>
		<strong>Developed by <a href='http://www.hitreach.co.uk' target="_blank" style='text-decoration:none;'>Hit Reach</a></strong>
		<a href='http://www.hitreach.co.uk' target="_blank" style='text-decoration:none;'></a>
	</p> 
	<p class='description'>
		<strong>Check out our other <a href='http://www.hitreach.co.uk/services/wordpress-plugins/' target="_blank" style='text-decoration:none;'>Wordpress Plugins</a></strong>
		<a href='http://www.hitreach.co.uk/services/wordpress-plugins/' target="_blank" style='text-decoration:none;'></a>
	</p> 
	<p class='description'>
		<strong>Version: <?php echo self::$plugin_version; ?> <a href='http://hitr.ch/wptis' target="_blank" style='text-decoration:none;'>Support, Comments &amp; Questions</a></strong>
	</p>
	<hr/>
	<h2>Please help! We need your support...</h2>
	<p>If this plugin has helped you, your clients or customers then please take a moment to 'say thanks'. </p>
	<p>By spreading the word you help increase awareness of us and our plugins which makes it easier to justify the time we spend on this project.</p>
	<p>Please <strong>help us keep this plugin free</strong> to use and allow us to provide on-going updates and support.</p>
	<p>Here are some quick, easy and free things you can do which all help and we would really appreciate.</p>
	<ol>
		<li> <strong>Promote this plugin on Twitter</strong><br/>
			<a href="http://twitter.com/home?status=I'm using the WP jQuery Text and Image Slider plugin by @hitreach and it rocks! You can download it here: http://hitr.ch/wptis" target="_blank"> <img src='<?php echo WPTIS_URL;?>/twitter.gif' border="0" width='55' height='20'/> </a><br/>
			<br/>
		</li>
		<li> <strong>Link to us</strong><br/>
			By linking to <a href='http://www.hitreach.co.uk' target="_blank">www.hitreach.co.uk</a> from your site or blog it means you can help others find the plugin on our site and also let Google know we are trust and link worthy which helps our profile.<br/>
			<br/>
		</li>
		<li> <strong>Like us on Facebook</strong><br/>
			Just visit <a href='http://www.facebook.com/webdesigndundee' target="_blank">www.facebook.com/webdesigndundee</a> and hit the 'Like!' button!<script src="http://connect.facebook.net/en_US/all.js#xfbml=1"></script>
			<fb:like href="http://www.facebook.com/webdesigndundee" send="true" width="400" show_faces="false" action="like" font="verdana"></fb:like>
			<br/>
			<br/>
		</li>
		<li> <strong>Share this plugin on Facebook</strong><br/>
			<div id="fb-root"></div>
			<script src="http://connect.facebook.net/en_US/all.js#xfbml=1"></script>
			<fb:like href="http://www.hitreach.co.uk/wordpress-plugins/wp-jquery-text-and-image-slider/" send="true" width="420" show_faces="false" action="recommend" font="verdana"></fb:like>
			Share a link to the plugin page with your friends on Facebook<br/>
			<br/>
		</li>
		<li> <strong>Make A Donation</strong><br/>
			Ok this one isn't really free but hopefully it's still a lot cheaper than if you'd had to buy the plugin or pay for it to be made for your project. Any amount is appreciated
			<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank">
				<input type="hidden" name="cmd" value="_donations">
				<input type="hidden" name="business" value="admin@hitreach.co.uk">
				<input type="hidden" name="lc" value="GB">
				<input type="hidden" name="item_name" value="Hit Reach">
				<input type="hidden" name="item_number" value="WPTIS-Plugin">
				<input type="hidden" name="no_note" value="0">
				<input type="hidden" name="currency_code" value="GBP">
				<input type="hidden" name="bn" value="PP-DonationsBF:btn_donate_LG.gif:NonHostedGuest">
				<input type="image" src="https://www.paypalobjects.com/en_GB/i/btn/btn_donate_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online.">
				<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
			</form>
		</li>
	</ol>
</div>
<?php
	}
}
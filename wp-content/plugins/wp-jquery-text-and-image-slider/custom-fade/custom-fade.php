<?php
/**
 * WPTIS FADE WITH BULLETS animation
 * Author: Hit Reach
 * Version: 1.0
**/

/* 
	$ID is the gallery id
	$SLIDES is an array of stdClass, filled with all the slides
	$GALLERY is all the information about the gallery inc width and height of the gallery
	A print_r($SLIDES) | print_r($GALLERY) can show all the information in the array
	Everything in this is output buffered
*/

function output($ID, $SLIDES, $GALLERY){
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
	$num_slides= sizeof($SLIDES);
	$width = $GALLERY->width. "px";
	$height= $GALLERY->height. "px";
	$rand = rand();
	$times = array();
?>
<style>
.fade_custom_<?php if($bulletposition == "inside"){echo "gallery";}elseif($bulletposition == "outside"){echo "slides";}?>{width:<?php echo $width;?>; padding:5px; border:1px #ccc solid;}
.fade_custom_gallery img{max-width:<?php echo $width;?> !important;}
.fade_custom_gallery .slide{display:none;}
.fade_custom_gallery .slide_first{display:block;}
.fade_custom_gallery .fade_custom_bullets{clear:both; margin-top:5px;}
.fade_custom_gallery .slide img{border:0px; margin:0px; padding:0px;}
.fade_custom_gallery .fade_custom_bullets ul {margin:0px !important; padding:0px !important; list-style:none; height:<?php echo $bulletsize;?>px; margin-top:4px; width: <?php echo $width + 12?>px;}
.fade_custom_gallery .fade_custom_bullets ul li{display:block; float:<?php echo $bulletalign;?>; padding:0px; width:<?php echo (integer)$bulletsize-2;?>px; height:<?php echo (integer)$bulletsize-2;?>px; overflow:hidden;<?php if($bulletalign =="left"){?> margin:0 0 0 5px!important;<?php } else {?>margin:0 5px 0 0 !important;<?php } ?>border:1px <?php echo $bulletborder;?> solid; background:<?php echo $bulletbg;?>; -moz-transition: all 0.25s ease-in-out 0s; -webkit-transition: all 0.25s ease-in-out 0s;-o-webkit-transition: all 0.25s ease-in-out 0s; transition: all 0.25s ease-in-out 0s; cursor:pointer; <?php if($bulletshape == "circle"){?> border-radius:<?php echo $bulletsize;?>px; -moz-border-radius:<?php echo $bulletsize;?>px; -webkit-border-radius:<?php echo $bulletsize;?>px;<?php }?>}
.fade_custom_gallery .fade_custom_bullets ul li:first-child{margin:0px 0px 0px 0px !important;}
.fade_custom_gallery .fade_custom_bullets ul li.active {background:<?php echo $bulletactivebg;?>;border:1px <?php echo $bulletactiveborder;?> solid;}
.fade_custom_gallery .fade_custom_bullets ul.inside{width: <?php echo $width+0?>px!important;}
</style>
<div class='fade_custom_gallery gallery_id_<?php echo $ID;?> slide_count_<?php echo $num_slides;?>' id="fade_<?php echo $rand;?>">
	<div class='fade_custom_slides'>
		<?php $counter = 0;
		foreach($SLIDES as $slide):?>
			<div class='slide <?php if($counter == 0){echo "slide_first";} ?> slide_<?php echo $counter;?>' id='fade_<?php echo $rand; ?>_slide_<?php echo $counter; ?>' style='width:<?php echo $width;?>; height:<?php echo $height;?>'>
				<?php echo $slide->content;?>
				<?php $times[$counter]=$slide->showtime;?>
			</div>
			<?php $counter ++; 
		endforeach;?>
	</div>
	<?php if($showbullets == 1): ?>
	<div class='fade_custom_bullets'>
		<ul class="<?php echo $bulletposition;?>">
		<?php 
		for($a = 0; $a < $counter; $a++){
			?>
			<li data-id='<?php echo $a;?>' class='fade_bullet <?php if($a == 0){echo "active";}?>' id='fade_bullet_<?php echo $a;?>'></li>
			<?php	
		}
		?>
		</ul>
	</div>
	<?php endif; ?>
</div>

<script type='text/javascript'>
var fade_rand = <?php echo $rand; ?>;
var timings = new Array();
var bullets = <?php echo $showbullets;?>;
var transition_time = <?php echo $transitiontime;?>;
<?php 
	$counter = 0; 
	foreach($times as $time):
		echo "timings[$counter] = ".((integer)$time).";\n";
		$counter++;
	endforeach;
?>
</script>
<?php 
}

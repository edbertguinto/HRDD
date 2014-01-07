<?php
/**
 * WPTIS FADE animation
 * Author: Hit Reach
 * Version: 1
**/

/* 
	$ID is the gallery id
	$SLIDES is an array of stdClass, filled with all the slides
	$GALLERY is all the information about the gallery inc width and height of the gallery
	A print_r($SLIDES) | print_r($GALLERY) can show all the information in the array
	Everything in this is output buffered
*/

function output($ID, $SLIDES, $GALLERY){
	$num_slides= sizeof($SLIDES);
	$width = $GALLERY->width. "px";
	$height= $GALLERY->height. "px";
	$rand = rand();
	$times = array();
?>

<div class='fade_gallery gallery_id_<?php echo $ID;?> slide_count_<?php echo $num_slides;?>'>
<?php $counter = 0;foreach($SLIDES as $slide):?>
<div class='slide <?php if($counter == 0){echo "slide_first";} ?> slide_<?php echo $counter;?>' id='fade_<?php echo $rand; ?>_slide_<?php echo $counter; ?>' style='width:<?php echo $width;?>; height:<?php echo $height;?>'>
<?php echo $slide->content;?>
<?php $times[$counter]=$slide->showtime;?>
</div>
<?php $counter ++; endforeach;?>
</div>
<script type='text/javascript'>
var fade_rand = <?php echo $rand; ?>;
var timings = new Array();
<?php 
	$counter = 0; 
	foreach($times as $time):
		echo "timings[$counter] = $time;\n";
		$counter++;
	endforeach;
?>
</script>
<?php 
}

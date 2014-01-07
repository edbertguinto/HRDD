<?php
/**
 * WPTIS HTML PROCESSOR
 * Author: Hit Reach
 * Version: 1
**/
// HTML SLIDE, Nothing to process
// lets add a extra section showing its passed into the processor

/*
	$slide is an Array of type stdClass.
	it contains all the information about the slide
	function print_r($slide) can be used to view all slide content
*/
function IMAGE($slide, $gallery){
	$src = $slide->content;
	$alt = $slide->name;
	$id = $slide->id;
	$link = $slide->link;
	$width = $gallery->width;
	$height = $gallery->height;
	$show = $slide->showtime;
	$img = "<img src='$src' alt='$alt' class='slide_image slide_image_$id' border='0' width='$width' height='$height' time='$show' />";
	if($link != ""){
		$img= "<a href='$link' class='slide_image_link slide_image_link_$id'>$img</a>";
	}
	$slide->content = $img;
	return $slide;
}

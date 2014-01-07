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
function HTML($slide, $gallery){
	$slide->processed = 1;
	$slide->content = html_entity_decode($slide->content);
	return $slide;
}

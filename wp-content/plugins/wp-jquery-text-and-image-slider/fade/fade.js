jQuery(document).ready(function($){
	//var timings
	//var fade_rand
	var slide_names = "fade_"+fade_rand+"_slide_";
	var current_slide = 0;
	$(document).oneTime(timings[0], function(){
		change_slide();
	});
	
	function change_slide(){
		var nextnum = current_slide +1;
		next = $("#"+slide_names+nextnum);
		next_id = next.attr("id");
		if(typeof(next_id) == "undefined"){
			next = $("#"+slide_names+"0");
			nextnum = 0;
		}
		var current = $("#"+slide_names+current_slide);
		if(current.attr("id") != next.attr("id")){
			current.fadeOut(500,function(){
				next.fadeIn(500, function(){
					current_slide = nextnum;
					$(document).oneTime(timings[current_slide], function(){
						change_slide();
					});
				});
			});
		}
		else{
		}
	}
});
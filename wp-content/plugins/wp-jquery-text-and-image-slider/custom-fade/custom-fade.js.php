jQuery(document).ready(function($){
	//var timings
	//var fade_rand
	//bullets
	//transition_time
	var blocked = false;
	var slide_names = "fade_"+fade_rand+"_slide_";
	var current_slide = 0;
	$(document).oneTime(timings[0],fade_rand+"_timer", function(){
		change_slide(null);
	});
	if(parseInt(bullets) == 1){
		$("#fade_"+fade_rand+" div.fade_custom_bullets ul li").click(function(){
	
			var id = $(this).attr("data-id");
			if(id != current_slide){
				$(document).stopTime(fade_rand+"_timer");
				change_slide(id);
			}
		});
	}
	
	function change_slide(next_slide){
		if(blocked == false){
			blocked = true;
			if(next_slide != null){
				var nextnum = next_slide;
			}
			else{
				var nextnum = parseInt(current_slide) +1;
			}
			next = $("#"+slide_names+nextnum);
			next_id = next.attr("id");
			if(next_slide == null){
				if(typeof(next_id) == "undefined"){
					next = $("#"+slide_names+"0");
					nextnum = 0;
				}
			}
			var current = $("#"+slide_names+current_slide);
			if(current.attr("id") != next.attr("id")){
				$("#fade_"+fade_rand+" div.fade_custom_bullets ul li#fade_bullet_"+current_slide).removeClass("active");
				current.fadeOut(transition_time,function(){
					$("#fade_"+fade_rand+" div.fade_custom_bullets ul li#fade_bullet_"+nextnum).addClass("active");
					next.fadeIn(transition_time, function(){
						current_slide = nextnum;
						blocked = false;
						$(document).oneTime(timings[current_slide],fade_rand+"_timer", function(){
							change_slide(null);
						});
					});
				});
			}
			else{
				blocked = false;
			}
		}
	}
});
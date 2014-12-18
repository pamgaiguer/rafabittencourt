(function(jQuery) {
	jQuery.fn.jfbSlider = function(options) {
		var opts = jQuery.extend({}, jQuery.fn.jfbSlider.defaults, options);
		return this.each(function() {
			var $jfb_container 	= jQuery(this),
			o = jQuery.meta ? jQuery.extend({}, opts, $jfb_container.data()) : opts;
			var $jfb_jfbslider		= jQuery('.boxsinglethumb',$jfb_container),
			$jfb_next		= jQuery('.next1',$jfb_container),
			$jfb_prev		= jQuery('.prev1',$jfb_container),
			jfbslideshow,
			total_elems = o.total,
			current			= 0;
			
			$jfb_next.bind('click',function(){
				if(o.auto != 0){
					clearInterval(jfbslideshow);
					jfbslideshow	= setInterval(function(){
						$jfb_next.trigger('click');
					},o.auto);
				}
				++current;
				if(current >= total_elems) current = 0;
				jfbslide(current,$jfb_jfbslider,o);
			});
			$jfb_prev.bind('click',function(){
				if(o.auto != 0){
					clearInterval(jfbslideshow);
					jfbslideshow	= setInterval(function(){
						$jfb_prev.trigger('click');
					},o.auto);
				}
				--current;
				if(current < 0) current = total_elems - 1;
				jfbslide(current,$jfb_jfbslider,o);
			});
			if(o.auto != 0){
				jfbslideshow	= setInterval(function(){
					$jfb_next.trigger('click');
				},o.auto);
			}
		});	
		
		jQuery.fn.jfbSlider.defaults = {
			auto			: 0,	
			speed			: 1000,
			easing			: 'jswing',
		};
	}

	var jfbslide			= function(current,$jfb_jfbslider,o){
		var jfbslide_to	= parseInt(-o.srzn_jfb_cont_width * current);
		if(o.vertical == true){
			$jfb_jfbslider.stop().animate({
					top : jfbslide_to + 'px'
			},o.speed, o.easing);
		}
		else{
			$jfb_jfbslider.stop().animate({
					left : jfbslide_to + 'px'
			},o.speed, o.easing);
		}
		
	}

})(jQuery);
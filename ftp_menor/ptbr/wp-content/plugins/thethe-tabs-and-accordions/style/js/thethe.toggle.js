;(function($) {	
	$.fn.thetheToggle = function(options) {				
		// defaults
		var defaults = {
			style : "",
			active : 0, 
			speed : 200
		},
		
		// merge defaults with options in new settings object				
			settings = $.extend({}, defaults, options),
	
		// define key variables
			$toggle = this,
			$header = $toggle.children('h3.thethe-toggle-header');
			
			
		if (settings.style == 'blank') {
			$toggle.addClass('thethe-toggle-blank');
			$header.each(function(){
				$(this).css({
					'text-align' : 'left',
					'font-weight' : 'bold',
					'font-size' : '120%'
				});				
				$(this).prepend('<span class="ui-icon ui-icon-triangle-1-e"></span>');				
				$(this).find('span.ui-icon').css('display','inline-block');
				//$(this).wrapInner('<strong />');
				$(this).next().hide().css('padding-left','16px');
			});
			$header.click(function(e){
				$(this).find('span.ui-icon').toggleClass('ui-icon-triangle-1-e ui-icon-triangle-1-s');
				$next = $(this).next();				
		        $next.slideToggle(settings.speed);			 
    		    return false;
	    	});
			
		} else {
			$toggle.addClass('ui-widget ui-accordion ui-accordion-icons');
			$header.each(function(){
				$(this).addClass('ui-accordion-header ui-helper-reset ui-state-default ui-corner-all');
				$(this).prepend('<span class="ui-icon ui-icon-triangle-1-e"></span>');
				$(this).next().addClass('ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom');				 
			});
		
	    	$header.hover(function(e){
				$(this).toggleClass('ui-state-hover');
		    },function(){
				$(this).toggleClass('ui-state-hover');
	    	});
			$header.click(function(e){
				$(this).toggleClass('ui-state-active ui-corner-top ui-corner-all');
				$(this).find('span.ui-icon').toggleClass('ui-icon-triangle-1-e ui-icon-triangle-1-s');
				$next = $(this).next();
	        	$next.slideToggle(settings.speed);
			 
    		    return false;
		    });
		
			if (settings.active) {
				var $this = $header.eq(settings.active-1);
				$this.toggleClass('ui-state-active ui-corner-top ui-corner-all');
				$this.find('span.ui-icon').toggleClass('ui-icon-triangle-1-e ui-icon-triangle-1-s')			
				$next = $this.next();
		        $next.slideToggle();
	    	    return false;
			}
		
		}
		return $toggle;		
	};	
})(jQuery);
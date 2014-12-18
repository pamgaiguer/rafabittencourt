/*************************************************
*
*	Based on:	liteAccordion - horizontal accordion plugin for jQuery
*	by:			Nicola Hibbert
*	url:	  	http://nicolahibbert.com/horizontal-accordion-jquery-plugin
*
/*************************************************/
;(function($) {
	
	$.fn.thetheHAccordion = function(options) {
				
		// defaults
		var defaults = {
			containerWidth : $(this).width(),
			containerHeight : 320,
			headerWidth : 32,
			
			firstSlide : 1, 
			onActivate : function() {},
			slideSpeed : 200,
			slideCallback : function() {},			
			
			autoPlay : false,
			pauseOnHover : false, 
			cycleSpeed : 6000,

			enumerateSlides : false
		},
		
		// merge defaults with options in new settings object				
			settings = $.extend({}, defaults, options),
	
		// define key variables
			$accordion = this,
			$slides = $accordion.find('li.thethe-haccord-section'),
			slideLen = $slides.length,
			slideWidth = settings.containerWidth - (slideLen * settings.headerWidth),
			$header = $slides.children('h3.thethe-haccord-header'),
			
		// core utility and animation methods
			utils = {
				getGroup : function(pos, index) {		
					if (this.offsetLeft === pos.left) {
						return $header.slice(index + 1, slideLen).filter(function() { return this.offsetLeft === $header.index(this) * settings.headerWidth });
					} else if (this.offsetLeft === pos.right) {
						return $header.slice(0, index + 1).filter(function() { return this.offsetLeft === slideWidth + ($header.index(this) * settings.headerWidth) });	
					} 					
				},
				nextSlide : function(slideIndex) {
					var slide = slideIndex + 1 || settings.firstSlide;

					// get index of next slide
					return function() {
						return slide++ % slideLen;
					}
				},
				play : function(slideIndex) {
					var getNext = utils.nextSlide((slideIndex) ? slideIndex : ''), // create closure
						start = function() {
							$header.eq(getNext()).click();
						};
					
					utils.playing = setInterval(start, settings.cycleSpeed);			
				},
				pause : function() {
					clearInterval(utils.playing);
				},
				playing : 0,
				sentinel : false
			};		
		
		// set container heights, widths
		$accordion
			.height(settings.containerHeight)
			.width(settings.containerWidth);
		
		// set tab width, height and selected class
		$header
			.width(settings.containerHeight)
			.height(settings.headerWidth)
			.eq(settings.firstSlide - 1).find('span').addClass('ui-state-active');
		
		// ie :(
		if ($.browser.msie) {
			if ($.browser.version.substr(0,1) > 8) {
				$header.css('filter', 'none');
			} else if ($.browser.version.substr(0,1) < 7) {
				return false;
			}
		}
		
		// set initial positions for each slide
		$header.each(function(index) {
			var $this = $(this),
				left = index * settings.headerWidth;
				$this.addClass('ui-helper-reset');
			    $this.hover(function(){
						$this.find('span').toggleClass('ui-state-hover');
				    },function(){
						$this.find('span').toggleClass('ui-state-hover');
			    });		
			//set line-heigth of span to vertical cwnter align text
			var $hSpan = $this.find('span'),
				$pad = 1;
				
			$pad = 1 + parseInt($hSpan.css('border-top-width')) + parseInt($hSpan.css('border-bottom-width'));
			$lineH = settings.headerWidth - $pad;
			$hSpan.addClass("ui-accordion-header ui-state-default ui-corner-all").css('line-height', $lineH+'px');
				
			if (index >= settings.firstSlide) left += slideWidth;
			$this
				.css('left', left)
				.next()
					.addClass("ui-corner-all ui-widget-content")
					.width(slideWidth)
					.css({ left : left, paddingLeft : settings.headerWidth });
			
			// add number to bottom of tab
			settings.enumerateSlides && $this.append('<b>' + (index + 1) + '</b>');
			if(settings.enumerateSlides){
				$this.find('b').css('color', $this.find('a').css('color'));
			}

		});	
				
		// bind event handler for activating slides
		$header.click(function(e) {
			var $this = $(this),
				index = $header.index($this),
				$next = $this.next(),
				pos = {
					left : index * settings.headerWidth,
					right : index * settings.headerWidth + slideWidth,
					newPos : 0
				}, 
				$group = utils.getGroup.call(this, pos, index);
								
			// set animation direction
			if (this.offsetLeft === pos.left) {
				pos.newPos = slideWidth;
			} else if (this.offsetLeft === pos.right) {
				pos.newPos = -slideWidth;
			}
			
			// check if animation in progress
			if (!$header.is(':animated')) {

				// activate onclick callback with slide div as context		
				if (e.originalEvent) {
					if (utils.sentinel === this) return false;
					settings.onActivate.call($next);
					utils.sentinel = this;
				} else {
					settings.onActivate.call($next);
					utils.sentinel = false;
				}

				// remove, then add selected class
				$header.find('span').removeClass('ui-state-active').filter($this.find('span')).addClass('ui-state-active');
			
				// get group of tabs & animate			
				$group
					.animate({ left : '+=' + pos.newPos }, settings.slideSpeed, function() { settings.slideCallback.call($next) })
					.next()
					.animate({ left : '+=' + pos.newPos }, settings.slideSpeed);
						
			};
			return false
		});
			
		// pause on hover			
		if (settings.pauseOnHover) {
			$accordion.hover(function() {
				utils.pause();
			}, function() {
				utils.play($header.index($header.find('span').filter('.ui-state-active')));
			});
		}
				
		// start autoplay, call utils with no args = start from firstSlide
		settings.autoPlay && utils.play();
		
		return $accordion;
		
	};
	
})(jQuery);
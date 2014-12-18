;(function($){
    var ImageSlider = function(){    
        var defaults = {},
        current_position = 0,
        block_count = 0,
        slide = function (strip,  dir){
            if(dir =='l')current_position++;
            else if(dir == 'r')current_position -- ;
            if (current_position<0) current_position = block_count -1;
            else current_position = (current_position%block_count);
            strip.animate({'left': -current_position*516+'px'},1000,'easeOutElastic');
        };
        return {
            init: function(opt){ 
                var opts = $.extend({}, defaults, opts);
                return this.each(function(){  
                    var $binnash_container  = $(this),
                    o = $.meta ? $.extend({}, opts, $binnash_container.data()) : opts;
                    var $binnash_next   = $('.binnash-next',$binnash_container),
                    $binnash_prev   = $('.binnash-prev',$binnash_container),
                    $stripribbon =$('.strip'),
                    $num_images = $stripribbon.children().length,                
                    $blockwidth = 172,
                    $masksize = 516;
                    block_count = Math.ceil($num_images/3);
                    $('.stripmask').css('width',$masksize+'px');
                    $stripribbon.css('width',$num_images*172+'px');
                    $binnash_next.bind('click',function(e){
                        slide($stripribbon, 'r');
                    });
                    $binnash_prev.bind('click',function(e){
                        slide($stripribbon,  'l');
                    });
                });
            }
        };    
    }();
    $.fn.extend({
        BinnashImageSlider : ImageSlider.init
    });
})(jQuery);

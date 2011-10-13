(function($) {
    $.fn.postexpander = function() {

        return this.each(function() {
            
            $(this).click(function() {

				var link = $(this);

				$.ajax({
					type: 'POST',
					url: link.attr("href"),
					beforeSend: function(XMLHttpRequest) { link.html(bz_post_expander_text).addClass('ajaxed'); },
					data: 'bz_post_expander=1',
					success: function(data) { link.parents(".storycontent").hide().html($(data)).fadeIn(600); }
				});

				return false;

			});

        });

    };

    $.fn.animate_menu = function() {

        return this.children('li').each(function() {
            
			var d = $(this).children('ul'); //for each main item, get the sub list
			var margintop_in = 50; //the starting distance between menu item and the popup submenu
			var margintop_out = 20; //the exiting distance between menu item and the popup submenu

			if(d.size() !== 0){ //if the sub list exists...

				$(this).children('a').append('<span class="hiraquo">&raquo;</span>'); //add a raquo to the main item
				
				d.css({'opacity' : 0 , 'margin-top' : margintop_in });
				
				$(this).mouseenter(function(){ //when mouse enters, slide down the sub list

					d.css({'display' : 'block' }).animate( { 'opacity' : 1 , 'margin-top' : 0 },	200, 'swing' );

				}).mouseleave(function(){ //when mouse leaves, hide the sub list

					d.stop().animate( { 'opacity' : 0 , 'margin-top' : margintop_out },	200, 'swing', function(){ d.css({'display' : '' , 'margin-top' : margintop_in }); }	);

				});
			}

        });

    };

    $.fn.gallery_slider = function() {

        return this.each(function() {
            
			$(this).after('<div class="bzg-slideshow" id="bzg-slideshow-' + $(this).attr('id') + '"><div class="bzg-info"><a class="bzg-preview-link" href="" onclick="bz_SwitchMe(\'' + $(this).attr('id') + '\'); return false;">' + bz_gallery_preview_text + '</a></div><div class="bzg-img"></div></div>');	

        });

    };

    $.fn.tipsy = function(options) {

        options = $.extend({}, $.fn.tipsy.defaults, options);
        
        return this.each(function() {
            
            var opts = $.fn.tipsy.elementOptions(this, options);
			
			//opts.fade = false;
            
            $(this).hover(function() {

                $.data(this, 'cancel.tipsy', true);

                var tip = $.data(this, 'active.tipsy');
                if (!tip) {
                    tip = $('<div class="tipsy"><div class="tipsy-inner"/></div>');
                    tip.css({position: 'absolute', zIndex: 100000});
                    $.data(this, 'active.tipsy', tip);
                }

                if ($(this).attr('title') || typeof($(this).attr('original-title')) != 'string') {
                    $(this).attr('original-title', $(this).attr('title') || '').removeAttr('title');
                }

                var title = $(this).attr('original-title');

                tip.find('.tipsy-inner')['text'](title || opts.fallback);

                var pos = $.extend({}, $(this).offset(), {width: this.offsetWidth, height: this.offsetHeight});
                tip.get(0).className = 'tipsy'; // reset classname in case of dynamic gravity
                tip.remove().css({top: 0, left: 0, visibility: 'hidden', display: 'block'}).appendTo(document.body);
                var actualWidth = tip[0].offsetWidth, actualHeight = tip[0].offsetHeight;
				var h_pos = ( $(this).parents('#pages').length ) ? 'to_left' : ''; // if in right sidebar, move to left
				tip.css({top: pos.top + pos.height, left: pos.left+(pos.width / 2)}).addClass(h_pos);
                if (opts.fade) {
                    tip.css({opacity: 0, display: 'block', visibility: 'visible'}).animate({opacity: 0.9});
                } else {
                    tip.css({visibility: 'visible'});
                }

            }, function() {
                $.data(this, 'cancel.tipsy', false);
                var self = this;
                setTimeout(function() {
                    if ($.data(this, 'cancel.tipsy')) return;
                    var tip = $.data(self, 'active.tipsy');
                    if (opts.fade) {
                        tip.stop().fadeOut(function() { $(this).remove(); });
                    } else {
                        tip.remove();
                    }
                }, 100);

            });
            
        });
        
    };
    
    // Overwrite this method to provide options on a per-element basis.
    // For example, you could store the gravity in a 'tipsy-gravity' attribute:
    // return $.extend({}, options, {gravity: $(ele).attr('tipsy-gravity') || 'n' });
    // (remember - do not modify 'options' in place!)
    $.fn.tipsy.elementOptions = function(ele, options) {
        return $.metadata ? $.extend({}, options, $(ele).metadata()) : options;
    };
    
    $.fn.tipsy.defaults = {
        fade: false,
        fallback: ''
    };	

    $.fn.tooltips = function() {

        return this.each(function() {
            
			var p = $(this).parent();
			var self = $(this);
			
			p.mouseenter(function(){

				self.css({opacity: 0, display: 'block', visibility: 'visible'}).animate({opacity: 0.9});

			}).mouseleave(function(){

				self.stop().delay(100).fadeOut();

			});
            
        });
        
    };

})(jQuery);

function bz_SwitchMe(domid) {

	var the_items = '#' + domid + ' .gallery-item a';
	var the_slider = jQuery('#bzg-slideshow-' + domid );
	var the_slider_info = the_slider.children('.bzg-info');
	var the_slider_img = the_slider.children('.bzg-img');

	jQuery('#' + domid).addClass('ajaxed');

	the_slider.addClass('bzg-slider');

	the_slider_info.html('<small>' + bz_gallery_click_text + '</small>');

	jQuery(the_items).click(function(){

		jQuery(the_items).children('img').removeClass('thumbsel');

		var link = jQuery(this);

		link.children('img').addClass('thumbsel');

		var img_ext = '.' + link.children('img').attr("src").match( /([^\.]+)$/g );

		var img_link = link.children('img').attr("src").replace( /\-([^\-]+)$/g , img_ext );

		the_slider_info.html('<span class="loading"></span>').slideDown();

		the_slider_img.stop().fadeOut(
			600,
			function(){
				the_slider_img.html('<a href="' + link.attr("href") + '"><img src="' + img_link + '" alt="image preview" /></a>');
			}
		).delay(1000).fadeIn(
			600,
			function(){
				the_slider_info.slideUp();
			}
		);

		return false;

	});

	var the_br = '#' + domid + ' br';
	var the_item = '#' + domid + ' .gallery-item';
	var the_caption = '#' + domid + ' .gallery-caption';
	
	jQuery(the_br).css({ 'display' : 'none' });

	jQuery(the_caption).css({ 'display' : 'none' });

	jQuery(the_br + ':last').css({ 'display' : '' });

	var d = jQuery(the_item);

	d.animate(
		{ 'width' : '10%', 'margin-right' : '10px' },
		1000
	);
}








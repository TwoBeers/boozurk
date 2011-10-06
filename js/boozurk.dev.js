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

					d.css({'display' : 'block' });

					d.animate(
						{ 'opacity' : 1 , 'margin-top' : 0 },
						200,
						'swing'
					);

				}).mouseleave(function(){ //when mouse leaves, hide the sub list

					d.stop();

					d.animate(
						{ 'opacity' : 0 , 'margin-top' : margintop_out },
						200,
						'swing',
						function(){ d.css({'display' : '' , 'margin-top' : margintop_in }); }
					);

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

                var title;
                if (typeof opts.title == 'string') {
                    title = $(this).attr(opts.title == 'title' ? 'original-title' : opts.title);
                } else if (typeof opts.title == 'function') {
                    title = opts.title.call(this);
                }

                tip.find('.tipsy-inner')[opts.html ? 'html' : 'text'](title || opts.fallback);

                var pos = $.extend({}, $(this).offset(), {width: this.offsetWidth, height: this.offsetHeight});
                tip.get(0).className = 'tipsy'; // reset classname in case of dynamic gravity
                tip.remove().css({top: 0, left: 0, visibility: 'hidden', display: 'block'}).appendTo(document.body);
                var actualWidth = tip[0].offsetWidth, actualHeight = tip[0].offsetHeight;
                var gravity = (typeof opts.gravity == 'function') ? opts.gravity.call(this) : opts.gravity;

                switch (gravity.charAt(0)) {
                    case 'n':
                        tip.css({top: pos.top + pos.height, left: pos.left + pos.width / 2 - actualWidth / 2}).addClass('tipsy-north');
                        break;
                    case 's':
                        tip.css({top: pos.top - actualHeight, left: pos.left + pos.width / 2 - actualWidth / 2}).addClass('tipsy-south');
                        break;
                    case 'e':
                        tip.css({top: pos.top + pos.height / 2 - actualHeight / 2, left: pos.left - actualWidth}).addClass('tipsy-east');
                        break;
                    case 'w':
                        tip.css({top: pos.top + pos.height / 2 - actualHeight / 2, left: pos.left + pos.width}).addClass('tipsy-west');
                        break;
                }

                if (opts.fade) {
                    tip.css({opacity: 0, display: 'block', visibility: 'visible'}).animate({opacity: 0.8});
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
        fallback: '',
        gravity: 'n',
        html: false,
        title: 'title'
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

jQuery(document).ready(function($){
    $('a.more-link').postexpander();
    $('#mainmenu').animate_menu();
    $('.storycontent .gallery').gallery_slider();
    $('#sidebarsx .bz_widget_latest_commentators li').tipsy({gravity: 'w'});
    $('#pages .bz_widget_latest_commentators li').tipsy({gravity: 'e'});
    $('#header-widget-area .bz_widget_latest_commentators li').tipsy({gravity: 'n'});
    $('#footer-widget-area .bz_widget_latest_commentators li').tipsy({gravity: 's'});
    $('#sidebarsx .bz-widget-social a').tipsy({gravity: 'w'});
    $('#pages .bz-widget-social a').tipsy({gravity: 'e'});
    $('#header-widget-area .bz-widget-social a').tipsy({gravity: 'n'});
    $('#footer-widget-area .bz-widget-social a').tipsy({gravity: 's'});
    $('.pmb_comm').tipsy({gravity: 'w'});
	
});








(function($) {
    $.fn.boozurk_PostExpander = function() {

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

    $.fn.boozurk_AnimateMenu = function() {

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

    $.fn.boozurk_GallerySlider = function() {

        return this.each(function() {
            
			$(this).after('<div class="bzg-slideshow" id="bzg-slideshow-' + $(this).attr('id') + '"><div class="bzg-info"><a class="bzg-preview-link" href="" onclick="bz_SwitchMe(\'' + $(this).attr('id') + '\'); return false;">' + bz_gallery_preview_text + '</a></div><div class="bzg-img"></div></div>');	

        });

    };

	// Based on Tipsy JQuery Plugin
	// http://plugins.jquery.com/project/tipsy
    $.fn.boozurk_Cooltips = function(options) {

        options = $.extend({}, $.fn.boozurk_Cooltips.defaults, options);
        
        return this.each(function() {
            
            var opts = $.fn.boozurk_Cooltips.elementOptions(this, options);
			
			//opts.fade = false;
            
            $(this).hover(function() {

                $.data(this, 'cancel.cooltips', true);

                var tip = $.data(this, 'active.cooltips');
                if (!tip) {
                    tip = $('<div class="cooltips"><div class="cooltips-inner"/></div>');
                    tip.css({position: 'absolute', zIndex: 100000});
                    $.data(this, 'active.cooltips', tip);
                }

                if ($(this).attr('title') || typeof($(this).attr('original-title')) != 'string') {
                    $(this).attr('original-title', $(this).attr('title') || '').removeAttr('title');
                }

                var title = $(this).attr('original-title');

                tip.find('.cooltips-inner')['text'](title || opts.fallback);

                var pos = $.extend({}, $(this).offset(), {width: this.offsetWidth, height: this.offsetHeight});
                tip.get(0).className = 'cooltips'; // reset classname in case of dynamic gravity
                tip.remove().css({top: 0, left: 0, visibility: 'hidden', display: 'block'}).appendTo(document.body);
                var actualWidth = tip[0].offsetWidth, actualHeight = tip[0].offsetHeight;
				var h_pos = ( $(this).parents('#pages,#navbuttons.fixed').length ) ? 'to_left' : ''; // if in right sidebar, move to left
				tip.css({top: pos.top - actualHeight, left: pos.left+(pos.width / 2)}).addClass(h_pos);
                if (opts.fade) {
                    tip.css({opacity: 0, display: 'block', visibility: 'visible'}).animate({opacity: 0.9});
                } else {
                    tip.css({visibility: 'visible'});
                }

            }, function() {
                $.data(this, 'cancel.cooltips', false);
                var self = this;
                setTimeout(function() {
                    if ($.data(this, 'cancel.cooltips')) return;
                    var tip = $.data(self, 'active.cooltips');
                    if (opts.fade) {
                        tip.stop().fadeOut(function() { $(this).remove(); });
                    } else {
                        tip.remove();
                    }
                }, 100);

            });
            
        });
        
    };
    
    $.fn.boozurk_Cooltips.elementOptions = function(ele, options) {
        return $.metadata ? $.extend({}, options, $(ele).metadata()) : options;
    };
    
    $.fn.boozurk_Cooltips.defaults = {
        fade: false,
        fallback: ''
    };	

    $.fn.boozurk_Tooltips = function() {

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
	
    $.fn.boozurk_AudioPlayer = function() {

		var the_id = 0;
		return this.each(function() {
			the_id++;
			$(this).attr('id', 'bz-player-id' + the_id );
			var the_source = $(this).children('source:first-child');
			if ( the_source.size() !== 0 ) {
				the_href = the_source.attr('src');
				var the_type = the_href.substr( the_href.length - 4, 4 )
				switch (the_type)
				{
				case '.ogg':
					if ( !document.createElement("audio").canPlayType ) {
						$(this).parent().html('<span class="bz-player-notice">' + bz_unknown_media_format + '</span>');
					}
					break;
				case '.mp3':
					if ( !document.createElement("audio").canPlayType || (document.createElement("audio").canPlayType && !document.createElement("audio").canPlayType('audio/mpeg')) ) {
						bz_AudioPlayer.embed(this.id, {  
							soundFile: the_href
						});  
					}
					break;
				case '.m4a':
					if ( !document.createElement("audio").canPlayType || (document.createElement("audio").canPlayType && !document.createElement("audio").canPlayType('audio/x-m4a')) ) {
						$(this).parent().html('<span class="bz-player-notice">' + bz_unknown_media_format + '</span>');
					}
					break;
				default:
					$(this).parent().html('<span class="bz-player-notice">' + bz_unknown_media_format + '</span>');
				}				
			}
			
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

var bz_AudioPlayer = function () {
	var instances = [];
	var activePlayerID;
	var playerURL = "";
	var defaultOptions = {};
	var currentVolume = -1;
	var requiredFlashVersion = "9";
	
	function getPlayer(playerID) {
		if (document.all && !window[playerID]) {
			for (var i = 0; i < document.forms.length; i++) {
				if (document.forms[i][playerID]) {
					return document.forms[i][playerID];
					break;
				}
			}
		}
		return document.all ? window[playerID] : document[playerID];
	}
	
	function addListener (playerID, type, func) {
		getPlayer(playerID).addListener(type, func);
	}
	
	return {
		setup: function (url, options) {
			playerURL = url;
			defaultOptions = options;
			if (swfobject.hasFlashPlayerVersion(requiredFlashVersion)) {
				swfobject.switchOffAutoHideShow();
				swfobject.createCSS(".swf-audio-player small", "display:none;");
			}
		},

		getPlayer: function (playerID) {
			return getPlayer(playerID);
		},
		
		addListener: function (playerID, type, func) {
			addListener(playerID, type, func);
		},
		
		embed: function (elementID, options) {
			var instanceOptions = {};
			var key;
			
			var flashParams = {};
			var flashVars = {};
			var flashAttributes = {};
	
			// Merge default options and instance options
			for (key in defaultOptions) {
				instanceOptions[key] = defaultOptions[key];
			}
			for (key in options) {
				instanceOptions[key] = options[key];
			}
			
			if (instanceOptions.transparentpagebg == "yes") {
				flashParams.bgcolor = "#FFFFFF";
				flashParams.wmode = "transparent";
			} else {
				if (instanceOptions.pagebg) {
					flashParams.bgcolor = "#" + instanceOptions.pagebg;
				}
				flashParams.wmode = "opaque";
			}
			
			flashParams.menu = "false";
			
			for (key in instanceOptions) {
				if (key == "pagebg" || key == "width" || key == "transparentpagebg") {
					continue;
				}
				flashVars[key] = instanceOptions[key];
			}
			
			flashAttributes.name = elementID;
			flashAttributes.style = "outline: none";
			
			flashVars.playerID = elementID;
			
			swfobject.embedSWF(playerURL, elementID, instanceOptions.width.toString(), "24", requiredFlashVersion, false, flashVars, flashParams, flashAttributes);
			
			instances.push(elementID);
		},
		
		syncVolumes: function (playerID, volume) {	
			currentVolume = volume;
			for (var i = 0; i < instances.length; i++) {
				if (instances[i] != playerID) {
					getPlayer(instances[i]).setVolume(currentVolume);
				}
			}
		},
		
		activate: function (playerID, info) {
			if (activePlayerID && activePlayerID != playerID) {
				getPlayer(activePlayerID).close();
			}

			activePlayerID = playerID;
		},
		
		load: function (playerID, soundFile, titles, artists) {
			getPlayer(playerID).load(soundFile, titles, artists);
		},
		
		close: function (playerID) {
			getPlayer(playerID).close();
			if (playerID == activePlayerID) {
				activePlayerID = null;
			}
		},
		
		open: function (playerID, index) {
			if (index == undefined) {
				index = 1;
			}
			getPlayer(playerID).open(index == undefined ? 0 : index-1);
		},
		
		getVolume: function (playerID) {
			return currentVolume;
		}
		
	}
	
}();


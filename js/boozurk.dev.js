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
		
    $.fn.boozurk_InfiniteScroll = function( behaviour ) {


			$('#bz-page-nav').addClass('ajaxed');
			$('#bz-next-posts-button').fadeOut();
			$('.nb-nextprev').hide();
			return this.scroll(function () {
				if ( $('body').height()-$(window).scrollTop()-$(window).height() < 100) {
					var link = $('#bz-next-posts-link a');

					if ( link.length > 0 ) {

						if ( behaviour == 'auto' ) {
							boozurk_AJAX_paged();
						} else if ( behaviour == 'manual' ) {
							$('#bz-next-posts-button').fadeIn();
						}


					} else {

						$('#bz-next-posts-button').html(bz_infinite_scroll_text_end).fadeIn();

						return false;
					}

				}

			});

	};

    $.fn.boozurk_AnimateMenu = function() {

        return this.children('li').each(function() {
            
			var d = $(this).children('ul'); //for each main item, get the sub list

			if(d.size() !== 0){ //if the sub list exists...

				$(this).children('a').append('<span class="hiraquo">&raquo;</span>'); //add a raquo to the main item
				
				d.css( {'opacity' : 0 } );
				
				$(this).mouseenter(function(){ //when mouse enters, slide down the sub list

					d.css( {'display' : 'block' } ).animate( { 'opacity' : 0.95 } );

				}).mouseleave(function(){ //when mouse leaves, hide the sub list

					d.stop().animate( { 'opacity' : 0 }, 200, 'swing', function(){ d.css( {'display' : '' } ); } );

				});
			}

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


function boozurk_AJAX_paged() {

	var next_href = jQuery('#bz-next-posts-link a').attr( "href" );
	
	var nav = jQuery('#bz-page-nav');
	
	jQuery.ajax({
		type: 'POST',
		url: next_href,
		beforeSend: function(XMLHttpRequest) { jQuery('#bz-page-nav-msg').addClass('loading').html(bz_infinite_scroll_text).animate( { 'opacity' : 1 } ); },
		data: 'bz_infinite_scroll=1',
		success: function(data) { nav.replaceWith( jQuery(data) ); }
	});
	
	return false;
	
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


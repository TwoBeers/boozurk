var boozurkScripts;

(function($) {

boozurkScripts = {

	init : function( in_modules ) {

		var modules = in_modules.split(',');

		for (i in modules) {

			switch(modules[i]) {

				case 'postexpander':
					boozurkScripts.post_expander();
					break;

				case 'thickbox':
					boozurkScripts.init_thickbox();
					break;

				case 'tooltips':
					boozurkScripts.tooltips();
					boozurkScripts.cooltips('.minibutton,.share-item img,.tb_widget_categories a,#bz-quotethis,.tb_widget_latest_commentators li,.tb_widget_social a,.post-format-item.compact img,a.bz-tipped-anchor',true,'');
					boozurkScripts.cooltips('.pmb_comm',true,boozurk_l10n.comments_closed);
					break;

				case 'plusone':
					gapi.plusone.go("posts_content");
					break;

				case 'quotethis':
					boozurkScripts.init_quote_this();
					break;

				case 'infinitescroll':
					boozurkScripts.infinite_scroll(boozurk_l10n.infinite_scroll_type);
					break;

				case 'animatemenu':
					boozurkScripts.animate_menu();
					break;

				case 'scrolltopbottom':
					boozurkScripts.scroll_top_bottom();
					break;

				case 'commentvariants':
					boozurkScripts.comment_variants();
					break;

				case 'resizevideo':
					boozurkScripts.resize_video();
					break;

				default :
					//no default action
					break;

			}

		}

    },


	post_expander : function() {

        return $('a.more-link').each(function() {
            
            $(this).click(function() {

				var link = $(this);

				$.ajax({
					type: 'POST',
					url: link.attr("href"),
					beforeSend: function(XMLHttpRequest) { link.html(boozurk_l10n.post_expander).addClass('ajaxed'); },
					data: 'boozurk_post_expander=1',
					success: function(data) { link.parents(".storycontent").hide().html($(data)).fadeIn(600); }
				});

				return false;

			});

        });

    },


	infinite_scroll : function( behaviour ) {

			$('#bz-page-nav').addClass('ajaxed');
			$('#bz-next-posts-button').fadeOut();
			$('.nb-nextprev').hide();
			return $(window).scroll(function () {
				if ( $('#bz-page-nav').position().top-$(window).scrollTop()-$(window).height() < -150) {
					var link = $('#bz-next-posts-link a');

					if ( link.length > 0 ) {

						if ( behaviour == 'auto' ) {
							boozurkScripts.AJAX_paged();
						} else if ( behaviour == 'manual' ) {
							$('#bz-next-posts-button').fadeIn();
						}


					} else {

						$('#bz-next-posts-button').html(boozurk_l10n.infinite_scroll_end).fadeIn();

						return false;
					}

				}

			});

	},


	animate_menu : function() {

        return $('#mainmenu').children('li').each(function() {
            
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

    },


	tooltips : function() {

        return $('.bz-tooltip,.nb_tooltip').each(function() {
            
			var p = $(this).parent();
			var self = $(this);
			
			p.mouseenter(function(){

				self.css({opacity: 0, display: 'block', visibility: 'visible'}).animate({opacity: 0.9});

			}).mouseleave(function(){

				self.stop().delay(100).fadeOut();

			});
            
        });
        
    },


	cooltips : function(selector,fade,fallback) {

        return $(selector).each(function() {
            
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

                tip.find('.cooltips-inner')['text'](title || fallback);

                var pos = $.extend({}, $(this).offset(), {width: this.offsetWidth, height: this.offsetHeight});
                tip.get(0).className = 'cooltips'; // reset classname in case of dynamic gravity
                tip.remove().css({top: 0, left: 0, visibility: 'hidden', display: 'block'}).appendTo(document.body);
                var actualWidth = tip[0].offsetWidth, actualHeight = tip[0].offsetHeight;
				var h_pos = ( $(this).parents('#sidebar-secondary,#navbuttons.fixed').length ) ? 'to_left' : ''; // if in right sidebar, move to left
				tip.css({top: pos.top - actualHeight, left: pos.left+(pos.width / 2)}).addClass(h_pos);
                if (fade) {
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
                    if (fade) {
                        tip.stop().fadeOut(function() { $(this).remove(); });
                    } else {
                        tip.remove();
                    }
                }, 100);

            });
            
        });
        
    },


	AJAX_paged : function() {

		var next_href = $('#bz-next-posts-link a').attr( "href" );
		
		var nav = $('#bz-page-nav');
		
		$.ajax({
			type: 'POST',
			url: next_href,
			beforeSend: function(XMLHttpRequest) { $('#bz-page-nav-msg').addClass('loading').html(boozurk_l10n.infinite_scroll).animate( { 'opacity' : 1 } ); },
			data: 'boozurk_infinite_scroll=1',
			success: function(data) {
				nav.replaceWith( $(data) );
				boozurkScripts.init(boozurk_l10n.script_modules_afterajax);
				$('#bz-next-posts-button input').click(function() {
					boozurkScripts.AJAX_paged();
				});
			}
		});
		
		return false;
		
	},


	init_thickbox : function() {

		$('.storycontent a img').parent('a[href$=".jpg"],a[href$=".png"],a[href$=".gif"]').addClass('thickbox');

		$('.storycontent .gallery').each(function() {
			$('a[href$=".jpg"],a[href$=".png"],a[href$=".gif"]',$(this)).attr('rel', $(this).attr('id'));
		});

	},


	scroll_top_bottom : function() {

		top_but = $('.minib_top');
		bot_but = $('.minib_bottom');

		// smooth scroll top/bottom
		top_but.click(function() {
			$("html, body").animate({
				scrollTop: 0
			}, {
				duration: 400
			});
			return false;
		});
		bot_but.click(function() {
			$("html, body").animate({
				scrollTop: $('#footer').offset().top - 80
			}, {
				duration: 400
			});
			return false;
		});
	},


	comment_variants : function() {
		$('.comment-variants label').click(function() {
			$('#comment').removeClass( 'style-default style-blue style-pink style-orange style-yellow style-green style-gray style-white' );
			$('#comment').addClass( $('input', this).val() );
			$('input', this).attr('checked',true);
		});
	},


	init_quote_this : function() {
		if ( document.getElementById('reply-title') && document.getElementById("comment") ) {
			bz_qdiv = document.createElement('small');
			bz_qdiv.innerHTML = ' - <a id="bz-quotethis" href="#" onclick="boozurkScripts.quote_this(); return false" title="' + boozurk_l10n.quote_tip + '" >' + boozurk_l10n.quote + '</a>';
			bz_replink = document.getElementById('reply-title');
			bz_replink.appendChild(bz_qdiv);
		}
	},


	quote_this : function() {
		var posttext = '';
		if (window.getSelection){
			posttext = window.getSelection();
		}
		else if (document.getSelection){
			posttext = document.getSelection();
		}
		else if (document.selection){
			posttext = document.selection.createRange().text;
		}
		else {
			return true;
		}
		posttext = posttext.toString().replace(/&/g,"&amp;").replace(/"/g,"&quot;").replace(/</g,"&lt;").replace(/>/g,"&gt;");
		if ( posttext.length !== 0 ) {
			document.getElementById("comment").value = document.getElementById("comment").value + '<blockquote>' + posttext + '</blockquote>';
		} else {
			alert( boozurk_l10n.quote_alert );
		}
	},


	resize_video : function() {
		// https://github.com/chriscoyier/Fluid-Width-Video
		var $allVideos = $("iframe[src^='http://player.vimeo.com'], iframe[src^='http://www.youtube.com'], object, embed"), $fluidEl = $(".storycontent");

		$allVideos.each(function() {
			$(this)
				// jQuery .data does not work on object/embed elements
				.attr('data-aspectRatio', this.height / this.width)
				.removeAttr('height')
				.removeAttr('width');
		});

		$(window).resize(function() {
			var newWidth = $fluidEl.width();
			$allVideos.each(function() {
				var $el = $(this);
				$el
					.width(newWidth)
					.height(newWidth * $el.attr('data-aspectRatio'));
			});
		}).resize();
	}


};

$(document).ready(function($){ boozurkScripts.init(boozurk_l10n.script_modules); });

})(jQuery);
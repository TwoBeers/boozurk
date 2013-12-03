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
					boozurkScripts.cooltips(boozurk_l10n.cooltips_selector);
					break;

				case 'plusone':
					gapi.plusone.go("posts_content");
					break;

				case 'addthis':
					addthis.button('.addthis_button_compact');
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

				case 'tinynav':
					boozurkScripts.tinynav();
					break;

				case 'tinyscrollbar':
					boozurkScripts.tinyscrollbar();
					break;

				default :
					//no default action
					break;

			}

		}

	},


	post_expander : function() {

		return $('#posts_content').find('a.more-link').each(function() {

			$(this).unbind().click(function() {

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

		return $('#mainmenu').children('.menu-item-parent').each(function() {

			$this = $(this);

			var d = $this.children('ul'); //for each main item, get the sub list

			d.css( {'opacity' : 0 } );

			$this.hoverIntent(

				function(){ //when mouse enters, slide down the sub list

					d.css( {'display' : 'block' } ).animate( { 'opacity' : 0.95 } );

				},

				function(){ //when mouse leaves, hide the sub list

					d.stop().animate( { 'opacity' : 0 }, 200, 'swing', function(){ d.css( {'display' : '' } ); } );

				}
			);

		});

	},


	tooltips : function() {

		return $('#posts_content').find('.bz-tooltip').each(function() {

			var p = $(this).parent();
			var self = $(this);
			var timeoutID;

			self.hide();

			p.unbind().hoverIntent(

				function(){

					window.clearTimeout(timeoutID);
					self.stop().css({opacity: 0, display: 'block', visibility: 'visible'}).animate({opacity: 0.9});

				},

				function(){

					timeoutID = window.setTimeout( function(){self.fadeOut()}, 200);

				}
			);
			
		});
		
	},


	cooltips : function(selector) {

		var baloon = $('<div class="cooltip"></div>');
		baloon.appendTo(document.body);
		var timeoutID;

		return $(selector).each(function() {

			var $this = $(this);

			$this.unbind().hoverIntent(

				function(){

					var offset,h_pos,pin_pos;

					if ($this.attr('title') || typeof($this.attr('original-title')) != 'string') {
						$this.attr('original-title', $this.attr('title') || '').removeAttr('title');
					}

					baloon.html($this.attr('original-title'));

					offset = $this.offset();
					baloon.css({top: 0, left: 0, display: 'block'}).removeClass('to_left to_right');
					
					if ( offset.left > ( $(window).width() - 250 )  ) {
						h_pos = offset.left - baloon.outerWidth() + ( $this.outerWidth() / 2 );
						pin_pos = 'to_left';
					} else {
						h_pos = offset.left + ( $this.outerWidth() / 2 );
						pin_pos = 'to_right';
					}

					baloon.css({top: offset.top - baloon.outerHeight() - 10, left: h_pos}).addClass(pin_pos);
					window.clearTimeout(timeoutID);
					baloon.stop().css({opacity: 0}).animate({opacity: 0.9});

				},

				function(){

					timeoutID = window.setTimeout( function(){baloon.fadeOut()}, 200);

				}
			);

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

		$('#posts_content').find('.storycontent a img').parent('a[href$=".jpg"],a[href$=".png"],a[href$=".gif"]').addClass('thickbox');

		$('#posts_content').find('.storycontent .gallery').each(function() {
			$('a[href$=".jpg"],a[href$=".png"],a[href$=".gif"]',$(this)).attr('rel', $(this).attr('id'));
		});

	},


	scroll_top_bottom : function() {

		top_but = $('#navbuttons').find('.minib_top a');
		bot_but = $('#navbuttons').find('.minib_bottom a');

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
		$('#commentform').find('.comment-variants label').click(function() {
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
		var $fluidEl = $("#posts_content").find(".storycontent");
		var $allVideos = $("iframe[src^='http://player.vimeo.com'], iframe[src^='http://www.youtube.com'], object, embed",$fluidEl);

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
	},

	tinynav : function() {
		$(".nav-menu").tinyNav({
			label: '<i class="icon-align-justify"></i>', // String: Sets the <label> text for the <select> (if not set, no label will be added)
			header: '' // String: Specify text for "header" and show header instead of the active item
		});
	},

	tinyscrollbar : function() {
		var $sidebar_primary_scrollable = $('#sidebar-primary.fixed');
		var $sidebar_secondary_scrollable = $('#sidebar-secondary.fixed');
		var doit;

		function resize_sidebar(){
			if ($sidebar_primary_scrollable.length)
				$sidebar_primary_scrollable.tinyscrollbar_update();

			if ($sidebar_secondary_scrollable.length)
				$sidebar_secondary_scrollable.tinyscrollbar_update();
		}

		if ($sidebar_primary_scrollable.length) {
			$sidebar_primary_scrollable.prepend( '<div class="scrollbar"><div class="track"><div class="thumb"><div class="end"></div></div></div></div>' );
			$sidebar_primary_scrollable.removeClass('fixed').addClass('tinyscroll');
			$sidebar_primary_scrollable.tinyscrollbar();
			$sidebar_primary_scrollable.hover(function () {$('.scrollbar', this).fadeIn()},function () {$('.scrollbar', this).stop().fadeOut()});
		}

		if ($sidebar_secondary_scrollable.length) {
			$sidebar_secondary_scrollable.prepend( '<div class="scrollbar"><div class="track"><div class="thumb"><div class="end"></div></div></div></div>' );
			$sidebar_secondary_scrollable.removeClass('fixed').addClass('tinyscroll');
			$sidebar_secondary_scrollable.tinyscrollbar();
			$sidebar_secondary_scrollable.hover(function () {$('.scrollbar', this).fadeIn()},function () {$('.scrollbar', this).stop().fadeOut()});
		}

		$(window).resize(function() {
			clearTimeout(doit);
			doit = setTimeout(function() {
				resize_sidebar();
			}, 100);
		});
	}

};

$(document).ready(function($){ boozurkScripts.init(boozurk_l10n.script_modules); });

$('body').on('post-load', function(event){ //Jetpack Infinite Scroll trigger
	boozurkScripts.init(boozurk_l10n.script_modules_afterajax);
});

})(jQuery);
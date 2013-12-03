jQuery(document).ready(function($){

	var $head_wrap = $('#head_wrap');
	var $posts_content = $('#posts_content');
	var $content_offset = $('#content').offset();
	var $sidebar_fixed_viewport = $('.sidebar.fixed .viewport');

	if ($head_wrap.length) {
		$head_wrap.addClass('fixed');
		$(window).resize(function() {
			$posts_content.css( {'padding-top' : $head_wrap.height() } );
			$head_wrap.css( {'width' : $posts_content.width() - 1 } );
		});
	}

	if ($sidebar_fixed_viewport.length) {
		$(window).resize(function() {
			$sidebar_fixed_viewport.css( {'height' : $(window).height() - $content_offset.top } );
		});
	}

});
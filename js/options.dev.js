var farbtastic;

// display the color picker
function showMeColorPicker(domid) {
	placeholder = '#bz_colorpicker_' + domid;
	jQuery(placeholder).fadeIn();
	farbtastic = jQuery.farbtastic(placeholder, function(color) { pickColor(domid,color); });
	farbtastic.setColor(jQuery('#bz_input_' + domid).val());
}

//update inputs value
function pickColor(domid,color) {
	boxid = '#bz_box_' + domid;
	inputid = '#bz_input_' + domid;
	jQuery(boxid).css('background-color', color );
	jQuery(inputid).val(color);
}

jQuery(document).ready(function(){
	boozurkSwitchTab.set('colors');
	
	jQuery('.bz_input').keyup(function() {
		var _hex = jQuery(this).val();
		var hex = _hex;
		if ( hex.substr(0,1) != '#' )
			hex = '#' + hex;
		hex = hex.replace(/[^#a-fA-F0-9]+/, '');
		hex = hex.substring(0,7);
		if ( hex != _hex )
			jQuery(this).val(hex);
		if ( hex.length == 4 || hex.length == 7 )
			pickColor( jQuery(this).attr("id").replace('bz_input_', '') , hex );
	});

	jQuery(document).mousedown(function(){
		jQuery('.bz_cp').each( function() {
			var display = jQuery(this).css('display');
			if (display == 'block')
				jQuery(this).fadeOut(200);
		});
	});
	jQuery('.fade').each( function() {
		jQuery(this).delay(2000).fadeOut(600);
	});

});

boozurkSwitchTab = {
	set : function (thisset) { //show only a set of rows
		if ( thisset != 'info' ) {
			jQuery('#boozurk-infos').css({ 'display' : 'none' });
			jQuery('#boozurk-options').css({ 'display' : '' });
			thisclass = '.bz-tabgroup-' + thisset;
			thissel = '#bz-selgroup-' + thisset;
			jQuery('.bz-tab-opt').css({ 'display' : 'none' });
			jQuery(thisclass).css({ 'display' : '' });
			jQuery('#bz-tabselector li').removeClass("sel-active");
			jQuery(thissel).addClass("sel-active");
		} else {
			jQuery('#boozurk-infos').css({ 'display' : '' });
			jQuery('#boozurk-options').css({ 'display' : 'none' });
			jQuery('#bz-tabselector li').removeClass("sel-active");
			jQuery('#bz-selgroup-info').addClass("sel-active");
		}
	}
}

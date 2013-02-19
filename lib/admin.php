<?php
/**
 * admin.php
 *
 * All that affetcs the admin side (options page, styles, scripts, etc)
 *
 * @package boozurk
 * @since boozurk 2.04
 */


add_action( 'admin_menu', 'boozurk_create_menu' ); // Add admin menus
add_action( 'admin_notices', 'boozurk_setopt_admin_notice' );
add_action( 'manage_posts_custom_column', 'boozurk_addthumbvalue', 10, 2 ); // column-thumbnail for posts
add_action( 'manage_pages_custom_column', 'boozurk_addthumbvalue', 10, 2 ); // column-thumbnail for pages
add_action( 'admin_head', 'boozurk_post_manage_style' ); // column-thumbnail style
add_action( 'admin_init', 'boozurk_default_options' ); // tell WordPress to run boozurk_default_options()

add_filter( 'manage_posts_columns', 'boozurk_addthumbcolumn' ); // column-thumbnail for posts
add_filter( 'manage_pages_columns', 'boozurk_addthumbcolumn' ); // column-thumbnail for pages

// create theme option page
function boozurk_create_menu() {
	//create new top-level menu
	$pageopt = add_theme_page( __( 'Theme Options','boozurk' ), __( 'Theme Options','boozurk' ), 'edit_theme_options', 'boozurk_functions', 'boozurk_edit_options' );
	//call register settings function
	add_action( 'admin_init', 'boozurk_register_tb_settings' );
	add_action( 'admin_print_styles-' . $pageopt, 'boozurk_theme_admin_styles' );
	add_action( 'admin_print_scripts-' . $pageopt, 'boozurk_theme_admin_scripts' );
	add_action( 'admin_print_styles-widgets.php', 'boozurk_widgets_style' );
	add_action( 'admin_print_scripts-widgets.php', 'boozurk_widgets_scripts' );
}

//register boozurk settings
function boozurk_register_tb_settings() {
	register_setting( 'boozurk_settings_group', 'boozurk_options', 'boozurk_sanitize_options' );
}

// check and set default options 
function boozurk_default_options() {
		global $boozurk_version;
		$the_coa = boozurk_get_coa();
		$the_opt = get_option( 'boozurk_options' );

		// if options are empty, sets the default values
		if ( empty( $the_opt ) || !isset( $the_opt ) ) {
			foreach ( $the_coa as $key => $val ) {
				$the_opt[$key] = $the_coa[$key]['default'];
			}
			$the_opt['version'] = ''; //null value to keep admin notice alive and invite user to discover theme options
			update_option( 'boozurk_options' , $the_opt );
		} else if ( !isset( $the_opt['version'] ) || $the_opt['version'] < $boozurk_version ) {
			// check for unset values and set them to default value -> when updated to new version
			foreach ( $the_coa as $key => $val ) {
				if ( !isset( $the_opt[$key] ) ) $the_opt[$key] = $the_coa[$key]['default'];
			}
			$the_opt['version'] = ''; //null value to keep admin notice alive and invite user to discover theme options
			update_option( 'boozurk_options' , $the_opt );
		}
}

// print a reminder message for set the options after the theme is installed or updated
function boozurk_setopt_admin_notice() {
	global $boozurk_opt, $boozurk_version;
	if ( current_user_can( 'manage_options' ) && ( $boozurk_opt['version'] < $boozurk_version ) )
		echo '<div class="updated"><p><strong>' . sprintf( __( "%s theme says: \"Dont forget to set <a href=\"%s\">my options</a>!\"", 'boozurk' ), 'Boozurk', get_admin_url() . 'themes.php?page=boozurk_functions' ) . '</strong></p></div>';
}

//add js script to the options page
function boozurk_theme_admin_scripts() {
	global $boozurk_version;
	wp_enqueue_script( 'boozurk-options-script', get_template_directory_uri().'/js/options.dev.js',array('jquery','farbtastic','thickbox'),$boozurk_version, true ); //thebird js
	$data = array(
		'confirm_to_defaults' => __( 'Are you really sure you want to set all the options to their default values?', 'boozurk' )
	);
	wp_localize_script( 'boozurk-options-script', 'boozurk_options_l10n', $data );
}

//add custom stylesheet
function boozurk_widgets_style() {
	wp_enqueue_style( 'boozurk-widgets-style', get_template_directory_uri() . '/css/widgets.css', false, '', 'screen' );
}

//add js script to the widgets page
function boozurk_widgets_scripts() {
	global $boozurk_version;
	wp_enqueue_script( 'boozurk-widgets-scripts', get_template_directory_uri() . '/js/widgets.dev.js', array('jquery'), $boozurk_version, true );
}

// the custon header page style
function boozurk_theme_admin_styles() {
	wp_enqueue_style( 'boozurk-options-style', get_template_directory_uri() . '/css/options.css', array('farbtastic','thickbox'), '', 'screen' );
}

// sanitize options value
if ( !function_exists( 'boozurk_sanitize_options' ) ) {
	function boozurk_sanitize_options($input) {
		global $boozurk_version;

		$the_coa = boozurk_get_coa();

		foreach ( $the_coa as $key => $val ) {
	
			if( $the_coa[$key]['type'] == 'chk' ) {								//CHK
				if( !isset( $input[$key] ) ) {
					$input[$key] = 0;
				} else {
					$input[$key] = ( $input[$key] == 1 ? 1 : 0 );
				}

			} elseif( $the_coa[$key]['type'] == 'sel' ) {						//SEL
				if ( !in_array( $input[$key], $the_coa[$key]['options'] ) )
					$input[$key] = $the_coa[$key]['default'];

			} elseif( $the_coa[$key]['type'] == 'opt' ) {						//OPT
				if ( !in_array( $input[$key], $the_coa[$key]['options'] ) )
					$input[$key] = $the_coa[$key]['default'];

			} elseif( $the_coa[$key]['type'] == 'col' ) {						//COL
				$color = str_replace( '#' , '' , $input[$key] );
				$color = preg_replace( '/[^0-9a-fA-F]/' , '' , $color );
				$input[$key] = '#' . $color;

			} elseif( $the_coa[$key]['type'] == 'url' ) {						//URL
				$input[$key] = esc_url( trim( strip_tags( $input[$key] ) ) );

			} elseif( $the_coa[$key]['type'] == 'txt' ) {						//TXT
				if( !isset( $input[$key] ) ) {
					$input[$key] = '';
				} else {
					$input[$key] = trim( strip_tags( $input[$key] ) );
				}

			} elseif( $the_coa[$key]['type'] == 'int' ) {						//INT
				if( !isset( $input[$key] ) ) {
					$input[$key] = $the_coa[$key]['default'];
				} else {
					$input[$key] = (int) $input[$key] ;
				}

			} elseif( $the_coa[$key]['type'] == 'txtarea' ) {					//TXTAREA
				if( !isset( $input[$key] ) ) {
					$input[$key] = '';
				} else {
					$input[$key] = trim( strip_tags( $input[$key] ) );
				}
			}
		}

		foreach ( $input['boozurk_cat_colors'] as $key => $val ) {				//CATCOL
			$color = str_replace( '#' , '' , $input['boozurk_cat_colors'][$key] );
			$color = preg_replace( '/[^0-9a-fA-F]/' , '' , $color );
			$input['boozurk_cat_colors'][$key] = '#' . $color;
		}

		// check for required options
		foreach ( $the_coa as $key => $val ) {
			if ( $the_coa[$key]['req'] != '' ) { if ( $input[$the_coa[$key]['req']] == ( 0 || '') ) $input[$key] = 0; }
		}

		$input['version'] = $boozurk_version; // keep version number
		return $input;
	}
}

// the theme option page
if ( !function_exists( 'boozurk_edit_options' ) ) {
	function boozurk_edit_options() {

		if ( !current_user_can( 'edit_theme_options' ) ) wp_die( 'You do not have sufficient permissions to access this page.' );

		global $boozurk_opt, $boozurk_current_theme, $boozurk_version;

		$the_coa = boozurk_get_coa();
		$the_groups = boozurk_get_coa( 'groups' );
		$the_option_name = 'boozurk_options';

		if ( isset( $_GET['erase'] ) ) {
			$_SERVER['REQUEST_URI'] = remove_query_arg( 'erase', $_SERVER['REQUEST_URI'] );
			delete_option( $the_option_name );
			boozurk_default_options();
			$boozurk_opt = get_option( $the_option_name );
		}

		// update version value when admin visit options page
		if ( $boozurk_opt['version'] < $boozurk_version ) {
			$boozurk_opt['version'] = $boozurk_version;
			update_option( $the_option_name , $boozurk_opt );
		}

		$the_opt = $boozurk_opt;

		// options have been updated
		if ( isset( $_REQUEST['settings-updated'] ) ) {
			//return options save message
			echo '<div id="message" class="updated fade"><p><strong>' . __( 'Options saved.','boozurk' ) . '</strong></p></div>';
		}

		// options to defaults done
		if ( isset( $_GET['erase'] ) ) {
			echo '<div id="message" class="updated fade"><p><strong>' . __( 'Defaults values loaded.', 'boozurk' ) . '</strong></p></div>';
		}

	?>
		<div class="wrap" id="main-wrap">
			<div class="icon32" id="theme-icon"><br></div>
			<h2><?php echo $boozurk_current_theme . ' - ' . __( 'Theme Options','boozurk' ); ?></h2>
			<ul id="tabselector" class="hide-if-no-js">
<?php
				foreach( $the_groups as $key => $name ) {
?>
				<li id="selgroup-<?php echo $key; ?>"><a href="#" onClick="boozurkOptions.switchTab('<?php echo $key; ?>'); return false;"><?php echo $name; ?></a></li>
<?php 
				}
?>
				<li id="selgroup-info"><a href="#" onClick="boozurkOptions.switchTab('info'); return false;"><?php _e( 'Theme Info' , 'boozurk' ); ?></a></li>
			</ul>
			<ul id="selector" class="hide-if-js">
				<li id="theme-options-li"><a href="#theme-options"><?php _e( 'Options','boozurk' ); ?></a></li>
				<li id="theme-infos-li"><a href="#theme-infos"><?php _e( 'Theme Info','boozurk' ); ?></a></li>
			</ul>
			<div id="tabs-container">
				<div class="clear"></div>
				<div id="theme-options">
					<h2 class="hide-if-js" style="text-align: center;"><?php _e( 'Options','boozurk' ); ?></h2>
					<form method="post" action="options.php">
						<?php settings_fields( 'boozurk_settings_group' ); ?>
						<div id="stylediv">
							<?php foreach ($the_coa as $key => $val) { ?>
								<?php if ( isset( $the_coa[$key]['sub'] ) && !$the_coa[$key]['sub'] ) continue; ?>
								<div class="tab-opt tabgroup-<?php echo $the_coa[$key]['group']; ?>">
									<span class="column-nam"><?php echo $the_coa[$key]['description']; ?></span>
								<?php if ( !isset ( $the_opt[$key] ) ) $the_opt[$key] = $the_coa[$key]['default']; ?>
								<?php if ( $the_coa[$key]['type'] == 'chk' ) { ?>
										<input name="<?php echo $the_option_name; ?>[<?php echo $key; ?>]" value="1" type="checkbox" class="ww_opt_p_checkbox" <?php checked( 1 , $the_opt[$key] ); ?> />
								<?php } elseif ( $the_coa[$key]['type'] == 'sel' ) { ?>
										<select name="<?php echo $the_option_name; ?>[<?php echo $key; ?>]">
										<?php foreach($the_coa[$key]['options'] as $optionkey => $option) { ?>
											<option value="<?php echo $option; ?>" <?php selected( $the_opt[$key], $option ); ?>><?php echo $the_coa[$key]['options_l10n'][$optionkey]; ?></option>
										<?php } ?>
										</select>
								<?php } elseif ( $the_coa[$key]['type'] == 'opt' ) { ?>
									<?php foreach( $the_coa[$key]['options'] as $optionkey => $option ) { ?>
										<label title="<?php echo esc_attr($option); ?>"><input type="radio" <?php checked( $the_opt[$key], $option ); ?> value="<?php echo $option; ?>" name="<?php echo $the_option_name; ?>[<?php echo $key; ?>]"> <span><?php echo $the_coa[$key]['options_readable'][$optionkey]; ?></span></label>
									<?php } ?>
								<?php } elseif ( $the_coa[$key]['type'] == 'url' ) { ?>
										<input class="boozurk_text" id="option_field_<?php echo $key; ?>" type="text" name="<?php echo $the_option_name; ?>[<?php echo $key; ?>]" value="<?php echo $the_opt[$key]; ?>" />
										<?php if ( $key == 'boozurk_logo' ) {
											$boozurk_arr_params['boozurk_media'] = '1'; 
											$boozurk_arr_params['_wpnonce'] = wp_create_nonce( 'boozurk-logo-nonce' );
											?>
											<input class="hide-if-no-js button" type="button" value="<?php echo __( 'Select', 'boozurk' ); ?>" onClick="tb_show( '<?php echo __( 'Click an image to select', 'boozurk' ); ?>', '<?php echo add_query_arg( $boozurk_arr_params, home_url() ); ?>&amp;TB_iframe=true'); return false;" />
										<?php } ?>
								<?php } elseif ( $the_coa[$key]['type'] == 'txt' ) { ?>
										<input class="boozurk_text" id="option_field_<?php echo $key; ?>" type="text" name="<?php echo $the_option_name; ?>[<?php echo $key; ?>]" value="<?php echo $the_opt[$key]; ?>" />
								<?php } elseif ( $the_coa[$key]['type'] == 'int' ) { ?>
										<input class="boozurk_text" id="option_field_<?php echo $key; ?>" type="text" name="<?php echo $the_option_name; ?>[<?php echo $key; ?>]" value="<?php echo $the_opt[$key]; ?>" />
								<?php } elseif ( $the_coa[$key]['type'] == 'txtarea' ) { ?>
										<textarea name="<?php echo $the_option_name; ?>[<?php echo $key; ?>]"><?php echo $the_opt[$key]; ?></textarea>
								<?php }	?>
								<?php if ( $the_coa[$key]['info'] != '' ) { ?><div class="column-des"><?php echo $the_coa[$key]['info']; ?></div><?php } ?>
								<?php if ( isset( $the_coa[$key]['sub'] ) ) { ?>
										<div class="sub-opt-wrap">
									<?php foreach ($the_coa[$key]['sub'] as $subkey => $subval) { ?>
										<?php if ( $subval == '' ) { echo '<br />'; continue;} ?>
											<div class="sub-opt">
											<?php if ( !isset ($the_opt[$subval]) ) $the_opt[$subval] = $the_coa[$subval]['default']; ?>
												<?php if ( $the_coa[$subval]['description'] != '' ) { ?><span><?php echo $the_coa[$subval]['description']; ?> : </span><?php } ?>
											<?php if ( $the_coa[$subval]['type'] == 'chk' ) { ?>
													<input name="<?php echo $the_option_name; ?>[<?php echo $subval; ?>]" value="1" type="checkbox" class="ww_opt_p_checkbox" <?php checked( 1 , $the_opt[$subval] ); ?> />
													<span><?php echo $the_coa[$subval]['info']; ?></span>
											<?php } elseif ( $the_coa[$subval]['type'] == 'sel' ) { ?>
													<select name="<?php echo $the_option_name; ?>[<?php echo $subval; ?>]">
													<?php foreach($the_coa[$subval]['options'] as $optionkey => $option) { ?>
														<option value="<?php echo $option; ?>" <?php selected( $the_opt[$subval], $option ); ?>><?php echo $the_coa[$subval]['options_l10n'][$optionkey]; ?></option>
													<?php } ?>
													</select>
													<span><?php echo $the_coa[$subval]['info']; ?></span>
											<?php } elseif ( $the_coa[$subval]['type'] == 'opt' ) { ?>
												<?php foreach( $the_coa[$subval]['options'] as $optionkey => $option ) { ?>
													<label title="<?php echo esc_attr($option); ?>"><input type="radio" <?php checked( $the_opt[$subval], $option ); ?> value="<?php echo $option; ?>" name="<?php echo $the_option_name; ?>[<?php echo $subval; ?>]"> <span><?php echo $the_coa[$subval]['options_readable'][$optionkey]; ?></span></label>
												<?php } ?>
											<?php } elseif ( $the_coa[$subval]['type'] == 'url' ) { ?>
													<input class="boozurk_text" id="option_field_<?php echo $subval; ?>" type="text" name="<?php echo $the_option_name; ?>[<?php echo $subval; ?>]" value="<?php echo $the_opt[$subval]; ?>" />
													<span><?php echo $the_coa[$subval]['info']; ?></span>
											<?php } elseif ( $the_coa[$subval]['type'] == 'txt' ) { ?>
													<input class="boozurk_text" id="option_field_<?php echo $subval; ?>" type="text" name="<?php echo $the_option_name; ?>[<?php echo $subval; ?>]" value="<?php echo $the_opt[$subval]; ?>" />
													<span><?php echo $the_coa[$subval]['info']; ?></span>
											<?php } elseif ( $the_coa[$subval]['type'] == 'int' ) { ?>
													<input class="boozurk_text" id="option_field_<?php echo $subval; ?>" type="text" name="<?php echo $the_option_name; ?>[<?php echo $subval; ?>]" value="<?php echo $the_opt[$subval]; ?>" />
													<span><?php echo $the_coa[$subval]['info']; ?></span>
											<?php } elseif ( $the_coa[$subval]['type'] == 'col' ) { ?>
													<div class="col-tools">
														<span><?php echo $the_coa[$subval]['info']; ?></span>
														<input onclick="boozurkOptions.showColorPicker('<?php echo $subval; ?>');" style="background-color:<?php echo $the_opt[$subval]; ?>;" class="color_preview_box" type="text" id="boozurk_box_<?php echo $subval; ?>" value="" readonly="readonly" />
														<div class="boozurk_cp" id="boozurk_colorpicker_<?php echo $subval; ?>"></div>
														<input class="boozurk_input" id="boozurk_input_<?php echo $subval; ?>" type="text" name="<?php echo $the_option_name; ?>[<?php echo $subval; ?>]" value="<?php echo $the_opt[$subval]; ?>" />
														<br />
														<a class="hide-if-no-js" href="#" onclick="boozurkOptions.showColorPicker('<?php echo $subval; ?>'); return false;"><?php _e( 'Select a Color' , 'boozurk' ); ?></a>
														<br />
														<a class="hide-if-no-js" style="color:<?php echo $the_coa[$subval]['default']; ?>;" href="#" onclick="boozurkOptions.updateColor('<?php echo $subval; ?>','<?php echo $the_coa[$subval]['default']; ?>'); return false;"><?php _e( 'Default' , 'boozurk' ); ?></a>
														<br class="clear" />
													</div>
											<?php } elseif ( $the_coa[$subval]['type'] == 'catcol' ) { ?>
													<?php
														$args=array(
															'orderby' => 'name',
															'order' => 'ASC',
															'hide_empty' => 0
														);
														$categories=get_categories($args);
														foreach($categories as $category) {
															$hexnumber = '#';
															for ($i2=1; $i2<=3; $i2++) {
																$hexnumber .= dechex( rand(64,255) );
															}
															$catcolor = isset($the_opt[$subval][$category->term_id]) ? $the_opt[$subval][$category->term_id] : $hexnumber;
													?>
														<div class="col-tools">
															<span><?php echo $category->name; ?></span>
															<input onclick="boozurkOptions.showColorPicker('<?php echo $subval.'-'.$category->term_id; ?>');" style="background-color:<?php echo $catcolor; ?>;" class="color_preview_box" type="text" id="boozurk_box_<?php echo $subval.'-'.$category->term_id; ?>" value="" readonly="readonly" />
															<div class="boozurk_cp" id="boozurk_colorpicker_<?php echo $subval.'-'.$category->term_id; ?>"></div>
															<input class="boozurk_input" id="boozurk_input_<?php echo $subval.'-'.$category->term_id; ?>" type="text" name="<?php echo $the_option_name; ?>[<?php echo $subval; ?>][<?php echo $category->term_id; ?>]" value="<?php echo $catcolor; ?>" />
															<br />
															<a class="hide-if-no-js" href="#" onclick="boozurkOptions.showColorPicker('<?php echo $subval.'-'.$category->term_id; ?>'); return false;"><?php _e( 'Select a Color' , 'boozurk' ); ?></a>
															<br />
															<a class="hide-if-no-js" style="color:<?php echo $the_coa[$subval]['defaultcolor']; ?>;" href="#" onclick="boozurkOptions.updateColor('<?php echo $subval.'-'.$category->term_id; ?>','<?php echo $the_coa[$subval]['defaultcolor']; ?>'); return false;"><?php _e( 'Default' , 'boozurk' ); ?></a>
															<br class="clear" />
															<?php if ( $category->description ) { ?><div class="column-des"><?php echo $category->description; ?></div><?php } ?>
														</div>
													<?php }	?>
													
											<?php }	?>
												</div>
										<?php }	?>
											<br class="clear" />
										</div>
								<?php }	?>
									<?php if ( $the_coa[$key]['req'] != '' ) { ?><div class="column-req"><?php echo '<u>' . __('requires','boozurk') . '</u>: ' . $the_coa[$the_coa[$key]['req']]['description']; ?></div><?php } ?>
								</div>
							<?php }	?>
						</div>
						<p id="buttons">
							<input type="hidden" name="<?php echo $the_option_name; ?>[hidden_opt]" value="default" />
							<input class="button-primary" type="submit" name="Submit" value="<?php _e( 'Update Options' , 'boozurk' ); ?>" />
							<a href="themes.php?page=boozurk_functions" target="_self"><?php _e( 'Undo Changes' , 'boozurk' ); ?></a>
							|
							<a id="to-defaults" href="themes.php?page=boozurk_functions&erase=1" target="_self"><?php _e( 'Back to defaults' , 'boozurk' ); ?></a>
						</p>
					</form>
					<p class="stylediv" style="clear: both; text-align: center; border: 1px solid #ccc;">
						<small>
							<?php _e( 'If you like/dislike this theme, or if you encounter any issues using it, please let us know it.', 'boozurk' ); ?><br />
							<a href="<?php echo esc_url( 'http://www.twobeers.net/annunci/tema-per-wordpress-boozurk' ); ?>" title="boozurk theme" target="_blank"><?php _e( 'Leave a feedback', 'boozurk' ); ?></a>
						</small>
					</p>
					<p class="stylediv" style="clear: both; text-align: center; border: 1px solid #ccc; margin-top: 10px;">
						<small>Support the theme in your language, provide a <a href="<?php echo esc_url( 'http://www.twobeers.net/wp-themes/themes-translations-wordpress' ); ?>" title="Themes translation" target="_blank">translation</a>.</small>
					</p>
				</div>
				<div id="theme-infos">
					<h2 class="hide-if-js" style="text-align: center;"><?php _e( 'Theme Info', 'boozurk' ); ?></h2>
					<?php locate_template( 'readme.html',true ); ?>
				</div>
				<div class="clear"></div>
			</div>
		</div>
	<?php
	}
}

// Add Thumbnail Column in Manage Posts/Pages List
function boozurk_addthumbcolumn($cols) {
	$cols['thumbnail'] = ucwords( __('thumbnail', 'boozurk') );
	return $cols;
}

// Add Thumbnails in Manage Posts/Pages List
function boozurk_addthumbvalue($column_name, $post_id) {
		$width = (int) 60;
		$height = (int) 60;
		if ( 'thumbnail' == $column_name ) {
			// thumbnail of WP 2.9
			$thumbnail_id = get_post_meta( $post_id, '_thumbnail_id', true );
			if ($thumbnail_id) $thumb = wp_get_attachment_image( $thumbnail_id, array($width, $height), true );
			if ( isset($thumb) && $thumb ) {
				echo $thumb;
			} else {
				echo '';
			}
		}
}

// Add Thumbnail Column style in Manage Posts/Pages List
if ( !function_exists( 'boozurk_post_manage_style' ) ) {
	function boozurk_post_manage_style(){
?>
<style type="text/css">
	.fixed .column-thumbnail {
		width: 70px;
	}
</style>
<?php
	}
}

<?php
/**
 * admin.php
 *
 * All that affects the admin side (options page, styles, scripts, etc)
 *
 * @package Boozurk
 * @since 2.04
 */


class Boozurk_Admin {

	//some bridge functions and variables
	var $option_name = 'boozurk_options';

	var $slug = 'boozurk';


	function get_opt( $key ) {

		return boozurk_get_opt( $key );

	}


	function get_info( $key ) {

		return boozurk_get_info( $key );

	}


	function get_coa( $key = '' ) {

		return boozurk_get_coa( $key );

	}


	var $the_opt = array();


	function __construct() {

		/* Custom actions - WP hooks */
		add_action( 'admin_menu'					, array( $this, 'create_menu' ) ); // Add admin menus
		add_action( 'admin_notices'					, array( $this, 'setopt_admin_notice' ) );
		add_action( 'manage_posts_custom_column'	, array( $this, 'add_thumb_value' ), 10, 2 ); // column-thumbnail for posts
		add_action( 'manage_pages_custom_column'	, array( $this, 'add_thumb_value' ), 10, 2 ); // column-thumbnail for pages
		add_action( 'admin_head'					, array( $this, 'post_manage_style' ) ); // column-thumbnail style
		add_action( 'admin_init'					, array( $this, 'default_options' ) ); // tell WordPress to run default_options()
		add_action( 'admin_bar_menu'				, array( $this, 'admin_bar_plus' ), 999 );

		/* Custom filters - WP hooks */

		add_filter( 'manage_posts_columns'			, array( $this, 'add_thumb_column' ) ); // column-thumbnail for posts
		add_filter( 'manage_pages_columns'			, array( $this, 'add_thumb_column' ) ); // column-thumbnail for pages

	}


	// create theme option page
	function create_menu() {

		$pageopt = add_theme_page( __( 'Theme Options','boozurk' ), __( 'Theme Options','boozurk' ), 'edit_theme_options', 'theme_options', array( $this, 'edit_options' ) ); //create new top-level menu

		add_action( 'admin_init'						, array( $this, 'register_settings' ) ); //call register settings function
		add_action( 'admin_print_styles-' . $pageopt	, array( $this, 'options_style' ) );
		add_action( 'admin_print_scripts-' . $pageopt	, array( $this, 'options_scripts' ) );

	}


	//register settings
	function register_settings() {

		register_setting( 'theme_options_fields', $this->option_name, array( $this, 'sanitize_options' ) );

	}


	// check and set default options 
	function default_options() {

		$the_coa = $this->get_coa();
		$this->the_opt = get_option( $this->option_name );

		// if options are empty, sets the default values
		if ( empty( $this->the_opt ) || !isset( $this->the_opt ) ) {

			foreach ( $the_coa as $key => $val ) {
				$this->the_opt[$key] = $the_coa[$key]['default'];
			}
			$this->the_opt['version'] = ''; //null value to keep admin notice alive and invite user to discover theme options
			update_option( $this->option_name , $this->the_opt );

		} else if ( !isset( $this->the_opt['version'] ) || $this->the_opt['version'] < $this->get_info( 'version' ) ) {

			// check for unset values and set them to default value -> when updated to new version
			foreach ( $the_coa as $key => $val ) {
				if ( !isset( $this->the_opt[$key] ) ) $this->the_opt[$key] = $the_coa[$key]['default'];
			}
			$this->the_opt['version'] = ''; //null value to keep admin notice alive and invite user to discover theme options
			update_option( $this->option_name , $this->the_opt );

		}

	}


	// print a reminder message for set the options after the theme is installed or updated
	function setopt_admin_notice() {

		if ( current_user_can( 'manage_options' ) && ( $this->get_opt( 'version' ) < $this->get_info( 'version' ) ) )
			echo '<div class="updated"><p><strong>' . sprintf( __( "%s theme says: \"Dont forget to set <a href=\"%s\">my options</a>!\"", 'boozurk' ), 'Boozurk', get_admin_url() . 'themes.php?page=theme_options' ) . '</strong></p></div>';

	}


	//add js script to the options page
	function options_scripts() {

		wp_enqueue_media();
		wp_enqueue_script( 'wp-color-picker' );
		wp_enqueue_script( $this->slug . '-options-script', get_template_directory_uri().'/js/options.js', array( 'jquery', 'thickbox' ), $this->get_info( 'version' ), true ); //thebird js

		$data = array(
			'confirm_to_defaults' => __( 'Are you really sure you want to set all the options to their default values?', 'boozurk' )
		);
		wp_localize_script( $this->slug . '-options-script', 'theme_options_l10n', $data );

	}


	// the custon header page style
	function options_style() {

		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_style( $this->slug . '-options-style', get_template_directory_uri() . '/css/options.css', array(), '', 'screen' );

	}


	// sanitize options value
	function sanitize_options($input) {

		$the_coa = $this->get_coa();

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
				if( isset( $the_coa[$key]['range'] ) ) {
					$input[$key] = min( $input[$key], $the_coa[$key]['range'][1] );
					$input[$key] = max( $input[$key], $the_coa[$key]['range'][0] );
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

		$input['version'] = $this->get_info( 'version' ); // keep version number

		return $input;

	}


	function print_option( $option, $value, $is_sub, $option_name, $key, $before = '', $after = '' ) {

		echo $before;

		switch ( $option['type'] ) {
			case 'chk':
				?>
					<input name="<?php echo $option_name; ?>[<?php echo $key; ?>]" value="1" type="checkbox" class="ww_opt_p_checkbox" <?php checked( 1 , $value ); ?> />
				<?php
				break;
			case 'sel':
				?>
					<select name="<?php echo $option_name; ?>[<?php echo $key; ?>]">
					<?php foreach($option['options'] as $optionkey => $optionval) { ?>
						<option value="<?php echo $optionval; ?>" <?php selected( $value, $optionval ); ?>><?php echo $option['options_l10n'][$optionkey]; ?></option>
					<?php } ?>
					</select>
				<?php
				break;
			case 'opt':
				foreach( $option['options'] as $optionkey => $optionval ) {
				?>
					<label name="<?php echo $option_name; ?>[<?php echo $key; ?>]" title="<?php echo esc_attr($optionval); ?>"><input type="radio" <?php checked( $value, $optionval ); ?> value="<?php echo $optionval; ?>"> <span><?php echo $option['options_l10n'][$optionkey]; ?></span></label>
				<?php
				}
				break;
			case 'col':
				?>
					<input name="<?php echo $option_name; ?>[<?php echo $key; ?>]" class="theme_option_input theme_option_cp" type="text" id="<?php echo $option_name; ?>[<?php echo $key; ?>]" value="<?php echo $value; ?>" data-default-color="<?php echo $option['default']; ?>" />
					<span class="description hide-if-js"><?php _e( 'Default' , 'boozurk' ); ?>: <?php echo $option['default']; ?></span>
				<?php
				break;
			case 'url':
				?>
					<input name="<?php echo $option_name; ?>[<?php echo $key; ?>]" class="theme_option_text" id="option_field_<?php echo $key; ?>" type="text" value="<?php echo $value; ?>" />
				<?php
				break;
			case 'txt':
				?>
					<input name="<?php echo $option_name; ?>[<?php echo $key; ?>]" class="theme_option_text" id="option_field_<?php echo $key; ?>" type="text" value="<?php echo $value; ?>" />
				<?php
				break;
			case 'int':
				?>
					<input name="<?php echo $option_name; ?>[<?php echo $key; ?>]" class="theme_option_text" id="option_field_<?php echo $key; ?>" type="text" value="<?php echo $value; ?>" />
				<?php
				break;
			case 'txtarea':
				?>
					<textarea name="<?php echo $option_name; ?>[<?php echo $key; ?>]"><?php echo $value; ?></textarea>
				<?php
				break;
		}

		echo $after;

	}

	// the theme option page
	function edit_options() {

		if ( !current_user_can( 'edit_theme_options' ) ) wp_die( 'You do not have sufficient permissions to access this page.' );

		$the_coa		= $this->get_coa();
		$the_hierarchy	= $this->get_coa( 'hierarchy' );

		if ( isset( $_GET['erase'] ) ) {
			$_SERVER['REQUEST_URI'] = remove_query_arg( 'erase', $_SERVER['REQUEST_URI'] );
			delete_option( $this->option_name );
			$this->default_options();
			$this->the_opt = get_option( $this->option_name );
		}

		// update version value when admin visit options page
		if ( $this->the_opt['version'] < $this->get_info( 'version' ) ) {
			$this->the_opt['version'] = $this->get_info( 'version' );
			update_option( $this->option_name , $this->the_opt );
		}

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
		<h2><?php echo $this->get_info( 'current_theme' ) . ' - ' . __( 'Theme Options','boozurk' ); ?></h2>

		<div id="theme_donation">
			<small><?php _e( 'Our developers need coffee (and beer). How about a small donation?', 'boozurk' ); ?></small>
			<br />
			<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=5FWKWFH62RRC8"><img src="https://www.paypalobjects.com/en_US/GB/i/btn/btn_donateCC_LG.gif" alt="PayPal - The safer, easier way to pay online."/></a>
		</div>

		<h2 id="tabselector" class="nav-tab-wrapper">
			<img src="<?php echo get_template_directory_uri() . '/images/boozurk.png' ?>" alt="boozurk"/>
			<?php foreach( $the_hierarchy as $key => $genus ) { ?>
				<a id="selgroup-<?php echo $key; ?>" class="nav-tab" href="#" onClick="themeOptions.switchTab('<?php echo $key; ?>'); return false;"><?php echo $genus['label']; ?></a>
			<?php } ?>
			<a id="selgroup-info" class="nav-tab" href="#" onClick="themeOptions.switchTab('info'); return false;"><?php _e( 'Theme Info' , 'boozurk' ); ?></a>
		</h2>

		<div id="theme-options">
			<form method="post" action="options.php">
				<?php settings_fields( 'theme_options_fields' ); ?>
				<?php foreach( $the_hierarchy as $genus_key => $genus ) { ?>
					<div class="tabgroup tabgroup-<?php echo $genus_key ?>">
						<?php foreach( $genus['sub'] as $species_key => $species ) { ?>
							<h2><?php echo $species['label']; ?></h2>
							<?php if ( $species['description'] != '' ) echo '<small>' . $species['description'] . '</small>'; ?>
							<div class="">
								<?php foreach( $species['sub'] as $item_key => $item ) { ?>
									<?php $key = $item; ?>
									<div class="tab-opt opt-<?php echo $the_coa[$key]['type']; ?>">
										<?php if ( !isset ( $this->the_opt[$key] ) ) $this->the_opt[$key] = $the_coa[$key]['default']; ?>
										<?php 
											switch ( $the_coa[$key]['type'] ) {
												case 'lbl':
													echo '<span class="column-nam">' . $the_coa[$key]['description'] . '</span>';
													break;

												case 'chk':
													$this->print_option( $the_coa[$key], $this->the_opt[$key], false, $this->option_name, $key , '<span class="column-nam">' . $the_coa[$key]['description'] . '</span>', '' );
													break;

												case 'sel':
													$this->print_option( $the_coa[$key], $this->the_opt[$key], false, $this->option_name, $key , '<span class="column-nam">' . $the_coa[$key]['description'] . '</span>', '' );
													break;

												case 'opt':
													$this->print_option( $the_coa[$key], $this->the_opt[$key], false, $this->option_name, $key , '<span class="column-nam">' . $the_coa[$key]['description'] . '</span>', '' );
													break;

												case 'col':
													$this->print_option( $the_coa[$key], $this->the_opt[$key], false, $this->option_name, $key , '<div class="col-tools"><span class="column-nam">' . $the_coa[$key]['description'] . '</span><br />', '</div>' );
													break;

												case 'url':
													if ( $key == 'boozurk_logo' )
														$after = '<a id="choose-logo-from-library-link" class="button hide-if-no-js" data-choose="' . esc_attr__( 'Choose a Logo Image' , 'boozurk' ) . '" data-update="' . esc_attr__( 'Set as logo' , 'boozurk' ) . '">' . __( 'Choose Image' , 'boozurk' ) . '</a>';
													$this->print_option( $the_coa[$key], $this->the_opt[$key], false, $this->option_name, $key , '<span class="column-nam">' . $the_coa[$key]['description'] . '</span>', $after );
													break;

												case 'txt':
													$this->print_option( $the_coa[$key], $this->the_opt[$key], false, $this->option_name, $key , '<span class="column-nam">' . $the_coa[$key]['description'] . '</span>', '' );
													break;

												case 'int':
													$this->print_option( $the_coa[$key], $this->the_opt[$key], false, $this->option_name, $key , '<span class="column-nam">' . $the_coa[$key]['description'] . '</span>', '' );
													break;

												case 'txtarea':
													$this->print_option( $the_coa[$key], $this->the_opt[$key], false, $this->option_name, $key , '<span class="column-nam">' . $the_coa[$key]['description'] . '</span>', '' );
													break;

												case 'catcol':
													echo '<span class="column-nam">' . $the_coa[$key]['description'] . '</span>';
													$args = array(
														'orderby'		=> 'name',
														'order'			=> 'ASC',
														'hide_empty'	=> 0,
													);
													$categories = get_categories( $args );
													foreach( $categories as $category ) {
														$hexnumber = '#';
														for ( $i2=1; $i2<=3; $i2++ ) {
															$hexnumber .= dechex( rand( 64, 255 ) );
														}
														$catcolor = isset( $this->the_opt[$key][$category->term_id] ) ? $this->the_opt[$key][$category->term_id] : $hexnumber;
														
														$this->print_option( array( 'type' => 'col', 'default' => $the_coa[$key]['defaultcolor'] ), $catcolor, false, $this->option_name, $key . '][' . $category->term_id, '<div class="col-tools"><span>' . $category->name . ' (' . $category->count . ')</span><br />', '<span class="description hide-if-js">' .  __( 'Default' , 'boozurk' ) . ': ' . $the_coa[$key]['defaultcolor'] . '</span></div>' );

													}
													break;

											}
										?>
										<?php if ( $the_coa[$key]['info'] != '' ) { ?><div class="column-des"><?php echo $the_coa[$key]['info']; ?></div><?php } ?>

										<?php if ( isset( $the_coa[$key]['sub'] ) ) { ?>
											<div class="sub-opt-wrap">
												<?php foreach ($the_coa[$key]['sub'] as $subkey) { ?>
													<?php if ( $subkey == '' ) { echo '<br />'; continue;} ?>
													<?php $after =( $the_coa[$subkey]['info'] != '' ) ? '<span>' . $the_coa[$subkey]['info'] . '</span>' : ''; ?>
													<div class="sub-opt">
														<?php if ( !isset ($this->the_opt[$subkey]) ) $this->the_opt[$subkey] = $the_coa[$subkey]['default']; ?>
														<?php 
															switch ( $the_coa[$subkey]['type'] ) {
																case 'chk':
																	$this->print_option( $the_coa[$subkey], $this->the_opt[$subkey], false, $this->option_name, $subkey , '<span>' . $the_coa[$subkey]['description'] . ' : </span>', $after );
																	break;

																case 'sel':
																	$this->print_option( $the_coa[$subkey], $this->the_opt[$subkey], false, $this->option_name, $subkey , '<span>' . $the_coa[$subkey]['description'] . ' : </span>', $after );
																	break;

																case 'opt':
																	$this->print_option( $the_coa[$subkey], $this->the_opt[$subkey], false, $this->option_name, $subkey , '<span>' . $the_coa[$subkey]['description'] . ' : </span>', $after );
																	break;

																case 'col':
																	$this->print_option( $the_coa[$subkey], $this->the_opt[$subkey], false, $this->option_name, $subkey , '<div class="col-tools"><span>' . $the_coa[$subkey]['description'] . '</span><br />', '</div>' . $after );
																	break;

																case 'url':
																	if ( $subkey == 'boozurk_logo' )
																		$after = '<a id="choose-logo-from-library-link" class="button hide-if-no-js" data-choose="' . esc_attr__( 'Choose a Logo Image' , 'boozurk' ) . '" data-update="' . esc_attr__( 'Set as logo' , 'boozurk' ) . '">' . __( 'Choose Image' , 'boozurk' ) . '</a>' . $after;
																	$this->print_option( $the_coa[$subkey], $this->the_opt[$subkey], false, $this->option_name, $subkey , '<span>' . $the_coa[$subkey]['description'] . ' : </span>', $after );
																	break;

																case 'txt':
																	$this->print_option( $the_coa[$subkey], $this->the_opt[$subkey], false, $this->option_name, $subkey , '<span>' . $the_coa[$subkey]['description'] . ' : </span>', $after );
																	break;

																case 'int':
																	$this->print_option( $the_coa[$subkey], $this->the_opt[$subkey], false, $this->option_name, $subkey , '<span>' . $the_coa[$subkey]['description'] . ' : </span>', $after );
																	break;

																case 'txtarea':
																	$this->print_option( $the_coa[$subkey], $this->the_opt[$subkey], false, $this->option_name, $subkey , '<span>' . $the_coa[$subkey]['description'] . ' : </span>', $after );
																	break;

																case 'catcol':
																	echo '<span class="column-nam">' . $the_coa[$subkey]['description'] . '</span>';
																	$args = array(
																		'orderby'		=> 'name',
																		'order'			=> 'ASC',
																		'hide_empty'	=> 0,
																	);
																	$categories = get_categories( $args );
																	foreach( $categories as $category ) {
																		$hexnumber = '#';
																		for ( $i2=1; $i2<=3; $i2++ ) {
																			$hexnumber .= dechex( rand( 64, 255 ) );
																		}
																		$catcolor = isset( $this->the_opt[$subkey][$category->term_id] ) ? $this->the_opt[$subkey][$category->term_id] : $hexnumber;

																		$this->print_option( array( 'type' => 'col', 'default' => $the_coa[$subkey]['defaultcolor'] ), $catcolor, false, $this->option_name, $subkey . '][' . $category->term_id, '<div class="col-tools"><span>' . $category->name . ' (' . $category->count . ')</span><br />', '<span class="description hide-if-js">' .  __( 'Default' , 'boozurk' ) . ': ' . $the_coa[$subkey]['defaultcolor'] . '</span></div>' );

																	}
																	break;

															}
														?>
													</div>
												<?php } ?>
												<br class="clear" />
											</div>
										<?php } ?>
										<?php if ( $the_coa[$key]['req'] != '' ) { ?><div class="column-req"><?php echo '<u>' . __('requires','boozurk') . '</u>: ' . $the_coa[$the_coa[$key]['req']]['description']; ?></div><?php } ?>
									</div>
								<?php } ?>
							</div>
						<?php } ?>
					</div>
				<?php } ?>
				<div id="buttons">
					<input type="hidden" name="<?php echo $this->option_name; ?>[hidden_opt]" value="default" />
					<input class="button button-primary" type="submit" name="Submit" value="<?php _e( 'Update Options' , 'boozurk' ); ?>" />
					<br />
					-
					<br />
					<a class="button" href="themes.php?page=theme_options" target="_self"><?php _e( 'Undo Changes' , 'boozurk' ); ?></a>
					<br />
					-
					<br />
					<a class="button" id="to-defaults" href="themes.php?page=theme_options&erase=1" target="_self"><?php _e( 'Back to defaults' , 'boozurk' ); ?></a>
				</div>
			</form>
			<div id="theme_bottom">
				<small>
					<?php _e( 'If you like/dislike this theme, or if you encounter any issues using it, please let us know it.', 'boozurk' ); ?><br />
					<a href="<?php echo esc_url( 'http://www.twobeers.net/annunci/tema-per-wordpress-boozurk' ); ?>" target="_blank"><?php _e( 'Leave a feedback', 'boozurk' ); ?></a>
				</small>
				<br />
				-
				<br />
				<small>Support the theme in your language, provide a <a href="<?php echo esc_url( 'http://www.twobeers.net/wp-themes/themes-translations-wordpress' ); ?>" title="Themes translation" target="_blank">translation</a>.</small>
			</div>
		</div>
		<div id="theme-infos">
			<h2 class="hide-if-js" style="text-align: center;"><?php _e( 'Theme Info', 'boozurk' ); ?></h2>
			<?php locate_template( 'readme.html',true ); ?>
		</div>
	</div>
<?php
	}


	// add link to the options page in the admin bar
	function admin_bar_plus() {
		global $wp_admin_bar;

		if (!is_super_admin() || !is_admin_bar_showing() || !current_user_can( 'edit_theme_options' ) ) return;

		$add_menu_meta = array(
			'target'    => '_blank'
		);

		$wp_admin_bar->add_menu( array(
			'id'		=> $this->slug . '_theme_options',
			'parent'	=> 'appearance',
			'title'		=> __( 'Theme Options','boozurk' ),
			'href'		=> get_admin_url() . 'themes.php?page=theme_options',
			'meta'		=> $add_menu_meta,
		) );

	}


	// Add Thumbnail Column in Manage Posts/Pages List
	function add_thumb_column($cols) {

		$cols['thumbnail'] = ucwords( __('thumbnail', 'boozurk') );
		return $cols;

	}


	// Add Thumbnails in Manage Posts/Pages List
	function add_thumb_value($column_name, $post_id) {

			$width = (int) 60;
			$height = (int) 60;

			if ( 'thumbnail' == $column_name ) {
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
	function post_manage_style(){

	?>
		<style type="text/css">
			.fixed .column-thumbnail {
				width: 70px;
			}
		</style>
	<?php

	}

}

new Boozurk_Admin;

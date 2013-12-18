<?php
/**
 * widgets.php
 *
 * This file defines the Widget functionality and 
 * custom widgets.
 *
 * Custom widgets:
 * - Popular Posts
 * - Latest activity
 * - Latest comment authors
 * - Popular Categories
 * - Follow Me
 * - besides...
 * - Recent Posts in Category
 * - Navigation buttons
 * - Post details
 * - Post Formats
 * - Image EXIF details
 * - User quick links
 * - Share this
 * - Clean Archives
 * - Font Resize
 *
 * @package Boozurk
 * @since 1.00
 */


/* Custom actions - WP hooks */

add_action( 'widgets_init'						, 'boozurk_widget_area_init' );
add_action( 'widgets_init'						, 'boozurk_widgets_init' );
add_action( 'admin_print_styles-widgets.php'	, 'boozurk_widgets_style' );
add_action( 'admin_print_scripts-widgets.php'	, 'boozurk_widgets_scripts' );


/**
 * Define default Widget arguments
 */
function boozurk_get_default_widget_args( $args = '' ) {

	$defaults = array(
		'before'	=> '',
		'after'		=> '',
		'id'		=> '%1$s',
		'class'		=> '%2$s',
	);

	$args = wp_parse_args( $args, $defaults );

	$args['id'] = $args['id'] ? ' id="' . $args['id'] . '"' : '';

	$widget_args = array(
		// Widget container opening tag, with classes
		'before_widget' => $args['before']  . '<div' . $args['id'] . ' class="widget ' . $args['class'] . '">',
		// Widget container closing tag
		'after_widget' => '</div>' . $args['after'],
		// Widget Title container opening tag, with classes
		'before_title' => '<div class="w_title">',
		// Widget Title container closing tag
		'after_title' => '</div>'
	);

	return $widget_args;

}


/**
 * Register all widget areas (sidebars)
 */
function boozurk_widget_area_init() {

	if ( ! ( boozurk_get_opt( 'boozurk_sidebar_primary' ) == 'hidden' ) )
		// Area 0, in the left sidebar.
		register_sidebar( array_merge( 
			array(
				'name' => __( 'Primary Sidebar', 'boozurk' ),
				'id' => 'primary-widget-area',
				'description' => __( 'The primary sidebar widget area', 'boozurk' )
			),
			boozurk_get_default_widget_args()
		) );

	if ( ! ( boozurk_get_opt( 'boozurk_sidebar_secondary' ) == 'hidden' ) )
		// Area 1, in the right sidebar.
		register_sidebar( array_merge( 
			array(
				'name' => __( 'Secondary sidebar', 'boozurk' ),
				'id' => 'fixed-widget-area',
				'description' => __( 'The secondary sidebar widget area', 'boozurk' )
			),
			boozurk_get_default_widget_args()
		) );

	// Area 2, located under the main menu.
	register_sidebar( array_merge( 
		array(
			'name' => __( 'Menu Widget Area', 'boozurk' ),
			'id' => 'header-widget-area',
			'description' => __( 'The widget area under the main menu', 'boozurk' )
		),
		boozurk_get_default_widget_args( 'before=<div class="bz-widget">&after=</div>' )
	) );

	// Area 3, located in the footer. Empty by default.
	register_sidebar( array_merge( 
		array(
			'name' => __( 'First Footer Widget Area', 'boozurk' ),
			'id' => 'first-footer-widget-area',
			'description' => __( 'The first footer widget area', 'boozurk' )
		),
		boozurk_get_default_widget_args()
	) );

	// Area 4, located in the footer. Empty by default.
	register_sidebar( array_merge( 
		array(
			'name' => __( 'Second Footer Widget Area', 'boozurk' ),
			'id' => 'second-footer-widget-area',
			'description' => __( 'The second footer widget area', 'boozurk' )
		),
		boozurk_get_default_widget_args()
	) );

	// Area 5, located in the footer. Empty by default.
	register_sidebar( array_merge( 
		array(
			'name' => __( 'Third Footer Widget Area', 'boozurk' ),
			'id' => 'third-footer-widget-area',
			'description' => __( 'The third footer widget area', 'boozurk' )
		),
		boozurk_get_default_widget_args()
	) );

	// Area 6, located in page 404.
	register_sidebar( array_merge( 
		array(
			'name' => __( 'Page 404', 'boozurk' ),
			'id' => 'error404-widgets-area',
			'description' => __( 'Enrich the page 404 with some useful widgets', 'boozurk' )
		),
		boozurk_get_default_widget_args()
	) );

	// Area 7, located after the post body.
	register_sidebar( array_merge( 
		array(
			'name' => __( 'Post Widget Area', 'boozurk' ),
			'id' => 'single-widgets-area',
			'description' => __( 'a widget area located after the post body', 'boozurk' ),
		),
		boozurk_get_default_widget_args( 'before=<div class="bz-widget">&after=</div>' )
	) );

}


//add custom stylesheet
function boozurk_widgets_style() {

	wp_enqueue_style( 'boozurk-widgets-style', get_template_directory_uri() . '/css/widgets.css', false, '', 'screen' );

}


//add js script to the widgets page
function boozurk_widgets_scripts() {

	wp_enqueue_script( 'boozurk-widgets-scripts', get_template_directory_uri() . '/js/widgets.js', array('jquery'), boozurk_get_info( 'version' ), true );

}


/**
 * Popular_Posts widget class
 */
class Boozurk_Widget_Popular_Posts extends WP_Widget {

	function Boozurk_Widget_Popular_Posts() {

		$widget_ops = array( 'classname' => 'tb_popular_posts', 'description' => __( 'The most commented posts on your site', 'boozurk' ) );
		$this->WP_Widget( 'bz-popular-posts', __( 'Popular Posts', 'boozurk' ), $widget_ops );
		$this->alt_option_name = 'tb_popular_posts';

		add_action( 'save_post'		,array( &$this, 'flush_widget_cache' ) );
		add_action( 'deleted_post'	,array( &$this, 'flush_widget_cache' ) );
		add_action( 'switch_theme'	,array( &$this, 'flush_widget_cache' ) );

		$this->defaults = array(
			'title' => __( 'Popular Posts', 'boozurk' ),
			'number' => 5,
			'thumb' => 0
		);

		$this->alert = array();

	}


	function widget($args, $instance) {
		$cache = wp_cache_get( 'tb_popular_posts', 'widget' );

		if ( !is_array($cache) )
			$cache = array();

		if ( isset($cache[$args['widget_id']]) ) {
			echo $cache[$args['widget_id']];
			return;
		}

		extract($args);

		$instance = wp_parse_args( (array)$instance, $this->defaults );

		$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base);
		$title = $title ? $before_title . $title . $after_title : '';

		$number = (int) $instance['number'];

		$ul_class = $instance['thumb'] ? ' class="with-thumbs"' : '';

		$r = new WP_Query( array(
			'showposts' => $number,
			'nopaging' => 0,
			'post_status' => 'publish',
			'ignore_sticky_posts' => 1,
			'orderby' => 'comment_count'
		) );

		$output = '';

		if ( $r->have_posts() ) {

			while ( $r->have_posts() ) {
				$r->the_post();

				$thumb = $instance['thumb'] ? boozurk_get_the_thumb( array( 'id' => get_the_ID(), 'size_w' => 32, 'class' => 'tb-thumb-format' ) ) . ' ' : '';
				$post_title = get_the_title() ? get_the_title() : get_the_ID();

				$output .= '<li><a href="' . get_permalink() . '" title="' . esc_attr( $post_title ) . '">' . $thumb . $post_title . ' <span class="details">(' . get_comments_number() . ')</span></a></li>';

			}

			$output = $before_widget . $title . '<ul' . $ul_class . '>' . $output . '</ul>' . $after_widget;
		}

		wp_reset_postdata();

		echo $output;

		$cache[$args['widget_id']] = $output;
		wp_cache_set( 'tb_popular_posts', $cache, 'widget' );

	}


	function update( $new_instance, $old_instance ) {

		$instance = $old_instance;

		$instance['title'] = strip_tags($new_instance['title']);

		$instance['number'] = (int) $new_instance['number'];
		if ( ( $instance['number'] > 15 ) || ( $instance['number'] < 1 ) ) {
			$instance['number'] = $this->defaults['number'];
			$this->alert[] = 'number';
		}

		$instance['thumb'] = (int) $new_instance['thumb'] ? 1 : 0;

		$this->flush_widget_cache();

		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset($alloptions['tb_popular_posts']) )
			delete_option( 'tb_popular_posts' );

		return $instance;

	}


	function flush_widget_cache() {

		wp_cache_delete( 'tb_popular_posts', 'widget' );

	}


	function field_class( $field ) {

		if ( in_array( $field , $this->alert ) ) echo ' class="invalid"';

	}


	function form( $instance ) {

		$instance = wp_parse_args( (array) $instance, $this->defaults );

		$title = esc_attr( $instance['title'] );
		$number = (int) $instance['number'];
		$thumb = (int) $instance['thumb'];

?>
	<?php if ( ! empty( $this->alert ) ) echo '<div class="error">' . __( 'Invalid value', 'boozurk' ) . '</div>'?>

	<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', 'boozurk' ); ?>:</label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" />
	</p>

	<p<?php $this->field_class( 'number' ); ?>>
		<label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e( 'Number of posts to show', 'boozurk' ); ?> [1-15]:</label>
		<input id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="text" value="<?php echo $number; ?>" size="3" />
	</p>

	<p>
		<input id="<?php echo $this->get_field_id( 'thumb' ); ?>" name="<?php echo $this->get_field_name( 'thumb' ); ?>" value="1" type="checkbox" <?php checked( 1 , $thumb ); ?> />
		<label for="<?php echo $this->get_field_id( 'thumb' ); ?>"><?php _e( 'Show post thumbnails', 'boozurk' ); ?></label>
	</p>
<?php

	}

}


/**
 * latest_Commented_Posts widget class
 *
 */
class Boozurk_Widget_Latest_Commented_Posts extends WP_Widget {

	function Boozurk_Widget_Latest_Commented_Posts() {
		$widget_ops = array( 'classname' => 'tb_latest_commented_posts', 'description' => __( 'The latest commented posts/pages of your site', 'boozurk' ) );
		$this->WP_Widget( 'bz-recent-comments', __( 'Latest activity', 'boozurk' ), $widget_ops );
		$this->alt_option_name = 'tb_latest_commented_posts';

		add_action( 'comment_post'				,array( &$this, 'flush_widget_cache' ) );
		add_action( 'transition_comment_status'	,array( &$this, 'flush_widget_cache' ) );

		$this->defaults = array(
			'title' => __( 'Latest activity', 'boozurk' ),
			'number' => 5,
			'thumb' => 0
		);

		$this->alert = array();

	}


	function flush_widget_cache() {

		wp_cache_delete( 'tb_latest_commented_posts', 'widget' );

	}


	function widget( $args, $instance ) {

		$cache = wp_cache_get( 'tb_latest_commented_posts', 'widget' );

		if ( ! is_array( $cache ) )
			$cache = array();

		if ( isset( $cache[$args['widget_id']] ) ) {
			echo $cache[$args['widget_id']];
			return;
		}

 		extract($args, EXTR_SKIP);

		$instance = wp_parse_args( (array)$instance, $this->defaults );

		$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base);
		$title = $title ? $before_title . $title . $after_title : '';

		$number = (int) $instance['number'];

		$ul_class = $instance['thumb'] ? ' class="with-thumbs"' : '';

		$output = '';

		$comments = get_comments( array( 'status' => 'approve', 'type' => 'comment', 'number' => 200 ) );

		if ( $comments ) {

			$post_array = array();
			$counter = 0;
			foreach ( (array) $comments as $comment) {

				if ( ! in_array( $comment->comment_post_ID, $post_array ) ) {

					$post = get_post( $comment->comment_post_ID );
					setup_postdata( $post );

					$the_thumb = $instance['thumb'] ? boozurk_get_the_thumb( array( 'id' => $post->ID, 'size_w' => 32, 'class' => 'tb-thumb-format' ) ) . ' ' : '';

					$output .=  '<li>' . ' <a href="' . get_permalink( $post->ID ) . '" title="' .  esc_attr( get_the_title( $post->ID ) ) . '">' . $the_thumb . get_the_title( $post->ID ) . '</a></li>';

					$post_array[] = $comment->comment_post_ID;

					if ( ++$counter >= $number ) break;

				}

			}

		} else {

			$output .= '<li>' . __( 'no comments yet', 'boozurk' ) . '</li>';

		}

		$output = $before_widget . $title . '<ul' . $ul_class . '>' . $output . '</ul>' . $after_widget;

		echo $output;

		$cache[$args['widget_id']] = $output;
		wp_cache_set( 'tb_latest_commented_posts', $cache, 'widget' );
	}


	function update( $new_instance, $old_instance ) {

		$instance = $old_instance;

		$instance['title'] = strip_tags($new_instance['title']);

		$instance['number'] = (int) $new_instance['number'];
		if ( ( $instance['number'] > 15 ) || ( $instance['number'] < 1 ) ) {
			$instance['number'] = $this->defaults['number'];
			$this->alert[] = 'number';
		}

		$instance['thumb'] = (int) $new_instance['thumb'] ? 1 : 0;

		$this->flush_widget_cache();

		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset($alloptions['tb_latest_commented_posts']) )
			delete_option( 'tb_latest_commented_posts' );

		return $instance;

	}


	function field_class( $field ) {

		if ( in_array( $field , $this->alert ) ) echo ' class="invalid"';

	}


	function form( $instance ) {

		$instance = wp_parse_args( (array) $instance, $this->defaults );

?>
	<?php if ( ! empty( $this->alert ) ) echo '<div class="error">' . __( 'Invalid value', 'boozurk' ) . '</div>'?>

	<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', 'boozurk' ); ?>:</label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
	</p>

	<p<?php $this->field_class( 'number' ); ?>>
		<label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e( 'Number of posts to show', 'boozurk' ); ?> [1-15]:</label>
		<input id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="text" value="<?php echo $instance['number']; ?>" size="3" />
	</p>

	<p>
		<input id="<?php echo $this->get_field_id( 'thumb' ); ?>" name="<?php echo $this->get_field_name( 'thumb' ); ?>" value="1" type="checkbox" <?php checked( 1 , $instance['thumb'] ); ?> />
		<label for="<?php echo $this->get_field_id( 'thumb' ); ?>"><?php _e( 'Show post thumbnails', 'boozurk' ); ?></label>
	</p>
<?php

	}
}


/**
 * latest_Comment_Authors widget class
 *
 */
class Boozurk_Widget_Latest_Commentators extends WP_Widget {

	function Boozurk_Widget_Latest_Commentators() {

		$widget_ops = array( 'classname' => 'tb_latest_commentators', 'description' => __( 'The latest comment authors', 'boozurk' ) );
		$this->WP_Widget( 'bz-recent-commentators', __( 'Latest comment authors', 'boozurk' ), $widget_ops);
		$this->alt_option_name = 'tb_latest_commentators';

		add_action( 'comment_post', array(&$this, 'flush_widget_cache' ) );
		add_action( 'transition_comment_status', array(&$this, 'flush_widget_cache' ) );

		$this->defaults = array(
			'title' => __( 'Latest comment authors', 'boozurk' ),
			'number' => 5,
			'icon_size' => 32
		);

		$this->alert = array();

	}


	function flush_widget_cache() {

		wp_cache_delete( 'tb_latest_commentators', 'widget' );

	}


	function widget( $args, $instance ) {

		if ( get_option( 'require_name_email' ) != '1' ) return; //commentors must be identifiable

		$cache = wp_cache_get( 'tb_latest_commentators', 'widget' );

		if ( ! is_array( $cache ) )
			$cache = array();

		if ( isset( $cache[$args['widget_id']] ) ) {
			echo $cache[$args['widget_id']];
			return;
		}

 		extract($args, EXTR_SKIP);

		$instance = wp_parse_args( (array)$instance, $this->defaults );

		$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base);
		$title = $title ? $before_title . $title . $after_title : '';

		$icon_size = (int) $instance['icon_size'];

		$number = (int) $instance['number'];

		$output = '';

		$comments = get_comments( array( 'status' => 'approve', 'type' => 'comment', 'number' => 200 ) );

		if ( $comments ) {

			$post_array = array();
			$counter = 0;
			foreach ( (array) $comments as $comment) {

				if ( !in_array( $comment->comment_author_email, $post_array ) ) {

					if ( $comment->comment_author_url == '' )
						$avatar =  get_avatar( $comment, $icon_size, $default = get_option( 'avatar_default' ) );
					else
						$avatar =  '<a target="_blank" href="' . $comment->comment_author_url . '">' . get_avatar( $comment, $icon_size, $default = get_option( 'avatar_default' ) ) . '</a>';

					$output .=  '<li title="' .  esc_attr( $comment->comment_author ) . '">' . $avatar . '</li>';

					$post_array[] = $comment->comment_author_email;

					if ( ++$counter >= $number ) break;

				}

			}

 		} else {

			$output .= '<li>' . __( 'no comments yet', 'boozurk' ) . '</li>';

		}

		$output = $before_widget . $title . '<ul>' . $output . '</ul><br class="fixfloat" />' . $after_widget;

		echo $output;

		$cache[$args['widget_id']] = $output;
		wp_cache_set( 'tb_latest_commentators', $cache, 'widget' );

	}


	function update( $new_instance, $old_instance ) {

		$instance = $old_instance;

		$instance['title'] = strip_tags($new_instance['title']);

		$instance['icon_size'] = $new_instance['icon_size'];
		if ( ! in_array( $instance['icon_size'], array ( '16', '24', '32', '48', '64' ) ) ) {
			$instance['icon_size'] = $this->defaults['icon_size'];
			$this->alert[] = 'icon_size';
		}

		$instance['number'] = (int) $new_instance['number'];
		if ( ( $instance['number'] > 10 ) || ( $instance['number'] < 1 ) ) {
			$instance['number'] = $this->defaults['number'];
			$this->alert[] = 'number';
		}

		$this->flush_widget_cache();

		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset($alloptions['tb_latest_commentators']) )
			delete_option( 'tb_latest_commentators' );

		return $instance;

	}


	function field_class( $field ) {

		if ( in_array( $field , $this->alert ) ) echo ' class="invalid"';

	}


	function form( $instance ) {

		$instance = wp_parse_args( (array) $instance, $this->defaults );

		$title = $instance['title'];
		$number = $instance['number'];
		$icon_size = $instance['icon_size'];

		if ( get_option( 'require_name_email' ) != '1' ) {
			printf ( __( 'Comment authors <strong>must</strong> use a name and a valid e-mail in order to use this widget. Check the <a href="%1$s">Discussion settings</a>', 'boozurk' ), esc_url( admin_url( 'options-discussion.php' ) ) );
			return;
		}

?>
	<?php if ( $this->alert ) echo '<div class="error">' . __( 'Invalid value', 'boozurk' ) . '</div>'?>

	<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', 'boozurk' ); ?>:</label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" />
	</p>

	<p<?php $this->field_class( 'number' ); ?>>
		<label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e( 'Number of users to show', 'boozurk' ); ?> [1-10]:</label>
		<input id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="text" value="<?php echo $number; ?>" size="3" />
	</p>

	<p<?php $this->field_class( 'icon_size' ); ?>>
		<label for="<?php echo $this->get_field_id( 'icon_size' ); ?>"><?php _e( 'Select your icon size', 'boozurk' ); ?>:</label><br />
		<select name="<?php echo $this->get_field_name( 'icon_size' ); ?>" id="<?php echo $this->get_field_id( 'icon_size' ); ?>" >
			<?php
				$size_array = array ( '16', '24', '32', '48', '64' );
				foreach($size_array as $size) {

					?><option value="<?php echo $size; ?>" <?php selected( $icon_size, $size ); ?>><?php echo $size; ?>px</option><?php
				}
			?>
		</select>
	</p>
<?php

	}

}


/**
 * Popular Categories widget class
 *
 */
class Boozurk_Widget_Pop_Categories extends WP_Widget {

	function Boozurk_Widget_Pop_Categories() {

		$widget_ops = array( 'classname' => 'tb_categories', 'description' => __( 'A list of popular categories', 'boozurk' ) );

		$this->WP_Widget( 'bz-categories', __( 'Popular Categories', 'boozurk' ), $widget_ops);

		$this->defaults = array(
			'title' => __( 'Popular Categories', 'boozurk' ),
			'number' => 5,
			'id' => ''
		);

		$this->alert = array();

	}


	function widget( $args, $instance ) {

		extract( $args );
		$instance = wp_parse_args( (array)$instance, $this->defaults );

		$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base);
		$title = $title ? $before_title . $title . $after_title : '';

		$number = (int) $instance['number'];

		$cat_args = array(
			'orderby' => 'count',
			'show_count' => 1,
			'hierarchical' => 0,
			'order' => 'DESC',
			'title_li' => '',
			'number' => $number
		);
		$cat_args = apply_filters( 'boozurk_widget_pop_categories_args', $cat_args);

		$view_all_url = ( $instance['id'] && get_permalink( $instance['id'] ) ) ? get_permalink( $instance['id'] ) : add_query_arg( 'allcat', 'y', home_url() );

?>
	<?php echo $before_widget; ?>

		<?php echo $title; ?>

		<ul>
			<?php wp_list_categories( $cat_args ); ?>
			<li class="allcat"><a rel="nofollow" title="<?php esc_attr_e( 'View all categories', 'boozurk' ); ?>" href="<?php echo $view_all_url; ?>"><?php _e( 'View all', 'boozurk' ); ?></a></li>
		</ul>

	<?php echo $after_widget; ?>

<?php

	}


	function update( $new_instance, $old_instance ) {

		$instance = $old_instance;

		$instance['title'] = strip_tags( $new_instance['title'] );

		$instance['number'] = (int) $new_instance['number'];
		if ( ( $instance['number'] > 15 ) || ( $instance['number'] < 1 ) ) {
			$instance['number'] = $this->defaults['number'];
			$this->alert[] = 'number';
		}

		$instance['id'] = $new_instance['id'] ? (int) $new_instance['id'] : '';
		if ( $instance['id'] && ! get_post( $instance['id'] ) ) {
			$instance['id'] = $this->defaults['id'];
			$this->alert[] = 'id';
		}

		return $instance;

	}


	function field_class( $field ) {

		if ( in_array( $field , $this->alert ) ) echo ' class="invalid"';

	}


	function form( $instance ) {

		$instance = wp_parse_args( (array) $instance, $this->defaults );

		$title = esc_attr( $instance['title'] );
		$id = $instance['id'] ? (int) $instance['id'] : '';
		$number = (int) $instance['number'];

?>
	<?php if ( $this->alert ) echo '<div class="error">' . __( 'Invalid value', 'boozurk' ) . '</div>'?>

	<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', 'boozurk' ); ?>:</label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" />
	</p>

	<p<?php $this->field_class( 'number' ); ?>>
		<label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e( 'Number of categories to show', 'boozurk' ); ?> [1-15]:</label>
		<input id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="text" value="<?php echo $number; ?>" size="3" />
	</p>

	<p<?php $this->field_class( 'id' ); ?>>
		<label for="<?php echo $this->get_field_id( 'id' ); ?>"><?php _e( 'ID of the page created using the "List of Categories" template (if any)', 'boozurk' ); ?>:</label>
		<input id="<?php echo $this->get_field_id( 'id' ); ?>" name="<?php echo $this->get_field_name( 'id' ); ?>" type="text" value="<?php echo $id; ?>" size="3" />
	</p>
<?php

	}

}


/**
 * Social network widget class.
 * Social media services supported: Facebook, Twitter, Myspace, Youtube, LinkedIn, Del.icio.us, Digg, Flickr, Reddit, StumbleUpon, Technorati and Github.
 * Optional: RSS icon. 
 *
 */
class Boozurk_Widget_Social extends WP_Widget {

	function Boozurk_Widget_Social() {

		$widget_ops = array(
			'classname'		=> 'tb_social',
			'description'	=> __( 'This widget lets visitors of your blog to subscribe to it and follow you on popular social networks like Twitter, FaceBook etc.' , 'boozurk' )
		);
		$control_ops = array( 'width' => 650 );

		$this->WP_Widget( 'bz-social', __( 'Follow Me', 'boozurk' ), $widget_ops, $control_ops );

		$this->follow_urls = array(
			// SLUG => NAME
			'Blogger'		=> 'Blogger',
			'blurb'			=> 'Blurb',
			'cloudup'		=> 'Cloudup',
			'Delicious'		=> 'Delicious',
			'Deviantart'	=> 'deviantART',
			'Digg'			=> 'Digg',
			'Dropbox'		=> 'Dropbox',
			'Facebook'		=> 'Facebook',
			'Flickr'		=> 'Flickr',
			'Github'		=> 'GitHub',
			'GooglePlus'	=> 'Google+',
			'Hi5'			=> 'Hi5',
			'instagram'		=> 'Instagram',
			'LinkedIn'		=> 'LinkedIn',
			'livejournal'	=> 'LiveJournal',
			'Myspace'		=> 'Myspace',
			'Odnoklassniki'	=> 'Odnoklassniki',
			'Orkut'			=> 'Orkut',
			'pengyou'		=> 'Pengyou',
			'Picasa'		=> 'Picasa',
			'pinterest'		=> 'Pinterest',
			'Qzone'			=> 'Qzone',
			'Reddit'		=> 'Reddit',
			'renren'		=> 'Renren',
			'scribd'		=> 'Scribd',
			'slideshare'	=> 'SlideShare',
			'StumbleUpon'	=> 'StumbleUpon',
			'soundcloud'	=> 'SoundCloud',
			'Technorati'	=> 'Technorati',
			'Tencent'		=> 'Tencent',
			'Twitter'		=> 'Twitter',
			'tumblr'		=> 'Tumblr',
			'ubuntuone'		=> 'Ubuntu One',
			'Vimeo'			=> 'Vimeo',
			'VKontakte'		=> 'VKontakte',
			'Sina'			=> 'Weibo',
			'WindowsLive'	=> 'Windows Live',
			'xing'			=> 'Xing',
			'yfrog'			=> 'YFrog',
			'Youtube'		=> 'Youtube',
			'Mail'			=> 'mail',
			'RSS'			=> 'RSS'
		);

		$this->defaults = array(
			'title'		=> __( 'Follow Me', 'boozurk' ),
			'icon_size'	=> 48,
		);
		foreach ( $this->follow_urls as $follow_service => $service_name ) {
			$this->defaults[$follow_service.'_account'] = '';
			$this->defaults['show_'.$follow_service] = false;
		}

		$this->alert = array();

	}


	function flush_widget_cache() {

		wp_cache_delete( 'tb_social', 'widget' );

	}


	function widget( $args, $instance ) {

		$cache = wp_cache_get( 'tb_social', 'widget' );

		if ( ! is_array( $cache ) )
			$cache = array();

		if ( isset( $cache[$args['widget_id']] ) ) {
			echo $cache[$args['widget_id']];
			return;
		}

		extract($args);

		$instance = wp_parse_args( (array)$instance, $this->defaults );

		$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base);
		$title = $title ? $before_title . $title . $after_title : '';

		$icon_size = $instance['icon_size'];

		$output = '';

		foreach ($this->follow_urls as $follow_service => $service_name ) {

			$show = $instance['show_'.$follow_service];
			$account = $instance[$follow_service.'_account'];
			$prefix = __( 'Follow us on %s', 'boozurk' );
			$onclick = '';
			$class = '';
			$target = '_blank';
			if ( $follow_service == 'RSS' ) {
				$account = $account? $account : get_bloginfo( 'rss2_url' );
				$prefix = __( 'Keep updated with our RSS feed', 'boozurk' );
			}
			if ( $follow_service == 'Mail' ) {
				$account = preg_replace( '/(.)(.)/', '$2$1', 'mailto:'.$account );
				$prefix = __( 'Contact us', 'boozurk' );
				$class= ' hide-if-no-js';
				$onclick = ' onclick="this.href=\'' . $account . '\'.replace(/(.)(.)/g, \'$2$1\');"';
				$account = '#';
				$target = '_self';
			}

			if ( $show && ! empty( $account ) ) {
				$icon = '<img src="' . esc_url( get_template_directory_uri() . '/images/follow/' . strtolower( $follow_service ) . '.png' ) . '" alt="' . $follow_service . '" style="width: ' . $icon_size . 'px; height: ' . $icon_size . 'px;" />';
				$output .= '<a target="' . $target . '" href="' . $account . '"' . $onclick . ' class="tb-social-icon' . $class . '" title="' . esc_attr( sprintf( $prefix, $service_name ) ) . '">' . $icon . '</a>';
			}

		}

		$output = $before_widget . $title . $output . $after_widget;

		echo $output;

		$cache[$args['widget_id']] = $output;
		wp_cache_set( 'tb_social', $cache, 'widget' );

	}


	function update($new_instance, $old_instance) {

		$instance = $old_instance;

		$instance["title"] = strip_tags($new_instance["title"]);

		$instance['icon_size'] = $new_instance['icon_size'];
		if ( ! in_array( $instance['icon_size'], array ( '16', '24', '32', '48', '64' ) ) ) {
			$instance['icon_size'] = $this->defaults['icon_size'];
			$this->alert[] = 'icon_size';
		}

		$url_pattern = "/^(http|https):\/\//";
		$email_pattern = "/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/";
		foreach ($this->follow_urls as $follow_service => $service_name ) {

			$instance['show_'.$follow_service] = $new_instance['show_'.$follow_service];
			$instance[$follow_service.'_account'] = $new_instance[$follow_service.'_account'];

			if ( $instance[$follow_service.'_account'] ) {

				if( $follow_service == 'Mail' )
					preg_match($email_pattern, strtoupper( $instance[$follow_service.'_account'] ), $is_valid_url);
				else
					preg_match($url_pattern, $instance[$follow_service.'_account'], $is_valid_url);

				if ( ! $is_valid_url ) {
					$instance['show_'.$follow_service] = false;
					$instance[$follow_service.'_account'] = '';
					$this->alert[] = $follow_service;
				}

			}

		}

		$this->flush_widget_cache();

		return $instance;

	}


	function field_class( $field ) {

		if ( in_array( $field , $this->alert ) ) echo ' class="invalid"';

	}


	function form( $instance ) {

		$instance = wp_parse_args( (array)$instance, $this->defaults );

?>
	<?php if ( $this->alert ) echo '<div class="error">' . __( 'Invalid value', 'boozurk' ) . '</div>'?>

	<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Title', 'boozurk' ); ?>:</label>
		<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['title']); ?>" />
	</p>

	<div class="services-wrap" style="padding: 10px 0; border-top: 1px solid #DFDFDF;">

		<p><?php echo __( 'NOTE: Enter the <strong>full</strong> addresses ( with <em>http://</em> )', 'boozurk' ); ?></p>

		<?php foreach( $this->follow_urls as $follow_service => $service_name ) { ?>

		<div class="service" style="float: left; width: 40%; margin: 0pt 5%;">

			<h2>
				<input id="<?php echo $this->get_field_id( 'show_'.$follow_service ); ?>" name="<?php echo $this->get_field_name( 'show_'.$follow_service ); ?>" type="checkbox" <?php checked( $instance['show_'.$follow_service], 'on' ); ?>  class="checkbox" />
				<img style="vertical-align:middle; width:32px; height:32px;" src="<?php echo esc_url( get_template_directory_uri() . '/images/follow/' . strtolower( $follow_service ) . '.png' ); ?>" alt="<?php echo esc_attr( $follow_service ); ?>" />
				<?php echo $service_name; ?>
			</h2>

			<?php
				if ( ( $follow_service != 'RSS' ) && ( $follow_service != 'Mail' ) )
					$text = __( 'Enter your %1$s account link', 'boozurk' );
				elseif ( $follow_service == 'Mail' )
					$text = __( 'Enter email address', 'boozurk' );
				elseif ( $follow_service == 'RSS' )
					$text = __( 'Enter your feed service address. Leave it blank for using the default WordPress feed', 'boozurk' );
			?>
			<p<?php $this->field_class( $follow_service ); ?>>
				<label for="<?php echo $this->get_field_id( $follow_service.'_account' ); ?>"><?php printf( $text, $service_name ) ?>:</label>
				<input type="text" id="<?php echo $this->get_field_id( $follow_service.'_account' ); ?>" name="<?php echo $this->get_field_name( $follow_service.'_account' ); ?>" value="<?php if ( isset( $instance[$follow_service.'_account'] ) ) echo $instance[$follow_service.'_account']; ?>" class="widefat" />
			</p>

		</div>

		<?php } ?>

		<div class="clear" style="padding: 10px 0; border-top: 1px solid #DFDFDF; text-align: right;">

			<label for="<?php echo $this->get_field_id( 'icon_size' ); ?>"><?php _e( 'Select your icon size', 'boozurk' ); ?>:</label><br />
			<select name="<?php echo $this->get_field_name( 'icon_size' ); ?>" id="<?php echo $this->get_field_id( 'icon_size' ); ?>" >
				<?php
					$size_array = array ( '16', '24', '32', '48', '64' );
					foreach($size_array as $size) {
				?>
					<option value="<?php echo $size; ?>" <?php selected( $instance['icon_size'], $size ); ?>><?php echo $size; ?>px</option>
				<?php
					}
				?>
			</select>

		</div>

	</div>

	<?php if ( $this->alert ) echo '<div class="error">' . __( 'Invalid value', 'boozurk' ) . '</div>'?>
<?php

	}

}


/**
 * Makes a custom Widget for displaying Aside and Status Posts
 *
 * Based on Twenty_Eleven_Ephemera_Widget
 *
 */

class Boozurk_Widget_Besides extends WP_Widget {

	function Boozurk_Widget_Besides() {

		$widget_ops = array( 'classname' => 'tb_besides', 'description' => __( 'Use this widget to list your recent Aside and Status posts', 'boozurk' ) );
		$this->WP_Widget( 'bz-widget-besides', __( 'besides...', 'boozurk' ), $widget_ops );
		$this->alt_option_name = 'tb_besides';

		add_action( 'save_post'		,array(&$this, 'flush_widget_cache' ) );
		add_action( 'deleted_post'	,array(&$this, 'flush_widget_cache' ) );
		add_action( 'switch_theme'	,array(&$this, 'flush_widget_cache' ) );

		$this->defaults = array(
			'title' => __( 'besides...', 'boozurk' ),
			'number' => 5,
			'type' => 'aside'
		);

		$this->alert = array();

	}


	function flush_widget_cache() {

		wp_cache_delete( 'tb_besides', 'widget' );

	}


	function widget( $args, $instance ) {

		$cache = wp_cache_get( 'tb_besides', 'widget' );

		if ( !is_array( $cache ) )
			$cache = array();

		if ( ! isset( $args['widget_id'] ) )
			$args['widget_id'] = null;

		if ( isset( $cache[$args['widget_id']] ) ) {
			echo $cache[$args['widget_id']];
			return;
		}

		ob_start();
		extract( $args, EXTR_SKIP );

		$instance = wp_parse_args( (array)$instance, $this->defaults );

		$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base);
		$title = $title ? $before_title . $title . $after_title : '';

		$type = $instance['type'];

		$number = (int) $instance['number'];

		$query_args = array(
			'order' => 'DESC',
			'posts_per_page' => $number,
			'nopaging' => 0,
			'post_status' => 'publish',
			'post__not_in' => get_option( 'sticky_posts' ),
			'tax_query' => array(
				array(
					'taxonomy' => 'post_format',
					'terms' => array( 'post-format-' . $type ),
					'field' => 'slug',
					'operator' => 'IN',
				),
			),
		);
		$besides = new WP_Query( $query_args );

		if ( $besides->have_posts() ) :

			echo $before_widget;

			echo $title;

?>
	<?php while ( $besides->have_posts() ) : $besides->the_post(); ?>

		<?php if ( $type == 'aside' ) { ?>
		<div class="wentry-aside">
			<?php the_content(); ?>
			<div class="aside-meta fixfloat" style="font-style: italic; color: #999;"><?php the_author(); ?> - <a href="<?php the_permalink() ?>" rel="bookmark"><?php the_time( get_option( 'date_format' ) ); ?></a> - <?php comments_popup_link( '(0)', '(1)', '(%)' ); ?></div>
		</div>
		<?php } elseif ( $type == 'status' ) { ?>
		<div class="wentry-status">
			<?php echo get_avatar( get_the_author_meta( 'user_email' ), 24, $default=get_option( 'avatar_default' ), get_the_author() ); ?>
			<a style="font-weight: bold;" href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>" title="<?php printf( 'View all posts by %s', esc_attr( get_the_author() ) ); ?>"><?php echo get_the_author(); ?></a>
			<?php the_content(); ?>
			<span style="color: #999;"><?php echo boozurk_friendly_date(); ?></span>
		</div>
		<?php } ?>

	<?php endwhile; ?>
<?php

			echo $after_widget;

		endif;

		wp_reset_postdata();

		$cache[$args['widget_id']] = ob_get_flush();
		wp_cache_set( 'tb_besides', $cache, 'widget' );

	}


	function update( $new_instance, $old_instance ) {

		$instance = $old_instance;

		$instance['title'] = strip_tags( $new_instance['title'] );

		$instance['number'] = (int) $new_instance['number'];
		if ( ( $instance['number'] > 5 ) || ( $instance['number'] < 1 ) ) {
			$instance['number'] = $this->defaults['number'];
			$this->alert[] = 'number';
		}

		$instance['type'] = $new_instance['type'];
		if ( ! in_array( $instance['type'], array( 'aside', 'status' ) ) ) {
			$instance['type'] = $this->defaults['type'];
			$this->alert[] = 'type';
		}

		$this->flush_widget_cache();

		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset( $alloptions['tb_besides'] ) )
			delete_option( 'tb_besides' );

		return $instance;

	}


	function field_class( $field ) {

		if ( in_array( $field , $this->alert ) ) echo ' class="invalid"';

	}


	function form( $instance ) {

		$instance = wp_parse_args( (array) $instance, $this->defaults );

		$title = esc_attr( $instance['title'] );
		$number = (int) $instance['number'];
		$type = $instance['type'];

?>
	<?php if ( $this->alert ) echo '<div class="error">' . __( 'Invalid value', 'boozurk' ) . '</div>'?>

	<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Title', 'boozurk' ); ?>:</label>
		<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
	</p>

	<p<?php $this->field_class( 'type' ); ?>>
		<label for="<?php echo esc_attr( $this->get_field_id( 'type' ) ); ?>"><?php _e( 'Type of posts to show', 'boozurk' ); ?>:</label>
		<select name="<?php echo esc_attr( $this->get_field_name( 'type' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'type' ) ); ?>" >
			<?php
				$type_array = array( 'aside', 'status' );
				foreach($type_array as $avaible_type) {
			?>
				<option value="<?php echo $avaible_type; ?>" <?php selected( $type, $avaible_type ); ?>><?php echo $avaible_type; ?></option>
			<?php
				}
			?>
		</select>
	</p>

	<p<?php $this->field_class( 'number' ); ?>>
		<label for="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>"><?php _e( 'Number of posts to show', 'boozurk' ); ?> [1-5]:</label>
		<input id="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'number' ) ); ?>" type="text" value="<?php echo esc_attr( $number ); ?>" size="3" />
	</p>
<?php

	}

}


/**
 * Recent Posts in Category widget class
 *
 */
class Boozurk_Widget_Recent_Posts extends WP_Widget {

	function Boozurk_Widget_Recent_Posts() {

		$widget_ops = array( 'classname' => 'tb_recent_entries', 'description' => __( 'The most recent posts in a single category', 'boozurk' ) );
		$this->WP_Widget( 'bz-recent-posts', __( 'Recent Posts in Category', 'boozurk' ), $widget_ops );
		$this->alt_option_name = 'tb_recent_entries';

		add_action( 'save_post'		,array( &$this, 'flush_widget_cache' ) );
		add_action( 'deleted_post'	,array( &$this, 'flush_widget_cache' ) );
		add_action( 'switch_theme'	,array( &$this, 'flush_widget_cache' ) );

		$this->defaults = array(
			'title' => __( 'Recent Posts in %s', 'boozurk' ),
			'number' => 5,
			'category' => '',
			'thumb' => 1,
			'description' => 1,
		);

		$this->alert = array();

	}


	function flush_widget_cache() {

		wp_cache_delete( 'tb_recent_posts', 'widget' );

	}


	function widget( $args, $instance ) {

		$cache = wp_cache_get( 'tb_recent_posts', 'widget' );

		if ( !is_array( $cache ) )
			$cache = array();

		if ( isset( $cache[$args['widget_id']] ) ) {
			echo $cache[$args['widget_id']];
			return;
		}

		extract( $args );
		$instance = wp_parse_args( (array)$instance, $this->defaults );

		$category = $instance['category'];
		if ( $category === -1 ) {
			if ( !is_single() || is_attachment() ) return;
			global $post;
			$category = get_the_category( $post->ID );
			$category = ( $category ) ? $category[0]->cat_ID : '';
		}

		$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
		$title = sprintf( $title, '<a href="' . esc_url( get_category_link( $category ) ) . '">' . get_cat_name( $category ) . '</a>' );
		$title = $title ? $before_title . $title . $after_title : '';

		$number = (int) $instance['number'];

		$ul_class = $instance['thumb'] ? ' class="with-thumbs"' : '';

		$description = ( $instance['description'] && category_description( $category ) ) ? '<div class="cat-descr">' . category_description( $category ) . '</div>' : '';

		$r = new WP_Query( array(
			'cat' => $category,
			'posts_per_page' => $number,
			'nopaging' => 0,
			'post_status' => 'publish',
			'ignore_sticky_posts' => true
		) );

		$output = '';

		if ($r->have_posts()) {

			while ( $r->have_posts() ) {
				$r->the_post();

				$thumb = $instance['thumb'] ? boozurk_get_the_thumb( array( 'id' => get_the_ID(), 'size_w' => 32, 'class' => 'tb-thumb-format' ) ) . ' ' : '';
				$post_title = get_the_title() ? get_the_title() : get_the_ID();

				$output .= '<li><a href="' . esc_url( get_permalink() ) . '" title="' . esc_attr( $post_title ) . '">' . $thumb . $post_title . '</a></li>';

			}

		$output = $before_widget . $title . $description . '<ul' . $ul_class . '>' . $output . '</ul>' . $after_widget;

		}

		wp_reset_postdata();

		$cache[$args['widget_id']] = $output;
		wp_cache_set( 'tb_recent_posts', $cache, 'widget' );

	}


	function update( $new_instance, $old_instance ) {

		$instance = $old_instance;

		$instance['title']			= strip_tags( $new_instance['title'] );

		$instance['number']			= (int) $new_instance['number'];
		if ( ( $instance['number'] > 15 ) || ( $instance['number'] < 1 ) ) {
			$instance['number'] = $this->defaults['number'];
			$this->alert[] = 'number';
		}

		$instance['category']		= (int) $new_instance['category'];

		$instance['thumb']			= (int) $new_instance['thumb'] ? 1 : 0;

		$instance['description']	= (int) $new_instance['description'] ? 1 : 0;

		$this->flush_widget_cache();

		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset($alloptions['tb_recent_entries']) )
			delete_option( 'tb_recent_entries' );

		return $instance;

	}


	function field_class( $field ) {

		if ( in_array( $field , $this->alert ) ) echo ' class="invalid"';

	}


	function form( $instance ) {

		$instance = wp_parse_args( (array) $instance, $this->defaults );

		$title = esc_attr( $instance['title'] );
		$number = $instance['number'];
		$category = $instance['category'];
		$thumb = $instance['thumb'];
		$description = $instance['description'];

?>
	<?php if ( $this->alert ) echo '<div class="error">' . __( 'Invalid value', 'boozurk' ) . '</div>'?>

	<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', 'boozurk' ); ?>:</label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" />
	</p>

	<p>
		<label for="<?php echo $this->get_field_id( 'category' ); ?>"><?php _e( 'Category', 'boozurk' ); ?>:</label>
		<?php 
			$dropdown_categories = wp_dropdown_categories( Array(
				'orderby'		=> 'ID', 
				'order'			=> 'ASC',
				'show_count'	=> 1,
				'hide_empty'	=> 0,
				'hide_if_empty'	=> true,
				'echo'			=> 0,
				'selected'		=> $category,
				'hierarchical'	=> 1, 
				'name'			=> $this->get_field_name( 'category' ),
				'id'			=> $this->get_field_id( 'category' ),
				'class'			=> 'widefat',
				'taxonomy'		=> 'category',
			) );
		?>

		<?php echo str_replace( '</select>', '<option ' . selected( $category , -1 , 0 ) . 'value="-1" class="level-0">' . __( '(current post category)', 'boozurk' ) . '</option></select>', $dropdown_categories ); ?>
		<small><?php echo __( 'by selecting "(current post category)", the widget will be visible ONLY in single posts', 'boozurk' ); ?></small>
	</p>

	<p>
		<input id="<?php echo $this->get_field_id( 'description' ); ?>" name="<?php echo $this->get_field_name( 'description' ); ?>" value="1" type="checkbox" <?php checked( 1 , $description ); ?> />
		<label for="<?php echo $this->get_field_id( 'description' ); ?>"><?php _e( 'Show category description', 'boozurk' ); ?></label>
	</p>

	<p<?php $this->field_class( 'number' ); ?>>
		<label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e( 'Number of posts to show', 'boozurk' ); ?> [1-15]:</label>
		<input id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="text" value="<?php echo $number; ?>" size="3" />
	</p>

	<p>
		<input id="<?php echo $this->get_field_id( 'thumb' ); ?>" name="<?php echo $this->get_field_name( 'thumb' ); ?>" value="1" type="checkbox" <?php checked( 1 , $thumb ); ?> />
		<label for="<?php echo $this->get_field_id( 'thumb' ); ?>"><?php _e( 'Show post thumbnails', 'boozurk' ); ?></label>
	</p>
<?php

	}

}


/**
 * Navigation buttons widget class
 */
class Boozurk_Widget_Navbuttons extends WP_Widget {

	function Boozurk_Widget_Navbuttons() {

		$widget_ops = array( 'classname' => 'tb_navbuttons', 'description' => __( 'Some usefull buttons for an easier navigation experience', 'boozurk' ) );
		$this->WP_Widget( 'bz-navbuttons', __( 'Navigation buttons', 'boozurk' ), $widget_ops );
		$this->alt_option_name = 'tb_navbuttons';

		$this->defaults = array(
			'print'		=> 1,
			'comment'	=> 1,
			'feed'		=> 1,
			'trackback'	=> 1,
			'home'		=> 1,
			'next_prev'	=> 1,
			'up_down'	=> 1,
			'fixed'		=> 0,
		);

	}


	function widget($args, $instance) {

		extract($args);
		$instance = wp_parse_args( (array)$instance, $this->defaults );

?>
	<?php echo $before_widget; ?>
	<?php boozurk_navbuttons( $instance ); ?>
	<?php echo $after_widget; ?>
<?php

	}


	function update( $new_instance, $old_instance ) {

		$instance = $old_instance;

		$instance['print']		= (int) $new_instance['print'] ? 1 : 0;
		$instance['comment']	= (int) $new_instance['comment'] ? 1 : 0;
		$instance['feed']		= (int) $new_instance['feed'] ? 1 : 0;
		$instance['trackback']	= (int) $new_instance['trackback'] ? 1 : 0;
		$instance['home']		= (int) $new_instance['home'] ? 1 : 0;
		$instance['next_prev']	= (int) $new_instance['next_prev'] ? 1 : 0;
		$instance['up_down']	= (int) $new_instance['up_down'] ? 1 : 0;

		return $instance;

	}


	function form( $instance ) {

		$instance = wp_parse_args( (array) $instance, $this->defaults );

		extract($instance);

?>
	<p>
		<input id="<?php echo $this->get_field_id( 'print' ); ?>" name="<?php echo $this->get_field_name( 'print' ); ?>" value="1" type="checkbox" <?php checked( 1 , $print ); ?> />
		<label for="<?php echo $this->get_field_id( 'print' ); ?>"><?php _e( 'Print preview', 'boozurk' ); ?></label>
		<br />
		<input id="<?php echo $this->get_field_id( 'comment' ); ?>" name="<?php echo $this->get_field_name( 'comment' ); ?>" value="1" type="checkbox" <?php checked( 1 , $comment ); ?> />
		<label for="<?php echo $this->get_field_id( 'comment' ); ?>"><?php _e( 'Leave a comment', 'boozurk' ); ?></label>
		<br />
		<input id="<?php echo $this->get_field_id( 'feed' ); ?>" name="<?php echo $this->get_field_name( 'feed' ); ?>" value="1" type="checkbox" <?php checked( 1 , $feed ); ?> />
		<label for="<?php echo $this->get_field_id( 'feed' ); ?>"><?php _e( 'Feed for comments', 'boozurk' ); ?></label>
		<br />
		<input id="<?php echo $this->get_field_id( 'trackback' ); ?>" name="<?php echo $this->get_field_name( 'trackback' ); ?>" value="1" type="checkbox" <?php checked( 1 , $trackback ); ?> />
		<label for="<?php echo $this->get_field_id( 'trackback' ); ?>"><?php _e( 'Trackback URL', 'boozurk' ); ?></label>
		<br />
		<input id="<?php echo $this->get_field_id( 'home' ); ?>" name="<?php echo $this->get_field_name( 'home' ); ?>" value="1" type="checkbox" <?php checked( 1 , $home ); ?> />
		<label for="<?php echo $this->get_field_id( 'home' ); ?>"><?php _e( 'Home', 'boozurk' ); ?></label>
		<br />
		<input id="<?php echo $this->get_field_id( 'next_prev' ); ?>" name="<?php echo $this->get_field_name( 'next_prev' ); ?>" value="1" type="checkbox" <?php checked( 1 , $next_prev ); ?> />
		<label for="<?php echo $this->get_field_id( 'next_prev' ); ?>"><?php _e( 'Previous/Next', 'boozurk' ); ?></label>
		<br />
		<input id="<?php echo $this->get_field_id( 'up_down' ); ?>" name="<?php echo $this->get_field_name( 'up_down' ); ?>" value="1" type="checkbox" <?php checked( 1 , $up_down ); ?> />
		<label for="<?php echo $this->get_field_id( 'up_down' ); ?>"><?php _e( 'Top/Bottom', 'boozurk' ); ?></label>
	</p>
<?php

	}
}


/**
 * Post details widget class
 */
class Boozurk_Widget_Post_Details extends WP_Widget {

	function Boozurk_Widget_Post_Details() {
		$widget_ops = array( 'classname' => 'tb_post_details', 'description' => __( "Show some details and links related to the current post. It's visible ONLY in single posts", 'boozurk' ) );
		$this->WP_Widget( 'bz-post-details', __( 'Post details', 'boozurk' ), $widget_ops);
		$this->alt_option_name = 'tb_post_details';

		$this->defaults = array(
			'title'			=> __( 'Post details', 'boozurk' ),
			'featured'		=> 1,
			'author'		=> 1,
			'avatar_size'	=> 1,
			'date'			=> 1,
			'tags'			=> 1,
			'categories'	=> 1,
			'fixed'			=> 1,
		);

	}


	function widget($args, $instance) {

		if ( !is_single() || is_attachment() ) return;

		extract($args);
		$instance = wp_parse_args( (array)$instance, $this->defaults );

		$avatar_size = $instance['avatar_size'];

		$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
		$title = $title ? $before_title . $title . $after_title : '';

		echo $before_widget;
		echo $title;
		boozurk_post_details( array( 'author' => $instance['author'], 'date' => $instance['date'], 'tags' => $instance['tags'], 'categories' => $instance['categories'], 'avatar_size' => $avatar_size, 'featured' => $instance['featured'] ) );
		echo $after_widget;

	}


	function update( $new_instance, $old_instance ) {

		$instance = $old_instance;

		$instance['title']			= strip_tags($new_instance['title']);
		$instance['featured']		= (int) $new_instance['featured'] ? 1 : 0;
		$instance['author']			= (int) $new_instance['author'] ? 1 : 0;
		$instance['avatar_size']	= in_array( $new_instance['avatar_size'], array ( '32', '48', '64', '96', '128' ) ) ? $new_instance['avatar_size'] : $this->defaults['icon_size'];
		$instance['date']			= (int) $new_instance['date'] ? 1 : 0;
		$instance['tags']			= (int) $new_instance['tags'] ? 1 : 0;
		$instance['categories']		= (int) $new_instance['categories'] ? 1 : 0;

		return $instance;

	}


	function form( $instance ) {

		$instance = wp_parse_args( (array) $instance, $this->defaults );

		extract($instance);

?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', 'boozurk' ); ?>:</label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" />
		</p>

		<p>
			<input id="<?php echo $this->get_field_id( 'featured' ); ?>" name="<?php echo $this->get_field_name( 'featured' ); ?>" value="1" type="checkbox" <?php checked( 1 , $featured ); ?> />
			<label for="<?php echo $this->get_field_id( 'featured' ); ?>"><?php _e( 'thumbnail', 'boozurk' ); ?></label>
		</p>

		<p>
			<input id="<?php echo $this->get_field_id( 'author' ); ?>" name="<?php echo $this->get_field_name( 'author' ); ?>" value="1" type="checkbox" <?php checked( 1 , $author ); ?> />
			<label for="<?php echo $this->get_field_id( 'author' ); ?>"><?php _e( 'Author', 'boozurk' ); ?></label>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'avatar_size' ); ?>"><?php _e( 'Select avatar size', 'boozurk' ); ?>:</label>
			<select name="<?php echo $this->get_field_name( 'avatar_size' ); ?>" id="<?php echo $this->get_field_id( 'avatar_size' ); ?>" >
				<?php
					$size_array = array ( '32', '48', '64', '96', '128' );
					foreach($size_array as $size) {
				?>
					<option value="<?php echo $size; ?>" <?php selected( $avatar_size, $size ); ?>><?php echo $size; ?>px</option>
				<?php
					}
				?>
			</select>
		</p>

		<p>
			<input id="<?php echo $this->get_field_id( 'date' ); ?>" name="<?php echo $this->get_field_name( 'date' ); ?>" value="1" type="checkbox" <?php checked( 1 , $date ); ?> />
			<label for="<?php echo $this->get_field_id( 'date' ); ?>"><?php _e( 'Date', 'boozurk' ); ?></label>
		</p>

		<p>
			<input id="<?php echo $this->get_field_id( 'tags' ); ?>" name="<?php echo $this->get_field_name( 'tags' ); ?>" value="1" type="checkbox" <?php checked( 1 , $tags ); ?> />
			<label for="<?php echo $this->get_field_id( 'tags' ); ?>"><?php _e( 'Tags', 'boozurk' ); ?></label>
		</p>

		<p>
			<input id="<?php echo $this->get_field_id( 'categories' ); ?>" name="<?php echo $this->get_field_name( 'categories' ); ?>" value="1" type="checkbox" <?php checked( 1 , $categories ); ?> />
			<label for="<?php echo $this->get_field_id( 'categories' ); ?>"><?php _e( 'Categories', 'boozurk' ); ?></label>
		</p>
<?php

	}

}


/**
 * Post Format list
 */
class Boozurk_Widget_Post_Formats extends WP_Widget {

	function Boozurk_Widget_Post_Formats() {

		$widget_ops = array( 'classname' => 'tb_post_formats', 'description' => __( 'A list of Post Formats', 'boozurk' ) );
		$this->WP_Widget( 'bz-widget-post-formats', __( 'Post Formats', 'boozurk' ), $widget_ops );
		$this->alt_option_name = 'tb_post_formats';

		add_action( 'save_post'		, array( &$this, 'flush_widget_cache' ) );
		add_action( 'deleted_post'	, array( &$this, 'flush_widget_cache' ) );
		add_action( 'switch_theme'	, array( &$this, 'flush_widget_cache' ) );

		$this->defaults = array(
			'title' => __( 'Post Formats', 'boozurk' ),
			'count' => 0,
			'icon' => 3
		);

	}


	function flush_widget_cache() {

		wp_cache_delete( 'tb_post_formats', 'widget' );

	}


	function widget( $args, $instance ) {

		$cache = wp_cache_get( 'tb_post_formats', 'widget' );

		if ( !is_array($cache) )
			$cache = array();

		if ( isset($cache[$args['widget_id']]) ) {
			echo $cache[$args['widget_id']];
			return;
		}

		extract( $args );
		$instance = wp_parse_args( (array)$instance, $this->defaults );

		$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base);
		$title = $title ? $before_title . $title . $after_title : '';

		$c = $instance['count'];

		$i = $instance['icon'];

		$output = '';

		foreach ( get_post_format_strings() as $slug => $string ) {
			if ( get_post_format_link($slug) ) {
				$post_format = get_term_by( 'slug', 'post-format-' . $slug, 'post_format' );
				if ( $post_format->count > 0 ) {
					$count = $c ? ' (' . $post_format->count . ')' : '';
					$text = ( $i != '2' ) ? $string : '';
					$icon = ( $i != '1' ) ? boozurk_get_the_thumb( array( 'default' => $slug, 'size_w' => 32, 'class' => 'tb-thumb-format' ) ) : '';
					$class = ( $i == '2' ) ? ' compact' : '';
					$sep = ( $text && $icon ) ? ' ' : '';
					$output .= '<li class="post-format-item' . $class . '"><a href="' . get_post_format_link($slug) . '">' . $icon . $sep . $text . '</a>' . $count . '</li>';
				}
			}
		}

		$output = $before_widget . $title . '<ul>' . $output . '</ul><br class="fixfloat" />' . $after_widget;

		echo $output;

		$cache[$args['widget_id']] = $output;
		wp_cache_set( 'tb_post_formats', $cache, 'widget' );

	}


	function update( $new_instance, $old_instance ) {

		$instance = $old_instance;

		$instance['title']	= strip_tags($new_instance['title']);
		$instance['icon']	= in_array( $new_instance['icon'], array ( '1', '2', '3' ) ) ? $new_instance['icon'] : $this->defaults['icon_size'];
		$instance['count']	= ( ( (int) $new_instance['count'] ) && ( $instance['icon'] != '2' ) ) ? 1 : 0;

		$this->flush_widget_cache();

		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset($alloptions['tb_post_formats']) )
			delete_option( 'tb_post_formats' );

		return $instance;

	}


	function form( $instance ) {

		$instance = wp_parse_args( (array) $instance, $this->defaults );

		extract($instance);

?>
	<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', 'boozurk' ); ?>:</label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" />
	</p>

	<p>
		<label for="<?php echo $this->get_field_id( 'icon' ); ?>"><?php _e( 'Show', 'boozurk' ); ?>:</label><br />
		<select name="<?php echo esc_attr( $this->get_field_name( 'icon' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'icon' ) ); ?>" >
			<option value="3" <?php selected( '3', $icon ); ?>><?php echo __( 'icons & text', 'boozurk' ); ?></option>
			<option value="2" <?php selected( '2', $icon ); ?>><?php echo __( 'icons', 'boozurk' ); ?></option>
			<option value="1" <?php selected( '1', $icon ); ?>><?php echo __( 'text', 'boozurk' ); ?></option>
		</select>
	</p>

	<p>
		<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id( 'count' ); ?>" name="<?php echo $this->get_field_name( 'count' ); ?>"<?php checked( $count ); ?> />
		<label for="<?php echo $this->get_field_id( 'count' ); ?>"><?php _e( 'Show posts count', 'boozurk' ); ?></label><br />
	</p>
<?php

	}

}


/**
 * Image EXIF widget class
 */
class Boozurk_Widget_Image_Exif extends WP_Widget {

	function Boozurk_Widget_Image_Exif() {

		$widget_ops = array( 'classname' => 'tb_exif_details', 'description' => __( "Display image EXIF details. It's visible ONLY in single attachments", "boozurk" ) );
		$this->WP_Widget( 'bz-exif-details', __( 'Image EXIF details', 'boozurk' ), $widget_ops );
		$this->alt_option_name = 'tb_exif_details';

		$this->defaults = array(
			'title' => __( 'Image EXIF details', 'boozurk' ),
		);

	}


	function widget($args, $instance) {

		if ( !is_attachment() ) return;

		extract($args);
		$instance = wp_parse_args( (array)$instance, $this->defaults );

		$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base);
		$title = $title ? $before_title . $title . $after_title : '';

		echo $before_widget . $title . boozurk_exif_details( false ) . $after_widget;

	}


	function update( $new_instance, $old_instance ) {

		$instance = $old_instance;

		$instance['title'] = strip_tags($new_instance['title']);

		return $instance;

	}


	function form( $instance ) {

		$instance = wp_parse_args( (array) $instance, $this->defaults );

		$title = esc_attr( $instance['title'] );

?>
	<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', 'boozurk' ); ?>:</label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" />
	</p>
<?php

	}
}


/**
 * User_quick_links widget class
 *
 */
class Boozurk_Widget_User_Quick_Links extends WP_Widget {

	function Boozurk_Widget_User_Quick_Links() {

		$widget_ops = array( 'classname' => 'tb_user_quick_links', 'description' => __( "Some useful links for users. It's a kind of enhanced meta widget", "boozurk" ) );
		$this->WP_Widget( 'bz-user-quick-links', __( 'User quick links', 'boozurk' ), $widget_ops );
		$this->alt_option_name = 'tb_user_quick_links';

		$this->defaults = array(
			'title' => __( 'Welcome %s', 'boozurk' ),
			'thumb' => 1,
			'nick' => 0
		);

	}


	function widget( $args, $instance ) {
		global $current_user;

		extract($args, EXTR_SKIP);
		$instance = wp_parse_args( (array)$instance, $this->defaults );

		$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
		$nick = $instance['nick'] ? boozurk_random_nick() : __( 'guest', 'boozurk' );
		$name = is_user_logged_in() ? $current_user->display_name : $nick;
		$title = sprintf ( $title, $name );
		if ( $instance['thumb'] ) {
			if ( is_user_logged_in() ) { //fix for notice when user not log-in
				$email = $current_user->user_email;
				$title = get_avatar( $email, 32, $default = get_template_directory_uri() . '/images/user.png', 'user-avatar' ) . ' ' . $title;
			} else {
				$title = get_avatar( 'dummyemail', 32, $default = get_option( 'avatar_default' ) ) . ' ' . $title;
			}
		}
		$title = $title ? $before_title . $title . $after_title : '';

?>
	<?php echo $before_widget; ?>
	<?php echo $title; ?>
	<ul>
		<?php if ( ! is_user_logged_in() || current_user_can( 'read' ) ) { wp_register(); }?>
		<?php if ( is_user_logged_in() ) { ?>
			<?php if ( current_user_can( 'read' ) ) { ?>
				<li><a href="<?php echo esc_url( admin_url( 'profile.php' ) ); ?>"><?php _e( 'Your Profile', 'boozurk' ); ?></a></li>
				<?php if ( current_user_can( 'publish_posts' ) ) { ?>
					<li><a title="<?php esc_attr_e( 'Add New Post', 'boozurk' ); ?>" href="<?php echo esc_url( admin_url( 'post-new.php' ) ); ?>"><?php _e( 'Add New Post', 'boozurk' ); ?></a></li>
				<?php } ?>
				<?php if ( current_user_can( 'moderate_comments' ) ) {
					$awaiting_mod = wp_count_comments();
					$awaiting_mod = $awaiting_mod->moderated;
					$awaiting_mod = $awaiting_mod ? ' <span class="details">(' . number_format_i18n( $awaiting_mod ) . ')</span>' : '';
				?>
					<li><a title="<?php esc_attr_e( 'Comments', 'boozurk' ); ?>" href="<?php echo esc_url( admin_url( 'edit-comments.php' ) ); ?>"><?php _e( 'Comments', 'boozurk' ); ?><?php echo $awaiting_mod; ?></a></li>
				<?php } ?>
			<?php } ?>
		<?php } ?>
		<li><?php wp_loginout(); ?></li>
	</ul>
	<?php echo $after_widget; ?>
<?php

	}


	function update( $new_instance, $old_instance ) {

		$instance = $old_instance;

		$instance['title']	= strip_tags( $new_instance['title'] );
		$instance['thumb']	= (int) $new_instance['thumb'] ? 1 : 0;
		$instance['nick']	= (int) $new_instance['nick'] ? 1 : 0;

		return $instance;

	}


	function form( $instance ) {

		$instance = wp_parse_args( (array) $instance, $this->defaults );

		extract($instance);

?>
	<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', 'boozurk' ); ?>:</label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" />
		<small><?php _e( 'default: "Welcome %s" , where %s is the user name', 'boozurk' );?></small>
	</p>

	<p>
		<input id="<?php echo $this->get_field_id( 'nick' ); ?>" name="<?php echo $this->get_field_name( 'nick' ); ?>" value="1" type="checkbox" <?php checked( 1 , $nick ); ?> />
		<label for="<?php echo $this->get_field_id( 'nick' ); ?>"><?php _e( 'Create a random nick for not-logged users', 'boozurk' ); ?></label>
	</p>

	<p>
		<input id="<?php echo $this->get_field_id( 'thumb' ); ?>" name="<?php echo $this->get_field_name( 'thumb' ); ?>" value="1" type="checkbox" <?php checked( 1 , $thumb ); ?> />
		<label for="<?php echo $this->get_field_id( 'thumb' ); ?>"><?php _e( 'Show user gravatar', 'boozurk' ); ?></label>
	</p>
<?php

	}
}


/**
 * Clean Archives Widget
 */
class Boozurk_Widget_Clean_Archives extends WP_Widget {

	function Boozurk_Widget_Clean_Archives() {

		$widget_ops = array( 'classname' => 'tb_clean_archives', 'description' => __( 'Show archives in a cleaner way', 'boozurk' ) );
		$this->WP_Widget( 'bz-clean-archives', __( 'Clean Archives', 'boozurk' ), $widget_ops );
		$this->alt_option_name = 'tb_clean_archives';

		add_action( 'save_post'		, array( $this, 'flush_widget_cache' ) );
		add_action( 'deleted_post'	, array( $this, 'flush_widget_cache' ) );
		add_action( 'switch_theme'	, array( $this, 'flush_widget_cache' ) );

		$this->defaults = array(
			'title' => __( 'Archives', 'boozurk' ),
			'month_style' => 'number',
		);

	}


	function flush_widget_cache() {

		wp_cache_delete( 'widget_recent_posts', 'widget' );

	}


	function widget($args, $instance) {
		$cache = wp_cache_get( 'tb_clean_archives', 'widget' );

		if ( !is_array($cache) )
			$cache = array();

		if ( ! isset( $args['widget_id'] ) )
			$args['widget_id'] = $this->id;

		if ( isset( $cache[ $args['widget_id'] ] ) ) {
			echo $cache[ $args['widget_id'] ];
			return;
		}

		extract( $args );
		$instance = wp_parse_args( (array)$instance, $this->defaults );

		global $wpdb; // Wordpress Database

		$years = $wpdb->get_results( "SELECT distinct year(post_date) AS year, count(ID) as posts FROM $wpdb->posts WHERE post_type = 'post' AND post_status = 'publish' GROUP BY year(post_date) ORDER BY post_date DESC" );

		if ( empty( $years ) ) {
			return; // empty archive
		}

		$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base);
		$title = $title ? $before_title . $title . $after_title : '';

		$month_style = $instance['month_style'];

		$output = '';

		if ( $month_style == 'acronym' )
			$months_short = array( '', __( 'jan', 'boozurk' ), __( 'feb', 'boozurk' ), __( 'mar', 'boozurk' ), __( 'apr', 'boozurk' ), __( 'may', 'boozurk' ), __( 'jun', 'boozurk' ), __( 'jul', 'boozurk' ), __( 'aug', 'boozurk' ), __( 'sep', 'boozurk' ), __( 'oct', 'boozurk' ), __( 'nov', 'boozurk' ), __( 'dec', 'boozurk' ) );
		else
			$months_short = array( '', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12' );

		foreach ( $years as $year ) {

			$output .= '<li><a class="year-link" href="' . get_year_link( $year->year ) . '">' . $year->year . '</a>';

			for ( $month = 1; $month <= 12; $month++ ) {

				if ( (int) $wpdb->get_var( "SELECT COUNT(ID) FROM $wpdb->posts WHERE post_type = 'post' AND post_status = 'publish' AND year(post_date) = '$year->year' AND month(post_date) = '$month'" ) > 0 ) {
					$output .= '<a class="month-link" href="' . get_month_link( $year->year, $month ) . '">' . $months_short[$month] . '</a>';
				}

			}

			$output .= '</li>';

		}

		$output = $before_widget . $title . '<ul class="tb-clean-archives">' . $output . '</ul>' . $after_widget;

		echo $output;

		$cache[$args['widget_id']] = $output;
		wp_cache_set( 'tb_clean_archives', $cache, 'widget' );

	}


	function update( $new_instance, $old_instance ) {

		$instance = $old_instance;

		$instance['title']			= strip_tags( $new_instance['title'] );
		$instance['month_style']	= in_array( $new_instance['month_style'], array ( 'number', 'acronym' ) ) ? $new_instance['month_style'] : $this->defaults['month_style'];

		$this->flush_widget_cache();

		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset($alloptions['tb_clean_archives']) )
			delete_option( 'tb_clean_archives' );

		return $instance;

	}


	function form( $instance ) {

		$instance = wp_parse_args( (array) $instance, $this->defaults );

		extract($instance);

?>
	<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', 'boozurk' ); ?>:</label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" />
	</p>

	<p>
		<label for="<?php echo $this->get_field_id( 'month_style' ); ?>"><?php _e( 'Select month style', 'boozurk' ); ?>:</label>
		<select name="<?php echo $this->get_field_name( 'month_style' ); ?>" id="<?php echo $this->get_field_id( 'month_style' ); ?>" >
			<option value="number" <?php selected( $month_style, 'number' ); ?>><?php _e( 'number', 'boozurk' ); ?></option>
			<option value="acronym" <?php selected( $month_style, 'acronym' ); ?>><?php _e( 'acronym', 'boozurk' ); ?></option>
		</select>
	</p>
<?php

	}

}


/**
 * simple font resize widget
 */
function boozurk_widget_font_resize($args) {

	extract($args);

	echo $before_widget;
	echo '<a class="fontresizer_minus" href="javascript:void(0)" title="' . esc_attr( __( 'Decrease font size', 'boozurk' ) ) . '">A</a>';
	echo '<i class="icon-angle-right"></i>';
	echo '<a class="fontresizer_reset" href="javascript:void(0)" title="' . esc_attr( __( 'Reset font size', 'boozurk' ) ) . '">A</a>';
	echo '<i class="icon-angle-right"></i>';
	echo '<a class="fontresizer_plus" href="javascript:void(0)" title="' . esc_attr( __( 'Increase font size', 'boozurk' ) ) . '">A</a>';
	echo $after_widget;

	wp_enqueue_script( 'boozurk-fontresize', get_template_directory_uri() . '/js/font-resize.min.js', array( 'jquery' ), '', true  );

}


/**
 * Register all of the default WordPress widgets on startup.
 */
function boozurk_widgets_init() {

	if ( !is_blog_installed() )
		return;

	if ( ! boozurk_get_opt( 'boozurk_custom_widgets' ) )
		return;

	register_widget( 'Boozurk_Widget_Popular_Posts' );

	register_widget( 'Boozurk_Widget_Latest_Commented_Posts' );

	register_widget( 'Boozurk_Widget_Latest_Commentators' );

	register_widget( 'Boozurk_Widget_Pop_Categories' );

	register_widget( 'Boozurk_Widget_Social' );

	register_widget( 'Boozurk_Widget_Besides' );

	register_widget( 'Boozurk_Widget_Recent_Posts' );

	register_widget( 'Boozurk_Widget_User_Quick_Links' );

	register_widget( 'Boozurk_Widget_Post_Details' );

	register_widget( 'Boozurk_Widget_Post_Formats' );

	register_widget( 'Boozurk_Widget_Image_Exif' );

	register_widget( 'Boozurk_Widget_Clean_Archives' );

	if ( boozurk_is_mobile() )
		return;

	register_widget( 'Boozurk_Widget_Navbuttons' );

	wp_register_sidebar_widget( 'bz-font-resize', 'Font Resize', 'boozurk_widget_font_resize', array( 'classname' => 'tb_font_resize', 'description' => __( 'Simple javascript-based font resizer', 'boozurk' ) ) );

}


<?php

/* Boozurk - Widgets */

/**
 * Popular_Posts widget class
 */
class boozurk_widget_popular_posts extends WP_Widget {

	function boozurk_widget_popular_posts() {
		$widget_ops = array('classname' => 'bz_widget_popular_posts', 'description' => __( 'The most commented posts on your site','boozurk') );
		$this->WP_Widget('bz-popular-posts', __('Popular Posts','boozurk'), $widget_ops);
		$this->alt_option_name = 'bz_widget_popular_posts';

		add_action( 'save_post', array(&$this, 'flush_widget_cache') );
		add_action( 'deleted_post', array(&$this, 'flush_widget_cache') );
		add_action( 'switch_theme', array(&$this, 'flush_widget_cache') );
	}

	function widget($args, $instance) {
		$cache = wp_cache_get('bz_widget_popular_posts', 'widget');

		if ( !is_array($cache) )
			$cache = array();

		if ( isset($cache[$args['widget_id']]) ) {
			echo $cache[$args['widget_id']];
			return;
		}

		ob_start();
		extract($args);

		$title = apply_filters('widget_title', $instance['title'], $instance, $this->id_base);
		if ( !$number = (int) $instance['number'] )
			$number = 10;
		else if ( $number < 1 )
			$number = 1;
		else if ( $number > 15 )
			$number = 15;
		$use_thumbs = ( !isset($instance['thumb']) || $thumb = (int) $instance['thumb'] ) ? 1 : 0;
		
		$r = new WP_Query(array('showposts' => $number, 'nopaging' => 0, 'post_status' => 'publish', 'ignore_sticky_posts' => 1, 'orderby' => 'comment_count'));
		if ($r->have_posts()) :
?>
		<?php echo $before_widget; ?>
		<?php if ( $title ) echo $before_title . $title . $after_title; ?>
		<ul<?php if ( $use_thumbs ) echo ' class="with-thumbs"'; ?>>
		<?php  while ($r->have_posts()) : $r->the_post(); ?>
			<li>
				<?php if ( $use_thumbs ) echo boozurk_get_the_thumb( get_the_ID(), 32, 32, 'bz-thumb-format' ); ?>
				<a href="<?php the_permalink() ?>" title="<?php echo esc_attr(get_the_title() ? get_the_title() : get_the_ID()); ?>"><?php if ( get_the_title() ) the_title(); else the_ID(); ?></a> <span>(<?php echo get_comments_number(); ?>)</span>
			</li>
		<?php endwhile; ?>
		</ul>
		<?php echo $after_widget; ?>
<?php
		// Reset the global $the_post as this query will have stomped on it
		wp_reset_postdata();

		endif;

		$cache[$args['widget_id']] = ob_get_flush();
		wp_cache_set('bz_widget_popular_posts', $cache, 'widget');
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['number'] = (int) $new_instance['number'];
		$instance['thumb'] = (int) $new_instance['thumb'] ? 1 : 0;
		$this->flush_widget_cache();

		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset($alloptions['bz_widget_popular_posts']) )
			delete_option('bz_widget_popular_posts');

		return $instance;
	}

	function flush_widget_cache() {
		wp_cache_delete('bz_widget_popular_posts', 'widget');
	}

	function form( $instance ) {
		$title = isset($instance['title']) ? esc_attr($instance['title']) : __('Popular Posts','boozurk');
		if ( !isset($instance['number']) || !$number = (int) $instance['number'] )
			$number = 5;
		$thumb = 1;
		if ( isset($instance['thumb']) && !$thumb = (int) $instance['thumb'] )
			$thumb = 0;
?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title','boozurk'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('Number of posts to show','boozurk'); ?></label>
			<input id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo $number; ?>" size="3" />
		</p>

		<p>
			<input id="<?php echo $this->get_field_id('thumb'); ?>" name="<?php echo $this->get_field_name('thumb'); ?>" value="1" type="checkbox" <?php checked( 1 , $thumb ); ?> />
			<label for="<?php echo $this->get_field_id('thumb'); ?>"><?php _e('Show post thumbnails','boozurk'); ?></label>
		</p>	
<?php
	}
}

/**
 * latest_Commented_Posts widget class
 *
 */
class boozurk_widget_latest_commented_posts extends WP_Widget {

	function boozurk_widget_latest_commented_posts() {
		$widget_ops = array('classname' => 'bz_widget_latest_commented_posts', 'description' => __( 'The latest commented posts/pages of your site','boozurk' ) );
		$this->WP_Widget('bz-recent-comments', __('Latest activity','boozurk'), $widget_ops);
		$this->alt_option_name = 'bz_widget_latest_commented_posts';

		add_action( 'comment_post', array(&$this, 'flush_widget_cache') );
		add_action( 'transition_comment_status', array(&$this, 'flush_widget_cache') );
	}

	function flush_widget_cache() {
		wp_cache_delete('bz_widget_latest_commented_posts', 'widget');
	}

	function widget( $args, $instance ) {
		global $comments, $comment;

		$cache = wp_cache_get('bz_widget_latest_commented_posts', 'widget');

		if ( ! is_array( $cache ) )
			$cache = array();

		if ( isset( $cache[$args['widget_id']] ) ) {
			echo $cache[$args['widget_id']];
			return;
		}

 		extract($args, EXTR_SKIP);
 		$output = '';
 		$title = apply_filters('widget_title', $instance['title']);

		if ( ! $number = (int) $instance['number'] )
 			$number = 5;
 		else if ( $number < 1 )
 			$number = 1;
		$use_thumbs = ( !isset($instance['thumb']) || $thumb = (int) $instance['thumb'] ) ? 1 : 0;

		$comments = get_comments( array( 'status' => 'approve', 'type' => 'comment', 'number' => 200 ) );
		$post_array = array();
		$counter = 0;
		$output .= $before_widget;
		if ( $title )
			$output .= $before_title . $title . $after_title;
		$ul_class = $use_thumbs ? ' class="with-thumbs"' : '';

		$output .= '<ul' . $ul_class . '>';
		if ( $comments ) {
			foreach ( (array) $comments as $comment) {
				if ( ! in_array( $comment->comment_post_ID, $post_array ) ) {
					$post = get_post( $comment->comment_post_ID );
					setup_postdata( $post );
					if ( $use_thumbs ) {
						$the_thumb = boozurk_get_the_thumb( $post->ID, 32, 32, 'bz-thumb-format' );
					} else {
						$the_thumb = '';
					}
					$output .=  '<li>' . $the_thumb . ' <a href="' . get_permalink( $post->ID ) . '" title="' .  esc_html( $post->post_title ) . '">' . $post->post_title . '</a></li>';
					$post_array[] = $comment->comment_post_ID;
					if ( ++$counter >= $number ) break;
				}
			}
 		}
		$output .= '</ul>';
		$output .= $after_widget;

		echo $output;
		$cache[$args['widget_id']] = $output;
		wp_cache_set('bz_widget_latest_commented_posts', $cache, 'widget');
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['number'] = (int) $new_instance['number'];
		$instance['thumb'] = (int) $new_instance['thumb'] ? 1 : 0;
		$this->flush_widget_cache();

		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset($alloptions['bz_widget_latest_commented_posts']) )
			delete_option('bz_widget_latest_commented_posts');

		return $instance;
	}

	function form( $instance ) {
		$title = isset($instance['title']) ? esc_attr($instance['title']) : __('Latest activity','boozurk');
		$number = isset($instance['number']) ? absint($instance['number']) : 5;
		$thumb = 1;
		if ( isset($instance['thumb']) && !$thumb = (int) $instance['thumb'] )
			$thumb = 0;
?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title','boozurk'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('Number of posts to show','boozurk'); ?></label>
			<input id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo $number; ?>" size="3" />
		</p>

		<p>
			<input id="<?php echo $this->get_field_id('thumb'); ?>" name="<?php echo $this->get_field_name('thumb'); ?>" value="1" type="checkbox" <?php checked( 1 , $thumb ); ?> />
			<label for="<?php echo $this->get_field_id('thumb'); ?>"><?php _e('Show post thumbnails','boozurk'); ?></label>
		</p>
<?php
	}
}


/**
 * latest_Comment_Authors widget class
 *
 */
class boozurk_widget_latest_commentators extends WP_Widget {

	function boozurk_widget_latest_commentators() {
		$widget_ops = array('classname' => 'bz_widget_latest_commentators', 'description' => __( 'The latest comment authors','boozurk' ) );
		$this->WP_Widget('bz-recent-commentators', __('Latest comment authors','boozurk'), $widget_ops);
		$this->alt_option_name = 'bz_widget_latest_commentators';

		add_action( 'comment_post', array(&$this, 'flush_widget_cache') );
		add_action( 'transition_comment_status', array(&$this, 'flush_widget_cache') );
	}

	function flush_widget_cache() {
		wp_cache_delete('bz_widget_latest_commentators', 'widget');
	}

	function widget( $args, $instance ) {
		global $comments, $comment;

		if ( get_option('require_name_email') != '1' ) return; //commentors must be identifiable
		
		$cache = wp_cache_get('bz_widget_latest_commentators', 'widget');

		if ( ! is_array( $cache ) )
			$cache = array();

		if ( isset( $cache[$args['widget_id']] ) ) {
			echo $cache[$args['widget_id']];
			return;
		}

 		extract($args, EXTR_SKIP);
 		$output = '';
 		$title = apply_filters('widget_title', $instance['title']);
		$icon_size = isset($instance['icon_size']) ? absint($instance['icon_size']) : '32';

		if ( ! $number = (int) $instance['number'] )
 			$number = 5;
 		else if ( $number < 1 )
 			$number = 1;

		$comments = get_comments( array( 'status' => 'approve', 'type' => 'comment', 'number' => 200 ) );
		$post_array = array();
		$counter = 0;
		$output .= $before_widget;
		if ( $title )
			$output .= $before_title . $title . $after_title;

		$output .= '<ul>';
		if ( $comments ) {
			foreach ( (array) $comments as $comment) {
				if ( !in_array( $comment->comment_author_email, $post_array ) ) {
					if ( $comment->comment_author_url == '' ) {
						$output .=  '<li title="' .  $comment->comment_author . '">' . get_avatar( $comment, $icon_size, $default=get_option('avatar_default') ) . '</li>';
					} else {
						$output .=  '<li title="' .  $comment->comment_author . '"><a target="_blank" href="' . $comment->comment_author_url . '">' . get_avatar( $comment, $icon_size, $default=get_option('avatar_default')) . '</a></li>';
					}
					$post_array[] = $comment->comment_author_email;
					if ( ++$counter >= $number ) break;
				}
			}
 		}
		$output .= '</ul><div class="fixfloat"></div>';
		$output .= $after_widget;

		echo $output;
		$cache[$args['widget_id']] = $output;
		wp_cache_set('bz_widget_latest_commentators', $cache, 'widget');
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['number'] = (int) $new_instance['number'];
        $instance["icon_size"] = in_array( $new_instance["icon_size"], array ('16', '24', '32', '48', '64') ) ? $new_instance["icon_size"] : '32' ;
		$this->flush_widget_cache();

		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset($alloptions['bz_widget_latest_commentators']) )
			delete_option('bz_widget_latest_commentators');

		return $instance;
	}

	function form( $instance ) {
		$title = isset($instance['title']) ? esc_attr($instance['title']) :  __('Latest comment authors','boozurk');
		$number = isset($instance['number']) ? absint($instance['number']) : 5;
		$icon_size = isset($instance['icon_size']) ? absint($instance['icon_size']) : '32';

		if ( get_option('require_name_email') != '1' ) {
			printf ( __( 'Comment authors <strong>must</strong> use a name and a valid e-mail in order to use this widget. Check the <a href="%1$s">Discussion settings</a>','boozurk' ), esc_url( admin_url( 'options-discussion.php' ) ) );
			return;
		}
?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title','boozurk'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('Number of users to show','boozurk'); ?></label>
			<input id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo $number; ?>" size="3" />
		</p>
		<p>
            <label for="<?php echo $this->get_field_id('icon_size'); ?>"><?php _e('Select your icon size', 'boozurk'); ?></label><br />
            <select name="<?php echo $this->get_field_name('icon_size'); ?>" id="<?php echo $this->get_field_id('icon_size'); ?>" >
<?php
            $size_array = array ('16', '24', '32', '48', '64');
            foreach($size_array as $size) {
?>
                <option value="<?php echo $size; ?>" <?php selected( $icon_size, $size ); ?>><?php echo $size; ?>px</option>
<?php
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
class boozurk_Widget_pop_categories extends WP_Widget {

	function boozurk_Widget_pop_categories() {
		$widget_ops = array( 'classname' => 'bz_widget_categories', 'description' => __( 'A list of popular categories', 'boozurk' ) );
		$this->WP_Widget('bz-categories', __('Popular Categories', 'boozurk'), $widget_ops);
	}

	function widget( $args, $instance ) {
		extract( $args );

		$title = apply_filters('widget_title', $instance['title'], $instance, $this->id_base);
		if ( !$number = (int) $instance['number'] )
			$number = 10;
		else if ( $number < 1 )
			$number = 1;
		else if ( $number > 15 )
			$number = 15;

		echo $before_widget;
		if ( $title ) echo $before_title . $title . $after_title;
?>
		<ul>
<?php
		$cat_args = 'number=' . $number . '&title_li=&orderby=count&order=DESC&hierarchical=0&show_count=1';
		wp_list_categories($cat_args);
?>
			<li style="text-align: right;margin-top:12px;"><a title="<?php _e('View all categories', 'boozurk'); ?>" href="<?php  echo home_url(); ?>/?allcat=y"><?php _e('View all', 'boozurk'); ?></a></li>
		</ul>
<?php
		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['number'] = (int) $new_instance['number'];

		return $instance;
	}

	function form( $instance ) {
		//Defaults
		$instance = wp_parse_args( (array) $instance, array( 'title' => __( 'Popular Categories', 'boozurk' )) );
		$title = esc_attr( $instance['title'] );
		if ( !isset($instance['number']) || !$number = (int) $instance['number'] )
			$number = 5;
?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title','boozurk'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('Number of categories to show','boozurk'); ?></label>
			<input id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo $number; ?>" size="3" />
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

class boozurk_Widget_social extends WP_Widget {
	function boozurk_Widget_social() {
		$widget_ops = array(
            'classname' => 'bz-widget-social',
            'description' => __("This widget lets visitors of your blog to subscribe to it and follow you on popular social networks like Twitter, FaceBook etc.", "boozurk"));
		$control_ops = array('width' => 650);

		$this->WP_Widget("bz-social", __("Follow Me", "boozurk"), $widget_ops, $control_ops);
        $this->follow_urls = array(
			'Buzz',
			'Delicious',
			'Deviantart',
			'Digg',
			'Dropbox',
			'Facebook',
			'Flickr',
			'Github',
			'GooglePlus',
			'Hi5',
			'LinkedIn',
			'Myspace',
			'Odnoklassniki',
			'Orkut',
			'Qzone',
			'Reddit',
			'Sina',
			'StumbleUpon',
			'Technorati',
			'Tencent',
			'Twitter',
			'Vimeo',
			'VKontakte',
			'WindowsLive',
			'Youtube',
			'Mail',
			'RSS');
	}

    function form($instance) {
        $defaults = array("title" => __("Follow Me", "boozurk"),
            "icon_size" => 48,
        );
        foreach ($this->follow_urls as $follow_service ) {
            $defaults[$follow_service."_icon"] = $follow_service;
            $defaults["show_".$follow_service] = false;
        }
        $instance = wp_parse_args((array)$instance, $defaults);
?>
	<p><label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Title', 'boozurk' ); ?></label>
	<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['title']); ?>" /></p>
    <div style="padding: 10px 0; border-top: 1px solid #DFDFDF;">

<?php
        foreach($this->follow_urls as $follow_service ) {
?>
        <div style="float: left; width: 40%; margin: 0pt 5%;">
			<h2>
				<input id="<?php echo $this->get_field_id('show_'.$follow_service); ?>" name="<?php echo $this->get_field_name('show_'.$follow_service); ?>" type="checkbox" <?php checked( $instance['show_'.$follow_service], 'on'); ?>  class="checkbox" />
				<img style="vertical-align:middle; width:32px; height:32px;" src="<?php echo get_template_directory_uri(); ?>/images/follow/<?php echo $follow_service; ?>.png" alt="<?php echo $follow_service; ?>" />
				<?php echo $follow_service; ?>
			</h2>
<?php
            if ( ( $follow_service != 'RSS' ) && ( $follow_service != 'Mail') ) {
?>
        <p>
            <label for="<?php echo $this->get_field_id($follow_service.'_account'); ?>">
<?php
				printf(__('Enter your %1$s <b>full link</b>', 'boozurk'), $follow_service);
?>
            </label>
            <input id="<?php echo $this->get_field_id($follow_service.'_account'); ?>" name="<?php echo $this->get_field_name($follow_service.'_account'); ?>" value="<?php if (isset($instance[$follow_service.'_account'])) echo $instance[$follow_service.'_account']; ?>" class="widefat" />
        </p>

<?php
            } elseif ($follow_service == 'Mail') {
?>
        <p>
            <label for="<?php echo $this->get_field_id($follow_service.'_account'); ?>">
<?php
				printf(__('Enter mail address', 'boozurk'), $follow_service);
?>
            </label>
            <input id="<?php echo $this->get_field_id($follow_service.'_account'); ?>" name="<?php echo $this->get_field_name($follow_service.'_account'); ?>" value="<?php if (isset($instance[$follow_service.'_account'])) echo $instance[$follow_service.'_account']; ?>" class="widefat" />
        </p>

<?php
            } 

?>
        </div>
<?php
        }
?>
        <div class="clear" style="padding: 10px 0; border-top: 1px solid #DFDFDF; text-align: right;">
            <label for="<?php echo $this->get_field_id('icon_size'); ?>"><?php _e('Select your icon size', 'boozurk'); ?></label><br />
            <select name="<?php echo $this->get_field_name('icon_size'); ?>" id="<?php echo $this->get_field_id('icon_size'); ?>" >
<?php
            $size_array = array ('16', '24', '32', '48', '64');
            foreach($size_array as $size) {
?>
                <option value="<?php echo $size; ?>" <?php selected( $instance['icon_size'], $size ); ?>><?php echo $size; ?>px</option>
<?php
            }
?>
            </select>
        </div>
    </div>
<?php
    }

    function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance["title"] = strip_tags($new_instance["title"]);
        $instance["icon_size"] = in_array( $new_instance["icon_size"], array ('16', '24', '32', '48', '64') ) ? $new_instance["icon_size"] : '16' ;

        foreach ($this->follow_urls as $follow_service ) {
            $instance['show_'.$follow_service] = $new_instance['show_'.$follow_service];
            $instance[$follow_service.'_account'] = $new_instance[$follow_service.'_account'];
        }

        return $instance;
    }

	function widget( $args, $instance ) {
		extract($args);
		$title = apply_filters('widget_title', $instance['title']);
		$icon_size = isset($instance['icon_size']) ? absint($instance['icon_size']) : '16';
		echo $before_widget;
		if ( $title ) echo $before_title . $title . $after_title;
		foreach ($this->follow_urls as $follow_service ) {
			$show = ( isset($instance['show_'.$follow_service]) ) ? $instance['show_'.$follow_service] : false;
			$account = ( isset($instance[$follow_service.'_account']) ) ? $instance[$follow_service.'_account'] : '';
			$prefix = __('Follow us on %s','boozurk');
			$onclick = '';
			$class = '';
			$target = '_blank';
			if ($follow_service == 'RSS') {
				$account = get_bloginfo( 'rss2_url' );
				$prefix = __('Keep updated with our %s feed','boozurk');
			}
			if ($follow_service == 'Mail') {
				$account = preg_replace('/(.)(.)/', '$2$1', 'mailto:'.$account);
				$prefix = __('Contact us','boozurk');
				$class= ' hide-if-no-js';
				$onclick = ' onclick="this.href=\'' . $account . '\'.replace(/(.)(.)/g, \'$2$1\');"';
				$account = '#';
				$target = '_self';
			}
			if ($show && !empty($account)) {
?>
        <a target="<?php echo $target; ?>" href="<?php echo $account; ?>"<?php echo $onclick; ?> class="bz-social-icon<?php echo $class; ?>" title="<?php printf( $prefix, $follow_service ); ?>">
            <img src="<?php echo get_template_directory_uri(); ?>/images/follow/<?php echo $follow_service;?>.png" alt="<?php echo $follow_service;?>" style="width: <?php echo $icon_size;?>px; height: <?php echo $icon_size;?>px;" />
        </a>
<?php
            }
        }
        echo $after_widget;
    }
}


/**
 * Makes a custom Widget for displaying Aside and Status Posts
 *
 * Based on Twenty_Eleven_Ephemera_Widget
 *
 */

class boozurk_Widget_besides extends WP_Widget {

	/**
	 * Constructor
	 *
	 * @return void
	 **/
	function boozurk_Widget_besides() {
		$widget_ops = array( 'classname' => 'bz_widget_besides', 'description' => __( 'Use this widget to list your recent Aside and Status posts', 'boozurk' ) );
		$this->WP_Widget( 'bz-widget-besides', __( 'besides...', 'boozurk' ), $widget_ops );
		$this->alt_option_name = 'bz_widget_besides';

		add_action( 'save_post', array(&$this, 'flush_widget_cache' ) );
		add_action( 'deleted_post', array(&$this, 'flush_widget_cache' ) );
		add_action( 'switch_theme', array(&$this, 'flush_widget_cache' ) );
	}
	function widget( $args, $instance ) {
		$cache = wp_cache_get( 'bz_widget_besides', 'widget' );

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

		$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base);

		$type = ( isset( $instance['type'] ) ) ? $instance['type'] : 'aside';

		if ( ! isset( $instance['number'] ) )
			$instance['number'] = '10';

		if ( ! $number = absint( $instance['number'] ) )
 			$number = 10;

		$besides_args = array(
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
		$besides = new WP_Query();
		$besides->query( $besides_args );

		if ( $besides->have_posts() ) :

		echo $before_widget;
		if ( $title ) echo $before_title . $title . $after_title;

		?>
		<?php while ( $besides->have_posts() ) : $besides->the_post(); ?>

			<?php if ( $type == 'aside' ) { ?>
			<div class="wentry-aside">
				<?php the_content(); ?>
				<span style="font-style: italic; color: #999;"><?php the_author(); ?> - <a href="<?php the_permalink() ?>" rel="bookmark"><?php the_time( get_option( 'date_format' ) ); ?></a> - <?php comments_popup_link('(0)', '(1)','(%)'); ?></span>
			</div>
			<?php } elseif ( $type == 'status' ) { ?>
			<div class="wentry-status">
				<?php echo get_avatar( get_the_author_meta('user_email'), 24, $default=get_option('avatar_default'), get_the_author() ); ?>
				<a style="font-weight: bold;" href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>" title="<?php printf( 'View all posts by %s', esc_attr( get_the_author() ) ); ?>"><?php echo get_the_author(); ?></a>
				<?php the_content(); ?>
				<span style="color: #999;"><?php echo boozurk_friendly_date(); ?></span>
			</div>
			<?php } ?>

		<?php endwhile; ?>
		<?php

		echo $after_widget;

		wp_reset_postdata();

		endif;

		$cache[$args['widget_id']] = ob_get_flush();
		wp_cache_set( 'bz_widget_besides', $cache, 'widget' );
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['number'] = (int) $new_instance['number'];
		$instance['type'] = in_array( $new_instance['type'], array( 'aside', 'status' ) ) ? $new_instance['type'] : 'aside';
		$this->flush_widget_cache();

		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset( $alloptions['bz_widget_besides'] ) )
			delete_option( 'bz_widget_besides' );

		return $instance;
	}

	function flush_widget_cache() {
		wp_cache_delete( 'bz_widget_besides', 'widget' );
	}

	function form( $instance ) {
		$title = isset( $instance['title']) ? esc_attr( $instance['title'] ) : __( 'besides...', 'boozurk' );
		$number = isset( $instance['number'] ) ? absint( $instance['number'] ) : 10;
		$type = isset( $instance['type'] ) ? $instance['type'] : 'aside';
?>
			<p><label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Title', 'boozurk' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" /></p>

            <p><label for="<?php echo esc_attr( $this->get_field_id( 'type' ) ); ?>"><?php _e( 'Type of posts to show', 'boozurk' ); ?></label>
            <select name="<?php echo esc_attr( $this->get_field_name( 'type' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'type' ) ); ?>" >
<?php
            $type_array = array( 'aside', 'status' );
            foreach($type_array as $avaible_type) {
?>
                <option value="<?php echo $avaible_type; ?>" <?php selected( $type, $avaible_type ); ?>><?php echo $avaible_type; ?></option>
<?php
            }
?>
            </select></p>

			<p><label for="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>"><?php _e( 'Number of posts to show', 'boozurk' ); ?></label>
			<input id="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'number' ) ); ?>" type="text" value="<?php echo esc_attr( $number ); ?>" size="3" /></p>
		<?php
	}
}

/**
 * Recent Posts in Category widget class
 *
 */
class boozurk_Widget_recent_posts extends WP_Widget {

	function boozurk_Widget_recent_posts() {
		$widget_ops = array('classname' => 'bz_widget_recent_entries', 'description' => __( "The most recent posts in a single category", 'boozurk' ) );
		$this->WP_Widget('bz-recent-posts', __('Recent Posts in Category', 'boozurk' ), $widget_ops);
		$this->alt_option_name = 'bz_widget_recent_entries';

		add_action( 'save_post', array(&$this, 'flush_widget_cache') );
		add_action( 'deleted_post', array(&$this, 'flush_widget_cache') );
		add_action( 'switch_theme', array(&$this, 'flush_widget_cache') );
	}

	function widget($args, $instance) {
		$cache = wp_cache_get('bz_widget_recent_posts', 'widget');

		if ( !is_array($cache) )
			$cache = array();

		if ( isset($cache[$args['widget_id']]) ) {
			echo $cache[$args['widget_id']];
			return;
		}

		ob_start();
		extract($args);

		$category = isset( $instance['category']) ? intval($instance['category'] ) : '';
		if ( $category === -1 ) {
			if ( !is_single() || is_attachment() ) return;
			global $post;
			$category = get_the_category( $post->ID );
			$category = ( $category ) ? $category[0]->cat_ID : '';
		}
		
		$use_thumbs = ( !isset($instance['thumb']) || $thumb = (int) $instance['thumb'] ) ? 1 : 0;
		$description = ( !isset($instance['description']) || $description = (int) $instance['description'] ) ? 1 : 0;
		$title = apply_filters('widget_title', $instance['title'], $instance, $this->id_base);
		$title = sprintf( $title, '<a href="' . get_category_link( $category ) . '">' . get_cat_name( $category ) . '</a>' );
		if ( ! $number = absint( $instance['number'] ) )
 			$number = 10;

		$r = new WP_Query( array( 'cat' => $category, 'posts_per_page' => $number, 'nopaging' => 0, 'post_status' => 'publish', 'ignore_sticky_posts' => true ) );
		if ($r->have_posts()) :
?>
		<?php echo $before_widget; ?>
		<?php if ( $title ) echo $before_title . $title . $after_title; ?>
		<?php if ( $description && category_description( $category ) ) echo '<div class="bz-cat-descr">' . category_description( $category ) . '</div>'; ?>
		<ul<?php if ( $use_thumbs ) echo ' class="with-thumbs"'; ?>>
		<?php  while ($r->have_posts()) : $r->the_post(); ?>
			<li>
				<?php if ( $use_thumbs ) echo boozurk_get_the_thumb( get_the_ID(), 32, 32, 'bz-thumb-format' ); ?>
				<a href="<?php the_permalink() ?>" title="<?php echo esc_attr(get_the_title() ? get_the_title() : get_the_date()); ?>"><?php if ( get_the_title() ) the_title(); else echo get_the_date(); ?></a>
			</li>
		<?php endwhile; ?>
		</ul>
		<?php echo $after_widget; ?>
<?php
		// Reset the global $the_post as this query will have stomped on it
		wp_reset_postdata();

		endif;

		$cache[$args['widget_id']] = ob_get_flush();
		wp_cache_set('bz_widget_recent_posts', $cache, 'widget');
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['number'] = (int) $new_instance['number'];
		$instance['category'] = (int) $new_instance['category'];
		$instance['thumb'] = (int) $new_instance['thumb'] ? 1 : 0;
		$instance['description'] = (int) $new_instance['description'] ? 1 : 0;
		$this->flush_widget_cache();

		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset($alloptions['bz_widget_recent_entries']) )
			delete_option('bz_widget_recent_entries');

		return $instance;
	}

	function flush_widget_cache() {
		wp_cache_delete('bz_widget_recent_posts', 'widget');
	}

	function form( $instance ) {
		$title = isset($instance['title']) ? esc_attr($instance['title']) : __('Recent Posts in %s', 'boozurk' );
		$number = isset($instance['number']) ? absint($instance['number']) : 5;
		$category = isset($instance['category']) ? intval($instance['category']) : '';
		$thumb = isset($instance['thumb']) ? absint($instance['thumb']) : 1;
		$description = isset($instance['description']) ? absint($instance['description']) : 1;
?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title', 'boozurk' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('category'); ?>"><?php _e('Category', 'boozurk' ); ?></label>
			<?php $dropdown_categories = wp_dropdown_categories( Array(
						'orderby'            => 'ID', 
						'order'              => 'ASC',
						'show_count'         => 1,
						'hide_empty'         => 0,
						'hide_if_empty'      => true,
						'echo'               => 0,
						'selected'           => $category,
						'hierarchical'       => 1, 
						'name'               => $this->get_field_name('category'),
						'id'                 => $this->get_field_id('category'),
						'class'              => 'widefat',
						'taxonomy'           => 'category',
					) ); ?>
					
			<?php 
			echo str_replace( '</select>', '<option ' . selected( $category , -1 , 0 ) . 'value="-1" class="level-0">' . __( '(current post category)', 'boozurk' ) . '</option></select>', $dropdown_categories );
			?>
			<small><?php echo __( 'by selecting "(current post category)", the widget will be visible ONLY in single posts', 'boozurk' ); ?></small>
		</p>

		<p>
			<input id="<?php echo $this->get_field_id('description'); ?>" name="<?php echo $this->get_field_name('description'); ?>" value="1" type="checkbox" <?php checked( 1 , $description ); ?> />
			<label for="<?php echo $this->get_field_id('description'); ?>"><?php _e('Show category description','boozurk'); ?></label>
		</p>	
		<p>
			<label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('Number of posts to show', 'boozurk' ); ?></label>
			<input id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo $number; ?>" size="3" />
		</p>

		<p>
			<input id="<?php echo $this->get_field_id('thumb'); ?>" name="<?php echo $this->get_field_name('thumb'); ?>" value="1" type="checkbox" <?php checked( 1 , $thumb ); ?> />
			<label for="<?php echo $this->get_field_id('thumb'); ?>"><?php _e('Show post thumbnails','boozurk'); ?></label>
		</p>	

<?php
	}
}

/**
 * Navigation buttons widget class
 */
class boozurk_Widget_navbuttons extends WP_Widget {

	function boozurk_Widget_navbuttons() {
		$widget_ops = array('classname' => 'bz_Widget_navbuttons', 'description' => __( 'Some usefull buttons for an easier navigation experience','boozurk') );
		$this->WP_Widget('bz-navbuttons', __('Navigation buttons','boozurk'), $widget_ops);
		$this->alt_option_name = 'bz_Widget_navbuttons';

	}

	function widget($args, $instance) {

		extract($args);

?>
		<?php echo $before_widget; ?>
		<?php boozurk_navbuttons( $instance['print'], $instance['comment'], $instance['feed'], $instance['trackback'], $instance['home'], $instance['next_prev'], $instance['up_down'], $instance['fixed'] ); ?>
		<?php echo $after_widget; ?>
<?php
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['print'] = (int) $new_instance['print'] ? 1 : 0;
		$instance['comment'] = (int) $new_instance['comment'] ? 1 : 0;
		$instance['feed'] = (int) $new_instance['feed'] ? 1 : 0;
		$instance['trackback'] = (int) $new_instance['trackback'] ? 1 : 0;
		$instance['home'] = (int) $new_instance['home'] ? 1 : 0;
		$instance['next_prev'] = (int) $new_instance['next_prev'] ? 1 : 0;
		$instance['up_down'] = (int) $new_instance['up_down'] ? 1 : 0;
		$instance['fixed'] = (int) $new_instance['fixed'] ? 1 : 0;

		return $instance;
	}

	function form( $instance ) {
		$print = isset($instance['print']) ? absint($instance['print']) : 1;
		$comment = isset($instance['comment']) ? absint($instance['comment']) : 1;
		$feed = isset($instance['feed']) ? absint($instance['feed']) : 1;
		$trackback = isset($instance['trackback']) ? absint($instance['trackback']) : 1;
		$home = isset($instance['home']) ? absint($instance['home']) : 1;
		$next_prev = isset($instance['next_prev']) ? absint($instance['next_prev']) : 1;
		$up_down = isset($instance['up_down']) ? absint($instance['up_down']) : 1;
		$fixed = isset($instance['fixed']) ? absint($instance['fixed']) : 1;
?>
		<p>
			<input id="<?php echo $this->get_field_id('print'); ?>" name="<?php echo $this->get_field_name('print'); ?>" value="1" type="checkbox" <?php checked( 1 , $print ); ?> />
			<label for="<?php echo $this->get_field_id('print'); ?>"><?php _e('Print preview','boozurk'); ?></label>
			</br>
			<input id="<?php echo $this->get_field_id('comment'); ?>" name="<?php echo $this->get_field_name('comment'); ?>" value="1" type="checkbox" <?php checked( 1 , $comment ); ?> />
			<label for="<?php echo $this->get_field_id('comment'); ?>"><?php _e('Leave a comment','boozurk'); ?></label>
			</br>
			<input id="<?php echo $this->get_field_id('feed'); ?>" name="<?php echo $this->get_field_name('feed'); ?>" value="1" type="checkbox" <?php checked( 1 , $feed ); ?> />
			<label for="<?php echo $this->get_field_id('feed'); ?>"><?php _e('Feed for comments','boozurk'); ?></label>
			</br>
			<input id="<?php echo $this->get_field_id('trackback'); ?>" name="<?php echo $this->get_field_name('trackback'); ?>" value="1" type="checkbox" <?php checked( 1 , $trackback ); ?> />
			<label for="<?php echo $this->get_field_id('trackback'); ?>"><?php _e('Trackback URL','boozurk'); ?></label>
			</br>
			<input id="<?php echo $this->get_field_id('home'); ?>" name="<?php echo $this->get_field_name('home'); ?>" value="1" type="checkbox" <?php checked( 1 , $home ); ?> />
			<label for="<?php echo $this->get_field_id('home'); ?>"><?php _e('Home','boozurk'); ?></label>
			</br>
			<input id="<?php echo $this->get_field_id('next_prev'); ?>" name="<?php echo $this->get_field_name('next_prev'); ?>" value="1" type="checkbox" <?php checked( 1 , $next_prev ); ?> />
			<label for="<?php echo $this->get_field_id('next_prev'); ?>"><?php _e('Previous/Next','boozurk'); ?></label>
			</br>
			<input id="<?php echo $this->get_field_id('up_down'); ?>" name="<?php echo $this->get_field_name('up_down'); ?>" value="1" type="checkbox" <?php checked( 1 , $up_down ); ?> />
			<label for="<?php echo $this->get_field_id('up_down'); ?>"><?php _e('Top/Bottom','boozurk'); ?></label>
			</br>
			</br>
			<input id="<?php echo $this->get_field_id('fixed'); ?>" name="<?php echo $this->get_field_name('fixed'); ?>" value="1" type="checkbox" <?php checked( 1 , $fixed ); ?> />
			<label for="<?php echo $this->get_field_id('fixed'); ?>"><?php _e('Fixed position (bottom right)','boozurk'); ?></label>
		</p>

<?php
	}
}

/**
 * Post details widget class
 */
class boozurk_Widget_post_details extends WP_Widget {

	function boozurk_Widget_post_details() {
		$widget_ops = array('classname' => 'bz_Widget_post_details', 'description' => __( "Show some details and links related to the current post. It's visible ONLY in single posts",'boozurk') );
		$this->WP_Widget('bz-post-details', __('Post details','boozurk'), $widget_ops);
		$this->alt_option_name = 'bz_Widget_post_details';

	}

	function widget($args, $instance) {
		if ( !is_single() || is_attachment() ) return;
		extract($args);

		$avatar_size = isset($instance['avatar_size']) ? absint($instance['avatar_size']) : '48';

		$title = apply_filters('widget_title', $instance['title'], $instance, $this->id_base);
		echo $before_widget;
		if ( $title ) echo $before_title . $title . $after_title;
		boozurk_post_details( $instance['author'], $instance['date'], $instance['tags'], $instance['categories'], false, $avatar_size );
		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['author'] = (int) $new_instance['author'] ? 1 : 0;
        $instance["avatar_size"] = in_array( $new_instance["avatar_size"], array ('32', '48', '64', '96', '128') ) ? $new_instance["avatar_size"] : '48' ;
		$instance['date'] = (int) $new_instance['date'] ? 1 : 0;
		$instance['tags'] = (int) $new_instance['tags'] ? 1 : 0;
		$instance['categories'] = (int) $new_instance['categories'] ? 1 : 0;

		return $instance;
	}

	function form( $instance ) {
		$title = isset($instance['title']) ? esc_attr($instance['title']) : __('Post details','boozurk');
		$author = isset($instance['author']) ? absint($instance['author']) : 1;
		$avatar_size = isset($instance['avatar_size']) ? absint($instance['avatar_size']) : '48';
		$date = isset($instance['date']) ? absint($instance['date']) : 1;
		$tags = isset($instance['tags']) ? absint($instance['tags']) : 1;
		$categories = isset($instance['categories']) ? absint($instance['categories']) : 1;
?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title','boozurk'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
		</p>
		<p>
			<input id="<?php echo $this->get_field_id('author'); ?>" name="<?php echo $this->get_field_name('author'); ?>" value="1" type="checkbox" <?php checked( 1 , $author ); ?> />
			<label for="<?php echo $this->get_field_id('author'); ?>"><?php _e('Author','boozurk'); ?></label>
		</p>
		<p>
            <label for="<?php echo $this->get_field_id('avatar_size'); ?>"><?php _e('Select avatar size', 'boozurk'); ?></label>
            <select name="<?php echo $this->get_field_name('avatar_size'); ?>" id="<?php echo $this->get_field_id('avatar_size'); ?>" >
<?php
            $size_array = array ('32', '48', '64', '96', '128');
            foreach($size_array as $size) {
?>
                <option value="<?php echo $size; ?>" <?php selected( $avatar_size, $size ); ?>><?php echo $size; ?>px</option>
<?php
            }
?>
            </select>
		</p>
		<p>
			<input id="<?php echo $this->get_field_id('date'); ?>" name="<?php echo $this->get_field_name('date'); ?>" value="1" type="checkbox" <?php checked( 1 , $date ); ?> />
			<label for="<?php echo $this->get_field_id('date'); ?>"><?php _e('Date','boozurk'); ?></label>
		</p>
		<p>
			<input id="<?php echo $this->get_field_id('tags'); ?>" name="<?php echo $this->get_field_name('tags'); ?>" value="1" type="checkbox" <?php checked( 1 , $tags ); ?> />
			<label for="<?php echo $this->get_field_id('tags'); ?>"><?php _e('Tags','boozurk'); ?></label>
		</p>
		<p>
			<input id="<?php echo $this->get_field_id('categories'); ?>" name="<?php echo $this->get_field_name('categories'); ?>" value="1" type="checkbox" <?php checked( 1 , $categories ); ?> />
			<label for="<?php echo $this->get_field_id('categories'); ?>"><?php _e('Categories','boozurk'); ?></label>
		</p>
<?php
	}
}

/**
 * Post Format list
 */
class boozurk_Widget_post_formats extends WP_Widget {

	function boozurk_Widget_post_formats() {
		$widget_ops = array( 'classname' => 'bz_widget_post_formats', 'description' => __( 'A list of Post Formats','boozurk' ) );
		$this->WP_Widget('bz-widget-post-formats', __('Post Formats','boozurk'), $widget_ops);
		$this->alt_option_name = 'bz_widget_post_formats';

		add_action( 'save_post', array(&$this, 'flush_widget_cache') );
		add_action( 'deleted_post', array(&$this, 'flush_widget_cache') );
		add_action( 'switch_theme', array(&$this, 'flush_widget_cache') );
	}

	function flush_widget_cache() {
		wp_cache_delete('bz_widget_post_formats', 'widget');
	}

	function widget( $args, $instance ) {
		$cache = wp_cache_get('bz_widget_post_formats', 'widget');

		if ( !is_array($cache) )
			$cache = array();

		if ( isset($cache[$args['widget_id']]) ) {
			echo $cache[$args['widget_id']];
			return;
		}

		ob_start();
		extract( $args );

		$title = apply_filters('widget_title', $instance['title'], $instance, $this->id_base);
		$c = $instance['count'] ? '1' : '0';
		$i = in_array( $instance['icon'], array ('1', '2', '3') ) ? $instance['icon'] : '3' ;

		echo $before_widget;
		if ( $title )
			echo $before_title . $title . $after_title;

?>
		<ul>
<?php
		foreach ( get_post_format_strings() as $slug => $string ) {
			if ( get_post_format_link($slug) ) {
				$post_format = get_term_by( 'slug', 'post-format-' . $slug, 'post_format' );
				if ( $post_format->count > 0 ) {
					$count = $c ? ' (' . $post_format->count . ')' : '';
					$text = ( $i != '2' ) ? $string : '';
					$icon = ( $i != '1' ) ? '<img title="' . $string . '" class="bz-thumb-format ' . $slug . '" alt="thumb" src="' . get_template_directory_uri() . '/images/img40.png" />' : '';
					$class = ( $i == '2' ) ? ' compact' : '';
					echo '<li class="post-format-item' . $class . '"><a href="' . get_post_format_link($slug) . '">' . $icon . $text . '</a>' . $count . '</li>';
				}
			}
		}
?>
		</ul>
		<div class="fixfloat"></div>
<?php
		echo $after_widget;
		$cache[$args['widget_id']] = ob_get_flush();
		wp_cache_set('bz_widget_post_formats', $cache, 'widget');
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['icon'] = in_array( $new_instance['icon'], array ('1', '2', '3') ) ? $new_instance['icon'] : '3' ;
		$instance['count'] = ( !empty($new_instance['count']) && ( $instance['icon'] != '2' ) ) ? 1 : 0;
		$this->flush_widget_cache();

		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset($alloptions['bz_widget_post_formats']) )
			delete_option('bz_widget_post_formats');

		return $instance;
	}

	function form( $instance ) {
		//Defaults
		$title = isset($instance['title']) ? esc_attr( $instance['title'] ) : __( 'Post Formats','boozurk' );
		$count = isset($instance['count']) ? (bool) $instance['count'] : false;
		$icon = isset($instance['icon']) ? absint($instance['icon']) : 3;
?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e( 'Title','boozurk' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>
		<p>
			<label for="<?php echo $this->get_field_id('icon'); ?>"><?php _e( 'Show','boozurk' ); ?></label><br />
			<select name="<?php echo esc_attr( $this->get_field_name( 'icon' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'icon' ) ); ?>" >
				<option value="3" <?php selected( '3', $icon ); ?>><?php echo __('icons & text','boozurk'); ?></option>
				<option value="2" <?php selected( '2', $icon ); ?>><?php echo __('icons','boozurk'); ?></option>
				<option value="1" <?php selected( '1', $icon ); ?>><?php echo __('text','boozurk'); ?></option>
			</select>
		</p>
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('count'); ?>" name="<?php echo $this->get_field_name('count'); ?>"<?php checked( $count ); ?> />
			<label for="<?php echo $this->get_field_id('count'); ?>"><?php _e( 'Show posts count','boozurk' ); ?></label><br />
		</p>

<?php
	}

}

/**
 * Image EXIF widget class
 */
class boozurk_Widget_image_EXIF extends WP_Widget {

	function boozurk_Widget_image_EXIF() {
		$widget_ops = array('classname' => 'bz_Widget_exif_details', 'description' => __( "Display image EXIF details. It's visible ONLY in single attachments",'boozurk') );
		$this->WP_Widget('bz-exif-details', __('Image EXIF details','boozurk'), $widget_ops);
		$this->alt_option_name = 'bz_Widget_exif_details';

	}

	function widget($args, $instance) {
		if ( !is_attachment() ) return;
		extract($args);
		
		$title = apply_filters('widget_title', $instance['title'], $instance, $this->id_base);
?>
		<?php echo $before_widget; ?>
		<?php if ( $title ) echo $before_title . $title . $after_title; ?>
		<?php boozurk_exif_details(); ?>
		<?php echo $after_widget; ?>
<?php
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);

		return $instance;
	}

	function form( $instance ) {
		$title = isset($instance['title']) ? esc_attr($instance['title']) : __('Image EXIF details','boozurk');
?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title','boozurk'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
		</p>

<?php
	}
}

/**
 * User_quick_links widget class
 *
 */
class boozurk_Widget_user_quick_links extends WP_Widget {

	function boozurk_Widget_user_quick_links() {
		$widget_ops = array('classname' => 'bz_widget_user_quick_links', 'description' => __( "Some useful links for users. It's a kind of enhanced meta widget",'boozurk' ) );
		$this->WP_Widget('bz-user-quick-links', __('User quick links','boozurk'), $widget_ops);
		$this->alt_option_name = 'bz_widget_user_quick_links';
	}

	function widget( $args, $instance ) {
		global $current_user;
		
		extract($args, EXTR_SKIP);
		
		$title = apply_filters('widget_title', $instance['title'], $instance, $this->id_base);
		$nick = ( isset($instance['nick']) && ( $instance['nick'] == 1 ) ) ? boozurk_random_nick() : __('guest','boozurk');
		$name = is_user_logged_in() ? $current_user->display_name : $nick;
		$title = sprintf ( $title, $name );
		
		$use_thumbs = ( !isset($instance['thumb']) || $thumb = (int) $instance['thumb'] ) ? 1 : 0;
		if ( $use_thumbs ) {
			if ( is_user_logged_in() ) { //fix for notice when user not log-in
				$email = $current_user->user_email;
				$title = get_avatar( $email, 32, $default = get_template_directory_uri() . '/images/user.png','user-avatar' ) . ' ' . $title;
			} else {
				$title = get_avatar( 'dummyemail', 32, $default=get_option('avatar_default') ) . ' ' . $title;
			}
		}
		
?>
		<?php echo $before_widget; ?>
		<?php if ( $title ) echo $before_title . $title . $after_title; ?>
		<ul>
			<?php if ( ! is_user_logged_in() || current_user_can( 'read' ) ) { wp_register(); }?>
			<?php if ( is_user_logged_in() ) { ?>
				<?php if ( current_user_can( 'read' ) ) { ?>
					<li><a href="<?php echo esc_url( admin_url( 'profile.php' ) ); ?>"><?php _e( 'Your Profile', 'boozurk' ); ?></a></li>
					<?php if ( current_user_can( 'publish_posts' ) ) { ?>
						<li><a title="<?php _e( 'Add New Post', 'boozurk' ); ?>" href="<?php echo esc_url( admin_url( 'post-new.php' ) ); ?>"><?php _e( 'Add New Post', 'boozurk' ); ?></a></li>
					<?php } ?>
					<?php if ( current_user_can( 'moderate_comments' ) ) {
						$awaiting_mod = wp_count_comments();
						$awaiting_mod = $awaiting_mod->moderated;
						$awaiting_mod = $awaiting_mod ? ' (' . number_format_i18n( $awaiting_mod ) . ')' : '';
					?>
						<li><a title="<?php _e( 'Comments', 'boozurk' ); ?>" href="<?php echo esc_url( admin_url( 'edit-comments.php' ) ); ?>"><?php _e( 'Comments', 'boozurk' ); ?></a><?php echo $awaiting_mod; ?></li>
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
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['thumb'] = (int) $new_instance['thumb'] ? 1 : 0;
		$instance['nick'] = (int) $new_instance['nick'] ? 1 : 0;
		return $instance;
	}

	function form( $instance ) {
		$title = ( isset($instance['title']) && !empty($instance['title']) ) ? esc_attr($instance['title']) : __('Welcome %s','boozurk');
		$nick = isset($instance['nick']) ? absint($instance['nick']) : 0;
		$thumb = isset($instance['thumb']) ? absint($instance['thumb']) : 1;
?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title','boozurk'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
			<small><?php _e('default: "Welcome %s" , where %s is the user name','boozurk');?></small>
		</p>
		<p>
			<input id="<?php echo $this->get_field_id('nick'); ?>" name="<?php echo $this->get_field_name('nick'); ?>" value="1" type="checkbox" <?php checked( 1 , $nick ); ?> />
			<label for="<?php echo $this->get_field_id('nick'); ?>"><?php _e('Create a random nick for not-logged users','boozurk'); ?></label>
		</p>
		<p>
			<input id="<?php echo $this->get_field_id('thumb'); ?>" name="<?php echo $this->get_field_name('thumb'); ?>" value="1" type="checkbox" <?php checked( 1 , $thumb ); ?> />
			<label for="<?php echo $this->get_field_id('thumb'); ?>"><?php _e('Show user gravatar','boozurk'); ?></label>
		</p>

<?php
	}
}

/**
 * Post share links
 */
class boozurk_Widget_share_this extends WP_Widget {

	function boozurk_Widget_share_this() {
		$widget_ops = array('classname' => 'bz_Widget_share_this', 'description' => __( "Show some popular sharing services links. It's visible ONLY in single posts, pages and attachments",'boozurk') );
		$this->WP_Widget('bz-share-this', __('Share this','boozurk'), $widget_ops);
		$this->alt_option_name = 'bz_Widget_share_this';

	}

	function widget($args, $instance) {
		if ( !is_singular() ) return;
		extract($args);
		
		$icon_size = isset($instance['icon_size']) ? absint($instance['icon_size']) : '16';

		$title = apply_filters('widget_title', $instance['title'], $instance, $this->id_base);
?>
		<?php echo $before_widget; ?>
		<?php if ( $title ) echo $before_title . $title . $after_title; ?>
		<?php boozurk_share_this( $icon_size ); ?>
		<?php echo $after_widget; ?>
<?php
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
        $instance["icon_size"] = in_array( $new_instance["icon_size"], array ('16', '24', '32', '48', '64') ) ? $new_instance["icon_size"] : '16' ;

		return $instance;
	}

	function form( $instance ) {
		$title = isset($instance['title']) ? esc_attr($instance['title']) : __('Share this','boozurk');
		$icon_size = isset($instance['icon_size']) ? absint($instance['icon_size']) : '16';
?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title','boozurk'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
		</p>
		<p>
            <label for="<?php echo $this->get_field_id('icon_size'); ?>"><?php _e('Select icon size', 'boozurk'); ?></label>
            <select name="<?php echo $this->get_field_name('icon_size'); ?>" id="<?php echo $this->get_field_id('icon_size'); ?>" >
<?php
            $size_array = array ('16', '24', '32', '48', '64');
            foreach($size_array as $size) {
?>
                <option value="<?php echo $size; ?>" <?php selected( $icon_size, $size ); ?>><?php echo $size; ?>px</option>
<?php
            }
?>
            </select>
		</p>

<?php
	}
}


/**
 * Register all of the default WordPress widgets on startup.
 */
function boozurk_widgets_init() {
	if ( !is_blog_installed() )
		return;

	register_widget('boozurk_widget_popular_posts');

	register_widget('boozurk_Widget_latest_Commented_Posts');
	
	register_widget('boozurk_widget_latest_commentators');
	
	register_widget('boozurk_Widget_pop_categories');
	
	register_widget('boozurk_Widget_social');
	
	register_widget('boozurk_Widget_besides');
	
	register_widget('boozurk_Widget_recent_posts');
	
	register_widget('boozurk_Widget_navbuttons');
	
	register_widget('boozurk_Widget_post_details');
	
	register_widget('boozurk_Widget_post_formats');
	
	register_widget('boozurk_Widget_image_EXIF');
	
	register_widget('boozurk_Widget_user_quick_links');
	
	register_widget('boozurk_Widget_share_this');
}

add_action('widgets_init', 'boozurk_widgets_init');

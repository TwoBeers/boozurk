<?php
/**
 * post-protected.php
 *
 * Template part file that contains the Protected entry
 * 
 * @package Boozurk
 * @since 1.00
 */
?>

<?php boozurk_hook_entry_before(); ?>

<div <?php post_class() ?> id="post-<?php the_ID(); ?>">

	<?php boozurk_extrainfo(); ?>

	<?php boozurk_hook_entry_top(); ?>

	<h2 class="storytitle"><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></h2>

	<div class="storycontent">
		<?php the_content(); ?>
	</div>

	<?php boozurk_hook_entry_bottom(); ?>

</div>

<?php boozurk_hook_entry_after(); ?>

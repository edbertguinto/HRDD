<?php
/**
 * The default template for displaying content
 *
 * @package WordPress
 * @subpackage MxS2
 */
?>

	<div <?php post_class(); ?>>
		<a href="<?php the_permalink() ?>" class="formatlink"><span class="formatlabel"><?php _e('quote', 'MxS') ?></span></a>
		<div class="clear"></div>
		<h2 class="entry-title"><a href="<?php the_permalink() ?>"  title="<?php the_title_attribute() ; ?>" rel="bookmark"><?php the_title_attribute(); ?></a></h2>
		<div class="indexentry">
			<?php the_content(''); ?>
		</div>
		<div class="clear"></div>
		<?php wp_link_pages(); ?>
		<div class="more-read"><a href="<?php the_permalink() ?>" class="more-link" title="<?php _e('read more', 'MxS') ?>"><span><?php _e('read more', 'MxS') ?></span>&#8594;</a></div>
	</div>

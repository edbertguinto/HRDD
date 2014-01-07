<?php
/**
 * The template for displaying content
 */
?>

		<div <?php post_class(); ?>>
			<h2 class="entry-title"><a href="<?php the_permalink() ?>"  title="<?php the_title_attribute() ; ?>" rel="bookmark"><?php the_title_attribute(); ?></a></h2>
			<div class="info">
				<span class="postinfo"><a href="<?php the_permalink() ?>"><?php the_time( get_option( 'date_format' )); ?></a><span class="separator">&#183;</span></span>
				<span class="postinfo"><?php echo __('Category', 'MxS') ,' :'; the_category(', ') ?><span class="separator">&#183;</span></span>
				<span class="postinfo"><?php if (has_tag()) : _e('tags :', 'MxS');  the_tags(' '); endif; ?><span class="separator">&#183;</span></span>					
				<span class="postinfo"><?php comments_popup_link(); ?></span>
				<?php edit_post_link(__(' Edit', 'MxS'), '<span class="postinfo"><span class="separator">&#183;</span>', '</span>'); ?>
			</div>
			<div class="clear"></div>
			<div class="indexentry">
					<?php if ( has_post_thumbnail() ){ ?>
					<a href="<?php the_permalink() ?>" class="post-thumb">
					<?php the_post_thumbnail(); ?></a>
				<?php }  ?>
				<?php $mxs_options = get_option('MxS_theme_options'); ?>
				<?php if ( $mxs_options['home_excerpt_check']== 1 ) { the_excerpt(__('&raquo; Read more','MxS')); } else {the_content(__( 'Read More','MxS' ));} ?>
			</div>
			<div class="clear"></div>
			<?php wp_link_pages('before=<div class="pages">' . __('Pages:', 'MxS').'&after=</div>'); ?>
			<div class="more-read"><a href="<?php the_permalink() ?>" class="more-link" title="<?php _e('read more', 'MxS') ?>"><span><?php _e('read more', 'MxS') ?></span>&#8594;</a></div>
		</div>

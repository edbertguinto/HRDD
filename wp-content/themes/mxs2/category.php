<?php get_header(); ?>
	<div id="main">
		<h1 class="entry-title"><?php echo single_cat_title(); ?><?php _e(' Archive', 'MxS'); ?></h1>
		<div class="section entry" id="entry<?php the_ID(); ?>">
			<?php if(have_posts()) : ?><?php while(have_posts()) : the_post(); ?>
			<div class="post">
				<h2 class="entry-title"><a href="<?php the_permalink() ?>"><?php the_title_attribute(); ?></a></h2>
				<div class="info">
					<span class="postinfo"><a href="<?php the_permalink() ?>"><?php the_time( get_option( 'date_format' )); ?></a><span class="separator">&#183;</span></span>
					<span class="postinfo"><?php if (has_tag())  : _e('tags :', 'MxS');  the_tags(' '); endif; ?><span class="separator">&#183;</span></span>
					<span class="postinfo"><?php comments_popup_link(); ?></span>
				</div>
				<div class="entry">
					<?php $mxs_options = get_option('MxS_theme_options'); ?>
					<?php if ( $mxs_options['arch_excerpt_check']== 1 ) { the_excerpt(); } else {the_content();} ?>
				</div>
			</div>
			<?php endwhile; ?>
			<?php else : ?>	
				<div id="main">
				<h2><?php _e('Not Found', 'MxS'); ?></h2>
				<?php get_search_form(); ?>
				</div>
			<?php endif; ?>	
		</div><!-- section entry -->
		<?php if (  $wp_query->max_num_pages > 1 ) : ?>
				<div class="page_navi"><?php MxS_pagenavi(5); ?></div>
		<?php endif;  ?><!-- page navi -->
		</div><!-- main -->
	<?php	get_sidebar(); ?>
	<?php	get_footer(); ?>
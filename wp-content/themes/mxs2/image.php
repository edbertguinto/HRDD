<?php
/**
 * The template for displaying image attachments.
 *
 * @package WordPress
 * @subpackage MxS2
 */
get_header(); ?>
	<div id="main">
		<ul id="flip1">
		<li class="newer"><?php previous_image_link('%link', __('&laquo; Previous', 'MxS')); ?> </li>
		<li class="older">|<?php next_image_link('%link', __('Next &raquo; ', 'MxS')); ?> </li>
		</ul>
	<div class="section entry" id="entry<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php while(have_posts()) : the_post(); ?>
		<div class="post">
			<h1 class="entry-title"><?php the_title_attribute(); ?></h1>
				<div class="singleinfo">
				<span class="dateinfo"><?php _e('Posted on ', 'MxS'); the_time( get_option( 'date_format' )); ?> | <?php echo __('Category', 'MxS') ,' :'; the_category(', ') ?> | <?php comments_popup_link(); ?></span>
				</div><div class="clear"></div>
			<div class="entry">
<?php
	/**
	 * Grab the IDs of all the image attachments in a gallery so we can get the URL of the next adjacent image in a gallery,
	 * or the first image (if we're looking at the last image in a gallery), or, in a gallery of one, just the link to that image file
	 */
	$attachments = array_values( get_children( array( 'post_parent' => $post->post_parent, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => 'ASC', 'orderby' => 'menu_order ID' ) ) );
	foreach ( $attachments as $k => $attachment ) {
		if ( $attachment->ID == $post->ID )
			break;
	}
	$k++;
	// If there is more than 1 attachment in a gallery
	if ( count( $attachments ) > 1 ) {
		if ( isset( $attachments[ $k ] ) )
			// get the URL of the next image attachment
			$next_attachment_url = get_attachment_link( $attachments[ $k ]->ID );
		else
			// or get the URL of the first image attachment
			$next_attachment_url = get_attachment_link( $attachments[ 0 ]->ID );
	} else {
		// or, if there's only 1 image, get the URL of the image
		$next_attachment_url = wp_get_attachment_url();
	}
?>
								<a href="<?php echo esc_url( $next_attachment_url ); ?>" title="<?php echo esc_attr( get_the_title() ); ?>" rel="attachment"><?php
								$attachment_size = apply_filters( 'twentyeleven_attachment_size', 848 );
								echo wp_get_attachment_image( $post->ID, array( $attachment_size, 1024 ) ); // filterable image width with 1024px limit for image height.
								?></a>

								<?php if ( ! empty( $post->post_excerpt ) ) : ?>
								<div class="entry-caption">
									<?php the_excerpt(); ?>
								</div>
								<?php endif; ?>			
			</div>
			<div class="clear"></div>
				<?php wp_link_pages(); ?>

		</div>
			<?php endwhile; ?>
		<div class="related">
			<?php if(function_exists('related_posts')) {  related_posts('number=10&include_page=false&order=count-desc'); } ?>
		</div>
			<div class="flip">
	<div class="prevpost"><?php previous_post_link(); ?></div>
	<div class="nextpost"><?php next_post_link(); ?></div>
	</div>
<?php comments_template(); ?>
	</div><!-- section entry -->
	</div><!-- main -->
	<?php	get_sidebar(); ?>	
	<?php	get_footer(); ?>
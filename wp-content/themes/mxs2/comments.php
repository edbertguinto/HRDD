<?php
/**
 * @package WordPress
 * @subpackage MxS
 */


	if ( post_password_required() ) { ?>
		<p class="nocomments"><?php _e('This post is password protected. Enter the password to view comments.', 'MxS') ?></p>
	<?php
		return;
	}
	$oddcomment = 'alt';
	/* to split comment and pings */
	$trackpingCount = 0;
	$commentCount = 0;	
	if ($comments) :
		foreach ($comments as $comment) {
			$type = get_comment_type();
			switch( $type ) {
				case 'trackback' :
				case 'pingback' :
					$trackpingArray[$trackpingCount++] = $comment;
					break;
				default :
					$commentArray[$commentCount++] = $comment;
			}
		}
	endif;
?>

<!-- You can start editing here. -->
	<div id="comments">
	<?php if ('open' == $post->comment_status) {
		echo '<h2 id="commentsx">';
		_e('Comments ', 'MxS');
		echo $commentCount; 
		echo '</h2>';
		}?>
	
		<?php if ( have_comments() ) : ?>

		<div class="page_navi">
			<?php paginate_comments_links(); ?> 
		</div>
		
		<ol class="commentlist">
		<?php wp_list_comments('type=comment&callback=MxS_custom_comments'); ?>
		</ol>
	 <?php else : // this is displayed if there are no comments so far ?>

		<?php if ( comments_open() ) : ?>
			<!-- If comments are open, but there are no comments. -->

		 <?php else : // comments are closed ?>
			<!-- If comments are closed. -->
			<p class="nocomments"><?php _e('Comments are closed.', 'MxS'); ?></p>

		<?php endif; ?>
		<?php endif; ?>

<?php comment_form(); ?>
	</div>
<div class="clear"></div>
<?php if ($trackpingCount > 0 ) : //display trackback?>
			<div class="section" id="trackback">
				<h2><?php _e('Pingbacks ', 'MxS'); ?><span class="count"><?php echo $trackpingCount; ?></span></h2>
				<dl class="log">
				<?php wp_list_comments('type=pings&callback=MxS_custom_pingbacks'); ?>
				</dl>
			</div>
<?php endif; // end of trackback ?>

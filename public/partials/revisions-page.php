<?php

if ( ! defined( 'ABSPATH' ) )
	exit;

get_header();
?>
<?php if (get_option('blog_public') == '1' || (get_option('blog_public') == '0' && current_user_can_for_blog($blog_id, 'read'))): ?>
	<div id="Revisions">
        <h1 class="page-title"><?php _e('Revisions', 'pb-revisions'); ?></h1>
        <div class="entry-content">
            <?php echo do_shortcode( '[revisions/]' );?>
        </div>
	</div>
<?php else: ?>
	<?php pb_private(); ?>
<?php endif; ?>
<?php get_footer(); ?>
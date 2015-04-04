<?php
/**
 * Template for single portfolio view
 * @package themify
 * @since 1.0.0
 */
?>

<?php get_header(); ?>

<?php
/** Themify Default Variables
 *  @var object */
global $themify, $themify_portfolio; ?>

<?php if( have_posts() ) while ( have_posts() ) : the_post(); ?>

	<div class="featured-area <?php echo themify_theme_featured_area_style(); ?>">

	<?php if ( $themify->hide_image != 'yes' ) : ?>

		<?php themify_before_post_image(); // Hook ?>

		<?php
		///////////// GALLERY //////////////////////
		if ( $images = $themify_portfolio->get_gallery_images() ) : ?>
			<?php
			// Find out the number of columns in shortcode
			$columns = $themify_portfolio->get_gallery_columns();

			// Count how many images we really have
			$n_images = count( $images );
			if ( $columns > $n_images ) {
				$columns = $n_images;
			}
			?>
			<div class="gallery-wrapper masonry clearfix <?php echo "gallery-columns-$columns"; ?>">
				<div class="grid-sizer"></div>
				<div class="gutter-sizer"></div>
				<?php
				$counter = 0; ?>

				<?php foreach ( $images as $image ) :
					$counter++;

					$caption = $themify_portfolio->get_caption( $image );
					$description = $themify_portfolio->get_description( $image );
					if ( $caption ) {
						$alt = $caption;
					} elseif ( $description ) {
						$alt = $description;
					} else {
						$alt = the_title_attribute('echo=0');
					}
					$featured = get_post_meta( $image->ID, 'themify_gallery_featured', true );
					if ( $featured && '' != $featured ) {
						$img_size = array(
							'width' => 474,
							'height' => 542,
						);
					} else {
						$img_size = array(
							'width' => 474,
							'height' => 271,
						);
					}

					if ( themify_check( 'setting-img_settings_use' ) ) {
						$size = $featured && '' != $featured ? 'large' : 'medium';
						$img = wp_get_attachment_image_src( $image->ID, apply_filters( 'themify_gallery_post_type_single', $size ) );
						$out_image = '<img src="' . $img[0] . '" alt="' . $alt . '" width="' . $img_size['width'] . '" height="' . $img_size['height'] . '" />';

					} else {
						$img = wp_get_attachment_image_src( $image->ID, apply_filters( 'themify_gallery_post_type_single', 'large' ) );
						$out_image = themify_get_image( "src={$img[0]}&w={$img_size['width']}&h={$img_size['height']}&ignore=true&alt=$alt" );
					}

					?>
					<div class="item gallery-icon <?php echo $featured; ?>">
						<a href="<?php echo $img[0]; ?>" class="" data-image="<?php echo $img[0]; ?>" data-caption="<?php echo $caption; ?>" data-description="<?php echo $description; ?>">
							<span class="gallery-item-wrapper">
								<?php echo $out_image; ?>
								<?php if ( $caption ) : ?>
									<span class="gallery-caption"><?php echo $caption; ?></span>
								<?php endif; // caption ?>
							</span>
						</a>
					</div>
				<?php endforeach; // images as image ?>
			</div>

		<?php
		///////////// SINGLE IMAGE //////////////////////
		elseif( $post_image = themify_get_image($themify->auto_featured_image . $themify->image_setting . "w=".$themify->width."&h=".$themify->height) ) : ?>

			<figure class="post-image <?php echo $themify->image_align; ?>">
				<?php if( 'yes' == $themify->unlink_image): ?>
					<?php echo $post_image; ?>
				<?php else: ?>
					<a href="<?php echo themify_get_featured_image_link(); ?>"><?php echo $post_image; ?><?php themify_zoom_icon(); ?></a>
				<?php endif; // unlink image ?>
			</figure>

		<?php endif; // video else image ?>

		<?php themify_after_post_image(); // Hook ?>

	<?php endif; // hide image ?>

	</div>

<!-- layout-container -->
<div id="layout" class="pagewidth clearfix">

	<?php themify_content_before(); // hook ?>
	
	<!-- content -->
	<div id="content" class="list-post">
    	<?php themify_content_start(); // hook ?>

		<?php get_template_part( 'includes/loop-portfolio', 'single' ); ?>

		<?php wp_link_pages(array('before' => '<p class="post-pagination"><strong>' . __('Pages:', 'themify') . ' </strong>', 'after' => '</p>', 'next_or_number' => 'number')); ?>
		
		<?php get_template_part( 'includes/author-box', 'single'); ?>
		
		<?php get_template_part( 'includes/post-nav', 'portfolio'); ?>
		
		<?php if(!themify_check('setting-comments_posts')): ?>
			<?php comments_template(); ?>
		<?php endif; ?>
		
        <?php themify_content_end(); // hook ?>
	</div>
	<!-- /content -->

    <?php themify_content_after(); // hook ?>

<?php endwhile; ?>

<?php 
/////////////////////////////////////////////
// Sidebar							
/////////////////////////////////////////////
if ($themify->layout != "sidebar-none"): get_sidebar(); endif; ?>

</div>
<!-- /layout-container -->
	
<?php get_footer(); ?>
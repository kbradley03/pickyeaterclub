<?php if(!is_single()) { global $more; $more = 0; } //enable more link ?>
<?php
/** Themify Default Variables
 *  @var object */
global $themify; ?>

<?php themify_post_before(); //hook ?>

<?php 
$categories = wp_get_object_terms(get_the_ID(), 'portfolio-category');
$class = '';
foreach($categories as $cat){
	$class .= ' cat-'.$cat->term_id;
}
?>

<article itemscope itemtype="http://schema.org/Article" id="portfolio-<?php the_ID(); ?>" class="<?php echo implode(' ', get_post_class('post clearfix portfolio-post' . $class)); ?>">

	<?php if( ( ! is_single() && $themify->hide_image != 'yes' ) || ( isset( $themify->is_builder_portfolio_loop ) && $themify->is_builder_portfolio_loop && $themify->hide_image != 'yes' ) ) : ?>

		<?php get_template_part( 'includes/post-media', get_post_type() ); ?>

	<?php endif //hide image ?>

	<?php if ( $themify->hide_meta != 'yes' ): ?>
		<p class="post-meta entry-meta">
			<?php the_terms( get_the_ID(), get_post_type() . '-category', '<span class="post-category">', ' <span class="separator">/</span> ', ' </span>' ) ?>
		</p>
	<?php endif; //post meta ?>

	<?php if ( $themify->hide_title != 'yes' ): ?>
		<h2 class="post-title entry-title" itemprop="name">
			<?php if ( $themify->unlink_title == 'yes' ): ?>
				<?php the_title(); ?>
			<?php else: ?>
				<a href="<?php echo themify_get_featured_image_link(); ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a>
			<?php endif; //unlink post title ?>
		</h2>
	<?php endif; //post title ?>

	<?php if ( $themify->hide_date != 'yes' ): ?>
		<div class="author-meta-box clearfix">
			<time datetime="<?php the_time( 'o-m-d' ) ?>" class="post-date entry-date updated" itemprop="datePublished"><?php echo get_the_date( apply_filters( 'themify_loop_date', '' ) ) ?></time>
		</div>
	<?php endif; //post date ?>

	<div class="post-content">

		<div class="entry-content" itemprop="articleBody">

			<?php if ( 'excerpt' == $themify->display_content && ! is_attachment() ) : ?>

				<?php the_excerpt(); ?>

			<?php elseif ( 'none' == $themify->display_content && ! is_attachment() ) : ?>

			<?php else: ?>

				<?php the_content(themify_check('setting-default_more_text')? themify_get('setting-default_more_text') : __('More &rarr;', 'themify')); ?>

			<?php endif; //display content ?>

		</div><!-- /.entry-content -->

		<?php edit_post_link(__('Edit', 'themify'), '<span class="edit-button">[', ']</span>'); ?>

	</div>
	<!-- /.post-content -->
</article>
<!-- /.post -->

<?php themify_post_after(); //hook ?>
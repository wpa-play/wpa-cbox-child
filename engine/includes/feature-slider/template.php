<?php
/**
 * Template Name: Features or Category Slider
 *
 * This template either displays Slides taken from the "Features" custom post type.
 * Or Loops through posts from a certain category. This is based on the theme options set by
 * the user.
 *
 * @author Bowe Frankema <bowe@presscrew.com>
 * @link http://infinity.presscrew.com/
 * @copyright Copyright (C) 2010-2011 Bowe Frankema
 * @license http://www.gnu.org/licenses/gpl.html GPLv2 or later
 * @package Infinity
 * @since 1.0
 */
?>
<?php

// slider type
$slider_type = (int) infinity_option_get( 'cbox_flex_slider' );

// locate no slides image url
$no_slides_url  = infinity_image_url( 'slides-bg.png' );
$no_slider_text = '';

// setup slider query args
$query_args = array();

$query_args['order'] = 'ASC';

$posts_per_page = infinity_option_get( 'cbox_flex_slider_amount' );
if ( ! empty( $posts_per_page ) ) {
	$query_args['posts_per_page'] = (int) infinity_option_get( 'cbox_flex_slider_amount' );
} else {
	$query_args['posts_per_page'] = '-1';
}

// site features
if ( $slider_type == 1 ) {
	$query_args['post_type'] = 'features';

	$no_slider_text = __( 'Did you know you can easily add introduction slides to your homepage? Simply visit your admin panel and add a new <strong>Site Feature</strong>.', 'cbox-theme' );
}

// post category
if ( $slider_type == 2 ) {
	$cat_id = infinity_option_get( 'cbox_flex_slider_category' );
	$cat    = get_category( $cat_id );

	$query_args['cat'] = $cat_id;

	$no_slider_text = sprintf( __( 'Did you know you can easily add introduction slides to your homepage? Simply visit your admin panel and add a new post in the <strong>%s</strong> category.', 'cbox-theme' ), $cat->name );
}

// setup the slider query
$slider_query = new WP_Query( $query_args );

?>

<div class="flex-container">
	<div class="flexslider">
	  	<ul class="slides">


<?php
if( $slider_query->have_posts() ) :
	while( $slider_query->have_posts() ) :
		$slider_query->the_post();
		$image = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'slider-image');
?>
		<!-- Loop through slides  -->
		<?php if( has_post_thumbnail() ) :?>
		<li>
			<!-- Image -->
			<a href="<?php the_permalink(); ?>">
				<img src="<?php echo $image[0]; ?>" class="attachment-nivothumb wp-post-image" title="<?php the_title_attribute(); ?>" alt="<?php the_title_attribute(); ?>" />
			</a>

			<!-- Caption -->
			<div class="flex-caption">
				<h3>
					<a href="<?php the_permalink(); ?>">
						<?php the_title_attribute();?>
					</a>
				</h3>
				
				<!-- use "get_the_excerpt() (prev. get_the_content()) function 
				      to make slider text more managable -->
				<?php echo apply_filters( 'the_content', cbox_create_excerpt( get_the_excerpt() ) );?>
			</div>

		</li>

		<!-- Fallback to default slide if no features are present -->
		<?php else :?>

		<li>
			<img src="<?php echo $no_slides_url ?>" width="589" height="319" alt="" style="height:319px;" />

			<div class="flex-caption">
				<h3><?php _e( 'No slides added yet!', 'cbox-theme' ); ?></h3>
				<p><?php echo $no_slider_text; ?></p>
			</div>
		</li>
		<?php endif;?>

	<?php endwhile;

else:
?>

		<!-- Fallback to default slide if no features are present -->
		<li>
			<img src="<?php echo $no_slides_url ?>" width="589" height="319" alt="" style="height:319px;" />

			<div class="flex-caption">
				<h3><?php _e( 'No slides added yet!', 'cbox-theme' ); ?></h3>
				<p><?php echo $no_slider_text; ?></p>
			</div>
		</li>

<?php endif; ?>

		</ul>
	</div>
</div>

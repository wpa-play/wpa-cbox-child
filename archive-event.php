<?php /*
--------------------------------------------------------------------------------
Copy of EO template file for WPA
--------------------------------------------------------------------------------
*/

infinity_get_header();

?>

<!-- archive-event.php  -->
<div id="content" role="main" class="column twelve sidebar-left">

	<!-- Page header-->
	<header class="page-header">
	<h1 class="page-title">
		<?php
			if( eo_is_event_archive('day') )
				//Viewing date archive
				echo __('Events: ','eventorganiser').' '.eo_get_event_archive_date('jS F Y');
			elseif( eo_is_event_archive('month') )
				//Viewing month archive
				echo __('Events: ','eventorganiser').' '.eo_get_event_archive_date('F Y');
			elseif( eo_is_event_archive('year') )
				//Viewing year archive
				echo __('Events: ','eventorganiser').' '.eo_get_event_archive_date('Y');
			elseif( 'upcoming-events' == get_query_var('wpa_cbox') )
				//Viewing upcoming events archive
				echo __('Upcoming Events','eventorganiser');
			else
				_e('Events','eventorganiser');
		?>
		</h1>
	</header>

	<?php if ( have_posts() ) : ?>

		<!-- Navigate between pages-->
		<!-- In TwentyEleven theme this is done by twentyeleven_content_nav-->
		<?php 
		global $wp_query;
		if ( $wp_query->max_num_pages > 1 ) : ?>
			<nav id="nav-above" class="clearfix">
				<div class="nav-next events-nav-newer"><?php next_posts_link( __( 'Later events <span class="meta-nav">&rarr;</span>' , 'eventorganiser' ) ); ?></div>
				<div class="nav-previous events-nav-newer"><?php previous_posts_link( __( ' <span class="meta-nav">&larr;</span> Newer events', 'eventorganiser' ) ); ?></div>
			</nav><!-- #nav-above -->
		<?php endif; ?>

		<?php /* Start the Loop */ ?>

		<div class="event-loop">

			<?php while ( have_posts() ) : the_post(); ?>

			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<header class="entry-header">

				<h1 class="entry-title" style="display: inline;">			
				<?php
					//If it has one, display the thumbnail
					the_post_thumbnail('thumbnail', array('style'=>'float:left;margin-right:20px;'));
				?>
				<a href="<?php the_permalink(); ?>"><?php the_title();?></a>
				</h1>

				<div class="event-entry-meta">

					<!-- Output the date of the occurrence-->
					<?php
						//Format date/time according to whether its an all day event.
						//Use microdata http://support.google.com/webmasters/bin/answer.py?hl=en&answer=176035
						if( eo_is_all_day() ){
							$format = 'd F Y';
							$microformat = 'Y-m-d';
						}else{
							$format = 'd F Y '.get_option('time_format');
							$microformat = 'c';
						}?>
						<time itemprop="startDate" datetime="<?php eo_the_start($microformat); ?>"><?php eo_the_start($format); ?></time>

					<!-- Display event meta list -->
					<?php echo eo_get_event_meta_list(); ?>

					<!-- Display event CiviCRM data -->
					<?php echo wpa_cbox_get_event_meta_list(); ?>

					<!-- Event excerpt -->
					<?php the_excerpt(); ?>
		
				</div><!-- .event-entry-meta -->			
	
				<div style="clear:both;"></div>
				</header><!-- .entry-header -->

			</article><!-- #post-<?php the_ID(); ?> -->


			<?php endwhile; ?><!--The Loop ends-->
			
		</div><!-- /.event-loop -->

	<!-- Navigate between pages-->
	<?php 
		if ( $wp_query->max_num_pages > 1 ) : ?>
			<nav id="nav-below" class="clearfix">
				<div class="nav-next events-nav-newer"><?php next_posts_link( __( 'Later events <span class="meta-nav">&rarr;</span>' , 'eventorganiser' ) ); ?></div>
				<div class="nav-previous events-nav-newer"><?php previous_posts_link( __( ' <span class="meta-nav">&larr;</span> Newer events', 'eventorganiser' ) ); ?></div>
			</nav><!-- #nav-below -->
		<?php endif; ?>

	<?php else : ?>
		<!-- If there are no events -->
		<article id="post-0" class="post no-results not-found">
			<header class="entry-header">
				<h1 class="entry-title"><?php _e( 'Nothing Found', 'eventorganiser' ); ?></h1>
			</header><!-- .entry-header -->

			<div class="entry-content">
				<p><?php _e( 'Apologies, but no results were found for the requested archive. ', 'eventorganiser' ); ?></p>
			</div><!-- .entry-content -->
		</article><!-- #post-0 -->

		<?php endif; ?>

		</div><!-- #content -->

<!-- Call template sidebar and footer -->
<?php get_sidebar(); ?>
<?php get_footer(); ?>

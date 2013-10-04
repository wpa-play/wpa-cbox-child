<?php
/**
 * Template Name: Full Calendar Page
 */

	infinity_get_header();
?>
	<div id="content-full" class="grid_24" role="main">
		<?php
			do_action( 'open_content' );
			do_action( 'open_page' );
		?>
		<?php
			
			// bust transient cache
			_eventorganiser_delete_calendar_cache();
			
			infinity_get_template_part( 'templates/loops/loop', 'page' );
		?>	
		<?php
			do_action( 'close_page' );
			do_action( 'close_content' );
		?>
	</div>

<?php
	infinity_get_footer();
?>

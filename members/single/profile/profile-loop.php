<?php if ( bp_has_profile() ) : ?>

	<?php while ( bp_profile_groups() ) : bp_the_profile_group(); ?>
		
		<?php if ( bp_profile_group_has_fields() ) : ?>

			<div class="post">

			<?php 
			
			while ( bp_profile_fields() ) : bp_the_profile_field(); 
				
				if ( 'Name' == bp_get_the_profile_field_name() ) continue;
				
				if ( 'WPA Role' == bp_get_the_profile_field_name() ) {
					
					?>
					
					<h3 class="page-title">WPA <?php bp_the_profile_field_value(); ?></h3>
					
					<?php
					
				} else {
				
				?>

				<h2 class="page-title"><?php bp_the_profile_field_name(); ?></h2>
				
				<?php bp_the_profile_field_value(); ?>

				<?php 
			
				}
			
			endwhile; ?>

			</div>

		<?php endif; ?>
		
	<?php endwhile; ?>

<?php endif; ?>

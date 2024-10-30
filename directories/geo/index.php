<?php get_header(); ?>

	<?php do_action( 'bp_before_directory_geo_content' ) ?>		

	<div id="content">

		<div class="page" id="geo-directory-page">

				<div id="members-directory-listing" class="directory-widget">
					<h3>
						<?php _e( 'Member Listing', 'buddypress' ) ?>
						<?php geo_profiles_friendly_search_type( ' - ' ); ?>
					</h3>

					<div id="member-dir-list">
						<?php locate_template( array( 'directories/geo/members-loop.php' ), true ) ?>			
					</div>

				</div>

		</div>
	</div>

	<div id="sidebar" class="geo-sidebar">
		<div id="geo-directory-search" class="directory-widget">
			<h3><?php _e( 'Find Members', 'buddypress' ) ?></h3>
			
			<?php geo_profiles_get_search_form(); ?>
			
			<?php do_action( 'bp_directory_geo_search' ) ?>
		</div>	
	</div>

<?php get_footer(); ?>

<?php if ( function_exists( 'geo_profiles_has_items' ) ) { ?>
	<?php 
		$type = "distance"; 
		if ( isset( $_GET['type'] ) ) $type = $_GET['type']; 
		$within = BP_GEO_DEFAULT_SEARCH;
		if ( isset( $_GET['within'] ) ) $within = (int)$_GET['within'];
	?>
	<?php if ( geo_profiles_has_items( 'type=' . $type . '&within=' .  $within ) ) { ?>
	
		<div class="pagination">
			
			<div class="pag-count" id="member-geo-count">
				<?php geo_profiles_pagination_count() ?>
			</div>
	
			<div class="pagination-links" id="member-geo-pag">
				<?php geo_profiles_pagination_links() ?>
			</div>
	
		</div>	
		
								
		<?php if ( geo_profiles_items() ) { ?>
										
			<ul id="members-list" class="item-list geo">

			<?php while ( geo_profiles_items() ) { ?>
				<?php geo_profiles_the_item(); ?>
				
				<li>
					<div class="item-avatar">
						<a href="<?php geo_profiles_user_link() ?>"><?php geo_profiles_the_member_avatar() ?></a>
					</div>
		
					<div class="item">
						<?php do_action( 'bp_directory_geo_item_pre_name' ) ?>
						
						<div class="geo-info">
							<?php do_action( 'bp_directory_geo_member_pre_info' ) ?>
							
							<?php geo_member_info(); ?>
							
							<?php do_action( 'bp_directory_geo_member_post_info' ) ?>
						</div>
					
						<div class="left-area">
							<div class="item-title"><a href="<?php geo_profiles_user_link() ?>"><?php geo_profiles_the_site_member_name() ?></a></div>
							
							<?php do_action( 'bp_directory_geo_item_post_name' ) ?>
							
							<div class="item-meta">
								<h4><?php geo_member_location(); ?></h4>
								<?php echo geo_profiles_miles_or_kms( geo_profiles_get_member_distance() ); ?>
							</div>
						</div>
		
						<?php do_action( 'bp_directory_geo_item' ) ?>
					</div>
		
					<div class="action">
						<?php do_action( 'bp_directory_geo_actions' ) ?>
					</div>
		
					<div class="clear"></div>
				</li>
				
			<?php } ?>
																			
			</ul>
			
		<?php } ?>
	<?php } else { ?>		
		<div id="message" class="info">
			<p><?php _e( 'No members found.', 'buddypress' ) ?></p>
		</div>
	<?php } ?>			
<?php } ?>		
<?php /*
================================================================================
Custom functionality for Thoreau CBOX Child Theme.
================================================================================
AUTHOR: Christian Wach <needle@haystack.co.uk>
--------------------------------------------------------------------------------
NOTES
=====

--------------------------------------------------------------------------------
*/



// bootstrap Infinity engine
require_once( 'engine/includes/custom.php' );



/** 
 * @description: enable TinyMCE editor in bbPress
 */
function wpa_cbox_enable_visual_editor( $args = array() ) {

	// add TinyMCE
	$args['tinymce'] = true;
	
	// --<
	return $args;
	
}

// add filter for the above
add_filter( 'bbp_after_get_the_content_parse_args', 'wpa_cbox_enable_visual_editor' );



/**
 * Infinity developer mode. 
 * Developer mode will refresh the dynamic.css on every page load.
 */

// if Christian's local install
if ( $_SERVER['HTTP_HOST'] == 'dev.wpa-play.cmw' ) {
	define( 'INFINITY_DEV_MODE', true );
}

// if WPA server DEV install
if ( $_SERVER['HTTP_HOST'] == 'dev.wpa-play.com' ) {
	define( 'INFINITY_DEV_MODE', true );
}



/**
 * Show the BP Group hierarchy
 */
function wpa_cbox_get_trail() {
	$bread = bp_group_hierarchy_get_breadcrumbs( '&rarr;' );
	echo '<p class="group-hierarchy-breadcrumb"><a href="/groups/">Groups</a> &rarr; '.$bread.'</p>';
}

add_filter( 'bp_before_group_header_meta', 'wpa_cbox_get_trail' );



/**
 * Below is a clone of the BP Group Hierarchy widget for use by the WPA theme
 */
if ( function_exists( 'bp_has_groups_hierarchy' ) ) {

/* Register widgets for groups component */
add_action( 'widgets_init', 'wpa_cbox_init_widgets' );

function wpa_cbox_init_widgets() {
	register_widget( 'WPA_Toplevel_Groups_Widget');
}

/*** TOPLEVEL GROUPS WIDGET *****************/
class WPA_Toplevel_Groups_Widget extends WP_Widget {
	function wpa_toplevel_groups_widget() {
		parent::WP_Widget( false, $name = __( 'WPA Top-level Groups', 'wpa-cbox' ), array( 'description' => __( 'A list of top-level WPA BuddyPress groups', 'wpa-cbox' ) ) );
	}

	function widget($args, $instance) {
		global $bp;

	    extract( $args );

		echo $before_widget;
		echo $before_title
		   . $instance['title']
		   . $after_title; ?>
		<?php if( ! class_exists('BP_Groups_Group') ) {
			 _e( 'You must enable Groups component to use this widget.', 'wpa-cbox' );
			 return; 
		} ?>
		<?php if ( bp_has_groups_hierarchy( 'type=' . $instance['sort_type'] . '&per_page=' . $instance['max_groups'] . '&max=' . $instance['max_groups'] . '&parent_id=0' ) ) : ?>

			<ul id="toplevel-groups-list" class="item-list clearfix">
				<?php while ( bp_groups() ) : bp_the_group(); ?>
					<li>
						<div class="item-avatar">
							<a href="<?php bp_group_permalink() ?>"><?php bp_group_avatar( 'type=full&width=150&height=150' ); ?></a>
						</div>

						<div class="item">
							<div class="item-title"><a href="<?php bp_group_permalink() ?>" title="<?php echo strip_tags(bp_get_group_description_excerpt()) ?>"><?php bp_group_name() ?></a></div>
							<div class="item-meta"><span class="activity">
								<?php switch($instance['sort_type']) {
										case 'newest':
											printf( __( 'created %s', 'buddypress' ), bp_get_group_date_created() );
											break;
										case 'alphabetical':
										case 'active':
											printf( __( 'active %s', 'buddypress' ), bp_get_group_last_active() );
											break;
										case 'popular':
											bp_group_member_count();
											break;
										case 'prolific':
											printf( _n( '%d member group', '%d member groups', bp_group_hierarchy_has_subgroups(), 'wpa-cbox'), bp_group_hierarchy_has_subgroups() );
									}
										
								?>
							</span></div>
							<?php if($instance['show_desc']) { ?>
							<div class="item-desc"><?php bp_group_description_excerpt() ?></div>
							<?php } ?>
						</div>
					</li>

				<?php endwhile; ?>
			</ul>
			<?php wp_nonce_field( 'groups_widget_groups_list', '_wpnonce-groups' ); ?>
			<input type="hidden" name="toplevel_groups_widget_max" id="toplevel_groups_widget_max" value="<?php echo esc_attr( $instance['max_groups'] ); ?>" />

		<?php else: ?>

			<div class="widget-error">
				<?php _e('There are no groups to display.', 'buddypress') ?>
			</div>

		<?php endif; ?>

		<?php echo $after_widget; ?>
		<?php
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['max_groups'] = strip_tags( $new_instance['max_groups'] );
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['sort_type'] = strip_tags( $new_instance['sort_type'] );
		$instance['show_desc'] = isset($new_instance['show_desc']) ? true : false;

		return $instance;
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'max_groups' => 5, 'title'	=> __('Groups'), 'sort_type' => 'active' ) );
		$max_groups = strip_tags( $instance['max_groups'] );
		$title = strip_tags( $instance['title'] );
		$sort_type = strip_tags( $instance['sort_type'] );
		$show_desc = $instance['show_desc'] ? true : false;
		?>

		<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', 'buddypress'); ?> <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" /></label></p>
		<p><label for="<?php echo $this->get_field_id( 'max_groups' ); ?>"><?php _e('Max groups to show:', 'buddypress'); ?> <input class="widefat" id="<?php echo $this->get_field_id( 'max_groups' ); ?>" name="<?php echo $this->get_field_name( 'max_groups' ); ?>" type="text" value="<?php echo esc_attr( $max_groups ); ?>" style="width: 30%" /></label></p>
		<p>
			<label for="<?php echo $this->get_field_id( 'sort_type' ); ?>">
				<?php _e('Order by:', 'buddypress'); ?>
				<select name="<?php echo $this->get_field_name( 'sort_type' ); ?>">
					<option value="alphabetical" <?php selected($sort_type,'alphabetical') ?>><?php _e( 'Alphabetical', 'buddypress' ) ?></option>
					<option value="newest" <?php selected($sort_type,'newest') ?>><?php _e( 'Newly Created', 'buddypress' ) ?></option>
					<option value="active" <?php selected($sort_type,'active') ?>><?php _e( 'Last Active', 'buddypress' ) ?></option>
					<option value="popular" <?php selected($sort_type,'popular') ?>><?php _e( 'Most Popular', 'buddypress' ) ?></option>
					<option value="prolific" <?php selected($sort_type,'prolific') ?>><?php _e( 'Most Member Groups', 'wpa-cbox' ) ?></option>
				</select>
			</label>
		</p>
		<p><label for="<?php echo $this->get_field_id( 'show_desc' ); ?>"><input type="checkbox" id="<?php echo $this->get_field_id( 'show_desc' ); ?>" name="<?php echo $this->get_field_name( 'show_desc' ); ?>"<?php if($show_desc) echo ' checked'; ?> /> <?php _e('Show descriptions:', 'wpa-cbox'); ?></label></p>
		<?php
	}
}

} // end check for bp_has_groups_hierarchy



/**
 * Style the login page a little
 */
add_action( 'login_enqueue_scripts', 'wpa_cbox_login' );
function wpa_cbox_login() { ?>
	<style type="text/css">
	#facebook-btn-wrap
	{
		margin-bottom: 15px;
	}
	</style>
	<?php
}



/**
 * change the "No member groups were found." text
 * Props: http://pankajanupam.in
 */
function wpa_cbox_no_subgroups_text( $translated, $text, $domain ) {
	
	// look only for BP Group Hierarchy translations
	if ('bp-group-hierarchy' != $domain) { return $translated; }
	
	// catch all instances of 'No member groups were found.'...
	if ( false !== strpos( $translated, 'No member groups were found.' ) ) {
		return str_replace(  'No member groups were found.', __( 'No local groups were found. If you would like a new local group, please ask a group administrator to set one up.', 'wpa-cbox' ), $translated );
	}
	
	// --<
	return $translated;
	
}

// change the "No member groups were found." text
add_filter( 'gettext', 'wpa_cbox_no_subgroups_text', 40, 3 );



function wpa_cbox_upcoming_events_page( $query ){

    if( eventorganiser_is_event_query( $query ) ){
        if( $query->get( 'wpa_cbox' ) && 'upcoming-events' == $query->get( 'wpa_cbox' ) ){

            //Show only upcoming events.
            $query->set( 'event_start_after', 'now' );
            //This is needed for now, but maybe redundant in the future...
            $query->set( 'showpastevents', true );
            
            //die('here');

        }
    }
}
add_action( 'pre_get_posts', 'wpa_cbox_upcoming_events_page', 5 );



function wpa_cbox_register_query_vars( $qvars ){
    $qvars[] = 'wpa_cbox';
    return $qvars;
}
add_filter('query_vars', 'wpa_cbox_register_query_vars' );



function wpa_cbox_add_rewrite_rule(){
    global $wp_rewrite;

    //Get base regex
    $regex = str_replace( '%event%', 'upcoming-events', $wp_rewrite->get_extra_permastruct('event') );

    //Get pagination base regex
    $pageregex = $wp_rewrite->pagination_base . '/?([0-9]{1,})/?$';

     //Add paged rewrite rule
    add_rewrite_rule( $regex.'/'.$pageregex, 'index.php?post_type=event&paged=$matches[1]&event_start_after=now&wpa_cbox=upcoming-events', 'top' );

     //Add standard rewrite url
    add_rewrite_rule( $regex, 'index.php?post_type=event&event_start_after=now&wpa_cbox=upcoming-events', 'top' );
}

add_action( 'init', 'wpa_cbox_add_rewrite_rule', 11 );



/**
 * Add our login box to the sliding header
 */
function wpa_cbox_add_login_form(){
	
	?>
	
	<div class="bp-sliding-login-left bsl-login-form">

		<div id="wp-fb-login-widget">

			<?php
		
			// only on server
			if ( $_SERVER['HTTP_HOST'] == 'dev.wpa-play.com' ) {
		
				// target AutoConnect Premium
				$widget = 'Widget_AutoConnect_Premium';
		
				// set up instance
				$instance = array( 
					"title" => "Login",
					"labelUserName" => "Username",
					"labelPass" => "Password",
					"labelBtn" => "Login", 
					"labelRemember" => "Remember me",
					"labelForgot" => "Forgot?",
					"labelLogout" => "Logout",
					"labelProfile" => "Edit Profile",
					"labelWelcome" => "Welcome,",
					"showwplogin" => true,
					"showrememberme" => false,
					"showregister" => true,
					"logoutofFB" => false,
					"showavatar" => false,
					"showEditProfile" => true,
					"bpProfileLink" => true,
					"avatarsize" => 35
				);
		
				// args?
				$args = array();
		
				// show widget
				the_widget( $widget, $instance, $args );
		
			}
		
			?>

		</div><!-- /#wp-fb-login-widget -->
	
	</div><!-- /.left.bsl-login-form -->
	
	<?php
	
}

add_action( 'bp_sliding_login_panel_anon_after_register', 'wpa_cbox_add_login_form' );



/**
 * Add our logo to the sliding header
 */
function wpa_cbox_add_logo_to_panel(){
	
	return get_stylesheet_directory_uri().'/assets/images/wpa-logo-panel.png';
	
}

add_action( 'bp_sliding_login_logo', 'wpa_cbox_add_logo_to_panel' );




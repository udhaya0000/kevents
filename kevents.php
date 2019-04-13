<?php
/**
* Plugin Name:     K Events
* Description:     This plugin adds custom post type for events
* version:         1.0.0
* Author:          Udhayakumar Sadagopan
* Author URI:      http://www.udhayakumars.com
**/

/*
* Creating a function to create our CPT
*/


function custom_events()
{

    $theme = "klinchit";
    $singular_name = "Event";
    $plural_name = "Events";
    $cpt_name = "events";

    // Set UI labels for Custom Post Type
    $event_labels = array(
            'name'                => _x($plural_name, 'Post Type General Name', $theme),
            'singular_name'       => _x($singular_name, 'Post Type Singular Name', $theme),
            'menu_name'           => __($plural_name, $theme),
            'parent_item_colon'   => __('Parent '.$singular_name, $theme),
            'all_items'           => __('All '.$plural_name, $theme),
            'view_item'           => __('View '.$singular_name, $theme),
            'add_new_item'        => __('Add New '.$singular_name, $theme),
            'add_new'             => __('Add New', $theme),
            'edit_item'           => __('Edit '.$singular_name, $theme),
            'update_item'         => __('Update '.$singular_name, $theme),
            'search_items'        => __('Search '.$plural_name, $theme),
            'not_found'           => __('Not Found', $theme),
            'not_found_in_trash'  => __('Not found in Trash', $theme),
        );

    // Set other options for Custom Post Type

    $event_args = array(
            'label'               => __($cpt_name, $theme),
            'description'         => __('List of '.$plural_name, $theme),
            'labels'              => $event_labels,
            // Features this CPT supports in Post Editor
            'supports'            => array( 'title', 'thumbnail', 'revisions', 'editor'),
            /* A hierarchical CPT is like Pages and can have
            * Parent and child items. A non-hierarchical CPT
            * is like Posts.
            */
            'hierarchical'        => false,
            'public'              => false,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'show_in_nav_menus'   => true,
            'show_in_admin_bar'   => true,
            'menu_position'       => 5,
            'menu_icon'           => 'dashicons-calendar-alt',
            'can_export'          => true,
            'has_archive'         => false,
            'exclude_from_search' => false,
            'publicly_queryable'  => true,
            'capability_type'     => 'page',
        );

    // Registering your Custom Post Type
    register_post_type($cpt_name, $event_args);

}

/* Hook into the 'init' action so that the function
* Containing our post type registration is not
* unnecessarily executed.
*/

add_action('init', 'custom_events', 0);

function add_event_meta_box()
{
    add_meta_box("event-meta-box", "Event Info", "event_meta_cb", "events", "advanced", "high", null);
}

function event_meta_cb($obj) {
  global $post;

  $values = get_post_custom($post->ID);
  $event_venue = isset($values['event_venue']) ? esc_attr($values['event_venue'][0]) : "";
  $event_start = isset($values['event_start']) ? esc_attr($values['event_start'][0]) : "";
  $event_end = isset($values['event_end']) ? esc_attr($values['event_end'][0]) : "";
  wp_nonce_field( 'event_meta_box_nonce', 'meta_box_nonce' );
  ?>
  <div class="form-group">
    <div>Venue</div>
    <input name="event_venue" class="form-control" type="text" value="<?php echo $event_venue; ?>">
  </div>
  <div class="form-group">
    <div>When</div>
    <input type="text" id="eventdaterange" class="form-control" value="" placeholder="Select date range">
    <input type="hidden" id="event_start" name="event_start" value="<?php echo $event_start; ?>"/>
    <input type="hidden" id="event_end" name="event_end" value="<?php echo $event_end; ?>"/>
  </div>
  <?php
}

add_action("add_meta_boxes", "add_event_meta_box");

function add_events_css_and_js() {
  wp_enqueue_style('bootstrap-grid', plugins_url( 'css/grid.css', __FILE__ ));
  wp_enqueue_style('daterangepicker-css', plugins_url( 'vendor/daterangepicker-master/daterangepicker.css', __FILE__ ));
  wp_enqueue_script('moment-js', plugins_url( 'vendor/daterangepicker-master/moment.min.js', __FILE__ ));
  wp_enqueue_script('daterangepicker-js', plugins_url('vendor/daterangepicker-master/daterangepicker.js', __FILE__), array('jquery', 'moment-js'));
    wp_enqueue_script('kevents-js', plugins_url('js/kevents.js', __FILE__), array('daterangepicker-js'));
}

add_action("admin_enqueue_scripts", 'add_events_css_and_js');

// save meta box contents
function save_event_meta($post_id) {
  //stop if we are doing an auto imap_save
  if(defined( 'DOING_AUTOSAVE') && DOING_AUTOSAVE ) return;


  if(!current_user_can('edit_post', $post_id)) return;

  if(isset($_POST['meta_box_nonce']) && wp_verify_nonce($_POST['meta_box_nonce'], 'event_meta_box_nonce')) {
      save_event_meta_contents($post_id);
  } else {
    return;
  }

}

function save_event_meta_contents($post_id) {

  if(isset($_POST['event_venue']))
    update_post_meta($post_id, 'event_venue', esc_attr($_POST['event_venue']));

  if(isset($_POST['event_start']))
    update_post_meta($post_id, 'event_start', esc_attr($_POST['event_start']));

  if(isset($_POST['event_end']))
    update_post_meta($post_id, 'event_end', esc_attr($_POST['event_end']));
}


add_action('save_post', 'save_event_meta');

<?php
add_action('init', 'nikeplus_create_runs_cpt');
function nikeplus_create_runs_cpt()
{
  $labels = array(
    'name' => _x('Nike+ Runs', 'post type general name', 'nikeplus'),
  );
  $args = array(
    'labels' => $labels,
    'public' => false,
    'publicly_queryable' => true,
    'show_ui' => true,
    '_builtin' => false,
    'show_in_menu' => true,
    'capability_type' => 'post',
    'has_archive' => false,
    'hierarchical' => false,
    'menu_events' => null,
    'menu_icon' => NIKE_PLUS_URL . 'img/sneaker.png',
  );
  register_post_type('nikeplus_runs',$args);
}

add_action('init', 'nikeplus_init');

function nikeplus_init() {
  remove_post_type_support( 'nikeplus_runs', 'editor' );
  //add_post_type_support( 'nikeplus_runs', 'custom_fields' );
}

function nikeplus_parse_query_useronly( $wp_query ) {
  if ( strpos( $_SERVER[ 'REQUEST_URI' ], '/wp-admin/edit.php' ) !== false ) {
      if ( !current_user_can( 'level_10' ) && 'nikeplus_runs' == $wp_query->query_vars['post_type'] ) {
          global $current_user;
          $wp_query->set( 'author', $current_user->id );
      }
  }
}

add_filter('parse_query', 'nikeplus_parse_query_useronly' );

add_action( 'admin_menu', 'nikeplus_adjust_the_wp_menu', 999 );
function nikeplus_adjust_the_wp_menu() {
  $page = remove_submenu_page( 'edit.php?post_type=nikeplus_runs', 'post-new.php?post_type=nikeplus_runs' );
}

add_filter( 'post_row_actions', 'nikeplus_remove_row_actions', 10, 1 );
function nikeplus_remove_row_actions( $actions ) {
    if( get_post_type() == 'nikeplus_runs' ) {
        unset( $actions['edit'] );
        unset( $actions['view'] );
        //unset( $actions['trash'] );
        unset( $actions['inline hide-if-no-js'] );
    }
    return $actions;
}

function nikeplus_hide_buttons() {
  global $pagenow;
  if(is_admin()){
    if($pagenow == 'edit.php' && $_GET['post_type'] == 'nikeplus_runs'){
        echo "<style type=\"text/css\">.add-new-h2{display: none;}</style>";
    }
  }
}
add_action('admin_head','nikeplus_hide_buttons');


add_filter( 'manage_edit-nikeplus_runs_columns', 'nikeplus_runs_columns' ) ;

function nikeplus_runs_columns( $columns ) {

    $columns = array(
        'cb' => '<input type="checkbox" />',
        'title' => __( 'Runs', 'nikeplus' ),
        'author' => __( 'Runner', 'nikeplus' ),
        'date' => __( 'Date', 'nikeplus' )
    );

    return $columns;
}

add_action('restrict_manage_posts', 'nikeplus_author_filter');

function nikeplus_author_filter() {
    if( current_user_can( 'adminiatrator' ) ) :
      $users = get_users( array('meta_key' => 'nike_password' ) );
      if( $user ) {
        $ids = array();
        foreach($users as $user ) {
          $ids[] = $user->ID;
        }
        join( ', ', $ids );
      }
      $args = array('name' => 'author', 'include' => $ids, 'show_option_all' => 'View all runners');
      if( isset( $_REQUEST['user'] ) && 'nikeplus_runs' == $_REQUEST['post_type'] ) {
          $args['selected'] = $_REQUEST['user'];
      }
      if( 'nikeplus_runs' == $_REQUEST['post_type'] )
        wp_dropdown_users( $args );
    endif;
}


/* Define the custom box */

add_action( 'add_meta_boxes', 'myplugin_add_custom_box' );

// backwards compatible (before WP 3.0)
// add_action( 'admin_init', 'myplugin_add_custom_box', 1 );


/* Adds a box to the main column on the Post and Page edit screens */
function myplugin_add_custom_box() {
    add_meta_box(
        'run_stats',
        __( 'Stored Run Data', 'nikeplus' ),
        'nikeplus_run_stats',
        'nikeplus_runs',
        'normal',
        'high'
    );
}

/* Prints the box content */
function nikeplus_run_stats( $post ) {

  $data = get_post_meta( $post->ID, 'run_data', 'single' );
  if(current_user_can( 'administrator' ) )
    echo '<code>get_post_meta( $post->ID, \'run_data\', \'single\' );</code>';

  echo '<pre>';
  print_r($data);
  echo '</pre>';

}

function nikeplus_remove_meta_boxes() {
    remove_meta_box('submitdiv', 'nikeplus_runs', 'core');
}
add_action( 'admin_menu', 'nikeplus_remove_meta_boxes' );


function nikeplus_runs_redirect() {
  global $pagenow;
  //echo $pagenow;
  if( 'post-new.php' === $pagenow && 'nikeplus_runs' == $_REQUEST['post_type'] ) {

    wp_redirect( admin_url('edit.php?post_type=nikeplus_runs') );

  }

}

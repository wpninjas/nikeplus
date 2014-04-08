<?php

function nikeplus_options_menu() {

    add_submenu_page(
        'edit.php?post_type=nikeplus_runs',
        'nike-plus',
        __( 'Nike+ Settings', 'nikeplus' ),
        'administrator',
        'nikeplus',
        'nikeplus_options_display'
    );

}
add_action( 'admin_menu', 'nikeplus_options_menu', 2 );

function nikeplus_options_display() {
    if( 'nikeplus' == $_REQUEST['page'] && 'true' == $_REQUEST['settings-updated'] )
        nikeplus_update_runs();
        nikeplus_activation();
?>
    <!-- Create a header in the default WordPress 'wrap' container -->
    <div class="wrap">

        <div id="icon-themes" class="icon32"></div>
        <h2><?php _e( 'Ninja Nike Options', 'nikeplus' ) ?></h2>
        <?php settings_errors(); ?>

        <form method="post" action="options.php">
            <?php //wp_nonce_field( $action, $name, $referer, $echo ); ?>
            <?php settings_fields( 'nikeplus_options' ); ?>
            <?php do_settings_sections( 'nikeplus_options' ); ?>
            <?php submit_button(__( 'Save Changes & Update Runs', 'nikeplus') ); ?>
        </form>

    </div><!-- /.wrap -->
<?php
}

function nikeplus_initialize_options() {

    if( false == get_option( 'nikeplus_options' ) ) {
        add_option( 'nikeplus_options' );
    }

    /*
    add_settings_section(
        'authentication_section',
        __( 'Authentication Options', 'nikeplus' ),
        'nikeplus_authentication_options_callback',
        'nikeplus_options'
    );

    add_settings_field(
        'email',
        __( 'Email Address', 'nikeplus' ),
        'nikeplus_email_field',
        'nikeplus_options',
        'authentication_section'
    );

    add_settings_field(
        'password',
        __( 'Password', 'nikeplus' ),
        'nikeplus_password_field',
        'nikeplus_options',
        'authentication_section'
    );
    */


    add_settings_section(
        'preferences_section',
        __( 'Preferences', 'nikeplus' ),
        'nikeplus_preferences_callback',
        'nikeplus_options'
    );

    add_settings_field(
        'update_frequency',
        __( 'Update Frequency', 'nikeplus' ),
        'nikeplus_updatefrequency_field',
        'nikeplus_options',
        'preferences_section'
    );

    add_settings_field(
        'distance_unit',
        __( 'Unit for Distance', 'nikeplus' ),
        'nikeplus_distanceunit_field',
        'nikeplus_options',
        'preferences_section'
    );

    register_setting(
        'nikeplus_options',
        'nikeplus_options',
        'nikeplus_options_validation'
    );

    add_settings_section(
        'update_section',
        __( 'Update all runners', 'nikeplus' ),
        'nikeplus_update_callback',
        'nikeplus_options'
    );

}
add_action('admin_init', 'nikeplus_initialize_options');

function nikeplus_authentication_options_callback() {
    echo '<p>' . __( 'Ninja Nike needs some basic information in order to get your Nike+ data.', 'nikeplus' ) . '</p>';
}

function nikeplus_email_field() {

    $opt = get_option( 'nikeplus_options' );
    //print_r($opt);
    if( ! isset( $opt['email']) )
        $opt['email'] = null;

    echo '<input id="nikeplus-email" name="nikeplus_options[email]" size="40" type="text" value="' . $opt['email'] . '" />';
    //echo '<label for="' . $name . '"> ' . __( 'label', 'nikeplus' ) . '</label>';
    echo '<div class="description">' .  __( 'Your email address you\'ve registered with Nike+.', 'nikeplus' ) . '</div>';

}

function nikeplus_password_field() {

    $opt = get_option( 'nikeplus_options' );
    //print_r($opt);
    if( ! isset( $opt['password']) )
        $opt['[password]'] = null;

    echo '<input id="nikeplus-pasword" name="nikeplus_options[password]" size="40" type="password" value="' . $opt['password'] . '" />';
    //echo '<label for="' . $name . '"> ' . __( 'label', 'nikeplus' ) . '</label>';
    ///echo '<div class="description">' .  __( 'Your password you\'ve registered with Nike+.', 'nikeplus' ) . '</div>';

}

function nikeplus_preferences_callback() {
    echo '<p>' . __( '', 'nikeplus' ) . '</p>';
}

function nikeplus_update_callback() {
    echo '<p>' . __( 'Upon clicking save of these options we will check for any unsaved runs with Nike+ and add them to your system. This may take a few moments.', 'nikeplus' ) . '</p>';
}

function  nikeplus_distanceunit_field() {

    $opt = get_option( 'nikeplus_options' );
    if( ! isset( $opt['distance_unit']) )
        $opt['distance_unit'] = null;

    echo '<select id="nikeplus-distanceunit" name="nikeplus_options[distance_unit]">';
        echo '<option value="KM"' . selected( 'KM', $opt['distance_unit'], false ) . '>' . __( 'Kilometers', 'nikeplus' ) . '</option>';
        echo '<option value="MI"' . selected( 'MI', $opt['distance_unit'], false ) . '>' . __( 'Miles', 'nikeplus' ) . '</option>';
    echo '</select>';
    //echo '<label for="' . $name . '"> ' . $label . '</label>';
    //echo '<div class="description">' . $description . '</div>';

}

function  nikeplus_updatefrequency_field() {

    $opt = get_option( 'nikeplus_options' );
    if( ! isset( $opt['update_frequency'] ) )
        $opt['update_frequency'] = null;

    //$options = wp_get_schedules();
    //echo '<pre>';
    //print_r($opt);
    //echo '</pre>';
    echo '<select id="nikeplus-update_frequency" name="nikeplus_options[update_frequency]">';
        $options = wp_get_schedules();
        foreach( $options as $k => $v ) {
            echo '<option value="' . $k . '"' . selected( $k, $opt['update_frequency'], false ) . '>' . $v["display"] . '</option>';
        }
    echo '</select>';
    //echo '<label for="' . $name . '"> ' . $label . '</label>';
    //echo '<div class="description">' . $description . '</div>';

}

function nikeplus_options_validation( $input ) {

    $valid = array();

    //print_r($input);
    if( !empty( $input['email'] ) )
        $valid['email'] = sanitize_email( $input['email'] );

    if( !empty($input['password'] ) )
        $valid['password'] = sanitize_text_field( $input['password'] );

    if( !empty( $input['distance_unit'] ) )
        $valid['distance_unit'] = $input['distance_unit'];

    if( !empty( $input['update_frequency'] ) )
        $valid['update_frequency'] = $input['update_frequency'];

    return $valid;

}
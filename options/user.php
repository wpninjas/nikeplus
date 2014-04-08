<?php

add_action( 'show_user_profile', 'nikeplus_profile_fields' );
add_action( 'edit_user_profile', 'nikeplus_profile_fields' );

function nikeplus_profile_fields( $user ) {

    $nike_username = get_user_meta( $user->ID, 'nike_username', true );
    $nike_password = get_user_meta( $user->ID, 'nike_password', true );
    $nike_profile = get_user_meta( $user->ID, 'nike_profile', true );

    if( empty( $nike_username ) )
        $nike_username = '';

    if( empty( $nike_password ) )
        $nike_password = '';

    if( empty( $nike_profile ) )
        $nike_profile = '';

    ?>

    <h3><?php _e( 'Nike+ Credentials', 'nikeplus' ); ?></h3>

    <table class="form-table">
        <tr>
            <th><label for="twitter"><?php _e( 'Username/Email', 'nikeplus' ); ?></label></th>
            <td>
                <input type="text" name="nike_username" id="nike_username" value="<?php echo $nike_username; ?>" size="20" />
                <span class="description"><?php _e( 'Please enter your Nike+ email.', 'nikeplus' ); ?></span>
            </td>
        </tr>
        <tr>
            <th><label for="twitter"><?php _e( 'Password', 'nikeplus' ); ?></label></th>
            <td>
                <input type="password" name="nike_password" id="nike_password" value="<?php echo $nike_password; ?>" size="20" />
                <span class="description"><?php _e( 'Please enter your Nike+ password.', 'nikeplus' ); ?></span>
            </td>
        </tr>
        <tr>
            <th><label for="twitter"><?php _e( 'Profile URL', 'nikeplus' ); ?></label></th>
            <td>
                <input type="text" name="nike_profile" id="nike_profile" value="<?php echo $nike_profile; ?>" class="regular-text" />
                <span class="description"><?php _e( 'Please enter your Nike+ profile URL.', 'nikeplus' ); ?></span>
            </td>
        </tr>
    </table>
<?php }

add_action( 'personal_options_update', 'nikeplus_save_extra_profile_fields' );
add_action( 'edit_user_profile_update', 'nikeplus_save_extra_profile_fields' );

function nikeplus_save_extra_profile_fields( $user_id ) {

    if ( !current_user_can( 'edit_user', $user_id ) )
        return false;

    if( !empty( $_POST['nike_username'] ) )
        $nike_username = sanitize_email( $_POST['nike_username'] );

    if( !empty( $_POST['nike_password'] ) )
        $nike_password = sanitize_text_field( $_POST['nike_password'] );

    if( !empty( $_POST['nike_profile'] ) )
        $nike_profile = esc_url_raw( $_POST['nike_profile'] );

    if( empty( $nike_username ) ) {
        delete_user_meta( $user_id, 'nike_username' );
    } else {
        update_user_meta( $user_id, 'nike_username', $nike_username );
    }
    if( empty( $nike_password ) ) {
        delete_user_meta( $user_id, 'nike_password' );
    } else {
        update_user_meta( $user_id, 'nike_password', $nike_password );
    }
    if( empty( $nike_profile ) ) {
        delete_user_meta( $user_id, 'nike_profile' );
    } else {
        update_user_meta( $user_id, 'nike_profile', $nike_profile );
    }
}
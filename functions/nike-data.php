<?php
function nikeplus_subval_sort($a) {
    foreach($a as $k=>$v) {
        $b[$k] = str_replace( 'RUN ON: ', '', strtolower($v->name) );
        $b[$k] = strtotime( $b[$k] );
    }
    foreach($b as $key=>$val) {
        $c[] = $a[$key];
    }
    rsort($c);
    $d = array();
    foreach($c as $k=>$v) {
        $v->name = str_replace( 'run on: ', '', strtolower($v->name) );
        $v->name = strtotime( $v->name );
        $new = $v->name;
        foreach($b as $key=>$val) {
            if($v->activityId == $key) {
                $d[$new] = $a[$key];
                break;
            }
        }
    }
    krsort( $d );
    return $d;
}

function nikeplus_update_runs() {

    $users = get_users( array('meta_key' => 'nike_password' ) );
    if( $users ) :
    foreach( $users as $user ) {

        $nike_username = get_user_meta( $user->ID, 'nike_username', true );
        $nike_password = get_user_meta( $user->ID, 'nike_password', true );
        $last_recorded_run = get_user_meta( $user->ID, 'last_recorded_run', true );
        $opt = get_option( 'nikeplus_options' );

        if( empty( $nike_username ) )
            $nike_username = '';

        if( empty( $nike_password ) )
            $nike_password = '';

        if( empty( $last_recorded_run ) )
            $last_recorded_run = '';

        if( isset( $nike_username ) && isset( $nike_password ) ) {
            $nike = new NikePlusPHP( $nike_username, $nike_password, true);

                $nike_runs = $nike->activities();
                //print_r($nike_runs);
                $nike_runs_sorted = nikeplus_subval_sort( $nike_runs );
                foreach( $nike_runs_sorted as $sorted_runs) {
                    $nike_last_run = $sorted_runs->name;
                    break;
                }

            if( $nike_last_run > $last_recorded_run ) {
                $run_data = array();
                $last_added = '';
                $i = 1;

                $nike_alltime = $nike->allTime()->lifetimeTotals;

                update_user_meta( $user->ID, 'run_totals', $nike_alltime );

                foreach( $nike_runs_sorted as $run ) {

                    $run_timestamp = $run->name;

                    if( $run_timestamp <= $last_recorded_run ) {
                        break;
                    } else {
                        //Create Posts

                            $gmt_date = date( 'Y-m-d H:i:s', $run_timestamp );
                            $post_title = 'Run on ' . date( 'l F jS Y \a\t H:i A', $run->name );

                            $post = array(
                                'post_title' => $post_title,
                                'post_content' => '',
                                'post_status' => 'publish',
                                'post_type' => 'nikeplus_runs',
                                'post_date_gmt' => $gmt_date,
                                'post_date' => $gmt_date,
                                'post_author' => $user->ID,
                            );
                            $post_id = wp_insert_post( $post );
                            update_post_meta( $post_id, 'run_data', $run );

                    }
                }

                update_user_meta( $user->ID, 'last_recorded_run', $nike_last_run );
                do_action( 'nikeplus_after_update' );

            }
        }
    }
    endif;
}

function nikeplus_update_team_totals() {

    $users = get_users( array('meta_key' => 'run_totals' ) );

    if( $users ) :

        if( ! isset( $total ) ) {
            $total = array(
                'calorie' => 0,
                'distance' => 0,
                'duration' => 0,
                'run' => 0,
                'fuel' => 0,
            );
        }

        foreach( $users as $user ) {
            $data = get_user_meta( $user->ID, 'run_totals' );
            $total['calorie'] += $data[0]->calorie;
            $total['distance'] += $data[0]->distance;
            $total['duration'] += $data[0]->duration;
            $total['run'] += $data[0]->run;
            $total['fuel'] += $data[0]->totalFuel;
        }

        update_option( 'nikeplus_team_totals', $total );

    endif;

}
add_action( 'nikeplus_after_update', 'nikeplus_update_team_totals' );
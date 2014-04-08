<?php
function nikeplus_personal_records_defaults() {
    $defaults = array(
        'runner'       => '', // accepts a single user_id
        'show_list'     => false,
        'show_name'     => false,
        'show_1k'       => false,
        'show_1m'       => true,
        'show_5k'       => true,
        'show_10k'      => true,
        'show_half'     => false,
        'show_full'     => false,
        'show_farthest' => true,
        'show_longest'  => true,
        'show_calories' => false,
    );
    return $defaults;
}
function nikeplus_personal_records( $args ) {
    $defaults = nikeplus_personal_records_defaults();
    $args = wp_parse_args( $args, $defaults );
    extract( $args, EXTR_SKIP );
    $totals = get_user_meta( $runner, 'run_totals' );

    if( $totals ) :
        //echo '<pre>';
        //print_r($totals);
        //echo '</pre>';
        $n = new NikePlusPHP( '', '', false);
        $opt = get_option( 'nikeplus_options' );
        $content = '';
        do_action( 'nikeplus_personal_records_before', $args );
        if( 'MI' == $opt['distance_unit'] ) {
            $distance = $n->toMiles($totals[0]->runFarthest);
        } else {
            $distance = $n->toTwoDecimalPlaces( $totals[0]->runFarthest );
        }
        if( $show_list ) {
            $content .= '<ul>';

            if( $show_name ) :
                $list[] = get_userdata($runner)->display_name;
            endif;
            if( $show_1k ) :
                $content .= '<li>'  . $n->formatDuration( $totals[0]->fastest1K ) . ' ' . __( 'Fastest 1k', 'nikeplus' ) . '</li>';
            endif;
            if( $show_1m ) :
                $content .= '<li>'  . $n->formatDuration( $totals[0]->fastest1M ) . ' ' . __( 'Fastest 1mi', 'nikeplus' ) . '</li>';
            endif;
            if( $show_5k ) :
                $content .= '<li>'  . $n->formatDuration( $totals[0]->fastest5K ) . ' ' . __( 'Fastest 5k', 'nikeplus' ) . '</li>';
            endif;
            if( $show_10k ) :
                $content .= '<li>'  . $n->formatDuration( $totals[0]->fastest10K ) . ' ' . __( 'Fastest 10k', 'nikeplus' ) . '</li>';
            endif;
            if( $show_half ) :
                $content .= '<li>'  . $n->formatDuration( $totals[0]->fastestHalfMarathon ) . ' ' . __( 'Fastest Half Marathon', 'nikeplus' ) . '</li>';
            endif;
            if( $show_full ) :
                $content .= '<li>'  . $n->formatDuration( $totals[0]->fastestMarathon ) . ' ' . __( 'Fastest Marathon', 'nikeplus' ) . '</li>';
            endif;
            if( $show_farthest ) :
                $content .= '<li>' . $distance . '/' . $opt['distance_unit'] . ' ' . __( 'Farthest Distance', 'nikeplus' ) . '</li>';
            endif;
            if( $show_longest ) :
                $content .= '<li>' . $n->formatDuration( $totals[0]->longestRunDuration ) . ' ' . __( 'Longest Run', 'nikeplus' ) . '</li>';
            endif;
            if( $show_calories ) :
                $content .= '<li>' . number_format($totals[0]->mostCaloriesBurnedSingleRun ) . ' ' . __( 'Most Calories', 'nikeplus' ) . '</li>';
            endif;

            $content .= '</ul>';
        } else {
            $content .= '<table>';
            $content .= '<tr>';
            if( $show_name ) :
                $content .= '<th>' . __( 'Runner', 'nikeplus' ) . '</th>';
            endif;
            if( $show_1k ) :
                $content .= '<th>' . __( 'Fastest 1k', 'nikeplus' ) . '</th>';
            endif;
            if( $show_1m ) :
                $content .= '<th>' . __( 'Fastest 1mi', 'nikeplus' ) . '</th>';
            endif;
            if( $show_5k ) :
                $content .= '<th>' . __( 'Fastest 5k', 'nikeplus' ) . '</th>';
            endif;
            if( $show_10k ) :
                $content .= '<th>' . __( 'Fastest 10k', 'nikeplus' ) . '</th>';
            endif;
            if( $show_half ) :
                $content .= '<th>' . __( 'Fastest Half Marathon', 'nikeplus' ) . '</th>';
            endif;
            if( $show_full ) :
                $content .= '<th>' . __( 'Fastest Marathon', 'nikeplus' ) . '</th>';
            endif;
            if( $show_farthest ) :
                $content .= '<th>' . __( 'Farthest Distance', 'nikeplus' ) . '</th>';
            endif;
            if( $show_longest ) :
                $content .= '<th>' . __( 'Longest Run', 'nikeplus' ) . '</th>';
            endif;
            if( $show_calories ) :
                $content .= '<th>' . __( 'Most Calories', 'nikeplus' ) . '</th>';
            endif;
            $content .= '</tr>';
                $content .= '<tr>';
                if( $show_name ) :
                    $content .= '<td>';
                        $content .= get_userdata($runner)->display_name;
                    $content .= '</td>';
                endif;
                if( $show_1k ) :
                    $content .= '<td>';
                        $content .= $n->formatDuration( $totals[0]->fastest1K );
                    $content .= '</td>';
                endif;
                if( $show_1m ) :
                    $content .= '<td>';
                        $content .= $n->formatDuration( $totals[0]->fastest1M );
                    $content .= '</td>';
                endif;
                if( $show_5k ) :
                    $content .= '<td>';
                        $content .= $n->formatDuration( $totals[0]->fastest5K );
                    $content .= '</td>';
                endif;
                if( $show_10k ) :
                    $content .= '<td>';
                        $content .= $n->formatDuration( $totals[0]->fastest10K );
                    $content .= '</td>';
                endif;
                if( $show_half ) :
                    $content .= '<td>';
                        $content .= $n->formatDuration( $totals[0]->fastestHalfMarathon );
                    $content .= '</td>';
                endif;
                if( $show_full ) :
                    $content .= '<td>';
                        $content .= $n->formatDuration( $totals[0]->fastestMarathon );
                    $content .= '</td>';
                endif;
                if( $show_farthest ) :
                    $content .= '<td>';
                        $content .= $distance . '/' . $opt['distance_unit'];
                    $content .= '</td>';
                endif;
                if( $show_longest ) :
                    $content .= '<td>';
                        $content .= $n->formatDuration( $totals[0]->longestRunDuration );
                    $content .= '</td>';
                endif;
                if( $show_calories ) :
                    $content .= '<td>';
                        $content .= number_format($totals[0]->mostCaloriesBurnedSingleRun );
                    $content .= '</td>';
                endif;
                $content .= '</tr>';
            $content .= '</table>';
        }
        do_action( 'nikeplus_personal_records_after' );
        return apply_filters( 'nikeplus_personal_records', $content );
    endif;

}

function nikeplus_personal_records_shortcode( $atts ){

    extract( shortcode_atts( nikeplus_personal_records_defaults(), $atts ) );

    $content = nikeplus_personal_records( $atts );
    return apply_filters( 'nikeplus_personal_records_shortcode', $content );

}
add_shortcode( 'personal_records', 'nikeplus_personal_records_shortcode' );

class nikeplus_personal_records extends WP_Widget {

    public function __construct() {
        // widget actual processes
        parent::__construct(
            'nikeplus_personal_records', // Base ID
            __( 'Nike+ Personal Records', 'nikeplus' ), // Name
            array( 'description' => __( 'List Nike+ Personal Records', 'nikeplus' ), ) // Args
        );
    }

    public function form( $instance ) {
        // outputs the options form on admin
        $defaults = nikeplus_personal_records_defaults();
        $args = wp_parse_args( $instance, $defaults );

        extract( $args, EXTR_SKIP );
        $users  = get_users( array('meta_key' => 'nike_password' ) );
        ?>

        <p>
        <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'nikeplus' ); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
        </p>

        <p>
        <label for="<?php echo $this->get_field_id( 'runner' ); ?>"><?php _e( 'Display Run As:', 'nikeplus' ); ?></label>
        <select id="<?php echo $this->get_field_id( 'show_list' ); ?>" name="<?php echo $this->get_field_name( 'show_list' ); ?>" type="text">
            <option value="0" <?php  selected( $show_list, 0, true ); ?>><?php _e( 'A Table', 'nikeplus' ); ?></option>
            <option value="1" <?php  selected( $show_list, 1, true ); ?>><?php _e( 'A List', 'nikeplus' ); ?></option>
        </select>
        </p>

        <p>
        <label for="<?php echo $this->get_field_id( 'runner' ); ?>"><?php _e( 'Runner:', 'nikeplus' ); ?></label>
        <select id="<?php echo $this->get_field_id( 'runner' ); ?>" name="<?php echo $this->get_field_name( 'runner' ); ?>" type="text">
            <?php
            if( $users ) {
                foreach ($users as $user) {
                    echo '<option value="' . $user->ID . '"' . selected( $runner, $user->ID, true ) . '>' . $user->display_name . '</option>';
                }
            } else {
                echo '<option value="">' . __( 'No Verified Runners', 'nikeplus' ) . '</option>';
            } ?>
        </select>
        </p>
        <p>
        <input id="<?php echo $this->get_field_id( 'show_1k' ); ?>" name="<?php echo $this->get_field_name( 'show_runs' ); ?>" type="checkbox" <?php checked( $show_1k, 1, true ); ?> value="1" />
        <label for="<?php echo $this->get_field_id( 'show_1k' ); ?>"><?php _e( 'Show 1k', 'nikeplus' ); ?></label>
        </p>
        <p>
        <input id="<?php echo $this->get_field_id( 'show_1m' ); ?>" name="<?php echo $this->get_field_name( 'show_1m' ); ?>" type="checkbox" <?php checked( $show_1m, 1, true ); ?> value="1" />
        <label for="<?php echo $this->get_field_id( 'show_1m' ); ?>"><?php _e( 'Show 1mi', 'nikeplus' ); ?></label>
        </p>
        <p>
        <input id="<?php echo $this->get_field_id( 'show_5k' ); ?>" name="<?php echo $this->get_field_name( 'show_5k' ); ?>" type="checkbox" <?php checked( $show_5k, 1, true ); ?> value="1" />
        <label for="<?php echo $this->get_field_id( 'show_5k' ); ?>"><?php _e( 'Show 5k', 'nikeplus' ); ?></label>
        </p>
        <p>
        <input id="<?php echo $this->get_field_id( 'show_10k' ); ?>" name="<?php echo $this->get_field_name( 'show_10k' ); ?>" type="checkbox" <?php checked( $show_10k, 1, true ); ?> value="1" />
        <label for="<?php echo $this->get_field_id( 'show_10k' ); ?>"><?php _e( 'Show 10k', 'nikeplus' ); ?></label>
        </p>
        <p>
        <input id="<?php echo $this->get_field_id( 'show_half' ); ?>" name="<?php echo $this->get_field_name( 'show_half' ); ?>" type="checkbox" <?php checked( $show_half, 1, true ); ?> value="1" />
        <label for="<?php echo $this->get_field_id( 'show_half' ); ?>"><?php _e( 'Show Half Marathon', 'nikeplus' ); ?></label>
        </p>
        <p>
        <input id="<?php echo $this->get_field_id( 'show_full' ); ?>" name="<?php echo $this->get_field_name( 'show_full' ); ?>" type="checkbox" <?php checked( $show_full, 1, true ); ?> value="1" />
        <label for="<?php echo $this->get_field_id( 'show_full' ); ?>"><?php _e( 'Show Marathon', 'nikeplus' ); ?></label>
        </p>
        <p>
        <input id="<?php echo $this->get_field_id( 'show_farthest' ); ?>" name="<?php echo $this->get_field_name( 'show_farthest' ); ?>" type="checkbox" <?php checked( $show_farthest, 1, true ); ?> value="1" />
        <label for="<?php echo $this->get_field_id( 'show_farthest' ); ?>"><?php _e( 'Show Farthest Run', 'nikeplus' ); ?></label>
        </p>
        <p>
        <input id="<?php echo $this->get_field_id( 'show_longest' ); ?>" name="<?php echo $this->get_field_name( 'show_longest' ); ?>" type="checkbox" <?php checked( $show_longest, 1, true ); ?> value="1" />
        <label for="<?php echo $this->get_field_id( 'show_longest' ); ?>"><?php _e( 'Show Longest Run', 'nikeplus' ); ?></label>
        </p>
        <p>
        <input id="<?php echo $this->get_field_id( 'show_calories' ); ?>" name="<?php echo $this->get_field_name( 'show_calories' ); ?>" type="checkbox" <?php checked( $show_calories, 1, true ); ?> value="1" />
        <label for="<?php echo $this->get_field_id( 'show_calories' ); ?>"><?php _e( 'Show Calories', 'nikeplus' ); ?></label>
        </p>
        <?php
    }

    public function update( $new_instance, $old_instance ) {
        // processes widget options to be saved
        $instance = array();
        $instance['title']          = strip_tags( $new_instance['title'] );
        $instance['runner']         = $new_instance['runner'];
        $instance['show_list']        = $new_instance['show_list'];
        $instance['show_1k']        = $new_instance['show_1k'];
        $instance['show_1m']        = $new_instance['show_1m'];
        $instance['show_5k']        = $new_instance['show_5k'];
        $instance['show_10k']       = $new_instance['show_10k'];
        $instance['show_half']      = $new_instance['show_half'];
        $instance['show_full']      = $new_instance['show_full'];
        $instance['show_farthest']  = $new_instance['show_farthest'];
        $instance['show_longest']   = $new_instance['show_longest'];
        $instance['show_calories']  = $new_instance['show_calories'];

        return $instance;
    }

    public function widget( $args, $instance ) {
        // outputs the content of the widget
        //extract( $args );
        /**
         * Define the array of defaults
         */

        $defaults = nikeplus_personal_records_defaults();

        $instance = wp_parse_args( $instance, $defaults );

        $args = wp_parse_args( $args, $instance );

        extract( $args, EXTR_SKIP );

        $title = apply_filters( 'widget_title', $instance['title'] );
            echo $before_widget;
            if ( ! empty( $title ) )
                echo $before_title . $title . $after_title;

            echo apply_filters( 'nikeplus_personal_records_widget', nikeplus_personal_records( $args ) );
            echo $after_widget;
    }

}
add_action( 'widgets_init', create_function( '', 'register_widget( "nikeplus_personal_records" );' ) );
<?php
function nikeplus_team_totals_defaults() {
    $defaults = array(
        'show_list'     => false,
        'show_runs'     => true,
        'show_distance' => true,
        'show_duration' => true,
        'show_pace'     => true,
        'show_fuel'     => true,
        'show_calories' => true,
    );
    return $defaults;
}
function nikeplus_team_totals( $args ) {
    $defaults = nikeplus_team_totals_defaults();
    $args = wp_parse_args( $args, $defaults );
    extract( $args, EXTR_SKIP );
    $totals = get_option( 'nikeplus_team_totals' );

    if( $totals ) :
        $n = new NikePlusPHP( '', '', false);
        $opt = get_option( 'nikeplus_options' );
        $content = '';
        do_action( 'nikeplus_team_totals_before', $args );
        if( 'MI' == $opt['distance_unit'] ) {
            $distance = $n->toMiles($totals['distance']);
            $pace = $n->calculatePace( $totals['duration'], $totals['distance'], $toMiles = true );
        } else {
            $distance = $n->toTwoDecimalPlaces( $totals['distance'] );
            $pace = $n->calculatePace( $totals['duration'], $totals['distance'], $toMiles = false );
        }
        if( $show_list ) {
            $content .= '<ul>';

            if( $show_runs ) :
                $content .= '<li>'  . $totals['run'] . ' ' . __( 'Runs', 'nikeplus' ) . '</li>';
            endif;
            if( $show_fuel ) :
                $content .= '<li>'  . number_format( $totals['fuel'] ) . ' ' . __( 'Fuel', 'nikeplus' ) . '</li>';
            endif;
            if( $show_distance ) :
                $content .= '<li>' . $distance . '/' . $opt['distance_unit'] . ' ' . __( 'Distance', 'nikeplus' ) . '</li>';
            endif;
            if( $show_duration ) :
                $content .= '<li>' . $n->formatLongDuration($totals['duration']) . ' ' . __( 'Duration', 'nikeplus' ) . '</li>';
            endif;
            if( $show_pace ) :
                $content .= '<li>' . $pace . '/' . $opt['distance_unit'] . ' ' . __( 'Pace', 'nikeplus' ) . '</li>';
            endif;
            if( $show_calories ) :
                $content .= '<li>' . number_format($totals['calorie'] ) . ' ' . __( 'Calories', 'nikeplus' ) . '</li>';
            endif;

            $content .= '</ul>';
        } else {
            $content .= '<table>';
            $content .= '<tr>';
            if( $show_runs ) :
                $content .= '<th>' . __( 'Runs', 'nikeplus' ) . '</th>';
            endif;
            if( $show_fuel ) :
                $content .= '<th>' . __( 'Fuel', 'nikeplus' ) . '</th>';
            endif;
            if( $show_distance ) :
                $content .= '<th>' . __( 'Distance', 'nikeplus' ) . '</th>';
            endif;
            if( $show_duration ) :
                $content .= '<th>' . __( 'Duration', 'nikeplus' ) . '</th>';
            endif;
            if( $show_pace ) :
                $content .= '<th>' . __( 'Average Pace', 'nikeplus' ) . '</th>';
            endif;
            if( $show_calories ) :
                $content .= '<th>' . __( 'Calories', 'nikeplus' ) . '</th>';
            endif;
            $content .= '</tr>';
                $content .= '<tr>';
                if( $show_runs ) :
                    $content .= '<td>';
                        $content .= $totals['run'];
                    $content .= '</td>';
                endif;
                if( $show_fuel ) :
                    $content .= '<td>';
                        $content .= number_format( $totals['fuel'] );
                    $content .= '</td>';
                endif;
                if( $show_distance ) :
                    $content .= '<td>';
                        $content .= $distance . '/' . $opt['distance_unit'];
                    $content .= '</td>';
                endif;
                if( $show_duration ) :
                    $content .= '<td>';
                        $content .= $n->formatLongDuration($totals['duration']);
                    $content .= '</td>';
                endif;
                if( $show_pace ) :
                    $content .= '<td>';
                        $content .= $pace;
                        $content .= '/' . $opt['distance_unit'];
                    $content .= '</td>';
                endif;
                if( $show_calories ) :
                    $content .= '<td>';
                        $content .= number_format( $totals['calorie'] );
                    $content .= '</td>';
                endif;
                $content .= '</tr>';
            $content .= '</table>';
        }
        do_action( 'nikeplus_team_totals_after' );
        return apply_filters( 'nikeplus_team_totals', $content );
    endif;

}

function nikeplus_team_totals_shortcode( $atts ){

    extract( shortcode_atts( nikeplus_team_totals_defaults(), $atts ) );

    $content = apply_filters( 'nikeplus_team_totals_shortcode', nikeplus_team_totals( $atts ) );
    return $content;

}
add_shortcode( 'team_totals', 'nikeplus_team_totals_shortcode' );

class nikeplus_team_totals extends WP_Widget {

    public function __construct() {
        // widget actual processes
        parent::__construct(
            'nikeplus_team_totals', // Base ID
            __( 'Nike+ Team Totals', 'nikeplus' ), // Name
            array( 'description' => __( 'List Nike+ Team Totals', 'nikeplus' ), ) // Args
        );
    }

    public function form( $instance ) {
        // outputs the options form on admin
        $defaults = nikeplus_team_totals_defaults();
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
        <input id="<?php echo $this->get_field_id( 'show_runs' ); ?>" name="<?php echo $this->get_field_name( 'show_runs' ); ?>" type="checkbox" <?php checked( $show_runs, 1, true ); ?> value="1" />
        <label for="<?php echo $this->get_field_id( 'show_runs' ); ?>"><?php _e( 'Show Runs', 'nikeplus' ); ?></label>
        </p>
        <p>
        <input id="<?php echo $this->get_field_id( 'show_fuel' ); ?>" name="<?php echo $this->get_field_name( 'show_fuel' ); ?>" type="checkbox" <?php checked( $show_fuel, 1, true ); ?> value="1" />
        <label for="<?php echo $this->get_field_id( 'show_fuel' ); ?>"><?php _e( 'Show Nike Fuel', 'nikeplus' ); ?></label>
        </p>
        <p>
        <input id="<?php echo $this->get_field_id( 'show_name' ); ?>" name="<?php echo $this->get_field_name( 'show_name' ); ?>" type="checkbox" <?php checked( $show_name, 1, true ); ?> value="1" />
        <label for="<?php echo $this->get_field_id( 'show_name' ); ?>"><?php _e( 'Show Runners Name', 'nikeplus' ); ?></label>
        </p>
        <p>
        <input id="<?php echo $this->get_field_id( 'show_distance' ); ?>" name="<?php echo $this->get_field_name( 'show_distance' ); ?>" type="checkbox" <?php checked( $show_distance, 1, true ); ?> value="1" />
        <label for="<?php echo $this->get_field_id( 'show_distance' ); ?>"><?php _e( 'Show Distance', 'nikeplus' ); ?></label>
        </p>
        <p>
        <input id="<?php echo $this->get_field_id( 'show_duration' ); ?>" name="<?php echo $this->get_field_name( 'show_duration' ); ?>" type="checkbox" <?php checked( $show_duration, 1, true ); ?> value="1" />
        <label for="<?php echo $this->get_field_id( 'show_duration' ); ?>"><?php _e( 'Show Duration', 'nikeplus' ); ?></label>
        </p>
        <p>
        <input id="<?php echo $this->get_field_id( 'show_pace' ); ?>" name="<?php echo $this->get_field_name( 'show_pace' ); ?>" type="checkbox" <?php checked( $show_pace, 1, true ); ?> value="1" />
        <label for="<?php echo $this->get_field_id( 'show_pace' ); ?>"><?php _e( 'Show Pace', 'nikeplus' ); ?></label>
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
        $instance['title'] = strip_tags( $new_instance['title'] );
        $instance['runner'] = $new_instance['runner'];
        $instance['runners'] = $new_instance['runners'];
        if( '' == $new_instance['runners'] ) {
            $instance['runners'] = $new_instance['runner'];
        }
        $instance['num_runs'] = intval( $new_instance['num_runs'] );
        $instance['show_list'] = $new_instance['show_list'];
        $instance['show_runs'] = $new_instance['show_runs'];
        $instance['show_fuel'] = $new_instance['show_fuel'];
        $instance['show_name'] = $new_instance['show_name'];
        $instance['show_distance'] = $new_instance['show_distance'];
        $instance['show_duration'] = $new_instance['show_duration'];
        $instance['show_pace'] = $new_instance['show_pace'];
        $instance['show_calories'] = $new_instance['show_calories'];

        return $instance;
    }

    public function widget( $args, $instance ) {
        // outputs the content of the widget
        //extract( $args );
        /**
         * Define the array of defaults
         */

        $defaults = nikeplus_team_totals_defaults();

        $instance = wp_parse_args( $instance, $defaults );

        $args = wp_parse_args( $args, $instance );

        extract( $args, EXTR_SKIP );

        $title = apply_filters( 'widget_title', $instance['title'] );
            echo $before_widget;
            if ( ! empty( $title ) )
                echo $before_title . $title . $after_title;

            echo apply_filters( 'nikeplus_team_totals_widget', nikeplus_team_totals( $args ) );
            echo $after_widget;
    }

}
add_action( 'widgets_init', create_function( '', 'register_widget( "nikeplus_team_totals" );' ) );
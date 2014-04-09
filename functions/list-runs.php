<?php
function nikeplus_list_runs_defaults() {
    $defaults = array(
        'runners'       => '', // accepts a single user_id, comma seprated list of user_id's, negative user_id's to exclude runners, or blank to show all
        'num_runs'      => '', // How many runs would you like to list. Accepts an int or '-1' to show all runs
        'show_list'     => false,
        'show_name'     => false,
        'show_date'     => true,
        'show_distance' => true,
        'show_duration' => true,
        'show_pace'     => true,
        'show_fuel'     => false,
        'show_calories' => false,
    );
    return $defaults;
}
/**
 * Loop through all runs based on $args passed
 * @param  Array $args
 * @return string
 */
function nikeplus_list_runs( $args ) {
    $defaults = nikeplus_list_runs_defaults();
    $args = wp_parse_args( $args, $defaults );
    extract( $args, EXTR_SKIP );
    $fetch = array(
        'author' => $runners,
        'post_type' => 'nikeplus_runs',
        'posts_per_page' => $num_runs
    );
    $query = new WP_Query($fetch);

    if($query->posts) :
        $n = new NikePlusPHP( '', '' );
        $opt = get_option( 'nikeplus_options' );
        $content = '';
        do_action( 'nikeplus_list_runs_before', $args );

        if( $show_list ) {
            $content .= '<ul>';

            foreach( $query->posts as $post ) {
                $run_data = get_post_meta( $post->ID, 'run_data', 'single' );
                if( 'MI' == $opt['distance_unit'] ) {
                    $distance = $n->toMiles($run_data->metrics->distance);
                    $pace = $n->calculatePace( $run_data->metrics->duration, $run_data->metrics->distance, $toMiles = true );
                } else {
                    $distance = $n->toTwoDecimalPlaces( $run_data->metrics->distance );
                    $pace = $n->calculatePace( $run_data->metrics->duration, $run_data->metrics->distance, $toMiles = false );
                }
                $content .= '<li>';
                $list = array();
                if( $show_name ) :
                    $list[] = get_userdata($post->post_author)->display_name;
                endif;
                if( $show_date ) :
                    $list[] = date( 'm/d/Y', $run_data->name );
                endif;
                if( $show_fuel ) :
                    $list[] = $run_data->metrics->fuel;
                endif;
                if( $show_distance ) :
                    $list[] = $distance . '/' . $opt['distance_unit'];
                endif;
                if( $show_duration ) :
                    $list[] = $n->formatDuration($run_data->metrics->duration);
                endif;
                if( $show_pace ) :
                    $list[] = $pace . '/' . $opt['distance_unit'];
                endif;
                if( $show_calories ) :
                    $list[] = $run_data->metrics->calories;
                endif;

                $list = join( ' - ', $list );
                $content .= $list;
                $content .= '</li>';
            }


            $content .= '</ul>';
        } else {
            $content .= '<table>';
            $content .= '<tr>';
            if( $show_name ) :
                $content .= '<th>'. __( 'Runner', 'nikeplus' ) . '</th>';
            endif;
            if( $show_date ) :
                $content .= '<th>'. __( 'Run Date', 'nikeplus' ) . '</th>';
            endif;
            if( $show_fuel ) :
                $content .= '<th>'. __( 'Fuel', 'nikeplus' ) . '</th>';
            endif;
            if( $show_distance ) :
                $content .= '<th>'. __( 'Distance', 'nikeplus' ) . '</th>';
            endif;
            if( $show_duration ) :
                $content .= '<th>'. __( 'Duration', 'nikeplus' ) . '</th>';
            endif;
            if( $show_pace ) :
                $content .= '<th>'. __( 'Average Pace', 'nikeplus' ) . '</th>';
            endif;
            if( $show_calories ) :
                $content .= '<th>'. __( 'Calories', 'nikeplus' ) . '</th>';
            endif;
            $content .= '</tr>';
            foreach( $query->posts as $post ) {
                $run_data = get_post_meta( $post->ID, 'run_data', 'single' );
                if( 'MI' == $opt['distance_unit'] ) {
                    $distance = $n->toMiles($run_data->metrics->distance);
                    $pace = $n->calculatePace( $run_data->metrics->duration, $run_data->metrics->distance, $toMiles = true );
                } else {
                    $distance = $n->toTwoDecimalPlaces( $run_data->metrics->distance );
                    $pace = $n->calculatePace( $run_data->metrics->duration, $run_data->metrics->distance, $toMiles = false );
                }
                $content .= '<tr>';
                if( $show_name ) :
                    $content .= '<td>';
                        $content .= get_userdata($post->post_author)->display_name;
                    $content .= '</td>';
                endif;
                if( $show_date ) :
                    $content .= '<td>';
                        $content .= date( 'm/d/Y', $run_data->name );
                    $content .= '</td>';
                endif;
                if( $show_fuel ) :
                    $content .= '<td>';
                        $content .= $run_data->metrics->fuel;
                    $content .= '</td>';
                endif;
                if( $show_distance ) :
                    $content .= '<td>';
                        $content .= $distance . '/' . $opt['distance_unit'];
                    $content .= '</td>';
                endif;
                if( $show_duration ) :
                    $content .= '<td>';
                        $content .= $n->formatDuration($run_data->metrics->duration);
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
                        $content .= $run_data->metrics->calories;
                    $content .= '</td>';
                endif;
                $content .= '</tr>';
            }
            $content .= '</table>';
        }
        do_action( 'nikeplus_list_runs_after' );
        return apply_filters( 'nikeplus_list_runs', $content, $args );
    endif;

}

function nikeplus_list_runs_shortcode( $atts ){

    extract( shortcode_atts( nikeplus_list_runs_defaults(), $atts ) );

    return apply_filters( 'nikeplus_list_runs_shortcode', nikeplus_list_runs( $atts ) );

}
add_shortcode( 'list_runs', 'nikeplus_list_runs_shortcode' );

class nikeplus_list_runs extends WP_Widget {

    public function __construct() {
        // widget actual processes
        parent::__construct(
            'nikeplus_list_runs', // Base ID
            __( 'Nike+ List Runs', 'nikeplus' ), // Name
            array( 'description' => __( 'List Nike+ Runs', 'nikeplus' ), ) // Args
        );
    }

    public function form( $instance ) {
        // outputs the options form on admin
        $defaults = nikeplus_list_runs_defaults();
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
        <label for="<?php echo $this->get_field_id( 'runner' ); ?>"><?php _e( 'Runner:' ); ?></label>
        <select id="<?php echo $this->get_field_id( 'runner' ); ?>" name="<?php echo $this->get_field_name( 'runner' ); ?>" type="text">

            <option value=""><?php _e( 'Select runner or enter user IDs below.', 'nikeplus' ); ?></option>
            <?php if( $users ) {
                foreach ($users as $user) {
                    echo '<option value="' . $user->ID . '"' . selected( $runner, $user->ID, true ) . '>' . $user->display_name . '</option>';
                }
            } else {
                echo '<option value="">' . __( 'No Verified Runners', 'nikeplus' ) . '</option>';
            } ?>
        </select>
        <input id="<?php echo $this->get_field_id( 'runners' ); ?>" name="<?php echo $this->get_field_name( 'runners' ); ?>" type="text" value="<?php echo $runners; ?>" />
        </p>
        <p>
        <input id="<?php echo $this->get_field_id( 'num_runs' ); ?>" name="<?php echo $this->get_field_name( 'num_runs' ); ?>" type="text" value="<?php echo $num_runs; ?>" size="1" />
        <label for="<?php echo $this->get_field_id( 'num_runs' ); ?>"><?php _e( '# of runs to display', 'nikeplus' ); ?></label>
        </p>
        <p>
        <p>
        <input id="<?php echo $this->get_field_id( 'show_fuel' ); ?>" name="<?php echo $this->get_field_name( 'show_fuel' ); ?>" type="checkbox" <?php checked( $show_fuel, 1, true ); ?> value="1" />
        <label for="<?php echo $this->get_field_id( 'show_fuel' ); ?>"><?php _e( 'Show Nike Fuel', 'nikeplus' ); ?></label>
        </p>
        <p>
        <input id="<?php echo $this->get_field_id( 'show_name' ); ?>" name="<?php echo $this->get_field_name( 'show_name' ); ?>" type="checkbox" <?php checked( $show_name, 1, true ); ?> value="1" />
        <label for="<?php echo $this->get_field_id( 'show_name' ); ?>"><?php _e( 'Show Runners Name', 'nikeplus' ); ?></label>
        </p>
        <p>
        <input id="<?php echo $this->get_field_id( 'show_date' ); ?>" name="<?php echo $this->get_field_name( 'show_date' ); ?>" type="checkbox" <?php checked( $show_date, 1, true ); ?> value="1" />
        <label for="<?php echo $this->get_field_id( 'show_date' ); ?>"><?php _e( 'Show Date', 'nikeplus' ); ?></label>
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
        $instance['show_fuel'] = $new_instance['show_fuel'];
        $instance['show_name'] = $new_instance['show_name'];
        $instance['show_date'] = $new_instance['show_date'];
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
        $defaults = nikeplus_list_runs_defaults();

        $instance = wp_parse_args( $instance, $defaults );

        $args = wp_parse_args( $args, $instance );

        extract( $args, EXTR_SKIP );

        $title = apply_filters( 'widget_title', $instance['title'] );
            echo $before_widget;
            if ( ! empty( $title ) )
                echo $before_title . $title . $after_title;
                echo apply_filters( 'nikeplus_list_runs_widget', nikeplus_list_runs( $args ) );
            echo $after_widget;
    }

}
add_action( 'widgets_init', create_function( '', 'register_widget( "nikeplus_list_runs" );' ) );

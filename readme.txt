=== Nike+ ===
Contributors: jameslaws, kstover
Tags: Nike+, Nike Plus, Nike, running, widget, shortcode
Requires at least: 3.6
Tested up to: 3.9
Stable tag: 1.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Nike+ lets you brag about you and your teams running progress by displaying your stats right on your WordPress website with widgets & shortcodes.

== Description ==

_This is not an official Nike+ plugin but simply my work to integrate Nike+ stats into the WordPress enviroment._

Nike+ is a personal project for the purpose of posting your Nike+ running stats that you most definitely should be proud of. This plugin can be used for individual or team sites that want to post their collective numbers. Nike+ currently provides a few types of stats.

1. **Individual Totals** - Your individual total stats since you started using Nike+. You can choose from most of Nike+'s metrics to be displayed such as Fuel, Number of Runs, Distance, Duration, Average Pace, and Calories. Choose to display only the stats you want.

1. **Personal Records** - Want to show off your fastest race times, longest runs, or even your most burned calories. You have that option.

1. **Run History** - You can now even display your entire run history, your last # of runs, or just the most recent extremely easily.

1. **Team Totals** - Have multiple runners attached to your site? No problem. Nike+ can display the totals of the entire team so you can show off what you are accomplishing as a combined unit.

You can post these stats with three different methods. You can use the provided widgets, shortcodes, or functions in your page templates. For more information on how to use these methods check out the Frequently Asked Questions section.

= IMPORTANT =

Currently NIke+ plugin requires PHP 5 with cURL and JSON. While these are common it's not beyond belief that not all web hosts have these exact specs set-up in your server.

= Future Plans =

I plan on adding a section very soon where you can manually add official race times for display as well as improved markup for styling. If you would like to see additional features or enhancements I welcome your suggestions.

= Shout Out =

This Nike+ WordPress plugin was built using [Nike+PHP](http://nikeplusphp.org/ "Nike+PHP") for easy connection to your Nike+ data. Nike+PHP is GPL as states here: [License](http://code.google.com/p/nike-plus-php/ "License")

== Installation ==

1. Upload the plugin folder (i.e. nikeplus) to the /wp-content/plugins/ directory of your WordPress installation.
1. Activate the plugin through the 'Plugins' menu in WordPress.
1. Visit your "Edit My Profile" page and enter your Nike+ credentials.
1. Visit the Nike+ settings page under the Nike+ Runs menu to set any additional preferences.
1. Add one of the widgets or shortcodes to your site.
1. Start Bragging!

== Frequently Asked Questions ==

= How do I display my individual running totals? =

You can use the Nike+ Individual Totals Widget, the [individual_totals] shortcode, or the nikeplus_individual_totals() function. Below are all the default options and how to use the shortcode and function.

**Defaults**

        $defaults = array(
            'runner'       => '', // accepts a single user_id
            'show_list'     => false,
            'show_name'     => false,
            'show_runs'     => true,
            'show_distance' => true,
            'show_duration' => true,
            'show_pace'     => true,
            'show_fuel'     => true,
            'show_calories' => true,
        );

In this example we have chosen to use the shortcode to show the totals for user_id 1 as an unordered list and without showing total fuel. Everything else was left with the default options. 1 being true and 0 being false.

        [individual_totals runner=1 show_list=1 show_fuel=0]

To do the same thing with the function you would simply pass an array of arguments to the function and echo it. It would look like this.

        $args = array(
            'runner'    => 1,
            'show_list' => 1,
            'show_fuel' => 0,
        );
        echo nikeplus_individual_totals( $args );

= How do I display my personal records? =

You can use the Nike+ Personal Records Widget, the [personal_records] shortcode, or the nikeplus_personal_records() function. Below are all the default options and how to use the shortcode and function.

**Defaults**

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

In the example below we have chosen to use the shortcode to show the records for user_id 3 our Half Marathon record bu not showing our longest run. Everything else was left with the default options. 1 being true and 0 being false.

        [personal_records runner=3 show_half=1 show_longest=0]

To do the same thing with the function you would simply pass an array of arguments to the function and echo it. It would look like this.

        $args = array(
            'runner'    => 3,
            'show_half' => 1,
            'show_longest' => 0,
        );
        echo nikeplus_personal_records( $args );

= How do I display my run history? =

You can use the Nike+ List Runs Widget, the [list_runs] shortcode, or the nikeplus_list_runs() function. Below are all the default options and how to use the shortcode and function.

**Defaults**

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

In the example below we have chosen to use the shortcode to show the runs for users 1, 3, and 6 while displaying their names but hiding the pace. Everything else was left with the default options. 1 being true and 0 being false.

        [list_runs runners="1,3,6" show_name=1 show_pace=0]

To do the same thing with the function you would simply pass an array of arguments to the function and echo it. It would look like this.

        $args = array(
            'runners'    => "1,3,6",
            'show_name' => 1,
            'show_pace' => 0,
        );
        echo nikeplus_list_runs( $args );

== Screenshots ==

I haven't taken any screenshots yet but you can see my stats displayed on my personal blog: [JamesLaws.com](http://jameslaws.com/ "JamesLaws.com")

== Changelog ==

= 1.1 =
* Upgraged to version 4.5.1 of Nike+PHP class
* Fixed some notices being displayed
* fixed some connectivitely issues some users have been reporting

= 1.0 =
This is a complete overhaul and adds several new features.

* Data is now stored in Custom Post Types, options, amd user meta removing the needs to connect to Nike+ each time the page loads.
* An option to choose how frequently it should check for new stats.
* Now stores Nike+ Credentials under user profile so you can have multiple runners on one site.
* added Personal Records.
* added entire run history.
* added Team Totals.
* added better handling of pace and distance.

= 0.1 =
* Initial release

== Upgrade Notice ==

= 1.1 =
* Upgraged to version 4.5.1 of Nike+PHP class
* Fixed some notices being displayed
* fixed some connectivitely issues some users have been reporting

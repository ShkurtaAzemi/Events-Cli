<?php
/*
Plugin Name: Events
Description: A plugin that fetches data from a json file and displays them as event posts.
Author: Shkurte Azemi
Version: 1.0.0
*/
if (!defined('ABSPATH')) exit; // Exit if accessed directly

require_once plugin_dir_path(__FILE__) . 'inc/Events.php';

if ( defined( 'WP_CLI' ) && WP_CLI ) {

    require_once plugin_dir_path(__FILE__) . 'inc/wp-cli-command.php';


}

//require_once plugin_dir_path(__FILE__) . 'inc/Events.php';


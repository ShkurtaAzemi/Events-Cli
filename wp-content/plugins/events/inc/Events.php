<?php

//create custom post type for events and tags taxonomy
class Events
{

    function __construct()
    {
        add_action('init', array($this, 'create_post_type'));
        add_action('init', array($this, 'create_events_non_hierarchical_taxonomy'));
    }

    function create_post_type()
    {

        $name = 'Events';
        $singular_name = 'Event';
        register_post_type(
            strtolower($name),
            array(
                'labels' => array(
                    'name' => _x($name, 'post type general name'),
                    'singular_name' => _x($singular_name, 'post type singular name'),
                    'menu_name' => _x($name, 'admin menu'),
                    'name_admin_bar' => _x($singular_name, 'add new on admin bar'),
                    'add_new' => _x('Add New', strtolower($name)),
                    'add_new_item' => __('Add New ' . $singular_name),
                    'new_item' => __('New ' . $singular_name),
                    'edit_item' => __('Edit ' . $singular_name),
                    'view_item' => __('View ' . $singular_name),
                    'all_items' => __('All ' .  $name),
                    'search_items' => __('Search ' . $name),
                    'parent_item_colon' => __('Parent :' . $name),
                    'not_found' => __('No ' . strtolower($name) . ' found.'),
                    'not_found_in_trash' => __('No ' . strtolower($name) . ' found in Trash.')
                ),
                'public' => true,
                'hierarchical' => false,
                'show_in_rest' => true,
                'rewrite' => array('slug' => strtolower($name)),
                'menu_icon' => 'dashicons-calendar',
                'supports' => array( 'title', 'editor')
            )
        );

    }

    function create_events_non_hierarchical_taxonomy(){

        $name = 'Tags';
        $singular_name= 'Tag';
        $labels = array(
            'name' => _x($name, 'taxonomy general name'),
            'singular_name' => _x($singular_name, 'taxonomy singular name'),
            'search_items' => __('Search '. $name),
            'all_items' => __('All '.$name),
            'parent_item' => __('Parent '. $singular_name),
            'parent_item_colon' => __('Parent: '. $singular_name),
            'edit_item' => __('Edit '. $singular_name),
            'update_item' => __('Update '. $singular_name),
            'add_new_item' => __('Add '. $singular_name),
            'new_item_name' => __('New '. $singular_name),
            'menu_name' => __($name),
        );

        register_taxonomy('tags', array('events'), array(
            'hierarchical' => false,
            'labels' => $labels,
            'show_ui' => true,
            'show_in_rest' => true,
            'show_admin_column' => true,
            'query_var' => true,
            'rewrite' => array('slug' => 'tags'),
        ));
    }
}

$events = new Events();
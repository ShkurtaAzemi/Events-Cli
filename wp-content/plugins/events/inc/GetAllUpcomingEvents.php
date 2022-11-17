<?php

//creates a Rest API endpoint to show all upcoming events
class GetAllUpcomingEvents
{
    function __construct()
    {
        add_action('rest_api_init', array($this, 'registerRestRoute'));

    }

    function registerRestRoute()
    {
        register_rest_route('events/v1', '/getAllUpcoming', array(
            'methods' => 'GET',
            'callback' => array($this, 'upcoming_events')
        ));

    }

    function upcoming_events()
    {
        $args = array(
            'posts_per_page' => -1,
            'post_type' => 'events',
            'post_status' => 'publish',
            'meta_key' => 'event_time',
            'orderby' => array('meta_value' => 'ASC'),
        );
        $events = new WP_Query($args);

        $structured_events = array();
        $event = array();
        if ($events->have_posts()) :
            while ($events->have_posts()) : $events->the_post();
                $tags = array();
                $terms = get_the_terms(get_the_ID(), 'tags');
                foreach ($terms as $term) {
                    $tags[] = $term->name;
                }
                $event['id'] = get_the_ID();
                $event['title'] = get_the_title();
                $event['about'] = get_the_content();
                $event['organizer'] = get_field('event_organizer');
                $event['timestamp'] = get_field('event_time');
                $event['email'] = get_field('event_email');
                $event['address'] = get_field('event_address');
                $event['latitude'] = get_field('event_latitude');
                $event['longitude'] = get_field('event_longitude');
                $event['tags'] = $tags;
                $structured_events[] = $event;
            endwhile;
        endif;

        if (empty($events)) {
            return new WP_Error('empty_events', 'There are no events to display', array('status' => 404));
        }

        $response = new WP_REST_Response($structured_events);

        $response->set_status(200);

        return $response;


    }


}

$getAllUpcomingEvents = new GetAllUpcomingEvents();
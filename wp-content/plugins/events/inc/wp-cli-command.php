<?php
require_once plugin_dir_path(__FILE__) . 'DatesDifferenceCalculator.php';

class EVENT_IMPORT_COMMAND extends WP_CLI_Command
{
    use DatesDifferenceCalculator;

    function events()
    {
        $args = array(
            'posts_per_page' => -1,
            'post_type' => 'events',
            'post_status' => array('publish', 'draft'),
            'fields' => 'ids'
        );
        $the_query = new WP_Query($args);
        $existing_events_ids = $the_query->posts;
        $existing_events_count = $the_query->found_posts;

        $json = file_get_contents(__DIR__ . '/data.json');

        // Decode the JSON file
        $events = json_decode($json, true);

        $count = count($events);
        $new_events = 0;
        $updated_events = 0;
        $error_message = '';
        $events_dictionary = array();

//        echo "<pre>";
        $progress = \WP_CLI\Utils\make_progress_bar('Importing events', $count);
        $to = 'shkurtaaaa12@gmail.com';
        $subject = 'Events Importer Data';
        $headers = 'From: Events Website <shkurtaazemi.ce@gmail.com>' . "\r\n";


//        foreach ($events as $event) {
        for ($i = 0; $i < $count; $i++) {
            $event = $events[$i];
            $non_hierarchical_terms = $events[$i]['tags']; // Can use array of ids or string of tax names separated by commas
            $organizer = $event['organizer'] ?? '';
            $email = $event['email'] ?? '';
            $address = $event['address'] ?? '';
            $latitude = $event['latitude'] ?? '';
            $longitude = $event['longitude'] ?? '';
            $time = $event['timestamp'] ?? '';
            if (!in_array($events[$i]['id'], $events_dictionary)) {
                $events_dictionary[] = $events[$i]['id'];
            }
            $event_has_passed = null;
            if ($time != '') {
                $event_has_passed = $this->calculateDifference($time);
            }


            $post_arr = array(
                'ID' => $event['id'],
                'post_title' => $event['title'],
                'post_content' => $event['about'],
                'post_type' => 'events',
                'post_author' => get_current_user_id(),
            );

            $post_arr['post_status'] = 'publish';
            if ($event_has_passed['past'] === true) {
                $post_arr['post_status'] = 'draft';
            }
            $post_id = wp_insert_post($post_arr, true);

            if (!is_wp_error($post_id)) {
                if (!in_array($post_id, $existing_events_ids)) {
                    $new_events++;
                } else {
                    $updated_events++;
                }
                wp_set_object_terms($post_id, $non_hierarchical_terms, 'tags');

                update_field('field_63741185320da', $organizer, $post_id);
                update_field('field_637411ae320db', $email, $post_id);
                update_field('field_637411bc320dc', $address, $post_id);
                update_field('field_637411ca320dd', $latitude, $post_id);
                update_field('field_637411e6320de', $longitude, $post_id);
                update_field('field_6375162acfed8', $time, $post_id);


            } else {
                $error_message = $post_id->get_error_message();
            }

            $progress->tick();
        }
        $progress->finish();
        if ($error_message == '') {
            WP_CLI::success('New Events: ' . $new_events . PHP_EOL . 'Updated Events: ' . $updated_events);
            $body = "New events: " . $new_events . "\n" . "Updated events: " . $updated_events . "\n";

        } else {
            WP_CLI::error($error_message);
            $body = "Oops! Something wrong happened while trying to import events please see error below for more information!\n" . $error_message;
        }

        wp_mail($to, $subject, $body, $headers);


    }

}

WP_CLI::add_command('import', 'EVENT_IMPORT_COMMAND');
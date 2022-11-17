<?php
require_once plugin_dir_path(__FILE__) . 'DatesDifferenceCalculator.php';

//creates custom wp-cli command to import events
//example command: wp import events
class EVENT_IMPORT_COMMAND extends WP_CLI_Command
{
    use DatesDifferenceCalculator;

    function events()
    {
        //get all existing events
        $args = array(
            'posts_per_page' => -1,
            'post_type' => 'events',
            'post_status' => array('publish', 'draft'),
            'fields' => 'ids'
        );
        $the_query = new WP_Query($args);
        $existing_events_ids = $the_query->posts;
        $existing_events_count = $the_query->found_posts;

        //read json file from inc folder if the file is not uploaded in plugins options page
        $json = file_get_contents(__DIR__ . '/data.json');

        if(get_option('events_json_file')){
            $json = file_get_contents(get_option('events_json_file'));
        }

        // Decode the JSON file
        $events = json_decode($json, true);

        $count = count($events);
        $new_events = 0;
        $updated_events = 0;
        $error_message = '';

        $progress = \WP_CLI\Utils\make_progress_bar('Importing events', $count);

        //fields to send to email
        $to = get_option('events_email_receiver');
        $subject = 'Events Importer Data';
        $headers = 'From: Events Website <shkurtaazemi.ce@gmail.com>' . "\r\n";

        //loop through all events
        for ($i = 0; $i < $count; $i++) {
            $event = $events[$i];
            $non_hierarchical_terms = $events[$i]['tags'];
            $organizer = $event['organizer'] ?? '';
            $email = $event['email'] ?? '';
            $address = $event['address'] ?? '';
            $latitude = $event['latitude'] ?? '';
            $longitude = $event['longitude'] ?? '';
            $time = $event['timestamp'] ?? '';

            $event_has_passed = null;
            if ($time != '') {
                $event_has_passed = $this->calculateDifference($time);
            }

            //build array of data to be inserted
            $post_arr = array(
                'ID' => $event['id'],
                'post_title' => $event['title'],
                'post_content' => $event['about'],
                'post_type' => 'events',
                'post_author' => get_current_user_id(),
            );

            //check if event has passed and insert/update as a draft post
            $post_arr['post_status'] = 'publish';
            if ($event_has_passed['past'] === true) {
                $post_arr['post_status'] = 'draft';
            }
            $post_id = wp_insert_post($post_arr, true);

            //if an error occurs wp_insert post will return a WP_ERROR
            if (!is_wp_error($post_id)) {
                //if new post is valid

                if (!in_array($post_id, $existing_events_ids)) {
                    //if post doesn't already exist increase counter
                    $new_events++;
                } else {
                    //else means that the post exists and is just being updated
                    $updated_events++;
                }
                //assign tags to post, if the tag doesn't already exist it will be added
                wp_set_object_terms($post_id, $non_hierarchical_terms, 'tags');

                //update acf fields with corresponding data
                update_field('field_63741185320da', $organizer, $post_id);
                update_field('field_637411ae320db', $email, $post_id);
                update_field('field_637411bc320dc', $address, $post_id);
                update_field('field_637411ca320dd', $latitude, $post_id);
                update_field('field_637411e6320de', $longitude, $post_id);
                update_field('field_6375162acfed8', $time, $post_id);


            } else {
                //save the error to a variable to send to receiver email
                $error_message = $post_id->get_error_message();
            }

            $progress->tick();
        }
        $progress->finish();

        //prepare body message to send to email
        if ($error_message == '') {
            WP_CLI::success('New Events: ' . $new_events . PHP_EOL . 'Updated Events: ' . $updated_events);
            $body = "Hello hello,\n  A new Import has finished. Check details below. \n New events: " . $new_events . "\n" . "Updated events: " . $updated_events . "\n Have a nice day!";

        } else {
            WP_CLI::error($error_message);
            $body = "Oops! Something wrong happened while trying to import events please see error below for more information!\n" . $error_message;
        }

        wp_mail($to, $subject, $body, $headers);
    }

}

WP_CLI::add_command('import', 'EVENT_IMPORT_COMMAND');
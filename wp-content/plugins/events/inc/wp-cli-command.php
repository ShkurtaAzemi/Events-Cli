<?php

class EVENT_IMPORT_COMMAND extends WP_CLI_Command
{

    function events()
    {


        $json = file_get_contents(__DIR__ . '/data.json');

        // Decode the JSON file
        $events = json_decode($json, true);

        $count = count($events);
        $new_events = 0;
        $updated_events = 0;
        $error_message='';
        $events_dictionary=array();

//        echo "<pre>";
        $progress = \WP_CLI\Utils\make_progress_bar( 'Importing events', $count );
//        foreach ($events as $event) {
        for($i=0; $i<$count; $i++){
            $non_hierarchical_terms = $events[$i]['tags']; // Can use array of ids or string of tax names separated by commas

            if( !in_array( $events[$i]['id'],$events_dictionary)) {
                $events_dictionary[] = $events[$i]['id'];
            }

            $post_arr = array(
                'ID'=>$events[$i]['id'],
                'post_title' => $events[$i]['title'],
                'post_content' => 'Test post content',
                'post_type'=>'events',
                'post_status' => 'publish',
                'post_author' => get_current_user_id(),
//                'tax_input' => array(
//                    'tags' => $non_hierarchical_terms,
//                ),
//                'meta_input' => array(
//                    'test_meta_key' => 'value of test_meta_key',
//                ),
            );
            $post_id  = wp_insert_post( $post_arr, true );

            if(!is_wp_error($post_id) && !in_array( $post_id,$events_dictionary)){
                $new_events++;
                wp_set_object_terms( $post_id, $non_hierarchical_terms,'tags' );
//                WP_CLI::success( 'Successfully imported '.$new_imports.' events.');
            }elseif (!is_wp_error($post_id) && in_array( $post_id,$events_dictionary)){
                $updated_events++;
                wp_set_object_terms( $post_id, $non_hierarchical_terms,'tags' );
            }
            else{
                //there was an error in the post insertion,
////                echo $post_id->get_error_message();
////                WP_CLI::error( $post_id->get_error_message());
             $error_message = $post_id->get_error_message();
            }

            $progress->tick();
        }
        $progress->finish();
        if($error_message==''){
            WP_CLI::success( 'New Events: '. $new_events.PHP_EOL.'Updated Events: '.$updated_events );
        }else{
            WP_CLI::error( $error_message );
        }




    }

}

WP_CLI::add_command('import', 'EVENT_IMPORT_COMMAND');
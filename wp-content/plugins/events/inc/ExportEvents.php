<?php
class ExportEvents
{
    function __construct()
    {
        add_action('rest_api_init',array($this, 'registerRestRoute'));

    }

    function registerRestRoute(){
        register_rest_route('events/v1', '/export', array(
            'methods' => 'GET',
            'callback' => array($this,'export_events')
        ));

    }
    function export_events(){

        $url      = get_site_url().'/wp-json/eventss/v1/getAllUpcoming';
        $response = wp_remote_get( esc_url_raw( $url ) );

        /* Will result in $api_response being an array of data,
        parsed from the JSON response of the API listed above */
        $data = wp_remote_retrieve_body( $response );
//
//        $response = new WP_REST_Response($structured_events);
//
//        $response->set_status(200);
//
//        $data = json_encode($structured_events);
        $upload_dir = wp_get_upload_dir(); // set to save in the /wp-content/uploads folder
        $file_name = date('Y-m-d') . '.json';
        $save_path = $upload_dir['basedir'] . '/' . $file_name;

        $f = fopen($save_path, "w"); //if json file doesn't gets saved, comment this and uncomment the one below
        //$f = @fopen( $save_path , "w" ) or die(print_r(error_get_last(),true)); //if json file doesn't gets saved, uncomment this to check for errors
        fwrite($f, $data);
        fclose($f);

        header('Content-type: application/pdf',true,200);
        header('Content-Disposition: attachment; filename="'.$file_name.'"');
        header('Cache-Control: public');
        readfile($save_path);

        $response = new WP_REST_Response($data);

        $response->set_status(200);
        return $response;


    }



}

$exportEvents = new ExportEvents();
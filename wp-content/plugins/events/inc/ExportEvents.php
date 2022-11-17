<?php

//creates a Rest API endpoint to export all upcoming events
class ExportEvents
{
    function __construct()
    {
        add_action('rest_api_init', array($this, 'registerRestRoute'));

    }

    function registerRestRoute()
    {
        register_rest_route('events/v1', '/export', array(
            'methods' => 'GET',
            'callback' => array($this, 'export_events')
        ));

    }

    function export_events()
    {

        $url = get_site_url() . '/wp-json/events/v1/getAllUpcoming';
        $response = wp_remote_get(esc_url_raw($url));

        $data = wp_remote_retrieve_body($response);

        if (empty($data)) {
            return new WP_Error('empty_events', 'There are no events to display', array('status' => 404));
        }
        $upload_dir = wp_get_upload_dir(); // set to save in the /wp-content/uploads folder
        $file_name = date('Y-m-d') . '.json';
        $save_path = $upload_dir['basedir'] . '/' . $file_name;

        $f = fopen($save_path, "w");
        fwrite($f, $data);
        fclose($f);

        header('Content-type: application/pdf', true, 200);
        header('Content-Disposition: attachment; filename="' . $file_name . '"');
        header('Cache-Control: public');
        readfile($save_path);

        $response = new WP_REST_Response($data);

        $response->set_status(200);
        return $response;


    }


}

$exportEvents = new ExportEvents();
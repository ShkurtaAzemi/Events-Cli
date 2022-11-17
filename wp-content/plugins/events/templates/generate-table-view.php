<?php
//fetches all upcoming events from rest api endpoint and shows them in a datatable
function generateTableView()
{
    $response = wp_remote_get(get_site_url() . '/wp-json/events/v1/getAllUpcoming');
    if ((!is_wp_error($response)) && (200 === wp_remote_retrieve_response_code($response))) :
        $events = json_decode($response['body']);
        if (json_last_error() === JSON_ERROR_NONE) :

            ob_start(); ?>
            <section class="events-table">
                <div class="container-fluid">
                    <div class="row title-row">
                        <h1> Upcoming Events</h1>
                    </div>
                    <div class="row buttons-row">
                        <div class="show-data-button">
                            <a href="<?php echo get_site_url() . '/wp-json/events/v1/getAllUpcoming' ?>"
                               target="_blank">Show Json</a>
                        </div>
                        <div class="export-data-button">
                            <a href="<?php echo get_site_url() . '/wp-json/events/v1/export' ?>">Export Json</a>
                        </div>
                    </div>

                </div>
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <table id="example" class="table table-striped table-bordered dt-responsive"
                                   style="width:100%">
                                <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>About</th>
                                    <th>Organizer</th>
                                    <th>Email</th>
                                    <th>Address</th>
                                    <th>Latitude</th>
                                    <th>Longitude</th>
                                    <th>Tags</th>
                                    <th>Remaining Time</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($events as $event):
                                    $title = $event->title;
                                    $about = $event->about;
                                    $trimmed_about = wp_trim_words($about, 5, '...');
                                    $organizer = $event->organizer;
                                    $email = $event->email;
                                    $address = $event->address;
                                    $latitude = $event->latitude;
                                    $longitude = $event->longitude;
                                    $date_difference = DatesDifferenceCalculator::calculateDifference($event->timestamp);
                                    $remaining_time = $date_difference['remaining_time'];
                                    $tags = implode(', ', $event->tags);
                                    ?>
                                    <tr>
                                        <td><?php echo $title ?></td>
                                        <td data-title="<?php echo $about ?>"><?php echo $trimmed_about ?></td>
                                        <td><?php echo $organizer ?></td>
                                        <td><?php echo $email ?></td>
                                        <td><?php echo $address ?></td>
                                        <td><?php echo $latitude ?></td>
                                        <td><?php echo $longitude ?></td>
                                        <td><?php echo $tags ?></td>
                                        <td>In <?php echo $remaining_time ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                <tr>
                                    <th>Title</th>
                                    <th>About</th>
                                    <th>Organizer</th>
                                    <th>Email</th>
                                    <th>Address</th>
                                    <th>Latitude</th>
                                    <th>Longitude</th>
                                    <th>Tags</th>
                                    <th>Remaining Time</th>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </section>
            <?php
            return ob_get_clean();
        endif;
    endif;

}
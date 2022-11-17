# Events Plugin

Events Plugin is a Wordpress Plugin that fetches json data from an uploaded file and imports them into Events posts.

## Setup

1. Clone this repository in your working directory.
2. Run the Wordpress install
3. Add this constant in wp-config file  in order to be able to upload json files in the uploads directory.

```php
define( 'ALLOW_UNFILTERED_UPLOADS', true );
```
4. Activate ACF plugin in Plugins page in dashboard.
5. Activate Events Plugin.
6. Fill in the Settings Option fields in the Settings->Events Settings.
7. Activate Events Theme in Appearance->Events Theme.
8. Go to Custom Fields in dashboard menu and then under Sync Available hover over Local JSON and click Import to import needed fields.
9. Make any page as the front-page of the theme in Settings->Reading -> Your homepage displays -> Static Page -> Homepage: Any page available or a new one (e.g Sample Page) and hit Save Changes.


## Usage

To Import Events run the following command:

```wp-cli
wp import events
```

Upcoming events will be showing in the frontpage of the website.

- To Show Data in json format go to the homepage of website and click Show Json button.
- To Export Data in json file go to the homepage of website and click Export Json button.

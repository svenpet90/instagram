[![StandWithUkraine](https://raw.githubusercontent.com/vshymanskyy/StandWithUkraine/main/badges/StandWithUkraine.svg)](https://github.com/vshymanskyy/StandWithUkraine/blob/main/docs/README.md)
[![TYPO3 10](https://img.shields.io/badge/TYPO3-10-orange.svg)](https://get.typo3.org/version/10)
[![TYPO3 11](https://img.shields.io/badge/TYPO3-11-orange.svg)](https://get.typo3.org/version/11)
[![Latest Stable Version](http://poser.pugx.org/svenpetersen/instagram/v)](https://packagist.org/packages/svenpetersen/instagram)
[![Total Downloads](http://poser.pugx.org/svenpetersen/instagram/downloads)](https://packagist.org/packages/svenpetersen/instagram)
[![Latest Unstable Version](http://poser.pugx.org/svenpetersen/instagram/v/unstable)](https://packagist.org/packages/svenpetersen/instagram)
[![License](http://poser.pugx.org/svenpetersen/instagram/license)](https://packagist.org/packages/svenpetersen/instagram)
[![PHP Version Require](http://poser.pugx.org/svenpetersen/instagram/require/php)](https://packagist.org/packages/svenpetersen/instagram)

TYPO3 Extension "instagram"
=================================

## What does it do?
TYPO3 Extension to import and display instagram posts/feeds in a TYPO3 Website.

Creates and auto-refreshes long-lived api access tokens, imports
instagram feeds/posts as entities to the database and output feeds/posts via
Frontend-Plugin.

**Summary of features**

* Backend module for easy creation of long-lived API access tokens
* Automatic refreshing of API access tokens to keep them valid
* Import multiple Instagram users feeds/posts
* Uses official Instagram-API to access a users feed.
* Provides commands to refresh access tokens and to import feeds via cronjob/scheduler
* Downloads the posts (images/videos). No API calls needed when displaying them = no frontend performance impact.
* Display users feeds/posts in any way you like
* Based on Extbase and Fluid

## Installation
The recommended way to install the extension is by using [Composer](https://getcomposer.org/). In your Composer based TYPO3 project root, just do:
<pre>composer require svenpetersen/instagram</pre>

## Setup
1. Include the provided static TypoScript
2. Create a Facebook "Instagram Basic Display" App: See the
   [official Documentation](https://developers.facebook.com/docs/instagram-basic-display-api/getting-started)
   for a step by step guide
3. Use the Backend Module provided by this Extension to create a "long-lived
   access token" in a "Feed" Entity.
4. Execute the command <code>instagram:import:posts {username}
   {storagePid} [limit|25]</code> to import the posts from a given users feed
5. Add a Frontend-Plugin on a page to output the imported posts in the frontend.

__Recommended__:

* Add a cronjob/scheduler task to refresh the API Access tokens automatically -
  see "Automatic Access Token refresh" for details.
* Add a cronjob/scheduler task to import the posts on a regular basis

## Compatibility
| Version | TYPO3       | PHP        | Support/Development                  |
|---------|-------------|------------|--------------------------------------|
| 1.x     | 10.4 - 11.5 | 7.4 - 8.0️ | Features, Bugfixes, Security Updates |

## Funtionalities

### Automatic import of posts
This extension comes with a command to import (new) posts of a given instagram
user.
It is recommended to set this command up to run regularly - e.g. once a day.

<pre>instagram:import:posts {username} {storagePid} [limit|25] [--since="01/01/2022 00:00:00" --until="12/31/2022 23:59:59"
]</pre>

__Arguments:__

| Name       | Description                                                   |
|------------|---------------------------------------------------------------|
| username   | The instagram username to import posts for                    |
| storagePid | The PID to save the imported posts                            |
| limit      | The maximum number of posts to import (Optional. Default: 25) |

__Options:__

| Name    | Description                                                    |
|---------|----------------------------------------------------------------|
| --since | Date string to fetch posts since (Format: "MM/DD/YYYY H:i:s"). |
| --until | Date string to fetch posts until (Format: "MM/DD/YYYY H:i:s"). |


### Automatic Access Token Refreshing
The generated long-lived access token is valid for 60 days.
It can be refreshed when at least 24 hours old.

To handle automatic refreshing of your access tokens this extension provides the
command
<pre>instagram:accesstoken:refresh-all</pre>

Make sure to run this command regularly - e.g. once a day via a
cronjob/scheduler - in order to keep your access token valid.

### Disable/enable Backend module "Token Generator"
To disable the Backend module - e.g. after you generated all needed tokens - add
this
line to the <code>LocalConfiguration.php/AdditionalConfiguration.php</code>:
<pre>$GLOBALS['TYPO3_CONF_VARS']['SYS']['features']['instagram.tokenGeneratorBeModule'] = false;</pre>

## Extending

### Additional Template Selector
If you need a kind of template selector inside a plugin, you can add your own selections by adding those to:
<pre>$GLOBALS['TYPO3_CONF_VARS']['EXT']['instagram']['templateLayouts']['myext'] = ['My Title', 'my value'];</pre>

### Local path to save downloaded files
By default all images/videos in imported posts are saved in <code>/public/fileadmin/instagram</code>
You can change this path via the Extensions settings <code>local_file_storage_path</code> option.

## Contributing
Please refer to the [contributing](CONTRIBUTING.md) document included in this repository.

## Testing
This Extension comes with a testsuite for coding styles and unit/functional
tests.
To run the tests simply use the provided composer script:

<pre>composer ci:test</pre>

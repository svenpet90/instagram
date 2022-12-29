[![StandWithUkraine](https://raw.githubusercontent.com/vshymanskyy/StandWithUkraine/main/badges/StandWithUkraine.svg)](https://github.com/vshymanskyy/StandWithUkraine/blob/main/docs/README.md)
[![TYPO3 10](https://img.shields.io/badge/TYPO3-10-orange.svg)](https://get.typo3.org/version/10)
[![TYPO3 11](https://img.shields.io/badge/TYPO3-11-orange.svg)](https://get.typo3.org/version/11)
[![Code Coverage Badge](https://github.com/svaen90/instagram/blob/code-coverage-badge/badge.svg)](https://github.com/svaen90/instagram/blob/code-coverage-badge/clover.xml)
[![CI Status](https://github.com/svaen90/instagram/workflows/CI/badge.svg)](https://github.com/svenpet90/instagram/actions)
[![Latest Stable Version](http://poser.pugx.org/svenpetersen/instagram/v)](https://packagist.org/packages/svenpetersen/instagram)
[![Total Downloads](http://poser.pugx.org/svenpetersen/instagram/downloads)](https://packagist.org/packages/svenpetersen/instagram)
[![Latest Unstable Version](http://poser.pugx.org/svenpetersen/instagram/v/unstable)](https://packagist.org/packages/svenpetersen/instagram)
[![License](http://poser.pugx.org/svenpetersen/instagram/license)](https://packagist.org/packages/svenpetersen/instagram)
[![PHP Version Require](http://poser.pugx.org/svenpetersen/instagram/require/php)](https://packagist.org/packages/svenpetersen/instagram)

__EXPERIMENTAL__ This extension is currently still in active development and is
likely to change, or even change drastically.

# instagram

TYPO3 Extension to create and auto-refresh long-lived api access tokens, import
instagram feeds as entities to the database and output feeds via
Frontend-Plugin.

## Installation

<pre>composer require svenpetersen/instagram</pre>

## Setup

1. Include the provided static TypoScript
2. Create a Facebook "Instagram Basic Display" App: See the
   official [Documentation](https://developers.facebook.com/docs/instagram-basic-display-api/getting-started)
   for a step by step guide
3. Use the Backend Module provided by this Extension to create a "long-lived
   access token" in a "Feed" Entity.
4. Execute the command <code>instagram:import:posts {username}
   {storagePid} [limit|25]</code> to import the posts from a given users feed
5. Add a Frontend-Plugin on a page to output the imported posts in the frontend.

__Recommended__:

* Add a cronjob/scheduler task to refresh the API Access tokens automatically -
  see "Automatic Access Token refresh" for details.
* Add a cronjob/scheduler task to import the postings on a regular basis

## Funtionalities

### Enable/disable Backend Modul "Token Generator"

To disable the BE Module - e.g. after you generated all needed tokens - add this
line to the <code>AdditionalConfiguration.php</code>:
<pre>$GLOBALS['TYPO3_CONF_VARS']['SYS']['features']['instagram.tokenGeneratorBeModule'] = false;</pre>

### Automatic Access Token Refreshing

The generated long-lived access token is valid for 60 days.
It can be refreshed when at least 24 hours old.

To handle automatic refreshing of your access tokens this extension provides the
command
<pre>instagram:accesstoken:refresh-all</pre>

Make sure to run this command regularly - e.g. once a day via a
cronjob/scheduler - in order to keep your access token valid.


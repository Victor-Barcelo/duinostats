Endroid Twitter Bundle
======================

*By [endroid](http://endroid.nl/)*

[![Build Status](https://secure.travis-ci.org/endroid/EndroidTwitterBundle.png)](http://travis-ci.org/endroid/EndroidTwitterBundle)
[![Latest Stable Version](https://poser.pugx.org/endroid/twitter-bundle/v/stable.png)](https://packagist.org/packages/endroid/twitter-bundle)
[![Total Downloads](https://poser.pugx.org/endroid/twitter-bundle/downloads.png)](https://packagist.org/packages/endroid/twitter-bundle)

This bundle enables you to use Endroid [`Twitter`](https://github.com/endroid/Twitter) as a service in your Symfony project.
It also provides an API controller that takes a local API request, adds an OAuth signature to it and returns the corresponding
Twitter API response. This enables you to expose the Twitter API on your own domain without having to bother about OAuth
signing your requests.

For more information see the [endroid/Twitter](https://github.com/endroid/Twitter) repository and the [Twitter API](https://dev.twitter.com/docs/api/1.1).

[![knpbundles.com](http://knpbundles.com/endroid/EndroidTwitterBundle/badge-short)](http://knpbundles.com/endroid/EndroidTwitterBundle)

## Requirements

* Symfony
* Dependencies:
 * [`Buzz`](https://github.com/kriswallsmith/Buzz)
 * [`Twitter`](https://github.com/endroid/Twitter)

## Installation

### Add in your composer.json

```js
{
    "require": {
        "endroid/twitter-bundle": "dev-master"
    }
}
```

### Install the bundle

``` bash
$ curl -s http://getcomposer.org/installer | php
$ php composer.phar update endroid/twitter-bundle
```

Composer will install the bundle to your project's `vendor/endroid` directory.

### Enable the bundle via the kernel

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Endroid\Bundle\TwitterBundle\EndroidTwitterBundle(),
    );
}
```

## Configuration

### config.yml

```yaml
endroid_twitter:
    consumer_key: "..."
    consumer_secret: "..."
    access_token: "..."
    access_token_secret: "..."
```

## Routing

If you don't want to expose the Twitter API via your application, you can skip this section.

``` yml
EndroidTwitterBundle:
    resource:	"@EndroidTwitterBundle/Controller/"
    type:		annotation
    prefix:		/twitterapi
```

This exposes the Twitter API via <yourdomain>/twitterapi. This means that instead of sending a signed request to
https://dev.twitter.com/docs/api/1.1/* you can now send an unsigned request to <yourdomain>/twitterapi/*. Make sure you
secure this area if you don't want others to be able to post on your behalf.

## Usage

After installation and configuration, the service can be directly referenced from within your controllers.

```php
<?php

$twitter = $this->get('endroid.twitter');

// Retrieve the user's timeline
$tweets = $twitter->getTimeline(array(
    'count' => 5
));

// Or retrieve the timeline using the generic query method
$response = $twitter->query('statuses/user_timeline', 'GET', 'json', $parameters);
$tweets = json_decode($response->getContent());

```

## License

This bundle is under the MIT license. For the full copyright and license information, please view the LICENSE file that
was distributed with this source code.

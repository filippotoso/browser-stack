# BrowserStack API Client
A simple client for browserstack.com API.

## Requirements

- PHP 5.6+
- guzzlehttp/guzzle 6.2+

## Installing

Use Composer to install it:

```
composer require filippo-toso/browser-stack
```

## Using It

```
use FilippoToso\BrowserStack\Client as BrowserStack;

$client = new BrowserStack('username', 'access_key');

// Get list of available OS and browsers
$browsers = $client->getBrowsers();

// Request the generation of a screenshot
$url = 'https://efes.to/';
$job = $client->generateScreenshots($url, $browsers);

// Get the list of screenshots and their states
$result = $client->getJob($job['job_id']);
```

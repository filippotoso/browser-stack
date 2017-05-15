<?php

namespace FilippoToso\BrowserStack;

use GuzzleHttp\Client as HTTPClient;
use GuzzleHttp\Exception\BadResponseException;

class Client
{

    protected $username = null;
    protected $access_key = null;

    /**
     * Create an instance of the client.
     * @param String $username        BrowserStack username
     * @param String $access_key     BrowserStack access key
     */
    public function __construct($username, $access_key) {
        $this->username = $username;
        $this->access_key = $access_key;
    }

    /**
     * Execute an HTTP GET request to BrowserStack API
     * @param  String $url The url of the API endpoint
     * @return Array|FALSE  The result of the request
     */
    protected function get($url) {

        $client = new HTTPClient();

        try {
            $res = $client->request('GET', $url, [
                'auth' => [$this->username, $this->access_key],
                'headers' => [
                    'Accept'     => 'application/json',
                ],
            ]);
        }
        catch (BadResponseException $e) {
            return FALSE;
        }

        $data = json_decode($res->getBody(), TRUE);

        return $data;

    }

    /**
     * Execute an HTTP POST request to BrowserStack API
     * @param  String $url The url of the API endpoint
     * @param  Array $data The parameters of the request
     * @return Array|FALSE  The result of the request
     */
    protected function post($url, $data) {

        $client = new HTTPClient();

        try {
            $res = $client->request('POST', $url, [
                'json' => $data,
                'auth' => [$this->username, $this->access_key],
                'headers' => [
                    'Accept'     => 'application/json',
                ],
            ]);
        }
        catch (BadResponseException $e) {
            return FALSE;
        }

        $data = json_decode($res->getBody(), TRUE);

        return $data;

    }

    /**
     * Get list of available OS and browsers
     * @return Array The list of available OS and browsers
     */
    public function getBrowsers() {
        return $this->get('https://www.browserstack.com/screenshots/browsers.json');
    }

    /**
     * Request the generation of a screenshot
     * @param  string  $url          The URL of the desired page.
     * @param  Array   $browsers     The details of the browser that will be used to generate the screenshots (Fields: os, os_version, bowser, browser_version, device)
     * @param  string  $orientation  Required if specifying the screen orientation for the device (Values: portrait, landscape - Default: portrait)
     * @param  string  $mac_res      Required if specifying the screen resolution for browsers on OSX (Values: 1024x768, 1280x960, 1280x1024, 1600x1200, 1920x1080 - Default: 1024x768 )
     * @param  string  $win_res      Required if specifying the screen resolution for browsers on Windows (Values: 1024x768, 1280x1024 - Default: 1024x768)
     * @param  string  $quality      Required if specifying the quality of the screenshot (Values: original, compressed - Default: compressed)
     * @param  integer $wait_time    Required if specifying the time (in seconds) to wait before taking the screenshot (Values: 2, 5, 10, 15, 20, 60 - Default: 5)
     * @param  string  $callback_url Required if results are to be sent back to a public URL (Default: null)
     * @param  boolean $local        Required if the page is local and that a Local Testing connection has been set up (Default: false)
     * @return Array                The details about the job created for the screenshot activity
     */
    public function generateScreenshots($url, Array $browsers, $orientation = 'portrait', $mac_res = '1024x768', $win_res = '1024x768', $quality = 'compressed', $wait_time = 5, $callback_url = null, $local = FALSE) {

        $data = [
            'url' => $url,
            'win_res' => $win_res,
            'mac_res' => $mac_res,
            'quality' => $quality,
            'wait_time' => $wait_time,
            'orientation' => $orientation,
            'local' => $local,
        ];

        if (!is_null($callback_url)) {
            $data['callback_url'] = $callback_url;
        }

        return $this->post('https://www.browserstack.com/screenshots', $data);

    }

    /**
     * Generate the list of screenshots and their states
     * @param  String $job_id The job ID you are requesting details about
     * @return Array         The details of the provided screenshot job
     */
    public function getJob($job_id) {
        return $this->get(sprintf('https://www.browserstack.com/screenshots/%s.json', urlencode($job_id)));
    }

}

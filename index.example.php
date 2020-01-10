<?php
/**
 * Dduers\IcecastStreamProxy
 * by Daniel Duersteler, 2020, https://github.com/dduers
 * this is an example configuration
 */

/**
 * allow php script to run forever
 * this needs to be supported by your webserver and php installation
 * otherwise the stream will break after a certain few seconds
 */
set_time_limit (0);

/**
 * include the icecast stream proxy class
 */
require_once 'src/dduers/icecaststreamproxy.php';

/**
 * create class instance with your configuration
 */
$streamproxy = new \Dduers\IcecastStreamProxy(array(

    /**
     * the root url of your icecast server
     * change this, if your icecast server is not running on the same host as the 
     * icecast stream proxy does
     * note: no trailing '/'
     * default: 'http://localhost:8000'
     */
    'ic_url' => 'http://localhost:8000',

    /**
     * stats page of the ice cast server
     * the stats are available with /?status on the url where this script is hosted
     * note: leading '/'
     * default: '/status-json.xsl'
     */
    'ic_mount_stats' => '/status-json.xsl',

    /**
     * mime type of the stats
     * default: 'application/json'
     */
    'ic_mount_stats_mime' => 'application/json',
    
    /**
     * ogg encoded mount
     * default for all browsers as long no mp3 mount is specified
     * note: leading '/'
     * default: '/stream.ogg'
     */
    'ic_mount_ogg' => '/stream.ogg',
    
    /**
     * mp3 encoded mount
     * if speciefied, microsoft browsers fall back to it
     * note: leading '/'
     * default: NULL
     */
    'ic_mount_mp3' => '/stream.mp3',

    /**
     * http basic auth credentials
     * if one of those parameters is missing, http basic auth is disabled
     * note: the stats page and all streams must share the same credentials
     * note: your icecast server must be setup to supprt http basic auth
     * default: both NULL
     */
    'ic_basic_auth_username' => NULL,
    'ic_basic_auth_password' => NULL,
    
    /**
     * frontend website
     * if you give an url here, all stream and stats requests are
     * limited to this referer (uses http referer header)
     * note: no trailing '/'
     * default: NULL
     */
    'sp_lock_http_referer' => 'https://www.radiostream.ch',

    /**
     * name of the getvar to query stats
     * you can get the icecasts stats with a call to (proxy url)/?status
     * default: 'status'
     */
    'sp_stats_get_var' => 'status',

    /**
     * size in bytes of a single chunk to read
     * default: 1024 * 1024
     */
    'sp_stream_chunk_size' => 1024 * 1024
));

/**
 * uncomment this line, when you're ready
 */
//$streamproxy->run();

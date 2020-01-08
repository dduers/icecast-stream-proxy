<?php
require 'icecastStreamProxy.class.php';
$streamproxy = new icecastStreamProxy(array(

    /**
     * the root url of your icecast server
     */
    'ic_url' => 'https://my.icecast-server.org',
    
    /**
     * ogg and mp3 mounts
     * if a mp3 mount is given, it will fall back to it for microsoft browsers
     */
    'ic_mount_ogg' => '/stream.ogg',
    'ic_mount_mp3' => '/stream.mp3',
    
    /**
     * stats page of the ice cast server
     * the stats are available with /?status on the url where this script is hosted
     */
    'ic_mount_stats' => '/status-json.xsl',
    
    /**
     * http basic auth credentials
     * if one of those parameters is missing, http basic auth is disabled
     * note: the stats page and all streams must share the same credentials
     * note: your icecast server must be setup to supprt http basic auth
     */
    'ic_basic_auth_username' => 'proxy',
    'ic_basic_auth_password' => 'proxy_hackme',
    
    /**
     * frontend website
     * if you give an url here, all stream and stats requests are
     * limited to this referer (uses http referer header)
     */
    'sp_lock_http_referer' => 'https://www.my-cool-radio-station.org'
));
$streamproxy->run();

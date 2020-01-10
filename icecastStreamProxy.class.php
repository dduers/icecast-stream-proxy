<?php
/**
 * icecastStreamProxy
 * by Daniel Duersteler, 2020, https://github.com/dduers
 */
class icecastStreamProxy
{
    private $config;
    private $defaults = array(
        'ic_url' => 'http://localhost:8000',
        'ic_mount_stats' => '/status-json.xsl',
        'ic_mount_stats_mime' => 'application/json',
        'ic_mount_ogg' => '/stream.ogg',
        'ic_mount_mp3' => NULL,
        'ic_basic_auth_username' => NULL,
        'ic_basic_auth_password' => NULL,
        'sp_lock_http_referer' => NULL,
        'sp_stats_get_var' => 'status',
        'sp_stream_chunk_size' => 1024 * 1024
    );
    private $stream_file = NULL;
    private $stream_mime = NULL;

    function __construct(array $options = array())
    {
        $this->config = array_merge($this->defaults, $options);
        if ($this->config['ic_url'] && ($this->config['ic_mount_ogg'] || $this->config['ic_mount_mp3'])) {
            if ($this->is_microsoft_browser()) {
                if ($this->config['ic_mount_mp3']) {
                    $this->stream_file = $this->config['ic_url'] . $this->config['ic_mount_mp3'];
                    $this->stream_mime = 'audio/mpeg';
                } elseif ($this->config['ic_mount_ogg']) { 
                    $this->stream_file = $this->config['ic_url'] . $this->config['ic_mount_ogg'];
                    $this->stream_mime = 'audio/ogg';
                }
            } else {
                if ($this->config['ic_mount_ogg']) {
                    $this->stream_file = $this->config['ic_url'] . $this->config['ic_mount_ogg'];
                    $this->stream_mime = 'audio/ogg';
                } elseif ($this->config['ic_mount_mp3']) {
                    $this->stream_file = $this->config['ic_url'] . $this->config['ic_mount_mp3'];
                    $this->stream_mime = 'audio/mpeg';
                }
            }
        }
    }
    
    function run()
    {
        if ($this->config['sp_lock_http_referer'] && !isset($_SERVER['HTTP_REFERER']))
            return false;
        if ($this->config['sp_lock_http_referer'] && (strpos($_SERVER['HTTP_REFERER'], $this->config['sp_lock_http_referer'].'/') === false))
            return false;
        if(isset($_GET[$this->config['sp_stats_get_var']]))
            return $this->ic_get_stats();
        return $this->ic_start_stream();
    }

    private function ic_start_stream()
    {
        if (!($this->stream_file && $this->stream_mime))
            return false;
        $context = NULL;
        if ($this->config['ic_basic_auth_username'] && $this->config['ic_basic_auth_password']) {
            $context = stream_context_create(array(
                'http' => array(
                    'header' => 'Authorization: Basic ' . base64_encode($this->config['ic_basic_auth_username'] . ':' . $this->config['ic_basic_auth_password'])
                )
            ));
            
        }
        $handle = fopen($this->stream_file, 'rb', false, $context);
        if ($handle === false)
            return false;
        if ($this->stream_mime)
            header('Content-Type: ' . $this->stream_mime);
        while (!feof($handle)) {
            echo fread($handle, $this->config['sp_stream_chunk_size']);
            ob_flush();
            flush();
        }
        return fclose($handle); 
    }

    private function ic_get_stats()
    {
        if (!($this->config['ic_url'] && $this->config['ic_mount_stats'] && $this->config['ic_mount_stats_mime']))
            return false;
        $context = NULL;
        if ($this->config['ic_basic_auth_username'] && $this->config['ic_basic_auth_password']) {
            $context = stream_context_create(array(
                'http' => array(
                    'header' => 'Authorization: Basic ' . base64_encode($this->config['ic_basic_auth_username'] . ':' . $this->config['ic_basic_auth_password'])
                )
            ));
        }
        $content = file_get_contents($this->config['ic_url'] . $this->config['ic_mount_stats'], false, $context);
        if ($content === false)
            return false;
        if ($this->config['sp_lock_http_referer'])
            header('Access-Control-Allow-Origin: ' . $this->config['sp_lock_http_referer']);
        else 
            header('Access-Control-Allow-Origin: *');
        if ($this->config['ic_mount_stats_mime'])
            header('Content-Type: ' . $this->config['ic_mount_stats_mime']);
        echo $content;
        return true;
    }
    
    private function is_microsoft_browser()
    {
        return 
            preg_match('/Edge/i', $_SERVER['HTTP_USER_AGENT']) 
            || preg_match('~MSIE|Internet Explorer~i', $_SERVER['HTTP_USER_AGENT']) || (strpos($_SERVER['HTTP_USER_AGENT'], 'Trident/7.0; rv:11.0') !== false);
    }
}

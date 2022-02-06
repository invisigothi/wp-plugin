<?php
namespace App\Base;

use App\Base\BaseController;
use App\Config\Config;
use App\Admin\Admin;

class Redirects extends BaseController
{
    private $host;
    private $protocol;
    private $port;
    private $currentUri;
    private $currentOptions = array();

    public function __construct()
    { 
        add_action('wp', array(
            $this,
            'init'
        ));
        $this->host =  $_SERVER["SERVER_NAME"];
        $this->protocol = !empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] != "off" ? "https" : "http";
        $this->port = !empty($_SERVER["SERVER_PORT"]) && $_SERVER["SERVER_PORT"] != "80" && $_SERVER["SERVER_PORT"] != "443" ? (":" . $_SERVER["SERVER_PORT"]) : "";
        $this->currentUri = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
        $this->currentOptions[] = [
            "ro_ss" => "on",
            "ro_404" => "on",
        ];
    }

    public function init() 
    {
        if (strtoupper($_SERVER['REQUEST_METHOD']) != "GET" && strtoupper($_SERVER['REQUEST_METHOD']) != "HEAD") {
            return;
        }
        $currentUri = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
        
        if ($this->currentOptions[0]["ro_ss"] == "on") {
            $changed = false;
            $u = parse_url($currentUri);
            if ($this->currentOptions[0]["ro_ss"] == "on") {
                $extensions = array (
                    ".php" => -4,
                    ".html" => -5,
                    ".htm" => -4,
                );
                $tmp = basename(rtrim($u["path"], "/"));
                if (substr($u["path"], -1, 1) != "/") {
                    $u["path"] .= "/";
                }
                foreach ($extensions as $ext => $value) {
                    if ( substr($tmp, $value) == $ext) {
                     $u["path"] =  str_replace("index" .$ext, "", $currentUri);
                     $this->localRedirect($u['path']);
                 }  
                }
            }
        }
}
    public function localRedirect($url)
    {
        header('Location: ' . $this->protocol . '://' . $this->host . $this->port . $url, true, 301);
        exit;
    }
}

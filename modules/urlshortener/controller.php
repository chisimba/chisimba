<?php

class urlshortener extends controller
{
    private $objOps;

    public function init()
    {
        $this->objOps = $this->getObject('urlshortenerops', 'urlshortener');
    }

    public function dispatch()
    {
        $action = $this->getParam('action');
        $url = $this->getParam('url');

        switch ($action) {
            case 'shorten':
                header('Content-Type: text/plain');
                echo $this->objOps->getShort($url);
                break;
            case 'lengthen':
                header('Content-Type: text/plain');
                echo $this->objOps->getLong($url);
                break;
            case 'redirect':
                $long = $this->objOps->getLong($url);
                if ($short === FALSE) {
                    header('HTTP/1.1 404 Not Found');
                } else {
                    header('HTTP/1.1 301 Moved');
                    header('Location: ' . $long);
                }
            default:
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    $this->url = $url;
                    $this->short = $this->objOps->getShort($url);
                }
                return 'urlshortener_tpl.php';
        }
    }
}

<?php

namespace Controller;

use Inphinit\Viewing\View;

class Home
{
    const NOT_ALLOWED_EXTENSIONS = [
        "php", "xphp", "php5"
    ];

    public function fetch($resource)
    {
        header("Cache-Control: max-age=31536000");
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, OPTIONS');
        header("Access-Control-Allow-Headers: *");
        
        $resourceFile = INPHINIT_PUBLIC . $resource;
        if (is_file($resourceFile)) {
            $pinfo = pathinfo($resourceFile);
            if (in_array($pinfo["extension"], static::NOT_ALLOWED_EXTENSIONS)) {
                header('X-Proj-Status: DENIED');
                http_response_code(403);
                die();
            } else {
                header('Content-Type: ' . mime_content_type($resourceFile));
                header('Content-Disposition: inline; filename="' . $pinfo["basename"] . '"');
                header('X-Proj-Status: FOUND');
                readfile($resourceFile);
            }
        } else {
            header('X-Proj-Status: NOTFOUND');
            http_response_code(404);
            die();
        }
    }
}

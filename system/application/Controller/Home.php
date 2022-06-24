<?php

namespace Controller;

use Inphinit\Viewing\View;

class Home
{
    const NOT_ALLOWED_EXTENSIONS = [
        "php", "xphp", "php5", "sh"
    ];

    function generateUpToDateMimeArray($url)
    {
        $s = array();
        foreach (explode("\n", file_get_contents($url)) as $x)
            if (isset($x[0]) && $x[0] !== '#' && preg_match_all('#([^\s]+)#', $x, $out) && isset($out[1]) && ($c = count($out[1])) > 1)
                for ($i = 1; $i < $c; $i++)
                    $s[$out[1][$i]] = $out[1][0];
        return $s;
    }

    public function fetch($resource)
    {
        header("Cache-Control: max-age=31536000");
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, OPTIONS');
        header("Access-Control-Allow-Headers: *");

        $mimeArray = self::generateUpToDateMimeArray(INPHINIT_ROOT . "mimes-types.txt");
        $resourceFile = INPHINIT_PUBLIC . $resource;

        if (is_file($resourceFile)) {
            $pinfo = pathinfo($resourceFile);
            if (in_array($pinfo["extension"], static::NOT_ALLOWED_EXTENSIONS)) {
                header('X-CDN-Status: NOT-ALLOWED');
                http_response_code(403);
                die();
            } else {
                header('Content-Type: ' . $mimeArray[$pinfo["extension"]]);
                header('Content-Disposition: inline; filename="' . $pinfo["basename"] . '"');
                header('X-CDN-Status: HIT');
                readfile($resourceFile);
            }
        } else {
            header('X-CDN-Status: NO-HIT');
            http_response_code(404);
            die();
        }
    }
}

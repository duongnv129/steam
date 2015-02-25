<?php
// Check download token
if (empty($_GET['mime']) || empty($_GET['token']))
{
    exit('Invalid download token 8{');
}

// Set operation params
$mime = filter_var($_GET['mime']);
$ext  = str_replace(array('/', 'x-'), '', strstr($mime, '/'));
$url  = base64_decode(filter_var($_GET['token']));
$name = urldecode($_GET['title']). '.' .$ext;

// Fetch and serve
if ($url)
{
    $size = get_size($url);
    // Generate the server headers
    if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== FALSE)
    {
        header('Content-Type: ' . $mime);
        header('Expires: 0');
        header('Content-Length: '.$size);
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header("Content-Transfer-Encoding: binary");
        header('Pragma: public');
    }
    else
    {
        header('Content-Type: ' . $mime);
        header("Content-Transfer-Encoding: binary");
        header('Expires: 0');
        header('Content-Length: '.$size);
        header('Pragma: no-cache');
    }
    readfile($url);
    exit;
}

// Not found
exit('File not found 8{');

function get_size($url)
{
    $my_ch = curl_init();
    curl_setopt($my_ch, CURLOPT_URL,$url);
    curl_setopt($my_ch, CURLOPT_HEADER,         true);
    curl_setopt($my_ch, CURLOPT_NOBODY,         true);
    curl_setopt($my_ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($my_ch, CURLOPT_TIMEOUT,        10);
    $r = curl_exec($my_ch);
    foreach(explode("\n", $r) as $header) {
        if(strpos($header, 'Content-Length:') === 0) {
            return trim(substr($header,16));
        }
    }
    return '';
}
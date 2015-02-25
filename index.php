<?php
/**
 * 
 * @author Duong
 * Email: nguyenduong127@gmail.com
 * @project_name Feb 25, 2015
 * api get video youtube
 */
$result["code"] = 0;
$result["message"] = "Success";
$result["result"] = "";

$name = "youfel";
if(isset($_GET["video_name"]))
    $name = $_GET["video_name"];

if(isset($_GET["video_id"]))
{
    $youTubeId = $_GET["video_id"];
    $listMedia = getListMedia($youTubeId);
    if(is_array($listMedia) && count($listMedia) > 0)
    {
        foreach ($listMedia as &$media)
        {
            //$token = base64_encode($media["url"]);
            //$media["stream"] = "http://" . $_SERVER["HTTP_HOST"] . "/stream.php?mime=" . $media["type"] . "&title=" . $name . "&token=" . $token;
            //$media["download"] = "http://" . $_SERVER["HTTP_HOST"] . "/download.php?mime=" . $media["type"] . "&title=" . $name . "&token=" . $token;
            $media["download"] = $media["url"] . "&mine=" . $media["type"] . "&title=" . $name;
            //unset($media["url"]);
            unset($media["itag"]);
            unset($media["expires"]);
            unset($media["ipbits"]);
            unset($media["ip"]);
            unset($media["expire_timestamp"]);
        }
        $result["result"] = $listMedia;
    }
    else
    {
        $result["code"] = 300;
        $result["message"] = "Can not get video";
    }
}
else
{
    $result["code"] = 100;
    $result["message"] = "Invalid param";
}

echo json_encode($result);

function getListMedia($youTubeId)
{
    $videoUrl = 'http://www.youtube.com/get_video_info?&video_id='. $youTubeId;// . "&el=vevo&el=embedded";
    $videoUrl = 'http://www.youtube.com/get_video_info?&video_id='. $youTubeId . "&el=embedded";
    $videoInfo = getRest($videoUrl);
    parse_str($videoInfo, $data);
    if(isset($data['url_encoded_fmt_stream_map']))
    {
        /* Now get the url_encoded_fmt_stream_map, and explode on comma */
        $formats = explode(',', $data['url_encoded_fmt_stream_map']);
    }
    else
        return false;
    
    $listMedia = array();
    $ipbits = $ip = $itag = $sig = $quality = '';
    $expire = time();
    foreach($formats as $i => $format)
    {
        parse_str($format);
        $listMedia[$i]['itag'] = $itag;
        $listMedia[$i]['quality'] = $quality;
        $type = explode(';',$type);
        $listMedia[$i]['type'] = $type[0];
        $listMedia[$i]['url'] = urldecode($url) . '&signature=' . $sig;
        parse_str(urldecode($url));
        $listMedia[$i]['expires'] = date("Y-m-d H:i:s", $expire);
        $listMedia[$i]['expire_timestamp'] = $expire;
        $listMedia[$i]['ipbits'] = $ipbits;
        $listMedia[$i]['ip'] = $ip;
    }
    return $listMedia;
}

function getRest($url)
{
    $parser = parse_url($url);    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    if($parser["scheme"] == "https")
    {
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    }    
    $result = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);
    return $result;
}
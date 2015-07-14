<?php

/*  ----------------------------------------------------------------------------
    tagDiv video support
    - downloads the video thumbnail and puts it asa a featured image to the post
 */

class td_video_support{

    /*
     * youtube
     */
    function getYoutubeId($videoUrl) {
        $query_string = array();
        parse_str(parse_url($videoUrl, PHP_URL_QUERY), $query_string);

        if (empty($query_string["v"])) {
            //explode at ? mark
            $yt_short_link_parts_explode1 = explode('?', $videoUrl);

            //short link: http://youtu.be/AgFeZr5ptV8
            $yt_short_link_parts = explode('/', $yt_short_link_parts_explode1[0]);
            if (!empty($yt_short_link_parts[3])) {
                return $yt_short_link_parts[3];
            }

            return $yt_short_link_parts[0];
        } else {
            return $query_string["v"];
        }
    }

    /*
     * youtube t param from url (ex: http://youtu.be/AgFeZr5ptV8?t=5s)
     */
    function getYoutubeTimeParam($videoUrl) {
        $query_string = array();
        parse_str(parse_url($videoUrl, PHP_URL_QUERY), $query_string);
        if (!empty($query_string["t"])) {

            if (strpos($query_string["t"], 'm')) {
                //take minutes
                $explode_for_minutes = explode('m', $query_string["t"]);
                $minutes = trim($explode_for_minutes[0]);

                //take seconds
                $explode_for_seconds = explode('s', $explode_for_minutes[1]);
                $seconds = trim($explode_for_seconds[0]);

                $startTime = ($minutes * 60) + $seconds;
            } else {
                //take seconds
                $explode_for_seconds = explode('s', $query_string["t"]);
                $seconds = trim($explode_for_seconds[0]);

                $startTime = $seconds;
            }

            return '&start=' . $startTime;
        } else {
            return '';
        }
    }

    /*
     * Vimeo id
     */
    function getVimeoId($videoUrl) {
        sscanf(parse_url($videoUrl, PHP_URL_PATH), '/%d', $video_id);
        return $video_id;
    }

    /*
     * Dailymotion
     */
    function getDailymotionID($videoUrl) {
        $id = strtok(basename($videoUrl), '_');
        if (strpos($id,'#video=') !== false) {
            $videoParts = explode('#video=', $id);
            if (!empty($videoParts[1])) {
                return $videoParts[1];
            }
        } else {
            return $id;
        }

    }

    /*
     * Detect the video service from url
     */
    function detectVideoSearvice($videoUrl) {
        $videoUrl = strtolower($videoUrl);
        if (strpos($videoUrl,'youtube.com') !== false or strpos($videoUrl,'youtu.be') !== false) {
            return 'youtube';
        }
        if (strpos($videoUrl,'dailymotion.com') !== false) {
            return 'dailymotion';
        }
        if (strpos($videoUrl,'vimeo.com') !== false) {
            return 'vimeo';
        }

        return false;
    }


    function is404($url) {
        $headers = get_headers($url);
        if (strpos($headers[0],'404') !== false) {
            return true;
        } else {
            return false;
        }
    }


    //returns the thumb url
    function getThumbUrl($videoUrl) {
        switch ($this->detectVideoSearvice($videoUrl)) {
            case 'youtube':
                if (!$this->is404('http://img.youtube.com/vi/' . $this->getYoutubeId($videoUrl) . '/maxresdefault.jpg')) {
                    return 'http://img.youtube.com/vi/' . $this->getYoutubeId($videoUrl) . '/maxresdefault.jpg';
                } else {
                    return 'http://img.youtube.com/vi/' . $this->getYoutubeId($videoUrl) . '/hqdefault.jpg';
                }

                break;
            case 'dailymotion':
                $dailyMotionApi = @file_get_contents('https://api.dailymotion.com/video/' . $this->getDailymotionID($videoUrl) . '?fields=thumbnail_url');
                $dailyMotionDecoded = @json_decode($dailyMotionApi);
                if (!empty($dailyMotionDecoded) and !empty($dailyMotionDecoded->thumbnail_url)) {
                    return $dailyMotionDecoded->thumbnail_url;
                }
                //print_r($dailyMotionDecoded);
                break;
            case 'vimeo':
                $vimeoApi = @file_get_contents('http://vimeo.com/api/v2/video/' . $this->getVimeoId($videoUrl) . '.php');
                if (!empty($vimeoApi)) {
                    $vimeoApiData = @unserialize($vimeoApi);
                    if (!empty($vimeoApiData[0]['thumbnail_large'])) {
                        return $vimeoApiData[0]['thumbnail_large'];
                    }
                    //print_r($vimeoApiData);
                }

                break;
        }
    }

    function renderVideo($videoUrl) {
        $buffy = '';
        switch ($this->detectVideoSearvice($videoUrl)) {
            case 'youtube':
                $buffy .= '
                <div class="wpb_video_wrapper">
                    <iframe width="600" height="560" src="http://www.youtube.com/embed/' . $this->getYoutubeId($videoUrl) . '?feature=oembed&wmode=opaque' . $this->getYoutubeTimeParam($videoUrl) . '" frameborder="0" allowfullscreen=""></iframe>
                </div>
                ';

                break;
            case 'dailymotion':
                $buffy .= '
                    <div class="wpb_video_wrapper">
                        <iframe frameborder="0" width="600" height="560" src="http://www.dailymotion.com/embed/video/' . $this->getDailymotionID($videoUrl) . '"></iframe>
                    </div>
                ';
                break;
            case 'vimeo':
                $buffy = '
                <div class="wpb_video_wrapper">
                    <iframe src="http://player.vimeo.com/video/' . $this->getVimeoId($videoUrl) . '" width="500" height="212" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>
                </div>
                ';
                break;
        }
        return $buffy;
    }


    function validateVideoUrl($videoUrl) {
        if ($this->detectVideoSearvice($videoUrl) === false) {
            return false;
        }

        if (!preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $videoUrl)) {
            return false;
        }

        return true;
    }
}



//action used to set featured images from uploaded photo
//by td_video_support and load_demo
function td_add_featured_image($att_id){
    // the post this was sideloaded into is the attachments parent!
    $p = get_post($att_id);
    update_post_meta($p->post_parent,'_thumbnail_id',$att_id);
}


add_action( 'save_post', 'td_get_video_thumb', 12 );

function td_get_video_thumb( $post_id ) {
    //verify post is not a revision
    if ( !wp_is_post_revision( $post_id ) ) {
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        $td_post_video = get_post_meta($post_id, 'td_post_video', true);


        //load video support
        $td_video_support = new td_video_support();

        //check to see if the url is valid
        if (empty($td_post_video['td_video']) or $td_video_support->validateVideoUrl($td_post_video['td_video']) === false) {
            return;
        }



        if (!empty($td_post_video['td_last_video']) and $td_post_video['td_last_video'] == $td_post_video['td_video']) {
            //we did not update the url
            return;
        }



        //$myFile = "D:/td_video.txt";
        //$fh = fopen($myFile, 'a') or die("can't open file");
        $stringData = $post_id . ' - ' . print_r($td_post_video, true) . "\n";

        //return;


        $videoThumbUrl = $td_video_support->getThumbUrl($td_post_video['td_video']);

        /*
        $stringData .= $post_id . ' - ' . $videoThumbUrl . "\n";
        fwrite($fh, $stringData);
        fclose($fh);

        */

        if (!empty($videoThumbUrl)) {
            // add the function above to catch the attachments creation
            add_action('add_attachment','td_add_featured_image');

            // load the attachment from the URL
            media_sideload_image($videoThumbUrl, $post_id, $post_id);

            // we have the Image now, and the function above will have fired too setting the thumbnail ID in the process, so lets remove the hook so we don't cause any more trouble
            remove_action('add_attachment','td_add_featured_image');
        }

    }
}




//$td_video_support = new td_video_support();
//echo $td_video_support->getThumbUrl('http://www.dailymotion.com/video/x17be7o_paraplegic-woman-walks-again_tech');
//die;
//echo $td_video_support->getThumbUrl('http://www.youtube.com/watch?v=irE7miqG_LU&list=FLOBuNbx8x0RyDnCgLpTznHA&index=2');
//echo '<br>';
//echo $td_video_support->getThumbUrl('http://vimeo.com/15274619');
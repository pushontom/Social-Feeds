   <?php 

    // Include the configuration settings
    // #################################################################################    
    include('config.php');

    // Twitter Auth
    // #################################################################################    
    function getConnectionWithAccessToken($cons_key, $cons_secret, $oauth_token, $oauth_token_secret) {
      $connection = new TwitterOAuth($cons_key, $cons_secret, $oauth_token, $oauth_token_secret);
      return $connection;
    }  

    // Custom function for fetching url contents via curl
    // #################################################################################
    function get_data($url) {
        $ch = curl_init();
        $timeout = 5;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $data = curl_exec($ch);
        curl_close($ch);
        
        return $data;
    }    

    // Cache the JSON feed into a JSON file
    // #################################################################################    
    function cache_feed($get,$network, $connection=null){
    
        switch($network){
            case "twitter":
                $cache_file = dirname(__FILE__).'/cache/'.'twitter-cache.json';
                break;
            case "facebook":
                $cache_file = dirname(__FILE__).'/cache/'.'facebook-cache.json';
                break;      
            case "instagram":
                $cache_file = dirname(__FILE__).'/cache/'.'instagram-cache.json';
                break;
            case "pinterest":
                $cache_file = dirname(__FILE__).'/cache/'.'pinterest-cache.json';
                break;
            case "youtube":
                $cache_file = dirname(__FILE__).'/cache/'.'youtube-cache.json';
                break;
        }        
        
        $modified = @filemtime( $cache_file );    
        $now = time();
        $interval = 6000; // one hour
        
        // check the cache file
        if ( !$modified || ( ( $now - $modified ) > $interval ) ) {
            
            switch($network){
                case "twitter":
                    $json = $connection->get($get);
                    break;
                case "facebook":
                    $json = $connection->api($get);
                    $json = json_encode($json);
                    break;
                case "instagram":
                    $json = get_data($get);
                    break;
                case "pinterest":
                    $json = get_data($get);
                    break;
                case "youtube":
                    $json = get_data($get);
                    break;
            }
        
        
            if ( $json ) {
                $cache_static = fopen( $cache_file, 'w' );
                fwrite( $cache_static, $json );
                fclose( $cache_static );
            } else {
            
            }
            
        }
        
        $json = file_get_contents( $cache_file );
        return json_decode($json);
    
    }

    // Get Social Channel
    // #################################################################################    
    function get_social_channel($channel){
             
        global $twitter_username;
        switch($channel){
                        
            // Get Twitter Feed
            case "twitter":

                require_once('twitteroauth-master/twitteroauth/twitteroauth.php'); //Path to twitteroauth library
                 
                $twitteruser = $twitter_username;
                $notweets = 9;
                $consumerkey = "4wnGpfFDcewFxagjGYjpZg";
                $consumersecret = "F4ekmArJRhtNzNNYCLrKt2bsSq86dWz5GbbrLh2FO0";
                $accesstoken = "18529846-BS4fNcN9qkdoGu5crc6XwpPUeC9kfiXsxVo4xo";
                $accesstokensecret = "YtT8oWKmdx91nk4G8PVeQXiTVvHDLHQAgEb7HTqcg";
                      
                $connection = getConnectionWithAccessToken($consumerkey, $consumersecret, $accesstoken, $accesstokensecret);
                $tweets = cache_feed("https://api.twitter.com/1.1/statuses/user_timeline.json?screen_name=".$twitteruser."&count=".$notweets,"twitter",$connection);
                
                $i=0;     
                foreach ( $tweets as $tweet ) {       
                    if($i < 9){
                        $twitter[] = $tweet;
                    }
                    $i++; 
                }
                
                return $twitter;
                  
            break;
            
            // Get Pinterest Feed
            case "pinterest":
                
                global $pinterest_username;
                $pins_url = 'http://pinterestapi.co.uk/' . $pinterest_username . '/pins';
                $pin_feed = cache_feed( $pins_url,"pinterest" );

                if( $pin_feed ){
                    $i=0;     
                    foreach ( $pin_feed->body as $pin ){
                        if($i < 9){
                            $pins[] = $pin;
                        }
                        $i++; 
                    }                    
                    return $pins;
                }
                
                
            
            break;
            
            // Get YouTube Videos
            case "youtube":
            
                global $youtube_user;
                $yt_url = 'http://gdata.youtube.com/feeds/api/users/' . $youtube_user . '/uploads/?v=2&alt=json';
                //$yt_feed = json_decode(get_data($yt_url));                
                $yt_feed = cache_feed($yt_url, "youtube");

                var_dump($yt_feed);
                exit;

                
                $i=0;     
                foreach ( $yt_feed->feed->entry as $yt ){
                    if($i < 9){
                        $videos[] = $yt;
                    }
                    $i++; 
                }            
                
                //d($videos)
                return $videos;
            
            break;
            
            // Get Facebook Posts
            case "facebook":
                
                require_once('facebook-php-sdk-master/src/facebook.php');

                $facebook = new Facebook(array(
                  'appId'  => '180203625475414',
                  'secret' => '4d06b90afb46cfa03f6c8bf83d3b840c',
                ));
                
                $access_token = $facebook->getAccessToken();
                $fb_feed = cache_feed( '131411386907026' . '/feed','facebook',$facebook );
                
                $i=0;     
                foreach ( $fb_feed->data as $fb_post ){
                    
                    if( isset($fb_post->message) ):
                                            
                        if($i < 9){
                            $fb_posts[] = $fb_post;
                        }

                        $i++;                         
                    
                    endif;
                                        
                }            
                
                return $fb_posts;
            
            break;
            
            case "instagram":
                
                global $instagram_userid;
                global $instagram_tag;

                //$returned_content = get_data('https://api.instagram.com/v1/users/438855825/media/recent/?access_token=1827519.f59def8.5164b0591331417fb6c84eedb6200f97');
                //$returned_content = cache_feed('https://api.instagram.com/v1/tags/memcr/media/recent?client_id=cad7ac625011443b96ddedc58b101ec1', 'instagram');
                //$returned_content = cache_feed('https://api.instagram.com/v1/tags/cats/media/recent?callback=?&client_id='.$instagram_clientid.'&max_tag_id=1364206789229?access_token=1827519.f59def8.5164b0591331417fb6c84eedb6200f97', 'instagram');
                $returned_content = cache_feed('https://api.instagram.com/v1/users/'.$instagram_userid.'/media/recent/?access_token=1827519.f59def8.5164b0591331417fb6c84eedb6200f97', 'instagram');                
                $instagram_data = $returned_content;
                //$instagram_data = json_decode($returned_content);

                $i=0;     
                foreach ($instagram_data->data as $instagram_feed ){
                    if($i < 9){
                        $instagram_feeds[$i] = $instagram_feed->images->standard_resolution;
                        $instagram_feeds[$i]->more_data = $instagram_feed;
                    }
                    $i++;
                }
                
                return $instagram_feeds;        
            
            break;
            
        }  
                
    }    
      
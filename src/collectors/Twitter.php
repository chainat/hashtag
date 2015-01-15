<?php namespace Chainat\Hashtag;

require_once( __DIR__ . '/../lib/twitteroauth.php');

/**
* Instagram class - pull images from Instagram
* Inspired by https://github.com/jfrazelle/hashtag-pull.git, rewritten as generic vendor
*/
class Twitter implements CollectorInterface
{
	use \Chainat\Hashtag\DataSetter;
	use \Chainat\Hashtag\Emoji;

	public function fetch($params){

		$connection = new \TwitterOAuth($params['consumer_key'], $params['consumer_secret'], $params['access_token'], $params['access_token_secret']);
		$hashtag 	= $params['hashtag'];
		$count 		= (isset($params['count']))?$params['count']:20;
		$since_id 	= (isset($params['min_tag_id']))?$params['min_tag_id']:0;
	    $twitterContent = $connection->get(
	        "search/tweets", array(
	            'q' 		=> '#'.$hashtag.' filter:images',
	            'since_id' 	=> $since_id,
	            'include_entities' => true,
	            'lang' 		=> 'en',
	            'count'		=> $count
	        )
	    );

	    $output = [];

	    if ($twitterContent){
	    	foreach($twitterContent->statuses as $tweet){    
		        if ($tweet->id > $since_id && !empty($tweet->entities->urls) || isset($tweet->entities->media)){
                    $twitter_id = $tweet->id;
                    $tweet_id = $tweet->id_str;
                    $created_at = $tweet->created_at;
                    $user_location = $this->stripEmojis($tweet->user->location);                    
                    //$link_post = 'https://twitter.com/'.$screen_name.'/status/'.$tweet_id;
                    $time_now = time();
                    $is_tweet = false;

			        $source_id = $tweet_id; //$insta['id'];
		            $created_time = '';// isset($insta['created_time'])? $insta['created_time']: '';
			        $user_id = $tweet->user->id;
			        $screen_name =  $tweet->user->screen_name;
			        $this_name = $this->stripEmojis($tweet->user->name);
			        $text = $this->stripEmojis($tweet->text);
			        $likes = 0;
			        $media_url_http = $media_url_https = '';
			        $next_min_id = $tweet_id;
                    $type = 'photo';

                    if (isset($tweet->entities->media)){
                        $is_tweet = true;
                        $media_url_http = $tweet->entities->media[0]->media_url;
                        $media_url_https = $tweet->entities->media[0]->media_url_https;
                    } 

                    $output[] = [
			        	'next_max_id'		=> -1,
			        	'next_min_id'		=> $next_min_id,
			        	'source_id'			=> $source_id,	        	
			        	'user_id'			=> $user_id,
			        	'name'				=> $this_name,
			        	'screen_name' 		=> $screen_name,
			        	'text'				=> $text,
			        	'likes'				=> $likes,
			        	'media_url_http'	=> $media_url_http,
			        	'media_url_https'	=> $media_url_https,
			        	'source'			=> 'Twitter',
			        	'type'				=> $type,
			        	'hashtag'			=> $params['hashtag'],
		                'media_created_time'=> $created_time,
			        ];
                }
            }
	    }

	    return $output;
	}

}
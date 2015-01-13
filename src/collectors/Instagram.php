<?php namespace Chainat\Hashtag;

/**
* Instagram class - pull images from Instagram
* Inspired by https://github.com/jfrazelle/hashtag-pull.git, rewritten as generic vendor
*/
class Instagram implements CollectorInterface
{
	use \Chainat\Hashtag\DataSetter;

	private $url =  'https://api.instagram.com/v1/tags/{hashtag}/media/recent?client_id={client_id}';

	private function stripEmojis($text){
	    $clean_text = "";

	    // Match Emoticons
	    $regexEmoticons = '/[\x{1F600}-\x{1F64F}]/u';
	    $clean_text = preg_replace($regexEmoticons, '', $text);

	    // Match Miscellaneous Symbols and Pictographs
	    $regexSymbols = '/[\x{1F300}-\x{1F5FF}]/u';
	    $clean_text = preg_replace($regexSymbols, '', $clean_text);

	    // Match Transport And Map Symbols
	    $regexTransport = '/[\x{1F680}-\x{1F6FF}]/u';
	    $clean_text = preg_replace($regexTransport, '', $clean_text);

	    // Match Miscellaneous Symbols
	    $regexMisc = '/[\x{2600}-\x{26FF}]/u';
	    $clean_text = preg_replace($regexMisc, '', $clean_text);

	    // Match Dingbats
	    $regexDingbats = '/[\x{2700}-\x{27BF}]/u';
	    $clean_text = preg_replace($regexDingbats, '', $clean_text);

	    return $clean_text;
	}
	

	public function fetch($params){

		$default = [
			'hashtag'	=>	'default',
			'client_id'	=>	'xxx',
			'max_tag_id'=>	0
		];
		$params = array_merge($default, $params);

		$get_media_url = $this->url;
	    $get_media_url = str_replace('{hashtag}', $params['hashtag'], $get_media_url);
	    $get_media_url = str_replace('{client_id}', $params['client_id'], $get_media_url);

	    if (isset($params['max_tag_id']) && $params['max_tag_id'] <= 0){
	    	unset($params['max_tag_id']);
	    }
	    if (isset($params['source_id'])){
	        $get_media_url .= '&max_tag_id='.$params['max_tag_id'];
	    }

	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $get_media_url);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    $response = curl_exec($ch);
	    curl_close($ch);

	    $media = json_decode($response, true);
	    $next_max_id = $media['pagination']['next_max_id'];
	    $next_min_id = $media['pagination']['next_min_id'];
	    $output = [];

	    foreach($media['data'] as $insta){
	        $time_now = time();
	        $source_id = $insta['id'];
	        $created_at = date('r', $insta['created_time']);
	        $user_id = $insta['user']['id'];
	        $screen_name = $insta['user']['username'];
	        $this_name = $this->stripEmojis($insta['user']['full_name']);
	        $text =  $this->stripEmojis($insta['caption']['text']);
	        $likes = $insta['likes']['count'];

	        if ($insta['type'] == 'video'){
	            $type= 'video';
	            $media_url=$insta['images']['standard_resolution']['url'];
	            $media_url_https=$insta['videos']['standard_resolution']['url'];
	        } else {
	            $type= 'photo';
	            $media_url=$insta['images']['standard_resolution']['url'];
	            $media_url_https= '';
	        }

	        $output[] = [
	        	'next_max_id'		=> $next_max_id,
	        	'next_min_id'		=> $next_min_id,
	        	'source_id'			=> $source_id,	        	
	        	'user_id'			=> $user_id,
	        	'name'				=> $this_name,
	        	'screen_name' 		=> $screen_name,
	        	'text'				=> $text,
	        	'likes'				=> $likes,
	        	'media_url_http'	=> $media_url,
	        	'media_url_https'	=> $media_url_https,
	        	'source'			=> 'Instagram',
	        	'type'				=> $type,
	        	'hashtag'			=> $params['hashtag'],
	        ];
	    }
	    return $output;
	}
	

}
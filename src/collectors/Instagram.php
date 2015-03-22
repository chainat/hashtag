<?php namespace Chainat\Hashtag;

/**
* Instagram class - pull images from Instagram
* Inspired by https://github.com/jfrazelle/hashtag-pull.git, rewritten as generic vendor
*/
class Instagram implements CollectorInterface
{
	use \Chainat\Hashtag\DataSetter;
	use \Chainat\Hashtag\Emoji;

	private $url =  'https://api.instagram.com/v1/tags/{hashtag}/media/recent?client_id={client_id}';

	public function fetch($params){

		$default = [
			'hashtag'	=>	'default',
			'client_id'	=>	'xxx',
			'max_tag_id'=>	0,
            'min_tag_id'=>	0,
		];
		$params = array_merge($default, $params);

		$get_media_url = $this->url;
        $get_media_url = str_replace('{hashtag}', $params['hashtag'], $get_media_url);
        $get_media_url = str_replace('{client_id}', $params['client_id'], $get_media_url);

        if (isset($params['max_tag_id']) && ($params['max_tag_id'] != 0)){
            $get_media_url .= '&max_tag_id='.$params['max_tag_id'];
        }
        if (isset($params['min_tag_id']) && ($params['min_tag_id'] != 0)){
            $get_media_url .= '&min_tag_id='.$params['min_tag_id'];
        }

        $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $get_media_url);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    $response = curl_exec($ch);
	    curl_close($ch);

	    $media = json_decode($response, true);
	    $next_max_id = isset($media['pagination']['next_max_id'])? $media['pagination']['next_max_id']: '';
	    $next_min_id = isset($media['pagination']['next_min_id'])? $media['pagination']['next_min_id']: '';

	    $output = [];

        if ($media['data']) {
            foreach ($media['data'] as $insta) {
                $time_now = time();
                $source_id = $insta['id'];
                $created_time = isset($insta['created_time']) ? $insta['created_time'] : '';
                $user_id = $insta['user']['id'];
                $screen_name = $insta['user']['username'];
                $this_name = $this->stripEmojis($insta['user']['full_name']);
                $text = $this->stripEmojis($insta['caption']['text']);
                $likes = $insta['likes']['count'];

                if ($insta['type'] == 'image') {
                    $type = 'photo';
                    $media_url = $insta['images']['standard_resolution']['url'];
                    $media_url_https = '';

                    $output[] = [
                        'next_max_id' => $next_max_id,
                        'next_min_id' => $next_min_id,
                        'source_id' => $source_id,
                        'user_id' => $user_id,
                        'name' => $this_name,
                        'screen_name' => $screen_name,
                        'text' => $text,
                        'likes' => $likes,
                        'media_url_http' => $media_url,
                        'media_url_https' => $media_url_https,
                        'source' => 'Instagram',
                        'type' => $type,
                        'hashtag' => $params['hashtag'],
                        'media_created_time' => $created_time,
                    ];
                }
            }
        }
	    return $output;
	}
	

}
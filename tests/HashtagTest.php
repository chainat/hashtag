<?php
use Chainat\Hashtag\Hashtag;

class HashtagTest extends PHPUnit_Framework_TestCase
{
	private $default = [
		'hashtag'	=>	'loveison',
		'client_id'	=>	'bd5bf61ab071492d94b360ada7b09693',
		'max_tag_id'=>	0,
	];

	private $twitter = [
		'hashtag'	=>	'loveison',
		'consumer_key'		=>	'JvtAEpyh5mVHfoJqHuAeUuZOh',
		'consumer_secret'	=>	'5sfNqDLLE31lr1P16j6fEHGvg0QZ9xcI3r1JXmGkd8qjBTNV9q',
		'access_token'		=>	'723130380-riUVIKgR7IseK6mQJghN7CoBMf6Ja5gaXPee3amO',
		'access_token_secret'	=> 'Et5hKAq2zdD6zu6m0yRO2RNDlX4b5Myj1AQClTfVN5CVt'
	];
	

	/*
	public function testInstagram() {
		$instagram = new Instagram();
		$output = $instagram->fetch($this->default);
		print_r($output);
		//$this->assertEquals($donut->displayName(), 'My Test Donut');
	}
	*/

	public function testInstagram() {
		$hashtag = Hashtag::get("Instagram");
		$output = $hashtag->fetch($this->default);
		$this->assertTrue(count($output) > 0, 'Output should contain multiple elements (larger than 0)');
	}

	public function testTwitter() {
		$hashtag = Hashtag::get("Twitter");
		$output = $hashtag->fetch($this->twitter);
		$this->assertTrue(count($output) > 0, 'Output should contain multiple elements (larger than 0)');
	}

}
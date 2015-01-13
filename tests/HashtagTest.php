<?php
use Chainat\Hashtag\Hashtag;

class HashtagTest extends PHPUnit_Framework_TestCase
{
	private $default = [
		'hashtag'	=>	'loveison',
		'client_id'	=>	'bd5bf61ab071492d94b360ada7b09693',
		'max_tag_id'=>	0
	];
	

	/*
	public function testInstagram() {
		$instagram = new Instagram();
		$output = $instagram->fetch($this->default);
		print_r($output);
		//$this->assertEquals($donut->displayName(), 'My Test Donut');
	}
	*/

	public function testHashtag() {
		$hashtag = Hashtag::get("Instagram");
		$output = $hashtag->fetch($this->default);
		//$hashtag->save([], $output);
		$this->assertTrue(count($output) > 0, 'Output should contain multiple elements (larger than 0)');
	}

}
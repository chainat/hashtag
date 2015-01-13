<?php namespace Chainat\Hashtag;

trait DataSetter 
{
	public function save($callback) {
		$callback();
	}
}
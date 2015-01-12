<?php namespace Chainat\Hashtag;

class Hashtag {
	
	public static function get($className){		
		$className =  __NAMESPACE__ .'\\'. $className;
		return new $className();
	}
}
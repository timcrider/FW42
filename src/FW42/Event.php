<?php
/**
*
*/

/**
*
*/

class FW42_Event {
	/**
	*
	*/
	protected static $Registry;

	/**
	*
	*/
	public static function register($events, $callback) {
		if (!is_callable($callback)) {
			throw new \Exception('Registration callback is not callable.');
		}

		if (!is_array(static::$Registry)) {
			static::$Registry = array();
		}
		
		$events = (array)$events;

		foreach ($events AS $event) {
			static::$Registry[$event][] = $callback;
		}
		
		return;
	}

	/**
	*
	*/
	public static function trigger($event, $data=array()) {
		if (!isset(static::$Registry[$event])) {
			return false;
		}
		
		foreach (static::$Registry[$event] AS $listener) {
			call_user_func_array($listener, array($data));
		}
		
		return true;
	}


}

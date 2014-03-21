<?php

class HipChat_Api
{

	public $api_urls = array(
		'room/message' => 'https://api.hipchat.com/v1/rooms/message?format=%s&auth_token=%s'
	);
	
	
	public $format = 'json';
	
	
	public function post_room_message($message, $color = 'red', $from = 'System', $format = 'html', $notify = 1)
	{
		$url = sprintf($this->api_urls['room/message'], $this->format, $this->__get_setting('authentication_code'));
		
		$params = array(
			'room_id' => $this->__get_setting('default_room'),
			'message' => $message,
			'from' => $from,
			'message_format' => $format,
			'notify' => $notify,
			'color' => $color
		);
		
		$response = static::__apicall($url, $params);
		
		return $response;
	}
		
	
	private static function __apicall($url, $params = false)
	{
		$c = curl_init();
		
		curl_setopt($c, CURLOPT_URL, $url);
		
		if($params)
			curl_setopt ($c, CURLOPT_POSTFIELDS, $params);
		
		curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
		
		$contents = curl_exec($c);
		curl_close($c);
		
		return json_encode($contents);
	}
	
	private function __get_setting($name)
	{
		$settings = Core_ModuleSettings::create('hipchat', 'hipchat');
		return isset($settings->$name) ? $settings->$name : false;
	}

}
<?php

if ( !function_exists('json_decode') ){
	// Para guardar compatibilidad con versiones de PHP menores a 5.2
	function json_decode($content, $assoc=true){
		require_once 'JSON.php';
		$json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
		return $json->decode($content);
	}
}

class JetMailsAPI {
	var $version = "2.1";
	var $errorMessage;
	var $errorCode;
	var $apiUrl;
	var $debug = false;
	var $format = 'json';
	var $timeout = 300;
	var $chunkSize = 8192;
	var $apikey;
	var $secure = false; // not implemented yet

	function JetMailsAPI($username_or_apikey, $password=null, $secure=false, $debug=false) {
		$this->secure = $secure;
		$this->debug = $debug;

		if ($this->debug) {
			$this->apiUrl = parse_url("http://127.0.0.1:8000/" . $this->version );
		} else {
			$this->apiUrl = parse_url("http://api.jetmails.com/" . $this->version );
		}

		if( $username_or_apikey && !$password ){
			$this->apikey = $username_or_apikey;
		}  else {
			$apiKey = $this->callServer("login", 
            array("username" => $username_or_apikey, "password" => $password));
            $this->apikey = $apiKey[0];
		}
	}

	function setTimeout($seconds){
		if (is_int($seconds)){
			$this->timeout = $seconds;
			return true;
		}
	}

	function getTimeout(){
		return $this->timeout;
	}

	function useSecure($val){ // not implemented yet
		if ($val===true){
			$this->secure = true;
		} else {
			$this->secure = false;
		}
	}

	function lists() {
		$params = array();
		return $this->callServer("lists", $params);
	}

	function listSubscribe($id, $email_address, $data, $double_optin=true,
								$update_existing=false, $send_welcome=false) {
		$params = array();
		$params["id"] = $id;
		$params["email_address"] = $email_address;
		$params["data"] = $data;
		$params["double_optin"] = $double_optin;
		$params["update_existing"] = $update_existing;  // not implemented yet
		$params["send_welcome"] = $send_welcome;        // not implemented yet
		return $this->callServer("listSubscribe", $params, "POST");
	}
	
	function listSubscribersCount($id) {
		$params = array();
		$params["id"] = $id;
		return $this->callServer("listSubscribersCount/".$id, $params, "GET");
	}

	function ping() {
		$params = array();
		return $this->callServer("ping", $params);
	}

	function callServer($resource, $params, $method="GET") {
		if($resource != "login") {
			$params["apikey"] = $this->apikey;
		}

		//Always include the format param
		$params["format"] = $this->format;
		$host = $this->apiUrl["host"];
		$port = $this->apiUrl["port"];
		$this->errorMessage = "";
		$this->errorCode = "";

		if ($method == "GET") {
			$post_vars = "";
			$get_vars = "?" . http_build_query($params, '', '&');
		} else {
			$post_vars = http_build_query($params, '', '&');
			$get_vars =  "";
		}

		$payload = "$method " . $this->apiUrl["path"] . "/" . $resource .  $get_vars . " HTTP/1.0\r\n";
		$payload .= "Host: " . $host . "\r\n";
		$payload .= "User-Agent: JetMailsAPI/" . $this->version ."\r\n";
		if ($method == "POST") {
			$payload .= "Content-type: application/x-www-form-urlencoded\r\n";
			$payload .= "Content-length: " . strlen($post_vars) . "\r\n";
		}
		$payload .= "Connection: close \r\n\r\n";
		$payload .= $post_vars;

		if ($this->debug) {
			echo "<pre>$payload</pre>";
		}

		ob_start();
		if ($this->secure){
			$sock = fsockopen("ssl://".$host, 443, $errno, $errstr, $this->timeout);
		} else {
			$sock = fsockopen($host.":80", $port, $errno, $errstr, $this->timeout);
		}
		if(!$sock) {
			$this->errorMessage = "Could not connect (ERR $errno: $errstr)";
			$this->errorCode = "-99";
			ob_end_clean();
			return false;
		}

		$response = "";
		fwrite($sock, $payload);
		while(!feof($sock)) {
			$response .= fread($sock, $this->chunkSize);
		}
		fclose($sock);
		ob_end_clean();

		if ($this->debug) {
			echo "<pre>$response</pre>";
		}

		list($throw, $response) = explode("\r\n\r\n", $response, 2);

		if(ini_get("magic_quotes_runtime")) {
			$response = stripslashes($response);
		}

		$serial = json_decode($response, true);
		if(is_null($serial)) {
			$response = array("error" => "Bad Response, got this: " . $response, "code" => "-1");
		} else {
			$response = $serial;
		}
		if(is_array($response) && isset($response["error"])) {
			$this->errorMessage = $response["error"];
			$this->errorCode = $response["code"];
			return false;
		}

		return $response;
	}
}

?>
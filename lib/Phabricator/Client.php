<?php

namespace Phabricator;

class Client{

	var $client = "php-phabricator-api",
        $client_version = "0.0.1",

        $url = null,
        $auth_certificate = null,
        $auth_user = null,
        $port = 80,

        $session_key = null,
        $connection_id = null,

        $apis = array(); // hold api obj references

    public function __construct($url, $auth_user, $auth_certificate)
    {
        $this->url = $url;
        $this->auth_certificate = $auth_certificate;
        $this->auth_user = $auth_user;

        $this->getSessionKey();
    }

    public function api($name)
    {
        if (!isset($this->apis[$name])) {
            switch ($name) {
                case 'diffs':
                    $api = new Api\Differential($this);
                    break;
                case 'users':
                	$api = new Api\User($this);
                	break;
                default:
                    throw new \InvalidArgumentException();
            }

            $this->apis[$name] = $api;
        }

        return $this->apis[$name];
    }

    // Retrieves an initial connection ID and session key for future API requests
    private function getSessionKey()
    {
        $time = time(); // UTC time in seconds
        $data = array();

    	$data['client'] = $this->client;
    	$data['clientVersion'] = $this->client_version;

    	$data['user'] = $this->auth_user;
    	$data['host'] = $this->url;
    	$data['authToken'] = $time; 
    	$data['authSignature'] = sha1($time.$this->auth_certificate);

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $this->url."/api/conduit.connect");
        curl_setopt($curl, CURLOPT_VERBOSE, 0);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_PORT , $this->port);
        curl_setopt($curl, CURLOPT_POST, 1);

        $post_fields = array(
        	"params" => json_encode($data),
        	"output" => "json",
        	"__conduit__" => true
        );

        curl_setopt($curl, CURLOPT_POSTFIELDS, $post_fields);   
	 	
	 	$response = curl_exec($curl);

        if (curl_errno($curl)) {
            $e = new \Exception(curl_error($curl), curl_errno($curl));
            curl_close($curl);
            throw $e;
        }
        curl_close($curl);

        $json = json_decode($response);
        //\Log::info(print_r($json,true));
        if (isset($json->error_info)){
        	throw new Exception($json->error_info);
        }

        $this->connection_id = $json->result->connectionID;
        $this->session_key = $json->result->sessionKey;

		return $response;
    }

    public function request($path, $method = 'GET', $data = '')
    {
    	if (is_null($this->session_key)){
    		$this->getSessionKey();
    	}

		$curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $this->url."/api/".$path);
        curl_setopt($curl, CURLOPT_VERBOSE, 0);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_PORT , $this->port);
        curl_setopt($curl, CURLOPT_POST, 1);

        $data["__conduit__"] = array(
        	"sessionKey" => $this->session_key,
        	"connectionId" => $this->connection_id
        );

        $post_fields = array(
        	"params" => json_encode($data),
        	"output" => "json"
        );

		curl_setopt($curl, CURLOPT_POSTFIELDS, $post_fields);        

		$response = curl_exec($curl);

        if (curl_errno($curl)) {
            $e = new \Exception(curl_error($curl), curl_errno($curl));
            curl_close($curl);
            throw $e;
        }
        curl_close($curl);

        $json = json_decode($response);

        //\Log::info(print_r($json,true));

   		return $json;
    }
}
<?php

namespace Phabricator\Api;

class User extends Api{

	public function query($params = array()){
		return $this->client->request('user.query', "POST", $params);
	}
	
}
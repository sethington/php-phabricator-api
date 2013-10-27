<?php

namespace Phabricator\Api;

class User extends AbstractApi{

	public function query($params = array()){
		return $this->post('user.query',$params);	
	}
	
}
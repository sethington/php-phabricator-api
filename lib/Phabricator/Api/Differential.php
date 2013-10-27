<?php

namespace Phabricator\Api;

class Differential extends Api{

	public function query($params = array()){
		return $this->client->request('differential.query',"POST",$params);	
	}
	
}
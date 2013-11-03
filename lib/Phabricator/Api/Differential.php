<?php

namespace Phabricator\Api;

class Differential extends Api{

	/**
		'authors'           => 'optional list<phid>',
		'ccs'               => 'optional list<phid>',
		'reviewers'         => 'optional list<phid>',
		'paths'             => 'optional list<pair<callsign, path>>',
		'commitHashes'      => 'optional list<pair<enum<'.$hash_types.'>, string>>',
		'status'            => 'optional enum<'.$status_types.'>',
		'order'             => 'optional enum<'.$order_types.'>',
		'limit'             => 'optional uint',
		'offset'            => 'optional uint',
		'ids'               => 'optional list<uint>',
		'phids'             => 'optional list<phid>',
		'subscribers'       => 'optional list<phid>',
		'responsibleUsers'  => 'optional list<phid>',
		'branches'          => 'optional list<string>',
		'arcanistProjects'  => 'optional list<string>',
	*/
	public function query($params = array()){
		return $this->client->request('differential.query',"POST",$params);	
	}
	
}
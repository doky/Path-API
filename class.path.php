<?php

/*
   Copyright 2012 Aurélien Hérault

   Licensed under the Apache License, Version 2.0 (the "License");
   you may not use this file except in compliance with the License.
   You may obtain a copy of the License at

       http://www.apache.org/licenses/LICENSE-2.0

   Unless required by applicable law or agreed to in writing, software
   distributed under the License is distributed on an "AS IS" BASIS,
   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
   See the License for the specific language governing permissions and
   limitations under the License.
   
   
   ABOUT PATH
   
   All trademarks are the property of their respective owners.
   
   About path 	 : https://path.com/about
   Download apps : https://path.com/
   
   METHODS
   
   * login 
   Set HTTP Authentification header for all requests
   
   * init
   Load data from current logged user
   
   * httpRequest
   Method for httpRequest
   
   * get*******
   Wrapper of path methods.
   
   CHANGELOG
   
   01/02/2012 	- Fisrt version of the Path Wrapper (Read-Only)
   
*/

class path_wrapper{
	
	/*configuration*/
	private $api_url 	   = "https://api.path.com/3/";
	private $authorization = false;
	
	/*user informations & data*/
	public $user;
	public $locations;
	public $deleted_moments;
	public $moments;
	public $music;
	public $places;
	public $sleep;
	public $users;
	
	
    /**
    * @function        public        	login          # Set authorization hash and check connection 
    * 
    * @param           string           $username      # username
    * @param           string           $password      # password
    * 
    * @return          array				   		   # user data
    * 
    **/
		
	public function login($username, $password) {
	
		if (!isset($username) && !isset($password) && !empty($username) && !empty($password)) {
			
			throw new Exception("Incorrect username or password"); 	
		
		}
		
		$this->authorization = base64_encode($username.':'.$password);

		
		return $this->init();

	}
	
	
	
	/**
    * @function        public        	getComments	   		# Get comments
    * 
    * @param           string           $moment_id      	# password
    *
    * @return          array				   		   		# Array of comments/moments
    * 
    **/		
	
	public function getComments($moment_id){
		
		$time = microtime() - 43200000;

		$url  = $this->api_url."moment/comments?moment_ids=".$moment_id;
		
		$data = $this->httpRequest($url);

		if($data == false){
			throw new Exception("Bad guy, bad data!");
			
		}
		
		return json_decode($data);
	}
	
	
	/**
    * @function        public        	getPath	   			# Get path feed
    * 
    * @return          array				   		   		#  Array of path activity
    * 
    **/		
	
	public function getPath(){
		
		$time = microtime() - 43200000;

		$url  = $this->api_url."moment/feed/home?all_friends=1&newer_than=".$time;
		
		$data = $this->httpRequest($url);

		if($data == false){
			throw new Exception("Bad guy, bad data!");
			
		}
		
		return json_decode($data);
	}


	/**
    * @function        public        	getHome		   			# Get home feed
    * 
    * @return          array				   		   		    #  Array of personnal activity
    * 
    **/		
	
	public function getHome(){
		
		$time = microtime() - 43200000;

		$url  = $this->api_url."moment/feed?newer_than=".$time;
		
		$data = $this->httpRequest($url);

		if($data == false){
			throw new Exception("Bad guy, bad data!");
			
		}
		
		return json_decode($data);
	}


	/**
    * @function        public        	getFriends   			# Get friends list
    * 
    * @return          array				   		   		    #  Array of friends
    * 
    **/		
	
	public function getFriends(){
		
		$url  = $this->api_url."user/friends?user_id=".$this->user->id;
		
		$data = $this->httpRequest($url);

		if($data == false){
			throw new Exception("Bad guy, bad data!");
			
		}
		
		return json_decode($data);
	}

	
	/**
    * @function        public        	getActivity   			# Get activity feed
    * 
    * @return          array				   		   		    #  Array of activity
    * 
    **/		
	
	public function getActivity(){
		
		$url  = $this->api_url."activity";
		
		$data = $this->httpRequest($url);

		if($data == false){
			throw new Exception("Bad guy, bad data!");
			
		}
		
		return json_decode($data);
	}
	
	
	/**
    * @function        public        	init	   # Load data from current user 
    * 
    * @return          array				   	   # user data
    * 
    **/	
    
	private function init(){
		
		$data = $this->get("home");
		
		if(!isset($data) && $data != false){
		  
		  	throw new Exception("A problem with you :)"); 	
		
		}
		
		if (!isset($data)){
			
			throw new Exception("A problem with you and your data! :)"); 	
			
		}

		$this->user->id 	  = (!isset ($data->cover->user->id)) ? '0' : $data->cover->user->id;
		$this->user->joined   =	(!isset ($data->cover->user->joined)) ? '0' : $data->cover->user->joined;
		$this->user->cover 	  =	(!isset ($data->cover->photo->url)) ? '0' : $data->cover->photo->url.'/'.$data->cover->photo->web->file;
		$this->user->original =	(!isset ($data->cover->photo->url)) ? '0' : $data->cover->photo->url.'/'.$data->cover->photo->original->file;
		$this->user->moments  = (!isset ($data->cover->moments)) ? '0' : $data->cover->moments;
		
		$this->locations 	   = (!isset ($data->locations)) ? '0' : $data->locations;
		$this->deleted_moments = (!isset ($data->deleted_moments)) ? '0' : $data->deleted_moments;
		$this->moments 		   = (!isset ($data->moments)) ? '0' : $data->moments;
		$this->music 		   = (!isset ($data->music)) ? '0' : $data->music;
		$this->places 		   = (!isset ($data->places)) ? '0' : $data->places;
		$this->sleep 		   = (!isset ($data->sleep)) ? '0' : $data->sleep;
		$this->users           = (!isset ($data->users)) ? '0' : $data->users;
		
		return $this->user;
	}
	
	
	/**
    * @function        private        	httpRequest    # Http requests
    * 
    * @param           string           $url	       # path url
    * 
    * @return          array				   		   # data
    * 
    **/	
	
	private function httpRequest($url){
		
		
		if (!empty($url) && !isset($url) && isset($this->authorization)){
				
			throw new Exception("Incorrect url or authorization");
				
		}
		
		$ch = curl_init();
		
		$http_headers = array('xPath Wrapper',
				 		 	  'Authorization: Basic '.$this->authorization,
		 		 		      'Accept-Charset: utf-8');
				
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $http_headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$data 		= curl_exec($ch);
		$curl_err 	= curl_errno($ch);
		$http_code 	= curl_getinfo($ch, CURLINFO_HTTP_CODE);
		
		if ($curl_err){	
			throw new Exception("Curl Error : ".curl_error($ch));
		}
		
		if ($http_code != 200){
			
			switch($http_code){
				case 400: $err_msg = '400 Bad guy'; break;
				case 404: $err_msg = '404 Not Found - Crappy url'; break;
				case 403: $err_msg = '403 Forbidden - Bad login/password'; break;
				default : $err_msg = $http_code.' - Bad news'; break;
			}
			
			/* More information about HTTP ERROR : http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html */
			
			throw new Exception("Http Error : ".$err_msg);
		}
		
		return $data;
		
	}

	
	
}



?>

<?php
/**
* A CodeIgniter library to allow one step creation of thumbnails from bit.ly and twitpic type URLs
* makes use of the LongUrl.org API
*
* Example Usage:
*	$this->load->library('longpic');
*	$this->longpic->lengthen('http://bit.ly/longurl');  
*	$this->longpic->thumbnail('http://bit.ly/longurl');  
*
* @author Steven Milne <steven@digitaldelivery.co.uk>
* @license Creative Commons Attribution-Share Alike 3.0 Unported
* http://creativecommons.org/licenses/by-sa/3.0/
**/

class Longpic {
	var $type = 'xml';
	var $user_agent = 'Longpic Library by Steven Milne (http://digitaldelivery.co.uk)';
 
	var $last_error;
 
	function lengthen($url){
		// the fine work of the longurl project - see longurl.org
		return $this->_fetch('http://api.longurl.org/v1/expand?url='.$url);
	}
	
	function thumbnail($url){
		$longthumb = '';
		// ######## YFROG.com ########
		// API DOCS AT http://code.google.com/p/imageshackapi/wiki/YFROGthumbnails
		// this is here because the longurl project turns this into an unthumbable url
		$long_part = "http://yfrog.com/";
		$thumb_part = "http://yfrog.com/";
		if(substr_count($url,$long_part)>0)$longthumb = $url.".th.jpg";  
		
		// NOW WE CHEAT and grab an instance to allow us to access the longurl library
		$url = $this->lengthen($url)->long_url;
		
		// #######  PIKCHUR.com ########
		// API DOCS AT http://pikchur.com/api
		$long_part = "http://pikchur.com/";
		$thumb_part = "https://s3.amazonaws.com/pikchurimages/pic_";
		if(substr_count($url,$long_part)>0)$longthumb = str_replace($long_part,$thumb_part, $url)."_t.jpg";  
	 
		// #######  TWITPIC.com ########
		// API DOCS AT http://twitpic.com/api.do
		$long_part = "http://twitpic.com/";
		$thumb_part = "http://twitpic.com/show/mini/";
		if(substr_count($url,$long_part)>0) $longthumb = str_replace($long_part,$thumb_part, $url);  
	 
		// #######  TWEETPHOTO.com ########
		// API DOCS AT http://www.tweetphoto.com/api-documentation.php 
		$long_part = "http://www.tweetphoto.com/";
		$thumb_part = "http://www.tweetphoto.com/show/mini/";
		if(substr_count($url,$long_part)>0)$longthumb = str_replace($long_part,$thumb_part, $url);  
	 
		// #######  PICKTOR.com ########
		// no api docs available	
		$long_part = "http://www.picktor.com/";
		$thumb_part = "http://www.picktor.com/get_photo_twitter.html?pic=";
		if(substr_count($url,$long_part)>0)$longthumb = str_replace($long_part,$thumb_part, $url)."&mode=150&crop=1"; 
	 
		// #######  PIKTER.com ########
		// no api docs available	
		$long_part = "http://pikter.com/view.php?V=";
		$thumb_part = "http://pktrs.com/photos/";
		if(substr_count($url,$long_part)>0)$longthumb = str_replace($long_part,$thumb_part, $url)."_tmb.jpg"; 
	 
		// #######  PHODROID.com ########
		// no api docs available	
		$long_part = "http://phodroid.com";
		$thumb_part = "http://s.phodroid.com";
		if(substr_count($url,$long_part)>0)$longthumb = str_replace($long_part,$thumb_part, $url).".jpg"; 
	 
		// #######  SCREENTWEET.com ########
		// no api docs FOUND	
		$long_part = "http://screentweet.com/";
		$thumb_part = "http://screentweet.com/content/pipe/?igcid=";
		if(substr_count($url,$long_part)>0)$longthumb = str_replace($long_part,$thumb_part, $url).";relPath=null;fileName=tn_small.jpg"; 
		
		
	 
	 
		if($longthumb>'') return $longthumb;
		else return FALSE;
	}
	
	/*
		System Methods adapted from CodeIgniter-Twitter Library by Simon Maddox (http://simonmaddox.com)
	*/

	function _fetch($url){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($ch, CURLOPT_USERAGENT, $this->user_agent);
		$returned = curl_exec($ch);
		$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close ($ch);
		
		if ($status == '200'){
			return $this->_parse_returned($returned, $url);
		} else {
			$error_data = $this->_parse_returned($returned, $url);
			$this->last_error = array('status' => $status, 'request' => $error_data->request, 'error' => $error_data->error);
			return false;
		}
	}
	
	function _post($url,$array){
		$params = $this->_build_params($array,FALSE);
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_USERPWD, $this->username . ':' . $this->password);
		curl_setopt($ch, CURLOPT_USERAGENT, $this->user_agent);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		$returned = curl_exec($ch);
		$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close ($ch);

		if ($status == '200'){
			return $this->_parse_returned($returned, $url);
		} else {
			$error_data = $this->_parse_returned($returned, $url);
			$this->last_error = array('status' => $status, 'request' => $error_data->request, 'error' => $error_data->error);
			return false;
		}
	}
	
	function _parse_returned($xml, $url){		
		switch ($this->type){
			case 'xml': 
				return $this->_build_return(new SimpleXMLElement($xml, LIBXML_NOCDATA),$this->type);
				break;
			case 'atom':
			case 'rss':
				return $this->_build_return(new SimpleXMLElement($xml, LIBXML_NOCDATA),$this->type);
				break;
			case 'json':
				return $this->_build_return(json_decode($xml),$this->type);
				break;
		}
	}
	 
	
	function _build_return($data,$type){
		if ($type == 'xml'){
			$data = json_decode(json_encode($data)); 
			$keys = array();
			foreach($data as $key => $value){
				if ($key !== '@attributes'){
					$keys[] = $key;
				}
			}
			if (count($keys) == 1){
				return $data->$keys[0];
			}
		}
		return $data;
	}
	
	function _build_params($array, $query_string = TRUE){
		$params = '';
		foreach ($array as $key => $value){
			if (!empty($value)){
				$params .= $key . '=' . $value . '&';
			}
		}
		$character = ($query_string) ? '?' : '';
		return (!empty($params)) ? $character . $params : '';
	}
	
	function get_last_error(){
		return $this->last_error;
	}
}
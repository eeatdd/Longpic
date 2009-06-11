<?php

class Test_longpic extends Controller {

	function Test_longpic()
	{
		parent::Controller();	  
		$this->load->library(array('longpic')); 
	}
	
	function index()
	{   
		$data['testurls'] = array(	'http://bit.ly/7mn8Z', 
							'http://twitpic.com/6owx3', 
							'http://twitpic.com/6mo9z', 
							'http://www.tweetphoto.com/9504xqi', 
							'http://yfrog.com/0fh5rvw4j',
							'http://www.picktor.com/ODUz',
							'http://pikchur.com/aUF',
							'http://pikter.com/view.php?V=4576',
							'http://phodroid.com/09/06/xf7m7t',
							'http://screentweet.com/0Kqop76', 
							'http://pix.im/EdILY');
							
		$this->load->view('view_longpic', $data);     
	}
}

/* End of file userpage.php */
/* Location: ./system/application/controllers/userpage.php */
<?php

class Society
{
	public $name;
	public $subtitle;
	public $type;
	public $description;
	public $logo;
	public $website;
	public $facebook;
	public $twitter;
	public $email;

	public function echoName(){
		echo $this->name;
	}

	public function echoLink($id){
		echo 'http://perform.susu.org/house/'.$this->link.'?id='.$id;
	}

	public function echoLogo($class){
		echo '<img class="'.$class.'" src="'.$this->logoURL.'" />"';
	}
}
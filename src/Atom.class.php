<?php
/**
 * Class Atom 
 *
 * @param string $content 
 */
	class Atom{
		public $content;
		public $value;
		
		function Atom($content){
			$this->content = $content;
			$this->value = 0; //valoracao do atom(1 = true, 0 = false)
		}
	}
?>

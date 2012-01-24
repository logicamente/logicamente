<?php
/**
 * Class Connective
 * Classe estrutura de conectivos
 */
class Connective {
	public $content;
	public $arity;
	public $order;
	public $value;
	public $children;
	
	/**
	 * Construtor da classe Conective
	 *
	 * @param string $content
	 * @param integer $arity
	 * @param integer $order
	 * @return Connective
	 */
	
	public function Connective($content, $arity, $order){
		$this->content = $content;
		$this->arity = $arity;
		$this->order = $order;
		$this->value = 0;
		$this->children = array();
	}
}
?>
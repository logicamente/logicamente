<?php
/**
 * class Node
 * node da arvore que representa uma formula
 * 
 * @param Connective | Atom | Quantificador $content
 */
	class Node{
		public $content;
		public $children = array();
		function Node($content){
			$this->content = $content;
			$this->children = array();
		}

		/**
		 * verifica se o node eh um atom
		 *
		 * @return boolean
		 */
		function isAtom(){
			return is_a($this->content, 'Atom');
		}
		
		public function __toString() {
			$t = new WFFTranslator();
			return $t->showFormulaInfix( $this );
		}
	}
?>

<?php
	/**
	* Signature.class.php
	*
	* @author Max Rosan
	* @author Thales Galdino
	* @author Giuliano Vilela
	* @author Lucas Araújo
	*/

	error_reporting(E_ALL);

	require_once("Node.class.php");
	require_once("Term.class.php");
	require_once("Constant.class.php");
	require_once("Variable.class.php");
	require_once("Function.class.php");
	require_once("Relation.class.php");

	/**
	* class Signature.
	* Responsável por extrair a assinatura mínima
	* de uma fórmula qualquer.
	*/
	class Signature {
		/**
		* Construtor recebe uma fórmula e já processa, pegando a assinatura.
		* @param $formula Objeto Node, representando a raiz da árvore da fórmula.
		*/
		public function Signature($formula) {
			$this->signature = array();
			$this->getSignature($formula);
		}

		/**
		* Método interno para extrair a assinatura.
		* Analisa o nó atual e recursivamente observa os seus filhos.
		* @param $formula Objeto Node, representando a raiz da árvore da fórmula atual.
		*/
		public function getSignature($formula) {
			// Caso o nó atual represente um elemento que pertença
			// à assinatura mínima relativa à fórmula
			if (($formula->content instanceof Constant) or
			    ($formula->content instanceof Variable) or
			    ($formula->content instanceof Func) or
			    ($formula->content instanceof Relation)) {

				// Vê se o elemento já tinha saido adicionado antes
				$exists = false;
				foreach ($this->signature as $element) {
					if ($element->content == $formula->content->content) {
						$exists = true;
						break;
					}
				}

				// Caso não tenha sido, adiciona-o à assinatura da fórmula
				if (!$exists) $this->signature[] = $formula->content;
			}

			// Recursivamente analisa os filhos do nó atual
			foreach ($formula->children as $value) {
				$this->getSignature($value);
			}
		}

		public $signature;
	}

?>

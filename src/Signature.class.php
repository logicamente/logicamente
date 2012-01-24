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

	class Signature {
		public function Signature($formula) {
			$this->relations = array();
			$this->functions = array();
			$this->constants = array();
			$this->getSignature($formula);
		}

		private function getSignature($formula) {
			if (($formula->content instanceof Constant) or
			    ($formula->content instanceof Variable && !($formula->content->isLinked)))
				$arr = &$this->constants;
			elseif ($formula->content instanceof Func)
				$arr = &$this->functions;
			elseif ($formula->content instanceof Relation)
				$arr = &$this->relations;

			if (isset($arr)) {
				$exists = false;
				foreach ($arr as $element) {
					if ($element->content == $formula->content->content) {
						$exists = true;
						break;
					}
				}

				if (!$exists)
					$arr[] = $formula->content;
			}

			foreach ($formula->children as $value) {
				$this->getSignature($value);
			}
		}

		public $relations;
		public $functions;
		public $constants;
	}

?>

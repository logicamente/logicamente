<?php
require_once("mod_converter/formulaConverter2.class.php");

class readFormula{
	
	/* Identificador para o tipo de expressão a ser trabalhada*/
	private $type;
	
	/**
	 * Construtor.
	 *
	 * @param String $type Letra inicial pindicando o formato da expressão.
	 * @return formulaConverter
	 */
	public function readFormula($type) {
		$this->type = new formulaConverter($type);
	}
	
		/* Módulo formulaConverter em formato TXT e
	Também deve ser usado paraos módulos ambiguityParser e parenthesesSaver. */
	public function TXTformulaConverter() {
		$this -> connectivesArray = Array();
		array_push ($this -> connectivesArray,new Connective("-->",2,250));
		array_push ($this -> connectivesArray,new Connective("<->",2,250));
		array_push ($this -> connectivesArray,new Connective("&",2,350));
		array_push ($this -> connectivesArray,new Connective("|",2,300));
		array_push ($this -> connectivesArray,new Connective("+",2,250));
		array_push ($this -> connectivesArray,new Connective("~",1,400));
		$this->noAssociativity["-->"] = false;
		$this->noAssociativity["+"] = false;
		$this->especialRelations = "^(1|0)";
	}
	
	/* Módulo formulaConverter em formato polonês. */
	public function polformulaConverter() {
		$this -> connectivesArray = Array();
		array_push ($this -> connectivesArray,new Connective("C",2,250));
		array_push ($this -> connectivesArray,new Connective("E",2,250));
		array_push ($this -> connectivesArray,new Connective("K",2,350));
		array_push ($this -> connectivesArray,new Connective("A",2,300));
		array_push ($this -> connectivesArray,new Connective("X",2,250));
		array_push ($this -> connectivesArray,new Connective("N",1,400));
		$this->noAssociativity["C"] = false;
		$this->noAssociativity["X"] = false;
		$this->especialRelations = "^(I|O)";
		
	}
	
	/* Módulo formulaConverter em formato funcional. */
	public function funcformulaConverter() {
		$this -> connectivesArray = Array();
		array_push ($this -> connectivesArray,new Connective("imp",2,250));
		array_push ($this -> connectivesArray,new Connective("eq",2,250));
		array_push ($this -> connectivesArray,new Connective("and",2,350));
		array_push ($this -> connectivesArray,new Connective("or",2,300));
		array_push ($this -> connectivesArray,new Connective("xor",2,250));
		array_push ($this -> connectivesArray,new Connective("neg",1,400));
		$this->noAssociativity["imp"] = false;
		$this->noAssociativity["xor"] = false;
		$this->especialRelations = "^(top|bottom)";
	}
	
	/**
	 * Método para montar a árvore de uma expressão Infixa (TXT).
	 *
	 * @param String $expression Expressão em formato TXT.
	 * @return node O nó raiz da a árvore montada.
	 */
	public function infixToTree($expression) {
		/*Testa se existe a mesma quantidade de '(' e ')'*/
		if ($this->type->testParentheses($expression)) {
			$arrayExpression = array();
			$arrayExpression = $this->type->propToArray($expression,$arrayExpression);
			$expression = $this->type->toPrefix($arrayExpression, false);
			$arrayExpression = Array();
			$arrayExpression = $this->type->propToArray($expression,$arrayExpression);
			$arrayExpression = $this->type->putParentheses($arrayExpression);
			$expression = $arrayExpression[0];
			return $this->makeTree($expression);
		} else return "";
	}

	/**
	 * Método para montar a árvore de uma expresão Prefixa (Polonesa).
	 *
	 * @param String $expression Expressão em formato polonês.
	 * @return node O nó raiz da a árvore montada.
	 */
	public function prefixToTree($expression) {
		/*Testa se existe a mesma quantidade de '(' e ')'*/
		if ($this->type->testParentheses($expression)) {
			$arrayExpression = array();
			$arrayExpression = $this->type->prefixToArray($expression,$arrayExpression);
			$expression = $this->type->toPrefix($arrayExpression, true);
			$arrayExpression = Array();
			$arrayExpression = $this->type->prefixToArray($expression,$arrayExpression);
			$arrayExpression = $this->type->putParentheses($arrayExpression);
			$expression = $arrayExpression[0];
			return $this->makeTree($expression);
		} else return "";
	}
	
	/**
	 * Método para montar a árvore de uma expressão funcional.
	 *
	 * @param String $expression Expressão em formato funcional
	 * @return node O nó raiz da a árvore montada.
	 */
	public function functionalToTree($expression) {
		/*Testa se existe a mesma quantidade de '(' e ')'*/
		if ($this->type->testParentheses($expression)) {
			$index = 0;
			/*Laço para remover todos os espaços existentes na expressão*/
			while (!ctype_graph($expression)) {
				/*Laço que busca um espaço*/
				while ($expression[$index] != " ") {
					$index++;
				}
				/*Testa se index é igual a 0*/
				if ($index == 0) $expression = substr($expression,1);
				else $expression = substr($expression,0,$index) . substr($expression,$index+1);
			}
			return $this->makeTree($expression);
		} else return "";
	}
	
	private function makeTree($expression) {
		$array = $this->type->connectiveChildren($expression);
		$connective = $array['connective'];
		/*Testa se op é um conectivo*/	
		if ($this->type->isConnective($connective)) {
			$arity = 0;
			$order = $this->type->connectiveOrder($connective);
			$tree = new Connective($connective,0,$order);
			/*Laço que cjama a recursão para cada argumento*/
			foreach ($array['children'] as $child) {
				$arity++;
				$children = $this->makeTree($child);
				array_push($tmp->children, $children);
			}
			$tree->arity = $arity;
			return $tree;
		/*Caso contrário é um átomo*/	
		} else {
			$tree = new Atom($connective);
			return $tree;
		}
	}
	
	public function printTree ($node,$ident) {
		$this->type->printTree($node,$ident);
	}
}
?>

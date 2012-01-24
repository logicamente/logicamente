<?php
require_once("Node.class.php");
require_once("Variable.class.php");
require_once("Constant.class.php");
require_once("Relation.class.php");
require_once("Function.class.php");
require_once("Quantifier.class.php");
require_once("Connective.class.php");
require_once("mod_converter/formulaConverter2.class.php");

/**
 * Classe para substituir um termo livre para uma variável na fórmula
 * 
 * @class SubstitutionMaster
 * 
 * @author Luciano Pereira Vieira
 * @version 2.0
 */


class SubstitutionMaster extends formulaConverter {
	public $varsTerm = array();  # Vetor contendo todas as variáveis do termo
	private $quantificadores = "A|E|E!"; #Quantificadores (POG pq a classe formulaConverter não está funcionando para 1ª ordem)
	private $livre = true; #Variável para guardar se a termo é livre ou não para a variável na fórmula
	
	/**
	 * Construtor da classe SubstitutionMaster
	 */
	public function __construct(){
		parent::formulaConverter("t", ""); # Construtor da classe herdada
	}
	
	/**
	 * Método para setar as variáveis ligadas e livres da fórmula
	 *
	 * @param Node $formula
	 * @param Array $vl
	 * @return void
	 */
	public function setVars(&$formula, $vl = array()){
		$node = $formula->content;
		if ($node instanceof Connective && ereg($this->quantificadores,$node->content)){
			array_push($vl,substr($node->content,1,1));
		}
		else if ($node instanceof Variable) {
			if (array_search($node->content,$vl) === false){
				$node->isLinked = false;
			}
			else {
				$node->isLinked = true;
			}
		}
		
		foreach ($formula->children as $n){
			$this->setVars($n,$vl);
		}
	}
        
	/**
	 * Método para pegar todas as variáveis do termo
	 *
	 * @param Node $term (Nó raiz do termo)
	 * @return void
	 */
	private function getVariablesTerm(&$term){
		$node = $term->content;
		if ($node instanceof Variable){
			$this->varsTerm[] = $node->content;
		}
		else {
			foreach ($term->children as $son){
				$this->getVariablesTerm($son);
			}
		}
	}
	
	/**
	 * Método para avaliar se o termo é livre para a variável na fórmula
	 *
	 * @param String $var
	 * @param Node $formula
	 * @param array $vl
	 */
	private function isFree(&$var, &$formula, $vl = array()){
		$node = $formula->content;
		if ($node instanceof Connective && ereg($this->quantificadores,$node->content)){
			substr($node->content,1,1) != $var->content ? array_push($vl,substr($node->content,1,1)) :  null;
		}
		
		if ($node instanceof Variable){
			if ($node->content == $var->content){
				if (count(array_intersect($vl, $this->varsTerm)) > 0){
					$this->livre = false;
				}
				else {
					foreach ($formula->children as $n){
						$this->isFree($var, $n, $vl);
					}
				}
			}
			else {
				foreach ($formula->children as $n){
					$this->isFree($var, $n, $vl);
				}
			}
		}
		else {
			foreach ($formula->children as $n){
				$this->isFree($var, $n, $vl);
			}
		}
	}
	
	/**
	 * Método para avaliar e substituir um termo por uma variável na fórmula
	 *
	 * @param Node $termo
	 * @param Variable $var
	 * @param Node $formula
	 */
	public function substitua(&$termo, &$var, &$formula){
		$this->setVars($formula);
		$this->getVariablesTerm($termo);
		$this->isFree($var,$formula);
		if ($this->livre){
			$this->__substitua($termo, $var, $formula);
			echo "O termo &eacute; livre para a vari&aacute;vel na f&oacute;rmula.";
		}
		else {
			echo "O termo n&atilde;o &eacute; livre para a vari&aacute;vel na f&oacute;rmula.";
		}
	}
	
	/**
	 * Método que realmente substitue se for o caso, a variável pelo termo na fórmula
	 *
	 * @param Node $termo
	 * @param Variable $var
	 * @param Node $formula
	 */
	private function __substitua(&$termo, &$var, &$formula){
		$node = $formula->content;
		if ($node instanceof Variable){
			if ($node->content == $var->content && !$node->isLinked){
				$formula->content = $termo->content;
				$formula->children = $termo->children;
			}
		}
		else {
			foreach ($formula->children as $n){
				$this->__substitua($termo, $var, $n);
			}
		}
	}
	
	/**
	 * Destrutor da classe, zera as variáveis
	 *
	 */
	public function __destruct(){
		$this->livre = true;
		$this->varsTerm = array();
	}
}


/************** EXEMPLO DE USO DA CLASSE **************/

/*
$formulaConverter = new formulaConverter("t","");

$RaizFormula = $formulaConverter->infixToTree("Ax(Ey(P(x,y) <-> ((P(x,y) --> (R(f(w),z) | Az(Q(y,h(z)) & Q(z,h(w))))) --> P(x,y))))",true);
$Termo = $formulaConverter->infixToTree("f(w)", true);

$SubMaster = new SubstitutionMaster();
$SubMaster->substitua($Termo, new Variable("z"), $RaizFormula);
echo "<p></p>";
$formulaConverter->printTree($RaizFormula,"");
*/
?>

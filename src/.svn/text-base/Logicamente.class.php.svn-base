<?php		 	    
require_once("mod_converter/formulaConverter2.class.php");
require_once("mod_generator/WFFGenerator.class.php");
require_once("mod_resolutiongame/ResolutionGame.class.php");
require_once("mod_truthtable/TruthTable.class.php");
require_once("mod_diagnoser/MiniDiagnoser.class.php");
require_once("mod_substitution/SubstitutionMaster.class.php");
require_once("mod_skolemizer/Skolemizer.class.php");
require_once("mod_prenexconverter/PrenexConverter.class.php");

require_once("PatternMatcher.class.php");

$counter = 0;

class Logicamente{

	function showFormula($formula){
		if ( count($formula->children) > 0){
			echo ("(");
			for ($i = 0; $i < count ($formula->children); $i++){
				if ($formula->content->arity == 1)
					echo (" ".$formula->content->content." ");
				$this->showFormula ($formula->children[$i]);
				if ($i < (count ($formula->children) - 1))
					echo (" ".$formula->content->content." ");
			}
			echo (")");
		} else
			echo (" ".$formula->content->content." ");
	}

	function generateFormulas($nConnectives, $nAtoms, $connectives){
		/*
		$connectives = array();

		foreach($symbol as $key => $value)
			array_push ($connectives, new Connective($value, $arity[$key]));
		*/

		$gerador = new WFFGenerator($nConnectives, $nAtoms, $connectives);
		$form = $gerador->getFormula();

		$this->showFormula( $form->root );
		return $form->root;
	}

	function checkFormula($formula, $connectives){
		$checker = new readFormula();
		$checker->checkFormula($formula, $connectives);
	}

	function readFormula($formula,$array,$type=false) {
		$formulaConverter = new formulaConverter("T",$array);
		return $formulaConverter->infixToTree($formula, $type);
	}

	function printFormula($node,$array) {
		//print_r($node);
		$formulaConverter = new formulaConverter("T",$array);
		$formulaConverter -> printTree($node,"");
	}

	function startGame($tree) {
		$game = new ResolutionGame();
		$game->addConvertedTree($tree);
		return $game;
	}

	function normalizeDisjunctions($disjunction) {
		$patternMatcher = new PatternMatcher();
		return $patternMatcher->normalizeDisjunctions($disjunction);
	}

	public function getTruthTable($formula){
		$this->modifyConnectives( $formula );
		//die();
		$objTable = new TruthTable($formula, 0);
		$objTable->printTable( 1 );
	}

	public function getTreeInteraction($formula){
		global $counter;
		$f = "<ul>";
		if (count($formula->children) > 0){
			$f .= "<li><p id='c".$formula->content->content."' class='connective' index='".$counter."'>".$formula->content->content."</p><script></script>";
			$counter++;
			for ($i = 0; $i < count ($formula->children); $i++){
				$f .= $this->getTreeInteraction ($formula->children[$i]);
			}
			$f .= "</li>";
		} else {
			$f .= "<li><p class='atom' index='".$counter."'>".$formula->content->content."</p></li>";
			$counter++;
		}

		return $f."</ul>";
	}

	/**
	 * Imprime uma fórmula árvore linearmente
	 *
	 * @param arvore $formula
	 */
	public function printTreePreOrder(&$formula){
		$quantificadores = "A|E|E!"; //POG devido a impplementação da classe formulaConverter (formulaConverter2.class.php) não está lendo fórmulas de primeira corretamente
		$node = $formula->content;
		if ($node instanceof Connective && !ereg($quantificadores,$node->content)){ // É Conectivo mesmo
			//echo "("; // Parênteses externos
			for ($i = count($formula->children)-1; $i >= 0 ; $i--){
				$this->printTreePreOrder($formula->children[$i]);
				if ($i > 0)
					echo $node->content;
			}
			//echo ")"; // Parênteses externos
		}
		if ($node instanceof Relation){
			echo "{$node->content}(";
			for ($i = 0; $i < count($formula->children); $i++){
				$this->printTreePreOrder($formula->children[$i]);
				if ($i < count($formula->children)-1)
					echo ",";
			}
			echo ")";
		}
		if ($node instanceof Func){
			echo "{$node->content}(";
			for ($i = 0; $i < count($formula->children); $i++){
				$this->printTreePreOrder($formula->children[$i]);
				if ($i < count($formula->children)-1)
					echo ",";
			}
			echo ")";
		}
		if ($node instanceof Variable ){
			echo $node->content;
		}
		if ($node instanceof Constant){
			echo $node->content;
		}
		if ($node instanceof Connective && ereg($quantificadores,$node->content)){ // É Quantificador
			echo "{$node->content}(";
			foreach ($formula->children as $children){
				$this->printTreePreOrder($children);
			}
			echo ")";
		}
	}

	private function modifyConnectives($formula){
		//print $formula->content->content." <br/>";
		switch( $formula->content->content ){
			case '~': $formula->content->content = '&not;'; print " NEG "; break;
			case '&': $formula->content->content = '&and;'; print " AND "; break;
			case '|': $formula->content->content = '&or;'; print " OR "; break;
			case '-->': $formula->content->content = '&rarr;'; print " IMP "; break;
			case '<->': $formula->content->content = '&harr;'; print " EQ "; break;
		}
		foreach( $formula->children as $child ){
			$this->modifyConnectives( $child );
		}
	}

	public function substitution($formula,$term,$variable){
		$formulaConverter = new formulaConverter("t","");
		//$RaizFormula = $formulaConverter->infixToTree($formula,true);
		$Termo = $formulaConverter->infixToTree($term,true);
		$SubMaster = new SubstitutionMaster();
		$SubMaster->substitua($Termo, new Variable($variable), $formula);
		//return $formulaConverter->printTree($RaizFormula,""); // Imprime a fórmula em árvore
		//echo ( "<div>".$this -> getTreeInteraction( $RaizFormula )."<div style='clear:both;'></div></div>" );
		echo "<br/><br/>\n\n";
		echo $formulaConverter->printFormula($formula,"");
		//echo $this->printTreePreOrder($formula); // Imprime linearmente
	}

	function skolemizer($formula){
		$Converter = new formulaConverter("T","");
		$skolem = new Skolemizer($formula);
		echo "<br /><b>Fórmula Skolemizada: <br /></b>";
		echo $Converter->printFormula($skolem->getCabeca(),"");
	}

	function prenex($formula){
		$Converter = new formulaConverter("T","");
		$prenex = new CNF($formula);
		echo "<br /><b>Fórmula FNP: <br /></b>";
		echo $Converter->printFormula($prenex->getCabeca(),"");
	}
}
?>

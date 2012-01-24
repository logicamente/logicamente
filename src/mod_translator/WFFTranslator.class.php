<?php

	class WFFTranslator{

		function WFFTranslator(){}

		function showFormulaInfix($formula){
			$f = "";
			if (count($formula->children) > 0){
				$f .= " (";
				for ($i = 0; $i < count ($formula->children); $i++){
					if ($formula->content->arity == 1)
						$f .= "<strong style='color:#000066;'>".$formula->content->content."</strong>";
					$f .= $this->showFormulaInfix ($formula->children[$i]);
					if ($i < (count ($formula->children) - 1))
						$f .= " <strong  style='color:#000066;'>".$formula->content->content."</strong> ";
				}
				$f .= ") ";
			} else
				$f .= $formula->content->content;
				
			return $f;
		}
	
		function showFormulaPrefix($formula){
			$f = "";
			if (count($formula->children) > 0){
				$f .= " <strong  style='color:#000066;'>".$formula->content->content."</strong> ";
				for ($i = 0; $i < count ($formula->children); $i++){
					$f .= $this->showFormulaPrefix ($formula->children[$i]);
				}
			} else
				$f .= " ".$formula->content->content." ";
	
			return $f;
		}
	
		function showFormulaPostfix($formula){
			$f = "";
			if (count($formula->children) > 0){
				for ($i = 0; $i < count ($formula->children); $i++){
					$f .= $this->showFormulaPostfix ($formula->children[$i]);
				}
				$f .= " <strong style='color:#000066;'>".$formula->content->content."</strong> ";
			} else
				$f .= " ".$formula->content->content." ";
	
			return $f;
		}
	
		function showFormulaFunctional($formula){
			$f = "";
			if (count($formula->children) > 0){
				$f .= " <strong  style='color:#000066;'>".$formula->content->content."</strong>(";
				$aux = "";
				for ($i = 0; $i < count ($formula->children); $i++){
					$f .= $aux.$this->showFormulaFunctional ($formula->children[$i]);
					$aux = ", ";
				}
				$f .= ") ";
			} else
				$f .= $formula->content->content;
	
			return $f;
		}
	}
	
// Teste -----------------------------------

	/*require_once("WFFGenerator.class.php");

	$conectivos = array();
	array_push($conectivos,new Connective("-->",2));
	array_push($conectivos,new Connective("<->",2));
	array_push($conectivos,new Connective("~",1));
	array_push($conectivos,new Connective("|",2));
	array_push($conectivos,new Connective("+",2));
	array_push($conectivos,new Connective("&&",2));
	
	$g = new WFFGenerator(10,10,$conectivos);
	echo "<h1>Translator Test</h1><table style='font-family:courier;'>";
	$f = $g->getFormula();

	$t = new WFFTranslator();
	
	echo ("<tr><td>Infix:</td><td>".$t->showFormulaInfix($f->root))."</td</tr>";
	echo ("<tr><td>Prefix:</td><td>".$t->showFormulaPrefix($f->root))."</td</tr>";
	echo ("<tr><td>Postfix:</td><td>".$t->showFormulaPostfix($f->root))."</td</tr>";
	echo ("<tr><td>Functional:</td><td>".$t->showFormulaFunctional($f->root))."</td</tr>";
	echo "</table><h2>End Test</h2>";*/
// Fim Teste ---------------------------------
?>
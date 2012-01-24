<?php

	require_once("Formula.class.php");
	require_once("Connective.class.php");
	require_once("Atom.class.php");
	require_once("Node.class.php");

	require_once("mod_translator/WFFTranslator.class.php");
	
	//Returns a random element from a vector
	function sortVector($vector){
		$chosen = rand(0,count($vector)-1);
		return $vector[$chosen];
	}

	class WFFGenerator{

		function WFFGenerator($nConnectives, $nAtoms, $connectives){
		
			//Set the variables needed
			$this->aCurrent = 0;
			$this->cCurrent = 0;
			$this->nAtoms = $nAtoms;
			$this->nConnectives = $nConnectives;
			$this->connectives = $connectives;
			$this->atoms = array();
			//Creates the atoms
			for ($i = 0; $i < $nAtoms; $i++)
				array_push($this->atoms, new Atom("p".$i));
			$this->formula = new Formula();
			//Start			
			$this->formula->root = $this->generateFormula(0, 1);
		}

		
		function generateFormula($atom, $last){
			//Tests if hadn´t created all connectives needed
			if ($this->cCurrent < $this->nConnectives && $atom == 0){
				//Selects a vector
				$connective = sortVector($this->connectives);
				//echo ("<ul><li>".$connective->content." (".$connective->arity.")</li>");
				$auxNode = new Node($connective);
				//A connective was used
				$this->cCurrent++;
				
				//Creates the children of the connective
				for ($i = 0; $i < $connective->arity; $i++){	
					if ($i == $connective->arity - 1) $isLast = 1;
					else $isLast = 0;

					if ($last == 0){
						array_push ($auxNode->children, $this->generateFormula(rand(0,1), $isLast));
					} else {
						array_push ($auxNode->children, $this->generateFormula(0, $isLast));
					}
				}
			//	echo ("</ul>");
				return $auxNode;
			} else {
				$auxNode = new Node($this->atoms[rand(0,$this->nAtoms-1)]);
			//	echo ("<ul><li><strong style='font-weight:bold;'>".$auxNode->content->content."</strong></li></ul>");
				return $auxNode;
			}
		}

		function getFormula(){
			return $this->formula;
		}
	}
	
	/*$cons = array();
	array_push($cons, new Connective("~",1,0));
	array_push($cons, new Connective("&",2,0));
	array_push($cons, new Connective("|",2,0));
	array_push($cons, new Connective("+",2,0));
	array_push($cons, new Connective("-->",2,0));
	array_push($cons, new Connective("<->",2,0));
	
	$g = new WFFGenerator(10,5,$cons);
	$t = new WFFTranslator();
	$ft = $g->getFormula();
	echo ("<br>".$t->showFormulaInfix($ft->root));*/
	
?>

<?

	require("Connective.class.php");
	require("Atom.class.php");
	require("Node.class.php");

	require("mod_translator/WFFTranslator.class.php");
	
class readFormula{
	function convertToRE($string){
		$string = str_replace("|","\\|",$string);
		$string = str_replace("+","\\+",$string);
		$string = str_replace("?","\\?",$string);
		$string = str_replace("-","\\-",$string);
		return $string;
	}
	
	function readFormula(){}
	
	function checkFormula($formula,$connectives){
	
		$auxUc = array();
		$auxZc = array();
		$auxC = array();
		
		foreach ($connectives as $c){
			if ($c->arity == 0) array_push($auxZc,$this->convertToRE($c->content));
			else if ($c->arity == 1) array_push($auxUc,$this->convertToRE($c->content));
			else array_push($auxC,$this->convertToRE($c->content));
		}
		
		$auxOr = ""; $ucs = "";
		foreach($auxUc as $uc){ $ucs .= $auxOr.$uc; $auxOr = "|"; }

		$auxOr = ""; $zcs = "";
		foreach($auxZc as $zc){ $zcs .= $auxOr.$zc; $auxOr = "|"; }

		$auxOr = ""; $cs = "(";
		foreach($auxC as $c){ $cs .= $auxOr.$c; $auxOr = "|"; }
		$cs .= ")";
	
		//echo ($ucs."<br>");
		//echo($zcs."<br>");
		//echo($cs."<br>");
		
		if ($ucs != "()") $ucs = "|".$ucs;
		else $ucs = "";
		
		if ($zcs != "") $zcs = "|".$zcs;
		else $zcs = "";
		
		if ($cs == "()") $cs = "";
		
		$v = "(\\(| ".$ucs.")*(([a-z]|[A-Z])+[0-9]+".$zcs.")( |\\))*";
	
		//echo ($v."(".$cs.$v.")*");
		
		if (ereg("^".$v."(".$cs.$v.")*$",$formula)){			
			$p = 0;
			for ($i = 0; $i < strlen($formula); $i++){
				if ($formula[$i] == "(") $p ++;
				else if ($formula[$i] == ")"){
					$p --;
					if ($p < 0){
						echo ("ERRO!<br>");
						break;
					}
				}
			}
			if ($p != 0) echo ("ERRO!<br>");
			else{
				echo ("OK!<br>");
				$this->toTree($formula,$connectives);
			}
		} else echo ("ERROR!<br>");
	}
	
	

	/**
	 * 
  	 * @param array $formula
	 * @param array $connectives
  	 */
	/*function getExternalConnective($formula, $connectives){
		//( ~ (( p2 <-> ( ~ p1 )) --> p0 ))
				
		foreach($a as $caracter){
			//verifica se é conectivo
			if(in_array($caracter, $connectives)){				
				$key = 
			}
		}
	}*/

	function is_connective($strCon, $connectives){
		foreach($connectives as $con){
			if( $strCon == $con->content ){
				return $con;
			}
		}
		return null;
	}

	
	function subFormula($s, $e, &$formula){
		$curr = $s;
		$external = NULL;
		$iExternal = 0;
		
		echo ("<h4>");
		foreach ($formula as $n){
			$imprimir = "";
			if ($n->usado) $imprimir = "<strong style='color:#0000FF;'>";
			if ($n->content == "(" || $n->content == ")") $imprimir .= "[".$n->content."]";
			else $imprimir .= "[".$n->content->content."]";
			if ($n->usado) $imprimir .= "</strong>";
			echo ($imprimir." ");
		}
		
		echo ("<h5>subFormula</h5>");
		while ($curr < $e){
			if ($formula[$curr]->content == ")" && !$formula[$curr]->usado) break;
			
			if ($formula[$curr]->content == "("){
				echo ($curr."[(]<br>");
				$formula[$curr] = $this->subFormula($curr+1,$e,$formula);
				$formula[$curr]->usado = true;
			} else {
				if (!$formula[$curr]->isAtom() && count($formula[$curr]->children) == 0 && !$formula[$curr]->usado){
					if (!$external){
						$external = $formula[$curr]; //Connective
						echo ("EXTERNAL: ");
						$iExternal = $curr;
					} else if ($external->content->order <= $formula[$curr]->content->order){
						$external = $formula[$curr];
						echo ("EXTERNAL: ");
						$iExternal = $curr;
					}
				}
				echo ($curr."[".$formula[$curr]->content->content."]<br>");			
			}
			$curr ++;
		}
		
		$formula[$curr]->usado = true;
		
		if ($external){
			$formula[$iExternal]->usado = true;
			if ($external->content->arity != 1)
				array_push($external->children,$this->subFormula($s,$iExternal-1,$formula));
			if ($external->content->arity != 0)
				
				array_push($external->children,$this->subFormula($iExternal+1,$curr-1,$formula));
			echo ("<h5>Fim subFormula</h5>");
			return $external;
		} else {
			echo ("<h5>Fim subFormula</h5>");
			$formula[$s]->usado = true;
			return $formula[$s];
		}
	}

	function toTree($formula, $connectives){
		
		$v = "([a-z]|[A-Z])+[0-9]+";
		$c = "";
		$auxOr = "";
		for ($i = 0; $i < sizeof($connectives); $i++){ $c .= $auxOr.$this->convertToRE($connectives[$i]->content); $auxOr = "|"; }
		$re = "(".$v."|".$c."|\(|\))";
		
		preg_match_all($re,$formula,$partes);
		
		$nodes = array();
		foreach ($partes[0] as $node){
			if ($node == "(" || $node == ")")
				$node = new Node($node);
			else
				if ($auxNode = $this->is_connective($node,$connectives)) $node = new Node($auxNode);
				else $node = new Node(new Atom($node));
			array_push($nodes,$node);
		}
		
		array_push($nodes,new Node(")"));
		array_unshift($nodes,new Node("("));
		return $this->subFormula(0,count($nodes),$nodes);
	}
}
	
	if (isset($_POST['f'])){
	
		$connectives = array();
		array_push ($connectives,new Connective("-->",2,4));
		array_push ($connectives,new Connective("<->",2,5));
		array_push ($connectives,new Connective("&",2,1));
		array_push ($connectives,new Connective("|",2,2));
		array_push ($connectives,new Connective("+",2,3));
		array_push ($connectives,new Connective("~",1,0));
		array_push ($connectives,new Connective("@",0,10));
	
		$rf = new readFormula();
		$t = new WFFTranslator();
		$resultado = $rf->toTree($_POST['f'], $connectives);
		echo ("<h5>Resultado</h5>");
		echo ($t->showFormulaInfix($resultado)."<br>");
		echo ($t->showFormulaPrefix($resultado)."<br>");
		echo ($t->showFormulaPostfix($resultado)."<br>");
		echo ($t->showFormulaFunctional($resultado)."<br>");
	}
?>

<form action="readFormula.class_besta.php" method="post">
	<input name="f" /><input type="submit" value="read" />
</form>

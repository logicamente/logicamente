<?php
//require ("Connective.class.php");
class Check{
	function checkFormula($formula, $connectives){
	
	
		$auxUc = array();
		$auxZc = array();
		$auxC = array();
		
		foreach ($connectives as $c){
			if ($c->arity == 0) array_push($auxZc,$c->content);
			else if ($c->arity == 1) array_push($auxUc,$c->content);
			else array_push($auxC,$c->content);
		}
		
		$auxOr = ""; $ucs = "(";
		foreach($auxUc as $uc){ $ucs .=$auxOr.$uc; $auxOr = "|"; }
		$ucs .= ")";

		$auxOr = ""; $zcs = "(";
		foreach($auxZc as $zc){ $zcs .=$auxOr.$zc; $auxOr = "|"; }
		$zcs .= ")";

		$auxOr = ""; $cs = "(";
		foreach($auxC as $c){ $cs .=$auxOr.$c; $auxOr = "|"; }
		$cs .= ")";
	
		echo ($ucs."<br>");
		echo($zcs."<br>");
		echo($cs."<br>");
	
		$v = "(\(* *|".$ucs.")*([a-z]+[A-Z]*[0-9]*|[a-z]*[A-Z]+[0-9]*|".$zcs.")( *\)*)*";
		
		echo ($formula." -> ");
		if (ereg("^".$v."(".$cs.$v.")*$",$formula)) echo ("OK!");
		else echo ("ERROR!");
	}
}
?>
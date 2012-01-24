<?php
	error_reporting(E_ALL);
	require_once("Node.class.php");
	require_once("Term.class.php");
	require_once("Quantifier.class.php");
	require_once("Connective.class.php");
	require_once("Constant.class.php");
	require_once("Variable.class.php");
	require_once("Function.class.php");
	require_once("Relation.class.php");
	
	require_once("mod_scm/SCMBuilder.class.php");
	require_once("mod_scm/SCMChecker.class.php");
	require_once("mod_scm/SCMUtil.class.php");

	$formula = $_POST['formula'];
	$univ_size = intval($_POST['univ_size'],10);

	$converter = new formulaConverter("T", "");
	$formula_tree = $converter->infixToTree($formula,true);
	
	$signature = new Signature($formula_tree);

	$builder = new SCMBuilder($signature,$univ_size);
	$checker = new SCMChecker(array($formula),$univ_size);

	$checker->last_usize = $univ_size;
	$model = $builder->firstModel();

	foreach($signature->relations as $rel) {
		if (!isset($_POST[$rel->content])) continue;

		foreach($_POST[$rel->content] as $tuple) {
			setMappedElement($model['_Relations'][$rel->content]['interp'],$tuple,0,true);
		}
	}

	foreach($signature->functions as $func) {
		if (!isset($_POST[$func->content])) continue;

		foreach($_POST[$func->content] as $tuple) {
			$el = 0;
			$args = array();
			
			foreach($tuple as $key => $val) {
				if ($key == sizeof($tuple)-1) {
					$el = intval($val);
				}
				else {
					$args[] = intval($val);
				}
			}
			
			setMappedElement($model['_Functions'][$func->content]['interp'],$args,0,$el);
		}
	}

	foreach($signature->constants as $cst) {
		if (!isset($_POST[$cst->content])) continue;

		$model['_Constants'][$cst->content] = intval($_POST[$cst->content]);
	}

	echo("<br/>");

	if ($checker->checkModel($formula_tree,$model)) {
		echo("Este modelo <strong>satisfaz</strong> a formula!");
	}
	else {
		echo("Este modelo <strong>nao satisfaz</strong> esta formula!");
	}

	echo("");
?>

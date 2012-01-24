<?php
	error_reporting(E_ALL);

	require_once("Signature.class.php");
	
	require_once("mod_translator/WFFTranslator.class.php");
	require_once("mod_converter/formulaConverter2.class.php");

	$formula = $_POST['formula'];
	$univ_size = $_POST['univ_size'];
	$form = "";

	$converter = new formulaConverter("T","");
 	$sig = new Signature($converter->infixToTree($formula,true));
	
	if (isset($_POST['make_tuple_rel'])) {
		$converter = new formulaConverter("T","");
 		$sig = new Signature($converter->infixToTree($formula,true));

		foreach ($sig->relations as $rel) {
			if ($rel->content != $_POST['name_rel']) continue;
	
			if ($_POST['tuple_code'] % 4 == 0 and $_POST['tuple_code'] != 0)
				$form .= "<br/><br/>";

			$form .= "  ".$_POST['tuple_code'].": ";

			for ($i = 0; $i < $rel->arity; ++$i) {
				$form .= " <select name=\"".$_POST['name_rel']."[" . $_POST['tuple_code'] . "][]\">";
				for ($u = 0; $u < $_POST['univ_size']; ++$u) {
					$form .= "<option value=\"${u}\">${u}</option>";
				}
				$form .= "</select>";
			}
		}

		echo $form;
		die();
	}

	if (isset($_POST['make_tuple_func'])) {
		$converter = new formulaConverter("T","");
 		$sig = new Signature($converter->infixToTree($formula,true));

		foreach ($sig->functions as $rel) {
			if ($rel->content != $_POST['name_func']) continue;
	
			if ($_POST['tuple_code'] % 4 == 0 and $_POST['tuple_code'] != 0)
				$form .= "<br/><br/>";

			$form .= "  ".$_POST['tuple_code'].": ";

			for ($i = 0; $i <= $rel->arity; ++$i) {

				if ($i == $rel->arity) {
					$form .= " = ";
				}

				$form .= " <select name=\"".$_POST['name_func']."[" . $_POST['tuple_code'] . "][]\">";
				for ($u = 0; $u < $_POST['univ_size']; ++$u) {
					$form .= "<option value=\"${u}\">${u}</option>";
				}
				$form .= "</select>";
			}			

			$form .= "</div>";
		}

		echo $form;
		die();
	}

	if (!isset($_POST['formula']) || !isset($_POST['univ_size']))
		die();

	$form .= "<fieldset>";
	$form .= "<legend>Relacoes</legend>";
	foreach ($sig->relations as $rel) {
		$form .= "<fieldset id =\"".$rel->content."\">";
		$form .= "<legend>".$rel->content."</legend>";
		$form .= "<button name=\"".$rel->content."\" id=\"rel_click\" onclick=\"return add_tuple_rel(this)\" >Adicionar tupla</button><br/><br>";
		$form .= "</fieldset>";
	}
	$form .= "</fieldset>";


	$form .= "<br/><fieldset>";
	$form .= "<legend>Funcoes</legend>";
	foreach ($sig->functions as $func) {
		$form .= "<fieldset id =\"".$func->content."\">";
		$form .= "<legend>".$func->content."</legend>";
		$form .= "<button name=\"".$func->content."\" id=\"func_click\" onclick=\"return  add_tuple_func(this)\">Adicionar tupla</button><br/><br>";
		$form .= "</fieldset>";
	}
	$form .= "</fieldset>";

	$form .= "<br/><fieldset>";
	$form .= "<legend>Variaveis / Constantes</legend>";
	foreach ($sig->constants as $cst) {
		$form .= "<fieldset id =\"".$cst->content."\">";
		$form .= "<legend>".$cst->content."</legend>";
		$form .= " <select name=\"".$cst->content."\">";
		for ($u = 0; $u < $_POST['univ_size']; ++$u) {
		$form .= "<option value=\"${u}\">${u}</option>";
		}
		$form .= "</select>";
		$form .= "</fieldset>";
	}
	$form .= "</fieldset>";

	echo $form;

?>

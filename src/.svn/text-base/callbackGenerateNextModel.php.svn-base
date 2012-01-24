<?php
	/**
	* testSCMChecker.php
	*
	* @author Max Rosan
	* @author Thales Galdino
	* @author Giuliano Vilela
	* @author Lucas Araújo
	*/

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



	function send($data = null) {
		echo (($data == null) ? json_encode(array()) : $data);
		die();
	}

	if (isset($_POST['clear']) and $_POST['clear'] == '1') {
		unset($_SESSION['scm_checker']);
		send();
	}

	if (!isset($_POST["formula"]) || !isset($_POST["univSize"])) {
		send();
	}

	$univ_size = $_POST["univSize"];
	$formula = json_decode($_POST["formula"]);

	if ($univ_size == 0 || sizeof($formula) == 0) {
		send();
	}

	if (!isset($_SESSION['scm_checker'])) {
		$_SESSION['scm_checker'] = new SCMChecker($formula,$univ_size);
	}

	$scm_checker = $_SESSION['scm_checker'];
	$model = $scm_checker->nextModel();
	$_SESSION['scm_checker'] = $scm_checker;

	if ($model == null) {
		send(json_encode(array()));
	}

	$ret_model = array(
		'_UnivSize' => $model['_UnivSize'],
		'_Constants' => $model['_Constants'],
		'_Relations' => array(),
		'_Functions' => array()
	);

	// Preparando as relações
	foreach($model['_Relations'] as $name => $sub_model) {
		$str = '';

		$int = $sub_model['interp'];
		$arr = array();
		relation_to_tuples($int,array(),$arr);

		$str .= '{ ';

		foreach ($arr as $k => $tupla) {
			$str .= '(';
			foreach ($tupla as $key => $val) {
				$str .= $val;
				if ($key != (sizeof($tupla)-1))
					$str .= ', ';
			}
			$str .= ')';
			if ($k != sizeof($arr)-1) $str .= ', ';
		}

		$str .= ' }';

		$ret_model['_Relations'][$name] = $str;
	}

	// Preparando as funções
	foreach($model['_Functions'] as $name => $sub_model) {
		$str = '';

		$int = $sub_model['interp'];
		$arr = array();
		function_to_tuples($int,array(),$arr);

		$str .= '{ ';

		foreach ($arr as $k => $tupla) {
			$str .= '(';
			foreach ($tupla as $key => $val) {
				$str .= $val;
				if ($key != (sizeof($tupla)-2))
					$str .= ', ';
				if ($key == sizeof($tupla)-2) break;
			}
			$str .= ') => '.$tupla[sizeof($tupla)-1];
			if ($k != sizeof($arr)-1) $str .= ', ';
		}

		$str .= ' }';

		$ret_model['_Functions'][$name] = $str;
	}

	send(json_encode($ret_model));
?>

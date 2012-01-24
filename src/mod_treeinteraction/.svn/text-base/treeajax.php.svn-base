<?php

	require_once("mod_generator/WFFGenerator.class.php");
	
	$counter = 0;
		
	function procura($index, $formula){
		global $counter;
		echo ($counter."<br>");
		if ($counter == $index)
			return $formula;
		else {
			$counter++;
			if (count($formula->children) > 0){
				for ($i = 0; $i < count ($formula->children); $i++){
					$retorno = procura ($index, $formula->children[$i]);
					if ($retorno != NULL) return $retorno;
				}
				return NULL;
			} else
				return NULL;
		}
	}
	
	$raiz = procura ($_POST['index'], unserialize($_SESSION['formula'])->root);
	
	if ($raiz != NULL){
		$t = new WFFTranslator();
		echo ($t->showFormulaInfix($raiz));
	} else echo ("There was an error while searching for the node index");
	
?>

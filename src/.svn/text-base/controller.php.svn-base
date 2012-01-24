<?php
/*require_once('/var/www/FirePHPCore/fb.php');
FB::log('Log message');
FB::info('Info message');
FB::warn('Warn message');
FB::error('Error message');
*/

header("Content-Type: text/html;  charset=ISO-8859-1",true);
ob_start();
session_start();
if (!isset($_SESSION['con'])) $_SESSION['con'] = "";
$_SESSION['con'];

require_once("Logicamente.class.php");

	//display modules
	if (isset($_POST['module'])){
		switch ($_POST['module']){
			case "menuSettings"  : require_once("mod_settings/settings.php");	  break;
			case "menuGenerator" : require_once("mod_generator/generator.php");  break;
			case "menuTranslator": require_once("mod_translator/translator.php"); break;
			case "menuChecker"   : require_once("mod_checker/checker.php");    break;
			case "menuReadFormula"   : require_once("mod_readformula/formReadFormula.php"); break;
			case "menuResolutionGame": require_once("mod_resolutiongame/formResolutionGame.php"); break;
			case "menuTester"   : require_once("mod_tester/formmenuTester.php"); break;
			case "menuTruthTable" : require_once("mod_truthtable/formTruthTable.php"); break;
			case "menuTreeInteraction" : require_once("mod_treeinteraction/formTreeInteraction.php"); break;
			case "menuSubstitutionMaster" : require_once("mod_substitution/formSubstitutionMaster.php"); break;
			case "menuSkolemizer" : require_once("mod_skolemizer/formSkolemizer.php"); break;
			case "menuPrenex" : require_once("mod_prenexconverter/formPrenex.php"); break;
			default: break;
		}
	}

	//display helpmodules
	if (isset($_POST['helpModule'])){
		switch ($_POST['helpModule']){
			case "menuResolutionGame": include("mod_resolutiongame/resolutionGame.php");	break;
			case "menuGenerator": include("mod_generator/generator.php");	break;
			case "menuReadFormula": include("mod_readformula/readFormula.php");	break;
			case "menuSettings" : include("mod_settings/help_settings.php");		break;
			default: include("help_logicamente.php");
		}
	}

	//up javasript
	if (isset($_POST['js'])){
		switch ($_POST['js']){
			case "menuGenerator" : require_once("mod_generator/formGenerator.js");            break;
			case "menuSettings"  : require_once("mod_settings/formSettings.js");             break;
			case "menuTranslator": require_once("mod_translator/formTranslator.js");           break;
			case "menuChecker"   : require_once("mod_diagnoser/formCheck.js");                break;
			case "menuReadFormula"   : require_once("mod_readformula/formReadFormula.js");      break;
			case "menuResolutionGame"   : require_once("mod_resolutiongame/formResolutionGame.js");      break;
			case "menuTester"   : require_once("mod_tester/formmenuTester.js");      break;
			case "menuTruthTable" : require_once("mod_truthtable/formTruthTable.js"); break;
			case "menuTreeInteraction" : require_once("mod_treeinteraction/formTreeInteraction.js"); break;
			case "menuSubstitutionMaster" : require_once("mod_substitution/formSubstitutionMaster.js"); break;
			case "menuSkolemizer" : require_once("mod_skolemizer/formSkolemizer.js"); break;
			case "menuPrenex" : require_once("mod_prenexconverter/formPrenex.js"); break;
			//default: echo ("zzzzzz");
		}
	}

	//up css
	if (isset($_POST['css'])){
		switch ($_POST['css']){
			case "menuSettings": print ("mod_settings/settings.css");	break;
			case "menuChecker" : print ("mod_diagnoser/checker.css");  break;
			case "menuResolutionGame": print("mod_resolutiongame/resolutionGame.css"); break;
			case "menuTreeInteraction": print("mod_treeinteraction/treeInteraction.css"); break;
			//case "menuSubstitutionMaster": print("system/css/treeInteraction.css"); break;
			//default: echo ("zzzzzz");
		}
	}

	//exec Logicamente
	if (isset($_POST['action'])){
		switch ($_POST['action']){
			case "generateFormulas": generateFormulas($_POST['nConnective'],$_POST['nAtom']);              break;
			case "setConnectives"  : setConnectives( $_POST['symbol'], $_POST['arity'],$_POST['order']); break;
			case "checkFormula"    : checkFormula($_POST['formula']); break;
			case "readFormula"     : serializeTree($_POST['formula'],$_POST['type']);			   break;
			case "skolemizer"	   : skolemizer($_POST['formula']);			   break;
			case "prenex"	   : prenex($_POST['formula']);			   break;
			//case "Tradutor"	   : tradutor($_POST['formula']);			   break;
			case "getFormula"      : unserializeTree($_POST['formula']);			   break;
			case "delFormula"      : delFormula($_POST['form']);			   break;
   			case "test"            : teste($_POST['form']);		   break;
			case "startGame"         : startGame($_POST["clauseNumber"]); break;
			case "solveAutomatically": solveAutomatically(); break;
			case "solve"			 : solve(); break;
			case "getClauseList"      : getClauseList(); break;
			case "resetGame"    	 : resetGame(); break;
			case "creatTT"         : creatTT( $_POST['formula'] ); break;
			case "getSubTree"         : getSubTree( $_POST['index'] ); break;
			case "getTreeToInteract"  : getTreeToInteract( $_POST['formula'] ); break;
			case "substitution"  : substitution( $_POST['formula'],$_POST['term'],$_POST['variable'] ); break;
			//default: echo ("zzzzzz");
		}
	}


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

	function getSubTree($index){

		$func = Teste_Callback("Node");
		ini_set('unserialize_callback_func', $formula);
		$node = unserialize( $_SESSION[$formula] );

		$raiz = procura ($index, $node);
		$t = new WFFTranslator();
		echo ($t->showFormulaInfix($raiz)."<input type='dubmit' value='Send to Transfer Area'>");
	}


	//interface between Logicamente and HTML

	//unserialize
	function Connective_Callback($classname){
		//require_once("Connective.class.php");
	}

	function generateFormulas($nConnective, $nAtoms){
		ini_set('unserialize_callback_func', 'Connective_Callback');
		$cons = unserialize($_SESSION['con']);
		$logica = new Logicamente();
		$tree = $logica->generateFormulas($nConnective, $nAtoms, $cons);
		$cont = $_POST['cont'];
		//setcookie('f'.$cont, serialize($tree));
		$_SESSION['f'.$cont] = serialize($tree);
		//print_r( $_SESSION['f'.$cont] );
	}

	function setConnectives($symbol, $arity, $order){
		$local = array();
		foreach($symbol as $key => $value){

			//$a = array();
			//array_push($a, htmlentities($value));

			$c = new Connective($value, $arity[$key], $order[$key]);
			//$value = '';
			//print "<br/>".html_entity_decode($value)." ".htmlentities($value)." ".htmlspecialchars($value)."<br/>";

			array_push($local, $c);
		}
		$_SESSION['con'] = serialize($local);

		//print_r($a);
	}

	function checkFormula($formula){
		$logica = new Logicamente();
		$cons = array();

		ini_set('unserialize_callback_func', 'Connective_Callback');

		foreach($_SESSION['con'] as $value)
			array_push($cons, unserialize($value));

		$logica->checkFormula($formula, $cons);
	}

    function serializeTree($formula,$type) {
        $form = str_replace("^","&",$formula); // Gambiarra para leitura dos conectivos pelo o ajax
        $logica = new Logicamente();
        $node = $logica->readFormula($form, unserialize($_SESSION['con']), $type);
        $cont = $_POST['cont'];
        //setcookie('f'.$cont, serialize($node));
		$_SESSION[('f'.$cont)] = serialize($node);
		//print_r( $_SESSION[('f'.$cont)] );
    }

	function Teste_Callback($classname){
		require_once("$classname.class.php");
	}

	function unserializeTree($formula) {
		echo $formula;
		$func = Teste_Callback("Node");
		ini_set('unserialize_callback_func', $func);
		$node = unserialize($_SESSION[$formula]);
		//print_r( $node );
	}
	function delFormula($id){
		unset($_SESSION[$id]);
	}

	function teste($form) {
		if ($form == -1) echo "Selecione uma formula";
		else {
			//print $form;
			//print_r( $_SESSION[$form] );

			$func = Teste_Callback("Node");
			ini_set('unserialize_callback_func', $func);
			$node = unserialize($_SESSION[$form]);

			//print_r( $node );

			$logica = new Logicamente();
			print $logica -> printFormula($node, unserialize($_SESSION['con']));
		}
	}

	function startGame($clauseNumber) {
		$logica = new Logicamente();
		$root = new Node(new Connective("&", "2" ,"400"));
		for ($i = 0; $i < $_POST["clauseNumber"]; $i++) {
			$tmpString = $_POST["clauseInput".$i];
			$tmpTree = $logica->readFormula($tmpString,"");
			$tmpClause = $logica->normalizeDisjunctions($tmpTree);
			array_push($root->children, $tmpClause);
		}
		$game = $logica->startGame($root);
		$game->printListOfClauses();
		$_SESSION["game"] = serialize($game);
	}

	function solveAutomatically() {
		$game = unserialize($_SESSION["game"]);
		if(!$game->isDerivationFinished()) {
			$game->autoSolve();
			$_SESSION["game"] = serialize($game);
		}
	}

	function solve() {
		$game = unserialize($_SESSION["game"]);
		if(!$game->isDerivationFinished()) {
			$resp = $game->solve($game->getClause($_POST["clause0"]), $game->getClause($_POST["clause1"]));
			$_SESSION["game"] = serialize($game);
			if (is_string($resp)) {
				echo $resp;
			}
		}
	}

	function getClauseList() {
		$game = unserialize($_SESSION["game"]);
		$game->printListOfClauses();
	}

	function resetGame() {
		unset($_SESSION["game"]);
	}

	function creatTT( $formula ){
		if ($formula == -1) print "Selecione uma formula";
		else {

			$func = Teste_Callback("Node");
			ini_set('unserialize_callback_func', $formula);
			$node = unserialize( $_SESSION[$formula] );

			$logica = new Logicamente();
			$logica -> getTruthTable( $node );
		//	print_r($node);
		}
	}

	function getTreeToInteract( $formula ){
		if ($formula == -1) print "Selecione uma formula";
		else {

			$func = Teste_Callback("Node");
			ini_set('unserialize_callback_func', $formula);
			$node = unserialize( $_SESSION[$formula] );

			$logica = new Logicamente();
			echo ( "<div>".$logica -> getTreeInteraction( $node )."<div style='clear:both;'></div></div>" );
		}
	}

	function substitution( $formula, $term, $variable ){
		if ($formula == -1) print "Selecione uma formula";
		else {
			$func = Teste_Callback("Node");
			ini_set('unserialize_callback_func', $formula);
			$node = unserialize( $_SESSION[$formula] );

			//$logica = new Logicamente();
			//$logica -> getTruthTable( $node );
			$logica = new Logicamente();
			echo $logica->substitution($node,$term,$variable);
			//print_r($node);
		}
		//echo "{$formula} - {$term} - {$variable} <br/>";
	}

	function skolemizer($formula){
		if ($formula == -1) print "Selecione uma formula";
		else {
			$func = Teste_Callback("Node");
			ini_set('unserialize_callback_func', $formula);
			$node = unserialize( $_SESSION[$formula] );

			$logica = new Logicamente();
			$logica->skolemizer($node);
		}
	}

	function prenex($formula){
		if ($formula == -1) print "Selecione uma formula";
		else {
			$func = Teste_Callback("Node");
			ini_set('unserialize_callback_func', $formula);
			$node = unserialize( $_SESSION[$formula] );

			$logica = new Logicamente();
			$logica->prenex($node);
		}
	}
?>

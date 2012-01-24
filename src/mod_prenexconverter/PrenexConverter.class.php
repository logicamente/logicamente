<?php
	require_once("Formula.class.php");
	require_once("Variable.class.php");
	require_once("Term.class.php");
	require_once("Relation.class.php");
	require_once("Function.class.php");
	require_once("Constant.class.php");
	require_once("Connective.class.php");
	require_once("Atom.class.php");
	require_once("Quantifier.class.php");
	require_once("Signature2.class.php");
	require_once("mod_converter/formulaConverter2.class.php");
/**
 * Classe que transforma uma formula que para forma normal prenex
 *
 * @class CNF
 *
 * @version 0.0
 */
class CNF{

	private $cabeca;	//uma referencia para o noh raiz
	public $estavel = true;

	public function getCabeca()
	{
		return $this->cabeca;
	}

	/**
	* Construtor
	*@param noh contendo o noh cabeca
	*/
	public function CNF($FNP){     //recebe formula em 1a ordem
			$this->cabeca = $FNP;
			$this->cabeca = $this->killBiImplicancias($this->cabeca);

			$estabilizado = false;
			while(!$estabilizado){
				$this->cabeca = $this->estabilizar($FNP);
				$this->estavel = true;//suponha que esteja estavel
				$this->cabeca = $this->testeEstabilidade($FNP);
				$estabilizado = $this->estavel;
			}

			//$this->killExi($this->cabeca);
			//echo "<br /><b>Tree FNP: <br /></b>";
			//$this->imprimir($this->cabeca);
			//$this->imprimir($FNP);
	}

	public function killBiImplicancias(&$root){
		$rootaux = $root->content;
		if($rootaux instanceof Connective){
			if ($rootaux->content == "<->"){
				//transforma em biimplicacao
				$tree01 = new Connective("-->",2,250);
				$node01 = new Node($tree01);
				array_push($node01->children, $root->children[0]);
				array_push($node01->children, $root->children[1]);

				//echo "<br /> quem eh node01? <br />";
				//print_r($node01);

				$tree02 = new Connective("-->",2,250);
				$node02 = new Node($tree01);
				array_push($node02->children, $root->children[1]);
				array_push($node02->children, $root->children[0]);

				//echo "<br /> quem eh node02? <br />";
				//print_r($node02);

				//mexendo com o principal agora
				$root->content = new Connective("&",2,350);//biimplicacao substituida por conjuncao
				$root->children[0] = $node01;//sobrescrevendo o primeiro filho, a ida da biimplicacao
				$root->children[1] = $node02;//sobrescrevendo o segundo filho, a volta da biimplicacao
			}
			else{
				if ($rootaux->content == "-->"){
					$tree01 = new Connective("~",1,400);
					$node01 = new Node($tree01);
					array_push($node01->children, $root->children[0]);

					$root->content = new Connective("|",2,300);//implicacao substituida por disjuncao
					$root->children[0] = $node01;//sobrescrevendo o primeiro filho, a negacao do antecedente
				}
			}
		}
		foreach ($root->children as $term) {
			$this->killBiImplicancias($term);
		}
		return $root;
	}

	public function testeEstabilidade (&$root, &$pai = null) {
		$rootaux = $root->content;
		if($rootaux instanceof Quantifier && !$this->isRoot($root)){//eh um quantificador e naum eh raiz
			if(!$this->estavel($rootaux,$pai)){
				$this->estavel = false;
			}
		}
		foreach ($root->children as $term) {
			$this->testeEstabilidade($term,$root);//$term passa a ser pai e $root passa a ser filho
		}
		return $root;
	}

	/**
	* Metodo para estabilizar a formula
	*@param $root           noh contendo a raiz corrente - passa por referencia
	*/
	public function estabilizar (&$root, &$pai = null) {
		$rootaux = $root->content;
		//echo "<br /> quem eh rootaux? <br />";
		//print_r($rootaux);
		if($rootaux instanceof Quantifier && !$this->isRoot($root)){//eh um quantificador e naum eh raiz
			//$paiaux = $pai->content;
			if (!$this->estavel($rootaux,$pai)){
				$this->tornarEstavel($root, $pai);
			}
			else{
				foreach ($root->children as $term) {
					$this->estabilizar($term,$root);//$term passa a ser pai e $root passa a ser filho
				}
			}
		}
		else{
			foreach ($root->children as $term) {
				$this->estabilizar($term,$root);//$term passa a ser pai e $root passa a ser filho
			}
		}
		return $root;
	}

	public function estavel(&$root, &$pai){
		if ($pai->content instanceof Quantifier){
			return true;
		}
		else{
			return false;
		}
	}

	public function isRoot(&$root){
		if ($root == $this->getCabeca()){
			return true;
		}
		return false;
	}

	public function tornarEstavel(&$root,&$pai){
		$paiaux = $pai->content;
		//echo "<br /> quem eh o pai da crianca? <br />";
		//print_r($pai);
		if ($paiaux->content == '~'){
			//echo "<br /> quem eh root content? <br />";
			//print_r($root->content);
			if ($root->content->content == 'A') {
				$root->content->content = 'E';
			}
			else{//entao eh 'E'
				$root->content->content = 'A';
			}
			$aux = $root->content;
			//echo "<br /> quem eh aux? <br />";
			//print_r($aux);
			//echo "<br /> quem eh o pai content? <br />";
			//print_r($pai->content);
			$root->content = $pai->content;
			//echo "<br /> quem eh o pai agora? <br />";
			//print_r($pai);

			$pai->content = $aux;
		}
		else{
			if($paiaux->content == '&'){
				$pai->content = $root->content;
				$pai->content->bound_variable->content = $this->variableGenerate();
				//echo "<br /> quem eh o pai children 0 222? <br />";
				//print_r($pai->children[0]);
				//echo "<br /> quem eh o root 222? <br />";
				//print_r($root);
				if ($root == $pai->children[0]){
					$secondChildren = $pai->children[1];
					unset($pai->children[1]);
					$root->content = new Connective("&",2,350);
					$root->children[1] = $secondChildren;
				}
				else{
					$firstChildren = $pai->children[0];
					unset($pai->children[0]);
					$root->content = new Connective("&",2,350);
					$root->children[1] = $root->children[0];
					$root->children[0] = $firstChildren;

				}
				//echo "<br /> quem eh o pai novamente 2222? <br />";
				//print_r($pai);
			}
			else{
				if($paiaux->content == '|'){
					$pai->content = $root->content;
					$pai->content->bound_variable->content = $this->variableGenerate();
					//echo "<br /> quem eh o pai children 0 222? <br />";
					//print_r($pai->children[0]);
					//echo "<br /> quem eh o root 222? <br />";
					//print_r($root);
					if ($root == $pai->children[0]){
						$secondChildren = $pai->children[1];
						unset($pai->children[1]);
						$root->content = new Connective("|",2,300);
						$root->children[1] = $secondChildren;
					}
					else{
						$firstChildren = $pai->children[0];
						unset($pai->children[0]);
						$root->content = new Connective("|",2,300);
						$root->children[1] = $root->children[0];
						$root->children[0] = $firstChildren;

					}
					//echo "<br /> quem eh o pai novamente 2222? <br />";
					//print_r($pai);
				}
			}
		}
	}

	public function variableGenerate(){
		$s = new Signature($this->getCabeca());//pegue a assinatura da formula
		$varDef = false; // variavel usada para substituir nao esta definida
		$variable = "x";
		$x = 0;
		//print_r($s);
		while (!$varDef){//enquanto a variavel usada para substituir nao esta definida
			$var = $variable.$x; //pegue uma variable
			//echo $var;
			$jaTemNaAssinatura = false; // suponha que nao tem na assinatura
			foreach ($s->signature as $term) {// para cada elemento do vetor de assinatura "$s" teste se a variable já existe na assinatura
				//echo "<br />";
				//print_r($term->content);
				//echo "<br />";
				if ($term->content == $var) // se já existir, entao "já tem na assinatura"
					$jaTemNaAssinatura = true;
			}
			if (!$jaTemNaAssinatura) // se nao tem na assinatura entao a variable será a "$var" corrente no loop
				$varDef = true;
			$x = $x + 1;
		}
		return $var;
	}

	//para imprimir em formato de arvore
	public function imprimir($formula){
		$tester = new formulaConverter("T","");
		$tester->printTree($formula,"");
		echo $tester->printFormula($formula,"");
	}

}

?>

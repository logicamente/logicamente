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
 * Classe que Skolemiza uma formula que se encontra na forma FNP
 *
 * @class Skolemizer
 *
 * @version 0.3
 */
class Skolemizer{

	private $cabeca;										    //uma referencia para o noh raiz

	public function getCabeca()
	{
		return $this->cabeca;
	}

	/**
	* Construtor
	*@param $FNP noh contendo o noh cabeca
	*/
	public function Skolemizer($FNP){     //recebe formula na forma normal prenex
			$this->cabeca = $FNP;
			$this->cabeca = $this->substituir($FNP);
			$this->killExi($this->cabeca);
			//echo "<br /><b>Tree skolemizada: <br /></b>";
			//$this->imprimir($this->cabeca);
	}

	/**
	* Metodo para substituir na arvore variaveis por funcoes
	*@param $root           noh contendo os objetos - passa por referencia
	*@param $listaVar_A  array contendo as variaveis ligadas aos quantificadores universais                          (como se fosse um conjunto de contents dos para_todos)
	*/
	public function substituir (&$root, $listaVar_A = array()) {
		$rootaux = $root->content;
		if($rootaux instanceof Quantifier){
			if ($rootaux->content == 'E'){              							//se quantificador for um 'E'
				if (sizeof($listaVar_A) == 0){
					$s = new Signature($root);//pegue a assinatura da formula
					$constDef = false; // constante usada para substituir nao esta definida
					$constante = "a";
					$x = 0;
					//print_r($s);
					while (!$constDef){//enquanto a constante usada para substituir nao esta definida
						$const = $constante.$x; //pegue uma constante
						//echo $const;
						$jaTemNaAssinatura = false; // suponha que nao tem na assinatura
						foreach ($s->signature as $term) {// para cada elemento do vetor de assinatura "$a" teste se a constante já existe na assinatura
							//echo "<br />";
							//print_r($term->content);
							//echo "<br />";
							if ($term->content == $const) // se já existir, entao "já tem na assinatura"
								$jaTemNaAssinatura = true;
						}
						if (!$jaTemNaAssinatura) // se nao tem na assinatura entao a constante será a "$const" corrente no loop
							$constDef = true;
						$x = $x + 1;
					}
					//$const = array_pop($this->constantes);
					$this->putConst($root,$rootaux->bound_variable->value,$const);
				}else{
					$s = new Signature($root);//pegue a assinatura da formula
					$funcDef = false; // funcao usada para substituir nao esta definida
					//print_r($s);
					$funcao = "f";
					$x = 0;
					while (!$funcDef){//enquanto a funcao usada para substituir nao esta definida
						$f = $funcao.$x; //pegue uma funcao
						//echo $f;
						$jaTemNaAssinatura = false; // suponha que nao tem na assinatura
						foreach ($s->signature as $term) {// para cada elemento do vetor de assinatura "$a" teste se a funcao já existe na assinatura
							//echo "<br />";
							//print_r($term->content);
							//echo "<br />";
							if ($term->content == $f) // se já existir, entao "já tem na assinatura"
								$jaTemNaAssinatura = true;
						}
						if (!$jaTemNaAssinatura) // se nao tem na assinatura entao a funcao será a "$f" corrente no loop
							$funcDef = true;
						$x = $x + 1;
					}
					//$f = array_pop($this->func);
					$this->putFunc($root,$rootaux->bound_variable->value,$listaVar_A,$f);
				}
			}
			else{// Senao, eh um quantificador universal
				$listaVar_A[] = $rootaux->bound_variable->content;
			}
		}
		foreach ($root->children as $term) {
			$this->substituir($term, $listaVar_A);
		}
		return $root;
	}

	/**
	* Metodo para colocar funcoes em lugar de variaveis quantificadas existencialmente e que possuem quantificadores antes
	*@param $root             noh contendo os objetos - passa por referencia
	*@param $value           valor da variavel a ser substituida
	*@param $listaVar_A  array contendo as variaveis ligadas aos quantificadores universais
	*@param $f                  nome da funcao que colocarei
	*/
	function putFunc(&$root,$value,$listaVar_A,$f){
		$rootaux = $root->content;
		if(($rootaux instanceof Variable) && ($rootaux->value == $value)){
			$rootaux = new Func($f);
			$rootaux->arity = sizeof($listaVar_A);							//aridade da funcao sendo armazenada
			$root->content = $rootaux;
			for ($i=0 ; $i < sizeof($listaVar_A) ; $i++){
				$root->children[$i] = new Node(new Variable($listaVar_A[$i]));
				$root->children[$i]->content->isLinked = 1;
			}
		}
		foreach ($root->children as $term) {
			$this->putFunc($term, $value, $listaVar_A,$f);
		}
		return $root;
	}

	/**
	* Metodo para colocar constantes em lugar de variaveis quantificadas existencialmente e que NAO possuem quantificadores antes
	*@param $root             noh contendo os objetos - passa por referencia
	*@param $value           valor da variavel a ser substituida
	*@param $const           nome da constante que eu colocarei
	*/
	function putConst(&$root,$value,$const){
		$rootaux = $root->content;
		if(($rootaux instanceof Variable) && ($rootaux->value == $value)){
			$root->content = new Constant($const);
		}
		foreach ($root->children as $term) {
			$this->putConst($term, $value, $const);
		}
		return $root;
	}

	//para imprimir em formato de arvore
	public function imprimir($formula){
		$tester = new formulaConverter("T","");
		$tester->printTree($formula,"");
	}

	public function killExi(&$root){
		$rootaux = $root->content;
		if($rootaux instanceof Quantifier){//se eh um quantificador, confinue percorrendo a arvore, pois ela se encontra na forma FNP
			if ($rootaux->content == 'E'){//se quantificador for um 'E', mate-o
				$root = $root->children[0];//matando e exixtencial
				$this->killExi($root);
			}
			else{// senao eh um A e mande procurar outro quantificador
				$this->killExi($root->children[0]);
			}
		}
	}
}

?>

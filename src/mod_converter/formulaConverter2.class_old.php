<?php

require_once("Atom.class.php");
require_once("Node.class.php");
require_once("Connective.class.php");
require_once("Term.class.php");
require_once("Function.class.php");
require_once("Variable.class.php");
require_once("Relation.class.php");
require_once("Constant.class.php");


class formulaConverter {
	/* Vetor onde são guardados os conectivos */
	private $connectivesArray;
	/* String dos quantificadores */
	private $quantifiers;
	/* Vetor onde são guardados os conectivos que não possuem associatividade*/
	private $noAssociativity;	
	/* String para top e bottom*/
	private $especialRelations;
	
	/**
	 * Construtor.
	 *
	 * @param String $type Letra inicial pindicando o formato da expressão.
	 * @return formulaConverter
	 */
	public function formulaConverter($type,$array) {
		if ($type == "T" | $type == "t") $this->TXTformulaConverter($array);
		elseif ($type == "P" | $type == "p") $this->polformulaConverter();
		elseif ($type == "F" | $type == "f") $this->funcformulaConverter();
	}

	/* Módulo formulaConverter em formato TXT e
	Também deve ser usado paraos módulos ambiguityParser e parenthesesSaver. */
	public function TXTformulaConverter($ar) {
	if ($ar == "") {
		$this -> connectivesArray = Array();
		array_push ($this -> connectivesArray,new Connective("-->",2,250));
		array_push ($this -> connectivesArray,new Connective("<->",2,250));
		array_push ($this -> connectivesArray,new Connective("&",2,350));
		array_push ($this -> connectivesArray,new Connective("|",2,300));
		array_push ($this -> connectivesArray,new Connective("+",2,250));
		array_push ($this -> connectivesArray,new Connective("~",1,400));
		$this->quantifiers = "^((A|E|E!)";
		$this->noAssociativity["-->"] = false;
		$this->noAssociativity["+"] = false;
		$this->especialRelations = "^(1|0)";
		} else {
			$this -> connectivesArray = $ar;
			$this->quantifiers = "^((A|E|E!)";
			$this->noAssociativity["-->"] = false;
			$this->noAssociativity["+"] = false;
			$this->especialRelations = "^(1|0)";
		}
	}
	
	/* Módulo formulaConverter em formato polonês. */
	public function polformulaConverter() {
		$this -> connectivesArray = Array();
		array_push ($this -> connectivesArray,new Connective("C",2,250));
		array_push ($this -> connectivesArray,new Connective("E",2,250));
		array_push ($this -> connectivesArray,new Connective("K",2,350));
		array_push ($this -> connectivesArray,new Connective("A",2,300));
		array_push ($this -> connectivesArray,new Connective("X",2,250));
		array_push ($this -> connectivesArray,new Connective("N",1,400));
		$this->quantifiers = "^(([[][P|S|S1][]])";
		$this->noAssociativity["C"] = false;
		$this->noAssociativity["X"] = false;
		$this->especialRelations = "^(I|O)";
		
	}
	
	/* Módulo formulaConverter em formato funcional. */
	public function funcformulaConverter() {
		$this -> connectivesArray = Array();
		array_push ($this -> connectivesArray,new Connective("imp",2,250));
		array_push ($this -> connectivesArray,new Connective("eq",2,250));
		array_push ($this -> connectivesArray,new Connective("and",2,350));
		array_push ($this -> connectivesArray,new Connective("or",2,300));
		array_push ($this -> connectivesArray,new Connective("xor",2,250));
		array_push ($this -> connectivesArray,new Connective("neg",1,400));
		$this->quantifiers = "^((forall|exists|onlyOne)";
		$this->noAssociativity["imp"] = false;
		$this->noAssociativity["xor"] = false;
		$this->especialRelations = "^(top|bottom)";
	}
	
	/**
	 * Método para adicionar quantificadores, um por vez.
	 *
	 * @param String $string Quantificador no formato string.
	 */
	public function addQuantifier ($string) {
		$newQuantifiers = $this->quantifiers;
		/*Teste para saber se está trabalhando com expressões funcionais ou não */
		if ($this->quantifiers[3] == "[") {
			$end = "][]])";
			$newQuantifiers = substr($quantifiers,0,strlen($quantifiers) - 6);
			$newQuantifiers .= "|" . $string . $end;
		} else {
			$newQuantifiers = substr($quantifiers,0,strlen($quantifiers) - 2);
			$newQuantifiers .= "|" . $string . ")";
		}
		$this->quantifiers = $newQuantifiers;
	}

	/**
	 * Método para adicionar conectivos, um por vez.
	 *
	 * @param String $connective O conectivo no formato em string.
	 * @param int $arity A aridade do conevctivo
	 * @param int $order A precedência do conectivo
	 * @param boolean $associativity Indica se o conectivo possui associatividade.
	 * @param boolean $associativityLeft Indica se a associatividade e a esquerda.
	 */
	public function addConnectives ($connective,$arity,$order,$associativity,$associativityLeft ) {
		$this -> connectivesArray[$connective] = new Connective ($connective,$arity,$order);
		/*Testa se caso não for associativo insere no vetor de não associativos com a sua respectiva associatividade*/
		if (!$associativity) $this->noAssociativity[$connective] = $associativityLeft;
	}

	/**
	 * Método para pegar o operador e seus operandos.
	 * Recebe uma expressão prefixa parentetizada.
	 *
	 * @param String $expression
	 * @return array com duas posições:
	 * ['connective'] Guarda o operador, geralmente um conectivo.
	 * ['children'] Guarda os operandos.
	 */
	private static function connectiveChildren ($expression) {
		/*Testa se não há '(' na expressão*/
		if (stristr($expression,'(') == false) return Array('connective' => $expression , 'children' =>  Array());
		$index = 0;
		/* Laço para ler a string até um '(' ou até o fim dela. */
		while($index < strlen($expression) - 1 && $expression[$index] != '(' ) {
			$index++;
		}
		$connective = substr($expression,0,$index);
		$rest = substr($expression,$index+ 1, strlen($expression) - $index - 2);
		$param = Array();
		$rest .= ',';
		$index = 0;
		$index2 = 0;
		$count = 0;
		/* Laço para pegar os argumentos do conectivo e colocar em um array*/
		while($index != strlen($rest)) {
			/*Testa se o caractere é um '(' e incrementa o contador se sim*/
			if($rest[$index] == '(') $count++;
			/*Testa se o caractere é um ')' e decrementa o contador se sim*/
			if($rest[$index] == ')') $count--;
			/*Testa se o contador é igual a 0*/
			if ($count == 0){
				/*Testa se encontrou uma virgula e adiciona no array a string até ela caso encontre*/
				if($rest[$index] == ',') {
					array_push($param, substr($rest,$index2, $index - $index2));
					$index2 = $index + 1;
				}
			}
			$index++;
		}
		$array = Array('connective' => $connective , 'children' =>  $param);
		return $array;
	}


	/**
	 * Método para montar a árvore.
	 * Para quantificadores, conectivos e relações.
	 *
	 * @param String $expression Expressão em formato prefixo parentetizada.
	 * @param array(array) $arrayLinked Vetor de variáveis ligada (inicialmente vazio).
	 * @param array $arrayUnlinked Vetor de variáveis desligada (contém todas as variáveis da fórmula).
	 * @param int $count Contador de quantificadores na fórmula.
	 * @param int $count1 Contador de quantificadores em uma sub-árvore.
	 * @param array(array) $arrayQuantifier Quantificadores relacionados da árvore.
	 * @return node o nó raiz da árvore pronta.
	 */
	private function  makeTree ($expression, $arrayLinked, $arrayUnlinked, $count, $count1, $arrayQuantifier){
		$array = $this->connectiveChildren($expression);
		$op = $array['connective'];
		/*Testa se op é um quantificador*/
		if ($this->isQuantifier($op)) {
			$node = new Node(new Connective($op,1,0));
			$index = 0;
			/*Testa se está usando quantificador funcional ou não*/
			if ($this->quantifiers[3] == "f") {
				$index = 2;
				/*Laço que busca o inicio de uma variavel*/
				while (!$this->isVariable(substr($op,$index))) {
					$index++;
				}
			} else 
			/*Laço que busca o inicio de uma variavel*/	
				while (!$this->isVariable(substr($op,$index))) {
					$index++;
				}
			$var = substr($op,$index);
			$count1++;
			array_push($arrayQuantifier[$count],$count1);
			$arrayLinked[$count1] = Array($var => new Variable($var));
			$child = $array['children'][0];
			array_push($node->children, $this->makeTree($child,$arrayLinked, $arrayUnlinked, $count, $count1,$arrayQuantifier));
			$count++;
			return $node;
		/*Testa se op é um conectivo*/	
		} elseif ($this->isConnective($op)) {
			$arity = 0;
			$order = $this->connectiveOrder($op);
			$connective = new Connective($op,0,$order);
			$node = new Node($connective);
			/*Laço que cjama a recursão para cada argumento*/
			foreach ($array['children'] as $child) {
				$arity++;
				$nodes = $this->makeTree($child,$arrayLinked, $arrayUnlinked, $count, $count1,$arrayQuantifier);
				array_push($node->children, $nodes);
			}
			$connective->arity = $arity;
			$node->content = $connective;
			return $node;
		/*Testa se op é uma relação*/	
		} elseif($this->isRelation($op)) {
			$relation = new Relation($op);
			$node = new Node($relation);
			$arity = 0;
			/*Testa se não é uma relação zero-ária*/
			if ($array['children'][0] != "") {
				/*Laço que chama a função de tratamento de termos para cada argumento*/
				foreach ($array['children'] as $child){
					$arity++;
					$nodes = $this->makeTree2($child,$arrayLinked, $arrayUnlinked, $count, $count1,$arrayQuantifier);
					array_push($node->children, $nodes);
				}
			}
			$relation->arity = $arity;
			$this->arrays[0] = $arrayLinked;
			$this->arrays[1] = $arrayQuantifier;
			$node->content = $relation;
			return $node;
		}
	}
	
	/**
	 * Método para juntar o quantificador a sua variavel no formato funcional.
	 *
	 * @param String $exp Expressão a ser ajustada.
	 * @return String Expressão devidamente ajustada.
	 */
	public function functionalToPrefix ($expression) {
		$array = Array();
		$array = $this -> connectiveChildren($expression);
		$connective = $array['connective'];
		/*Testa se não é um conectivo nem um quantificador funcional*/
		if (!$this -> isQuantifierFunctional($connective) && !$this -> isConnective($connective)) return $expression;
		$expressionFinal = "";
		/*Testa se é um quantificador funcional*/
		if ($this -> isQuantifierFunctional($connective)) {
			$expressionFinal = $connective . $array['children'][0];
			$expression = $connective . $array['children'][0] . "(" . $array['children'][1] . ")";
		} else $expressionFinal = $connective;
		$array = Array();
		$array = $this -> connectiveChildren($expression);
		$tmp = "";
		/* Laço para tratar recursivamente todos os argumentos do conectivo*/
		foreach ($array['children'] as $term){
			/*Testa se a string tmp não é vazia*/
			if ($tmp != "") $tmp .= ",";
			$tmp = $tmp . ($this -> functionalToPrefix($term));
		}
		return $expressionFinal . "(" . $tmp . ")";
	}

	/**
	 * Método para montar a árvore.
	 * Para funções, constantes, variáveis.
	 *
	 * @param String $expression Expressão em formato prefixo parentetizada.
	 * @param array(array) $arrayLinked Vetor de variáveis ligada (inicialmente vazio).
	 * @param array $arrayUnlinked Vetor de variáveis desligada (contém todas as variáveis da fórmula).
	 * @param int $count Contador de quantificadores na fórmula.
	 * @param int $count1 Contador de quantificadores em uma sub-árvore.
	 * @param array(array) $arrayQuantifier Quantificadores relacionados da árvore.
	 * @return node o nó raiz da árvore pronta.
	 */
	private function  makeTree2 ($expression, $arrayLinked, $arrayUnlinked, $count, $count1, $arrayQuantifier){
		$array = $this->connectiveChildren($expression);
		$op = $array['connective'];
		/*Testa se há argumentos e é uma função*/
		if (sizeof($array['children']) != 0 && $this->isFunction($op)) {
			$function = new Func($op);
			$node = new Node($function);
			$arity = 0;
			/* Laço para tratar recursivamente todos os argumentos do conectivo*/
			foreach ($array['children'] as $child){
				$arity++;		
				$nodes = $this->makeTree2($child,$arrayLinked, $arrayUnlinked, $count, $count1,$arrayQuantifier);
				array_push($node->children, $nodes);
			}
			$function->arity = $arity;
			$node->content = $function;
			return $node;
		/*Testa se é uma variável*/	
		} elseif ($this->isVariable($op)){
			$f = true;
			/* Laço para tratar se uma variável é ligada ou não*/
			foreach ($arrayQuantifier[$count] as $q) {
				/*Testa se é uma variável ligada*/
				if (isset($arrayLinked[$q][$op])) {
					$var = $arrayLinked[$q][$op];
					$f = false;
				}
			}
			/*Testa se é uma variável desligada*/
			if($f) $var = $arrayUnlinked[$op];
			$node = new Node($var);
			return $node;
		/*Testa se é uma constante*/	
		} elseif ($this-> isConstant($op)) {
			$var = $arrayUnlinked[$op];
			//array_push($tmp->children,$var);
			$node = new Node($var);
			return $node;
		} else {
			echo "Erro: '$op' não é um tipo conhecido! <br/>";
		}
	}
	
	/**
	 * Método para verificar se um expressão inicia com uma variável.
	 *
	 * @param String $term Expressão a ser testada.
	 * @return boolean
	 */
	private function isVariable($term){
		return (ereg("^(([u-z]|[U-Z])([a-z]|[A-Z])*[0-9]*)",$term));
	}
	
	/**
	 * Método para verificar se um expressão inicia com uma constante.
	 *
	 * @param String $term Expressão a ser testada.
	 * @return boolean
	 */
	private function isConstant($term){
		return (ereg("^((([a-n]|[A-N])([a-z]|[A-Z])*[0-9]*)|([0-9]+))",$term));

	}
	
	/**
	 * Método para verificar se um expressão inicia com uma função.
	 *
	 * @param String $term Expressão a ser testada.
	 * @return boolean
	 */
    private function isFunction($term){
		return (ereg("^(([a-h]|[A-H])([a-z]|[A-Z])*[0-9]*)",$term));
	}
	
	/**
	 * Método para verificar se um expressão inicia com uma relação.
	 *
	 * @param String $term Expressão a ser testada.
	 * @return boolean
	 */
	private function isRelation($term){
		return (ereg("^(([a-z]|[A-Z])+[0-9]*)|".$this->especialRelations,$term));
	}

	/**
	 * Método para verificar se um expressão inicia com um quantificador.
	 *
	 * @param String $term Expressão a ser testada.
	 * @return boolean
	 */
	private function isQuantifier($term) {
		$quantifiers = $this->quantifiers;
		return (ereg(($quantifiers."([u-z]|[U-Z])([a-z]|[A-Z])*[0-9]*)"),$term));
	}
	
	
	/**
	 * Método para verificar se um expressão inicia com um quantificador.
	 * usado para o formato funcional.
	 *
	 * @param String $term Expressão a ser testada.
	 * @return boolean
	 */
	private function isQuantifierFunctional($term) {
		$quantifiers = $this->quantifiers;
		return (ereg($quantifiers.")",$term));
	}

	/**
	 * Método para verificar se um expressão começa com um conectivo.
	 *
	 * @param String $term Expressão a ser testada.
	 * @return boolean
	 */
    private function isConnective($term){
		$flag = false;
		$indexEnd = 0;
		/*Laço para verificar se no inicio da string tem um conectivo*/
		while ($indexEnd < strlen($term)) {
			$indexEnd++;
			/*Laço que percorre o array de conectivos*/
			foreach ($this->connectivesArray as $t) {
				/*Testa se o conectivo foi encontrado*/
				if ($t->content == substr($term,0,$indexEnd)) $flag = true;
			}
		}
		return $flag;
	}
   
	/**
	 * Método para retornar a precedência de um conectivo.
	 *
	 * @param String $term Um conectivo.
	 * @return int Ordem de precedência do conectivo.
	 */
	private function connectiveOrder($term){
		$order = 0;
		$tmp = $this -> connectivesArray;
		/*Laço para procuarar a precedencia de um conectivo*/
		while($t = array_pop($tmp)){
			/*Testa se t é nulo*/
			if($t == null) break;
			/*Testa se encontrou o conectivo*/
			if ($t->content == $term) $order = $t->order;
		}
		return $order;
	}

	/**
	 * Método para retornar a aridade de um conectivo
	 *
	 * @param String $term Um conectivo.
	 * @return int Aridade do conectivo.
	 */
	private function connectiveArity($term){
		$arity = 0;
		$connectivesArray = $this -> connectivesArray;
		/*Testa se a expressão começa com um quantificador*/
		if ($this->isQuantifier($term)) return ($arity + 1);
		/*Laço procurar a aridade de um conectivo*/
		while($t = array_pop($connectivesArray)){
			/*Testa se t é nulo*/
			if($t == null) break;
			/*Testa se encontrou o conectivo*/
			if ($t->content == $term) $arity = $t->arity;
		}
		return $arity;
	}

	/**
	 * Método para montar o vetor de variáveis desligadas.
	 *
	 * @param String $expression Expressão prefixa parentetizada.
	 * @return array O vetor de variáveis desligadas.
	 */
	private function makeArrayUnlinked($expression) {
		$tmp = ($this->connectiveChildren($expression));
		$array = $tmp['children'];
		$arrayFinal = Array();
		/*Laço para pegar todas as variáveis e constantes existentes na expressão
		 e colocá-las em um array*/
		while ($tmp = array_pop($array)) {
			/*Testa se tmp é nulo*/
			if ($tmp == null) break;
			$tmp2 = $this->connectiveChildren($tmp);
			/*Testa se não há argumentos*/
			if (sizeof($tmp2['children']) == 0) {
				/*Testa se é uma variável*/
				if ($this->isVariable($tmp)){
					$t = new Variable($tmp);
					$t->value = 1;
					$arrayFinal[$tmp] = $t;
				/*Testa se é uma constante*/
				} elseif ($this->isConstant($tmp) && !$this->isQuantifier($tmp)) {
					$arrayFinal[$tmp] = new Constant($tmp);
				} 
			} else {
				$tmp = $this-> connectiveChildren($tmp);
				$tmp = $tmp['children'];
				/*Testa se o primeiro argumento de tmp não é nulo*/
				if ($tmp[0] != null) {
					/*Laço que coloca todos argumentos no array*/
					foreach ($tmp as $t){
						array_push($array,$t);
					}
				} else $tmp = 1;
			}
		}
	return $arrayFinal;
	}
	
	/**
	 * Método para retornar se um conectivo possui ou não associatividade.
	 *
	 * @param String $connective
	 * @return boolean
	 */
	private function haveAssociativity ($connective) {
		/*Testa s eo conectivo está no vetor de conectivos não associativos*/
		if (isset($this->noAssociativity[$connective])) return false;
		else return true;
	}

	/**
	 * Método para testar se o primeiro conectivo tem precedência maior que o segundo.
	 *
	 * @param String $exp1 Conectivo 1 a ser analizado.
	 * @param String $exp2 Conectivo 2 a ser analizado.
	 * @return boolean
	 */
	private function priorityOrder($exp1,$exp2){
		/*Testa se exp2 é um quantificador ou exp1 um '(' ou exp1 nulo*/
		if ($this->isQuantifier($exp2)|| $exp1 == '(' || $exp1 == null) return false;
		/*Testa se exp1 é um quantificador*/
		elseif ($this->isQuantifier($exp1)) return true;
		else return ($this->connectiveOrder($exp1) >= $this->connectiveOrder($exp2));
	}

	/**
	 * Método para montar a árvore de uma expressão Infixa (TXT).
	 *
	 * @param String $expression Expressão em formato TXT.
	 * @return node O nó raiz da a árvore montada.
	 */
	public function infixToTree($expression,$type) {
		/*Testa se existe a mesma quantidade de '(' e ')'*/
		if ($this->testParentheses($expression)) {
			$arrayExpression = array();
			if ($type) {
				$arrayExpression = $this->firstToArray($expression,$arrayExpression);
			} else {
				$arrayExpression = $this->propToArray($expression,$arrayExpression);
			}
			$expression = $this->toPrefix($arrayExpression, false);
			$arrayExpression = Array();
			if ($type) {
				$arrayExpression = $this->firstToArray($expression,$arrayExpression);
			} else {
				$arrayExpression = $this->propToArray($expression,$arrayExpression);
			}
			$arrayExpression = $this->putParentheses($arrayExpression);
			$expression = $arrayExpression[0];
			if ($type) {
				$arrayLinked = Array(Array());
				$arrayUnlinked = $this->makeArrayUnlinked($expression);
				$arrayQuantifier = Array(array());
				return $this->makeTree($expression,$arrayLinked,$arrayUnlinked,0,0,$arrayQuantifier);
			} else {
				return $this->propMakeTree($expression);
			}
		} else return "";
	}

	/**
	 * Método para montar a árvore de uma expresão Prefixa (Polonesa).
	 *
	 * @param String $expression Expressão em formato polonês.
	 * @return node O nó raiz da a árvore montada.
	 */
	public function prefixToTree($expression,$type) {
		/*Testa se existe a mesma quantidade de '(' e ')'*/
		if ($this->testParentheses($expression)) {
			$arrayExpression = array();
			$arrayExpression = $this->prefixToArray($expression,$arrayExpression);
			$expression = $this->toPrefix($arrayExpression, true);
			$arrayExpression = Array();
			$arrayExpression = $this->prefixToArray($expression,$arrayExpression);
			$arrayExpression = $this->putParentheses($arrayExpression);
			$expression = $arrayExpression[0];
			if ($type) {
				$arrayLinked = Array(Array());
				$arrayUnlinked = $this->makeArrayUnlinked($expression);
				$arrayQuantifier = Array(array());
				return $this->makeTree($expression,$arrayLinked,$arrayUnlinked,0,0,$arrayQuantifier);
			} else {
				return $this->propMakeTree($expression);
			}
			
		} else return "";
	}
	
	/**
	 * Método para montar a árvore de uma expressão funcional.
	 *
	 * @param String $expression Expressão em formato funcional
	 * @return node O nó raiz da a árvore montada.
	 */
	public function functionalToTree($expression,$type) {
		/*Testa se existe a mesma quantidade de '(' e ')'*/
		if ($this->testParentheses($expression)) {
			$index = 0;
			/*Laço para remover todos os espaços existentes na expressão*/
			while (!ctype_graph($expression)) {
				/*Laço que busca um espaço*/
				while ($expression[$index] != " ") {
					$index++;
				}
				/*Testa se index é igual a 0*/
				if ($index == 0) $expression = substr($expression,1);
				else $expression = substr($expression,0,$index) . substr($expression,$index+1);
			}
			if ($type) {
				$expression = $this -> functionalToPrefix($expression);
				$arrayLinked = Array(Array());
				$arrayUnlinked = $this->makeArrayUnlinked($expression);
				$arrayQuantifier = Array(array());
				return $this->makeTree($expression,$arrayLinked,$arrayUnlinked,0,0,$arrayQuantifier); 
			} else {
				return $this->propMakeTree($expression);
			}
		} else return "";
	}

	/**
	 * Método para que transforma um vetor em uma expressão prefixa.
	 * 
	 * @param array $arrayExpression Vetor da expressão original.
	 * @param boolean $type Indica que tipo de expressão está recebendo.
	 * 	true para expressão prefixa não virgulada e false para expressão infixa
	 * @return String Expressão prefixa virgulada
	 */
	private function toPrefix($arrayExpression, $type) {
		$index = 0;
		$tmp = "";
		$top = "";
		$final = "";
		$pilha = array();
		/*Laço para tratar cada termo do array*/
		while ($index < sizeof($arrayExpression)){
			$tmp = $arrayExpression[$index];
			/*Testa se tmp é um '('*/
			if ($tmp == '(') {
				array_push($pilha,$tmp);
			/*Testa se tmp é um ')'*/
			} elseif($tmp == ')') {
				/*Laço para tratamento de parenteses, desempilha tudo da pilha até um '('*/
				while ($top = array_pop($pilha)){
					/*Testa se top é um '('*/
					if ($top == '(') break;
					/*Testa se top é um quantificador*/
					if ($this->isQuantifier($top)) $final = " " . $final;
					$final = $top . $final;
				}
			/*Testa se tmp não é conectivo nem quantificador*/	
			} elseif (!$this->isConnective($tmp) && !$this->isQuantifier($tmp)) {
				/*Testa se type é true, a expressão é prefixa*/
				if ($type) {
					/*Testa se final é vazia, se o topo da pilha é uma negação ou final começa com ')'*/
					if ($final == "" || $final[0] == ')') $final .= $tmp;
					else $final .= ',' .  $tmp;
				} else {
					/*Testa se final é vazia, se o topo da pilha é uma negação ou final começa com ')'*/
					if ($final == "" || $final[0] == ')') $final = $tmp.$final;
					else $final = $tmp .',' . $final;
				}
			} else {
				$top = array_pop($pilha);
				/*Laço para encontrar na pilha um conectivo de precedencia menor que tmp*/
				while ($this->priorityOrder($top,$tmp)) {
					/*Testa se top é nulo*/
					if ($top == null) break;
					/*Testa se tmp não possui associatividade, não é a esquerda e é igual a top*/
					if (!$this->haveAssociativity($tmp) && !$this->noAssociativity[$tmp] && $top == $tmp) {
						array_push($pilha,$top);
						break;
					}
					/*Testa se tmp é diferente da negação e não é vazia*/
					if ($tmp != '~' && $final != "") {
						/*Testa se top é um quantificador ou type é true, a expressão é prefixa*/
						if ($this->isQuantifier($top) || $type) $final = " " . $final;
						$final = $top . $final;
						$top = array_pop($pilha);
					} else {
						$final = $final;
						array_push($pilha,$top);
						$top = null;
					}
				}
				/*Testa se a precedencia de tmp é maior que a de top e top não é nulo */
				if (!$this->priorityOrder($top,$tmp) && $top != null ) array_push($pilha,$top);
				array_push($pilha,$tmp);
			}
		$index++;
		}
		/*Laço para desempilhar todos os conectivos ainda existentes na pilha*/
		while (true) {
			$top = array_pop($pilha);
			/*Testa se top e nulo*/
			if ($top == null) break;
			/*Testa se top é diferente de '('*/
			if ($top != '(') {
				/*Testa se top é um quantificador ou type é true, a expressão é prefixa*/
				if ($this->isQuantifier($top) || $type) $final = " " . $final; 
				$final = $top . $final;
			}
		}
		return $final;
	}
	
	/**
	 * Método que monta um vetor para uma expressão de 1ª ordem.
	 * Cada campo do vetor será um quantificador, um conectivo, uma relação ou um parêntese.
	 *
	 * @param String $expression Expressão de 1ª ordem.
	 * @param array $arrayExpression Vetor da expressão.
	 * @return array Vetor da expressão.
	 */
	private function firstToArray($expression,$arrayExpression) {
		$indexEnd = 0;
		$countParentheses = 0;
		/*Testa se a expressão não é vazia*/
		if ($expression != "") {
			$expression = $this->removeSpaces($expression);
			$indexEnd = 0;
			/*Testa se a expressão começa com um quantificador*/
			if ($this->isQuantifier($expression)) {
				/* Laço para encontrar um " " ou um '('*/
				while($expression[$indexEnd] != " ") {
					/*Testa se o caractere é um '('*/
					if ($expression[$indexEnd] == '(') break;
					$indexEnd++;
				}
				array_push($arrayExpression, substr($expression,0,$indexEnd));
				/*Testa se o caractere é um " "*/
				if ($expression[$indexEnd] == " ") $indexEnd++;
				$expression = substr($expression,$indexEnd);
				$arrayExpression = $this->firstToArray($expression, $arrayExpression);
			/*Testa se a expressão começa com uma relação*/	
			} elseif ($this->isRelation($expression)) {
				/*Laço para encontrar um '('*/
				while($expression[$indexEnd] != '(') {
					$indexEnd++;
				}
				$indexEnd++;
				$countParentheses++;
				/*Laço para pegar a string do '(' até o seu ')' correspondente */
				while ($countParentheses != 0) {
					/*Testa se o caractere é um ')'*/
					if ($expression[$indexEnd] == ')') {
						$countParentheses--;
					/*Testa se o caractere é um '('*/	
					} elseif ($expression[$indexEnd] == '(') {
						$countParentheses++;
					}
					$indexEnd++;
				}
				array_push($arrayExpression, substr($expression,0,$indexEnd));
				$expression = substr($expression,$indexEnd);
				$arrayExpression = $this->firstToArray($expression, $arrayExpression);
			/*Testa se o caractere é um '('*/	
			} elseif ($expression[$indexEnd] == '(') {
				array_push($arrayExpression, $expression[$indexEnd]);
				$countParentheses++;
				$indexEnd++;
				/*Laço para pegar a string do '(' até o seu ')' correspondente e aplicar a recursão nela*/
				while ($countParentheses != 0) {
					/*Testa se o caractere é um ')'*/
					if ($expression[$indexEnd] == ')') {
						$countParentheses--;
					/*Testa se o caractere é um '('*/	
					} elseif ($expression[$indexEnd] == '(') {
						$countParentheses++;
					}
					$indexEnd++;
				}
				$arrayExpression = $this->firstToArray(substr($expression,1,$indexEnd-2), $arrayExpression);
				array_push($arrayExpression,$expression[$indexEnd-1]);
				$expression = substr($expression,$indexEnd);
				$arrayExpression = $this->firstToArray($expression, $arrayExpression);
			/*Testa se o caractere é um ','*/	
			} elseif ($expression[0] == ',') {
				array_push($arrayExpression,$expression[0]);
				$expression = substr($expression,1);
				$arrayExpression = $this->firstToArray($expression, $arrayExpression);
				$indexEnd++;
			} else {
				/*Laço para pegar o conectivo do início da string */
				while(true) {
					/*Testa se o caractere é um '(' ou se o caractere é um '~'*/
					if ($expression[$indexEnd] == '(' || $expression[$indexEnd] == '~') break;
					/*Testa se a expressão a partir daquele caractere é uma relação*/
					if ($this->isRelation(substr($expression,$indexEnd))) break;
					/*Testa se a expressão a partir daquele caractere é um conectivo*/
					if ($this->isConnective(substr($expression,0,$indexEnd))) break;
					$indexEnd++;
				}
				array_push($arrayExpression, substr($expression,0,$indexEnd));
				/*Testa se o caractere é um '~'*/
				if ($expression[$indexEnd] == '~') {
					array_push($arrayExpression,$expression[$indexEnd]);
					$indexEnd++;
				}
				$expression = substr($expression,$indexEnd);
				$arrayExpression = $this->firstToArray($expression, $arrayExpression);
			}
		}
		/*Laço para remover parenteses em apenas uma relação. Ex.: (R(x))*/
		while ($indexEnd < sizeof($arrayExpression)) {
			/*Testa se há parenteses em volta de uma relação*/
			if ( $indexEnd < (sizeof($arrayExpression) - 2) && $arrayExpression[$indexEnd] == '(' && $arrayExpression[$indexEnd+2] == ')') {
				array_splice($arrayExpression,$indexEnd,1);
				array_splice($arrayExpression,$indexEnd+1,1);
			}
			$indexEnd++;
		}
		return $arrayExpression;
	}

	/**
	 * Método para testar se para cada um "(" exite um ")" correspondente.
	 *
	 * @param String $expression
	 * @return boolean
	 */
	private function testParentheses($expression) {
		$index = 0;
		$countParentheses = 0;
		/*Laço para testar se existe a mesma quantidade de '(' e ')'*/
		while($index < strlen($expression)) {
			/*Testa se o caractere é um ')'*/
			if ($expression[$index] == ')') {
				$countParentheses--;
			/*Testa se o caractere é um '('*/
			} elseif ($expression[$index] == '(') {
				$countParentheses++;
			}
			$index++;
		}
		return ($countParentheses == 0);
	}

	/**
	 * Método para reduzir ao máximo o número de parênteses, sem
	 * modificar o valor da expressão.
	 *
	 * @param String $expression Expressão de a ser simplificada.
	 * @param boolean $type Indica se a expressão é de 1ª ordem ou proposicional.
	 * 	true para 1ª ordem e false para proposicional.
	 * @return String Expressão simplificada.
	 */
	public function parenthesesSaver ($expression, $type) {
		$correctExpression = "";
		$indexIni = 0;
		$indexEnd = 0;
		$countParentheses = 0;
		$arrayParentheses = array();
		$testArray = array();
		$arrayExpression = array();
		$testExpression = "";
		/*Testa se há a mesma quantidade de '(' e ')'*/
		if ($this->testParentheses($expression)) {
			/*Testa se type é true, a expressão é de 1ª ordem*/
			if ($type) {
				$arrayExpression = $this->firstToArray($expression,$arrayExpression);
			} else {
				$arrayExpression = $this->propToArray($expression,$arrayExpression);
			}
			$correctExpression = $this->toPrefix($arrayExpression, false);
			/*Testa se type é true, a expressão é de 1ª ordem*/
			if ($type) {
				$testArray = ($this->putParentheses($this->firstToArray($correctExpression, $testArray)));
			} else {
				$testArray = ($this->putParentheses($this->propToArray($correctExpression, $testArray)));
			}
			$correctExpression = $testArray[0];
			$testArray = array();
			/*Laço para percorrer o array em busca de '('*/
			while ($indexEnd < sizeof($arrayExpression)) {
				/*Testa se a string é um '('*/
				if ($arrayExpression[$indexEnd] == '(') {
					$countParentheses++;
					$indexEnd;
					$indexIni = $indexEnd;
					/*Laço para encontrar o respectivo ')' para o '(' encontrado */
					while ($countParentheses != 0) {
						$indexEnd++;
						/*Testa se a string é um ')'*/
						if ($arrayExpression[$indexEnd] == ')') {
							$countParentheses--;
						/*Testa se a string é um '('*/	
						} elseif ($arrayExpression[$indexEnd] == '(') {
							$countParentheses++;
						}
						/*Testa se o contador é diferente de 0*/
						if ($countParentheses != 0)  $testExpression = $testExpression . $arrayExpression[$indexEnd];
						/*Testa se a string é um quantificador*/
						if ($this->isQuantifier($arrayExpression[$indexEnd])) $testExpression .= " ";
					}
					$testExpression = $this->parenthesesSaver($testExpression,$type);
					/*Testa se type é true, a expressão é de 1ª ordem*/
					if ($type) {
						$arrayParentheses = $this->firstToArray($testExpression,$arrayParentheses);
					} else {
						$arrayParentheses = $this->propToArray($testExpression,$arrayParentheses);
					}
					array_splice($arrayExpression, $indexIni, ($indexEnd - $indexIni + 1 ), $arrayParentheses);
					$tmp = $arrayExpression;
					$testExpression = "";
					/*Laço para transformar o array tmp em uma string*/
					while ($top = array_pop($tmp)) {
						/*Testa se top é nulo*/
						if ($top == null) break;
						/*Testa se top é um quantificador*/
						if ($this->isQuantifier($top)) $testExpression = " " . $testExpression;
						$testExpression = $top . $testExpression;
					}
					/*Testa se type é true, a expressão é de 1ª ordem*/
					if ($type) {
						$testExpression = $this->toPrefix($this->firstToArray($testExpression, $testArray),false);
						$testArray = ($this->putParentheses($this->firstToArray($testExpression, $testArray)));
					} else {
						$testExpression = $this->toPrefix($this->propToArray($testExpression, $testArray),false);
						$testArray = ($this->putParentheses($this->propToArray($testExpression, $testArray)));
					}
					$testExpression = $testArray[0];
					$testArray = array();
					/*Testa se as expressões são iguais*/
					if ($this->expressionComparator($correctExpression,$testExpression)) {
						$correctExpression = $testExpression;
						$testExpression = "";
						$arrayParentheses = Array();
						$indexEnd = 0;
					} else {
						$indexEnd = 0;
						$testExpression = ")";
						/*Laço para transformar o array arrayParentheses em uma string*/
						while ($top = array_pop($arrayParentheses)) {
							/*Testa se top é nulo*/
							if ($top == null) break;
							/*Testa se top é um quantificador*/
							if ($this->isQuantifier($top)) $top .= " ";
							$testExpression = $top . $testExpression;
							$indexEnd++;
						}
						$testExpression = "(" . $testExpression;
						array_push($arrayParentheses, $testExpression);
						array_splice($arrayExpression, $indexIni, $indexEnd, $arrayParentheses);
						$indexEnd = 0;
						$testExpression = "";
						$arrayParentheses = Array();
					}
					$testArray = array();
				} else {
					$indexEnd++;
				}
			}
		}
		$correctExpression = "";
		/*Laço para transformar o array arrayExpression em uma string*/
		while ($top = array_pop($arrayExpression)) {
			/*Testa se top é nulo*/
			if ($top == null) break;
			/*Testa se top é um quantificador*/
			if ($this->isQuantifier($top)) $top .= " ";
			$correctExpression = $top . $correctExpression;
		}
		return $correctExpression;
	}

	/**
	 * Método para verificar a igualdade de duas expressões na forma prefixa parentetizada.
	 *
	 * @param String $expression1 Expressão 1 a ser comparada.
	 * @param String $expression2 Expressão 2 a ser comparada.
	 * @return boolean
	 */
	public function expressionComparator ($expression1,$expression2) {
		/*Testa se as strings são iguais */
		if ($expression1 == $expression2) return true;
		else {
			$result = true;
			$arrayExpression1 = $this->connectiveChildren($expression1);
			$arrayExpression2 = $this->connectiveChildren($expression2);
			/*Testa se os conectivos são diferentes*/
			if ($arrayExpression1['connective'] != $arrayExpression2['connective']) return false;
			else {
				$index1 = 0;
				/*Laço para unificar argumentos de conectivos iguais*/
				while ($index1 < sizeof($arrayExpression1['children'])) {
					$term1 = $arrayExpression1['children'][$index1];
					$array1 = $this->connectiveChildren($term1);
					/*Testa se os conectivos são iguais*/
					if ($array1['connective'] == $arrayExpression1['connective']) {
						/*Testa se o conectivo não possui associatividade*/
						if (!$this->haveAssociativity($arrayExpression1['connective'])) break;
						array_splice($arrayExpression1['children'],$index1,1);
						/*Laço para colocar no arrayExpression os argumentos do seu argumento de mesmo conectivo*/
						foreach ($array1['children'] as $t1) {
							array_push($arrayExpression1['children'], $t1);
						}
						$index1 = 0;
					} else $index1++;
				}
				$index2 = 0;
				/*Laço para unificar argumentos de conectivos iguais*/
				while ($index2 < (sizeof($arrayExpression2['children']))) {
					$term2 = $arrayExpression2['children'][$index2];
					$array2 = $this->connectiveChildren($term2);
					/*Testa se os conectivos são iguais*/
					if ($array2['connective'] == $arrayExpression2['connective']) {
						/*Testa se o conectivo não possui associatividade*/
						if (!$this->haveAssociativity($arrayExpression2['connective'])) break;
						array_splice($arrayExpression2['children'],$index2,1);
						/*Laço para colocar no arrayExpression os argumentos do seu argumento de mesmo conectivo*/
						foreach ($array2['children'] as $t2) {
							array_push($arrayExpression2['children'], $t2);
						}
						$index2 = 0;
					} else $index2++;
					
				}
				$index2 = 0;
				/*Laço para comparar se duas expressões são iguais, comparando os argumentos 
				independentemente da posição */
				while ($index2 < (sizeof($arrayExpression2['children']))) {
					$index1 = array_search($arrayExpression2['children'][$index2],$arrayExpression1['children']);
					/*Testa se um argumento de uma expressão ocorre nos argumentos da outra*/
					if ($index1 === false) {
					/*Laço que chama a recursão para cada argumento coso sejam diferentes*/	
					foreach ($arrayExpression1['children'] as $term1) {
							$result = $result && $this->expressionComparator($arrayExpression2['children'][$index2],$term1);
						}
					} else $result = $result && true;
					$index2++;
				}
			}
			return $result;
		}
	}

	/**
	 * Método para colocar parênteses em um vetor de expressão prefixa virgulada.
	 *
	 * @param  $array Vetor de expressão prefixa virgulada.
	 * @return Um vetor com a expressão parentetizada.
	 */
	private function putParentheses ($array) {
		/*Testa se o array só possui um elemento*/
		if (sizeof($array) == 1) return $array;
		$indexUlt = 0;
		$index = -1;
		$newTerm = "";
		$arity = 0;
		/*Laço para buscar o último conectivo no array*/
		while ($index < (sizeof($array) - 1)) {
			$index++;
			/*Testa se o elemento é um conectivo ou quantificador e não é o último elemento do array 
			e não possui parenteses*/
			if (($this->isConnective($array[$index]) ||	$this->isQuantifier($array[$index]))
				&& $index != (sizeof($array) - 1) && $this->noParentheses($array[$index]))
			$indexUlt = $index;
		}
		$arity = $this->connectiveArity($array[$indexUlt]);
		$index = $indexUlt;
		$newTerm .= $array[$indexUlt] . '(';
		/*Laço para colocar a quantidade argumentos, de acordo com a aridade do conectivo,
		na string newTerm*/
		while ($arity > 0) {
			$index++;
			/*Testa se é o último elemento do array ou se o próximo elemento é uma virgula*/
			if ($index == (sizeof($array) - 1 ) || $array[$index+1] == ',') $arity--;
			$newTerm .= $array[$index];
		}
		$newTerm .= ')';
		$arrayParentheses = array ();
		array_push($arrayParentheses,$newTerm);
		/*Testa se o índice do último conectivo é 0*/
		if ($indexUlt == 0) $array = $arrayParentheses;
		else array_splice($array,$indexUlt,($index+1-$indexUlt),$arrayParentheses);
		$array = $this->putParentheses($array);
		return $array;
	}

	/**
	 * Método para verificar se a expressão não contém parênteses.
	 *
	 * @param String $string Expressão a ser testada.
	 * @return boolean
	 */
	private function noParentheses ($string) {
		$flag = true;
		$index = 0;
		/*Laço para verificar se há parenteses na expressão*/
		while ($flag) {
			/*Testa se é o índice já ultrapassou a string*/
			if ($index == strlen($string)) break;
			/*Testa se o caractere e um '('*/
			if ($string[$index] == '(') $flag = false;
			$index++;
		}
		return $flag;
	}

	/**
	 * Método para transformar uma expressção prefixa parentetizada em uma expressão infixa. 
	 *
	 * @param String $expressionPrefix Expressão prefixa parentetizada.
	 * @return String Expressão infixa.
	 */
	private function toInfix($expressionPrefix) {
		/*Testa se a expressão começa com um quantificador ou não é começa com uma relação e o primeiro caractere diferente de '(' */
		if ($this->isQuantifier($expressionPrefix) || (!$this->isRelation($expressionPrefix) && $expressionPrefix[0] != "(")) {
			$term = "";
			$arrayExpresion = $this->connectiveChildren($expressionPrefix);
			$index = sizeof($arrayExpresion['children']) - 1;
			$connective = $arrayExpresion['connective'];
			$expressionInfix = $arrayExpresion['children'][$index];
			/*Testa se a expressão começa com um quantificador*/
			if ($this->isQuantifier($expressionInfix)) 
				$expressionInfix = ($this->toInfix($expressionInfix));
			/*Testa se a expressão nao começa com uma relação*/
			elseif (!$this->isRelation($expressionInfix))
				$expressionInfix = "(" . ($this->toInfix($expressionInfix));
			/*Testa se o conectivo tem aridade 1*/	
			if (sizeof($arrayExpresion['children']) == 1)
				$expressionInfix = "(" . $connective . $expressionInfix;
			/*Laço para chamar recursão para cada argumento e acrescentá-lo na string*/
			while ($index > 0) {
				$index--;
				$term = $arrayExpresion['children'][$index];
				/*Testa se term não começa com uma relação*/
				if (!$this->isRelation($term))
					$term = "(" . $this->toInfix($term) . ")";
				/*Testa se é um conectivo e a aridade é diferente de 1*/	
				if ($this->isConnective($connective) && sizeof($arrayExpresion['children']) != 1)
					$expressionInfix .= $connective;
				 $expressionInfix .= $term;
			}
			/*Laço para enquanto houver '(' sem um ')' coloque um ')'*/
			while (!$this->testParentheses($expressionInfix)) {
				$expressionInfix .= ")";
			}
			return $this->toInfix($expressionInfix);
		} else return $expressionPrefix;
	}

	/**
	 * Método para retornar todas as formas possíveis de parentetização,
	 * destacando a correta.
	 *
	 * @param String $incorrectExpression Expressão a ser avaliada.
	 * @param boolean $type Indica se a expressão é de 1ª ordem ou proposicional.
	 * 	true para 1ª ordem e false para proposicional.
	 * @return array Vetor contendo todas as formas possíveis de parentetização.
	 * 	['correct'] Contém a expressão correta.
	 */
	public function ambiguityParser ($incorrectExpression, $type) {
		$arrayExpression = array();
		/*Testa se type é true, uma expressão de 1ªordem*/
		if ($type) {
			$arrayExpression = $this->firstToArray($incorrectExpression,$arrayExpression);
		} else {
			$arrayExpression = $this->propToArray($incorrectExpression,$arrayExpression);
		}
		$expression = $this->toPrefix($arrayExpression,false);
		$arrayGeneral = $this ->removeParentheses($arrayExpression);
		$arrayExpression = array();
		/*Testa se type é true, uma expressão de 1ªordem*/
		if ($type) {
			$arrayExpression = $this->firstToArray($expression,$arrayExpression);
		} else {
			$arrayExpression = $this->propToArray($expression,$arrayExpression);
		}
		$expression = $this->putParentheses($arrayExpression);
		$expression = $expression[0];
		$arrayExpression = array();
		$arrayExpression['correct'] = $this->toInfix($expression);
		$arrayExpression = $this->arrayParentheses($arrayGeneral,$arrayExpression);
		return $arrayExpression;
	}
	
	/**
	 * Método para transformar um vetor em uma string
	 *
	 * @param array $array Vetor contendo uma expressão
	 * @return String Expressão correspondente.
	 */
	private function toString ($array) {
		$string = "";
		/*Laço par transformar um array em uma string*/
		foreach ($array as $term) {
			$string .= $term;
		}
		return $string;
	}
	
	/**
	 * Método para retornar o número de operandos e operadores unitários. 
	 *
	 * @param array $array Vetor contendo a expressão
	 * @return int número de operandos e operadores unitários.
	 */
	private function numberTerms ($array) {
		$number = 0;
		$index = 0;
		/*Laço para contar quantos termos há no vetor, seja um termo uma relação ou conectivo unário*/
		while ($index < sizeof($array)) {
			/*Testa se não é um conectivo ou é um conectivo de aridade 1*/
			if ((!$this->isConnective($array[$index]) || $this->connectiveArity($array[$index]) == 1)
				&& $array[$index][0] != "(")
				$number++;
			$index++;
		}
		return $number;
	}
	
	/**
	 * Método para remover parênteses de um vetor contendo uma expressão.
	 *
	 * @param array $arrayExpression Vetor contendo uma expressão.
	 * @return array Vetor contendo a expressão sem parênteses.
	 */
	private function removeParentheses ($arrayExpression) {
		$index = 0;
		/*Laço para remover todos os parenteses existentes no array*/
		while ($index < sizeof($arrayExpression)) {
			/*Testa se é um parentese*/
			if ($arrayExpression[$index] == "(" || $arrayExpression[$index] == ")")
				array_splice($arrayExpression,$index,1);
			$index++;
		}
		return $arrayExpression;
	}
	
	/**
	 * Método para retornar todas as formas possíveis de parentetização.
	 *
	 * @param array $incorrectExpression Vetor contendo todas as expressão sem parênteses.
	 * @param array $arrayTotal Vetor contendo todas as expressões possíveis.
	 * @return array Vetor contendo todas as expressões possíveis.
	 */
	private function arrayParentheses ($incorrectExpression,$arrayTotal) {
		$indexIni = 0;
		$indexEnd = 0;
		$expression = $this->toString($incorrectExpression);
		/* As linhas abaixo podem ser descomentadas para visualizar as espressões
		durante a execução do programa */
		/*$number = sizeof($arrayTotal) - 1;
		if (!isset($arrayTotal[$expression]))
			echo ("[$number] => ".$expression."<br/>");*/
		$arrayTotal[$expression] = $expression;
		/*Testa se o array só possui 1 elemento*/
		if (sizeof($incorrectExpression) == 1) return $arrayTotal;
		$arrayAux = array();
		$arrayExpression = $incorrectExpression;
		$numberTerms = $this->numberTerms($incorrectExpression);
		/*Laço que continua enquanto houver termos*/
		while ($numberTerms > 0) {
			/*Laço que percorre todos os elementos do array*/
			while($indexEnd < sizeof($arrayExpression)) {
				$string = "(" . $arrayExpression[$indexIni];
				$term = $arrayExpression[$indexEnd];
				$index = $indexIni+1;
				/*Laço que coloca todos elementos do array até índice indicado*/
				while ($index <= $indexEnd) {
					$term = $arrayExpression[$index];
					$string .= $term;
					$index++;
				}
				$term = $arrayExpression[$indexEnd];
				/*Laço que coloca todos elementos do array do índice indicado até o final*/
				while ($indexEnd < (sizeof($arrayExpression) - 1)) {
					/*Testa se term é uma relação e não um quantificador*/
					if ($this->isRelation($term) && !$this->isQuantifier($term)) break;
					$indexEnd++;
					$term = $arrayExpression[$indexEnd];
					$string .= $term;
					/*Testa se o primeiro caractere do term é um '(' e term é diferente da relação inicial*/
					if ($term[0] == "(" && $term != $arrayExpression[$indexIni]) break;
				}
				$string2 = $string . ")";
				array_push($arrayAux, $string2);
				array_splice($arrayExpression,$indexIni, ($indexEnd+1-$indexIni) ,$arrayAux); 
				$arrayTotal = $this->arrayParentheses($arrayExpression,$arrayTotal);
				$arrayExpression = $incorrectExpression;
				$indexEnd++;
				$arrayAux = array();
			}
			$string = "";
			$indexIni++;
			/*Testa se o índice é menor que o array*/
			if ($indexIni < (sizeof($arrayExpression) - 1)) {
				$term = $arrayExpression[$indexIni];
				/*Laço que busca uma relação, um quantificador ou conectivo de aridade 1 até o fim do array */
				while (!$this->isRelation($term) && $indexIni < (sizeof($arrayExpression) - 1)) {
					/*Testa se term é um quantificador*/
					if	($this->isQuantifier($term)) break;
					/*Testa se term é um conectivo de aridade 1*/
					if ($this->connectiveArity($term) == 1) break;
					$indexIni++;
					$term = $arrayExpression[$indexIni];
				}
			}
			$indexEnd = $indexIni;
			$numberTerms--;
		}
		return $arrayTotal;
	}
	
	/**
	 * Método para montar um vetor de uma expressão proposicional
	 *
	 * @param String $expression Expressão proposicional.
	 * @param array $arrayExpression Vetor da expressão.
	 * @return array Vetor da expressão.
	 */
	public function propToArray ($expression,$arrayExpression) {
		/*Testa se a string não é vazia*/
		if ($expression != "") {
			$index = 0;
			/*Laço para remover todos os espaços*/
			while (!ctype_graph($expression)) {
				/*Laço que busca um espaço*/
				while ($expression[$index] != " ") {
					$index++;
				}
				/*Testa se index é igual a 0*/
				if ($index == 0) $expression = substr($expression,1);
				else $expression = substr($expression,0,$index) . substr($expression,$index+1);
			}
			$index = 0;
			/*Testa se a expressão começa com uma relação*/
			if ($this->isRelation($expression)) {
				/*Laço para buscar uma virgula, um ')' ou um conectivo */
				while (!$this->isConnective(substr($expression,$index))) {
					/*Testa se index é o último caractere*/
					if ($index == (strlen($expression))) break;
					/*Testa se o caractere é uma ','*/
					if ($expression[$index] == ",") break;
					/*Testa se o caractere é uma ')'*/
					if ($expression[$index] == ")") break;
					$index++;
				}
				/*Testa se index é igual a 0*/
				if ($index == 0) $index++;
				array_push($arrayExpression,substr($expression,0,$index));
				$arrayExpression = $this->propToArray(substr($expression,$index), $arrayExpression);
			} elseif ($this->isConnective(substr($expression,$index))) {
				/*Laço para buscar uma virgula, um ')', um conectivo ou um relação*/
				while (!$this->isRelation(substr($expression,$index))) {
					/*Testa se index é igual a '('*/
					if ($expression[$index] == "(") break;
					/*Testa se index é o último caractere*/
					if ($index == (strlen($expression) - 1)) break;
					/*Testa se index é igual a ','*/
					if ($expression[$index] == ",") break;
					$index++;
					/*Testa se a partir de index é um conectivo*/
					if ($this->isConnective(substr($expression,$index))) break;
				}
				/*Testa se index é igual a 0*/
				if ($index == 0) $index++;
				array_push($arrayExpression,substr($expression,0,$index));
				$arrayExpression = $this->propToArray(substr($expression,$index), $arrayExpression);
			}else {
				$index++;
				array_push($arrayExpression,substr($expression,0,$index));
				$arrayExpression = $this->propToArray(substr($expression,$index), $arrayExpression);
			}
		}
		return $arrayExpression;
	}
	
	/**
	 * Método para imprimir o Ambiguity Parser em html.
	 *
	 * @param array $array Array com todas as expressões possíveis.
	 */
	public function printArrayAmbiguityParser ($array) {
		echo "<b>Ambiguity Parser expression:<br /></b>";
		$index = 0;
		$correct = $array['correct'];
		echo "<i>Correct expression:</i><br /><dd>$correct<br />";
		echo "<i>Other expressions:<br /></i>";
		/*Laço para imprimir todos os elementos do array*/
		foreach ($array as $expression) {
			/*Testa se a expressção é diferente da correta*/
			if ($expression != $correct) {
				echo "<DD>[$index] => $expression<br />";
				$index++;
			}
		}
	}
	
	/**
	 * Método para imprimir uma árvore em html.
	 *
	 * @param Node $node Árvore a ser impressa.
	 * @param String $ident Identador, inicialmente deve ser vazio.
	 */
	public function printTree ($tree,$ident) {
		echo "<DL>";
		$ident .= "<DD>";
		/*Testa se é um conectivo*/
		$node = $tree->content;
		if ($node instanceof Connective) {
			echo $ident."<i><b>Connective Object</b></i><br />";
			echo $ident."[<i>content</i>] => $node->content<br />";
			echo $ident."[<i>arity</i>] => $node->arity<br />";
			echo $ident."[<i>order</i>] => $node->order<br />";
			echo $ident."[<i>value</i>] => $node->value<br />";
			echo $ident."[<i>children</i>] => ";
			/*Laço que chama a recursão para cada argumento*/
			foreach ($tree->children as $term) {
				$this->printTree($term,$ident);
			}
		/*Testa se é uma relação*/	
		} elseif ($node instanceof Relation) {
			echo $ident."<i><b>Relation Object</b></i><br />";
			echo $ident."[<i>content</i>] => $node->content<br />";
			echo $ident."[<i>arity</i>] => $node->arity<br />";
			echo $ident."[<i>value</i>] => $node->value<br />";
			echo $ident."[<i>children</i>] => ";
			/*Laço que chama a recursão para cada argumento*/
			foreach ($tree->children as $term) {
				$this->printTree($term,$ident);
			}
		/*Testa se é uma função*/	
		} elseif ($node instanceof Func) {
			echo $ident."<i><b>Function Object</b></i><br />";
			echo $ident."[<i>content</i>] => $node->content<br />";
			echo $ident."[<i>arity</i>] => $node->arity<br />";
			echo $ident."[<i>value</i>] => $node->value<br />";
			echo $ident."[<i>children</i>] => ";
			/*Laço que chama a recursão para cada argumento*/
			foreach ($tree->children as $term) {
				$this->printTree($term,$ident);
			}
		/*Testa se é uma variável*/	
		} elseif ($node instanceof Variable) {
			echo $ident."<i><b>Variable Object</b></i><br />";
			echo $ident."[<i>content</i>] => $node->content<br />";
			echo $ident."[<i>value</i>] => $node->value<br />";
		/*Testa se é uma constante*/	
		} elseif ($node instanceof Constant) {
			echo $ident."<i><b>Constant Object</b></i><br />";
			echo $ident."[<i>content</i>] => $node->content<br />";
			echo $ident."[<i>value</i>] => $node->value<br />";
		} elseif ($node instanceof Atom) {
			echo $ident."<i><b>Atom Object</b></i><br />";
			echo $ident."[<i>content</i>] => $node->content<br />";
			echo $ident."[<i>value</i>] => $node->value<br />";
		}
		echo "</DL>";
	}
	
	/**
	 * Método para remover todos os espaços desnecessários uma expressão.
	 *
	 * @param String $expression Expressão a ser desparentetizada.
	 * @return String Expressão já desparentetizada.
	 */
	private function removeSpaces ($expression) {
		$indexEnd = 0;
		$testExpression = "";
		/*Laço que continua até não houver mais espaços*/
		while (!ctype_graph($expression)) {
			/*Laço que busca um espaço na expressão*/
			while ($expression[$indexEnd] != " ") {
				/*Testa se a partir de indexEnd é um quantificador*/
				if($this->isQuantifier(substr($expression,$indexEnd))) {
					/*Laço que busca um espaço na expressão e mantém caso vier após um quantificador*/
					while ($expression[$indexEnd] != " ") {
						$indexEnd++;
					}
					$indexEnd++;
					$testExpression = substr($expression,0,$indexEnd);
					$testExpression .= $this->removeSpaces(substr($expression,$indexEnd));
					return $testExpression;
				}
				$indexEnd++;
			}
			/*Testa se indexEnd é igual a 0*/
			if ($indexEnd == 0) $expression = substr($expression,1);
			else $expression = substr($expression,0,$indexEnd) . substr($expression,$indexEnd+1);		
		}
		return $expression;
	}

	/**
	 * Método para formar um vetor a partir de uma expressão prefixa.
	 *
	 * @param String $expression Expressão a ser transformada.
	 * @param array $arrayExpression Array onde ficará a expressão
	 * @return array O Array terminado.
	 */
	private function prefixToArray ($expression, $arrayExpression) {
		$index = 0;
		/*Laço para percorrer a expressão em busca de um espaço ou virgula, 
		acrescentar no array e chamar recursivamente*/
		while ($index < strlen($expression)) {
			/*Testa se o caractere é um espaço e index é igual a 0*/
			if ($expression[$index] == " " && $index == 0) {
				$arrayExpression = $this -> prefixToArray(substr($expression,1), $arrayExpression);
				return $arrayExpression;
			/*Testa se o caractere é um espaço ou se o caractere é uma virgula*/
			} elseif ($expression[$index] == " " || $expression[$index] == ",") {
				array_push($arrayExpression, substr($expression,0,$index));
				/*Testa se o caractere é um virgula*/
				if ($expression[$index] == ",") array_push($arrayExpression, $expression[$index]);
				$arrayExpression = $this -> prefixToArray(substr($expression,$index+1), $arrayExpression);
				return $arrayExpression;
			}
			$index++;
		}
		array_push($arrayExpression,$expression);
		return $arrayExpression;
	}
	
	/**
	 * Método para montar a árvore.
	 * Conectivos e átomos.
	 *
	 * @return node o nó raiz da árvore pronta.
	 */
	private function propMakeTree($expression) {
		$array = $this->connectiveChildren($expression);
		$connective = $array['connective'];
		/*Testa se op é um conectivo*/	
		if ($this->isConnective($connective)) {
			$arity = 0;
			$order = $this->connectiveOrder($connective);
			$tree = new Connective($connective,0,$order);
			$node = new Node($tree);
			/*Laço que chama a recursão para cada argumento*/
			/*foreach ($array['children'] as $child) {
				$arity++;
				$children = $this->propMakeTree($child);
				array_push($node->children, $children);
			}*/
			for ($i = sizeof($array['children']) - 1; $i >= 0; $i--) {
				$arity++;
				$child = $array['children'][$i];
				$children = $this->propMakeTree($child);
				array_push($node->children, $children);
			}
			$tree->arity = $arity;
			$node->content = $tree;
			return $node;
		/*Caso contrário é um átomo*/	
		} else {
			$tree = new Node (new Atom($connective));
			return $tree;
		}
	}
	
	
	
}

?>

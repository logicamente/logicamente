<?php

//require_once("testeCNFConverter.php");

/**
 * Classe que busca e casa padrões em uma determinada arvore
 */
class PatternMatcher {
	/**
	 * Array que guarda os padrões encontrados
	 */
	private $rootArray = array();
	
	/**
	 * Array que guarda a versão da regra utilizada.
	 */
	private $patternTypeArray = array();
	
	/**
	 * Encontra na árvore todas as subárvores que possuem o padrão desejádo.
	 * @param Node $tree Árvore em que se procura o padrão
	 * @param Pattern $pattern Padrãp procurado
	 */
	public function matchPattern(Node $tree, Pattern $pattern) {
		/*$t = new WFFTranslator();
		echo "[tree]:".$t->showFormulaInfix($tree)."<br />";
		echo "[pattern]:".$t->showFormulaInfix($pattern)."<br />";*/
		//Se o padrão está sendo bem sucedido em suas comparações...
		if ( $pattern->getPatternRoot()->isSubstitutionOk() ) {
			//Se o conteúdo da árvore e do padrão são iguais...
			if ( $tree->content == $pattern->content ) {
				//Se são iguais ao 'top' ou ao 'bottom'
				if ( $pattern->content->content == "0" || $pattern->content->content == "1" ) {
					//Se final de padrão...
					if ($pattern->getEndOfPattern()) {
						// Se o padrao ainda não se encontra na arvore...
						//if (!in_array($pattern->getTreeRoot(), $this->rootArray)) {
							//Empilha o padrão encontrado no rootArray
							array_push( $this->rootArray, $pattern->getTreeRoot() );
							//Empilha a versão do padrão encontrado no pattertypeArray
							array_push( $this->patternTypeArray, $pattern->getPatternType() );
						//}
					}
					//Se ainda não for um átomo na árvore
					if (!$tree->isAtom()) {
						//Reseta o padrão e continua a busca
						$this->matchPattern($tree, PatternCreator::CreatePattern($pattern->getPatternType(), $tree));
					}
				}
				else {
					//Caminha na árvore e no padrão
					for ($i = 0; $i < count($tree->children); $i++) {
						$this->matchPattern($tree->children[$i], $pattern->children[$i]);
					}
				}
			}
			else {
				//Se o nó do padrão é um átomo (entao ele é uma variável)...
				if ( $pattern->isAtom() ) {
					if ( ($pattern->content->content == "0" || $pattern->content->content == "1") ) {
						//O padrão, da maneira que está, não funcionará mais para comparações
						$pattern->getPatternRoot()->setSubstitutionOk(false);
						//Reseta o padrão e continua a busca
						$this->matchPattern($tree, PatternCreator::CreatePattern($pattern->getPatternType(), $tree));
					}
					else {
						$list = $pattern->getPatternRoot()->getVariableList();
						//Se a variável em questão não foi substituída ainda...
						if (!array_key_exists($pattern->content->content, $list)) {
							//Adiciona a substituição para a lista de variáveis
							$list[$pattern->content->content] = $tree;
							$pattern->getPatternRoot()->setVariableList($list);
							//Se final de padrão...
							if ($pattern->getEndOfPattern()) {
								// Se o padrao ainda não se encontra na arvore...
								//if (!in_array($pattern->getTreeRoot(), $this->rootArray)) {
									//Empilha o padrão encontrado no rootArray
									array_push( $this->rootArray, $pattern->getTreeRoot() );
									//Empilha a versão do padrão encontrado no pattertypeArray
									array_push( $this->patternTypeArray, $pattern->getPatternType() );
								//}
							}
							//Se ainda não for um átomo na árvore
							if (!$tree->isAtom()) {
								//Reseta o padrão e continua a busca
								$this->matchPattern($tree, PatternCreator::CreatePattern($pattern->getPatternType(), $tree));
							}
						}
						//Se a variável já foi substituída...
						else {
							//Se a árvore não for a mesma que esta no array...
							if ($list[$pattern->content->content] != $tree) {
								//Reseta o padrão e continua a busca
								$this->matchPattern($tree, PatternCreator::CreatePattern($pattern->getPatternType(), $tree));
								//O padrão, da maneira que está, não funcionará mais para comparações
								$pattern->getPatternRoot()->setSubstitutionOk(false);
							}
							//Se a árvore for a mesma que está no array
							else {
								//Se final de padrão...
								if ($pattern->getEndOfPattern()) {
									// Se o padrao ainda não se encontra na arvore...
									//if (!in_array($pattern->getTreeRoot(), $this->rootArray)) {
										//Empilha o padrão encontrado no rootArray
										array_push( $this->rootArray, $pattern->getTreeRoot() );
										//Empilha a versão do padrão encontrado no pattertypeArray
										array_push( $this->patternTypeArray, $pattern->getPatternType() );
									//}
								}
								//Se ainda não for um átomo na árvore
								if (!$tree->isAtom()) {
									//Reseta o padrão e continua a busca
									$this->matchPattern($tree, PatternCreator::CreatePattern($pattern->getPatternType(), $tree));
								}
							}
							//Se ainda não for um átomo na árvore
							if (!$tree->isAtom()) {
								//Reseta o padrão e continua a busca
								$this->matchPattern($tree, PatternCreator::CreatePattern($pattern->getPatternType(), $tree));
							}
						}
					}
				}
				//Se o nó do padrão não e um átomo (entao ele e um conectivo diferente do que está na mesma posição da árvore)
				else {
					//O padrão, da maneira que está, não funcionará mais para comparações
					$pattern->getPatternRoot()->setSubstitutionOk(false);
					//Se padrão já foi testado para o determinado nó...
					if ($pattern->isPatternTested()) {
						//Continua caminhando na árvore e no padrão
						for ($i = 0; $i < count($tree->children); $i++) {
							$this->matchPattern($tree->children[$i], PatternCreator::CreatePattern($pattern->getPatternType(), $tree->children[$i]));
						}
					//Se padrão não foi testado para o determinado no...
					} else {
						$pattern = PatternCreator::CreatePattern($pattern->getPatternType(), $tree);
						$pattern->setPatternTested(true);
						$this->matchPattern($tree, $pattern);
					}
				}
			}
		}
		//O padrão não está sendo bem sucedido em suas comparações...
		else {
			//Reseta o padrão e continua a busca
			$this->matchPattern($tree, PatternCreator::CreatePattern($pattern->getPatternType(), $tree));
		}
	}
	
	// Tentativa de casar os padrões utilizando a associatividade. Infelizmente não esta completa,
	// casando uma mísera parcela de casos. É possivel observar a imensa quantidade de combinações
	// que ainda podem ser feitas.
	/*public function matchAssociatively (Node $tree, Pattern $pattern) {
		$t = new WFFTranslator();
		if ( $tree->content->content == "&" ){
			$tmpTree = clone $tree;
			$this->normalizeConjunctions($tree);
			echo "[conjunction]:".$t->showFormulaInfix($tree)."<br />";
			if ($tree != $tmpTree) {
				for ($i = 0; $i < count ($tree->children); $i++) {
					for ($j = $i+1; $j < count ($tree->children); $j++) {
						$auxTree = new Node( new Connective("&", 2, 350));
						array_push($auxTree->children, $tree->children[$i]);
						array_push($auxTree->children, $tree->children[$j]);
						
						$this->matchPattern($auxTree, $pattern);
					}
				}
			}
			else {
				$this->matchPattern($tree, $pattern);
			}
		}
		else {
			if ( $tree->content->content == "|" ){
				$tmpTree = clone $tree;
				$this->normalizeDisjunctions($tree);
				echo "[disjunction]:".$t->showFormulaInfix($tree)."<br />";
				if ($tree != $tmpTree) {
					for ($i = 0; $i < count ($tree->children); $i++) {
						for ($j = $i+1; $j < count ($tree->children); $j++) {
							$auxTree = new Node( new Connective("|", 2, 300));
							array_push($auxTree->children, $tree->children[$i]);
							array_push($auxTree->children, $tree->children[$j]);
							
							$this->matchPattern($auxTree, $pattern);
						}
					}
				}
				else {
					$this->matchPattern($tree, $pattern);					
				}
			}
			else {
				$this->matchPattern($tree, $pattern);
			}
		}
	}*/
	
	/**
	 * Função que normaliza a arvore da FNC para que todas as conjunções aninhadas 
	 * sejam representadas por uma única disjunção.
	 *
	 * @param Node $tree
	 * @return Node
	 */
	public function normalizeConjunctions(&$tree) {
		if ($tree->content->content == "&") {	
			$root = new Node ( new Connective("&", 2, 350) );
			for ($i = 0; $i < count($tree->children); $i++) {
				if ($tree->children[$i]->content->content == "&") {
					array_push ($root->children, $tree->children[$i]->children[0]);
					array_push ($root->children, $tree->children[$i]->children[1]);
				}
				else {
					array_push ($root->children, $tree->children[$i]);
				}
			}
			if ($tree != $root) {
				$root = $this->normalizeConjunctions($root);
			}
			$tree = $root;
		}
		return $tree;
	}
	
	/**
	 * Normaliza as disjunções que são filhos da árvore na FNC, realizando 
	 * a mesma idéia da normalizeConjunctions.
	 *
	 * @param Node $tree
	 * @return Node
	 */
	public function normalizeDisjunctions(&$tree) {
		if ($tree->content->content == "|") {	
			$root = new Node ( new Connective("|", 2, 300) );
			for ($i = 0; $i < count($tree->children); $i++) {
				if ($tree->children[$i]->content->content == "|") {
					array_push ($root->children, $tree->children[$i]->children[0]);
					array_push ($root->children, $tree->children[$i]->children[1]);
				}
				else {
					array_push ($root->children, $tree->children[$i]);
				}
			}
			if ($tree != $root) {
				$root = $this->normalizeDisjunctions($root);
			}
			$tree = $root;
		}
		return $tree;
	}
	
	/**
	 * Acessa o rootArray
	 *
	 * @return array
	 */
	public function getRootArray() {
		return $this->rootArray;
	}
	
	/**
	 * Atribui um novo valor ao rootArray
	 *
	 * @param $newValue
	 */
	public function setRootArray($newValue) {
		$this->rootArray = $newValue;
	}
	
	/**
	 * Acessa o patternTypeArray
	 *
	 * @return array
	 */
	public function getPatternTypeArray() {
		return $this->patternTypeArray;
	}
	
	/**
	 * Atribui um novo valor ao patternTypeArray
	 *
	 * @param $newValue
	 */
	public function setPatternTypeArray($newValue) {
		$this->patternTypeArray = $newValue;
	}
	
	/**
	 * Verifica se algum padrão foi encontrado
	 *
	 * @return boolean
	 */
	public function hasMatched() {
		if (count($this->rootArray) == 0 ) {
			return false;
		}
		else {
			return true;
		}
	}
	
	/**
	 * Verifica se foi encontrado exatamente um padrão
	 *
	 * @param $pattern String que determina a regra a ser aplicada
	 */
	public function oneMatched($pattern) {
		$t = new WFFTranslator();
		$psol = new PatternSolutions();
	
		echo "One match finded: ".$t->showFormulaInfix($this->rootArray[0])."<br />";
		echo "Solving...<br />";
		$psol->solve($this->rootArray[0], $pattern, $this->patternTypeArray[0]);
		$this->rootArray = array();
		$this->patternTypeArray = array();
	}
	
	/**
	 * Imprime os padrões encontrados. Se não for o caso, imprime uma mensagem de erro para o usuário.
	 * Com ela e possivel atribuir índices a cada padrão encontrado para que o usuário tenha acesso a escolher um.
	 */
	public function printMatched() {
		if ( !$this->hasMatched() ) {
			echo "<strong style='color:#000066;'>[Error]: The chosen pattern has no matches.</strong><br />";
		}
		else {
			$translator = new WFFTranslator();
			for ($i = 0; $i < count($this->rootArray); $i++) { 
				echo "[".$i."] => ".$translator->showFormulaInfix($this->rootArray[$i])."<br />";
			}
			echo "<br />";
		}
	}
	
	/**
	 * Acha e armazena os casos de dupla negação que possam ocorrer na árvore dada, imprimindo
	 * em uma lista indexada todas as ocorrências encontradas.
	 * Caso haja somente uma ocorrência, realiza-se a conversão da dupla negação diretamente.
	 *
	 * @param Node $tree
	 * @param String $patternName
	 */
	public function matchDoubleNegation($tree, $patternName) {
		$pattern = PatternCreator::CreatePattern("doubleNegation", $tree);
		
		$this->matchPattern($tree, $pattern);
		
		if ( count($this->rootArray) == 1 ) {
			$this->oneMatched($patternName);
		}
		else {
			$this->printMatched();
		}
	}
	
	/**
	 * Acha e armazena os casos de distributividade que possam ocorrer na árvore dada, imprimindo
	 * em uma lista indexada todas as ocorrências encontradas.
	 * Caso haja somente uma ocorrência, realiza-se a conversão da distributividade diretamente.
	 *
	 * @param Node $tree
	 * @param String $patternName
	 */
	public function matchDistributivity($tree, $patternName) {
		$pattern1 = PatternCreator::CreatePattern("distributivity1", $tree);
		$pattern2 = PatternCreator::CreatePattern("distributivity2", $tree);
		
		$this->matchPattern($tree, $pattern1);
		$this->matchPattern($tree, $pattern2);
		
		if ( count($this->rootArray) == 1 ) {
			$this->oneMatched($patternName);
		}
		else {
			$this->printMatched();
		}
	}
	
	/**
	 * Acha e armazena os casos de absorção que possam ocorrer na árvore dada, imprimindo
	 * em uma lista indexada todas as ocorrências encontradas.
	 * Caso haja somente uma ocorrência, realiza-se a conversão da absorção diretamente.
	 *
	 * @param Node $tree
	 * @param String $patternName
	 */
	public function matchAbsorption($tree, $patternName) {
		$pattern1 = PatternCreator::CreatePattern("absorption1", $tree);
		$pattern2 = PatternCreator::CreatePattern("absorption2", $tree);
		$pattern3 = PatternCreator::CreatePattern("absorption3", $tree);
		$pattern4 = PatternCreator::CreatePattern("absorption4", $tree);
		$pattern5 = PatternCreator::CreatePattern("absorption5", $tree);
		$pattern6 = PatternCreator::CreatePattern("absorption6", $tree);
		$pattern7 = PatternCreator::CreatePattern("absorption7", $tree);
		$pattern8 = PatternCreator::CreatePattern("absorption8", $tree);
		
		$this->matchPattern($tree, $pattern1);
		$this->matchPattern($tree, $pattern2);
		$this->matchPattern($tree, $pattern3);
		$this->matchPattern($tree, $pattern4);
		$this->matchPattern($tree, $pattern5);
		$this->matchPattern($tree, $pattern6);
		$this->matchPattern($tree, $pattern7);
		$this->matchPattern($tree, $pattern8);
		
		if ( count($this->rootArray) == 1 ) {
			$this->oneMatched($patternName);
		}
		else {
			$this->printMatched();
		}
	}
	
	/**
	 * Acha e armazena os casos De Morgan que possam ocorrer na árvore dada, imprimindo
	 * em uma lista indexada todas as ocorrências encontradas.
	 * Caso haja somente uma ocorrência, realiza-se a conversão da De Morgan diretamente.
	 *
	 * @param Node $tree
	 * @param String $patternName
	 */
	public function matchDeMorgan($tree, $patternName) {
		$pattern1 = PatternCreator::CreatePattern("deMorgan1", $tree);
		$pattern2 = PatternCreator::CreatePattern("deMorgan2", $tree);
		
		$this->matchPattern($tree, $pattern1);
		$this->matchPattern($tree, $pattern2);
		
		if ( count($this->rootArray) == 1 ) {
			$this->oneMatched($patternName);
		}
		else {
			$this->printMatched();
		}
	}
	
	/**
	 * Acha e armazena os casos de implicação que possam ocorrer na árvore dada, imprimindo
	 * em uma lista indexada todas as ocorrências encontradas.
	 * Caso haja somente uma ocorrência, realiza-se a conversão da implicação diretamente.
	 *
	 * @param Node $tree
	 * @param String $patternName
	 */
	public function matchImplication($tree, $patternName) {
		$pattern = PatternCreator::CreatePattern("implication", $tree);
		
		$this->matchPattern($tree, $pattern);
		
		if ( count($this->rootArray) == 1 ) {
			$this->oneMatched($patternName);
		}
		else {
			$this->printMatched();
		}
	}
	
	/**
	 * Acha e armazena os casos de biimplicação que possam ocorrer na árvore dada, imprimindo
	 * em uma lista indexada todas as ocorrências encontradas.
	 * Caso haja somente uma ocorrência, realiza-se a conversão da biimplicação diretamente.
	 *
	 * @param Node $tree
	 * @param String $patternName
	 */
	public function matchBiimplication($tree, $patternName) {
		$pattern = PatternCreator::CreatePattern("biimplication", $tree);
		
		$this->matchPattern($tree, $pattern);
		
		if ( count($this->rootArray) == 1 ) {
			$this->oneMatched($patternName);
		}
		else {
			$this->printMatched();
		}
	}

	/**
	 * Acha e armazena os casos de idempotência que possam ocorrer na árvore dada, imprimindo
	 * em uma lista indexada todas as ocorrências encontradas.
	 * Caso haja somente uma ocorrência, realiza-se a conversão da idempotência diretamente.
	 *
	 * @param Node $tree
	 * @param String $patternName
	 */
	public function matchIdempotence($tree, $patternName) {
		$pattern1 = PatternCreator::CreatePattern("idempotence1", $tree);
		$pattern2 = PatternCreator::CreatePattern("idempotence2", $tree);
		
		$this->matchPattern($tree, $pattern1);
		$this->matchPattern($tree, $pattern2);
		
		if ( count($this->rootArray) == 1 ) {
			$this->oneMatched($patternName);
		}
		else {
			$this->printMatched();
		}
	}

	/**
	 * Acha e armazena os casos de elemento neutro que possam ocorrer na árvore dada, imprimindo
	 * em uma lista indexada todas as ocorrências encontradas.
	 * Caso haja somente uma ocorrência, realiza-se a conversão da elemento neutro diretamente.
	 *
	 * @param Node $tree
	 * @param String $patternName
	 */
	public function matchNeutralElement($tree, $patternName) {
		$pattern1 = PatternCreator::CreatePattern("neutralElement1", $tree);
		$pattern2 = PatternCreator::CreatePattern("neutralElement2", $tree);
		$pattern3 = PatternCreator::CreatePattern("neutralElement3", $tree);
		$pattern4 = PatternCreator::CreatePattern("neutralElement4", $tree);
		
		$this->matchPattern($tree, $pattern1);
		$this->matchPattern($tree, $pattern2);
		$this->matchPattern($tree, $pattern3);
		$this->matchPattern($tree, $pattern4);
		
		if ( count($this->rootArray) == 1 ) {
			$this->oneMatched($patternName);
		}
		else {
			$this->printMatched();
		}
	}

	/**
	 * Acha e armazena os casos de sups que possam ocorrer na árvore dada, imprimindo
	 * em uma lista indexada todas as ocorrências encontradas.
	 * Caso haja somente uma ocorrência, realiza-se a conversão da sups diretamente.
	 *
	 * @param Node $tree
	 * @param String $patternName
	 */
	public function matchSups($tree, $patternName) {
		$pattern1 = PatternCreator::CreatePattern("sups1", $tree);
		$pattern2 = PatternCreator::CreatePattern("sups2", $tree);
		
		$this->matchPattern($tree, $pattern1);
		$this->matchPattern($tree, $pattern2);
		
		if ( count($this->rootArray) == 1 ) {
			$this->oneMatched($patternName);
		}
		else {
			$this->printMatched();
		}
	}

	/**
	 * Acha e armazena os casos de infs que possam ocorrer na árvore dada, imprimindo
	 * em uma lista indexada todas as ocorrências encontradas.
	 * Caso haja somente uma ocorrência, realiza-se a conversão da infs diretamente.
	 *
	 * @param Node $tree
	 * @param String $patternName
	 */
	public function matchInfs($tree, $patternName) {
		$pattern1 = PatternCreator::CreatePattern("infs1", $tree);
		$pattern2 = PatternCreator::CreatePattern("infs2", $tree);
		
		$this->matchPattern($tree, $pattern1);
		$this->matchPattern($tree, $pattern2);
		
		if ( count($this->rootArray) == 1 ) {
			$this->oneMatched($patternName);
		}
		else {
			$this->printMatched();
		}
	}

	/**
	 * Acha e armazena os casos onde é possível normalizar os sups e  os infs, imprimindo
	 * em uma lista indexada todas as ocorrências encontradas.
	 * Caso haja somente uma ocorrência, realiza-se a normalização dos sups ou infs diretamente.
	 *
	 * @param Node $tree
	 * @param String $patternName
	 */
	public function matchNormalizeSupsAndInfs($tree, $patternName) {
		$pattern1 = PatternCreator::CreatePattern("normalizeSupsAndInfs1", $tree);
		$pattern2 = PatternCreator::CreatePattern("normalizeSupsAndInfs2", $tree);
		
		$this->matchPattern($tree, $pattern1);
		$this->matchPattern($tree, $pattern2);
		
		if ( count($this->rootArray) == 1 ) {
			$this->oneMatched($patternName);
		}
		else {
			$this->printMatched();
		}
	}

	/**
	 * Acha e armazena os casos de terceiro excluido que possam ocorrer na árvore dada, imprimindo
	 * em uma lista indexada todas as ocorrências encontradas.
	 * Caso haja somente uma ocorrência, realiza-se a conversão da terceiro excluido diretamente.
	 *
	 * @param Node $tree
	 * @param String $patternName
	 */
	public function matchExcludedMiddle($tree, $patternName) {
		$pattern1 = PatternCreator::CreatePattern("excludedMiddle1", $tree);
		$pattern2 = PatternCreator::CreatePattern("excludedMiddle2", $tree);
		
		$this->matchPattern($tree, $pattern1);
		$this->matchPattern($tree, $pattern2);
		
		if ( count($this->rootArray) == 1 ) {
			$this->oneMatched($patternName);
		}
		else {
			$this->printMatched();
		}
	}

	/**
	 * Acha e armazena os casos de explosão que possam ocorrer na árvore dada, imprimindo
	 * em uma lista indexada todas as ocorrências encontradas.
	 * Caso haja somente uma ocorrência, realiza-se a conversão da explosão diretamente.
	 *
	 * @param Node $tree
	 * @param String $patternName
	 */
	public function matchExplosion($tree, $patternName) {
		$pattern1 = PatternCreator::CreatePattern("explosion1", $tree);
		$pattern2 = PatternCreator::CreatePattern("explosion2", $tree);
		
		$this->matchPattern($tree, $pattern1);
		$this->matchPattern($tree, $pattern2);
		
		if ( count($this->rootArray) == 1 ) {
			$this->oneMatched($patternName);
		}
		else {
			$this->printMatched();
		}
	}
	
	/**
	 * Reune todas as funções de casamento de padrões facilitando a manipulação
	 * das mesmas.
	 *
	 * @param Node $tree
	 * @param String $patternName
	 */
	public function match($tree, $patternName) {
		switch ($patternName) {
			case "doubleNegation":
				$this->matchDoubleNegation($tree, $patternName);
				break;
			case "distributivity":
				$this->matchDistributivity($tree, $patternName);
				break;
			case "absorption":
				$this->matchAbsorption($tree, $patternName);
				break;
			case "deMorgan":
				$this->matchDeMorgan($tree, $patternName);
				break;
			case "implication":
				$this->matchImplication($tree, $patternName);
				break;
			case "biimplication":
				$this->matchBiimplication($tree, $patternName);
				break;
			case "idempotence":
				$this->matchIdempotence($tree, $patternName);
				break;
			case "neutralElement":
				$this->matchNeutralElement($tree, $patternName);
				break;
			case "sups":
				$this->matchSups($tree, $patternName);
				break;
			case "infs":
				$this->matchInfs($tree, $patternName);
				break;
			case "normalizeSupsAndInfs":
				$this->matchNormalizeSupsAndInfs($tree, $patternName);
				break;
			case "excludedMiddle":
				$this->matchExcludedMiddle($tree, $patternName);
				break;
			case "explosion":
				$this->matchExplosion($tree, $patternName);
				break;
		}
	}
}

?>

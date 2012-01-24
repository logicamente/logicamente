<?php
	/**
	* Signature.class.php
	*
	* @author Max Rosan
	* @author Thales Galdino
	* @author Giuliano Vilela
	* @author Lucas Araújo
	*/

	require_once("Node.class.php");
	require_once("Term.class.php");
	require_once("Quantifier.class.php");
	require_once("Connective.class.php");
	require_once("Constant.class.php");
	require_once("Variable.class.php");
	require_once("Function.class.php");
	require_once("Relation.class.php");
	
	require_once("mod_converter/formulaConverter2.class.php");
	
	require_once("SCMBuilder.class.php");

	error_reporting(E_ALL);

	/**
	* SCMChecker.
	* Classe responsável por encontrar todos os modelos
	* que satisfazem uma certa fórmula.
	*/
	class SCMChecker {
		/**
		* Prepara um SCMChecker para gerar todos os
		* modelos que satisfazem uma certa fórmula $delta.
		* Tenta montar modelos sob um certo tamanho de universo,
		* começando em 1 até $max_univ_size.
		* Esta versão é mais eficiente pois não precisa gerar
		* todos os modelos previamente. Ele realmente vai gerando de um
		* em um, sequencialmente.
		*/
		public function SCMChecker($gamma, $max_univ_size) {
			// O último tamanho de universo tentado foi 0
			$this->last_usize = 0;
			// Não existe um último modelo visto
			$this->last_model = null;
			// Não existe um último SCMBuilder preparado
			$this->builder = null;
			$this->max_usize = $max_univ_size;

			// Converte o array de strings em uma única string,
			// uma grande conjunção das hipóteses
			$this->gamma = "";
			$converter = new formulaConverter("T", "");

			foreach($gamma as $key => $hipot) {
				$this->gamma .= "(".$hipot.")";
				if ($key != sizeof($gamma)-1)
					$this->gamma .= " & ";
			}

			// Finalmente, armazena a foŕmula final em forma de árvore
			$this->gamma = $converter->infixToTree($this->gamma,true);
			// E depois extrai a assinatura da fórmula
			$this->signature = new Signature($this->gamma);
		}

		/**
		* Retorna o próximo modelo que satisfaz delta,
		* na sequência predeterminada.
		*/
		public function nextModel() {
			// Loop NÃO-infinito
			while (true) {
				// Caso não tenha um SCMBuilder preparado para gerar um
				// outro modelo, ou seja, caso esteja no início ou tenha
				// esgotado algum tamanho de universo.
				if ($this->builder == null) {
					// Caso o tamanho do último universo visto tenha sido o 
					// máximo que pode explorar, retorne null.
					if ($this->last_usize == $this->max_usize)
						return null;

					// Caso contrário, prepare um novo SCMBuilder para gerar os
					// modelos e atualize o valor do último tamanho de universo visto
					$this->builder = new SCMBuilder($this->signature,++$this->last_usize);
					// Como recomeçou, o último modelo visto não existe.
					$this->last_model = null;
				}
	
				// Caso consiga gerar um próximo modelo à partir do último visto
				if ($this->builder->generateNextModel($this->last_model)) {
					// Caso este modelo satisfaça $gamma
					if ($this->checkModel($this->gamma,$this->last_model)) {
						// Retorne-o
						return $this->last_model;
					}
				}
				// Caso o último modelo visto tenha sido o último
				else {
					// Invalide o SCMBuilder para que na próxima iterção do loop
					// possa gerar modelos para um outro tamanho de universo.
					$this->builder = null;
					$this->last_model = null;
				}
			}
		}

		/**
		* Checa se um certo modelo $model e uma atribuição
		* $attribution satisfazem uma fórmula $formula
		*/
		public function checkModel($formula,$model,$attribution = array()) {
			// $element irá guardar o elemento da fórmula atual
			$element = $formula->content;

			// Caso seja um conectivo
			if ($element instanceof Connective) {
				// Para avaliar esta satisfabilidade, precisamos saber o valor
				// de verdade de pelo menos um dos "filhos do conectivo"
				// O segundo filho só será avaliado caso seja realmente necessário
				$ch = array();
				// Guarda este valor em $ch[0]
				$ch[0] = $this->checkModel($formula->children[0],$model,$attribution);

				// Precisamos saber o tipo do conectivo
				switch ($element->content) {
					// Caso seja uma implicação clássica (A --> B)
					case "-->":
					case "imp":
					case "C":
						// Caso ¬A, retorne verdadeiro
						if (!$ch[0]) return true;
						// Caso contrário, retorne B
						return $this->checkModel($formula->children[1],$model,$attribution);
					// Caso seja uma bi-implicação clássica (A <-> B)
					case "<->":
					case "eq":
					case "E":
						// Retorne verdadeiro caso o valor de verdade de A seja igual ao de B
						return ($ch[0] == $this->checkModel($formula->children[1],$model,$attribution));
					// Caso seja uma conjunção (A & B)
					case "&":
					case "and":
					case "K":
						// Caso ¬A, retorne falso
						if (!$ch[0]) return false;
						// Caso contrário, retorne B
						return $this->checkModel($formula->children[1],$model,$attribution);
					// Caso seja uma disjunção (A | B)
					case "|":
					case "or":
					case "A":
						// Caso A, retorne verdadeiro
						if ($ch[0]) return true;
						// Caso contrário, retorne B
						return $this->checkModel($formula->children[1],$model,$attribution);
					// Caso seja um ou-exclusivo (A + B)
					case "+":
					case "xor":
					case "X":
						// Retorne verdadeiro caso o valor de verdade de A seja diferente ao de B
						return ($ch[0] != $this->checkModel($formula->children[1],$model,$attribution));
					// Caso seja uma negação (¬A)
					case "~":
					case "neg":
					case "N":
						// Retorne verdadeiro caso ¬A
						return (!$ch[0]);
					// Caso seja o top
					case "1":
					case "I":
						// Retorne verdadeiro
						return true;
					// Caso seja o bottom
					case "0":
					case "O":
						// Retorne falso
						return false;
				}
			}
			// Caso seja um quantificador
			elseif ($element instanceof Quantifier) {
				// Precisamos saber o tipo do quantificador
				switch ($element->content) {
					// Caso seja um para-todo
					case "A": {
						// Para cada elemento do universo
						for ($i = 0; $i < $this->last_usize; $i++) {
							// Geramos uma atribuição x-equivalente à atual,
							// onde o $attribution[x] é o elemento atual do laço
							$attribution[$element->bound_variable->value] = $i;
							// Caso o modelo $model e a nova atribuição não satisfaçam
							// a fórmula sendo quantificada
							if (!$this->checkModel($formula->children[0],$model,$attribution))
								// Então a quantificação não é satisfeita
								return false;
						}
						// Caso a fórmula seja válida para qualquer x,
						// então a quantificação é satisfeita
						return true;
					}
					// Caso seja um existe
					case "E": {
						// Para cada elemento do universo
						for ($i = 0; $i < $this->last_usize; $i++) {
							// Geramos uma atribuição x-equivalente à atual,
							// onde o $attribution[x] é o elemento atual do laço
							$attribution[$element->bound_variable->value] = $i;
							// Caso o modelo $model e a nova atribuição satisfaçam
							// a fórmula sendo quantificada
							if ($this->checkModel($formula->children[0],$model,$attribution))
								// Então a quantificação é satisfeita
								return true;
						}
						// Caso a fórmula não seja satisfeira para algum x,
						// então a quantificação não é satisfeita
						return false;
					}
					// Caso seja um existe-único
					case "E!": {
						// $found irá indicar se já foi encontrado um x tal que
						// a fórmula seja satisfeita
						$found = false;
						// Para cada elemento do universo
						for ($i = 0; $i < $this->last_usize; $i++) {
							// Geramos uma atribuição x-equivalente à atual,
							// onde o $attribution[x] é o elemento atual do laço
							$attribution[$element->bound_variable->value] = $i;
							// Caso o modelo $model e a nova atribuição satisfaçam
							// a fórmula sendo quantificada
							if ($this->checkModel($formula->children[0],$model,$attribution)) {
								// Caso já tenha achado um outro x que satisfaça a fórmula
								if ($found)
									// Então existem pelo menos dois que satisfazem. Logo,
									// a quantificação não é satisfeita
									return false;
								else
									// Caso este seja o primeiro, então atualize $found
									$found = true;
							}
						}
						// Ao final, $found indica se foi achado algum x
						// que satisfaça a fórmula
						return $found;
					}
				}
			}
			// Caso seja uma relação
			elseif ($element instanceof Relation) {
				// Precisamos extrair os termos (elementos do universo)
				// correspondentes aos argumentos da relação.
				// Armazenaremos seus valores em $ch
				$ch = array();

				// Para cada argumento $child desta relação
				foreach ($formula->children as $child) {
					// Extrai o termo correspondente à ele e armazena em $ch, ordenadamente
					$ch[] = $this->getTerm($child,$model,$attribution);
				}

				// Retorne o valor de verdade (true/false) o qual esta relação mapeia estes argumentos
				return $this->getMappedElement($model['_Relations'][$element->content]['interp'],$ch,0);
			}
			// Caso não seja nem conectivo, nem quantificador, nem relação
			else {
				// Então não trata-se de uma fórmula válida
				throw new Exception("Invalid formula.");
			}
		}
	
		/**
		* Retorna o elemento do universo representado pelo termo
		* $term no modelo $model e na atribuição $attribution
		*/
		public function getTerm($term,$model,$attribution) {
			// $element guarda o elemento atual deste termo $term
			$element = $term->content;
			
			// Caso seja uma constante
			if ($element instanceof Constant) {
				// Então o termo está no mapeamento de constantes do modelo
				return $model['_Constants'][$element->content];
			}
			// Caso seja uma variável
			elseif ($element instanceof Variable) {
				// Caso seja uma variável ligada (bounded)
				if ($element->isLinked) {
					// Então seu valor está presente na atribuição $attribution
					return $attribution[$element->value];
				}
				// Caso seja uma variável livre
				else {
					// Então é tratada do mesmo modo que uma constante
					return $model['_Constants'][$element->content];
				}
			}
			// Caso seja uma função
			elseif ($element instanceof Func) {
				// Precisamos pegar os termos correspondentes aos argumentos
				// desta função. Armazena-os em $ch
				$ch = array();
				
				// Para cada argumento $child desta função
				foreach ($term->children as $child) {
					// Pega o termo correspondente à este argumento
					$ch[] = $this->getTerm($child,$model,$attribution);
				}

				// Retorne o elemento do universo o qual esta função mapeia estes argumentos
				return $this->getMappedElement($model['_Functions'][$element->content]['interp'],$ch,0);
			}
			// Caso não seja uma constante, uma variável ou uma função
			else {
				// Então não é um termo válido
				throw new Exception("Invalid term element.");
			}
		}

		/**
		* Percorre as dimensões do array
		* de relação ou de função e retorna o
		* último elemento mapeado.
		*/
		public function getMappedElement($el,$args,$pos) {
			if ($pos == sizeof($args))
				return $el;
			else
				return $this->getMappedElement($el[$args[$pos]],$args,$pos+1);
		}

		public $gamma;
		public $signature;
		public $max_usize;
		public $last_model;
		public $last_usize;
		public $builder;
	}

?>

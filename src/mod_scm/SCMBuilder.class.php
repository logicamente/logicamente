<?php
	/**
	* SCMBuilder.class.php
	*
	* @author Max Rosan
	* @author Thales Galdino
	* @author Giuliano Vilela
	* @author Lucas Araújo
	*/

	require_once("Node.class.php");
	require_once("Term.class.php");
	require_once("Constant.class.php");
	require_once("Variable.class.php");
	require_once("Function.class.php");
	require_once("Relation.class.php");
	require_once("Signature.class.php");
	
	require_once("SCMUtil.class.php");

	require_once("mod_translator/WFFTranslator.class.php");
	
	error_reporting(E_ALL);

	/**
	* Classe responsável por enumerar todos
	* os possíveis modelos sob uma certa assinatura.
	*
	* @param $signature instância de Signature
	* @param $univ_size Tamanho do universo
	*
	* Exemplo de uso:
	*
	* $model = null;
	* $signature = new Signature($formula);
	* $univ_size = 4;
	* $builder = new SCMBuilder($signature,$univ_size);
	*
	* while ($builder->generateNextModel($model)) {
	* 	echo "New model: "; print_r($model);
	* }
	*
	* echo "No more models";
	*/
	class SCMBuilder {
		public function SCMBuilder($signature,$univ_size) {
			$this->univ_size = $univ_size;
			$this->signature = $signature;
		}

		/**
		* Retorna o "primeiro" modelo da sequência
		* que irá ser gerada sequencialmente pela classe.
		* À partir deste primeiro, pode gerar todos os outros.
		*/
		public function firstModel() {
			// Um modelo é implementado como sendo uma coleção:
			// '_UnivSize': Inteiro representando o tamanho do universo
			// '_Constants', '_Relations', '_Functions':
			//		Mapeamento do nome do elemento para a sua interpretação
			$model = array(
				'_UnivSize' => $this->univ_size,
				'_Constants' => array(),
				'_Relations' => array(),
				'_Functions' => array()
			);
			
			// Todas as constantes são mapeadas para o elemento 0
			foreach ($this->signature->constants as $const) {
				$model['_Constants'][$const->content] = 0;
			}

			// Todas as relações são conjuntos vazios (sempre não são o caso)
			foreach ($this->signature->relations as $rel) {
				$model['_Relations'][$rel->content] = array(
					"arity" => $rel->arity,
					"interp" => n_arity_array($rel->arity,$this->univ_size,false)
				);
			}

			// Todas as funções são tais que o conjunto imagem é {0}
			foreach ($this->signature->functions as $func) {
				$model['_Functions'][$func->content] = array(
					"arity" => $func->arity,
					"interp" => n_arity_array($func->arity,$this->univ_size,0)
				);
			}

			return $model;
		}

		/**
		* Recebe um modelo $model e
		* retorna o próximo modelo na sequência
		* de todos os modelos.
		* Caso receba null, irá retornar o primeiro.
		*
		* @param $model Recebe como referência e o altera para o próximo.
		* @return True caso consiga gerar um próximo modelo
		*/
		public function generateNextModel(&$model = null) {
			// Caso este seja o primeiro modelo sendo gerado
			if ($model == null) {
				// Monte-o de acordo com o método firstModel
				$model = $this->firstModel();
				return true;
			}

			// Primeiramente, tenta variar o valor dos mapeamentos das constantes
			if (gen_next_const($model['_Constants'],$this->univ_size)) {
				// Se conseguiu, então gerou um outro modelo na sequência
				return true;
			}

			// Para cada relação $rel
			foreach ($model['_Relations'] as &$rel) {
				// Transforma ela em uma representação uni-dimensional
				$flat_rel = rel_to_flat($rel['interp']);
				// Tenta variar o valor dos mapeamentos da relação
				// e armazena a indicação de sucesso em $b
				$b = gen_next_rel($flat_rel);
				// Pega a variação, retorna à forma de array multi-dimensional
				// e armazena novamente em $rel
				$rel['interp'] = flat_to_rel($flat_rel,$rel['arity'],$this->univ_size);

				// Caso tenha conseguido variar a relação, então
				// conseguiu variar o modelo
				if ($b) return true;
			}

			// Para cada função $func.
			foreach ($model['_Functions'] as &$func) {
				// Transforma ela em uma representação uni-dimensional
				$flat_func = func_to_flat($func['interp']);
				// Tenta variar o valor dos mapeamentos da função
				// e armazena a indicação de sucesso em $b
				$b = gen_next_func($flat_func,$this->univ_size);
				// Pega a variação, retorna à forma de array multi-dimensional
				// e armazena novamente em $func
				$func['interp'] = flat_to_func($flat_func,$func['arity'],$this->univ_size);

				// Caso tenha conseguido variar a função, então
				// conseguiu variar o modelo
				if ($b) return true;
			}

			// Caso tenha chegado aqui, não conseguiu variar
			// nenhum dos parâmetros. Logo, o modelo passado era o
			// último modelo para este universo. Retorne falso.
			return false;
		}

		public $univ_size;
		public $signature;
	}

?>

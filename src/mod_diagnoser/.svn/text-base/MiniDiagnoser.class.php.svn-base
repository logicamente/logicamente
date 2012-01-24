<?php
	/**
	* Esta classe implementa uma versao reduzida do Diagnoser, o qual analisa uma formula,
	* para ser usada no modulo da Tabela Verdade
	*
	* @author Alba Sandyra Bezerra Lopes <albasandyra@yahoo.com.br>
	* @author Jonuhey Ferreira da Costa <jonuhey_ferreira@yahoo.com.br>
	* @author Miklecio Bezerra da Costa <miklecio@gmail.com>
	* @version 2.0
	* @access public
	*/

	//require_once("autoload.php");

	class MiniDiagnoser
	{
		/**
		* @access private
		* @var Node
		* @name $formula
		*/
		private $formula;
	
		/**
		* Construtor do Mini Diagnoser
		* @access public
		* @param Node $formula Guarda a formula a ser analisada
		* @return void
		*/
		public function __construct(Node $formula)
		{
			$this->formula = $formula;
		}
		
		/**
		* funcao para obter os atomos da formula
		* @access public
		* @return array Atomos da formula
		*/
		public function getAtoms()
		{
			return $this->fixArrayIndexes(array_unique($this->getFAtoms($this->formula)));
		}
		
		/**
		* funcao para obter as subformulas da formula
		* @access public
		* @return array Subformulas da formula
		*/
		public function getSubFormulas()
		{
			return $this->fixArrayIndexes(array_unique($this->getFSubFormulas($this->formula)));
		}
		
		/**
		* funcao auxiliar recursiva para obter os atomos da formula
		* @access private
		* @param Node $node Node da subformula a ser analisada em busca de atomos
		* @return array Atomos da subformula
		*/
		private function getFAtoms(Node $node)
		{
			// atomos
			$nArray = array();
			
			if ( $node->isAtom() ) // atomo
			{
				array_push($nArray, $node);
				return $nArray;
			}
			else // conectivo
			{
				// guarda os atomos de todos os filhos
				foreach ($node->children as $child)
				{
					$nArray = array_merge($nArray , $this->getFAtoms($child));
				}
				return $nArray;
			}
		}
		
		/**
		* funcao auxiliar recursiva para obter as subformulas da formula
		* @access private
		* @param Node $node Node da subformula a ser analisada em busca de subformulas
		* @return array Subformulas da subformula
		*/
		private function getFSubFormulas($node)
		{
			// subformulas
			$nArray = array();
			
			if ($node->isAtom()) // atomo
			{
				array_push($nArray, $node);
				return $nArray;
			}
			else // conectivo
			{
				$atoms = array();
				$connectives = array();
				// separa os filhos atomos dos filhos conectivos
				foreach ($node->children as $child)
				{
					if ($child->isAtom()) {
						array_push($atoms, $child);
					}
					else {
						array_push($connectives, $child);
					}
				}
				
				// guarda primeiro as subformulas dos filhos atomos
				foreach ($atoms as $a) {
					$nArray = array_merge($nArray , $this->getFSubFormulas($a));
				}
				// depois guarda as subformulas dos filhos conectivos
				foreach ($connectives as $c) {
					$nArray = array_merge($nArray , $this->getFSubFormulas($c));
				}
				
				// guarda ele mesmo como subformula
				array_push($nArray, $node);

				return $nArray;
			}
			
		}

		/** 
		* Realiza a atualizacao dos indices de um array (necessaria apos a utilizacao de funcoes como array_unique,
		* que alteram os indices do array)
		* @access private
		* @param array $arr Array a sofrer a atualizacao
		* @return array Array atualizado
		*/
		private function fixArrayIndexes($arr) {
			
			$newArr = array();
			foreach ($arr as $val) {
				array_push($newArr, $val);
			}
			return $newArr;
			
		}
		
	}

?>

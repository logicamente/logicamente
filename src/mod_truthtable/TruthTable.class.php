<?php
	/**
	* Esta classe implementa a Tabela Verdade
	*
	* @author Alba Sandyra Bezerra Lopes <albasandyra@yahoo.com.br>
	* @author Jonuhey Ferreira da Costa <jonuhey_ferreira@yahoo.com.br>
	* @author Miklecio Bezerra da Costa <miklecio@gmail.com>
	* @version 2.0
	* @access public
	*/

	//require_once("autoload.php");

	class TruthTable
	{
		/**
		* @access protected
		* @var array
		* @name $formulas
		*/
		protected $formulas;
		/**
		* @access protected
		* @var integer
		* @name $options
		*/
		protected $options;
		
		/**
		* Construtor da Truth Table
		* @access public
		* @param array $formulas Guarda todas as formulas das quais serao geradas as Truth Tables
		* @param integer $options Define o tipo de Truth Table desejado
		* @return void 
		*/
		public function __construct($formulas, $options)
		{
			$this->formulas = $formulas;
			$this->options = $options;
		}

		/** 
		* funcao para gerar uma Truth Table completa (com todas as subformulas das formulas de entrada)
		* @access public
		* @return array Array bidimensional com uma linha contendo as formulas e as demais contendo as valoracoes
		*/
		public function getFullTruthTable()
		{
			
			//Obtendo todos os átomos e subfórmulas presentes nas fórmulas informadas
			$atomsA = array();
			$subformulas = array();
			
			//quando eh uma unica node, estava dando problema
			if ( count($this->formulas ) === 1) {
				$diagnoser = new MiniDiagnoser( $this->formulas );
				$atomsA = array_merge($atomsA, $diagnoser->getAtoms());
				$subformulas = array_merge($subformulas, $diagnoser->getSubFormulas());				
			}
			else {
				foreach($this->formulas as $form)
				{
					$diagnoser = new MiniDiagnoser($form);
					$atomsA = array_merge($atomsA, $diagnoser->getAtoms());
					$subformulas = array_merge($subformulas, $diagnoser->getSubFormulas());
				}
			}
			
			//Eliminando átomos e subfórmulas repetidos
			$atomsA = $this->fixArrayIndexes(array_unique($atomsA));
			$subformulas = $this->fixArrayIndexes(array_diff(array_unique($subformulas), $atomsA));
						
			//Quantidade de átomos e subfórmulas existentes
			$numberAtoms = count($atomsA);	
			$numberSubformulas = count($subformulas);
			
			//Quantidade de linhas da tabela
			$tableRows = pow(2, $numberAtoms);
			
			$table = array();
			
			//Gerando a 1ª linha da tabela com as representações infixas das subfórmulas
			$labels = array();
			for ($j = 0; $j < $numberAtoms; ++$j) {
				array_push($labels, $atomsA[$j]->content->content);
			}
			$t = new WFFTranslator();
			for ($j = 0; $j < $numberSubformulas; ++$j) {
				array_push($labels, $t->showFormulaInfix($subformulas[$j]));
			}
			
			array_push($table, $labels);
			
			//Gerando os valores da tabela verdade para cada linha
			for ($i = 0; $i < $tableRows; $i++)
			{
				//Valoração dos átomos em sequencia binária
				$atomsValues = $this->dec2bin($i, $numberAtoms);
				
				//print "dec2bin:<br/>";
				//print_r( $atomsValues );
				
				$values = array();
							
				//Valores dos átomos na tabela
				for ($j = 0; $j < $numberAtoms; ++$j) {
					array_push($values, $atomsA[$j]->content->value = $atomsValues[$j]);
					//print "ValAtom: ".$atomsA[$j]->content->value."<br/>";
				}				
								
				//Valoração das subfórmulas na tabela
				for ($j = 0; $j < $numberSubformulas; ++$j) {
					//array_push($values, $this->getConnectiveValue( $subformulas[$j] ) );
					array_push($values, $this->getConnectiveValue( $subformulas[$j] ) );
					//print "J: ".$j."<br/>";
					//print_r($subformulas[$j]);
				}		
												
				array_push($table, $values);
			}
			
			return $table;
			
		}
		
		private function fixSubFormulas( $subFormulas, $valuesAtoms){
			print "consertando: ";
			print_r($valuesAtoms);
			if ( count($subFormulas->children) > 0 ){				
				for ($i = 0; $i < count ($subFormulas->children); $i++){
					$this->fixSubFormulas ($subFormulas->children[$i], $valuesAtoms);
				}
			} else {//atom
				print "oi";print_r($subFormulas); print "foi";
				print " | val = ".$subFormulas->content->value;
				$subFormulas->content->value = $valuesAtoms[0];
				print "agora: ".$subFormulas->content->value;
				$valuesAtoms = array_shift($valuesAtoms);
			}
	
			return $subFormulas;
		}
		
		/** 
		* Gera uma Truth Table parcial (sem as subformulas das formulas de entrada)
		* @access public
		* @return array Array bidimensional com uma linha contendo as formulas e as demais contendo as valoracoes
		*/
		public function getPartialTruthTable()
		{
			
			//Obtendo todos os átomos presentes nas fórmulas informadas
			$atomsA = array();
			foreach($this->formulas as $form)
			{
				$diagnoser = new MiniDiagnoser($form);
				$atomsA = array_merge($atomsA, $diagnoser->getAtoms());
			}
			
			//Eliminando átomos repetidos
			$atomsA = $this->fixArrayIndexes(array_unique($atomsA));
			
			//Quantidade de átomos existentes
			$numberAtoms = count($atomsA);	
			
			//Quantidade de linhas da tabela
			$tableRows = pow(2, $numberAtoms);
			
			$table = array();
			
			//Gerando a 1ª linha da tabela com os átomos e as representações infixas das fórmulas
			$labels = array();
			$t = new WFFTranslator();
			foreach ($atomsA as $atom) {
				array_push($labels, $atom->content->content);
			}
			foreach ($this->formulas as $form) {
				array_push($labels, $t->showFormulaInfix($form));
			}
			
			array_push($table, $labels);
			
			//Gerando os valores da tabela verdade para cada linha
			for ($i = 0; $i < $tableRows; $i++)
			{
				
				//Valoração dos átomos em sequencia binária
				$atomsValues = $this->dec2bin($i, $numberAtoms);
				
				$values = array();
				
				//Valores dos átomos na tabela
				for ($j = 0; $j < $numberAtoms; ++$j) {
					array_push($values, $atomsA[$j]->content->value = $atomsValues[$j]);
				}
				
				//Valoração das fórmulas na tabela
				foreach ($this->formulas as $form) {
					array_push($values, $this->getFormulaValue($form));
				}
				
				array_push($table, $values);
				
			}
			
			return $table;
			
		}
		
		/** 
		* Imprime a Truth Table
		* @access public
		* @param integer $option Indica de que forma as valoracoes serao mostradas na impressao
		* @return void
		*/
		public function printTable($option) {
			
			switch ($this->options) {
				case 0: // Tabela completa
					$this->printTableHTML($this->getFullTruthTable(), $option);
					break;
				case 1: // Tabela parcial
					$this->printTableHTML($this->getPartialTruthTable(), $option);
					break;
			}
			
		}
		
		/** 
		* Verifica quais formulas sao tautologias, antilogias e contingencias
		* @access public
		* @param array $table Truth Table a ser verificada
		* @return array Array bidimensional com 3 linhas: uma para as tautologias, outra para as antilogias e outra para as contingencias
		*/
		public function getTACFromTable($table) {
			$t = new WFFTranslator();
			
			$lengthTable = count($table);
			
			$tautologias = array();
			$antilogias = array();
			$contigencias = array();

			/*
				Para cada fórmula de entrada é verificada a situação.
				A seguinte lógica foi utilizada: é contado o número de valores de verdade (quantidade de 1s)
				para cada fórmula da tabela, ou seja, quantos 1s aparecem na coluna correspondente àquela fórmula.
				Se a quantidade de 1s for igual à quantidade de linhas da tabela, então a fórmula é considerada
				tautologia e é inserida no array correspondente.
				Se a quantidade de 1s for igual a zero, a fórmula é inserida no vetor de antilogias.
				Caso contrário, trata-se de uma contingência, e a fórmula é inserida no vetor de contingência.
			*/
			
			if (count($this->formulas) === 1) {
				$label = $t->showFormulaInfix($this->formulas);
				$j = array_search($label, $table[0]);
				$count = 0;
				
				for ($i=1; $i<$lengthTable; ++$i)
				{
					if ($table[$i][$j] == 1)
					{
						++$count;
					}                                
				}
				
				if ($count == $lengthTable-1)
				{
					array_push($tautologias, $label);
				}
				else if ($count == 0){                                        
					array_push($antilogias, $label);
				}
				else {
					array_push($contigencias, $label);
				}				
			}
			else {
				foreach ($this->formulas as $form) {
					$label = $t->showFormulaInfix($form);
					$j = array_search($label, $table[0]);
					$count = 0;
					
					for ($i=1; $i<$lengthTable; ++$i)
					{
						if ($table[$i][$j] == 1)
						{
							++$count;
						}                                
					}
					
					if ($count == $lengthTable-1)
					{
						array_push($tautologias, $label);
					}
					else if ($count == 0){                                        
						array_push($antilogias, $label);
					}
					else {
						array_push($contigencias, $label);
					}
				}
			}
			
			return array($tautologias, $antilogias, $contigencias);
			
		}
		
		/** 
		* Funcao auxiliar que transforma um valor em binario. Sera utilizada para efetuar a atribuicao
		* de valores aos atomos. Como parametro, e passado tambem o tamanho do valor em binario desejado
		* ex: dec2bin(10, 6) tera como resultado um array da seguinte forma line = {0,0,0,1,1,0}.
		* @access protected
		* @param integer $number Valor a ser transformado em binario
		* @param integer $size Tamanho do valor binario desejado
		* @return array Array com os bits do valor em binario
		*/
		protected function dec2bin($number, $size)
		{
			$line = array();
			
			//Obtendo o valor do número em binário. O retorno é uma string.
			$numberBin = decbin($number);
			
			//Obtendo o tamanho da string contendo o valor binário
			$lenNumberBin = strlen($numberBin);
			
			//Calculando a diferença entre o tamanho obtido e o desejado
			$diff = ($size - $lenNumberBin);
			
			//Inserindo no array de retorno os 0's extras necessários para obter o tamanho do valor
			for ($i = 0; $i < $diff; ++$i) {
				array_push($line, 0);
			}
			
			//Inserindo no array de retorno o valor binário
			for ($i = 0; $i < $lenNumberBin; ++$i) {
				array_push($line, $numberBin[$i]);
			}
			
			return $line;
			
		}
		
		/** 
		* Funcao para obter o valor do conectivo de uma formula.
		* @access protected
		* @param Node $nodeConnective Node do conectivo na formula
		* @return integer Valoracao do conectivo
		*/
		protected function getConnectiveValue($nodeConnective) {
			
			switch ($nodeConnective->content->content) {

				//Negação
				//case "~":
				case "&not;":
					$nodeConnective->content->value = $nodeConnective->children[0]->content->value == 1 ? 0 : 1;
					break;

				//Conjunção
				//case "^":
				case "&and;":
					$nodeConnective->content->value = 1;
					foreach ($nodeConnective->children as $subFormula) {
						if ($subFormula->content->value == 0) {
							$nodeConnective->content->value = 0;
							break;
						}
					}
					break;

				//Disjunção
				//case "v":
				case "&or;":
					$nodeConnective->content->value = 0;
					foreach ($nodeConnective->children as $subFormula) {
						//print "<br/>";
						//print_r( $subFormula->content->content );
						//print "sub: ".$subFormula->content->value;
						if ($subFormula->content->value == 1) {
							$nodeConnective->content->value = 1;
							break;
						}
					}
					//print "<br/>Con: ".$nodeConnective->content->value."<br/><br/>";
					break;
					
				//Implicação
				//case "-->":
				case "&rarr;":
					if ($nodeConnective->children[0]->content->value == 1 && $nodeConnective->children[1]->content->value == 0) {
						$nodeConnective->content->value = 0;
					}
					else {
						$nodeConnective->content->value = 1;
					}
					break;
					
				//Bi-implicação
				//case "<->":
				case "&harr;":
					if ($nodeConnective->children[0]->content->value == $nodeConnective->children[1]->content->value) {
						$nodeConnective->content->value = 1;
					}
					else {
						$nodeConnective->content->value = 0;
					}
					break;
					
				//Disjunção Exclusiva
				//case "+":
				case "&oplus;":
					if ($nodeConnective->children[0]->content->value != $nodeConnective->children[1]->content->value) {
						$nodeConnective->content->value = 1;
					}
					else {
						$nodeConnective->content->value = 0;
					}
					break;
					
				//Top
				//case "T":
				case "&#8868;":
					$nodeConnective->content->value = 1;
					break;

				//Bottom
				//case "B":
				case "&#8869":
				
					$nodeConnective->content->value = 0;
					break;
			}
			
			return $nodeConnective->content->value;
			
		}
		
		/** 
		* Realiza a atualizacao dos indices de um array (necessaria apos a utilizacao de funcoes como array_unique,
		* que alteram os indices do array)
		* @access protected
		* @param array $arr Array a sofrer a atualizacao
		* @return array Array atualizado
		*/
		protected function fixArrayIndexes($arr) {
			
			$newArr = array();
			foreach ($arr as $val) {
				array_push($newArr, $val);
			}
			return $newArr;
			
		}
		
		/** 
		* Funcao recursiva que retorna o valor de uma formula atraves da analise dos atomos e subformulas
		* @access protected
		* @param Node $form Node da formula a ser valorada
		* @return integer Valoracao da formula
		*/
		protected function getFormulaValue($form) {
			
			if ($form->isAtom()) {
				return $form->content->value;
			}
			else {
				foreach ($form->children as $sub) {
					$this->getFormulaValue($sub);
				}
				return $this->getConnectiveValue($form);
			}
			
		}
		
		/** 
		* Impressao da tabela em formato HTML, junto com as informacoes
		* sobre tautologias, antilogias e contingencias.
		* Permite optar pela impressao em notacao binaria (0 e 1) ou (V e F)
		* @access protected
		* @param array $table Truth Table a imprimir
		* @param integer $option Opcao de impressao dos valores (0 e 1) ou (V e F)
		* @return void
		*/
		protected function printTableHTML($table, $option) {
			
			//Imprimindo a tabela
			print "<table id=\"truthTable\">\n";
			foreach ($table as $linha) {
				print "<tr>\n";
				foreach ($linha as $celula) {				
					if($celula == "0" || $celula == "1")
					{				
						//Escolha do formato de impressão. (0) para formato 0 e 1. 
						//								   (1) para formato V e F	 				
						if ($option == 0)
							print "<td>$celula</td>";
						else 
							print "<td>".($celula==0?"F":"V")."</td>";
					}
					else
						print "<td class=\"title\">$celula</td>";
						
					print "</td>\n";
				}
				print "</tr>\n";
			}
			print "</table>\n";
			
			//Obtendo o vetor que contem as fórmulas classificadas em Tautologia, antilogia e contingência.
			$tac = $this->getTACFromTable($table);
			print "<br/>";
			print "Tautologias:
					<ul>\n";
			
			//Imprimindo as tautologias
			if (count($tac[0]) > 0) {
				foreach ($tac[0] as $taut) {
			 		print "<li>$taut</li>\n";
				}
			}
			else {
				print "<li>Nenhuma</li>";
			}
			print "</ul>\n";
			print "Antilogias:
					<ul>\n";
			
			//Imprimindo as antilogias
			if (count($tac[1]) > 0) {
				foreach ($tac[1] as $ant) {
				 	print "<li>$ant</li>\n";
				}
			}
			else {
				print "<li>Nenhuma</li>";
			}
			print "</ul>\n";
			print "Conting&ecirc;ncias:
					<ul>\n";
			
			//Imprimindo as contingências
			if (count($tac[2]) > 0) {
				foreach ($tac[2] as $cont) {
				 	print "<li>$cont</li>\n";
				}
			}
			else {
				print "<li>Nenhuma</li>";
			}
			print "</ul>\n";
			
		}
		
	}
	

?>

<head> 
<!--  <script type="text/javascript" src="teste.js"></script> -->
</head>
<?php
ini_set("memory_limit","1000M");
///////////////////////////////////////////
//		INCLUDES!!!!!		 //
///////////////////////////////////////////
require_once("mod_converter/formulaConverter2.class.php");

////////////////////////////////////////////
//	HTML Interaction section	  //
////////////////////////////////////////////
$input            = $HTTP_POST_VARS['formula'];
$domain		  = $HTTP_POST_VARS['domain'];


/*echo "<br>Formula escolhida: $input<br>";
echo "Cardinalidade do domínio: $domain<br>";
echo "<br>----------------------------------------------------<br>";*/


/** Classes section **/
/////////////////////////////////////////////////
//	Classe Item da Lista Ligada            //
/////////////////////////////////////////////////
class item
{
	var $data;
	var $next;

	function item($term)
	{
		$this->data=$term;
		$this->next=null;
	}
}


/////////////////////////////////////////////////
//		Classes de Listas Ligadas      //
/////////////////////////////////////////////////
class slist
{
	var $head;
	var $size;

	function slist()
	{
		$this->head=null;$size=0;
	}

	function add($term)
	{
		$aux = new item($term);		
		$aux->next=$this->head;
		$this->head=$aux;
		$this->size++;
	}

	function pop()
	{
		if($this->size>0)$this->size--; else return null;
		$aux = $this->head->data;
		$this->head=$this->head->next;
		return $aux;
	}

	function size()
	{
		return $this->size;
	}

	function printl()
	{
		$sum=0;
		$aux = $this->head;
		while($aux)
		{
			$sum++;		
			echo($aux->data->content->content);echo " ";
			$aux=$aux->next;
		}
		echo("Total : ".$sum);
	}

	function is_empty()
	{			
		return $this->head==null;
	}
}


class queue
{
	var $head;
	var $size;

	function queue()
	{
		$this->head=null;$size=0;
	}

	function add($term)
	{						
		$this->size++;
		if($this->head==null)
			{$this->head=new item($term);return;}
		$aux=$this->head;		
		while($aux->next!=null)		
			$aux=$aux->next;
		$aux->next = new item($term);
	}

	function pop()
	{
		if($this->size>0)$this->size--; else return null;
		$aux = $this->head->data;
		$this->head=$this->head->next;
		return $aux;
	}

	function size()
	{
		return $this->size;
	}

	function printl()
	{
		$sum=0;
		$aux = $this->head;
		while($aux)
		{
			$sum++;		
			echo($aux->data->content->content);echo " ";
			$aux=$aux->next;
		}
		echo("Total : ".$sum);
	}

	function is_empty()
	{			
		return $this->head==null;
	}
}


/////////////////////////////////////////////////////////////////

/** Recursive permutation function. Used to generate ALL the truth table **/
function permuta_all($counter,$tree,$lista,$matrix,$i,$j,$dom)
{
	
	if($lista->is_empty())
	{		
		valorize($tree, $matrix);
		$SCM = evaluate($tree);	
		if($SCM==0)
			$counter->add( new countermodel($matrix));
	}
	else
	{		
		$aux = $lista->pop();

		if($matrix[$i][$j] instanceOf Variable && $matrix[$i][$j]->content->isLinked!=1)
		{			
			$nextI=(($j+1)%count($matrix[$i]))==0 ? ($i+1) : ($i);				
			$nextJ=($j+1)%count($matrix[$i]);
			permuta_all(&$counter,&$tree,&$lista,&$matrix,$nextI,$nextJ,$dom);
		}
		elseif($matrix[$i][$j] instanceOf Relation)
		{
			for($k=0;$k<2;$k++)
			{				
				$matrix[$i][$j]->value=$k;
				$nextI=(($j+1)%count($matrix[$i]))==0 ? ($i+1) : ($i);				
				$nextJ=($j+1)%count($matrix[$i]);			
				permuta_all(&$counter,&$tree,&$lista,&$matrix,$nextI,$nextJ,$dom);
			}
		}else											
		for($k=0;$k<$dom;$k++)
		{				
			$matrix[$i][$j]->value=$k;
			$nextI=(($j+1)%count($matrix[$i]))==0 ? ($i+1) : ($i);			
			if(count($matrix[$nextI])==0)$nextI++;
			$nextJ=($j+1)%count($matrix[$i]);			
			permuta_all(&$counter,&$tree,&$lista,&$matrix,$nextI,$nextJ,$dom);
		}
		$lista->add($aux);
	}
}

////////////////////////////////////////////////////////////////////////////////

/** copia nodes em outros nodes **/
function copyElement($term)
{
	$node = $term->content;
	if($node instanceof Relation)
	{
		$aux = new Relation($term->content);
		$aux->arity = $term->arity;
		$aux->value = $term->value;
		for($i = 0; $i < count($term->children); $i++)
		{
			$aux->children[$i] = copyElement($term->children[$i]);
		}
		
		return $aux;	
	}
	elseif($node instanceof Func)
	{
		$aux = new Func($node);
		$aux->arity = $term->arity;
		$aux->value = $term->value;
		for($i = 0; $i < count($term->children); $i++)
		{
			$aux->children[$i] = copyElement($term->children[$i]);
		}
		return $aux;
	}
	elseif($node instanceof Variable)
	{
		$aux = new Variable(new Variable($node->content));
		$aux->value = $term->value;
		$aux->isLinked = $term->isLinked;
		return $aux;
	}elseif($node instanceof Constant)
    	{
        	$aux = new Constant($node);
        	$aux->value = $term->value;        
        	return $aux;
    	}
}


/** Função que imprime (por inércia) e que coloca os termos na matriz, em ordem: variáveis, funções, relações. (Depois colocar constantes) **/

function printTree3 ($tree,$ident, &$elements)
{		
	/*Testa se é um conectivo*/
	$teste = $tree;
	$node = $tree->content;
	if ($node instanceof Quantifier)
	{
		$node2 = $node->bound_variable;				
		foreach ($tree->children as $term) 
			printTree3($term,$ident, $elements);			
	}
	elseif ($node instanceof Connective)
	{		
		foreach ($tree->children as $term) 
			printTree3($term,$ident, $elements);
			
	} elseif ($node instanceof Relation)
	{	
		$indice = count($elements[2]);
		/** Prucura se a relação já não foi inserida antes **/
		$belong = false;
		for ($i = 0; $i < $indice && !$belong; $i ++)		
		if (!(strcmp($node->content, $elements[2][$i]->content->content)))
				$belong = true;
		if(!$belong)
			$elements[2][$indice] = $teste;					
		foreach ($tree->children as $term) 
			printTree3($term,$ident, $elements);
			
	}
	elseif ($node instanceof Func)
	{
		$indice = count($elements[1]);
		$belong = false;
		for ($i = 0; $i < $indice && !$belong; $i ++)		
		if (!(strcmp($node->content, $elements[1][$i]->content->content)))
			$belong = true;		
		if(!$belong)
			$elements[1][$indice] = $teste;				
		foreach ($tree->children as $term) 
			printTree3($term,$ident, $elements);			
	}
	elseif ($node instanceof Variable)
	{	
		$indice = count($elements[0]);
		$belong = false;
		for ($i = 0; $i < $indice && !$belong; $i ++)		
		if (!(strcmp($node->content, $elements[0][$i]->content->content)))
			$belong = true;
		if(!$belong)
			$elements[0][$indice] = $teste;
	}elseif ($node instanceof Constant)
	{
        	$indice = count($elements[0]);
        	$belong = false;
        	for ($i = 0; $i < $indice && !$belong; $i ++)
        	{
            	if (!(strcmp($node->content, $elements[0][$i]->content->content)))
	                $belong = true;
        	}
        	if(!$belong)
            		$elements[0][$indice] = copyElement($teste);
    }
}
	

/** Função que valoriza os nós relação da árvore **/

function valorize(&$tree, &$elements)
{
	
	
	$teste = $tree;
	$node = $tree->content;
		
	if ($node instanceof Quantifier) {
		valorize($tree->children[0], $elements);
	
	}elseif ($node instanceof Connective) {
		foreach ($tree->children as $term) {
			valorize($term, $elements);
		}
	/*Testa se é uma relação*/
	} elseif ($node instanceof Relation) {
		
		$indice = count($elements[2]);
		$belong = false;
		
		for ($i = 0; $i < $indice && !$belong; $i ++)
		{
			if (!(strcmp($node->content, $elements[2][$i]->content->content)))
			{
				$belongI = false;
				for ($j = 0; ($j < $tree->content->arity) && (!$belongI); $j++)
				{
					if (getValue($tree->children[$j], $elements)!= $elements[2][$i]->children[$j]->value)
					{
						$belongI = true;
					}
				}
				if(!$belongI)
				{
					$tree->value = $elements[2][$i]->value;
					$belong = true;
				}
			}
		}
		
	}
}

/** Valoriza as funções e variáveis, para retornarem seus valores aos filhos da relação **/

function getValue(&$tree, &$elements)
{
	$node = $tree->content;
	/*Testa se é uma função*/	
	 if ($node instanceof Func) {
		$indice = count($elements[1]);
		$belong = false;
		for ($i = 0; $i < $indice && !$belong; $i ++)
		{
			if (!(strcmp($node->content, $elements[1][$i]->content->content)))
			{
				$belongI = false;
				for ($j = 0; ($j < $tree->arity) && (!$belongI); $j++)
				{
					if (getValue($tree->children[$i], $elements)!= $elements[1][$i]->children[$j]->value)
						$belongI = true;
					
				}
				if(!$belongI)
				{
					$tree->value = $elements[1][$i]->value;
					$belong = true;
				}
				return $tree->value;
			}
		}
	/*Testa se é uma variável*/	
	} elseif ($node instanceof Variable) {

		$indice = count($elements[0]);
		$belong = false;
		for ($i = 0; $i < $indice && !$belong; $i ++)
		{
			if (!(strcmp($node->content, $elements[0][$i]->content->content)))
			{
				$tree->value = $elements[0][$i]->value;
				return $tree->value;
			}
		}
	}	

	 elseif ($node instanceof Constant) {

		$indice = count($elements[0]);
		$belong = false;
		for ($i = 0; $i < $indice && !$belong; $i ++)
		{
			if (!(strcmp($node->content, $elements[0][$i]->content->content)))
			{
				$tree->value = $elements[0][$i]->value;
				return $tree->value;
			}
		}
	}
}

/** Baseado nas valorações das relações, valoriza os conectios **/

function evaluate($tree)
{
	$node = $tree->content;
	
	if ($node instanceof Quantifier) {
        	return evaluate($tree->children[0]);

    	}
	elseif( $node instanceof Connective)
    	{	switch($node->content)
		{
			case "-->":
				$Left = evaluate($tree->children[0]);
				$Right = evaluate($tree->children[1]);
				if (($Left == 1) && ($Right == 0))
					$tree->value = 0;
				else
					$tree->value = 1;
				return $tree->value;
				
			case "<->":
				$Left = evaluate($tree->children[0]);
				$Right = evaluate($tree->children[1]);
				if ($Left == $Right)
					$tree->value = 1;
				else
					$tree->value = 0;
				return $tree->value;
				
			case "&":
				$Left = evaluate($tree->children[0]);
				$Right = evaluate($tree->children[1]);
				if (($Left == 1) && ($Right == 1))
					$tree->value = 1;
				else
					$tree->value = 0;
				return $tree->value;
				
		  	case "|":
        		        $Left = evaluate($tree->children[0]);
                		$Right = evaluate($tree->children[1]);
                
                		//echo ("<DD>Left: ".$Left." Right: ".$Right."<BR>");
                		if (($Left == 1) || ($Right == 1))
                    			$tree->value = 1;
                		else
                    			$tree->value = 0;
                		return $tree->value;

                
			case "+":
				$Left = evaluate($tree->children[0]);
				$Right = evaluate($tree->children[1]);
				
				//echo ("<DD>Left: ".$Left." Right: ".$Right."<BR>");
				if ($Left == $Right)
				$tree->value = 0;
				else
				$tree->value = 1;
				return $tree->value;
                
	
			case "~":
				$Left = evaluate($tree->children[0]);
				if ($Left == 1)
					$tree->value = 0;
				else
					$tree->value = 1;
				return $tree->value;
		}			
	}
	else
		return $tree->value;
	
}

///Seta um vetor recursivamente
function setup($children,$i,$domain)
{	
	for($q=0;$q<count($children);$q++)
		$children[$q]->value=0;
	while($i>0)
	{		
		$i--;
		$k=count($children)-1;
		if($children[$k]->value<$domain-1)$children[$k]->value++;
		else
		{
			$carry=1;$h=$k;
			while($carry!=0)
			{
				if($h==0)break;
				$children[$h]->value=0;$carry=0;				
				if($children[$h-1]->value<$domain-1)$children[$h-1]->value++;
				else {$carry=1;$h--;}
			}
		}
	}

}

///Expande a matrix passada, de modo que ela possa ser apropriadamente usada em permuta_all
function expand($matrix,$domain)
{

	///Set the aux buffer!	
	for($k=1;$k<3;$k++)if(count($matrix[$k])>0){
	$buffer=array();
	for($i=0;$i<count($matrix[$k]);$i++)
		$buffer[$i]=copyElement($matrix[$k][$i]);

	///Set the array size!
	$sum=0;
	for($i=0;$i<count($buffer);$i++)	
		$sum=$sum + pow($domain, count($buffer[$i]->children) );			

	///Extrude the array!
	$i=count($matrix[$k]);
	if($i>0)while( count($matrix[$k])<$sum )
	{
		$matrix[$k][$i]=copyElement($matrix[$k][$i-1]);
		$i++;
	}
	
	//Fix the strings messed up last stage!
	$i=0;
	for($h=0;$h<count($buffer);$h++)
	{	
		$sum=pow($domain,count($buffer[$h]->children))+$i;
		$iteration=0;
		for($i;$i<$sum;$i++)
		{				
			$matrix[$k][$i]=copyElement($buffer[$h]);
			setup(&$matrix[$k][$i]->children,$iteration++,$domain);

			//Check if expand works as should

		}
	}}
}

class countermodel
{
	var $matrix;
	function countermodel($mat)
	{
		$this->matrix=array();
		for($i=0;$i<3;$i++)if( count($mat[$i])!=0 )
			for($j=0;$j<count($mat[$i]);$j++)		
				$this->matrix[$i][$j]=copyElement($mat[$i][$j]);
	}	

	function printNode($node)
	{		
		if($node instanceOf Variable)return $node->value;
		$ret=$node->content->content;
		$ret=$ret."(";
		$i=0;
		while($i<count($node->children))
		{
			$ret=$ret.($node->children[$i++]->value);
			if($i!=count($node->children))$ret=$ret.',';
		}
		$ret=$ret.")";
		return $ret;
		
	}

	function printCounterModel()
	{
		for($i=0;$i<3;$i++)if( count($this->matrix[$i])!=0 )
		for($j=0;$j<count($this->matrix[$i]);$j++)
		{					
			if($this->matrix[$i][$j] instanceOf Variable || $this->matrix[$i][$j] instanceOf Constant) echo ( ($this->matrix[$i][$j]->content->content)." = ".$this->matrix[$i][$j]->value."<br>");
			else echo( $this->printNode($this->matrix[$i][$j])." = ".$this->matrix[$i][$j]->value."<br>");			
			if($j==count($this->matrix[$i])-1)echo("<br>");
		}
		echo("--------------------------<br>");
	}
}

function printMatrix($mat)
{	
	for($i=0;$i<3;$i++)if( count($mat[$i])!=0 )
	for($j=0;$j<count($mat[$i]);$j++)
	{		
		echo (($mat[$i][$j]->content->content).($mat[$i][$j]->value)." ");
		if($j==count($mat[$i])-1)echo("<br>");
	}
	echo("<br>");
}

function uber_valorize($node, $var, $value)
{

	if($node->content instanceof Quantifier && !strcmp($node->content->content,$var->content))return;
	if( $node->content instanceof Variable && !strcmp($node->content->content,$var->content) )
		$node->content->value=$value;
	else
		for($i=0;$i<count($node->children);$i++)
			uber_valorize($node->children[$i], $var, $value);
}

function uber_expand($root,$domain,$fila)
{
	$append=null;	

	///enqueue quantifiers
	if($root->content instanceof Quantifier)			
		$fila->add($root);	
	
	///call for children
	for($i=0;$i<count($root->children);$i++)
	{		
		$append=uber_expand(&$root->children[$i],$domain,&$fila);
		if($append!=null)					
			$root->children[$i]=$append;				
	}	
	
	///back from children
	if($root->content instanceof Quantifier)
	{		

		$node=$fila->pop();
		if($node==null)return null;	
		$aux=null;		

		if(!strcmp($node->content->content,'A'))$aux = new Node(new Connective("&",2,350));
		elseif(!strcmp($node->content->content,'E'))$aux = new Node(new Connective("|",2,300));

		if($aux==null)return null; /// well this shouldn't really happen, so blow everything up!
		
		$aux->children[0]=copyElement($root->children[0]);
			uber_valorize($aux->children[0],$node->content->bound_variable,0);
	
		$aux->children[1]=copyElement($root->children[0]);
			uber_valorize($aux->children[1],$node->content->bound_variable,1);
		
		return $aux;
	}
	return null;
}

/** main code here **/

	$formato = "pre";
	$exp = $input;
	
	$elements = array();
			
	$tester = new formulaConverter("T","");
	$test = $tester->infixToTree($exp,true);	
	printTree3($test,"", $elements);
	
	expand(&$elements,$domain);

	$lista = new slist();
	for($i=2;$i>=0;$i--)if( count($elements[$i])!=0 )
	for($j=count($elements[$i])-1;$j>=0;$j--)
		$lista->add($elements[$i][$j]);
	
	///brake quantifiers down
	$myqueue=new queue();
 	$aux=uber_expand(&$test,$domain,&$myqueue);

// 	$tester->printTree($aux,'');

	$counterList = new slist();
	permuta_all(&$counterList,$test,$lista,&$elements,0,0,$domain);
	
	echo("<font size=\"3\"><Blockquote><Blockquote><Blockquote><Blockquote>
	<Blockquote><Blockquote><Blockquote><Blockquote><center><fieldset><legend>Small Counter-Model</legend>
	<fieldset><legend>Parâmetros </legend>Formula Escolhida: $input<br><br>Cardinalidade Escolhida: $domain<br>
	</fieldset><br><fieldset><legend>Counter-Models</legend>");

	while(!$counterList->is_empty())
		$counterList->pop()->printCounterModel();
	

	echo("</fieldset></fieldset></center></Blockquote></Blockquote></Blockquote>
	      </Blockquote></Blockquote></Blockquote></Blockquote></Blockquote></font>");
	
?>

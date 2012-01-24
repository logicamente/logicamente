

<html>
<head>
<title>Untitled Document</title>

<style>
	ul{
		list-style:none;
		padding:0px;
		margin:0px;
	}
	
	li{
		float:left;
		padding:0px;
		margin:0px;
	}
	
	.connective{
		background:#008800;
		text-align:center;
		padding:5px;
		margin:5px;
		border: 1px solid #000000;
		font-weight:bold;
	}	
	.connectiveChild{background:#00BB00;}
	.connectiveActive{background:#00FF00;}
	.connectiveSame{background:#FFCC00;}
	
	.atom{
		background:#000088;
		text-align:center;
		padding:5px;
		margin:5px;
		border: 1px solid #000000;
	}
	.atomChild{background:#0000BB;}
	.atomActive{background:#0000FF;}
	.atomSame{background:#00FFFF;}
	
	
</style>

<script src="jquery.js"></script>
<script src="interface.js"></script>

<script>	

	var curr = "";
	
	$(document).ready(function(){
	
		$('.connective, .connectiveChild').bind('click', function(){
			limpar();
			$('.connective', this.parentNode).addClass('connectiveChild');
			$('.atom', this.parentNode).addClass('atomChild');
			
			curr = $(this).attr("id");
			$(".connective", this.parentNode).each(function(){
				if ($(this).attr("id") == curr)
					$(this).addClass("connectiveSame");
			});
			$(this).removeClass('connectiveSame');
			$(this).addClass('connectiveActive');
		});
		
		$('.atom').bind('click', function(){
			limpar();
			var aux = $(this).html();
			$('.atom').each(function(){
				if ($(this).html() == aux)
					$(this).addClass('atomSame');
			});
			$(this).removeClass('atomSame');
			$(this).addClass('atomActive');
			
		});
		
		$(".connective, .atom").bind('click',function(){
			$.post("treeajax.php",
				{
					index: $(this).attr("index")
				},
				//Descomente o "alert" para depuração
				function(result){
					$("#resultado").html(result);
				}
	);
		});
	});
	
	function limpar(){
		$('.connective').removeClass('connectiveActive');
		$('.connective').removeClass('connectiveChild');
		$('.connective').removeClass('connectiveSame');
		$('.atom').removeClass('atomActive');
		$('.atom').removeClass('atomChild');
		$('.atom').removeClass('atomSame');
	}
</script>

</head>

<body>

<?php
	$counter = 0;
	
	function showFormulaUL($formula){
		global $counter;
		$f = "<ul>";
		if (count($formula->children) > 0){
			$f .= "<li><p id='c".$formula->content->content."' class='connective' index='".$counter."'>".$formula->content->content."</p><script></script>";
			$counter++;
			for ($i = 0; $i < count ($formula->children); $i++){
				$f .= showFormulaUL ($formula->children[$i]);
			}
			$f .= "</li>";
		} else {
			$f .= "<li><p class='atom' index='".$counter."'>".$formula->content->content."</p></li>";
			$counter++;
		}

		return $f."</ul>";
	}
		
	require("mod_generator/WFFGenerator.class.php");
		
	$cons = array();
	array_push($cons, new Connective("~",1,1));
	array_push($cons, new Connective("&",2,1));
	array_push($cons, new Connective("|",2,1));
	array_push($cons, new Connective("+",2,1));
	array_push($cons, new Connective("-->",2,1));
	array_push($cons, new Connective("<->",2,1));
	
	$g = new WFFGenerator(10,5,$cons);
	$ft = $g->getFormula();

	$_SESSION['formula'] = serialize($ft);
	
	echo ("<br/>".showFormulaUL($ft->root));
	
?>

	<div id="resultado">Resultado</div>

</body>
</html>

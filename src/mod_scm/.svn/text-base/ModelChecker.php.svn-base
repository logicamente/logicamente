<?php
	error_reporting(E_ALL);
	session_start();
?>

<html>
	<head>

		<style type="text/css">
			body {
				background-color: white;
				font-family: "sans serif";
				color: black;
				text-align: center;
			}
		</style>

		<title>Model Checker</title>

		<script src="jquery.js"> </script>
		<script src="json.js"> </script>

		<script type="text/javascript">

			$(document).ready(function() {
				function size(obj) {
					var sz = 0;
					for (var k in obj)
						++sz;
					return sz;
				}

				function setup_table(model) {
					$('#results_table').append(
						'<tr id="table_headers"></tr>'
					).append(
						'<tr id="table_subheaders"></tr>'
					);

					$('#table_headers').html(
						'<th rowspan="2" colspan="1">Modelo</th>'
					);

					var c_sz = size(model['_Constants']);
					var r_sz = size(model['_Relations']);
					var f_sz = size(model['_Functions']);

					if (c_sz > 0) {
						$('#table_headers').append("<th colspan=\""+c_sz+"\">Constantes</th>");
					}
					if (r_sz > 0) {
						$('#table_headers').append("<th colspan=\""+r_sz+"\">Relacoes</th>");
					}
					if (f_sz > 0) {
						$('#table_headers').append("<th colspan=\""+f_sz+"\">Funcoes</th>");
					}

					$('#table_headers').append("<th rowspan=\"2\" colspan=\"1\">Tamanho do universo</th>");

					for (var constant in model['_Constants']) {
						$('#table_subheaders').append('<th>' + constant + '</th>');
					}
					for (var relation in model['_Relations']) {
						$('#table_subheaders').append('<th>' + relation + '</th>');
					}
					for (var func in model['_Functions']) {
						$('#table_subheaders').append('<th>' + func + '</th>');
					}
				}

				function show_model(model) {
					if ($('#results_table').children().size() == 0)
						setup_table(model);

					$('tr:eq(1)').after('<tr id="model_row"></tr>');

					var sz = $('tr').size()-2;
					var mr = $('#model_row').filter(':first');
					
					mr.append('<td>'+sz+'</td>');
					for (var cst in model['_Constants']) {
						mr.append('<td>'+model['_Constants'][cst]+'</td>');
					}
					for (var rel in model['_Relations']) {
						mr.append('<td>'+model['_Relations'][rel]+'</td>');
					}
					for (var func in model['_Functions']) {
						mr.append('<td>'+model['_Functions'][func]+'</td>');
					}
					mr.append('<td>'+model['_UnivSize']+'</td>');
				}

				$('#submit').click(function() {
					$('#wait').fadeIn("fast");

					var formulae = new Array();

					$('#formula_set').children().each(function(i) {
						formulae.push(this.value);
					});

					$.post(
						'callbackGenerateNextModel.php',
						{ 'formula': JSON.stringify(formulae), 'univSize': $('#max_usize').val() },
						function(data) {
							$('#wait').fadeOut("fast");

							if (data == '[]') {
								alert('Nao existem mais modelos que satisfacam o conjunto de formulas.');
							}
							else {
								show_model(eval('('+data+')'));
							}
						}
					);

				});
	
				$('#clear').click(function() {
					$.post(
						'callbackGenerateNextModel.php',
						{ 'clear' : '1' },
						function(data) {
							$('#results_table').html('');
							alert('Limpou');
						}
					);
				});

				$('#add_formula').click(function() {
					$('#formula_set').append("<input id=\"formula\" type=\"text\"/>");
				});

				$('#rem_formula').click(function() {
					$('#formula').filter(':last').remove();
				});

				$('#wait').fadeOut("fast");
			});

		</script>
	</head>

	<body>

		<center><h2>Model Checker</h2></center>

		<center>Conjunto de formulas: <br/> </center>
			<div id="formula_set">
				<input id="formula" type="text"/>
			</div>
			<br/>
		<button id="add_formula">Adicionar formula</button>
		<button id="rem_formula">Remover formula</button>

		<center>Numero maximo de objetos nos modelos: <br/><input id="max_usize" type="text"> <br/><br/></center>

		<center><button id="submit">Gerar mais um modelo</button> <button id="clear">Reiniciar</button> <br/></center>

		<center><img id="wait" width=30 height=30 src="../images/loading2.gif"/></center>

		<br/>

		<center>
		<table id="results_table" border="1" align="center"> </table>
		</center>

	</body>
</html>

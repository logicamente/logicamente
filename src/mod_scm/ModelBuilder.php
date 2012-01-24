<?php
	error_reporting(E_ALL);

?>

<html>

	<head>

		<style type="text/css">
			body {
				background-color: white;
				font-family: "sans serif";
				color: black;
			}
			.drag {
				border: 2px solid #101010;
				background-color: #000000;
				width: 500px;
				height: 500px;
				margin: 10px;
			}
		</style>

		<title>Model Builder</title>

		<script src="jquery-latest.js"></script>
		<script src="jquery.form.js"></script>

		<script type="text/javascript">

			var arr_indice_tupla = {};
			var arr_indice_tupla_func = {};

			$(document).ready(function() {
				$('#gen_signature').click(function() {
					$('#model_config').html('');
					$('#result').html('');

					arr_indice_tupla = {};
					arr_indice_tupla_func = {};

					if ($('#formula').val() == '' ||
						$('#univ_size').val() == '')
						return alert('Dados invalidos');

					$.post(
						"callbackGenerateSignature.php",
						{
							'formula':$('#formula').val(),
							'univ_size':$('#univ_size').val()
						},
						function(data) {
							$('#model_config').append(data);
							if ($('#check').size() == 0) {
								$('#model_form').append("<br/><button id=\"check\" onclick=\"return false;\">Checar modelo</button>");

								$('#check').unbind().click(check_model_click);
							}
						}
					);
				});

			});

 			function check_model_click() {
				var str = "";
				
				str += 'formula='+$('#formula').val()+'&';
				str += 'univ_size='+$('#univ_size').val()+'&';
				str += $('#model_form').formSerialize();

				$.post(
					"callbackCheckModel.php",
					str,
					function(data) {
						$('#result').html('').append(data);
					}
				);
			}

			function add_tuple_rel(button) {
				if (isNaN(arr_indice_tupla[button.name])) {
					arr_indice_tupla[button.name] = 0;
				} else {
					arr_indice_tupla[button.name]++;
				}
				$.post(
					"callbackGenerateSignature.php",
					{
						'formula':$('#formula').val(),
						'univ_size':$('#univ_size').val(),
						'make_tuple_rel':1,
					 	'name_rel':button.name,
						'tuple_code':arr_indice_tupla[button.name]
					},
					function (data) {
						$('#' + button.name).append(data);
					}
				);
				return false;
			}

			function add_tuple_func(button) {
				if (isNaN(arr_indice_tupla_func[button.name])) {
					arr_indice_tupla_func[button.name] = 0;
				} else {
					arr_indice_tupla_func[button.name]++;
				}
				$.post(
					"callbackGenerateSignature.php",
					{
						'formula':$('#formula').val(),
						'univ_size':$('#univ_size').val(),
						'make_tuple_func':1,
					 	'name_func':button.name,
						'tuple_code':arr_indice_tupla_func[button.name]
					},
					function (data) {
						$('#' + button.name).append(data);
					}
				);
				return false;
			}

		</script>

	</head>

	<body>

		<center><h2>Model Builder</h2></center>

		<fieldset>
		<legend> <strong>Model Builder</strong> </legend>

		<br/>

 		<form id="model_form">

		Formula: <input id="formula" type="text" size="30"/>
		Tamanho do universo: <input id="univ_size" type="text" size=2 maxlength=2/>

		<button id="gen_signature" onclick="return false;">Gerar assinatura</button>

		<br/><br/>

		<div id="model_config"> </div>

		</form>

		<div id="result"> </div>

	</body>

</html>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Tester</title>
</head>

<body>

<H1> <center> Prenex Converter </center></H1><p>
<B><center>Módulo da Suite Logicamente - UFRN</center></B>
</p>

<form id="form1" name="form1" method="post" action="">
  <div align="center">
    <table width="177" border="1" bordercolor="#0000FF" bgcolor="#FFFFFF">
      <tr>
        <td width="74">Express&atilde;o 1a ordem:</td>
        <td width="87"><label>
          <input name="exp" type="text" id="exp" value = "<?php if (!empty($_POST['exp'])) echo $_POST['exp']?>" />
        </label></td>
      </tr>

      <tr>
        <td colspan="2"><label>
          <div align="center">
            <input type="submit" name="Submit" value="Estabilizar" />
          </div>
        </label></td>
      </tr>
      </table>
  </div>
  <label></label>
</form>

<!--Codigo para o php:-->
<?php
require_once("formulaConverter2.class.php");
require_once("PrenexConverter.class.php");

if (!empty($_POST['exp'])) {
	$exp = $_POST['exp'];

	echo "<br /><b>Fórmula antes: <br /></b>";
	echo $exp;
	echo "<br />";
		$tester = new formulaConverter("T","");
		$test = $tester->infixToTree($exp,true);//transformando formula em arvore
	echo "<br /><b>arvore antes: <br /></b>";
		$tester->printTree($test,"");

		$test2 = new CNF($test);// passando a arvore para uma instancia do skolemizer

	//echo "<br /><b>Fórmula Na forma normal: <br /></b>";
		//echo $tester->printFormula($test2->getCabeca(),"");//to passando apenas a arvore $test pois ela eh alterada por referencia na no objeto $test2
											 //alternativamente, poderia-se passar $test2->cabeca, que teria o mesmo efeito
}
?>
</body>
</html>

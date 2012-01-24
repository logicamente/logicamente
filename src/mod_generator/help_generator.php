<h1>Generator</h1>
<p>O m&oacute;dulo <strong>Formula Generator</strong> &eacute; respons&aacute;vel por gerar f&oacute;rmulas bem-formadas arbitr&aacute;rias de acordo com as especifica&ccedil;&otilde;es do usu&aacute;rio.</p>
<p>Inputs:</p>
<ul>
	<li><em>Number of Connectives</em> > 0 com n&uacute;mero de ocorr&icirc;ncias de conectivos</li>
	<li><em>Number of Atoms</em> > 0 com n&uacute;mero m&aacute;ximo de vari&aacute;veis at&ocirc;micas (com nomes entre p<sub>0</sub> e p<sub>n-1</sub>)</li>
	<li>Lista de Conectivos atribu&iacute;dos na aba <strong>Settings</strong></li>
</ul>

<p>Exemplos:</p>

<p><strong>(1) Number of Connectives = 5; Number of Atoms = 3; Connectives = {~, &, |, -->}</strong>

     <ul><li> (~~~p1 --> p0) --> p2</li>

      <li>p1 | (p2 & ((p0 & ~p1) & p2))</li>

      <li>((~~p0 | p2) -> p1) & p0</li>

      <li>p0 --> ((~(p0 | p0) | p0) --> p0)</li>

      <li><strong>~p1 & ((p1 | (p0 & p2)) | p2)</strong></li></ul></p>

<p><strong>(2) Number of Connectives = 3; Number of Atoms = 10; Connectives = {T, &, +, <->}</strong>

      <ul><li>p3 <-> ((p1 & p0) & p2)</li>

      <li>((p1 & p0) + T)</li>

      <li>p0 & ((p1 <-> p3) + p2)</li>

      <li>T <-> T</li>

      <li><strong>(p1 <-> (p0 + p2)) <-> p3</strong></li></ul></p>

<hr/>	  
<br/>
<p>Abaixo est&atilde;o listados os atalhos que podem ser usados neste site:</p>
	<ul>
		<li><strong>Ctrl+Alt+H</strong>: Exibe esta janela</li>
		<li><strong>Alt+Shift+S</strong>: Abre o m&oacute;dulo Settings</li>
		<li><strong>Alt+Shift+G</strong>: Abre o m&oacute;ulo Generator</li>
		<li><strong>Alt+Shift+F</strong>: Abre o m&oacute;dulo Formula Reader</li>
		<li><strong>Alt+Shift+T</strong>: Abre o m&oacute;dulo Truth Table</li>
        <li><strong>Alt+Shift+I</strong>: Abre o m&oacute;dulo Tree Interaction</li>
		<li><strong>Alt+Shift+R</strong>: Abre o m&oacute;dulo Resolution Game</li>
	</ul>
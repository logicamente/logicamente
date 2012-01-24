<form name="formTranslator" id="formTranslator">
	<fieldset>
		<legend id="translator">Translator</legend>				
		<fieldset>
			<legend>Formula Style</legend>
			<div class="field-widget">
				<label>Infix:      <input type="radio" name="style" id="infix" value="Infix" checked="checked"/></label>
				<label>Polish:     <input type="radio" name="style" id="polish" value="Polish" /></label>
				<label>Functional: <input type="radio" name="style" id="functional" value="Functional" /></label>						
			</div>
		</fieldset>
		<fieldset>
			<legend>Formula</legend>
			<input type="text" name="formula" id="formula" class="required"/>
		</fieldset>
		<input type="submit" id="btnTranslator" value="Translator" />
	</fieldset>
</form>

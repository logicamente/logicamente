<!-- <form name="formSettings" id="formSettings" action="#" method="post"> -->
<div style="padding-bottom:3px;">
    <fieldset>
        <legend>Formula Style</legend>
        <div class="field-widget">
            <label>Infix:      <input type="radio" name="style" id="infix" value="Infix" checked="checked"/></label>
            <label>Polish:     <input type="radio" name="style" id="polish" value="Polish" /></label>
            <label>Functional: <input type="radio" name="style" id="functional" value="Functional" /></label>						
        </div>
    </fieldset>

    <fieldset>
        <legend>Connectives</legend>
        <div id="connectives" class="field-widget">
            <div class="left" id="divBack">
                <div class="left">
                    <label for="unselected">Unselected</label>
                    <div class="field-widget">	
                        <select id="unselected" name="unselected" size="7" class="xT" multiple="multiple"> 
                                <option value="1&order[]=400">~</option>
                                <option value="2&order[]=350">& </option>
                                <option value="2&order[]=300">|</option>
                                
                                <option value="2&order[]=250">+</option>
                                <option value="2&order[]=250">--></option>								
                                <option value="2&order[]=250"><-></option>																																
                        </select>					
                    </div>
                </div>
                <div id="btBack">
                    <button name="back" id="back"><=</button>
                </div>
            </div>				
            <div class="right" id="divGo">
                <div id="btGo" class="left">
                    <button name="go" id="go">=></button>
                </div>
                <div class="right">
                    <label for="selected">Selected</label>					
                    <div class="field-widget">							
                        <select id="selected" name="selected[]" size="7" class="xT" multiple="multiple" class="required validate-selection"> 
                        </select>
                    </div>
                </div>				
            </div>
        </div>
    </fieldset>
    <button id="btnSet" name="btnSet">Set Settings</button>
    <button id="btnAddConnective" name="btnAddConnective">Add New Connective</button>
</div>
<!-- </form> -->
	
<fieldset id="newConnective">
	<legend>New Connective</legend>
	<form action="#" method="post" name="formNewConnective" id="formNewConnective">
		<div class="form-row">		
			<div class="field-label">
			  <label for="symbol">Symbol:</label>
			</div>
			<div class="field-widget">
				<input type="text" name="symbol" id="symbol" class="required"/>
			</div>
		</div>		
		
		<div class="form-row">		
			<div class="field-label">
			  <label for="arity">Arity:</label>
			</div>
			<div class="field-widget">
				<input type="text" name="arity" id="arity" class="required validate-number"/>
			</div>
		</div>	
				<div class="form-row">		
			<div class="field-label">
			  <label for="arity">Order:</label>
			</div>
			<div class="field-widget">
				<input type="text" name="order" id="order" class="required validate-number"/>
			</div>
		</div>	
		<input type="button" value="Add" id="btnNewCon" name="btnNewCon"/>
	</form>
</fieldset>
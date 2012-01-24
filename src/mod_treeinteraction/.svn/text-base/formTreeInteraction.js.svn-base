Event.observe('BTI', 'click', createTreeInteraction);

function createTreeInteraction() {
	var listMenu = $('transfer').getElementsByClassName('selected');
	var node = $A(listMenu);
	if (node.size() == 1) {
		var node = node[0];
		var pars = 'action=getTreeToInteract&formula=' + node.id;
		updateWindow('area', 'src/controller.php', pars);
		startTreeInteraction ();
	} else {
		var pars = 'action=getTreeToInteract&formula=-1';
		updateWindow('area', 'src/controller.php', pars);
	}
	$('area').style.display = "";
}

function startTreeInteraction (){
	
	alert("Generating Tree");
	
	jQuery.noConflict();

	var curr = "";

	jQuery('.connective').bind('click', function(){
		limpar();
		jQuery('.connective', this.parentNode).addClass('connectiveChild');
		jQuery('.atom', this.parentNode).addClass('atomChild');
		
		curr = jQuery(this).attr("id");
		jQuery(".connective", this.parentNode).each(function(){
			if (jQuery(this).attr("id") == curr)
				jQuery(this).addClass("connectiveSame");
		});
		jQuery(this).removeClass('connectiveSame');
		jQuery(this).addClass('connectiveActive');
	});
	
	jQuery('.atom').bind('click', function(){
		limpar();
		var aux = jQuery(this).html();
		jQuery('.atom').each(function(){
			if (jQuery(this).html() == aux)
				jQuery(this).addClass('atomSame');
		});
		jQuery(this).removeClass('atomSame');
		jQuery(this).addClass('atomActive');
		
	});
	
	jQuery(".connective, .atom").bind('click',function(){
		jQuery.post("src/mod_treeinteration/treeajax.php",
			{
				index: jQuery(this).attr("index")
			},
			//Descomente o "alert" para depura��o
			function(result){
				jQuery("#resultado").html(result);
			}
		);
	});	
}




/*


	jQuery(document).ready(function(){
									
		
	});
	
*/
	function limpar(){
		jQuery('.connective').removeClass('connectiveActive');
		jQuery('.connective').removeClass('connectiveChild');
		jQuery('.connective').removeClass('connectiveSame');
		jQuery('.atom').removeClass('atomActive');
		jQuery('.atom').removeClass('atomChild');
		jQuery('.atom').removeClass('atomSame');
	}

// $(document).ready(function(){ teste de javascript
// 	$('#l_diff_period').hide();
// });

$('#paralysed').submit(function (e){
	var ini = $('#ini_date_paralysed').val();
	var end = $('#end_date_paralysed').val();
		if (Date.parse(ini) > Date.parse(end)){
			$('#end_date_paralysed').popover({
				title : 'Erro: Data de fim anterior a de inicio',
				placement: 'bottom',
			});
			$('#end_date_paralysed').on('shown.bs.popover', function(){
				setTimeout(function(){
					$('#end_date_paralysed').popover('hide');
				}, 3000);
				})
			$('#end_date_paralysed').popover('show');
			e.preventDefault();	// previne que seja submetido os dados
			}else{
				var diff = (((Date.parse(end)-(Date.parse(ini)))/(24*60*60*1000)).toFixed(0));
				$('#diff_period').val(diff);
				// $('#diff_period').prop('type', 'text');
				// $('#l_diff_period').show();
		return true;
		}
});

$('#myTab a').click(function (e) {
  e.preventDefault();
  $(this).tab('show');
});

$(document).ready(function(){
	$(window).keydown(function(event){
    if(event.keyCode == 13) {
      event.preventDefault();
      return false;
    }
  });
	$('#plate').mask("aaa 9999");
	  $('.datepicker').datepicker({
    language: 'pt-BR',
    autoclose: true,
    format: 'dd/mm/yyyy'
  });
  $(".dateMask").mask("99/99/9999");
  $("#phone").mask("(99) 9999-9999"); 
  $(".hour").mask("99:99"); 
  $('.help').tooltip();
});

$('.consortium').change(function(){
	$.getJSON('/api/consortium-companies/id/'+this.value, { }, function(data){
		$('.consortium_company').empty();
		$('.consortium_company').append("<option value='0'> -- Selecione uma célula operacional -- </option>");
		$.each(data, function(index,item) {
			$('.consortium_company').append("<option value=" + item.id + ">" + item.name + "</option>");
		});
	});
});

$('#consortiumTransfer').change(function(){
	$.getJSON('/api/consortium-companies-name/id/'+this.value, { }, function(data){
		$('.consortium_company').empty();
		$('.consortium_company').append("<option value='0'> -- Selecione uma célula operacional -- </option>");
		$.each(data, function(index,item) {
			$('.consortium_company').append("<option value=" + item.id + ">" + item.name + "</option>");
		});
	});
});

$('#consotiumOption').change(function(){
	$.getJSON('/api/consortium-companies/id/'+this.value, { }, function(data){
		$('#cellOption').empty();
		$('#cellOption').append("<option value='0'> -- Selecione uma célula operacional -- </option>");
		$.each(data, function(index,item) {
			$('#cellOption').append("<option value=" + item.id + ">" + item.name + "</option>");
		});
	});
});

$("#consotiumOption").focusout(function(){
var teste2 = $("#consotiumOption option:selected").text(); 	
$('#consotiumName').val(teste2);	
})

$("#cellOption").focusout(function(){
var teste = $("#cellOption option:selected").text(); 	
$('#cellOptionName').val(teste);	
})

$('#submitMCO').click(function(){
	$("#barProgressMCO").show();
	$("#formMCO").hide();
});


function addHour(hour){
	console.log(hour);
	$('#qhour_'+hour).append('<div class="row"><input type="text" class="hourMask" name="qh_'+hour+'[]" id="qh_'+hour+'" style="width: 25px; text-align: center;" maxlength="2"></div>');
	// $('#qh_'+hour).focus();
	return false;
}

function openData(vehicle){
	if($('#vehicle_'+vehicle).hasClass('hide')){
		$('#vehicle_'+vehicle).removeClass('hide');
		$('#icon_'+vehicle).removeClass('glyphicon-folder-close');
		$('#icon_'+vehicle).addClass('glyphicon-folder-open');
	}
	else{
		$('#vehicle_'+vehicle).addClass('hide');
		$('#icon_'+vehicle).removeClass('glyphicon-folder-open');
		$('#icon_'+vehicle).addClass('glyphicon-folder-close');
	}
}

	$("#receiver").typeahead({

	source:function(query,process){
		var objects=[];
		map={};
		$.getJSON("/api/return-user", {query:query},function(data){
			$.each(data,function(i, object){
				map[object.label]=object;
				objects.push(object.label);
		});
			process(objects);
	});	

	},
	updater:function(item){

		$("#receiver_id").val(map[item].id);
		$('#receiver').attr('disabled',true);
        $('#removeReceiverNew').css('display','');
		return item;

	},

	matcher: function(item){

		if(item===null)
			return false;
		return ~item.toLowerCase().indexOf(this.query.toLowerCase());
	}	
}).on('typeahead:opened', function() {
    $(this).closest('.panel-body').css('overflow','visible');
}).on('typeahead:closed', function() {
    $(this).closest('.panel-body').css('overflow','hidden');
}); 

$('#removeReceiverNew').click(function(){
  $('#receiver_id').val('');
  $('#receiver').attr('disabled',false);
  $('#receiver').val('');
  $('#removeReceiverNew').hide();
});

$("#receiver_forw").typeahead({

	source:function(query,process){
		var objects=[];
		map={};
		$.getJSON("/api/return-user", {query:query},function(data){
			$.each(data,function(i, object){
				map[object.label]=object;
				objects.push(object.label);
		});
			process(objects);
	});	

	},
	updater:function(item){

		$("#receiver_id_forw").val(map[item].id);
		$('#receiver_forw').attr('disabled',true);
        $('#removeReceiverForw').css('display','');
		return item;

	},

	matcher: function(item){

		if(item===null)
			return false;
		return ~item.toLowerCase().indexOf(this.query.toLowerCase());
	}	
}).on('typeahead:opened', function() {
    $(this).closest('.panel-body').css('overflow','visible');
}).on('typeahead:closed', function() {
    $(this).closest('.panel-body').css('overflow','hidden');
}); 

$('#removeReceiverForw').click(function(){
  $('#receiver_id_forw').val('');
  $('#receiver_forw').attr('disabled',false);
  $('#receiver_forw').val('');
  $('#removeReceiverForw').hide();
});

function aux_resp_inbox(id,name){ 
	$('#aux').html(''); // limpa o modal
	$('#name_child').hide(); // esconde o nome do remetente
	$('#loadingmessage').show(); // mostra a figura do load enquanto carrega o ajax
	$('#resp').attr('disabled', true);
	if($('#row_'+id).hasClass('clida') == false || $("#collum_"+id).hasClass('lida') == false || $("#data_"+id).hasClass('lida') == false || $("#dat_"+id).hasClass('lida') == false )
	{
	window.parent.document.getElementById("row_"+id).setAttribute("class","clida");
	window.parent.document.getElementById("collum_"+id).setAttribute("class","lida");
	window.parent.document.getElementById("data_"+id).setAttribute("class","lida");
	window.parent.document.getElementById("dat_"+id).setAttribute("class","lida");
	}
	$.ajax({
		url: '/mail/parent',
		type: 'POST',
		data: { parent: id },
		success: function(data){
			var foo = $.parseJSON(data);
			var aux_date = [];
			$( document ).ready(function() {
			$.each(foo, function(i, aux){ 
			if(aux.date_received_aux != null){
			aux_date.push(aux.date_received_aux);
			}
			else aux_date.push("Menssagem não foi lida");
			$("<div class='panel-group' id='accordion'>" +
					"<div class='panel panel-default'>" +
						"<div class='panel-heading' " +
							"<a id='title_message_"+foo[i].id+"'class='panel-title' data-toggle='collapse' data-parent='#accordion'" + 
								"href='#body_resp_"+foo[i].id +"'>"+ foo[i].title +"&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"+ foo[i].name +"&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"+aux_date[i]+"" +
							"</a>" +
						"</div>" +
					"</div>" + 
					"<div id='body_resp_"+foo[i].id +"' class='panel-collapse collapse'>" +
						"<div id='child_body' class='panel-body'>"+
							"<div class='col-md-8'>"+
								foo[i].body+
						  "</div>"+
							"<div class='col-md-4'>"+	
								"<button onclick=\"forwarding("+"'"+ foo[i].body+"','"+foo[i].title+"','"+name+"','"+foo[i].date+"','"+foo[i].id+"','"+
										foo[i].annex+"','"+foo[i].sender+"')\" type='button' class='btn btn-danger btn-xs conteiner'"+
									"href='#myModal_4' id='message_"+foo[i].id +"'data-toggle='modal'>Encaminhar</button>"+	
							"</div>" +
						"</div>" +
						(foo[i].annex !== null ? "<hr>Anexo:   <a id='get_annex_"+foo[i].id+
										"' target='_blank' href='/mail/download/id/"+
										foo[i].id+"/name/"+foo[i].annex+"'"+">"+foo[i].annex+"</a><hr>" : '') +
					"</div>" +
				"</div>").hide().appendTo('#aux').show('slow');
			var tut = foo[i].parent;   // manda pro footer do modal as informações necessarias para responder a mensagem.
			var tot = foo[i].id;
			var tit = foo[i].sender;
			var tet = foo[0].date;
			$('#parent_id_aux').val(tut);
			$('#last_child_id').val(tot);
			$('#sender_id_resp').val(tit);
			$('#name_child').html(name +"&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"+ tet);
			$('#name_child_resp').html(name);
			$('#title_message_aux').val(foo[i].title);
			$('#loadingmessage').hide(); // esconde a figura do load
			$('#name_child').show(); //mostra o nome do remetente
			$('#resp').attr('disabled', false); // ativa o botão de resposta
			})});
		}

	});
	
};

function aux_resp_outbox(id,name){ 
$('#aux').html(''); // limpa o modal
	$('#name_child').hide(); // esconde o nome do remetente
	$('#loadingmessage').show(); // mostra a figura do load enquanto carrega o ajax
	$('#resp').attr('disabled', true);
	$.ajax({
		url: '/mail/parent-out',
		type: 'POST',
		data: { parent: id },
		success: function(data){
			var foo = $.parseJSON(data);
			var aux_date = [];
			$( document ).ready(function() {
			$.each(foo, function(i, aux){ 
			if(aux.date_received_aux != null){
			aux_date.push(aux.date_received_aux);
			}
			else aux_date.push("Menssagem não foi lida");
			$("<div class='panel-group' id='accordion'>" +
					"<div class='panel panel-default'>" +
						"<div class='panel-heading' " +
							"<a id='title_message_"+foo[i].id+"'class='panel-title' data-toggle='collapse' data-parent='#accordion'" + 
								"href='#body_resp_"+foo[i].id +"'>"+ foo[i].title +"&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"+ foo[i].name +"&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"+aux_date[i]+"" +
							"</a>" +
						"</div>" +
					"</div>" + 
					"<div id='body_resp_"+foo[i].id +"' class='panel-collapse collapse'>" +
						"<div id='child_body' class='panel-body'>"+
							"<div class='col-md-8'>"+
								foo[i].body+
						  "</div>"+
							"<div class='col-md-4'>"+	
								"<button onclick=\"forwarding("+"'"+ foo[i].body+"','"+foo[i].title+"','"+name+"','"+foo[i].date+"','"+foo[i].id+"','"+
										foo[i].annex+"','"+foo[i].sender+"')\" type='button' class='btn btn-danger btn-xs conteiner'"+
									"href='#myModal_4' id='message_"+foo[i].id +"'data-toggle='modal'>Encaminhar</button>"+	
							"</div>" +
						"</div>" +
						(foo[i].annex !== null ? "<hr>Anexo:   <a id='get_annex_"+foo[i].id+
										"' target='_blank' href='/mail/download/id/"+
										foo[i].id+"/name/"+foo[i].annex+"'"+">"+foo[i].annex+"</a><hr>" : '') +
					"</div>" +
				"</div>").hide().appendTo('#aux').show('slow');
			var tut = foo[i].parent;   // manda pro footer do modal as informações necessarias para responder a mensagem.
			var tot = foo[i].id;
			var tit = foo[i].sender;
			var tet = foo[0].date;
			$('#parent_id_aux').val(tut);
			$('#last_child_id').val(tot);
			$('#sender_id_resp').val(tit);
			$('#name_child').html(name +"&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"+ tet);
			$('#name_child_resp').html(name);
			$('#title_message_aux').val(foo[i].title);
			$('#loadingmessage').hide(); // esconde a figura do load
			$('#name_child').show(); //mostra o nome do remetente
			$('#resp').attr('disabled', false); // ativa o botão de resposta
			})});
		}

	});
	
};

function fetch_resp(){
		var name = $('#name_child_resp').text();
		var title = $('#title_message_aux').val()
		var parent_id = $('#parent_id_aux').val()
		var last_child_id = $('#last_child_id').val()
		var sender_id_resp = $('#sender_id_resp').val()
		$('#sender').val(name);
		$('#title_ref').val('Re: '+ title);
		$('#sender').attr('disabled',true);
		$('#receiver_id_answer').val(sender_id_resp);
		console.log($("#receiver_id_answer").val());
		if (parent_id == '')
		{
			$("#parent_id").val(last_child_id);
		}
		else
		{
			$("#parent_id").val(parent_id);
		}
};

function forwarding(body, title, name, date, id, annex){
		$('#message_forw').html("");
		$('#title_forw').val('Fwd: '+title);
		$('#body_forw').val("\n\n\n"+"----------------------"+
			"Mensagem Encaminhada"+"-----------------------"+"\n"+"Usuário da mensagem: "+name+"\n"+
			"Enviado na data: "+date+"\n"+"Mensagem original: "+body+"\n");
		console.log(annex);
		if (annex !=  'null')
		{ 
			$('#message_forw').append("<a id='get_annex_forw"+id+"' href='/mail/download/id/"+id+"/name/"+annex+"'"+">"+annex+"</a>")
		}
		if (annex !=  'null')
		{  
		$('#annex_forw').val(annex);
		}
		else
		{
			$('#annex_forw').val(null);
		}
		$('#forwarded_message_id').val(id);
		$('#parent_null').val('');

};

	$("#myModal_1").on('shown.bs.modal', function(){ // Coloca o foco no Destinatario do modal 1
		$("#receiver").focus();
	});

	$("#myModal_4").on('shown.bs.modal', function(){ // Coloca o foco no Destinatario do modal 4
		$("#receiver_forw").focus();
	});

$('#receiver_forw').keyup(function(){
	if(($('#body_forw').val().length == 0) || ($('#title_forw').val().length == 0) || ($('#receiver_forw').val().length == 0))
		{
			$("#submit_forw").attr('disabled','disabled');
		}
		else
		{
			$("#submit_forw").removeAttr('disabled');	
		}
});


$('.resp').keyup(function(){
if(($('#body_resp').val().length == 0) || ($('#title_ref').val().length == 0))
		{
			$("#submit").attr('disabled','disabled');
		}
		else
		{
			$("#submit").removeAttr('disabled');	
		}
});

$('.new').keyup(function(){
if(($('#body').val().length == 0) || ($('#title').val().length == 0) || ($('#receiver').val().length == 0))
		{
			$("#submit_new").attr('disabled','disabled');
		}
		else
		{
			$("#submit_new").removeAttr('disabled');	
		}
});


$('#annex_btn').click(function(){
	$('#annex_btn').hide('slow');
	$('#cancel_btn').show('slow');
});

$('#cancel_btn').click(function(){
	$('#annex_btn').show('slow');
});

$('#delete').click(function(){
	$('#deleteMCO').hide();
	$('#statusDelete').css('display','block');
});

$('#delete_calendar').click(function(){
	$('#deleteCalendar').hide();
	$('#statusDelete').css('display','block');
});

/**  VALIDATING VEHICLE FORM **/
$('#service').change(function(){
	if(this.value > 0) {
		$('#form-service').addClass('has-success');
		$('#form-service').addClass('has-feedback');
	} else {
		$('#form-service').removeClass('has-success');
		$('#form-service').removeClass('has-feedback');
	}
});

$('#plate').keyup(function(){
	$('#feedback-success-plate').css('color','#468847');
	$('#feedback-error-plate').css('color','#b94a48');
	if(this.value.indexOf('_') === -1) {
		$('#form-plate').addClass('has-success');
		$('#form-plate').addClass('has-feedback');
		$('#form-plate').removeClass('has-error');
		$('#feedback-success-plate').removeClass('hide');
		$('#feedback-error-plate').addClass('hide');
	} else if(this.value.indexOf('_')) {
		$('#form-plate').addClass('has-error');
		$('#form-plate').addClass('has-feedback');
		$('#form-plate').removeClass('has-success');
		$('#feedback-success-plate').addClass('hide');
		$('#feedback-error-plate').removeClass('hide');
	} else {
		$('#form-plate').removeClass('has-success');
		$('#form-plate').removeClass('has-feedback');
		$('#form-plate').removeClass('has-error');
		$('#feedback-success-plate').addClass('hide');
		$('#feedback-error-plate').addClass('hide');
	}
});


$('#renavam').keyup(function(){
	$('#feedback-success-renavam').css('color','#468847');
	$('#feedback-error-renavam').css('color','#b94a48');
	if(this.value.length > 7 && $.isNumeric(this.value)) {
		$('#form-renavam').addClass('has-success');
		$('#form-renavam').addClass('has-feedback');
		$('#form-renavam').removeClass('has-error');
		$('#feedback-success-renavam').removeClass('hide');
		$('#feedback-error-renavam').addClass('hide');
	} else if( (this.value.length > 1 && this.value.length < 8) || (!$.isNumeric(this.value))) {
		$('#form-renavam').addClass('has-error');
		$('#form-renavam').addClass('has-feedback');
		$('#form-renavam').removeClass('has-success');
		$('#feedback-success-renavam').addClass('hide');
		$('#feedback-error-renavam').removeClass('hide');
	} else {
		$('#form-renavam').removeClass('has-error');
		$('#form-renavam').removeClass('has-success');
		$('#form-renavam').removeClass('has-feedback');
		$('#feedback-success-renavam').addClass('hide');
		$('#feedback-error-renavam').addClass('hide');
	}
});


$('#external-number').keyup(function(){
	$('#feedback-success-external-number').css('color','#468847');
	$('#feedback-error-external-number').css('color','#b94a48');
	if(this.value.length > 4 && $.isNumeric(this.value)) {
		$('#form-external-number').addClass('has-success');
		$('#form-external-number').addClass('has-feedback');
		$('#form-external-number').removeClass('has-error');
		$('#feedback-success-external-number').removeClass('hide');
		$('#feedback-error-external-number').addClass('hide');
	} else if( (this.value.length > 1 && this.value.length < 5) || (!$.isNumeric(this.value))) {
		$('#form-external-number').addClass('has-error');
		$('#form-external-number').addClass('has-feedback');
		$('#form-external-number').removeClass('has-success');
		$('#feedback-success-external-number').addClass('hide');
		$('#feedback-error-external-number').removeClass('hide');
	} else {
		$('#form-external-number').removeClass('has-error');
		$('#form-external-number').removeClass('has-success');
		$('#form-external-number').removeClass('has-feedback');
		$('#feedback-success-external-number').addClass('hide');
		$('#feedback-error-external-number').addClass('hide');
	}
});

$('#consortium').change(function(){
	if(this.value > 0) {
		$('#form-consortium').addClass('has-success');
		$('#form-consortium').addClass('has-feedback');
	} else {
		$('#form-consortium').removeClass('has-success');
		$('#form-consortium').removeClass('has-feedback');
	}
});

$('#pattern').change(function(){
	if(this.value > 0) {
		$('#form-pattern').addClass('has-success');
		$('#form-pattern').addClass('has-feedback');
	} else {
		$('#form-pattern').removeClass('has-success');
		$('#form-pattern').removeClass('has-feedback');
	}
});

$('#color').change(function(){
	if(this.value > 0) {
		$('#form-color').addClass('has-success');
		$('#form-color').addClass('has-feedback');
	} else {
		$('#form-color').removeClass('has-success');
		$('#form-color').removeClass('has-feedback');
	}
});

$('#type').change(function(){
	if(this.value > 0) {
		$('#form-type').addClass('has-success');
		$('#form-type').addClass('has-feedback');
	} else {
		$('#form-type').removeClass('has-success');
		$('#form-type').removeClass('has-feedback');
	}
});


$('#line').keyup(function(){
	$('#feedback-success-line').css('color','#468847');
	$('#feedback-error-line').css('color','#b94a48');
	if(this.value.length == 4 && $.isNumeric(this.value)) {
		$('#form-line').addClass('has-success');
		$('#form-line').addClass('has-feedback');
		$('#form-line').removeClass('has-error');
		$('#feedback-success-line').removeClass('hide');
		$('#feedback-error-line').addClass('hide');
	} else{
		$('#form-line').addClass('has-error');
		$('#form-line').addClass('has-feedback');
		$('#form-line').removeClass('has-success');
		$('#feedback-success-line').addClass('hide');
		$('#feedback-error-line').removeClass('hide');
	} 
});

$('#craft').keyup(function(){
	$('#feedback-success-craft').css('color','#468847');
	$('#feedback-error-craft').css('color','#b94a48');
	if(this.value.length > 6) {
		$('#form-craft').addClass('has-success');
		$('#form-craft').addClass('has-feedback');
		$('#form-craft').removeClass('has-error');
		$('#feedback-success-craft').removeClass('hide');
		$('#feedback-error-craft').addClass('hide');
	} else{
		$('#form-craft').addClass('has-error');
		$('#form-craft').addClass('has-feedback');
		$('#form-craft').removeClass('has-success');
		$('#feedback-success-craft').addClass('hide');
		$('#feedback-error-craft').removeClass('hide');
	} 
});

$('#vehicle_number').keyup(function(){
	$('#feedback-success-vehicle-number').css('color','#468847');
	$('#feedback-error-vehicle-number').css('color','#b94a48');
	if(this.value.length > 4 && $.isNumeric(this.value)) {
		$('#form-vehicle-number').addClass('has-success');
		$('#form-vehicle-number').addClass('has-feedback');
		$('#form-vehicle-number').removeClass('has-error');
		$('#feedback-success-vehicle-number').removeClass('hide');
		$('#feedback-error-vehicle-number').addClass('hide');
	} else if( (this.value.length > 0 && this.value.length < 5) || (!$.isNumeric(this.value))) {
		$('#form-vehicle-number').addClass('has-error');
		$('#form-vehicle-number').addClass('has-feedback');
		$('#form-vehicle-number').removeClass('has-success');
		$('#feedback-success-vehicle-number').addClass('hide');
		$('#feedback-error-vehicle-number').removeClass('hide');
	} else {
		$('#form-vehicle-number').removeClass('has-error');
		$('#form-vehicle-number').removeClass('has-success');
		$('#form-vehicle-number').removeClass('has-feedback');
		$('#feedback-success-vehicle-number').addClass('hide');
		$('#feedback-error-vehicle-number').addClass('hide');
	}
});

$('#start_roulette').keyup(function(){
	$('#feedback-success-start-roulette').css('color','#468847');
	$('#feedback-error-start-roulette').css('color','#b94a48');
	if(this.value.length == 5 && $.isNumeric(this.value)) {
		$('#form-start-roulette').addClass('has-success');
		$('#form-start-roulette').addClass('has-feedback');
		$('#form-start-roulette').removeClass('has-error');
		$('#feedback-success-start-roulette').removeClass('hide');
		$('#feedback-error-start-roulette').addClass('hide');
	} else {
		$('#form-start-roulette').addClass('has-error');
		$('#form-start-roulette').addClass('has-feedback');
		$('#form-start-roulette').removeClass('has-success');
		$('#feedback-success-start-roulette').addClass('hide');
		$('#feedback-error-start-roulette').removeClass('hide');
	}
});

$('#mid_roulette').keyup(function(){
	$('#feedback-success-mid-roulette').css('color','#468847');
	$('#feedback-error-mid-roulette').css('color','#b94a48');
	if(this.value.length == 5 && $.isNumeric(this.value)) {
		$('#form-mid-roulette').addClass('has-success');
		$('#form-mid-roulette').addClass('has-feedback');
		$('#form-mid-roulette').removeClass('has-error');
		$('#feedback-success-mid-roulette').removeClass('hide');
		$('#feedback-error-mid-roulette').addClass('hide');
	} else{
		$('#form-mid-roulette').addClass('has-error');
		$('#form-mid-roulette').addClass('has-feedback');
		$('#form-mid-roulette').removeClass('has-success');
		$('#feedback-success-mid-roulette').addClass('hide');
		$('#feedback-error-mid-roulette').removeClass('hide');
	}
});

$('#end_roulette').keyup(function(){
	$('#feedback-success-end-roulette').css('color','#468847');
	$('#feedback-error-end-roulette').css('color','#b94a48');
	if(this.value.length == 5 && $.isNumeric(this.value)) {
		$('#form-end-roulette').addClass('has-success');
		$('#form-end-roulette').addClass('has-feedback');
		$('#form-end-roulette').removeClass('has-error');
		$('#feedback-success-end-roulette').removeClass('hide');
		$('#feedback-error-end-roulette').addClass('hide');
	} else{
		$('#form-end-roulette').addClass('has-error');
		$('#form-end-roulette').addClass('has-feedback');
		$('#form-end-roulette').removeClass('has-success');
		$('#feedback-success-end-roulette').addClass('hide');
		$('#feedback-error-end-roulette').removeClass('hide');
	}
});

$('#start_hour').keyup(function(){
	$('#feedback-success-start-hour').css('color','#468847');
	$('#feedback-error-start-hour').css('color','#b94a48');
	if(this.value.indexOf('_') == -1) {
		$('#form-start-hour').addClass('has-success');
		$('#form-start-hour').addClass('has-feedback');
		$('#form-start-hour').removeClass('has-error');
		$('#feedback-success-start-hour').removeClass('hide');
		$('#feedback-error-start-hour').addClass('hide');
	} else if(this.value.indexOf('_')) {
		$('#form-start-hour').addClass('has-error');
		$('#form-start-hour').addClass('has-feedback');
		$('#form-start-hour').removeClass('has-success');
		$('#feedback-success-start-hour').addClass('hide');
		$('#feedback-error-start-hour').removeClass('hide');
	} else {
		$('#form-start-hour').removeClass('has-success');
		$('#form-start-hour').removeClass('has-feedback');
		$('#form-start-hour').removeClass('has-error');
		$('#feedback-success-start-hour').addClass('hide');
		$('#feedback-error-start-hour').addClass('hide');
	}
});

$('#mid_hour').keyup(function(){
	$('#feedback-success-mid-hour').css('color','#468847');
	$('#feedback-error-mid-hour').css('color','#b94a48');
	if(this.value.indexOf('_') == -1) {
		$('#form-mid-hour').addClass('has-success');
		$('#form-mid-hour').addClass('has-feedback');
		$('#form-mid-hour').removeClass('has-error');
		$('#feedback-success-mid-hour').removeClass('hide');
		$('#feedback-error-mid-hour').addClass('hide');
	} else if(this.value.indexOf('_')) {
		$('#form-mid-hour').addClass('has-error');
		$('#form-mid-hour').addClass('has-feedback');
		$('#form-mid-hour').removeClass('has-success');
		$('#feedback-success-mid-hour').addClass('hide');
		$('#feedback-error-mid-hour').removeClass('hide');
	} else {
		$('#form-mid-hour').removeClass('has-success');
		$('#form-mid-hour').removeClass('has-feedback');
		$('#form-mid-hour').removeClass('has-error');
		$('#feedback-success-mid-hour').addClass('hide');
		$('#feedback-error-mid-hour').addClass('hide');
	}
});

$('#end_hour').keyup(function(){
	$('#feedback-success-end-hour').css('color','#468847');
	$('#feedback-error-end-hour').css('color','#b94a48');
	if(this.value.indexOf('_') == -1) {
		$('#form-end-hour').addClass('has-success');
		$('#form-end-hour').addClass('has-feedback');
		$('#form-end-hour').removeClass('has-error');
		$('#feedback-success-end-hour').removeClass('hide');
		$('#feedback-error-end-hour').addClass('hide');
	} else if(this.value.indexOf('_')) {
		$('#form-end-hour').addClass('has-error');
		$('#form-end-hour').addClass('has-feedback');
		$('#form-end-hour').removeClass('has-success');
		$('#feedback-success-end-hour').addClass('hide');
		$('#feedback-error-end-hour').removeClass('hide');
	} else {
		$('#form-end-hour').removeClass('has-success');
		$('#form-end-hour').removeClass('has-feedback');
		$('#form-end-hour').removeClass('has-error');
		$('#feedback-success-end-hour').addClass('hide');
		$('#feedback-error-end-hour').addClass('hide');
	}
});

$('#start_date').keyup(function(){
	$('#feedback-success-start-date').css('color','#468847');
	$('#feedback-error-start-date').css('color','#b94a48');
	if(this.value.indexOf('_') == -1) {
		$('#form-start-date').addClass('has-success');
		$('#form-start-date').addClass('has-feedback');
		$('#form-start-date').removeClass('has-error');
		$('#feedback-success-start-date').removeClass('hide');
		$('#feedback-error-start-date').addClass('hide');
	} else if(this.value.indexOf('_')) {
		$('#form-start-date').addClass('has-error');
		$('#form-start-date').addClass('has-feedback');
		$('#form-start-date').removeClass('has-success');
		$('#feedback-success-start-date').addClass('hide');
		$('#feedback-error-start-date').removeClass('hide');
	} else {
		$('#form-start-date').removeClass('has-success');
		$('#form-start-date').removeClass('has-feedback');
		$('#form-start-date').removeClass('has-error');
		$('#feedback-success-start-date').addClass('hide');
		$('#feedback-error-start-date').addClass('hide');
	}
});

$('#mid_date').keyup(function(){
	$('#feedback-success-mid-date').css('color','#468847');
	$('#feedback-error-mid-date').css('color','#b94a48');
	if(this.value.indexOf('_') == -1) {
		$('#form-mid-date').addClass('has-success');
		$('#form-mid-date').addClass('has-feedback');
		$('#form-mid-date').removeClass('has-error');
		$('#feedback-success-mid-date').removeClass('hide');
		$('#feedback-error-mid-date').addClass('hide');
	} else if(this.value.indexOf('_')) {
		$('#form-mid-date').addClass('has-error');
		$('#form-mid-date').addClass('has-feedback');
		$('#form-mid-date').removeClass('has-success');
		$('#feedback-success-mid-date').addClass('hide');
		$('#feedback-error-mid-date').removeClass('hide');
	} else {
		$('#form-mid-date').removeClass('has-success');
		$('#form-mid-date').removeClass('has-feedback');
		$('#form-mid-date').removeClass('has-error');
		$('#feedback-success-mid-date').addClass('hide');
		$('#feedback-error-mid-date').addClass('hide');
	}
});

$('#end_date').keyup(function(){
	$('#feedback-success-end-date').css('color','#468847');
	$('#feedback-error-end-date').css('color','#b94a48');
	if(this.value.indexOf('_') == -1) {
		$('#form-end-date').addClass('has-success');
		$('#form-end-date').addClass('has-feedback');
		$('#form-end-date').removeClass('has-error');
		$('#feedback-success-end-date').removeClass('hide');
		$('#feedback-error-end-date').addClass('hide');
	} else if(this.value.indexOf('_')) {
		$('#form-end-date').addClass('has-error');
		$('#form-end-date').addClass('has-feedback');
		$('#form-end-date').removeClass('has-success');
		$('#feedback-success-end-date').addClass('hide');
		$('#feedback-error-end-date').removeClass('hide');
	} else {
		$('#form-end-date').removeClass('has-success');
		$('#form-end-date').removeClass('has-feedback');
		$('#form-end-date').removeClass('has-error');
		$('#feedback-success-end-date').addClass('hide');
		$('#feedback-error-end-date').addClass('hide');
	}
});


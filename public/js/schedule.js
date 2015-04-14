
$(document).ready(function() {

	$.getJSON("/scheduling/return-all-events", function(data) {
		
		var events = [];
		$.each(data, function(error, value) { 
			var date = value.date.split('-');
			var obj = {
				'year': date[0],
				'month': date[1],
				'day': date[2]
			};
			events.push(obj);
		});

		var datepicker = $('#calendar div').datepicker({
			todayBtn: true,
			language: "PT-BR",
			todayHighlight: true,
			endDate: "+3m",
			daysOfWeekDisabled: "0,6",
			startDate: new Date(),
			beforeShowDay: function(date){
				for(var i=0;i<events.length;i++){
					if(date.getFullYear() == events[i].year){
						if(date.getMonth()+1 == events[i].month){
							if(date.getDate() == events[i].day){
								return false;
							}
						}
					}
				}
			}
		}).on('changeDate', function(ev) {
			var dateCur = new Date(ev.date);
			var month = dateCur.getMonth()+1;
			var en_Date = dateCur.getFullYear() + '-' + month + '-' + dateCur.getDate();
			var date = {'date': en_Date};
			$.ajax({
				url: '/scheduling/return-events',
				method: 'post',
				data: date, 
				success: function(result) {
					if(result.length) {
						$.each(result, function(error, data) {
							$("select#hour option").filter("[value='"+data.hour+"']").remove();
						});
					}
					$('#formSchedule').removeClass('hide');
					$('#date').val(en_Date);
				}
			})
		});
	});

});


	function fncReschedule(id) {
		$('#reschedule').removeClass('hide');
		$('#id').val(id);
	}

	// SCHEDULING
	$('#user_type').change(function() {
		if(this.value == 2) {
			$('#type_scheduling option[value="1"]').remove();
			$('#type_scheduling option[value="2"]').remove();
			$('#type_scheduling option[value="3"]').remove();
			$('#type_scheduling option[value="4"]').remove();
			$('#type_scheduling option[value="7"]').remove();
			$('#type_scheduling option[value="9"]').remove();
			$('#type_scheduling option[value="10"]').remove();
		} else {
			$('#type_scheduling option[value="1"]').remove();
			$('#type_scheduling option[value="2"]').remove();
			$('#type_scheduling option[value="3"]').remove();
			$('#type_scheduling option[value="4"]').remove();
			$('#type_scheduling option[value="5"]').remove();
			$('#type_scheduling option[value="6"]').remove();
			$('#type_scheduling option[value="7"]').remove();
			$('#type_scheduling option[value="8"]').remove();
			$('#type_scheduling option[value="9"]').remove();
			$('#type_scheduling option[value="10"]').remove();
			$('#type_scheduling option[value="11"]').remove();
			$('#type_scheduling').append('<option value="11">Atendimento geral</option>');
			$('#type_scheduling').append('<option value="2">Baixa de auxiliar</option>');
			$('#type_scheduling').append('<option value="1">Cadastro de auxiliar</option>');
			$('#type_scheduling').append('<option value="4">Conclusão do emplacamento</option>');
			$('#type_scheduling').append('<option value="5">Emissão de carteira</option>');
			$('#type_scheduling').append('<option value="6">Entrega de certidão</option>');
			$('#type_scheduling').append('<option value="10">Início de emplacamento</option>');
			$('#type_scheduling').append('<option value="7">Permuta de veículos</option>');
			$('#type_scheduling').append('<option value="3">Reserva de permissão</option>');
			$('#type_scheduling').append('<option value="8">Segunda via de documentos</option>');
			$('#type_scheduling').append('<option value="9">Transferência de permissão</option>');
		}
	});



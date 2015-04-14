var language = {
	error_noview: 'Calendario: vista {0} non trovata',
	error_dateformat: 'Calendario: formato data {0} non valido. Dovrebbe essere "now" o "yyyy-mm-dd"',
	error_loadurl: 'Calendario: URL di caricamento degli eventi non impostato',
	error_where: 'Calendario: direzione di spostamento {0} non valida. I valori validi sono "next" o "prev" o "today"',

	title_year: 'Ano {0}',
	title_month: '{0} {1}',
	title_week: 'Semana {0} de {1}',
	title_day: '{0} {1} {2} {3}',

	week:'Semana',

	m0: 'Janeiro',
	m1: 'Fevereiro',
	m2: 'Março',
	m3: 'Abril',
	m4: 'Mario',
	m5: 'Junho',
	m6: 'Julho',
	m7: 'Agosto',
	m8: 'Setembro',
	m9: 'Outubro',
	m10: 'Novembro',
	m11: 'Dezembro',

	ms0: 'Jan',
	ms1: 'Fev',
	ms2: 'Mar',
	ms3: 'Abr',
	ms4: 'Mar',
	ms5: 'Jul',
	ms6: 'Jun',
	ms7: 'Ago',
	ms8: 'Set',
	ms9: 'Out',
	ms10: 'Nov',
	ms11: 'Dez',

	d0: 'Domingo',
	d1: 'Segunda',
	d2: 'Terça',
	d3: 'Quarta',
	d4: 'Quinta',
	d5: 'Sexta',
	d6: 'Sabado',

	easter: 'Pasqua',
	easterMonday: 'Lunedì dell’Angelo'
};

if(!String.prototype.format) {
	String.prototype.format = function() {
		var args = arguments;
		return this.replace(/{(\d+)}/g, function(match, number) {
			return typeof args[number] != 'undefined' ? args[number] : match;
		});
	};
}
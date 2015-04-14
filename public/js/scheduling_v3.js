function dateFromISO(s) {
  s = s.split(/\D/);
  return new Date(Date.UTC(s[0], --s[1]||'',s[2]||'',s[3]||'',s[4]||'',s[5]||'',s[6]||''))
}

$(document).ready(function() {
    $.getJSON('/scheduling/return-schedulings', { }, function(data) {
  	var events = [];
  	$.each(data, function( index, value ) {
      var myDate = new Date(dateFromISO(value.start));
      myDate.setHours(myDate.getHours() + 2);

  		var aux = {
  			title : value.title,
  			start : myDate,
  			allDay: false
  		}
		  events.push(aux);
		});
  	$('#calendar').fullCalendar({
			editable: false,
			events: events
		});
  });
});

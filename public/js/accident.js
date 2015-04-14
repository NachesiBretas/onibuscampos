

	d3.json('/api/return-accidents', function(error, accidents) {
		if(error) return console.warn(error);


		var margin = {top: 20, right: 40, bottom: 30, left: 40},
		    width = 960 - margin.left - margin.right,
		    height = 300 - margin.top - margin.bottom;

		var y = d3.scale.linear()
			.domain([0,d3.max(accidents,function(data){return parseInt(data.amount);})])
		    .range([height, 0]);

		var yAxis = d3.svg.axis()
			.scale(y)
        	.orient("left");
		
		window.svg = d3.select("#chart").append("svg")
		    .attr("width", width + margin.left + margin.right)
		    .attr("height", height + margin.top + margin.bottom);

    var bar = svg.selectAll("g")
				.data(accidents)
			.enter().append("g")
		    .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

		svg.append("g")
			.attr("class", "axis")
			.attr("transform", "translate("+margin.left+",20)")
    	.call(yAxis);

    bar.append("rect")
      .attr("stroke", "black")
      .attr("stroke-width", 1)
      .style("fill", "steelblue")
      .attr("x", function(d,i) { return (i*50)+20; })
      .attr("width", 40 )
      .attr("y", function(d) { return y(d.amount); })
      .attr("height", function(d) { return (height - y(d.amount)); });

  	bar.append("text")
  	 	.attr('class','label')
    	.attr("x", function(d,i) { return (i*50)+45; })
    	.attr("y", function(d) { return y(d.amount-3); })
    	.attr("dy", ".35em")
    	.text(function(d) { return d.amount; });

    bar.append("text")
  	 	.attr('class','label labelrit')
    	.attr("x", function(d,i) { return (i*50)+50; })
    	.attr("y", function(d) { return height+10; })
    	.attr("dy", ".35em")
    	.text(function(d) { return 'RIT '+d.rit; });

	});

d3.json('/api/return-accidents-city', function(error, accidents) {
		// if(error) return console.warn(error);


		// var margin = {top: 20, right: 40, bottom: 30, left: 40},
		//     width = 960 - margin.left - margin.right,
		//     height = 300 - margin.top - margin.bottom;

		// var x = d3.scale.identity()
		// 	.domain([0,])

		// var y = d3.scale.linear()
		// 	.domain([0,d3.max(accidents,function(data){return parseInt(data.amount);})])
		//     .range([height, 0]);

		// var yAxis = d3.svg.axis()
		// 	.scale(y)
  //       	.orient("left");
		
		// var svg = d3.select("#chart-city").append("svg")
		//     .attr("width", width + margin.left + margin.right)
		//     .attr("height", height + margin.top + margin.bottom);

  //   var bar = svg.selectAll("g")
		// 		.data(accidents)
		// 	.enter().append("g")
		//     .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

		// svg.append("g")
		// 	.attr("class", "axis")
		// 	.attr("transform", "translate("+margin.left+",20)")
  //   	.call(yAxis);

  //   bar.append("rect")
  //     .attr("stroke", "black")
  //     .attr("stroke-width", 1)
  //     .style("fill", "steelblue")
  //     .attr("x", function(d,i) { return (i*50)+20; })
  //     .attr("width", 40 )
  //     .attr("y", function(d) { return y(d.amount); })
  //     .attr("height", function(d) { return (height - y(d.amount)); });

  // 	bar.append("text")
  // 	 	.attr('class',function(d) { if(d.amount < 10) return 'label-black'; else return 'label';})
  //   	.attr("x", function(d,i) { return (i*50)+45; })
  //   	.attr("y", function(d) { if(d.amount < 10) return y(d.amount)-10; else return y(d.amount-5);})
  //   	.attr("dy", ".35em")
  //   	.text(function(d) { return d.amount; });

  //   bar.append("text")
  // 	 	.attr('class','label labelrit')
  //     .attr("x", function(d,i) { return (i*50)+20; })
  //   	.attr("y", 10)
  //   	.attr("transform", "rotate(10)")
  //   	.text(function(d) { return d.name; });

	});


$('#filter').click(function(){
	var startDate = $('#ini_date').val(),
			endDate		= $('#end_date').val();
			startDate = startDate.split('/');
			endDate = endDate.split('/');

	d3.json('/api/return-accidents/startDate/'+startDate[2]+'-'+startDate[1]+'-'+startDate[0]+'/endDate/'+endDate[2]+'-'+endDate[1]+'-'+endDate[0], function(error, accidents) {
		if(error) return console.warn(error);

		window.svg.remove();


		var margin = {top: 20, right: 40, bottom: 30, left: 40},
		    width = 960 - margin.left - margin.right,
		    height = 300 - margin.top - margin.bottom;

		var y = d3.scale.linear()
			.domain([0,d3.max(accidents,function(data){return parseInt(data.amount);})])
		    .range([height, 0]);

		var yAxis = d3.svg.axis()
			.scale(y)
        	.orient("left");
		
		window.svg = d3.select("#chart").append("svg")
		    .attr("width", width + margin.left + margin.right)
		    .attr("height", height + margin.top + margin.bottom);

		var bar = svg.selectAll("g")
				.data(accidents)
			.enter().append("g")
		    .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

		svg.append("g")
			.attr("class", "axis")
			.attr("transform", "translate("+margin.left+",20)")
    	.call(yAxis);

    bar.append("rect")
      .attr("stroke", "black")
      .attr("stroke-width", 1)
      .style("fill", "steelblue")
      .attr("x", function(d,i) { return (i*50)+20; })
      .attr("width", 40 )
      .attr("y", function(d) { return y(d.amount); })
      .attr("height", function(d) { return (height - y(d.amount)); });

  	bar.append("text")
  	 	.attr('class','label')
    	.attr("x", function(d,i) { return (i*50)+45; })
    	.attr("y", function(d) { return y(d.amount-3); })
    	.attr("dy", ".35em")
    	.text(function(d) { return d.amount; });

    bar.append("text")
  	 	.attr('class','label labelrit')
    	.attr("x", function(d,i) { return (i*50)+50; })
    	.attr("y", function(d) { return height+10; })
    	.attr("dy", ".35em")
    	.text(function(d) { return 'RIT '+d.rit; });

	});

});

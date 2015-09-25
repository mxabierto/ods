<?php

include('h_objetivos.php');

$o_id=pg_escape_string($_GET["o"]);
$i_id=pg_escape_string($_GET["i"]);
	
$ch = curl_init();
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_URL,"http://api.datos.gob.mx/v1/ods.metadata?pageSize=999999");
$result=curl_exec($ch);
curl_close($ch);
$metadata = json_decode($result, true);
$indicadores_id = array();
foreach($metadata["results"] as $value) {
	if (array_key_exists($value["Nombre_del_objetivo"],$indicadores)) array_push($indicadores[$value["Nombre_del_objetivo"]],$value);
	$indicadores_id[$value["Clave"]] = $value;
}

foreach($indicadores as $key => $obj) {
	if (count($obj) < 1) unset($indicadores[$key]);
}

$ch = curl_init();
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_URL,"http://api.datos.gob.mx/v1/ods.desagregacion?pageSize=999999");
$result=curl_exec($ch);
curl_close($ch);

$metadata_desag = json_decode($result, true);
$desagregacion = array();
foreach($metadata_desag["results"] as $value) {
	if (!array_key_exists($value["DesGeo"],$desagregacion)) $desagregacion[$value["DesGeo"]] = array();
	array_push($desagregacion[$value["DesGeo"]],$value["id"]);
}

$ch = curl_init();
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_URL,"http://api.datos.gob.mx/v1/ods.codigosGrupos?pageSize=999999");
$result=curl_exec($ch);
curl_close($ch);

$metadata_grupos = json_decode($result, true);
$grupos = array();
foreach($metadata_grupos["results"] as $value) {
	if (!array_key_exists($value["id"],$grupos)) $grupos[$value["id"]] = array();
	array_push($grupos[$value["id"]],$value);
}


?>

<script type="text/javascript">
	
	function commaSeparateNumber(x){var parts=x.toString().split(".");parts[0]=parts[0].replace(/\B(?=(\d{3})+(?!\d))/g,",");return parts.join(".");}
	
	var metadata_grouped = <?php echo json_encode($indicadores); ?>;
	var metadata_groupedbyid = <?php echo json_encode($indicadores_id); ?>;
	var indicadores_by_desagregacion = <?php echo json_encode($desagregacion); ?>;
	var indicadores_grupos = <?php echo json_encode($grupos); ?>;
	
	function exportToPDF(i) {
		(function ($) {
			if (i == "map") {
				var svg = $("svg.leaflet-zoom-animated")[0];
			}
			else if (i == "line") {
				var svg = $("#chart svg")[0];
			}
			else if (i == "gm") {
				var svg = $("#gm-chart svg")[0];
			}
			// var svg = document.getElementsByTagName("svg")[1];
			var serializer = new XMLSerializer();
			var str = serializer.serializeToString(svg);
			$.post("<?php echo path_to_theme(); ?>/php/svg2pdf/svg2pdf.php", str)
				.done(function(data) {
					window.open(data);
				});
			
		}(jQuery));
	}
	
	function closest (num, arr) {
        var curr = arr[0];
        var diff = Math.abs (num - curr);
        for (var val = 0; val < arr.length; val++) {
            var newdiff = Math.abs (num - arr[val]);
            if (newdiff < diff) {
                diff = newdiff;
                curr = arr[val];
            }
        }
        return curr;
    }
	var firstrun = true;
	var active_year = null;
	var active_unit = null;
	var active_group = "";
	var active_geom = null;
	var data_grouped = null;
	var data_grouped_b = null;
	var years = null;
	var years_b = null;
	var choro_layer = null;
	
	var geom_grouped = {
		"N": nacion,
		"E": entidad,
		"M": municipio
	};
	var searchControl = null;
	var map = new L.Map('map', {
			maxZoom: 14,
			minZoom: 5,
			scrollWheelZoom: false
		}).setView(new L.LatLng(24.75,-101.5),5);
	var basemap = new L.TileLayer("http://{s}.google.com/vt/?hl=es&x={x}&y={y}&z={z}&s={s}&apistyle=s.t%3A5|p.l%3A53%2Cs.t%3A1314|p.v%3Aoff%2Cp.s%3A-100%2Cs.t%3A3|p.v%3Aon%2Cs.t%3A2|p.v%3Aoff%2Cs.t%3A4|p.v%3Aoff%2Cs.t%3A3|s.e%3Ag.f|p.w%3A1|p.l%3A100%2Cs.t%3A18|p.v%3Aoff%2Cs.t%3A49|s.e%3Ag.s|p.v%3Aon|p.s%3A-19|p.l%3A24%2Cs.t%3A50|s.e%3Ag.s|p.v%3Aon|p.l%3A15&style=47,37", {
		subdomains: ['mt0','mt1','mt2','mt3'],
		zIndex: -1,
		detectRetina: true,
		scrollWheelZoom: false
	});
	map.addLayer(basemap);
	if( /Android|webOS|iPhone|iPad|iPod|BlackBerry/i.test(navigator.userAgent) ) {
		map.dragging.disable();
	}
	
	// BEGIN API FUNCTIONS
		
	function gm() {
		
		var min_year = Math.min.apply(Math, [ Math.min.apply(Math, years), Math.min.apply(Math, years_b) ]);
		var max_year = Math.max.apply(Math, [ Math.max.apply(Math, years), Math.max.apply(Math, years_b) ]);
		
		var v1_name = null, v2_name = null;
		var v1_domain = null;
		var v2_domain = null;
		var scatter_data = [];

		(function ($) {
			$("#chart-slider").html('<table><td id="min-time">'+min_year+'</td><td><input type="range" name="points" min="0" max="'+(max_year-min_year)+'" step="1" value="0" id="slider-time" style="width:900px"></td><td id="max-year">'+max_year+'</td></table>');
			v1_name = $("select#select-indicador-a option:selected").text();
			v2_name = $("select#select-indicador-b option:selected").text();
			v1_values = [];
			v2_values = [];
			$.each(active_geom.features, function(key, unit) {
				geography = {};
				if (active_unit == "E") geography.name = unit.properties.nom_ent;
				else if (active_unit == "M") geography.name = unit.properties.nom_mpo + ", " + unit.properties.nom_ent;
				geography.cve = unit.properties.cve;
				geography.v1 = [];
				$.each(years, function (key,year) {
					try {
						geography.v1.push([year, parseFloat(data_grouped[year][active_unit][unit.properties.cve].valor)]);
						v1_values.push(parseFloat(data_grouped[year][active_unit][unit.properties.cve].valor));
					}
					catch (e) {}
				});
				geography.v2 = [];
				$.each(years_b, function (key,year) {
					try { 
						geography.v2.push([year, parseFloat(data_grouped_b[year][active_unit][unit.properties.cve].valor)]);
						v2_values.push(parseFloat(data_grouped_b[year][active_unit][unit.properties.cve].valor));
					}
					catch (e) {}
				});
				scatter_data.push(geography);
			});
			v1_domain = [ Math.min.apply(Math, v1_values), Math.max.apply(Math, v1_values) ];
			v2_domain = [ Math.min.apply(Math, v2_values), Math.max.apply(Math, v2_values) ];
		}(jQuery));
		
		(function ($) {
			$("#gm-chart").empty();
		}(jQuery));
		
		// Various accessors that specify the four dimensions of data to visualize.
		function x(d) { return d.v2; }
		function y(d) { return d.v1; }
		function key(d) { return d.name; }
		// Chart dimensions.
		var margin = {top: 19.5, right: 19.5, bottom: 40, left: 39.5},
		    width = 960 - margin.right,
		    height = 600 - margin.top - margin.bottom;
		// Various scales. These domains make assumptions of data, naturally.
		var xScale = d3.scale.linear().domain(v2_domain).range([0, width]),
		    yScale = d3.scale.linear().domain(v1_domain).range([height, 0]);
		// The x & y axes.
		var xAxis = d3.svg.axis().orient("bottom").scale(xScale).ticks(12, d3.format(",d")),
		    yAxis = d3.svg.axis().scale(yScale).orient("left");
		// Create the SVG container and set the origin.
		var svg = d3.select("#gm-chart").append("svg")
		    .attr("width", width + margin.left + margin.right)
		    .attr("height", height + margin.top + margin.bottom)
		  .append("g")
		    .attr("transform", "translate(" + margin.left + "," + margin.top + ")")
		    .attr("class", "gRoot")
		// Add the x-axis.
		svg.append("g")
		    .attr("class", "x axis")
		    .attr("transform", "translate(0," + height + ")")
		    .call(xAxis);
		// Add the y-axis.
		svg.append("g")
		    .attr("class", "y axis")
		    .call(yAxis);
		// Add an x-axis label.
		svg.append("text")
		    .attr("class", "x label")
		    .attr("text-anchor", "end")
		    .attr("x", width / 1.5)
		    .attr("y", height + 36)
		    .text(v2_name);
		// Add a y-axis label.
		svg.append("text")
		    .attr("class", "y label")
		    .attr("text-anchor", "end")
		    .attr("y", -40)
		    .attr("x", -100)
		    .attr("dy", ".75em")
		    .attr("transform", "rotate(-90)")
		    .text(v1_name);
		// Add the year label; the value is set on transition.
		var label = svg.append("text")
		    .attr("class", "year label")
		    .attr("text-anchor", "end")
		    .attr("y", height - 24)
		    .attr("x", width)
		    .text(min_year);
		// Add the country label; the value is set on transition.
		var countrylabel = svg.append("text")
		    .attr("class", "country label")
		    .attr("text-anchor", "start")
		    .attr("y", 80)
		    .attr("x", 40)
		    .text(" ");
		var first_time = true;

		  // A bisector since many nation's data is sparsely-defined.
		  var bisect = d3.bisector(function(d) { return d[0]; });
		  // Add a dot per nation. Initialize the data at 1800, and set the colors.
		  var dot = svg.append("g")
		      .attr("class", "dots")
		    .selectAll(".dot")
		      .data(interpolateData(min_year))
		    .enter().append("circle")
		      .attr("class", "dot")
		      .style("fill", function(d) { return "#00cc99"; })
		      .call(position)
		      .on("mouseup", function(d, i) {
		        dot.classed("selected", false);
		        d3.select(this).classed("selected", !d3.select(this).classed("selected"));
		        dragit.trajectory.display(d, i, "selected");
		        //TODO: test if has been dragged
		        // Look at the state machine history and find a drag event in it?
		      })
		      .on("mouseenter", function(d, i) {
		        if(dragit.statemachine.current_state == "idle") {
		          dragit.trajectory.display(d, i)
		          dragit.utils.animateTrajectory(dragit.trajectory.display(d, i), dragit.time.current, 1000)
		          countrylabel.text(d.name);
		          dot.style("opacity", .4)
		          d3.select(this).style("opacity", 1)
		          d3.selectAll(".selected").style("opacity", 1)
		        }
		      })
		      .on("mouseleave", function(d, i) {
		        if(dragit.statemachine.current_state == "idle") {
		          countrylabel.text("");
		          dot.style("opacity", 1);
		        }
		  
		        dragit.trajectory.remove(d, i);
		      })
		      .call(dragit.object.activate)
		  // Add a title.
		  dot.append("title")
		      .text(function(d) { return d.name; });
		  // Start a transition that interpolates the data based on year.
		  svg.transition()
		      .duration(30000)
		      .ease("linear")
		  // Positions the dots based on data.
		  function position(dot) {
		    dot.attr("cx", function(d) { return xScale(x(d)); })
		       .attr("cy", function(d) { return yScale(y(d)); })
		       .attr("r", function(d) { return 5; });
		  }
		  // Updates the display to show the specified year.
		  function displayYear(year) {
		    dot.data(interpolateData(year+dragit.time.min), key).call(position);
		    label.text(dragit.time.min + Math.round(year));
		    d3.select("#slider-time").property("value", dragit.time.current);
		  }
		  // Interpolates the dataset for the given (fractional) year.
		  function interpolateData(year) {
		    return scatter_data.map(function(d) {
		      return {
		        name: d.name,
		        cve: d.cve,
		        v1: interpolateValues(d.v1, year),
		        v2: interpolateValues(d.v2, year)
		      };
		    });
		  }
		  // Finds (and possibly interpolates) the value for the specified year.
		  function interpolateValues(values, year) {
		    var i = bisect.left(values, year, 0, values.length - 1),
		        a = values[i];
		    if (i > 0) {
		      var b = values[i - 1],
		          t = (year - a[0]) / (b[0] - a[0]);
		      return a[1] * (1 - t) + b[1] * t;
		    }
		    return a[1];
		  }
		  
		  init();
		 
		  function update(v, duration) {
		    dragit.time.current = v || dragit.time.current;
		    displayYear(dragit.time.current)
		    d3.select("#slider-time").property("value", dragit.time.current);
		  }
		  function init() {
		    dragit.init(".gRoot");
		    dragit.time = {min:min_year, max:max_year, step:1, current:min_year}
		    dragit.data = d3.range(scatter_data.length).map(function() { return Array(); })
		    for(var yy = min_year; yy<=max_year; yy++) {
		      interpolateData(yy).filter(function(d, i) { 
		        dragit.data[i][yy-dragit.time.min] = [xScale(x(d)), yScale(y(d))];
		      })
		    }
		    dragit.evt.register("update", update);
		    //d3.select("#slider-time").property("value", dragit.time.current);
		    d3.select("#slider-time")
		      .on("mousemove", function() { 
		        update(parseInt(this.value), 500);
		      })
		    var end_effect = function() {
		      countrylabel.text("");
		      dot.style("opacity", 1)
		    }
		    dragit.evt.register("dragend", end_effect)
		    displayYear(active_year-min_year);
		  }
	}

	
	function render_line() {
		var line_columns = [];
		line_columns_years = ["x"];
		(function ($) {
			$.each(years, function(key,year) {
				line_columns_years.push(year);
			});
			line_columns.push(line_columns_years);
			line_colors = [];
			$.each(active_geom.features, function(key,feature) {
				row = [feature.properties.nom_ent];
				$.each(years, function(ykey,year) {
					if (typeof feature.properties[year] != 'undefined')
						row.push(feature.properties[year]);
					else row.push(null);
				});
				line_columns.push(row);
				line_colors.push('#ccc');
			});
			prom_row = ["Promedio nacional"];
			$.each(years, function(ykey,year) {
				prom_val = 0;
				$.each(active_geom.features, function(key,feature) {
					prom_val = prom_val + feature.properties[year];
				});
				prom_row.push(prom_val/active_geom.features.length);
			});
			line_columns.push(prom_row);
			line_colors.push('#f00');
		}(jQuery));
		if (years[0].indexOf("-") != -1) date_format = '%Y%m';
		else date_format = '%Y';
		var chart = c3.generate({
		    data: {
		        x: 'x',
		        xFormat: date_format,
		        columns: line_columns
		    },
		    padding: {
		        left: 40,
		        right: 30
		    },
		    point: {
			  show: true
			},
		    axis: {
		        x: {
		            type: 'timeseries',
		            tick: {
		                format: date_format
		            }
		        }
		    },
		    color: {
		        pattern: line_colors
		    },
		    size: {
		        width: 700
		    },
		    legend: {
		        show: false
		    },
		    tooltip: {
				grouped: false,
				format: {
		            title: function (d) { return d.name; },
		            value: function (value, ratio, id) {
			            (function ($) {
				            if (id != "Promedio nacional") {
					            $( "svg g.c3-chart-line g.c3-circles circle" ).css('fill','#ccc');
								$( "svg g.c3-chart-line path.c3-line" ).css('stroke','#ccc');
								$( "svg g.c3-chart-line g.c3-circles-Promedio-nacional circle" ).css('fill','#f00');
								$( "svg g.c3-chart-line path.c3-line-Promedio-nacional" ).css('stroke','#f00');
								$( "svg g.c3-chart-line g.c3-circles-" +id.replace(/ /g,'-') + " circle" ).css('fill','#00cc99');
								$( "svg g.c3-chart-line path.c3-line-" +id.replace(/ /g,'-') ).css('stroke','#00cc99');
				            }
				            else {
					            $( "svg g.c3-chart-line g.c3-circles circle" ).css('fill','#ccc');
								$( "svg g.c3-chart-line path.c3-line" ).css('stroke','#ccc');
					            $( "svg g.c3-chart-line g.c3-circles-Promedio-nacional circle" ).css('fill','#f00');
								$( "svg g.c3-chart-line path.c3-line-Promedio-nacional" ).css('stroke','#f00');
				            }
			            }(jQuery));
		                return commaSeparateNumber(Math.round(value*10)/10);
		            }
		//            value: d3.format(',') // apply this format to both y and y2
		        }
			},
		    grid: {
		        x: {
		            show: false
		        },
		        y: {
		            show: true
		        }
		    }
		});
	}
	
	function highlightFromLegend(i) {
		(function ($) {
			$("svg path.class-"+i).addClass("highlighted");
		}(jQuery));
	}
	
	function clearHighlight() {
		(function ($) {
			$("path").removeClass("highlighted");
		}(jQuery));
	}
	
	function render_map() {
		<?php if ($page == "compara"): ?>
		nb_breaks = turf.jenks(active_geom, (active_year), 3);
		nb_breaks_b = turf.jenks(active_geom, (closest(active_year,years_b)+"_b"), 3);
		(function ($) {
			$("td.legend-breaks-0").html(commaSeparateNumber(Math.round(nb_breaks[0]*10)/10) + " - " + commaSeparateNumber(Math.round(nb_breaks[1]*10)/10));
			$("td.legend-breaks-1").html(commaSeparateNumber(Math.round(nb_breaks[1]*10)/10) + " - " + commaSeparateNumber(Math.round(nb_breaks[2]*10)/10));
			$("td.legend-breaks-2").html(commaSeparateNumber(Math.round(nb_breaks[2]*10)/10) + " - " + commaSeparateNumber(Math.round(nb_breaks[3]*10)/10));
			$("td.legend-breaks-b-0").html(commaSeparateNumber(Math.round(nb_breaks_b[0]*10)/10) + " - " + commaSeparateNumber(Math.round(nb_breaks_b[1]*10)/10));
			$("td.legend-breaks-b-1").html(commaSeparateNumber(Math.round(nb_breaks_b[1]*10)/10) + " - " + commaSeparateNumber(Math.round(nb_breaks_b[2]*10)/10));
			$("td.legend-breaks-b-2").html(commaSeparateNumber(Math.round(nb_breaks_b[2]*10)/10) + " - " + commaSeparateNumber(Math.round(nb_breaks_b[3]*10)/10));
		
		}(jQuery));
		function fill_color(a,b) {
			if (a == -99999 || b == -99999) return "#ccc";
			else if (b >= nb_breaks_b[2]) {
				if (a >= nb_breaks[2]) return '#897c36';
				else if (a >= nb_breaks[1]) return '#95a865';
				else if (a >= nb_breaks[0]) return '#95c497';
			}
			else if (b >= nb_breaks_b[1]) {
				if (a >= nb_breaks[2]) return '#c0976b';
				else if (a >= nb_breaks[1]) return '#cbc39a';
				else if (a >= nb_breaks[0]) return '#ccdfcc';
			}
			else if (b >= nb_breaks_b[0]) {
				if (a >= nb_breaks[2]) return '#e6a583';
				else if (a >= nb_breaks[1]) return '#f1d2b2';
				else if (a >= nb_breaks[0]) return '#f2eee4';
			}
		}
		function category(a,b) {
			if (a == -99999 || b == -99999) return "00";
			else if (b >= nb_breaks_b[2]) {
				if (a >= nb_breaks[2]) return '22';
				else if (a >= nb_breaks[1]) return '21';
				else if (a >= nb_breaks[0]) return '20';
			}
			else if (b >= nb_breaks_b[1]) {
				if (a >= nb_breaks[2]) return '12';
				else if (a >= nb_breaks[1]) return '11';
				else if (a >= nb_breaks[0]) return '10';
			}
			else if (b >= nb_breaks_b[0]) {
				if (a >= nb_breaks[2]) return '02';
				else if (a >= nb_breaks[1]) return '01';
				else if (a >= nb_breaks[0]) return '00';
			}
		}
		<?php endif; ?>
		<?php if ($page != "compara"): ?>
		nb_breaks = turf.jenks(active_geom, (active_year), 4);
		(function ($) {
			$("td.legend-breaks-0").html(commaSeparateNumber(Math.round(nb_breaks[0]*10)/10) + " - " + commaSeparateNumber(Math.round(nb_breaks[1]*10)/10));
			$("td.legend-breaks-1").html(commaSeparateNumber(Math.round(nb_breaks[1]*10)/10) + " - " + commaSeparateNumber(Math.round(nb_breaks[2]*10)/10));
			$("td.legend-breaks-2").html(commaSeparateNumber(Math.round(nb_breaks[2]*10)/10) + " - " + commaSeparateNumber(Math.round(nb_breaks[3]*10)/10));
			$("td.legend-breaks-3").html(commaSeparateNumber(Math.round(nb_breaks[3]*10)/10) + " - " + commaSeparateNumber(Math.round(nb_breaks[4]*10)/10));
		
		}(jQuery));
		function fill_color(v) {
			if (v == -99999) return "#ccc";
			else if (v >= nb_breaks[3]) return '#086';
			else if (v >= nb_breaks[2]) return '#00cc99';
			else if (v >= nb_breaks[1]) return '#7fd';
			else if (v >= nb_breaks[0]) return '#ddfff6';
		}
		function category(v) {
			if (v == -99999) return "#ccc";
			else if (v >= nb_breaks[3]) return "3";
			else if (v >= nb_breaks[2]) return "2";
			else if (v >= nb_breaks[1]) return "1";
			else if (v >= nb_breaks[0]) return "0";
		}
		<?php endif; ?>	
		
		
		
		if (choro_layer != null) map.removeLayer(choro_layer);
		choro_layer = L.geoJson(active_geom, {
			style: function(feature) {
				 return {
					<?php if ($page != "compara"): ?>
			        fillColor: fill_color(feature.properties[active_year]),
			        <?php endif; ?>
			        <?php if ($page == "compara"): ?>
			        fillColor: fill_color(feature.properties[active_year], feature.properties[closest(active_year,years_b)+"_b"]),
			        <?php endif; ?>
			        <?php if ($page != "compara"): ?>
			        className: "class-"+String(category(feature.properties[active_year])),
			        <?php endif; ?>
			        <?php if ($page == "compara"): ?>
			        className: "class-"+String(category(feature.properties[active_year],feature.properties[closest(active_year,years_b)+"_b"])),
			        <?php endif; ?>
			        weight: 0.5,
			        opacity: 1,
			        color: '#005540',
			        fillOpacity: 0.5
			    };
			},
			onEachFeature: function(feature, layer) {
				if (active_unit == "E") feature.properties.name = feature.properties.nom_ent + " (" + commaSeparateNumber(Math.round(feature.properties[active_year]*10)/10) + ")";
				else if (active_unit == "M") feature.properties.name = feature.properties.nom_mun + ", " + feature.properties.nom_ent + " (" + commaSeparateNumber(Math.round(feature.properties[active_year]*10)/10) + ")";
				layer.on({
					mouseover: function(e) {
						var layer = e.target;
						layer.setStyle({
							weight: 2,
							color: '#666',
							dashArray: '',
							fillOpacity: 0.7
						});
						if (!L.Browser.ie && !L.Browser.opera) {
							layer.bringToFront();
						}
						(function ($) {
							$(".infobox").show();
							if (active_unit == "E") {
								$(".unit-name").html(feature.properties.nom_ent);
								$(".edo-name").html("");
								$(".edo-image").html("<img width=40 height=40 src='<?php echo path_to_theme().'/assets/estados/';?>"+feature.properties.cve+".png'/>");
							}
							else if (active_unit == "M") {
								$(".unit-name").html(feature.properties.nom_mun);
								$(".edo-name").html(feature.properties.nom_ent);
								$(".edo-image").html("<img width=40 height=40 src='<?php echo path_to_theme().'/assets/estados/';?>"+feature.properties.cve.substring(0, feature.properties.cve.length - 3)+".png'/>");
							}
							if (typeof feature.properties[active_year] != 'undefined')
								$(".indicador-valor").html(commaSeparateNumber(Math.round(feature.properties[active_year]*10)/10));
							else
								$(".indicador-valor").html("N/A");
							$(".indicador-nombre").html($("select#select-indicador-a option:selected").text() + " ("+active_year+")"); 
							<?php if ($page == "compara"): ?>
								closest_year = closest(active_year,years_b)
								$(".indicador-valor-b").html(feature.properties[closest_year+"_b"]);
								$(".indicador-nombre-b").html($("select#select-indicador-b option:selected").text() + " ("+closest_year+")"); 
							<?php endif; ?>
						}(jQuery));
						// Render individual line chart
						<?php if ($page != "compara"): ?>
						var line_columns = [];
						line_columns_years = ["x"];
						(function ($) {
							$.each(years, function(key,year) {
								line_columns_years.push(year);
							});
							line_columns.push(line_columns_years);
							row = [feature.properties.nom_ent];
							$.each(years, function(ykey,year) {
								if (typeof feature.properties[year] != 'undefined')
									row.push(Math.round(feature.properties[year]*10)/10);
								else row.push(null);
							});
							line_columns.push(row);
						}(jQuery));
						if (years[0].indexOf("-") != -1) date_format = '%Y%m';
						else date_format = '%Y';
						var chart = c3.generate({
							bindto: '#infobox-line-chart',
							padding: {
								top: 10,
						        left: 30,
						        right: 10
						    },
						    data: {
						        x: 'x',
						        xFormat: date_format,
						        columns: line_columns,
						        
						    },
						    axis: {
						        x: {
						            type: 'timeseries',
						            tick: {
						                format: date_format
						            }
						        }
						    },
						    color: {
						        pattern: ['#00cc99']
						    },
						    size: {
						        width: 260,
						        height: 160
						    },
						    legend: {
						        show: false
						    },
						    grid: {
						        x: {
						            show: false
						        },
						        y: {
						            show: true
						        }
						    }
						});
						<?php endif; ?>
					},
					mouseout: function(e) {
						choro_layer.resetStyle(e.target);
					}
				});
			}
		});
		map.addLayer(choro_layer);
		
		if (searchControl != null) map.removeControl(searchControl);
		searchControl = new L.Control.Search({layer: choro_layer, propertyName: 'name', circleLocation:false});
		searchControl.on('search_locationfound', function(e) {
			map.fitBounds(e.layer.getBounds());
			choro_layer.eachLayer(function(layer) {	//restore feature color
				choro_layer.resetStyle(layer);
				e.layer.setStyle({fillColor: '#3f0', color: '#0f0'});
			});
		}).on('search_collapsed', function(e) {
			choro_layer.eachLayer(function(layer) {	//restore feature color
				choro_layer.resetStyle(layer);
			});	
		});
		
		map.addControl( searchControl );  //inizialize search control
		
		<?php if ($page != "compara"): ?>
			render_line();
		<?php endif; ?>
		<?php if ($page == "compara"): ?>
			gm();
		<?php endif; ?>
		(function ($) { $("#loading_wrap").fadeOut(); }(jQuery));
	}
	
	function change_active_year(y) {
		active_year = y;
		(function ($) {
			$("section.year-selector .year-selected").removeClass('year-selected');
			$("section.year-selector .filtro-year-"+y).addClass('year-selected');
		}(jQuery));
		asc = false;
		prop = y;
		active_geom.features = active_geom.features.sort(function(a, b) {
	        if (asc) return (a.properties[prop] > b.properties[prop]) ? 1 : ((a.properties[prop] < b.properties[prop]) ? -1 : 0);
	        else return (b.properties[prop] > a.properties[prop]) ? 1 : ((b.properties[prop] < a.properties[prop]) ? -1 : 0);
	    });
		for (i = 0; i < 3; i++) {
			(function ($) {
				if (active_unit == "E") $(".top-municipios-group-"+i+" .top-municipios-name").html(active_geom.features[i].properties.nom_ent);
				if (active_unit == "M") $(".top-municipios-group-"+i+" .top-municipios-name").html(active_geom.features[i].properties.nom_mun);
				$(".top-municipios-group-"+i+" .top-municipios-pct").html(commaSeparateNumber(Math.round(active_geom.features[i].properties[active_year]*10)/10));
			}(jQuery));
		}
		render_map();
	}
	
	function change_active_unit(u) {
		active_unit = u;
		active_geom = geom_grouped[active_unit];
		(function ($) {
			$.each(active_geom.features, function(key, unit) {
				$.each(years, function (key,year) {
					if (typeof data_grouped[year] != 'undefined') {
						if (typeof data_grouped[year][active_unit] != 'undefined') {
							if (typeof data_grouped[year][active_unit][unit.properties.cve] != 'undefined') {
								unit.properties[year] = parseFloat(data_grouped[year][active_unit][unit.properties.cve].valor);
							}
							else unit.properties[year] == -99999;
						}
						else unit.properties[year] == -99999;
					}
					else unit.properties[year] == -99999;
				})
			});
			<?php if ($page == "compara"): ?>
				$.each(active_geom.features, function(key, unit) {
					$.each(years_b, function (key,year) {
						if (typeof unit.properties[year] != 'undefined')
							unit.properties[year] = parseFloat(data_grouped[year][active_unit][unit.properties.cve].valor);
						else unit.properties[year] == -99999;
						unit.properties[year+"_b"] = parseFloat(data_grouped_b[year][active_unit][unit.properties.cve].valor);
					})
				});
			<?php endif; ?>
		}(jQuery));
		
		change_active_year(active_year);
	}
	
	function change_variable() {
		active_unit_b = null;
		data_grouped = {};
		data_grouped_b = {};
		years = [];
		years_b = [];
		units = [];
		units_b = [];
		
		(function ($) {
			params = { id: $("select#select-indicador-a option:selected").val(), pageSize: 999999 };
			if ($("select#filter-grupo option:selected").val() != "") params["id2"] = $("select#filter-grupo option:selected").val();
			$.getJSON("http://api.datos.gob.mx/v1/ods.datos", params, function (data) {		
				<?php if ($page == "compara"): ?>
				$.getJSON("http://api.datos.gob.mx/v1/ods.datos", { id: $("select#select-indicador-b option:selected").val(), pageSize: 999999}, function (data_b) {
				<?php endif; ?>
				
				$.each(data.results, function(key, valor) {
					// Month present
					if (parseInt(valor["m"]) != 0) {
						time_val = parseInt(valor["t"]) + "-" + parseInt(valor["m"]);
					}
					// Month not present
					else {
						time_val = String(parseInt(valor["t"]));
					}
					if (years.indexOf(time_val) == -1) { years.push(time_val) };
					if (units.indexOf(valor["DesGeo"]) == -1) units.push(valor["DesGeo"]);
					if (typeof data_grouped[time_val] === 'undefined') data_grouped[time_val] = {};
					if (typeof data_grouped[time_val][valor["DesGeo"]] === 'undefined') data_grouped[time_val][valor["DesGeo"]] = [];
					data_grouped[time_val][valor["DesGeo"]][String(parseInt(valor["cve"]))] = valor;
				});
				
				<?php if ($page == "compara"): ?>
					$.each(data_b.results, function(key, valor) {
						// Month present
						if (parseInt(valor["m"]) != 0) {
							time_val = valor["t"] + "-" + valor["m"];
						}
						// Month not present
						else {
							time_val = valor["t"];
						}
						if (years_b.indexOf(time_val) == -1) { years_b.push(time_val) };
						if (units_b.indexOf(valor["DesGeo"]) == -1) units_b.push(valor["DesGeo"]);
						if (typeof data_grouped_b[time_val] === 'undefined') data_grouped_b[time_val] = {};
						if (typeof data_grouped_b[time_val][valor["DesGeo"]] === 'undefined') data_grouped_b[time_val][valor["DesGeo"]] = [];
						data_grouped_b[time_val][valor["DesGeo"]][valor["cve"]] = valor;
					});
				<?php endif; ?>
				
				// Organize years
				years.sort();
				$("section.year-selector").empty();
				$.each(years, function(key, year) {
					$("section.year-selector").append("<div onmousedown='change_active_year("+year+")' style='width: "+Math.floor(100/years.length)+"%' class='year filtro-year-"+year+"'>"+year+"</div>")
				});
				$("section.year-selector").append('<div style="clear:both;"></div>');
				active_year = years[years.length-1];
				// Organize units
				$(".filter-geo").html('');
				<?php if ($page == "compara"): ?>
					/*
					if (units.indexOf("N") != -1 && units_b.indexOf("N") != -1 ) {
						$(".filter-geo").append('<option value="N" class="filter-item filtro-geo-N">Nacional</option>');
						active_unit = "N";
					}
					*/
					if (units.indexOf("E") != -1 && units_b.indexOf("E") != -1) {
						$(".filter-geo").append('<option value="E" class="filter-item filtro-geo-E">Estatal</option>');
						$(".stat-column-header-chart").html("Indicador a nivel estatal");
						$(".stat-column-header-top").html("Top 3 Estados");
						active_unit = "E";
					}
					if (units.indexOf("M") != -1 && units_b.indexOf("M") != -1) {
						$(".filter-geo").append('<option value="M" class="filter-item filtro-geo-M">Municipal</option>');
						$(".stat-column-header-chart").html("Top 3 Municipios");
						active_unit = "M";
					}
				<?php endif; ?>
				<?php if ($page != "compara"): ?>
					/*
					if (units.indexOf("N") != -1) {
						$(".filter-geo").append('<option value="N" class="filter-item filtro-geo-N">Nacional</option>');
						active_unit = "N";
					}
					*/
					if (units.indexOf("E") != -1) {
						$(".filter-geo").append('<option value="E" class="filter-item filtro-geo-E">Estatal</option>');
						$(".stat-column-header-chart").html("Indicador a nivel estatal");
						$(".stat-column-header-top").html("Top 3 Estados");
						active_unit = "E";
					}
					if (units.indexOf("M") != -1) {
						$(".filter-geo").append('<option value="M" class="filter-item filtro-geo-M">Municipal</option>');
						$(".stat-column-header-chart").html("Top 3 Municipios");
						active_unit = "M";
					}
				<?php endif; ?>
				$('select.filter-geo option[value='+ active_unit +']').attr('selected', 'selected');
				change_active_unit(active_unit);
				// Create filters (if applicable)
				<?php if ($page != "compara"): ?>
						if ($("select#select-indicador-a option:selected").val() in indicadores_grupos) {
							$("select.filter-grupo").empty();
							$.each(indicadores_grupos[$("select#select-indicador-a option:selected").val()], function(k, v) {
								$("select.filter-grupo").append('<option value="'+v.id2+'">'+v.id3+'</option>');
							})
							$(".form-group-grupo").show();
						}
						else {
							active_group = "";
							$("select.filter-grupo").empty();
							$("select.filter-grupo").append('<option value="" selected>-- Todos --</option>');
							$(".form-group-grupo").hide();
						}
						$('select#filter-grupo option[value="'+ active_group +'"]').attr('selected', 'selected');
				<?php endif; ?>
				// Add to Datos table
				$("table#datos tbody tr.indicador-a").remove();
				metadatos_a = metadata_groupedbyid[$("select#select-indicador-a option:selected").val()];
				$("table#datos tbody").append("<tr class='indicador-a datos-indicador'><td>"+metadatos_a["Nombre_del_indicador"]+"</td>"+metadatos_a["Dependencia"]+"<td></td><td>N/A</td><td><center><a href='"+metadatos_a["Desagregacion"]+"'><img width=35 height=36 src='<?php echo path_to_theme().'/assets/icon-circle-arrow-right-gray.png'; ?>'/></a></center></td></tr>");
				<?php if ($page == "compara"): ?>
					$("table#datos tbody tr.indicador-b").remove();
					metadatos_b = metadata_groupedbyid[$("select#select-indicador-b option:selected").val()];
					$("table#datos tbody").append("<tr class='indicador-b datos-indicador'><td>"+metadatos_b["Nombre_del_indicador"]+"</td>"+metadatos_b["Dependencia"]+"<td></td><td>N/A</td><td><center><a href='"+metadatos_b["Desagregacion"]+"'><img width=35 height=36 src='<?php echo path_to_theme().'/assets/icon-circle-arrow-right-gray.png'; ?>'/></a></center></td></tr>");
				<?php endif; ?>
			<?php if ($page == "compara"): ?>
				});
			<?php endif; ?>
			});
		}(jQuery));
	}
	
	function populate_indicador_a() {
		(function ($) {
			$("select#select-indicador-a").empty();
			if (firstrun == true) {
				o_id = '<?php echo $o_id; ?>';
				if (o_id != "") {
					$('select#select-objetivo-a option:nth-child('+(parseInt(o_id)+1)+')').attr('selected', 'selected');
				}
			}
			$.each(metadata_grouped[$("select#select-objetivo-a option:selected").val()], function(key, indicador) {
				if (indicadores_by_desagregacion["E"].indexOf(indicador.Clave) != -1 || indicadores_by_desagregacion["M"].indexOf(indicador.Clave) != -1) {
					$("select#select-indicador-a").append("<option value='"+indicador.Clave+"'>"+indicador.Nombre_del_indicador+"</option>");
				}
			});
			if (firstrun == true) {
				o_id = '<?php echo $o_id; ?>';
				i_id = '<?php echo $i_id; ?>';
				if (o_id != "") {
					if (i_id != "") {
						$('select#select-indicador-a option[value='+ i_id +']').attr('selected', 'selected');
					}
					else {
						$('select#select-indicador-a option:nth-child(1)').attr('selected', 'selected');
					}
				}
				else {
					$('select#select-indicador-a option[value='+ 'i7' +']').attr('selected', 'selected');
				}
				<?php if ($page != "compara"): ?>
				firstrun = false;
				<?php endif; ?>
			}
			change_variable();
		}(jQuery));
	}
	
	function populate_indicador_b() {
		(function ($) {
			$("select#select-indicador-b").empty();
			$.each(metadata_grouped[$("select#select-objetivo-b option:selected").val()], function(key, indicador) {
				if (indicadores_by_desagregacion["E"].indexOf(indicador.Clave) != -1 || indicadores_by_desagregacion["E"].indexOf(indicador.Clave) != -1) {
					$("select#select-indicador-b").append("<option value='"+indicador.Clave+"'>"+indicador.Nombre_del_indicador+"</option>");
				}
			});
			if (firstrun == true) {
				$('select#select-indicador-b option[value='+ 'i9' +']').attr('selected', 'selected');
				firstrun = false;
			}
			change_variable();
		}(jQuery));
	}
	
	(function ($) {

			// Indicador A
			$("select#select-objetivo-a").empty();
			$.each(metadata_grouped, function(objetivo, indicadores) {
				$("select#select-objetivo-a").append("<option value='"+objetivo+"'>"+objetivo+"</option>");
			});
			$("select#select-objetivo-a").change(function() {
				(function ($) { $("#loading_wrap").fadeIn(); }(jQuery));
				populate_indicador_a();
			});
			if (firstrun == true) {
				$('select#select-objetivo-a option[value="'+ 'Demanda insatisfecha de métodos anticonceptivos modernos' +'"]').attr('selected', 'selected');
			}
			populate_indicador_a();
			<?php if ($page == "compara"): ?>
				// Indicador B
				$("select#select-objetivo-b").empty();
				$.each(metadata_grouped, function(objetivo, indicadores) {
					$("select#select-objetivo-b").append("<option value='"+objetivo+"'>"+objetivo+"</option>");
				});
				$("select#select-objetivo-b").change(function() {
					(function ($) { $("#loading_wrap").fadeIn(); }(jQuery));
					populate_indicador_b();
				});
				if (firstrun == true) {
				$('select#select-objetivo-b option[value="'+ 'Poner fin a la pobreza en todas sus formas en todo el mundo' +'"]').attr('selected', 'selected');
			}
				populate_indicador_b();
			<?php endif; ?>
			change_variable();
		
		$("select#select-indicador-a").change(function() {
			(function ($) { $("#loading_wrap").fadeIn(); }(jQuery));
			change_variable();
		});
		$("select#filter-grupo").change(function() {
			active_group = $("select#filter-grupo option:selected").val();
			change_variable();
		});
		$("ul.menu li.leaf:nth-child(2) a").mousedown(function() {
			o=$("select#select-objetivo-a option:selected").index();
			i=$("select#select-indicador-a option:selected").val();
			$("ul.menu li.leaf:nth-child(2) a").attr("href","/explora?o="+o+"&i="+i);
		});
		$("ul.menu li.leaf:nth-child(3) a").mousedown(function() {
			o=$("select#select-objetivo-a option:selected").index();
			i=$("select#select-indicador-a option:selected").val();
			$("ul.menu li.leaf:nth-child(3) a").attr("href","/compara?o="+o+"&i="+i);
		})
		<?php if ($page == "compara"): ?>
			$("select#select-indicador-b").change(function() {
				(function ($) { $("#loading_wrap").fadeIn(); }(jQuery));
				change_variable();
			});
		<?php endif; ?>
	}(jQuery));
	

	// END API FUNCTIONS
	
</script>
<?php
	$page = "compara";
	drupal_add_css('https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.5/leaflet.css','external');
	drupal_add_css(path_to_theme().'/js/leaflet-search.min.css');
	drupal_add_css('https://cdnjs.cloudflare.com/ajax/libs/c3/0.4.10/c3.min.css','external');
	drupal_add_js('https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.5/leaflet.js','external');
	drupal_add_js(path_to_theme().'/js/jquery.svg.min.js');
	drupal_add_js(path_to_theme().'/js/jquery.svgdom.min.js');
	drupal_add_js(path_to_theme().'/js/leaflet-search.min.js');
	drupal_add_js('https://cdnjs.cloudflare.com/ajax/libs/d3/3.5.6/d3.min.js','external');
	drupal_add_js('https://cdnjs.cloudflare.com/ajax/libs/c3/0.4.10/c3.min.js','external');
	drupal_add_js('https://api.tiles.mapbox.com/mapbox.js/plugins/turf/v2.0.0/turf.min.js','external');
	// Local functions
	drupal_add_css(path_to_theme().'/js/dragit.css');
	drupal_add_js(path_to_theme().'/js/dragit.js');
	drupal_add_js(path_to_theme().'/js/geom/nacion.json');
	drupal_add_js(path_to_theme().'/js/geom/entidad.json');
	drupal_add_js(path_to_theme().'/js/geom/municipio.json');
?>
<style>

#gm-chart {
  height: 640px;
  padding: 30px;
  margin-left: -40px;
}

#gm-chart text {
  font-family: 'Open Sans', 'Helvetica Neue', 'Helvetica', sans-serif;
  font: 10px;
}

#gm-chart .dot {
  stroke: #000;
}

#gm-chart .axis path, .axis line {
  fill: none;
  stroke: #aaa;
  shape-rendering: crispEdges;
}


#gm-chart .country.label  {
  font-size: 90px;
  font-weight: 700;
  fill: #ddd;
}

#gm-chart .year.label {
  font-size: 120px;
  font-weight: 700;
  fill: #ddd;
}

#gm-chart .year.label.active {
  fill: #aaa;
}

.overlay {
  fill: none;
  pointer-events: all;
  cursor: ew-resize;
}

</style>
<div id='loading_wrap' style='position:fixed; height:100%; width:100%; overflow:hidden; top:0; left:0;'><div style="margin-right: 50px;">Cargando datos...</div></div><page class="explora">
	<section class="objective-selector">
		<div class="container">
			<div class="row" style="margin-bottom: 10px;">
				<div class="col-xs-12 col-sm-2"><span class="circle-letter">A</span><span>Objetivo</span></div>
				<div class="col-xs-12 col-sm-4">
					<select id="select-objetivo-a">
					</select>
				</div>
				<div class="col-xs-12 col-sm-1">Indicador</div>
				<div class="col-xs-12 col-sm-5">
					<select id="select-indicador-a">
					</select>
				</div>
			</div>
			<div class="row" style="margin-bottom: 10px;">
				<div class="col-xs-12 col-sm-2"><span class="circle-letter">B</span><span>Objetivo</span></div>
				<div class="col-xs-12 col-sm-4">
					<select id="select-objetivo-b">
					</select>
				</div>
				<div class="col-xs-12 col-sm-1">Indicador</div>
				<div class="col-xs-12 col-sm-5">
					<select id="select-indicador-b">
					</select>
				</div>
			</div>
			<div class="row">
				<div class="col-xs-12 col-sm-2"><span class="circle-letter">C</span><span>Desagregación</span></div>
				<div class="col-xs-12 col-sm-4">
					<select class="filter-geo">
					</select>
				</div>
			</div>
		</div>
	</section>
	<div id="map">
		<div class="infobox" style="display: none;">
			<div style="margin-bottom: 8px;" class="row">
				<div class="name-box col-xs-9">
					<div class="unit-name">--</div>
					<div class="edo-name">--</div>
				</div>
				<div class="edo-image col-xs-3">--</div>
			</div>
			<div class="row">
				<div class="col-xs-4 indicador-valor">--</div>
				<div class="col-xs-8 indicador-nombre">--</div>
			</div>
			<div class="row">
				<div class="col-xs-4 indicador-valor-b" style="color: #e6a583;">--</div>
				<div class="col-xs-8 indicador-nombre-b">--</div>
			</div>
			<div id="infobox-line-chart"></div>
			<div class="map-legend">
				<table id="legend-colors">
					<tbody>
						<tr>
							<td class="legend-breaks legend-breaks-b-0"></td>
							<td class="legend-color legend-color-00" onmouseout="clearHighlight();" onmouseover="highlightFromLegend('00')" style="background-color: #f2eee4;"></td>
							<td class="legend-color legend-color-01" onmouseout="clearHighlight();" onmouseover="highlightFromLegend('01')"  style="background-color: #f1d2b2;"></td>
							<td class="legend-color legend-color-02" onmouseout="clearHighlight();" onmouseover="highlightFromLegend('02')"  style="background-color: #e6a583;"></td>
						</tr>
						<tr>
							<td class="legend-breaks legend-breaks-b-1"></td>
							<td class="legend-color legend-color-10" onmouseout="clearHighlight();" onmouseover="highlightFromLegend('10')"  style="background-color: #ccdfcc;"></td>
							<td class="legend-color legend-color-11" onmouseout="clearHighlight();" onmouseover="highlightFromLegend('11')"  style="background-color: #cbc39a;"></td>
							<td class="legend-color legend-color-12" onmouseout="clearHighlight();" onmouseover="highlightFromLegend('12')"  style="background-color: #c0976b;"></td>
						</tr>
						<tr>
							<td class="legend-breaks legend-breaks-b-2"></td>
							<td class="legend-color legend-color-20" onmouseout="clearHighlight();" onmouseover="highlightFromLegend('20')"  style="background-color: #95c497;"></td>
							<td class="legend-color legend-color-21" onmouseout="clearHighlight();" onmouseover="highlightFromLegend('21')"  style="background-color: #95a865;"></td>
							<td class="legend-color legend-color-22" onmouseout="clearHighlight();" onmouseover="highlightFromLegend('22')"  style="background-color: #897c36;"></td>
						</tr>
						<tr>
							<td></td>
							<td class="legend-breaks legend-breaks-0"></td>
							<td class="legend-breaks legend-breaks-1"></td>
							<td class="legend-breaks legend-breaks-2"></td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
		<div onmousedown="exportToPDF('map');" class="btn btn-map-export btn-outline">Exportar mapa</div>
	</div>
	<section class="year-selector">
		<div class="container">
		</div>
	</section>
	<div class="filters-map">
		<div class="row">
			<div class="container">
				<div id="gm-chart"></div>
				<div id="chart-slider"></div>
				<div onmousedown="exportToPDF('gm');" class="btn btn-line-export btn-outline">Exportar gráfica</div>
			</div>
		</div>
	</div>
	<?php include('section_datatable.php'); ?>
</page>

<?php include('api_graphics.php'); ?>
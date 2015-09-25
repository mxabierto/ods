<section class="stats">
	<div class="container">
		<div style="margin-top: 30px;" class="col-xs-12">
			<div class="stat-column-header stat-column-header-chart">Indicador a nivel estatal</div>
			<div id="chart"></div>
			<div><img style="margin-left: 35px;" width=147 height=25 src='<?php echo path_to_theme().'/assets/';?>promedio.png'/></div>
			<div onmousedown="exportToPDF('line');" class="btn btn-line-export btn-outline">Exportar gráfica como PDF</div>
		</div>
		<div style="margin-top: 40px;" class="col-xs-12 col-sm-8">
			<div class="stat-column-header stat-column-header-top">Top 3 Municipios</div>
			<div class="top-municipios-group top-municipios-group-0">
				<div class="top-municipios-number">1</div>
				<div class="top-municipios-name">Cuernavaca</div>
				<div class="top-municipios-pct">83%</div>
			</div><div class="top-municipios-group top-municipios-group-1">
				<div class="top-municipios-number">3</div>
				<div class="top-municipios-name">Celaya</div>
				<div class="top-municipios-pct">81.5%</div>
			</div>
			<div class="top-municipios-group top-municipios-group-2">
				<div class="top-municipios-number">3</div>
				<div class="top-municipios-name">Huitzilac</div>
				<div class="top-municipios-pct">73.2%</div>
			</div>
		</div>
	</div>
</section>
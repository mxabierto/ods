<section class="stats">
	<div class="container">
		<div class="col-xs-12">
			<form style="display: none;" id="form-filter-entidad-mpal" class="form-inline" role="form">
				<div class="form-group form-entidad-mpal">
					<label class="filter-header">Filtrar por entidad federativa:</label>
					<select id="filter-entidad-mpal" class="filter-group">
						<option value="1">Aguascalientes</option>
						<option value="2">Baja California</option>
						<option value="3">Baja California Sur</option>
						<option value="4">Campeche</option>
						<option value="5">Coahuila</option>
						<option value="6">Colima</option>
						<option value="7">Chiapas</option>
						<option value="8">Chihuahua</option>
						<option value="9">Distrito Federal</option>
						<option value="10">Durango</option>
						<option value="11">Guanajuato</option>
						<option value="12">Guerrero</option>
						<option value="13">Hidalgo</option>
						<option value="14">Jalisco</option>
						<option value="15">Estado de México</option>
						<option value="16">Michoacán</option>
						<option value="17">Morelos</option>
						<option value="18">Nayarit</option>
						<option value="19">Nuevo León</option>
						<option value="20">Oaxaca</option>
						<option value="21">Puebla</option>
						<option value="22">Querétaro</option>
						<option value="23">Quintana Roo</option>
						<option value="24">San Luis Potosí</option>
						<option value="25">Sinaloa</option>
						<option value="26">Sonora</option>
						<option value="27">Tabasco</option>
						<option value="28">Tamaulipas</option>
						<option value="29">Tlaxcala</option>
						<option value="30">Veracruz</option>
						<option value="31">Yucatán</option>
						<option value="32">Zacatecas</option>
					</select>
				</div>
			</form>
		</div>
		<div style="margin-top: 30px;" class="col-xs-12">
			<div class="stat-column-header stat-column-header-chart">Indicador a nivel estatal</div>
			<div id="chart"></div>
			<div><img style="margin-left: 35px;" width=147 height=25 src='<?php echo path_to_theme().'/assets/';?>promedio.png'/></div>
			<div onmousedown="exportToPDF('line');" class="btn btn-line-export btn-outline">Exportar gráfica</div>
		</div>
		<div style="margin-top: 40px;" id="stat-tables" class="col-xs-12 col-sm-8">
			<div class="stat-column-header stat-column-header-top">Top 3 Municipios</div>
			<div class="top-municipios-group top-municipios-group-0">
				<div class="top-municipios-number">1</div>
				<div class="top-municipios-name">--</div>
				<div class="top-municipios-pct">--</div>
			</div><div class="top-municipios-group top-municipios-group-1">
				<div class="top-municipios-number">2</div>
				<div class="top-municipios-name">--</div>
				<div class="top-municipios-pct">--</div>
			</div>
			<div class="top-municipios-group top-municipios-group-2">
				<div class="top-municipios-number">3</div>
				<div class="top-municipios-name">--</div>
				<div class="top-municipios-pct">--</div>
			</div>
		</div>
	</div>
</section>
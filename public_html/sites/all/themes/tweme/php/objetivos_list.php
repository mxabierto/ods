<?php
	
include('h_objetivos.php');
	
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

?>

<div class="jumbotron-block col-xs-12 col-sm-12">
	<h4 style="font-weight: 700; color: #00cc99;">Agenda 2030 de Desarrollo Sostenible</h4>
	<h3>Objetivos de Desarrollo Sostenible</h3>
	<p>Iniciativa del Gobierno de la República, el Programa de las Naciones Unidas para el Desarrollo (PNUD) y la Agencia Mexicana de Cooperación Internacional para el Desarrollo (AMEXCID) a fin de poner a prueba un conjunto de indicadores de inclusión social y una plataforma piloto para el seguimiento de los Objetivos de Desarrollo Sostenible.</p>
	<p style="margin-top: 20px;">
		<span style="margin-right: 20px;"><img width=133 height=35 src="<?php echo path_to_theme(); ?>/assets/presidencia.png" /></span>
		<span style="margin-left: 20px;"><img width=26 height=42 src="<?php echo path_to_theme(); ?>/assets/pnud.png" /></span>
		<span style="margin-left: 20px;"><img width=142 height=37 src="<?php echo path_to_theme(); ?>/assets/amexcid.png" /></span>
	</p>
</div>
<?php
	echo('<div class="jumbotron-block col-xs-6">');
	$i = 0;
	foreach($indicadores as $key => $objetivo) {
		if ($i == 6) {
			echo("</div>");
			echo('<div class="jumbotron-block col-xs-6">');
		}
			echo ('<div class="col-xs-12 noselect indicador-group" value="'.$i.'"><div class="row indicador-row"><div class="col-xs-1"><img src="'.path_to_theme().'/assets/sdg_icons/'.$objetivo_icons[$key].'.png"/></div><div class="col-xs-11"><div class="objetivo-name"><strong>'.($i+1).'. </strong>'.$objetivo_nombres[$key].'</div></div></div><div style="display: none;" class="row listed-indicadores"><div class="listed-indicadores-title">INDICADORES</div>');
		foreach($objetivo as $indicador) {
			echo ( '<div class="listed-indicador"><div class="col-xs-1"></div><div class="col-xs-11">'.$indicador["Nombre_del_indicador"]."</div></div>" );
		}
		echo ('</div></div>');
		$i++;
	}
?>
</div>
<script type="text/javascript">
	(function ($) {
		$(".indicador-group").mousedown(function() {
			console.log($(this).val());
			$(".listed-indicadores").slideUp();
			$(this).find(".listed-indicadores").slideToggle();
		});
	}(jQuery));

	function visit_objetivo(n,i) {
		window.location.href='explora?o='+i;
	}
</script>
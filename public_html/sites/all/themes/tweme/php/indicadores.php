<div class="row">
	<div class="col-xs-12">
		Consulta datos de más de 100 indicadores correspondientes a los Objetivos de Desarrollo Sostenible.<br/>Primero selecciona un objetivo, y posteriormente elige un indicador:
</div>
</div>
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

$i = 0;
foreach($indicadores as $key => $objetivo) {
	echo ('<div class="row indicador-header"><div class="col-xs-1"><img src="'.path_to_theme().'/assets/sdg_icons/'.$objetivo_icons[$key].'.png"/></div><div class="col-xs-11"><h4><strong>'.($i+1).'.</strong> '.$objetivo_nombres[$key].'</h4></div></div>');
	foreach($objetivo as $indicador) {
		echo ( '<div class="row indicador-page-row" onmousedown="visit_indicador(\''.$i.'\',\''.$indicador["Clave"].'\')" ><div class="col-xs-1"></div><div class="col-xs-11">'.$indicador["Nombre_del_indicador"]."</div></div>" );
	}
	$i++;
}

?>
<script type="text/javascript">
	function visit_indicador(o,i) {
		window.location.href='explora?o='+o+'&i='+i;
	}
</script>
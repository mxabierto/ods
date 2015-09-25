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

foreach($indicadores as $key => $objetivo) {
	echo ('<div class="row indicador-header"><div class="col-xs-1"><img src="'.path_to_theme().'/assets/sdg_icons/'.$objetivo_icons[$key].'.png"/></div><div class="col-xs-11"><h4>'.$objetivo_nombres[$key].'</h4></div></div>');
	foreach($objetivo as $indicador) {
		echo ( '<div class="row"><div class="col-xs-1"></div><div class="col-xs-11">'.$indicador["Nombre_del_indicador"]."</div></div>" );
	}
}

?>
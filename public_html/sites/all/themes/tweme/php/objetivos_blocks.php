<?php
	
$ch = curl_init();
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_URL,"http://api.datos.gob.mx/v1/ods.metadata");
$result=curl_exec($ch);
curl_close($ch);

$metadata = json_decode($result, true);
$objetivos = array();
$indicadores = array();
foreach($metadata["results"] as $value) {
	if (!in_array($value["Nombre_del_objetivo"],$objetivos)) array_push($objetivos,$value["Nombre_del_objetivo"]);
	if (!array_key_exists($value["Nombre_del_objetivo"],$indicadores)) $indicadores[$value["Nombre_del_objetivo"]] = array();
	array_push($indicadores[$value["Nombre_del_objetivo"]],$value);
}

include('h_objetivos.php');
?>

<h3 style="font-weight: bold; text-align: center; margin-bottom: 50px;">Objetivos de Desarrollo Sostenible</h3>
<div class="objetivos-grid">
	<div class="col-xs-12">
		<?php for ($i = 0; $i < count($objetivos); $i++) {
			echo '<div class="col-xs-12 col-sm-2">';
			$img_tag = "<div><img src='".path_to_theme()."/assets/sdg_icons/".$objetivo_icons[$objetivos[$i]].".png'/></div>";
			echo '<div onmousedown="show_objetivo(\''.$objetivos[$i].'\','.$i.');" class="objetivo-button">'.$img_tag.'</div>';
			echo '</div>';
		}?>
	</div>
</div>

<div id="objetivo-indicadores-grid" class="col-xs-12" style="display: none;">
	<div class="objetivo-indicadores-grid-header">
		<div class="objetivo-icon"></div>
		<div class="objetivo-number">OBJETIVO 1</div>
		<div class="objetivo-name">Name</div>
		<div onmousedown="hide_objetivo();" class="close-x"><img width=24 height=24 src="<?php echo path_to_theme(); ?>/assets/close-x.png" /></div>
	</div>
	<div class="objetivo-indicadores"></div>
</div>

<script type="text/javascript">
	
	function commaSeparateNumber(x){var parts=x.toString().split(".");parts[0]=parts[0].replace(/\B(?=(\d{3})+(?!\d))/g,",");return parts.join(".");}
	
	(function ($) {
		$(".objetivo-button").matchHeight();
	}(jQuery));
	var objetivo_nombres = <?php echo json_encode($objetivo_nombres); ?>;
	var indicadores_by_objetivo = <?php echo json_encode($indicadores); ?>;
	
	function hide_objetivo() {
		(function ($) {
			$('#objetivo-indicadores-grid').hide();
			$('.objetivos-grid').show();
		}(jQuery));
	}
	function show_objetivo(nom,num) {
		(function ($) {
			indicadores = indicadores_by_objetivo[nom];
			$(".objetivo-indicadores").empty();
			$.each(indicadores, function (obj,ind) {
				$.getJSON("http://api.datos.gob.mx/v1/ods.datos", { id: ind.Clave, pageSize: 999999, DesGeo: "N" }, function (data) {
					greatest_year = 0;
					selected_key = 0;
					$.each(data.results, function (k,result) {
						if (result.t > greatest_year) {
							greatest_year = result.t;
							selected_key = k;
						}
					});
					$(".objetivo-indicadores").append("<div class='col-xs-12 col-sm-4'><table class='objetivo-grid-block'><td class='objetivo-grid-value col-xs-3'>"+commaSeparateNumber(Math.round(data.results[selected_key].valor*10)/10)+"</td><td class='objetivo-grid-name col-xs-8'>"+ind["Nombre_del_indicador"]+" ("+data.results[selected_key].t+")</td><div style='clear:both;'></div></table></div>");
					$(".objetivo-indicadores .col-xs-4").matchHeight();
					$(".objetivo-indicadores .objetivo-grid-block").matchHeight();
				});
			});
			$('.objetivos-grid').hide();
			$('#objetivo-indicadores-grid').show();
			$(".objetivo-number").html("OBJETIVO " + (num+1));
			$(".objetivo-name").html(objetivo_nombres[nom]);
		}(jQuery));
	}
</script>
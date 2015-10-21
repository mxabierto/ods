<?php
	require(path_to_theme().'/php/simple_html_dom.php');
	drupal_add_css(path_to_theme().'/js/jquery.magnific-popup.css');
	drupal_add_js(path_to_theme().'/js/jquery.magnific-popup.min.js');
	
	function get_data($url) {
		$ch = curl_init();
		$timeout = 5;
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		$data = curl_exec($ch);
		curl_close($ch);
		return $data;
	}
	
?>
<script type="text/javascript">

	var objetivos_iconos = {
		"Poner fin a la pobreza en todas sus formas en todo el mundo": "pobreza",
		"Poner fin al hambre, lograr la seguridad alimentaria y la mejora de la nutrición y promover la agricultura sostenible": "hambre",
		"Garantizar una educación inclusiva, equitativa y de calidad y promover oportunidades de aprendizaje durante toda la vida para todos": "educacion",
		"Reducir la desigualdad en y entre los países": "desigualdad",
		"Construir infraestructura resiliente, promover la industrialización inclusiva y sostenible y fomentar la innovación": "infraestructura",
		"Lograr la igualdad entre los géneros y el empoderamiento de todas las mujeres y niñas": "igualdad_generos",
		"Garantizar una vida sana y promover el bienestar para todos en todas las edades": "salud",
		"Lograr que las ciudades y los asentamientos humanos sean inclusivos, seguros, resilientes y sostenibles": "ciudades",
		"Garantizar la disponibilidad de agua y su ordenación sostenible y el saneamiento para todos": "agua",
		"Garantizar el acceso a una energía asequible, segura, sostenible y moderna para todos": "energia",
		"Promover el crecimiento económico sostenido, inclusivo y sostenible, el empleo pleno y productivo y el trabajo decente para todos": "economia"
	};
		
</script>
<?php print render($page['body_top']) ?>
<?php 
	$html = file_get_html('https://raw.githubusercontent.com/mxabierto/dgm-navbar/master/dgm-navbar.html');
	foreach($html->find('dom-module') as $element)
		$element = str_replace(array("<template>","</template>","@import url('http://fonts.googleapis.com/css?family=Open+Sans:200+400');",'<script src="https://code.jquery.com/jquery-2.1.4.min.js"></script>','font-family:"Helvetica Neue",Helvetica,Arial,sans-serif;',"font-family:sans-serif;"), "", $element);
		$element = str_replace(array(":host"), "header#dgm-navbar", $element);
    	echo str_replace("dom-module", "header", $element);
?>
<header class="header">
  <div class="jumbotron">
	<?php if (drupal_is_front_page()): ?>
	<video autoplay loop poster='<?php echo path_to_theme().'/assets/';?>mapa.jpg' id="bgvid">
	    <source src='<?php echo path_to_theme().'/assets/';?>mapa.mp4' type="video/mp4">
	</video>
	<?php endif; ?>
    <div class="container">
      <?php print $breadcrumb ?>
      <?php print render($title_prefix) ?>
      <div class="col-xs-12">
      	<?php print render($primary_nav) ?>
      </div>
      <?php if (current_path() == "node/1"): ?>
      <div class="col-xs-12 col-sm-6 page-caption">
	      Esta sección permite visualizar los indicadores de los cuales se dispone información para 11 de los 17 objetivos de la Agenda 2030 para el Desarrollo Sostenible. Igualmente ofrece la posibilidad de filtrar la información por tipo de desagregación y unidades territoriales menores, en el caso de que ésta se encuentre disponible, y exportarla para su manipulación al igual que los materiales gráficos que se generen por el usuario.
      </div>
      <?php endif; ?>
      <?php if (current_path() == "node/8"): ?>
      <div class="col-xs-12 col-sm-6 page-caption">
	  	Esta sección permite llevar a cabo el cruce de dos indicadores seleccionados por el usuario, a fin de que pueda analizar de manera sencilla y visual la correlación existente entre dos variables. Igualmente se puede llevar a cabo un seguimiento del comportamiento de los dos indicadores seleccionados en el tiempo por medio de un gráfico y exportar la información, mapas y gráficos para su uso.</div>
      <?php endif; ?>
      <?php print render($title_suffix) ?>
      <?php print render($page['header']) ?>
    </div>
  </div>
  <div class="header-bottom">
    <div class="container">
      <?php if (!empty($action_links)): ?>
      <ul class="action-links pull-right">
        <?php print render($action_links) ?>
      </ul>
      <?php endif ?>
      <?php print render($tabs) ?>
    </div>
  </div>
</header>

<?php if (!empty($page['content_noncontainer'])): ?>
<?php print render($page['content_noncontainer']) ?>
<?php endif ?>

<?php if (!empty($page['content'])): ?>
<section class="main">
  <div class="container">
    <div class="row">
      <?php $_content_cols = 12 - 3 * !empty($page['sidebar_first']) - 3 * !empty($page['sidebar_second']) ?>
      <section class="main-col col-md-<?php print $_content_cols  ?><?php print !empty($page['sidebar_first']) ? ' col-md-push-3' : '' ?>">
        <?php print $messages ?>
        <?php print render($page['help']) ?>
        <?php print render($page['highlighted']) ?>
        <?php print render($page['content']) ?>
      </section>
      <?php if (!empty($page['sidebar_first'])): ?>
      <aside class="main-col col-md-3 col-md-pull-<?php print $_content_cols  ?>">
        <?php print render($page['sidebar_first']) ?>
      </aside>
      <?php endif ?>
      <?php if (!empty($page['sidebar_second'])): ?>
      <aside class="main-col col-md-3">
        <?php print render($page['sidebar_second']) ?>
      </aside>
      <?php endif ?>
    </div>
  </div>
</section>
<?php endif ?>

<?php if (!empty($page['section_button_left']) || !empty($page['section_button_right'])): ?>
<section class="section-buttons">
	<div class="container">
		<div class="row">
			<section class="col-xs-12 col-sm-6"><?php print render($page['section_button_left']) ?></section>
			<section class="col-xs-12 col-sm-6"><?php print render($page['section_button_right']) ?></section>
		</div>
	</div>
</section>
<?php endif ?>

<?php if (!empty($page['blog'])): ?>
<section class="objetivos-blocks">
	<div class="container">
		<?php print render($page['blog']) ?>
	</div>
</section>
<?php endif ?>


<?php print render($page['share']) ?>

<?php if ($page['bottom']): ?>
<section class="bottom">
  <div class="container">
    <?php print render($page['bottom']) ?>
  </div>
</section>
<?php endif ?>

<?php 
	$html = file_get_html('https://raw.githubusercontent.com/mxabierto/dgm-footer/master/dgm-footer.html');
	foreach($html->find('dom-module') as $element)
		$element = str_replace(array("<template>","</template>","font-family:sans-serif;",'font-family:"Helvetica Neue",Helvetica,Arial,sans-serif;',"@import url('http://fonts.googleapis.com/css?family=Open+Sans:200+400');"), "", $element);
		$element = str_replace(array(":host"), "footer", $element);
    	echo str_replace("dom-module", "footer", $element);
?>

<?php print render($page['body_bottom']) ?>

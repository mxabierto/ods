<?php
	drupal_add_css(path_to_theme().'/js/jquery.magnific-popup.css');
	drupal_add_js(path_to_theme().'/js/jquery.magnific-popup.min.js');
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
<div class="site-header">
<header class="site-header-bottom" role="banner" aria-label="Encabezado del sitio">
    <div class="container">
      <div class="row">
        <div class="col col-xs-12">
          <div class="col-inner">
            <a class="site-brand" href="http://datos.gob.mx/" title="Ir a la página de inicio de DATOS.GOB.MX">
              <img src="http://datos.gob.mx/assets/svg/logo-dgm-white.svg" alt="DATOS.GOB.MX" role="presentation">
            </a>
            <div class="nav-burguer" onmousedown='toggle_burguer()' aria-hidden="true"><span></span></div>
            <script type="text/javascript">
	            function toggle_burguer() {
		            (function ($) {
	            	$("ul.site-navigation").slideToggle();
	            	}(jQuery));
				}
	        </script>
            <ul class="site-navigation" aria-label="Navegación principal" role="navigation">
              <li>
                <a class="page-link" href="http://datos.gob.mx/catalogo/" title="Ir al conjunto de datos" aria-label="Ir al catalogo de datos">Datos</a>
              </li>
              <li>
                <a class="page-link" href="http://datos.gob.mx/guia/" title="Ir a la guía de datos" aria-label="Ir a la guía de datos">Guía</a>
              </li>
              
              
                
                  <li>
                    <a class="page-link" href="http://datos.gob.mx/historias/" aria-label="Conoce las historias con Datos">Historias</a>
                  </li>
                
              
                
                  <li>
                    <a class="page-link" href="http://datos.gob.mx/apps/" aria-label="">Apps</a>
                  </li>
                
              
                
                  <li>
                    <a class="page-link" href="http://datos.gob.mx/herramientas/" aria-label="">Herramientas</a>
                  </li>
                
              
                
                  <li>
                    <a class="page-link" href="http://datos.gob.mx/avances/" aria-label="">Avances</a>
                  </li>
                
              
                
                  <li>
                    <a class="page-link" href="http://datos.gob.mx/acerca/" aria-label="Conoce más sobre este sitio">Acerca</a>
                  </li>
                
              
            </ul>
          </div>
        </div>
      </div>
    </div>
  </header>
</div>
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
      <div class="col-xs-12 col-xs-6 page-caption">
	      Esta sección permite visualizar los indicadores de los cuales se dispone información para 11 de los 17 objetivos de la Agenda 2030 para el Desarrollo Sostenible. Igualmente ofrece la posibilidad de filtrar la información por tipo de desagregación y unidades territoriales menores, en el caso de que ésta se encuentre disponible, y exportarla para su manipulación al igual que los materiales gráficos que se generen por el usuario.
      </div>
      <?php endif; ?>
      <?php if (current_path() == "node/8"): ?>
      <div class="col-xs-12 col-xs-6 page-caption">
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

<footer class="site-footer" role="contentinfo" aria-label="Pie de página del sitio">
  <div class="container">
    <div class="col-xs-12">
      <div class="col col-xs-12 col-sm-3">
        <div class="col-inner">
          <div class="footer-section">
            <ul class="open-knowledge-list" aria-label="Enlaces legales" role="navigation"> 
              <li class="footer-libre-">
                <a href="/libreusomx" title="Libre Uso MX">
                  <i class="i libre-uso-white"></i>
                </a>
              </li>
              <li>
                <a href="http://opendefinition.org/" target="_blank">
                  <img alt="Este material es de Conocimiento Abierto" src="http://assets.okfn.org/images/ok_buttons/ok_80x15_blue.png" style="margin-top: 5px;">
                </a>
              </li>
              <li>
                <a href="https://github.com/mxabierto/dgm" title="Repositorio GitHub" target="_blank">
                  <img src="http://datos.gob.mx/assets/svg/icon-octocat.svg" alt="Github">
                </a>
              </li>
            </ul>
          </div>
        </div>
      </div>
      <div class="col-xs-12 col-sm-3">
        <div class="col-inner">
          <div class="footer-section">
            <a href="http://datos.gob.mx/catalogo" class="footer-section-title">Datos</a>
            <ul class="section-links-list">
              <li><a href="http://datos.gob.mx/acerca/">Acerca de</a></li>
              <li><a href="http://adela.datos.gob.mx/">ADELA</a></li>
              <li><a href="http://datos.gob.mx/guia">Guía de implementación</a></li>
              <li>
                <a class="page-link" href="http://datos.gob.mx/terminos-y-condiciones">Términos y condiciones</a>
              </li>
              <li>
                <a class="page-link" href="http://datos.gob.mx/privacidad">Aviso de Privacidad</a>
              </li>
            </ul>
          </div>
        </div>
      </div>
      <div class="col col-xs-12 col-sm-2">
        <div class="col-inner">
          
          <div class="footer-section">
            <a href="http://datos.gob.mx/historias/" class="footer-section-title">Historias</a>
            <ul class="section-links-list">
              
                
              
                
                <li>
                  <a href="http://datos.gob.mx/historias/salud/" aria-hidden="true" tabindex="-1">Salud</a>  
                </li>
                
              
                
                <li>
                  <a href="http://datos.gob.mx/historias/geografia/" aria-hidden="true" tabindex="-1">Geografia</a>  
                </li>
                
              
                
                <li>
                  <a href="http://datos.gob.mx/historias/seguridad/" aria-hidden="true" tabindex="-1">Seguridad</a>  
                </li>
                
              
                
                <li>
                  <a href="http://datos.gob.mx/historias/medio-ambiente/" aria-hidden="true" tabindex="-1">Ambiente</a>  
                </li>
                
              
                
                <li>
                  <a href="http://datos.gob.mx/historias/educacion/" aria-hidden="true" tabindex="-1">Educación</a>  
                </li>
                
              
                
                <li>
                  <a href="http://datos.gob.mx/historias/economia/" aria-hidden="true" tabindex="-1">Economia</a>  
                </li>
                
              
                
                <li>
                  <a href="http://datos.gob.mx/historias/otras/" aria-hidden="true" tabindex="-1">Otras</a>  
                </li>
                
                
            </ul>
          </div>
        </div>
      </div>
      <div class="col col-xs-12 col-sm-2">
        <div class="col-inner">
          
          <div class="footer-section">
            <a href="http://datos.gob.mx/apps/" class="footer-section-title">Apps</a>
            <ul class="section-links-list">
              
                
              
                
                <li>
                  <a href="http://datos.gob.mx/apps/movil/" aria-hidden="true" tabindex="-1">Movil</a>  
                </li>
                
              
                
                <li>
                  <a href="http://datos.gob.mx/apps/web/" aria-hidden="true" tabindex="-1">Web</a>  
                </li>
                
                
            </ul>
          </div>
          <div class="footer-section">
            <a href="http://datos.gob.mx/herramientas/" class="footer-section-title">Herramientas</a>
          </div>
          <div class="footer-section">
            <a href="http://foro.datos.gob.mx/" class="footer-section-title" target="_blank">Foro</a>
          </div>
        </div>
      </div>
      <div class="col col-xs-12 col-sm-2">
        <div class="col-inner">
          <div class="footer-section">
            <a href="http://datos.gob.mx/avances/" class="footer-section-title">Avances</a>
            
            <ul class="section-links-list">
              
                
              
                
                  <li>
                    <a href="http://datos.gob.mx/avances/eventos/" tabindex="-1">Eventos</a>  
                  </li>
                
              
                
                  <li>
                    <a href="http://datos.gob.mx/avances/noticias/" tabindex="-1">Noticias</a>  
                  </li>
                
              
                
                  <li>
                    <a href="http://datos.gob.mx/avances/politica/" tabindex="-1">Política</a>  
                  </li>
                
              
                
                  <li>
                    <a href="http://datos.gob.mx/avances/consejo-consultivo/" tabindex="-1">Consejo Consultivo</a>  
                  </li>
                
              
                
                  <li>
                    <a href="http://datos.gob.mx/avances/redmxabierto/" tabindex="-1">Red México Abierto</a>  
                  </li>
                
              
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
</footer>

<?php print render($page['body_bottom']) ?>

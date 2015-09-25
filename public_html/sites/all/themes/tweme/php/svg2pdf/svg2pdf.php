<?php
	
$id = uniqid();
file_put_contents("tmp/".$id.".svg", file_get_contents("php://input"));

exec("rsvg-convert -f pdf -o tmp/".$id.".pdf tmp/".$id.".svg");

echo("/sites/all/themes/tweme/php/svg2pdf/tmp/".$id.".pdf");

?>
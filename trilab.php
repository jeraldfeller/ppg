<?php
require '../simple_html_dom.php';
$url = 'https://www.trilab.it/catalogsearch/result/?q=tecniart+%28L%27Oreal%29';
$html = file_get_contents($url);

$myfile = fopen("tri.html", "w") or die("Unable to open file!");
fwrite($myfile, $html);
fclose($myfile);

<?php
require 'Model/Init.php';
require 'Model/Main.php';
require 'simple_html_dom.php';
$main = new Main();
$brands = json_decode($main->getBrands(), true);
$siteId = 3;
$domain = 'http://www.kepro.it';
foreach($brands as $row){
  $brandId = $row['id'];
  $brand = $row['brand_name'];
  $brandUrl = str_replace(' ', '+', urlencode($row['brand_name']));
  $url = "http://www.kepro.it/cerca?chiave=$brandUrl&x=0&y=0";
  $html = file_get_html($url, false);
  $cont = $html->find('#listaarticoli', 0);
  $list = $cont->find('.product-tile-m');

  for($x = 0; $x < count($list); $x++){
    $productName = $list[$x]->find('.pr-name', 0)->plaintext;
    $productUrl = $domain.$list[$x]->find('.pr-name', 0)->find('a',0)->getAttribute('href');
    $price = $list[$x]->find('.price-box', 0)->plaintext;
    $price = preg_replace("/[^0-9,.]/", "", $price);
    $data = array(
              'brandId' => $brandId,
              'siteId' => $siteId,
              'productName' => trim($productName),
              'productUrl' => trim($productUrl),
              'price' => trim($price)
            );
    $main->recordData($data);
  }
}

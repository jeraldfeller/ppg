<?php
require '/var/www/html/ppg/ppg/Model/Init.php';
require '/var/www/html/ppg/ppg/Model/Main.php';
require '/var/www/html/ppg/ppg/simple_html_dom.php';
$main = new Main();
$brands = json_decode($main->getBrands(), true);
$siteId = 5;
foreach($brands as $row){
  $brandId = $row['id'];
  $brand = $row['brand_name'];
  $brandUrl = str_replace(' ', '+', urlencode($row['brand_name']));
  $p = 1;
  $hasNextPage = true;
  while($hasNextPage == true){
    $url = "https://www.stylosophy-shop.it/index.php?route=product/search&search=$brandUrl&limit=100&page=$p";
    $html = file_get_html($url, false);
    $list = $html->find('.product-thumb');
    if($list){
      for($x = 0; $x < count($list); $x++){
        $productName = $list[$x]->find('.name', 0)->plaintext;
        $productUrl = $list[$x]->find('.name', 0)->find('a', 0)->getAttribute('href');
        $price = $list[$x]->find('.price', 0)->find('.price-new', 0);
        if($price){
          $price = $price->plaintext;
          $price = preg_replace("/[^0-9,.]/", "", $price);
        }else{
          $price = $list[$x]->find('.price', 0)->plaintext;
          $price = preg_replace("/[^0-9,.]/", "", $price);
        }
        $data = array(
                  'brandId' => $brandId,
                  'siteId' => $siteId,
                  'productName' => trim($productName),
                  'productUrl' => trim($productUrl),
                  'price' => trim($price)
                );
        $main->recordData($data);

      }
    }else{
      $hasNextPage = false;
    }
    $p++;
  }
}

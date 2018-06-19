<?php
require 'Model/Init.php';
require 'Model/Main.php';
require 'simple_html_dom.php';
$main = new Main();
$brands = json_decode($main->getBrands(), true);
$siteId = 4;
foreach($brands as $row){
  $brandId = $row['id'];
  $brand = $row['brand_name'];
  $brandUrl = str_replace(' ', '+', urlencode($row['brand_name']));
  $p = 1;
  $hasNextPage = true;
  while($hasNextPage == true){
    $url = "https://www.planethair.it/?subcats=Y&pcode_from_q=Y&pshort=Y&pfull=Y&pname=Y&pkeywords=Y&search_performed=Y&q=$brandUrl&dispatch=products.search&page=$p";
    $htmlData = $main->curlTo($url);
    $html = str_get_html($htmlData['html']);
    $forms = $html->find('form.cm-ajax-full-render');
    if($forms){
      for($x = 0; $x < count($forms); $x++){
        $productName = $forms[$x]->find('.product-title', 0)->plaintext;
        $productUrl = $forms[$x]->find('.product-title', 0)->getAttribute('href');
        $price = $forms[$x]->find('.ty-price-num', 1)->plaintext;
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
    }else{
      $hasNextPage = false;
    }

    $p++;
  }

}

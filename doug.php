<?php
require 'Model/Init.php';
require 'Model/Main.php';
require 'simple_html_dom.php';
$main = new Main();
$brands = json_decode($main->getBrands(), true);
$siteId = 2;
$domain = 'https://www.douglas.it';
foreach($brands as $row){
    $brandId = $row['id'];
    $brand = $row['brand_name'];
    $brandUrl = str_replace(' ', '+', urlencode($row['brand_name']));
    $p = 1;
    $hasNextPage = true;
    while($hasNextPage == true){
      /*
      if($p == 2){
        break;
      }*/
      $url = "https://www.douglas.it/search.html?page=$p&query=$brandUrl";
      $html = file_get_html($url, false);
      $scripts =  $html->find('script');
      $overview = $html->find('.data-rd-product-overview', 0);
        $htmlAsText = $html->innertext;
        if(strpos($htmlAsText, 'document.productOverviewData') !== false) {
          $firstString = substr($htmlAsText, strpos($htmlAsText, "document.productOverviewData = "));
          $trimText = substr($firstString, 0, strpos($firstString, "</script>"));
          $cleanText = trim(str_replace(';', '', str_replace('document.productOverviewData = ', '', $trimText)));
          $data = json_decode($cleanText, true);
          $pagination = $data['pagination'];
          if( $p <= $pagination['pageCount'] ){
            $products = $data['products'];
            foreach($products as $prod){
              $productName = $prod['line'] . ' ' . (isset($prod['name']) ? $prod['name'] : '');
              $productUrl = $domain.$prod['href'];
              $price = (isset($prod['redPrice']) ? $prod['redPrice']['price'] : $prod['whitePrice']['price']);
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
        }else{
          $hasNextPage = false;
        }
      $p++;
    }

}

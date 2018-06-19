<?php
require '/var/www/html/ppg/ppg/Model/Init.php';
require '/var/www/html/ppg/ppg/Model/Main.php';
require '/var/www/html/ppg/ppg/simple_html_dom.php';
$main = new Main();
$brands = json_decode($main->getBrands(), true);
$siteId = 1;
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
      $url = "https://www.tigota.it/it/advancedsearch/result/index/?p=$p&q=$brandUrl";
      $htmlData = $main->curlTo($url);
      $html = str_get_html($htmlData['html']);

      $pages = $html->find('.pages', 0);
      if($pages){
        $pageOl = $pages->find('ol', 0);
        $pageOlLi = $pageOl->find('li');
        $lastPage = trim($pageOlLi[count($pageOlLi) - 1]->plaintext);
        if($lastPage == ''){
          $lastPage = trim($pageOlLi[count($pageOlLi) - 2]->plaintext);
        }
      }else{
        $lastPage = 1;
      }
      if($lastPage >= $p){
        if($html){
          $ul = $html->find('.products-grid', 0);
          if($ul){
            $list = $ul->find('li');
            if($list){

              for($x = 0; $x < count($list); $x++){
                $productName = $list[$x]->find('.product-name', 0)->find('a', 0)->plaintext;
                $productUrl = $list[$x]->find('.product-name', 0)->find('a', 0)->getAttribute('href');
                $price = trim($list[$x]->find('.price', 0)->plaintext);
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
        }else{
          $hasNextPage = false;
        }
      }else{
        $hasNextPage = false;
      }
      $p++;
    }

}


/*
$url = 'https://www.tigota.it/it/advancedsearch/result/index/?p=1&q=biopoint';
$html = file_get_html($url, false);
$ul = $html->find('.products-grid', 0);
$list = $ul->find('li');
$pages = $html->find('.pages', 0);
$pageOl = $pages->find('ol', 0);
$pageOlLi = $pageOl->find('li');
$pageCount = count($pageOlLi) - 1;
var_dump($pageCount);
*/

/*
for($x = 0; $x < count($list); $x++){
  $productName = $list[$x]->find('.product-name', 0)->plaintext;
  echo $productName . '<br>';
}
*/
?>

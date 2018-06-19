<?php
class Main
{
    public $debug = TRUE;
    protected $db_pdo;

    public function pdoQuoteValue($value)
    {
        $pdo = $this->getPdo();
        return $pdo->quote($value);
    }


    public function getBrands(){
      $pdo = $this->getPdo();
      $sql = 'SELECT * FROM `brand`';
      $stmt = $pdo->prepare($sql);
      $stmt->execute();
      $content = array();
      while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      //  if($row['id'] == 2){
          $content[] = $row;
      //  }
      }

      return json_encode($content);
    }

    public function recordData($data){
      $pdo = $this->getPdo();
      $isMatch = $this->getProductByMatch($data['brandId'], $data['siteId'], $data['productName']);
      if($isMatch){
        $id = $isMatch['id'];
      }else{
        $sql = 'INSERT INTO `product_name`
                SET `brand_id` = '.$data['brandId'].',
                `site_id` = '.$data['siteId'].',
                `product_url` = "'.$data['productUrl'].'",
                `product_name` = "'.$data['productName'].'"
                ';
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $id = $pdo->lastInsertId();
      }

      $sql = 'INSERT INTO `product_prices` SET `product_id` = '.$id.',
              `price` = "'.$data['price'].'",
              `currency` = "EUR"';

      $stmt = $pdo->prepare($sql);
      $stmt->execute();


      return true;

    }

    public function getProductByMatch($brandId, $siteId, $productName){
        $pdo = $this->getPdo();
        $sql = 'SELECT `id` FROM `product_name` WHERE `product_name` = "'.$productName.'" AND `brand_id` = '.$brandId.' AND `site_id` = '.$siteId.'';
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function curlTo($url){
      $proxy = '213.184.109.142';
      $port = 43848;
      $curl = curl_init();
  //    curl_setopt($curl, CURLOPT_TIMEOUT, 20);
  //    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
      curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
      curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
    //  curl_setopt($curl, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
  //    curl_setopt($curl, CURLOPT_PROXY, $proxy);
  //    curl_setopt($curl, CURLOPT_PROXYPORT, $port);
  //    curl_setopt($curl, CURLOPT_PROXYUSERPWD, 'amznscp:dfab7c358');
      curl_setopt($curl, CURLOPT_URL, $url);
      $content = curl_exec($curl);

      return array('html' => $content);

    }


    public function getPdo()
    {
        if (!$this->db_pdo)
        {
            if ($this->debug)
            {
                $this->db_pdo = new PDO(DB_DSN, DB_USER, DB_PWD, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
            }
            else
            {
                $this->db_pdo = new PDO(DB_DSN, DB_USER, DB_PWD);
            }
        }
        return $this->db_pdo;
    }
  }

?>

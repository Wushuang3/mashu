<?php

$postXml = $_REQUEST;
if($postXml==null){
    $postXml = file_get_contents("php://input");
}

if (empty($postXml)) {
    return false;
}

//将xml格式转换成数组
/*
$postXml = <<<EOF
<xml><appid><![CDATA[wx62f4cad175ad0f90]]></appid>
<attach><![CDATA[test]]></attach>
<bank_type><![CDATA[ICBC_DEBIT]]></bank_type>
<cash_fee><![CDATA[1]]></cash_fee>
<fee_type><![CDATA[CNY]]></fee_type>
<is_subscribe><![CDATA[N]]></is_subscribe>
<mch_id><![CDATA[1499579162]]></mch_id>
<nonce_str><![CDATA[963b42d0a71f2d160b3831321808ab79]]></nonce_str>
<openid><![CDATA[o9coS0eYE8pigBkvSrLfdv49b8k4]]></openid>
<out_trade_no><![CDATA[019493b4d81bd1db1486]]></out_trade_no>
<result_code><![CDATA[SUCCESS]]></result_code>
<return_code><![CDATA[SUCCESS]]></return_code>
<sign><![CDATA[17BEF985A5CB7364FC11A7B495AE61F6]]></sign>
<time_end><![CDATA[20180624122501]]></time_end>
<total_fee>1</total_fee>
<trade_type><![CDATA[JSAPI]]></trade_type>
<transaction_id><![CDATA[4200000146201806242438472701]]></transaction_id>
</xml>
EOF;
*/
function xmlToArray($xml) {
    //禁止引用外部xml实体
    libxml_disable_entity_loader(true);
    $xmlstring = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
    $val = json_decode(json_encode($xmlstring), true);
    return $val;
}

$post_data = xmlToArray($postXml);

$url = 'http://'.$_SERVER['HTTP_HOST'].'/api/v1/booking/wxNotify';
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
curl_setopt($curl,CURLOPT_SSLVERSION,CURL_SSLVERSION_TLSv1);
curl_setopt($curl, CURLOPT_POST, 1);
curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
$output = curl_exec($curl);
curl_close($curl);
echo $output;
?>

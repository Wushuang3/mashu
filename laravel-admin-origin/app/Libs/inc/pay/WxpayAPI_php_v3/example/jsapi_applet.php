<?php 
require_once("inc/pay/WxpayAPI_php_v3/lib/WxPay.Api.php");//echo 111;
require_once("inc/pay/WxpayAPI_php_v3/example/WxPay.JsApiPay.php");
require_once("inc/pay/WxpayAPI_php_v3/example/log.php");
global $db,$request,$order;
$comId = $order->comId;
$weixin_set = $db->get_row("select status,info from demo_kehu_pay where comId=$comId and type=3 limit 1");
if(empty($weixin_set)||$weixin_set->status==0||empty($weixin_set->info)){
	return '{"code":0,"message":"微信配置信息有误"}';
}
$weixin_arr = json_decode($weixin_set->info);
define('WX_APPID',$weixin_arr->appid);
define('WX_MCHID',$weixin_arr->mch_id);
define('WX_KEY',$weixin_arr->key);
define('WX_APPSECRET',$weixin_arr->appsecret);
//初始化日志
$logHandler= new CLogFileHandler("inc/pay/WxpayAPI_php_v3/logs/".date('Y-m-d').'.log');
$log = Log::Init($logHandler, 15);

//echo 3333;
//①、获取用户openid
$tools = new JsApiPay();
$openId = $db->get_var("select unionid from users where id=$order->userId");
if(empty($openId)){
	return '{"code":0,"message":"获取不到会员的openId"}';
}
$product_json = json_decode($order->product_json);
foreach ($product_json as $pdt) {
	$subject.=','.$pdt->title.'*'.$pdt->num;
}
$body = substr($subject,1);
$subject = sys_substr($body,30,true);
$subject = str_replace('_','',$subject).'_'.$comId;
$pay_price = round($order->price*100);

$dtTime = date("YmdHis",strtotime($order->dtTime));
$expireTime = date("YmdHis", time() + 60*60*24);

//②、统一下单
$input = new WxPayUnifiedOrder();
$input->SetBody($subject);
$input->SetAttach($comId);//自定义数据
$input->SetOut_trade_no($order->orderId);
$input->SetTotal_fee($pay_price);
$input->SetTime_start(date("YmdHis"));
$input->SetTime_expire($expireTime);
$input->SetGoods_tag($subject);
$input->SetNotify_url("http://".$_SERVER['HTTP_HOST']."/notify_applet.php");
$input->SetTrade_type("JSAPI");
$input->SetOpenid($openId);
/*if($comId==755){
	file_put_contents('request.txt',serialize($input));
}*/
$orders = WxPayApi::unifiedOrder($input);
if($orders['err_code']){
	return '{"code":0,"message":"'.$orders['err_code'].':'.$orders['err_code_des'].'"}';
}
$resultData = json_decode($tools->GetJsApiParameters($orders));
$return = array();
$return['code'] = 1;
$return['message'] = '';
$return['data'] = array();
$return['data']['appId'] = $resultData->appid;
$return['data']['timeStamp'] = $resultData->timeStamp;
$return['data']['nonceStr'] = $resultData->nonceStr;
$return['data']['package'] = $resultData->package;
$return['data']['signType'] = $resultData->signType;
$return['data']['paySign'] = $resultData->paySign;
return json_encode($return,JSON_UNESCAPED_UNICODE);

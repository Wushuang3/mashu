<?php 
require_once("inc/pay/WxpayAPI_php_v3/lib/WxPay.Api.php");//echo 111;
require_once("inc/pay/WxpayAPI_php_v3/example/WxPay.JsApiPay.php");
require_once("inc/pay/WxpayAPI_php_v3/example/log.php");
global $db,$request;
$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
$comId = (int)$_SESSION['demo_comId'];
$weixin_set = $db->get_row("select status,info from demo_kehu_pay where comId=$comId and type=1 limit 1");
if(empty($weixin_set)||$weixin_set->status==0||empty($weixin_set->info)){
	die('微信配置信息有误');
}
$money = $request['money'];
$orderId = date("YmdHis").rand(100000,999999);
$chongzhi = array();
$chongzhi['comId'] = $comId;
$chongzhi['userId'] = $userId;
$chongzhi['type'] = 2;
$chongzhi['money'] = $money;
$chongzhi['orderId'] = $orderId;
$db->insert_update('user_chongzhi',$chongzhi,'id');
$weixin_arr = json_decode($weixin_set->info);
define('WX_APPID',$weixin_arr->appid);
define('WX_MCHID',$weixin_arr->mch_id);
define('WX_KEY',$weixin_arr->key);
define('WX_APPSECRET',$weixin_arr->appsecret);
//初始化日志
$logHandler= new CLogFileHandler("inc/pay/WxpayAPI_php_v3/logs/".date('Y-m-d').'.log');
$log = Log::Init($logHandler, 15);

//file_put_contents('cccccc.txt',serialize($_SESSION));exit;
//打印输出数组信息
function printf_info($data)
{
	foreach($data as $key=>$value){
		echo "<font color='#00ff55;'>$key</font> : $value <br/>";
	}
}

//echo 3333;
//①、获取用户openid
$tools = new JsApiPay();
$openId = $db->get_var("select openId from users where id=".(int)$_SESSION[TB_PREFIX.'user_ID']);
if(empty($openId)){
	$openId = $tools->GetOpenid();
	$db->query("update users set openId='$openId' where id=".(int)$_SESSION[TB_PREFIX.'user_ID']);
}
$body = substr($subject,1);
$subject = '余额充值';
$pay_price = round($money*100);

$dtTime = date("YmdHis");
$expireTime = date("YmdHis", time() + 60*60*24);

//②、统一下单
$input = new WxPayUnifiedOrder();
$input->SetBody($subject);
$input->SetAttach($comId);//自定义数据
$input->SetOut_trade_no($orderId);
$input->SetTotal_fee($pay_price);
$input->SetTime_start(date("YmdHis"));
$input->SetTime_expire($expireTime);
$input->SetGoods_tag($subject);
$input->SetNotify_url("http://".$_SERVER['HTTP_HOST']."/notify_chongzhi.php");
$input->SetTrade_type("JSAPI");
$input->SetOpenid($openId);
//file_put_contents('request.txt',serialize($input));
$orders = WxPayApi::unifiedOrder($input);
if($orders['err_code']){
	echo $orders['err_code'].':'.$orders['err_code_des'];exit;
}
$jsApiParameters = $tools->GetJsApiParameters($orders);
$url = '/index.php?p=8&a=qianbao';
?>

<html>
<head>
	<meta http-equiv="content-type" content="text/html;charset=utf-8"/>
	<meta name="viewport" content="width=device-width, initial-scale=1"/> 
	<link href="/skins/default/styles/common.css" rel="stylesheet" type="text/css">
	<link href="/skins/default/styles/wode.css" rel="stylesheet" type="text/css">
	<link href="/skins/default/styles/zhifu.css" rel="stylesheet" type="text/css">
	<title>微信支付</title>

	<script type="text/javascript">
	//调用微信JS api 支付
	function jsApiCall()
	{
		WeixinJSBridge.invoke(
			'getBrandWCPayRequest',
			<?php echo $jsApiParameters; ?>,
			function(res){
				WeixinJSBridge.log(res.err_msg);
				if(res.err_msg == 'get_brand_wcpay_request:ok'){
					location.href='<?=$url?>';
				}else{
					alert(res.err_msg);
					location.href='/index.php?p=8&a=qianbao';//支付失败返回订单列表
				}
			}
			);
	}

	function callpay()
	{
		if (typeof WeixinJSBridge == "undefined"){
			if( document.addEventListener ){
				document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
			}else if (document.attachEvent){
				document.attachEvent('WeixinJSBridgeReady', jsApiCall); 
				document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
			}
		}else{
			jsApiCall();
		}
	}
</script>
<script type="text/javascript">
	//获取共享地址
	function editAddress()
	{
		WeixinJSBridge.invoke(
			'editAddress',
			<?php echo $editAddress; ?>,
			function(res){
				var value1 = res.proviceFirstStageName;
				var value2 = res.addressCitySecondStageName;
				var value3 = res.addressCountiesThirdStageName;
				var value4 = res.addressDetailInfo;
				var tel = res.telNumber;
				
				alert(value1 + value2 + value3 + value4 + ":" + tel);
			}
			);
	}
	
	window.onload = function(){
		if (typeof WeixinJSBridge == "undefined"){
			if( document.addEventListener ){
				document.addEventListener('WeixinJSBridgeReady', editAddress, false);
			}else if (document.attachEvent){
				document.attachEvent('WeixinJSBridgeReady', editAddress); 
				document.attachEvent('onWeixinJSBridgeReady', editAddress);
			}
		}else{
			editAddress();
		}
	};
	
</script>
</head>
<body style="color:#000;">
	<div id="pintuanzutuan">
		<div class="wode_1">
			订单支付
			<div class="wode_1_left" onclick="location.href='/index.php?p=8';">
				<img src="/skins/default/images/sousuo_1.png" />
			</div>
		</div>
		<div class="zhanghumingxi">
			<div class="yudingchenggong">
				<div class="yudingchenggong_1">
					<!-- 订单支付金额 -->
				</div>
				<div class="yudingchenggong_2">
					￥<span><?=$money?></span>
				</div>
				<div class="yudingchenggong_3">
					<a href="javascript:void(0);" onClick="callpay()">立即支付</a>
				</div>
			</div>

		</div>
	</div>


</body>
</html>
<?php exit();?>

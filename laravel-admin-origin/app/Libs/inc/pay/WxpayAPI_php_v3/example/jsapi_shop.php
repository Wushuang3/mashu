<?php 
require_once("inc/pay/WxpayAPI_php_v3/lib/WxPay.Api.php");//echo 111;
require_once("inc/pay/WxpayAPI_php_v3/example/WxPay.JsApiPay.php");
require_once("inc/pay/WxpayAPI_php_v3/example/log.php");
global $db,$orderId,$pdt_id,$num;
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
$openId = $tools->GetOpenid();
$inventory = $db->get_row("select title,price_sale from demo_pdt_inventory where productId=$pdt_id order by id desc limit 1");

$subject = sys_substr($inventory->title,10,true);
$price   = $inventory->price_sale * $num;
$body    = $subject;

$pay_price = $price*100;

$dtTime = date("YmdHis");
$expireTime = date("YmdHis", time() + 60*60*24);

//②、统一下单
$input = new WxPayUnifiedOrder();
$input->SetBody($body);
$input->SetAttach('');//自定义数据
$input->SetOut_trade_no($orderId);
$input->SetTotal_fee($pay_price);
$input->SetTime_start(date("YmdHis"));
$input->SetTime_expire($expireTime);
$input->SetGoods_tag($subject);
$input->SetNotify_url("https://".$_SERVER['HTTP_HOST']."/notify_shop.php");
$input->SetTrade_type("JSAPI");
$input->SetOpenid($openId);
//file_put_contents('request.txt',serialize($input));
$orders = WxPayApi::unifiedOrder($input);
if($orders['err_code']){
	echo $orders['err_code'].':'.$orders['err_code_des'];exit;
}
$jsApiParameters = $tools->GetJsApiParameters($orders);
$url = '/index.php?p=14&a=shangquan_zhifu&id='.$pdt_id;
?>

<html>
<head>
	<meta http-equiv="content-type" content="text/html;charset=utf-8"/>
	<meta name="viewport" content="width=device-width, initial-scale=1"/> 
	<link href="/skins/demo/styles/common.css" rel="stylesheet" type="text/css">
	<link href="/skins/demo/styles/wode.css" rel="stylesheet" type="text/css">
	<link href="/skins/demo/styles/zhifu.css" rel="stylesheet" type="text/css">
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
					location.href='/index.php?p=8';
				}else{
					alert(res.err_msg);
					location.href='/index.php?p=14&a=shangquan_zhifu&id=<?=$pdt_id?>';//支付失败返回订单列表
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
				<img src="/skins/demo/images/sousuo_1.png" />
			</div>
		</div>
		<div class="zhanghumingxi">
			<div class="yudingchenggong">
				<div class="yudingchenggong_1">
					<!-- 订单支付金额 -->
				</div>
				<div class="yudingchenggong_2">
					￥<span><?=$pay_price/100?></span>
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

<?php


namespace App\Libs;

use GuzzleHttp\Psr7\Request as Face;
use GuzzleHttp\Client;
use Hhxsv5\LaravelS\Swoole\Socket\Http;

class AliyunFaceApi
{
    protected $akId;
    protected $akSecret;

    public $api_attribute = 'https://dtplus-cn-shanghai.data.aliyuncs.com/face/attribute'; // 人脸识别地址
    public $api_verify = 'https://dtplus-cn-shanghai.data.aliyuncs.com/face/verify'; // 人脸对比地址

    public function __construct()
    {
        $this->akId = env('ALIOSS_AccessKeyId');
        $this->akSecret = env('ALIOSS_AccessKeySecret');
    }

    /**
     * 获取人脸属性识别
     * @param $api
     * @param $img
     * @param int $type
     * @return bool|string
     */
    public function getFace($api, $img, $type = 0)
    {
        $body1 = json_encode([
            'type' => $type,
            'image_url' => $img,
        ]);

        $headers = $this->makeSignHeader($api, $body1);

        HttpClient::setHeader($headers);
        $res = HttpClient::post($api, $body1);
        return $res;
    }

    /**
     * 人脸对比识别
     * @param $api
     * @param $img1         对比图片1,绝对路径http的，图片顺序无所谓
     * @param $img2         对比图片2
     * @param int $type
     * @return bool|string
     */
    public function contrastFace($api, $img1, $img2, $type = 0)
    {
        $body1 = json_encode([
            'type' => $type,
            'image_url_1' => $img1,
            'image_url_2' => $img2,
        ]);

        $headers = $this->makeSignHeader($api, $body1);

        HttpClient::setHeader($headers);
        $res = HttpClient::post($api, $body1);
        return $res;
    }

    /**
     * 生成api签名
     * @param $api
     * @param $body1
     * @return array
     */
    public function makeSignHeader($api, $body1)
    {
        $akId = $this->akId;
        $akSecret = $this->akSecret;
        $url = $api;



        $date1 = gmdate("D, d M Y H:i:s \G\M\T");
        // 参数构造
        $options = array(
            'http' => array(
                'header' => array(
                    'accept'=> "application/json",
                    'content-type'=> "application/json",
                    'date'=> $date1,
                    'authorization' => '',
                ),
                'method' => "POST", //可以是 GET, POST, DELETE, PUT
                'content' => $body1//如有数据，请用json_encode()进行编码
            )
        );

        $http = $options['http'];
        $header = $http['header'];
        $urlObj = parse_url($url);
        if(empty($urlObj["query"]))
            $path = $urlObj["path"];
        else
            $path = $urlObj["path"]."?".$urlObj["query"];
        $body = $http['content'];
        if(empty($body))
            $bodymd5 = $body;
        else
            $bodymd5 = base64_encode(md5($body,true));
        $stringToSign = $http['method']."\n".$header['accept']."\n".$bodymd5."\n".$header['content-type']."\n".$header['date']."\n".$path;
        $signature = base64_encode(
            hash_hmac(
                "sha1",
                $stringToSign,
                $akSecret, true));
        $authHeader = "Dataplus "."$akId".":"."$signature";
        $options['http']['header']['authorization'] = $authHeader;
        $headers = ['Content-type' => 'application/json', 'Accept' => 'application/json', 'Authorization' => $options['http']['header']['authorization'], 'Date' => $date1];
        return $headers;
    }

}

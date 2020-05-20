<?php

namespace App\Models;

use App\Support\Helpers;
use Illuminate\Database\Eloquent\Model;
use App\Libs\HttpClient;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class Member extends Model
{
    const SEX_ONE = 1; // 男
    const SEX_TOW = 2; // 女
    const NO_TEACHER = 0; //否
    const YES_TEACHER = 1; //是

    public static function SexMap($sex = 0)
    {
        $map = [
            self::SEX_ONE => '男',
            self::SEX_TOW => '女',
        ];
        if (!empty($sex)) {
            return $map[$sex] ?? '';
        }

        return $map;
    }

    public static function IsTeacherMap($sex = 0)
    {
        $map = [
            self::NO_TEACHER => '否',
            self::YES_TEACHER => '是',
        ];
        if (!empty($is_teacher)) {
            return $map[$is_teacher] ?? '';
        }

        return $map;
    }

    /**
     * 根据小程序提供code获取用户openid
     * @param $code
     * @return array|bool|mixed|string   返回获取到的openid数组，获取失败返回空
     */
    public static function getOpenidByCode($code)
    {
        $api = 'https://api.weixin.qq.com/sns/jscode2session?appid=' . env('WX_APPID') . '&secret=' . env('WX_SECRET') . '&js_code=' . $code . '&grant_type=authorization_code';

        $res = HttpClient::get($api);
        $res = json_decode($res, true);
        //$res['openid'] =9;
        //$res['session_key'] =9;
        if (isset($res['openid'])) {
            return $res;
        }
        return [];
    }

    public static function login($data, $ip)
    {
        $log = new MemberLogs();
        $log->ip = $ip;
        $openid = $data['openid'];
        $session_key = $data['session_key'];
        if (empty($session_key) || empty($openid)) {
            Log::error('登录失败，原因openid或sessionid为空');
            return false;
        }
        $member = self::findManagerByOpenid($openid);
        $token = md5($data['openid'] . $data['session_key']);
        if (!$member) {
            $member = self::createManager($openid);
            if (!$member) {
                Log::error('注册失败，原因openid或sessionid为空');
                return false;
            }
            $log->action = '注册';
            //向商城注册会员
            $url = 'https://buy.zhishangez.com/service.php';
            $register['action'] ='users_appletReg';
            $register['com_id'] =1141;
            $register['token'] =$token;
            $register['openid'] =$data['openid'];
            $register['session_key'] =$data['session_key'];
            $res = Helpers::curl_post($url,$register);
            Log::info('register:'.json_encode($res).' -- data:'.json_encode($register));
            //var_dump($res);die;
        } else {
            $log->action = '登录';
        }

        $log->user_id = $member->id;

        Cache::forget($token);
        Cache::add($token, [
            'member_id' => $member->id,
            'openid' => $member->openid,
            'session_key' => $data['session_key'],
            'member_mobile' => $member->mobile,
            'member_name' => $member->name,
            'member_head_icon' => $member->member,
            'is_teacher' => $member->is_teacher
        ], 3600 * 24);
        $data['other'] = Cache::get($token, []);
        $log->data = json_encode($data);
        if (!$log->save()) {
            Log::error('记录登录日志失败');
        }
        $data['other']['token'] = $token;
        return $data['other'];

    }

    /**
     * 根据openid查找用户是否存在
     * @param $openid
     * @return mixed
     */
    public static function findManagerByOpenid($openid)
    {
        return self::where(['open_id' => $openid])->first();
    }

    /**
     * 添加用户
     * @param $openid
     * @return bool|mixed
     */
    public static function createManager($openid)
    {
        if (self::findManagerByOpenid($openid)) return false;
        $member = new Member();
        $member->open_id = $openid;
        if ($member->save()) {
            return $member;
        } else {
            return false;
        }
    }

    /**
     * 根据查找教练
     * @param
     * @return
     */
    public static function findManagerByTeacher()
    {
        $teachers = self::where(['is_teacher' => 1])->get();
        $first_names = array();
        if ($teachers) {
            $first_names = array_column($teachers->toArray(), 'name', 'id');
        }

        return $first_names;
    }

    /**
     * 根据查找教练
     * @param $openid
     * @return mixed
     */
    public static function findManagerByTeacherName($user_id)
    {
        $teachers = self::where(['id' => $user_id])->first();
        $first_names = "";
        if ($teachers) {
            $first_names = $teachers->name;
        }

        return $first_names;
    }
}

<?php


namespace App\Support;


use App\Models\ApiErrorLog;
use http\Env\Request;
use Illuminate\Support\Facades\Log;

class Code
{
    /**
     * 正常时,返回码
     */
    const SUCC = 0;

    /**
     * 100 致命错误
     */
    const TOKEN_ERROR = 101; // token验证失败

    /**
     * http 相关错误
     */
    const ERR_HTTP_UNAUTHORIZED = 401; // 登录已过期，请重新登录
    const ERR_HTTP_FORBIDDEN = 403; // 无权访问该地址
    const ERR_HTTP_NOT_FOUND = 404; // 请求地址不存在
    const ERR_INTERNAL_SERVER = 500; // 服务器内部错误
    const ERR_NO_AUTH = 401; // 没有改访问权限

    /**
     * 1000 系统级别错误
     */
    const ERR_QUERY = 1001; // 数据库操作失败
    const ERR_DB = 1002; // 数据库连接失败
    const ERR_PARAMS = 1003; // '参数验证失败： %s
    const ERR_MODEL = 1004; // 数据不存在
    const ERR_FILE_UP_LOAD = 1005; // 文件上传出错
    const ERR_PERM = 1007; // 没有该操作权限，请联系管理员
    const ERR_CLOSE = 1008; // 该接口已停用



    /**
     * 2100 小程序级别错误
     * @var array
     */
    const ERR_FETCH_OPENID = 2101; // 获取openid失败：%s
    const ERR_LOGIN_FAILS = 2102; // 登录失败
    const ERR_BIND_PHONE = 2103; // 绑定手机号失败
    /**
     * 3000 预约错误码
     * @var array
     */
    const ERR_TEACHER_ID = 3001; //教师ID错误
    const ERR_TIME = 3002; //预约时间错误
    const ERR_NAME = 3003; //姓名错误
    const ERR_MOBILE = 3005; //手机号错误
    const ERR_COURSE_ID = 3006; //课程id错误
    const ERR_USER_ID = 3009; //用户id错误
    const ERR_BOOKING = 3010; //预约已存在
    const ERR_CULTURE= 3011;  //文化课课时不足
    const ERR_EXPERIENCE = 3012; //体验课课时不足
    const ERR_OFFICIAL = 3013; //正式课课时不足
    const ERR_MEMBER = 3014; //会员不存在
    const ERR_BOOKING_ID = 3015; //预约id错误
    const ERR_CANCEL_BOOKING = 3016; //24小时以内不能取消预约
    const ERR_NOT_BOOKING = 3017; //排班错误
    const ERR_TEACHER_LEVEL = 3008; //教师级别错误
    const ERR_BOOKING_NUM = 3009; //预约人数已满
    const ERR_RATING_NUM = 3010; //分数不能为空
    const ERR_ATTENTION = 3011; // 是否关注

    public static $msgs = [
        self::SUCC => '请求成功',

        self::TOKEN_ERROR => 'token验证失败',

        self::ERR_HTTP_UNAUTHORIZED => '登录已过期，请重新登录',
        self::ERR_HTTP_FORBIDDEN => '无权访问该地址',
        self::ERR_HTTP_NOT_FOUND => '请求地址不存在',
        self::ERR_INTERNAL_SERVER => '服务器内部错误',
        self::ERR_NO_AUTH => '没有改访问权限：%s',

        self::ERR_QUERY => '数据库操作失败： %s',
        self::ERR_DB => '数据库连接失败',
        self::ERR_PARAMS => '参数验证失败： %s',
        self::ERR_MODEL => '数据不存在： %s',
        self::ERR_FILE_UP_LOAD => '文件上传出错： %s',
        self::ERR_PERM => '没有该操作权限，请联系管理员',
        self::ERR_CLOSE => '该接口已停用',

        self::ERR_FETCH_OPENID => '获取openid失败：%s',
        self::ERR_LOGIN_FAILS => '登录失败：%s',
        self::ERR_BIND_PHONE => '绑定手机号失败',
    ];

    /**
     * 提示代码
     * @var | int
     */
    protected static $code;

    /**
     * 提示信息
     * @var | string
     */
    protected static $msg;

    /**
     * 详情信息
     * @var
     */
    protected static $detail;

    /**
     * 需要返回的数据
     * @var
     */
    protected static $data;
    protected static $rquest_params;

    /**
     * 设置提示信息
     *
     * @param $code 提示代码
     * @param null $msg 提示信息
     * @param array $params 提示信息中动态参数
     */
    public static function setCode($code, $msg = null, $data = [], $params = [], $rquest_params = [])
    {
        self::$code = $code = (int)$code;
        if (null == $msg || '' == $msg) {
            if (isset(self::$msgs[$code])) {
                if (!empty($params)) {
                    array_unshift($params, self::$msgs[$code]);
                    self::$msg = call_user_func_array('sprintf', $params);
                } else {
                    self::$msg = self::$msgs[$code];
                }
            } else {
                self::$msg = '提示信息未定义';
            }
        } else {
            self::$msg = $msg;
        }

        if (self::SUCC !== $code) {
            // save log
        }
        self::$data = $data;
        self::$rquest_params = $rquest_params;
        self::addLog();
        if (empty($data)) {
            return ['code' => self::$code, 'message' => self::$msg];
        } else {
            return ['code' => self::$code, 'message' => self::$msg, 'data' => $data];

        }

    }

    public static function addLog()
    {
        $log = new ApiErrorLog();
        $log->code = self::$code;
        $log->message = self::$msg;
        $log->data = self::$data;
        $log->param = self::$rquest_params;
        if (!$log->save()) {
            Log::error('请求日志写入失败');
        }
    }

}

<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Course;
use App\Models\Member;
use App\Models\Rating;
use App\Models\Attention;
use App\Models\RatingSum;
use App\Models\SetClass;
use App\Models\Teacher;
use App\Support\Code;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Support\Helpers;
use PHPUnit\Framework\StaticAnalysis\HappyPath\AssertNotInstanceOf\A;


class BookingController extends Controller
{
    /**
     * TODO 教练列表
     * @param Request $request
     * @return array
     */
    public function getTeacher(Request $request)
    {
        //$token = $request->input('token');
        // $user_id = Helpers::getUserIdByToken($token);
        //if (empty($user_id)) return Code::setCode(Code::TOKEN_ERROR, 'token验证失败', '', '');
        //level name
        $level = $request->input('level');
        $name = $request->input('name');

        $teachers = Teacher::where('id','>',0);

        if($level){
            $teachers->where(['level' => $level]);
        }

        if($name){
            $users = Member::select('id')->where(['name'=>$name])->get();

            if($users){
                $id_arr = array();
                foreach ($users as $v){
                    $id_arr[] = $v['id'];
                }
                $teachers-> whereIn('user_id', [$id_arr]);
            }
        }

        $res = $teachers->get();
        $teachers = Member::findManagerByTeacher();

        foreach ($res as $k => $v) {
            $res[$k]->name = empty($v->user_id) ? '' : $teachers[$v->user_id];
            $res[$k]->head_icon = env('APP_URL') . '/storage/' .$v->head_icon;
        }
        return Code::setCode(Code::SUCC, '', $res);
    }

    /**
     * TODO 教练排班表
     * @param Request $request
     * @return array
     */
    public function getTeacherClass(Request $request)
    {
        $token = $request->input('token');
        $user_id = Helpers::getUserIdByToken($token);
        if (empty($user_id)) return Code::setCode(Code::TOKEN_ERROR, 'token验证失败', '', '');

        $teacher_id = $request->input('teacher_id', 0);
        $time = $request->input('time', 0);
        if (empty($teacher_id)) return Code::setCode(Code::ERR_TEACHER_ID, '教师ID错误', '', '');
        if (empty($time)) return Code::setCode(Code::ERR_TIME, '预约时间错误', '', '');
        $res = SetClass::where(['teacher' => $teacher_id, 'time' => $time])->get();
        $data['num'] = array();
        $data['total'] = array();
        $data['hour'] = array();

        if ($res) {
            foreach ($res as $k => $v) {
                $hour = explode(',', $v->hour);
                foreach ($hour as $vv) {
                    $data['hour'][$vv] = Course::CourseMap()[$v->course];
                    $data['total'][$vv] = $v->num;
                }
            }


            $query = Booking::where(['teacher' => $teacher_id]);
            $query->where('time', '>=', $time);

            $bookings = $query->Where('time', '<=', $time + 86400)->get();
            $booking_arr = array();

            foreach ($bookings as $v) {
                $booking_arr[date('H', strtotime($v->time))][] = $v->time;
            }
            foreach ($booking_arr as $key => $val) {
                $data['num'][$key] = count($val);
            }
        }

        return Code::setCode(Code::SUCC, '', $data);
    }

    /**
     * TODO 预约表单提交
     * @param Request $request
     * @return array
     */
    public function submitBooking(Request $request)
    {
        $token = $request->input('token');
        $user_id = Helpers::getUserIdByToken($token);
        if (empty($user_id)) return Code::setCode(Code::TOKEN_ERROR, 'token验证失败', '', '');

        $name = $request->input('name', '');
        $mobile = $request->input('mobile', '');
        $comment = $request->input('comment', 0);
        $teacher_id = $request->input('teacher_id', 0);
        $course_id = $request->input('course_id', 0);
        $level = $request->input('level', 0);
        $time = $request->input('time', 0);
        if (empty($teacher_id)) return Code::setCode(Code::ERR_TEACHER_ID, '教师ID错误', '', '');
        if (empty($user_id)) return Code::setCode(Code::ERR_USER_ID, '用户id错误', '', '');
        if (empty($time)) return Code::setCode(Code::ERR_TIME, '预约时间错误', '', '');
        if (empty($name)) return Code::setCode(Code::ERR_NAME, '姓名错误', '', '');
        if (empty($mobile)) return Code::setCode(Code::ERR_MOBILE, '手机号错误', '', '');
        if (empty($course_id)) return Code::setCode(Code::ERR_COURSE_ID, '课程id错误', '', '');
        if (empty($level)) return Code::setCode(Code::ERR_TEACHER_LEVEL, '教师级别错误', '', '');
        $data = self::createBooking($name, $user_id, $mobile, $comment, $teacher_id, $course_id, $level, $time);
        //return Code::setCode(Code::ERR_QUERY, '预约失败',"");
        return Code::setCode($data['code'], $data['msg']);
    }

    /**
     * TODO 预约列表
     * @param Request $request
     * @return array
     */
    public function getBooking(Request $request)
    {
        $token = $request->input('token');
        $user_id = Helpers::getUserIdByToken($token);
        if (empty($user_id)) return Code::setCode(Code::TOKEN_ERROR, 'token验证失败', '', '');

        $res = Booking::where(['user_id' => $user_id])->get();
        if ($res) {
            $teachers = Teacher::all()->toArray();
            $names = Member::findManagerByTeacher();
            $levels = array_column($teachers, 'level', 'id');
            foreach ($res->toArray() as $k => $v) {
                $res[$k]['teacher'] = empty($v['teacher']) ? '' : $names[$v['user_id']];
                $res[$k]['course'] = Course::CourseMap()[$v['course']];
                $res[$k]['level'] = $levels[$v['teacher']];
                //status 1已上课 2待上课 3取消
                $res[$k]['status'] = 2;
                //0否 1是
                $res[$k]['is_rating'] = 0;
                if ($v['is_del'] == 1) {
                    $res[$k]['status'] = 3;
                }

                if (strtotime($v['time']) < time()) {
                    $res[$k]['status'] = 1;
                    $num = Rating::where(['booking_id' => $v['id']])->count();
                    if ($num) {
                        $res[$k]['is_rating'] = 1;
                    }
                }
            }
        }
        return Code::setCode(Code::SUCC, '', $res);
    }

    /**
     * TODO 取消预约列表
     * @param Request $request
     * @return array
     */
    public function cancelBooking(Request $request)
    {
        $token = $request->input('token');
        $user_id = Helpers::getUserIdByToken($token);
        if (empty($user_id)) return Code::setCode(Code::TOKEN_ERROR, 'token验证失败', '', '');

        $id = $request->input('id', 0);
        if (empty($id)) return Code::setCode(Code::ERR_BOOKING_ID, '预约id错误', '', '');
        $res = Booking::where(['user_id' => $user_id, 'id' => $id])->first();
        if ($res) {

            if (strtotime($res->time) < time() + 86400) {
                return Code::setCode(Code::ERR_CANCEL_BOOKING, '24小时以内不能取消预约');
            }
            $res->is_del = 1;
            if (!$res->update()) {
                return Code::setCode(Code::ERR_CANCEL_BOOKING, '取消失败');
            }
            return Code::setCode(Code::SUCC, '取消成功');
        }
        return Code::setCode(Code::ERR_QUERY, '取消失败');
    }


    /**
     * TODO 一键延课
     * @param Request $request
     * @return array
     */
    public function continueBooking(Request $request)
    {
        $token = $request->input('token');
        $user_id = Helpers::getUserIdByToken($token);
        if (empty($user_id)) return Code::setCode(Code::TOKEN_ERROR, 'token验证失败', '', '');

        $id = $request->input('id', 0);
        $level = $request->input('level', 0);
        if (empty($id)) return Code::setCode(Code::ERR_BOOKING_ID, '预约id错误', '', '');
        if (empty($level)) return Code::setCode(Code::ERR_TEACHER_LEVEL, '教师级别错误', '', '');
        $res = Booking::where(['user_id' => $user_id, 'id' => $id])->first();
        $data['code'] = Code::ERR_NOT_BOOKING;
        $data['msg'] = '排班错误';
        if ($res) {
            $time = strtotime(date('Y-m-d', time()));
            $class = SetClass::where(['teacher' => $res->teacher, 'time' => $time])->first();
            //$query->where('time','>=',$time)->orderBy('id','asc')->limit(1);
            //$class = $query->get();
            if (!$class) {
                return Code::setCode(Code::ERR_NOT_BOOKING, '排班错误');
            }

            $lTime = date('H', strtotime($res->time));
            $hour = explode(',', $class->hour);
            $newHour = '';
            foreach ($hour as $v) {
                if ($lTime < $v) {
                    $newHour = $v;
                }
            }
            if (empty($newHour)) {
                return Code::setCode(Code::ERR_NOT_BOOKING, '排班错误');
            }
            $newTime = strtotime($class->time . ' ' . $newHour . ':00:00');

            $data = self::createBooking($res->name, $user_id, $res->mobile, "", $res->teacher, $res->course, $level, $newTime, $class->num);
        }
        //echo 111;die;
        return Code::setCode($data['code'], $data['msg']);
    }

    /**
     * TODO 预约
     * @param Request $request
     * @return array
     */
    private static function createBooking($name, $user_id, $mobile, $comment = '', $teacher_id, $course_id, $level, $time, $num = 0)
    {
        //报名总数限制
        if (empty($num)) {
            $class = SetClass::where(['teacher' => $teacher_id, 'time' => $time])->first();
            if (!$class) {
                $data['code'] = Code::ERR_NOT_BOOKING;
                $data['msg'] = '排班错误';
                return $data;
            }
            $num = $class->num;
        }

        $bookding_num = Booking::where(['teacher' => $teacher_id, 'time' => $time])->groupBy('time')->count();
        if ($num <= $bookding_num) {
            $data['code'] = Code::ERR_BOOKING_NUM;
            $data['msg'] = '预约人数已满';
            return $data;
        }

        //查看用户是否又余课
        $member = Member::where(['id' => $user_id])->first();
        $data = array();
        if (empty($member)) {
            return Code::setCode(Code::ERR_USER_ID, '用户id错误', "");
        }

        if ($course_id == Course::COURSE_CULTURE) {//文化课

            if (empty($member->culture_num)) {
                $data['code'] = Code::ERR_CULTURE;
                $data['msg'] = '文化课课时不足';
                return $data;
            }
            $member->culture_num = $member->culture_num - 1;
        }
        if ($course_id == Course::COURSE_EXPERIENCE) {//体验课
            if (empty($member->experience_num) && empty($member->culture_num)) {
                // return Code::setCode(Code::ERR_EXPERIENCE, '体验课课时不足',"");
                $data['code'] = Code::ERR_EXPERIENCE;
                $data['msg'] = '体验课课时不足';
                return $data;
            }

            if (empty($member->experience_num)) {
                $member->culture_num = $member->culture_num - 1;
            } else {
                $member->experience_num = $member->experience_num - 1;
            }

        }
        if ($course_id == Course::COURSE_OFFICIAL) {//正式课
            if (empty($member->official_num) && empty($member->culture_num)) {
                //return Code::setCode(Code::ERR_OFFICIAL, '正式课课课时不足',"");
                $data['code'] = Code::ERR_OFFICIAL;
                $data['msg'] = '正式课课课时不足';
                return $data;
            }

            if (empty($member->official_num)) {
                $member->culture_num = $member->culture_num - 1;
            } else {
                $member->official_num = $member->official_num - 1;
            }
        }

        //每个时间段只有一个预约
        $query = Booking::where(['user_id' => $user_id, 'teacher' => $teacher_id, 'time' => $time]);
        $res = $query->first();
        if ($res) {
            //return Code::setCode(Code::ERR_BOOKING, '预约已存在',"");
            $data['code'] = Code::ERR_BOOKING;
            $data['msg'] = '预约已存在';
            return $data;
        }

        $booking = new Booking();
        $booking->user_id = $user_id;
        $booking->name = $name;
        $booking->mobile = $mobile;
        $booking->comment = $comment;
        $booking->teacher = $teacher_id;
        $booking->course = $course_id;
        $booking->time = $time;
        DB::beginTransaction(); // 开启事务
        if ($booking->save()) {
            //扣除课时

            if (!$member->update()) {
                DB::rollBack();
                //return Code::setCode(Code::ERR_MEMBER, '扣除课时失败',"");
                $data['code'] = Code::ERR_MEMBER;
                $data['msg'] = '扣除课时失败';
                return $data;
            }

            //level  > 1
            if ($level > Teacher::TEACHER_ONE) {

            }
            DB::commit();
            //return Code::setCode(Code::SUCC, '预约成功', "");
            $data['code'] = Code::SUCC;
            $data['msg'] = '预约成功';
            return $data;
        }
    }

    /**
     * TODO 评价表
     * @param Request $request
     * @return array
     */
    public function createRating(Request $request)
    {
        $token = $request->input('token');
        $user_id = Helpers::getUserIdByToken($token);
        if (empty($user_id)) return Code::setCode(Code::TOKEN_ERROR, 'token验证失败', '', '');

        $teacher_id = $request->input('teacher_id', 0);
        $course_id = $request->input('course_id', 0);
        $score = $request->input('score', 0);
        $is_show = $request->input('is_show', 0);
        $booking_id = $request->input('booking_id', 0);
        $tags = $request->input('tags', '');
        $content = $request->input('content', '');
        if (empty($teacher_id)) return Code::setCode(Code::ERR_TEACHER_ID, '教师ID错误', '', '');
        if (empty($course_id)) return Code::setCode(Code::ERR_COURSE_ID, '课程id错误', '', '');
        if (empty($booking_id)) return Code::setCode(Code::ERR_BOOKING_ID, '预约id错误', '', '');
        if (empty($score)) return Code::setCode(Code::ERR_RATING_NUM, '分数不能为空', '
        ', '');
        $rating_img = Helpers::uploadFile($request->file('imgs'), 'public', true);

        $rating = new Rating();
        $rating->user_id = $user_id;
        $rating->teacher_id = $teacher_id;
        $rating->course_id = $course_id;
        $rating->booking_id = $booking_id;
        $rating->score = $score * 10;
        $rating->is_show = $is_show;
        $rating->tags = $tags;
        $rating->content = $content;
        $rating->imgs = $rating_img;
        if ($rating->save()) {
            return Code::setCode(Code::SUCC, '评价成功');
        }
        return Code::setCode(Code::ERR_INTERNAL_SERVER, '评价失败');
    }

    /**
     * TODO 关注表
     * @param Request $request
     * @return array
     */
    public function createAttention(Request $request)
    {
        $token = $request->input('token');
        $user_id = Helpers::getUserIdByToken($token);
        if (empty($user_id)) return Code::setCode(Code::TOKEN_ERROR, 'token验证失败', '', '');

        $teacher_id = $request->input('teacher_id', 0);
        $is_del = $request->input('is_del', 0);
        if (empty($teacher_id)) return Code::setCode(Code::ERR_TEACHER_ID, '教师ID错误', '', '');
        $attention = Attention::where(['teacher_id' => $teacher_id, 'user_id' => $user_id])->first();
        if (!$attention) {
            $attention = new Attention();
        }
        $attention->user_id = $user_id;
        $attention->teacher_id = $teacher_id;
        $attention->is_del = $is_del;

        if ($attention->save()) {
            return Code::setCode(Code::SUCC, '成功');
        }
        return Code::setCode(Code::ERR_INTERNAL_SERVER, '失败');
    }

    /**
     * TODO 我的关注
     * @param Request $request
     * @return array
     */
    public function myAttention(Request $request)
    {
        $token = $request->input('token');
        $user_id = Helpers::getUserIdByToken($token);
        if (empty($user_id)) return Code::setCode(Code::TOKEN_ERROR, 'token验证失败', '', '');

        $attentions = Attention::where(['user_id' => $user_id, 'is_del' => 0])->get();
        if (!$attentions) {
            return Code::setCode(Code::SUCC, '成功', []);
        }
        $teachers = Teacher::all()->toArray();
        $teacher_arr = $data = array();
        $names = Member::findManagerByTeacher();
        foreach ($teachers as $v) {
            $teacher_arr[$v['id']] = $v;
            $teacher_arr[$v['id']]['name'] = empty($v['user_id']) ? '' : $names[$v['user_id']];
        }
        foreach ($attentions as $item) {
            $data[] = $teacher_arr[$item->teacher_id];
        }

        return Code::setCode(Code::SUCC, '成功', $data);
    }

    /**
     * TODO 我的评价
     * @param Request $request
     * @return array
     */
    public function myRating(Request $request)
    {
        $token = $request->input('token');
        $user_id = Helpers::getUserIdByToken($token);
        if (empty($user_id)) return Code::setCode(Code::TOKEN_ERROR, 'token验证失败', '', '');

        $rating = Rating::where(['user_id' => $user_id])->get();
        if (!$rating) {
            return Code::setCode(Code::SUCC, '成功', []);
        }
        $teachers = Teacher::all()->toArray();
        $names = Member::findManagerByTeacher();
        $head_icon = array_column($teachers, 'head_icon', 'id');
        $teacher_user_id = array_column($teachers, 'user_id', 'id');
        foreach ($rating as $k => $v) {
            $rating[$k]->teacher_name = empty($teacher_user_id[$v->teacher_id]) ? '' : $names[$teacher_user_id[$v->teacher_id]];
            $rating[$k]->course_name = Course::CourseMap()[$v->teacher_id];
            $rating[$k]->head_icon = $head_icon[$v->teacher_id];
            $rating[$k]->imgs = json_decode($rating[$k]->imgs);
            $rating[$k]->score = $rating[$k]->score / 10;

        }
        return Code::setCode(Code::SUCC, '成功', $rating);
    }

    /**
     * TODO 教练详情
     * @param Request $request
     * @return array
     */
    public function teacherDetail(Request $request)
    {
        $token = $request->input('token');
        $user_id = Helpers::getUserIdByToken($token);
        if (empty($user_id)) return Code::setCode(Code::TOKEN_ERROR, 'token验证失败', '', '');

        $teacher_id = $request->input('teacher_id', 0);
        if (empty($teacher_id)) return Code::setCode(Code::ERR_TEACHER_ID, '教师ID错误', '', '');
        $detail = array();
        $teacher = Teacher::where(['id' => $teacher_id])->first();
        $rating = Rating::where(['teacher_id' => $teacher_id])->get();
        $names = Member::findManagerByTeacher();

        $detail['name'] = empty($teacher->user_id) ? '' : $names[$teacher->user_id];
        $detail['head_icon'] = $teacher->head_icon;
        $detail['level'] = $teacher->level;
        $detail['description'] = $teacher->description;
        $num = $good = $middle = $bad = $score = 0;
        $count = count($rating);
        foreach ($rating as $k => $v) {
            $num = $k;
            $score += $v->score;
            if ($v->score > 3){
                $good += 1;
            }
            if ($v->score == 3){
                $middle += 1;
            }
            if ($v->score < 3){
                $bad += 1;
            }
        }

        $attention= Attention::where(['user_id' => $user_id,'teacher_id'=>$teacher_id,'is_del'=>0])->first();
        $detail['score'] = empty($score) ? 0 : intval($score / $count)/10;
        $detail['total'] = $num;
        $detail['good'] = empty($num) ? 0 : $good/$num;
        $detail['middle'] = empty($num) ? 0 : $middle/$num;
        $detail['bad'] = empty($num) ? 0 : $bad/$num;
        $detail['head_icon'] = env('APP_URL') . '/storage/' .$detail["head_icon"];
        $detail['is_attention'] = empty($attention) ? 1 : 0;

        return Code::setCode(Code::SUCC, '成功', $detail);
    }

    /**
     * TODO 教练评价列表
     * @param Request $request
     * @return array
     */
    public function teacherRating(Request $request)
    {
        $token = $request->input('token');
        $user_id = Helpers::getUserIdByToken($token);
        if (empty($user_id)) return Code::setCode(Code::TOKEN_ERROR, 'token验证失败', '', '');

        $teacher_id = $request->input('teacher_id', 0);
        $limit = $request->input('limit', 10);
        $offset = $request->input('offset', 0);
        if (empty($teacher_id)) return Code::setCode(Code::ERR_TEACHER_ID, '教师ID错误', '', '');
        $rating = Rating::where(['teacher_id' => $teacher_id])->limit($limit)->offset($offset)->orderBy('id', 'desc')->get();
        $user_arr = array();
        foreach ($rating as $v) {
            $user_arr[] = $v->user_id;
        }

        $users = Member::whereIn('id', array_unique($user_arr))->get()->toArray();
        $names = array_column($users, 'name', 'id');
        $head_icon = array_column($users, 'head_icon', 'id');

        foreach ($rating as $k => $val) {
            $rating[$k]->user_name = $names[$val->user_id];
            $rating[$k]->user_head_icon = $head_icon[$val->user_id];
        }
        return Code::setCode(Code::SUCC, '成功', $rating);
    }

    /**
     * TODO 会员信息
     * @param Request $request
     * @return array
     */
    public function memberDetail(Request $request)
    {
        $token = $request->input('token');
        $user_id = Helpers::getUserIdByToken($token);
        if (empty($user_id)) return Code::setCode(Code::TOKEN_ERROR, 'token验证失败', '', '');

        $detail = Member::where(['id' => $user_id])->first();
        return Code::setCode(Code::SUCC, '成功', $detail);
    }

    /**
     * TODO update会员信息
     * @param Request $request
     * @return array
     */
    public function updateMember(Request $request)
    {
        $token = $request->input('token');
        $user_id = Helpers::getUserIdByToken($token);
        if (empty($user_id)) return Code::setCode(Code::TOKEN_ERROR, 'token验证失败', '', '');

        $name = $request->input('name', '');
        $sex = $request->input('sex', 0);
        $birthday = $request->input('birthday', '');
        $mobile = $request->input('mobile', '');
        $member = Member::where(['id' => $user_id])->first();
        if (!$member) {
            return Code::setCode(Code::ERR_PERM, '', [], [], $request->input());
        }
        $member_img = Helpers::uploadFile($request->file('head_icon'), 'public', true);
        $member->name = $name;
        $member->sex = $sex;
        $member->birthday = $birthday;
        $member->mobile = $mobile;
        $member->head_icon = !empty($member_img) ? $member_img : $member->head_icon;
        if ($member->save()) {
            return Code::setCode(Code::SUCC, '成功');
        }

        return Code::setCode(Code::ERR_QUERY, '');
    }

    /**
     * TODO 我的消课记录
     * @param Request $request
     * @return array
     */
    public function myCancelClass(Request $request)
    {
        $token = $request->input('token');
        $user_id = Helpers::getUserIdByToken($token);
        if (empty($user_id)) return Code::setCode(Code::TOKEN_ERROR, 'token验证失败', '', '');
        $query = Booking::where(['user_id' => $user_id, 'is_del' => 0]);
        $booking = $query->where('time', '<=', time())->get();
        $teachers = Teacher::all()->toArray();
        $teacher_user_id = array_column($teachers, 'user_id', 'id');
        $data = array();
        $data['total'] = count($booking);
        $names = Member::findManagerByTeacher();
        foreach ($booking as $k => $v) {
            $data[$k]['teacher_name'] = empty($teacher_user_id[$v->teacher]) ? '' : $names[$teacher_user_id[$v->teacher]];
            $data[$k]['course_name'] = Course::CourseMap()[$v->course];
            $data[$k]['time'] = $v->time;
        }


        return Code::setCode(Code::SUCC, '成功', $data);
    }

    /**
     * TODO 教师预约列表
     * @param Request $request
     * @return array
     */
    public function getTeacherBooking(Request $request)
    {
        $token = $request->input('token');
        $user_id = Helpers::getUserIdByToken($token);
        if (empty($user_id)) return Code::setCode(Code::TOKEN_ERROR, 'token验证失败', '', '');

        $teacher_id = $request->input('teacher_id', 0);
        $time = $request->input('time', 0);
        $course_id = $request->input('course_id', 0);
        $status = $request->input('status', 0);
        $limit = $request->input('limit', 10);
        $offset = $request->input('offset', 0);
        if (empty($teacher_id)) return Code::setCode(Code::ERR_TEACHER_ID, '教师ID错误', '', '');
        $query = Booking::where(['teacher' => $teacher_id]);
        if ($time) {
            $query->where(['time' => $time]);
        }
        if ($course_id) {
            $query->where(['course' => $course_id]);
        }
        if ($status) {//1完成  2待服务 3已取消
            if ($status == 3) {
                $query->where(['is_del' => 1]);
            }
            if ($status == 2) {
                $query->where('time', '>=', time());
            }
            if ($status == 1) {
                $query->where('time', '<=', time() + 3600);
            }
        }

        $res = $query->limit($limit)->offset($offset)->get();
        $data = array();

        if ($res) {
            $user_arr = array();
            foreach ($res as $v) {
                $user_arr[] = $v->user_id;
            }
            $users = Member::whereIn('id', array_unique($user_arr))->get()->toArray();
            $names = array_column($users, 'name', 'id');
            $head_icon = array_column($users, 'head_icon', 'id');
            foreach ($res as $k => $v) {
                $data[$k]['id'] = $v->id;
                $data[$k]['course'] = Course::CourseMap()[$v->course];
                $data[$k]['name'] = $names[$v->user_id];
                $data[$k]['head_icon'] = $head_icon[$v->user_id];
                $data[$k]['time'] = $v->time;
                $data[$k]['status'] = 2;
                if ($v->is_del == 1) {
                    $data[$k]['status'] = 3;
                }
                if (strtotime($v->time) < time()) {
                    $data[$k]['status'] = 1;
                }
            }
        }
        return Code::setCode(Code::SUCC, '', $data);
    }

    /**
     * TODO 通过会员id获取教练信息
     * @param Request $request
     * @return array
     */
    public function getUserIdByTeacher(Request $request)
    {
        $token = $request->input('token');
        $user_id = Helpers::getUserIdByToken($token);
        if (empty($user_id)) return Code::setCode(Code::TOKEN_ERROR, 'token验证失败', '', '');
        $res = Teacher::where(['user_id' => $user_id])->first();
        $teachers = Member::findManagerByTeacher();

        $res->name = empty($res->user_id) ? '' : $teachers[$res->user_id];

        return Code::setCode(Code::SUCC, '', $res);
    }

    /**
     * TODO 小程序登录
     * @param Request $request
     * @return array
     */
    public function login(Request $request)
    {
        $code = $request->input('code', '');
        if (empty($code)) return Code::setCode(Code::ERR_PARAMS, '', '', 'code值错误');

        $openid_data = Member::getOpenidByCode($code);

        if (empty($openid_data)) return Code::setCode(Code::ERR_FETCH_OPENID, '', $openid_data, ['请求失败'], $request->input());

        if (!$res = Member::login($openid_data, $request->getClientIp())) {
            return Code::setCode(Code::ERR_LOGIN_FAILS, '', $openid_data, [], $request->input());
        }

        return Code::setCode(Code::SUCC, '', $res);
    }

}

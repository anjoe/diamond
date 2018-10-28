<?php

namespace App\Http\Controllers\ShortMessage;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redis;

class SendMessageController extends Controller
{
    /**
     * Verification Code
     * @var integer
     */
    private  $code;

    /**
     * @var string
     */
//  private $apikey = "48809fdf8978aee119b70a4143471973";
    private $apikey = "147581afd06f6415d2ea1f08264bdb84";

    /**
     *
     * @var string
     */
    private $text;

    private function validator($data)
    {
        $message = [
            'required'  => '数据不能为空',
            'regex:^((1[3,5,8][0-9])|(14[5,7])|(17[0,6,7,8])|(19[7]))\\d{8}$' => '手机号格式不合法'
        ];
        return Validator::make($data, [
            'mobile'    => ['required', 'regex:/^((1[3,5,8][0-9])|(14[5,7])|(17[0,6,7,8])|(19[7]))\\d{8}$/']
        ], $message);
    }

    /**
     * send short message
     * @param $mobile
     * @return mixed
     */
    public function sendMessage(Request $request)
    {
        if ($request->isMethod('post')) {
            $validator = $this->validator($request->all());
            if ($validator->fails()) {
                $errors = $validator->errors()->first();
                if ($errors == "数据不能为空") {
                    $code = 1001;
                } else if ($errors == "数据不合法") {
                    $code = 1002;
                } else {
                    $code = 5001;
                }
                return response()->json([
                    'code' => $code,
                    'msg' => $errors
                ]);
            }
            $mobile = request('mobile');
//            $mobile = 15955702565;
            $this->createCode();
            $this->createText();
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_HTTPHEADER,
                array('Accept:text/plain;charset=utf-8',
                'Content-Type:application/x-www-form-urlencoded', 'charset=utf-8'));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            // Sending single SMS
            $data = array('text' => $this->text, 'apikey' => $this->apikey, 'mobile' => $mobile);
            curl_setopt($ch, CURLOPT_URL, 'https://sms.yunpian.com/v2/sms/single_send.json');
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            $result = curl_exec($ch);
            $pattern = '/"code":((-)?\d+)/';
            preg_match($pattern, $result, $match);
//            var_dump($result);
//            var_dump($match);
            if (is_array($match) && count($match) > 1) {
                $code = (int)$match[1];
                if ($code == 0) {
                    session()->put(base64_encode($mobile), $this->code);
                    session()->save();
                    return response()->json([
                        'code'  => 2000,
                        'data'  => ''
                    ]);
                } else if ($code == 33 || $code == 22 ||$code == 8 || $code == 9 | $code == 43) {
                    return response()->json([
                        'code'  => 3001,
                        'msg'  => '操作过频繁'
                    ]);
                }
            } else {
                return response()->json([
                    'code'  => 5001,
                    'msg'  => '未知错误'
                ]);
            }
            return ;
        }
    }

    /**
     * create code
     * @return int
     */
    private function createCode()
    {
       return $this->code = rand(100000, 999999);
    }

    /**
     * create short message text
     * @return string
     */
    private function createText()
    {
        return $this->text = "【方片记忆科技公司】您的验证码是".$this->code."。如非本人操作，请忽略本短信";
    }

}

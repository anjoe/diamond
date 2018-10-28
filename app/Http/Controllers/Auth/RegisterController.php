<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $message = [
            'required' => '数据不能为空',
            'min' => '数据不合法',
            'max' => '数据不合法',
            'string' => '数据不合法',
            'unique' => '用户已存在',
            'regex:/^1+[35678]+\\d{9}/' => '数据不合法'
        ];

        return Validator::make($data, [
            'mobile' => 'required|regex:/^1+[35678]+\\d{9}/|unique:users',
            'code' => 'required|string|min:6|max:6',
            'password' => 'required|string|min:6',
            'passwordConfirm' => 'required|string|min:6',
        ], $message);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        return User::create([
            'mobile'    => $data['mobile'],
            'password'  => $data['password'],
            'permission'=> 0
        ]);
    }

    /**
     * 用户注册
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        if ($request->isMethod('post')) {
            $validator = $this->validator($request->all());
            if ($validator->fails()) {
                $errors = $validator->errors()->first();
                if ($errors == "数据不能为空") {
                    $code = 1001;
                } else if ($errors == "数据不合法") {
                    $code = 1002;
                } else if ($errors == "用户已存在") {
                    $code = 1003;
                } else {
                    $code = 5001;
                }
                return response()->json([
                    'code' => $code,
                    'msg' => $errors
                ]);
            }
            //判断密码是否一致
            $password = request('password');
            $passwordConfirm = request('passwordConfirm');
            if ($password != $passwordConfirm) {
                return response()->json([
                    'code'  => 3001,
                    'msg'   => '密码不一致'
                ]);
            }
            $mobile = request('mobile');
            $code_request = request('code');
            $code = session(base64_encode($mobile));
//            $code = Redis::get('name');
//            var_dump($code);
//            var_dump($code_request);
            // 判断验证码是否正确
            if ($code == $code_request) {
                session()->put(base64_encode($mobile), null);
                $data = [
                    'mobile'    => $mobile,
                    'password'  => bcrypt($password)
                ];
                $res = $this->create($data);
                if (!$res) {
                    return response()->json([
                        'code' => 5001,
                        'msg' => '未知错误'
                    ]);
                }
                return response()->json([
                    'code' => 2000,
                    'data' => $mobile
                ]);
            } else {
                return response()->json([
                    'code'  => 3002,
                    'msg'   => '验证码错误'
                ]);
            }
        }
    }
}

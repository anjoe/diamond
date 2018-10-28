<?php

namespace App\Http\Controllers\Auth;


use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Http\Controllers\Auth\TokenController as Token;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * 验证数据后是否合法
     * @param $data
     * @return mixed
     */
    private function validator($data)
    {
        $message = [
            'required'  => '数据不能为空',
            'regex:^((1[3,5,8][0-9])|(14[5,7])|(17[0,6,7,8])|(19[7]))\\d{8}$' => '数据不合法',
            'string' => '数据不合法',
            'min' => '数据不合法'
        ];
        return Validator::make($data, [
            'mobile'    => ['required', 'regex:/^((1[3,5,8][0-9])|(14[5,7])|(17[0,6,7,8])|(19[7]))\\d{8}$/'],
            'password'      => 'required|string|min:6'
            ], $message);
    }

    /**
     * 用户登录
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request)
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
            $credentials = $request->only('mobile', 'password');
            if (Auth::attempt($credentials)) {
                $result = DB::table('users')
                    ->where('mobile', $credentials['mobile'])
                    ->update(array(
                        'updated_at'    => Carbon::now()
                    ));
                $user = DB::table('users')
                    ->where('mobile', request('mobile'))
                    ->first();
                $tokenObject = new Token();
                $token = $tokenObject->createToken($user->id, $user->mobile);
//                var_dump($token);
                return response()->json([
                    'code' => 2000,
                    'data' => [
                        'userId'    => $user->id,
                        'mobile'    => $user->mobile
                    ]
                ])->header('Authorization', $token);
            } else {
                return response()->json([
                    'code'  => 3001,
                    'msg'   => '用户名或密码错误'
                ]);
            }
        }
    }
}

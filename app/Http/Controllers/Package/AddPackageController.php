<?php

namespace App\Http\Controllers\Package;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Models\Package;
use App\Http\Controllers\Auth\TokenController as Token;

class AddPackageController extends Controller
{
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
            'string' => '数据不合法',
            'numeric' => '数据不合法'
        ];

        return Validator::make($data, [
            'description' => 'required|string',
            'cover' => 'required|string',
            'type' => 'required|numeric',
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
//        return Package::create($data);
        $package = new Package();
        $package->description = $data['description'];
        $package->cover = $data['cover'];
        $package->type = $data['type'];
        $package->card_num = $data['card_num'];
        $package->user_id = $data['user_id'];
        $package->save();
    }

    /**
     * 用户添加卡包
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addPackage(Request $request)
    {
        if ($request->isMethod('post')) {
            $tokenObject = new Token();
            $userId = $tokenObject->getUserId();

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
            $data['user_id'] = $userId;
//            $data['user_id'] = 1;
            $data['description'] = request('description');
            $data['cover'] = request('cover');
            $data['type'] = request('type');
            $data['card_num'] = 0;

            $ret = $this->create($data);
            if (!$ret) {
                return response()->json([
                    'code'  => 2000,
                    'data'  => [
                        'userId'    => $data['user_id'],
                        'description'    => $data['description'],
                        'cover'    => $data['cover'],
                        'type'    => $data['type'],
                        'cardNum'   => $data['card_num']
                    ]
                ]);
            } else {
                return response()->json([
                    'code' => 5001,
                    'msg' => '未知错误'
                ]);
            }
        }
    }
}

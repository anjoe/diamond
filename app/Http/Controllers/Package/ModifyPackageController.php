<?php

namespace App\Http\Controllers\Package;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Models\Package;
use Carbon\Carbon;

class ModifyPackageController extends Controller
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
            'numeric' => '数据不合法',
            'exists' => '卡包不存在',

        ];

        return Validator::make($data, [
            'name' => 'required|string',
            'cover' => 'required|string',
            'type' => 'required|numeric',
            'id' => 'required|numeric|exists:packages',
        ], $message);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function update($id, array $data)
    {
        $package = Package::find($id);
        if (!$package) {
            return -1;
        }
        $package->update($data);


        return 0;
    }

    /**
     * 用户修改卡包信息
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function modifyPackage(Request $request)
    {
        if ($request->isMethod('post')) {
            $validator = $this->validator($request->all());
            if ($validator->fails()) {
                $errors = $validator->errors()->first();
                if ($errors == "数据不能为空") {
                    $code = 1001;
                } else if ($errors == "数据不合法") {
                    $code = 1002;
                } else if ($errors == "卡包不存在") {
                    $code = 1003;
                } else {
                    $code = 5001;
                }
                return response()->json([
                    'code' => $code,
                    'msg' => $errors
                ]);
            }
            $id = request('id');
            $data['user_id'] = 1;
            $data['name'] = request('name');
            $data['cover'] = request('cover');
            $data['type'] = request('type');
            $data['card_num'] = 0;
            $data['updated_at'] = Carbon::now();

            $ret = $this->update($id, $data);
//            dd($ret);
            if ($ret == -1) {
                return response()->json([
                    'code' => 1003,
                    'msg' => '卡包不存在'
                ]);
            }
            else if ($ret == 0) {
                $ret = Package::find($id)->first();
                return response()->json([
                    'code'  => 2000,
                    'data'  => [
                        'id'    => $ret->getAttribute('id'),
                        'name'    => $ret->getAttribute('name'),
                        'cover'    => $ret->getAttribute('cover'),
                        'type'    => $ret->getAttribute('type'),
                        'userId'    => $ret->getAttribute('user_id'),
                        'createdAt'    => strtotime($ret->getAttribute('created_at')),
                        'updatedAt'    => strtotime($ret->getAttribute('updated_at'))
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

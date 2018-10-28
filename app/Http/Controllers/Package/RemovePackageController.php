<?php

namespace App\Http\Controllers\Package;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Models\Package;

class RemovePackageController extends Controller
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
            'numeric' => '数据不合法',
            'exists' => '卡片不存在',

        ];

        return Validator::make($data, [
            'id' => 'required|numeric|Exists:packages',
        ], $message);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function destroy($id)
    {
        $package = Package::find($id);
        if (!$package) {
            return -1;
        }
        $package->delete();
        if ($package->trashed()) {
            return 0;
        }
        return 1;
    }

    /**
     * 用户删除卡包
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function removePackage(Request $request)
    {
        if ($request->isMethod('post')) {
            $validator = $this->validator($request->all());
            if ($validator->fails()) {
                $errors = $validator->errors()->first();
                if ($errors == "数据不能为空") {
                    $code = 1001;
                } else if ($errors == "数据不合法") {
                    $code = 1002;
                } else if ($errors == "卡片不存在") {
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
            $ret = $this->destroy($id);
            if (!$ret) {
                return response()->json([
                    'code'  => 2000,
                    'data'  => [
                        'id'    => $id
                    ]
                ]);
            } else if ($ret == -1){
                return response()->json([
                    'code' => 1003,
                    'msg' => '卡片不存在'
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

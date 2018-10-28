<?php

namespace App\Http\Controllers\Upload;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use zgldh\QiniuStorage\QiniuStorage;
use Illuminate\Support\Facades\Validator;

class UploadPictureController extends Controller
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
            'required' => '数据不能为空'
        ];

        return Validator::make($data, [
            'image' => 'required'
        ], $message);
    }

    /**
     * 使用七牛云上传文件
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function uploadPicture(Request $request)
    {
        $validator = $this->validator($request->all());
        if ($validator->fails()) {
            $errors = $validator->errors()->first();
            if ($errors == "数据不能为空") {
                $code = 1001;
            } else {
                $code = 5001;
            }
            return response()->json([
                'code' => $code,
                'msg' => $errors
            ]);
        }
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $extension = $file->getClientOriginalExtension();
            $filetype = ['jpg', 'jpeg', 'gif', 'bmp', 'png'];
            if (!in_array($extension, $filetype)) {
                return response()->json([
                    'code'  => 3001,
//                    'msg'   => $file,
                    'msg'   => '文件不是图片'
                ]);
            }
            $upload = new UploadController();
            if (($imgurl = $upload->upload($file, 'diamond')) == false) {
                return '头像上传失败';
            } else {
                $imgurl = "http://img.creatshare.com/" . $imgurl;

                return response()->json([
                    'code'  => 2000,
                    'data'  => [
                        'imageUrl' => $imgurl,
                    ]
                ]);
            }
        } else {
            return response()->json([
                'code'  => '3002',
                'msg'   => '没有文件'
            ]);
        }
    }
}

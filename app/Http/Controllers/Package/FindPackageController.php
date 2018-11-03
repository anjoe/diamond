<?php

namespace App\Http\Controllers\Package;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Models\Package;
use App\Http\Controllers\Auth\TokenController as Token;

class FindPackageController extends Controller
{
    // 用户查找自己的所有卡片
    public function findPackage() {
//        $tokenObject = new Token();
//        $userId = $tokenObject->getUserId();
        $userId = 3;
        $packages = Package::where('user_id', $userId)->get();
//        var_dump($data);
        $data = array();
        for ($i = 0; $i < count($packages); ++$i) {
            $data[$i]['id'] = $packages[$i]->getAttribute('id');
            $data[$i]['userId'] = $packages[$i]->getAttribute('user_id');
            $data[$i]['name'] = $packages[$i]->getAttribute('name');
            $data[$i]['cover'] = $packages[$i]->getAttribute('cover');
            $data[$i]['cardNum'] = $packages[$i]->getAttribute('card_num');
            $data[$i]['type'] = $packages[$i]->getAttribute('type');
            $data[$i]['createdAt'] = strtotime($packages[$i]->getAttribute('created_at'));
            $data[$i]['updatedAt'] = strtotime($packages[$i]->getAttribute('updated_at'));
        }
        return response()->json([
            'code'  => 2000,
            'data'  => $data
        ]);
    }
}

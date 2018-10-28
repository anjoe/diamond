<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

class TokenController extends Controller
{
    /**
     * header
     * @var array
     */
    private $header = [
        "type" => "token",
        "alg"  => "HS256"
    ];

    /**
     * the id of user
     * @var integer $userId
     */
    private $userId;

    /**
     * the permission of user
     * @var string $permission
     */
    private $permission;

    /**
     * TokenController constructor.
     */
    public function __construct()
    {
        $this->userId = -1;
        $this->permission = -1;
    }

    /**
     * create payload
     * @param $userId
     * @param $permission
     * @return array
     */
    private function payload($userId, $permission)
    {
        return [
            "iss"       => "http://api.creatshare.com",
            "iat"       => $_SERVER['REQUEST_TIME'],
            "exp"       => $_SERVER['REQUEST_TIME'] + 7200,
            "GivenName" => "CreatShare",
            "userId"  => $userId,
            "permission"=> $permission
        ];
    }

    /**
     * encode data
     * @param $data
     * @return string
     */
    private function encode($data)
    {
        return base64_encode(json_encode($data));
    }

    /**
     * generate a signature
     * @param $header
     * @param $payload
     * @param string $secret
     * @return string
     */
    private function signature($header, $payload, $secret = 'secret')
    {
        return hash_hmac('sha256', $header.$payload, $secret);
    }

    /**
     * generate a token
     * @param $userId
     * @param $permission
     * @return string
     */
    public function createToken($userId, $permission)
    {
        $header = $this->encode($this->header);
        $payload = $this->encode($this->payload($userId, $permission));
        $signature = $this->signature($header, $payload);

        return $header . '.' .$payload . '.' . $signature;
    }

    /**
     * check a token
     * @param $jwt
     * @param string $key
     * @return array|string
     */
    public function checkToken($jwt, $key = 'secret')
    {
        $token = explode('.', $jwt);
        if (count($token) != 3)
            return 'token invalid';

        list($header64, $payload64, $sign) = $token;
        if ($this->signature($header64 , $payload64) !== $sign)
            return 'token invalid';
        $header = json_decode(base64_decode($header64), JSON_OBJECT_AS_ARRAY);
        $payload = json_decode(base64_decode($payload64), JSON_OBJECT_AS_ARRAY);

        if ($header['type'] != 'token' || $header['alg'] != 'HS256')
            return 'token invalid';
        if ($payload['iss'] != 'http://api.creatshare.com' || $payload['GivenName'] != 'CreatShare')
            return 'token invalid';

        if (isset($payload['exp']) && $payload['exp'] < time())
            return 'timeout';
        $this->userId = $payload['userId'];
        $this->permission = $payload['permission'];

        return 0;
    }

    /**
     * get a token
     * @return null
     */
    public function getToken()
    {
        $token = null;
        if (isset($_SERVER['HTTP_AUTHORIZATION']))
            $token = $_SERVER['HTTP_AUTHORIZATION'];
        return $token;
    }

    /**
     * get the id of member
     * @return int $userId
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * get the permission of member
     * @return string permission
     */
    public function getUserPermission()
    {
        return $this->permission;
    }
}

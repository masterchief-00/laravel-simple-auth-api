<?php

namespace App\Http\Controllers;

use App\Helper\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function register(Request $req)
    {
        $response=(new UserService($req->email,$req->password,$req->names))->register($req->deviceName);
        return response()->json($response);
    }
    public function login(Request $req)
    {
        $response=(new UserService($req->email,$req->password,''))->login();
        return response()->json($response);
    }
}

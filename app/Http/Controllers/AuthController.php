<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * 用户注册
     * @param Request $request
     * @return mixed
     * @throws \Illuminate\Validation\ValidationException
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6'],
            'src' => 'bail|active_url|max:255'
        ], [

            'name.required' => '邮箱必须!',
            'name.email' => '邮箱格式不正确!',
            'name.string' => '邮箱必须是字符格式!',
            'name.max' => '邮箱长度不超过255个字符!',
            'name.unique' => '邮箱已存在!',
            'password.required' => '密码必须!',
            'password.min' => '密码最少6位数字!',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first(), 'status' => -1], 422);
        }

        // 在数据库中创建用户并返回
        $email = $request->name;
        try {
            $user = User::create([
                'name' => substr($email, 0, strpos($email, '@')),
                'email' => $email,
                'avatar' => $request->src,
                'password' => Hash::make($request->password),
            ]);
            if ($user) {
                return response()->json(['message' => '注册成功!', 'status' => 1, 'data' => $user], 201);
            } else {
                return response()->json(['message' => '注册失败!', 'status' => -1], 423);
            }
        } catch (QueryException $exception) {
            return response()->json(['message' => '注册失败!', 'status' => -1, 'data' => '保存用户到数据库异常：' . $exception->getMessage()], 423);
        }

    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string',
            'password' => 'required|string'
        ], [
            'name.required' => '用户名必须!',
            'name.string' => '用户名必须是字符!',
            'email.required' => '邮箱必须!',
            'email.email' => '邮箱格式不正确!',
            'email.string' => '邮箱必须是字符格式!',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator, 'status' => -1], 422);
        }


        $credentials = [
            'email' => $request->email,
            'password' => $request->password
        ];

        if (Auth::attempt($credentials)) {
            $token = Auth::user()->createToken('wechats')->accessToken;
            return response()->json(['message' => 'Authorised', 'status' => 1, 'data' =>[
                'token' => $token
            ]], 200);
        } else {
            return response()->json(['message' => 'UnAuthorised', 'status' => -1], 401);
        }

    }


    public function logout()
    {
        if (Auth::check()) {
            Auth::user()->token()->delete();
        }

        return response()->json(['message' => 'Logout', 'status' => 1], 204);
    }
}

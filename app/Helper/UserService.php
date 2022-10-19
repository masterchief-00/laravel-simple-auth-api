<?php

namespace App\Helper;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Log;

class UserService
{
    public $email, $password, $name;

    public function __construct($email, $password, $name)
    {
        $this->email = $email;
        $this->password = $password;
        $this->name = $name;
    }
    public function validateInput($auth = false)
    {
        $validationRule = $auth ? 'exists:users' : 'unique:users';
        $validator = Validator::make([
            'email' => $this->email,
            'password' => $this->password,
            'name' => $this->name
        ], [
            'email' => ['required', 'email:rfc,dns', $validationRule],
            'password' => ['required', 'string', Password::min(8)]
        ]);

        if ($validator->fails()) {
            return ['status' => false, 'messages' => $validator->messages()];
        } else {
            return ['status' => true];
        }
    }
    public function register($deviceName)
    {

        $validate = $this->validateInput();
        if (!$validate['status']) {
            return $validate;
        } else {
            $user = new User();
            $user->name = $this->name;
            $user->email = $this->email;
            $user->password = Hash::make($this->password);
            $user->save();

            $token = $user->createToken($deviceName)->plainTextToken;
            return ['status' => true, 'token' => $token, 'user' => $user];
        }
    }
    public function login()
    {
        $validate = $this->validateInput(true);
        if (!$validate['status']) {
            return $validate;
        } else {
            $user = User::where('email', $this->email)->first();
            if ($user) {
                if (Hash::check($this->password, $user->password)) {
                    return ['status' => true, 'user' => $user];
                } else {
                    return ['status' => false, 'messages' => ['password' => 'Incorrect password']];
                }
            }
        }
    }
}

<?php

namespace App\Http\Controllers;

use Validator;
use App\User;
use Illuminate\Http\Request;
use App\Http\Requests;

class UserController extends Controller
{
    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function getUsers()
    {
        $users = $this->user
            ->orderBy('email', 'ASC')
            ->get();

        return json_encode($users);
    }

    public function postAdd(Request $request)
    {
        $response = [];

        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:145',
        ]);

        if ($validator->fails()) {
            $response = ['status' => false, 'message' => 'The email is not valid.'];
            return json_encode($response);
        }

        $email = $request->email;

        if(empty($email))
        {
            $response = ['status' => false, 'message' => 'The email is required.'];
            return json_encode($response);
        }

        if($this->user->exists($email))
        {
            $response = ['status' => false, 'message' => 'The user '.$email.' already exist in database.'];
            return json_encode($response);
        }

        $user = new $this->user;
        $user->email = $email;
        $user->key = $this->user->generateKey($email);
        $user->save();

        $response = ['status' => true, 'message' => 'User created successfully!', 'key' => $user->key];

        return json_encode($response);
    }

    public function showKeyError()
    {
        $response = ['status' => false, 'message' => 'Invalid Key'];
        return json_encode($response);
    }
}

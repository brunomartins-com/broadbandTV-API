<?php

namespace App\Http\Controllers;

use App\Log;
use Validator;
use App\User;
use Illuminate\Http\Request;
use App\Http\Requests;

class UserController extends Controller
{
    private $user;
    private $log;

    public function __construct(User $user, Log $log)
    {
        $this->user = $user;
        $this->log  = $log;
    }

    /**
     * Get all users
     *
     * @return json
     */
    public function getList()
    {
        $users = $this->user->getAll();
        return json_encode($users);
    }

    /**
     * Add new user and return it's key
     *
     * @param Request
     *
     * @return json
     */
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

        if(empty($email)) {
            $response = ['status' => false, 'message' => 'The email is required.'];
            return json_encode($response);
        }

        if($this->user->exists($email)) {
            $response = ['status' => false, 'message' => 'The user '.$email.' already exist in database.'];
            return json_encode($response);
        }

        $user           = new $this->user;
        $user->email    = $email;
        $user->key      = $this->user->generateKey();
        $user->save();

        $response = [
            'status'    => true,
            'message'   => 'User created successfully!',
            'key'       => $user->key
        ];

        // Record log
        $this->log->logAdd($response['message'], $user->key, __CLASS__, __METHOD__, $user->id);

        return json_encode($response);
    }

    /**
     * Return a error when middleware identify that the key is unavailable
     *
     * @return json
     */
    public function showKeyError()
    {
        $response = ['status' => false, 'message' => 'Invalid Key'];
        return json_encode($response);
    }
}

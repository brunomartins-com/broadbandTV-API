<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = "user";

    public function exists($email)
    {
        $userCount = $this->where('email', '=', $email)->count();

        if ($userCount > 0) {
            return true;
        }

        return false;
    }

    public function generateKey($email)
    {
        $key = str_random(32);
        $keyCount = $this->where('key', '=', $key)->count();
        if($keyCount > 0)
        {
            $this->generateKey($email);
        }
        return $key;
    }
}

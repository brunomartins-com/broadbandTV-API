<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = "user";

    /**
     * Get all users
     * 
     * @return mixed
     */
    public function getAll()
    {
        return $this->orderBy('email', 'ASC')->get();
    }

    /**
     * Check if user exists
     *
     * @param User e-mail
     * @return bool
     */
    public function exists($email)
    {
        $userCount = $this->where('email', '=', $email)->count();

        if ($userCount > 0) {
            return true;
        }

        return false;
    }

    /**
     * Generate a Key for user. It will be used all the API long.
     *
     * @return String
     */
    public function generateKey()
    {
        $key = str_random(32);
        $keyCount = $this->where('key', '=', $key)->count();
        if($keyCount > 0)
        {
            $this->generateKey();
        }
        return $key;
    }
}

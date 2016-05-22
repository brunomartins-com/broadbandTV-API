<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    protected $table = "log";

    public function logAdd($action, $key, $entity_name, $entity_method, $entity_id)
    {
        $log = new Log();
        $log->action        = $action;
        $log->key           = $key;
        $log->entity_name   = $entity_name;
        $log->entity_method = $entity_method;
        $log->entity_id     = $entity_id;
        $log->save();
    }
}

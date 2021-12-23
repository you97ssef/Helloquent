<?php

namespace src\Models;

use Helloquent\Model;

class Person extends Model
{
    // name TEXT
    // age INTEGER
    // role TEXT

    protected static $table = "people";

    public function cars()
    {
        return $this->has_many(Car::class, "id_person");
    }
}

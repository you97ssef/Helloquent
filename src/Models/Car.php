<?php

namespace src\Models;

use Helloquent\Model;

class Car extends Model
{
    // matricule TEXT
    // type TEXT
    // model INTEGER
    // id_person INTEGER 
    // FOREIGN KEY(id_person) REFERENCES people(id) ON DELETE CASCADE 

    protected static $table = "cars";

    protected static $primaryKey = "matricule";

    public function person()
    {
        return $this->belongs_to(Person::class, "id_person");
    }
}

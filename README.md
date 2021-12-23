# Helloquent

    This is a php ORM i created while studying. it uses pdo to connect with mysql and sqlite databases.

## Database

The configuration od the database is in the file config/config.ini

- Database driver: sqlite or mysql ==> driver = sqlite
- Database Name ==> schema = dbName
- if driver is mysql ==>
  - Database host ==> host = localhost
  - Database username ==> username = root
  - Database password ==> password = password

```ini
[database]
driver = sqlite
schema = db
host = localhost
username = root
password = pwd
```

### To Generate Database

run this command on the terminal 

```sh
php generateDb
```

## Tables

Tables are represented by PHP classes that extends from Model class table name should be specified in attribute called $table.

The primary key can be specefied in attribute called $primaryKey, or without specifing it the ORM generate one by default id autoincrement integer.

Columns and constraints can be specified as comments on the class.

Associations between tables:

- Has many: function inside it return $this->has_many(Class::class, "ForeignColumn");
- Belongs To: function inside it return $this->belongs_to(Class::class, "ForeignColumn");

```php
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
```

```php
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
```

### To Generate Database Tables

run this command on the terminal (this command can also create sqlite database)

```sh
php generateDbTables
```

## CRUD

### Create

`$object->insert()` return primary key value if its inserted otherwise it return -1
#### Example
```php
$youssef = new Person();
$youssef->name = "Youssef BAHI";
$youssef->age = 24;
$youssef->role = "ORM Creator";

if($youssef->insert() !== -1)
    echo "<br>Person added: " . json_encode($youssef->getData()) . "<br>";


$person1 = new Person();
$person1->name = "John Doe";
$person1->age = 100;
$person1->role = "Example";

echo "<br>Person added Id: " . $person1->insert() . "<br>";


$otherWayToConstructObject = new Person([
    "name" => "Dohn Joe",
    "age" => 69,
    "role" => "other Way To Construct Object"
]);

if ($otherWayToConstructObject->insert() !== -1)
    echo "<br>Person added: " . json_encode($otherWayToConstructObject->getData()) . "<br>";
```

### READ

All Table Data: `Table::all();` return array of Table objects

First: 

- `Table::first(1);` return when primary key === 1
- `Table::first(["column", "condition", "value"]);` return first from condition
- `Table::first([["column1", "condition1", "value1"],["column2", "condition2", "value2"],...]);` return first from the conditions

Find: 

- `Table::find(["column", "condition", "value"]);` return array from condition
- `Table::find([["column1", "condition1", "value1"],["column2", "condition2", "value2"],...]);` return array from the conditions
```php
echo "<br>All people: <br>";
$all = Person::all();
foreach ($all as $value) {
    echo json_encode($value->getData()) . "<br>";
}

$personId1 = Person::first(1);
echo "<br>Person with id == 1: " . json_encode($personId1->getData()) . "<br>";

$personId2 = Person::first(2);
echo "<br>Person with id == 2: " . json_encode($personId2->getData()) . "<br>";

$firstOver99 = Person::first([["age", ">=", 100]]);
echo "<br>First Person with age >= 100: " . json_encode($firstOver99->getData()) . "<br>";

echo "<br>All people with age >= 69: <br>";
$peopleOver69 = Person::find(["age", ">=", 69]);
foreach ($peopleOver69 as $value) {
    echo json_encode($value->getData()) . "<br>";
}
```

### UPDATE

`$object->update()` return 1 if updated otherwise it return -1
```php
// Add new Person for test
$personToUpdate = new Person();
$personToUpdate->name = "John Doe";
$personToUpdate->age = 100;
$personToUpdate->role = "Example";

if ($personToUpdate->insert() !== -1)
    echo "<br>Person added: " . json_encode($personToUpdate->getData()) . "<br>";

$personToUpdate = Person::first($personToUpdate->id);

$personToUpdate->name = "Updated name";
$personToUpdate->age = 50;
$personToUpdate->role = "Updated Example";

if ($personToUpdate->update() !== -1)
    echo "<br>Person updated: " . json_encode($personToUpdate->getData()) . "<br>";

echo "<br>Getting updated Person : <br>";
$updatedPerson = Person::first($personToUpdate->id);

echo "Updated Person new Data: " . json_encode($updatedPerson->getData()) . "<br>";
```

### DELETE

`$object->delete()` return 1 if deleted otherwise it return -1
```php
// Added new Person for test
$personToDelete = new Person();
$personToDelete->name = "John Doe";
$personToDelete->age = 100;
$personToDelete->role = "Example";

if ($personToDelete->insert() !== -1)
    echo "<br>Person added: " . json_encode($personToDelete->getData()) . "<br>";

if ($personToDelete->delete() !== -1)
    echo "<br>Person Deleted: " . json_encode($personToDelete->getData()) . "<br>";

echo "<br>Getting deleted Person : <br>";
$deletedPerson = Person::first($personToDelete->id);
try {
    echo json_encode($deletedPerson->getData()) . "<br>"; // Error because it doesn't exist anymore
} catch (\Throwable $th) {
    echo "Error: " . $th->getMessage() . "<br>";
}
```

### ASSOCIATIONS

`$object->foreignObject()` OR `$object->foreignObject` return object or array if exists otherwise it return null. (foreignObject is name of method that contains return of has_many or belongs_to)
```php
// Added new car for test
$youssef207 = new Car();
$youssef207->matricule = substr(md5(microtime()), rand(0, 26), 5);;
$youssef207->type = "PEUGEOT";
$youssef207->model = 2003;
$youssef207->id_person = $youssef->id;

if ($youssef207->insert() !== -1)
    echo "<br>Car added: " . json_encode($youssef207->getData()) . "<br>";

// Added new car for test
$youssefGolf = new Car([
    "matricule" => substr(md5(microtime()), rand(0, 26), 5),
    "type" => "VW GOLF PLUS",
    "model" => 2006,
    "id_person" => $youssef->id
]);

if ($youssefGolf->insert() !== -1)
    echo "<br>Car added: " . json_encode($youssefGolf->getData()) . "<br>";

// Added new car for test
$personGolf7 = new Car();
$personGolf7->matricule = substr(md5(microtime()), rand(0, 26), 5);;
$personGolf7->type = "VW GOLF 7";
$personGolf7->model = 2015;
$personGolf7->id_person = $updatedPerson->id;

if ($personGolf7->insert() !== -1)
    echo "<br>Car added: " . json_encode($personGolf7->getData()) . "<br>";


// Has Many
echo "<br>Getting Youssef Cars: (Has Many)<br>";
foreach ($youssef->cars as $value) {
    echo "Car: " . json_encode($value->getData()) . "<br>";
}


// Belongs To
echo "<br>Getting Person that has the Golf7: (Belongs To)<br>";
$golf7 = Car::first($personGolf7->matricule);
echo "Person: " . json_encode($golf7->person()->getData()) . "<br>";
```
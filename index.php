<?php

// Class loader ---------------------------------------------------------------------------------------------------------------------
require_once "src/utils/ClassLoader.php";
$loader = new src\utils\ClassLoader('.');
$loader->register();
// ----------------------------------------------------------------------------------------------------------------------------------


// Uses -----------------------------------------------------------------------------------------------------------------------------
use Helloquent\ConnectionFactory;
use src\Models\Car;
use src\Models\Person;
// ----------------------------------------------------------------------------------------------------------------------------------


// Database -------------------------------------------------------------------------------------------------------------------------
$keys = parse_ini_file("config/config.ini");
ConnectionFactory::makeConnection($keys, dirname(__FILE__));
// ----------------------------------------------------------------------------------------------------------------------------------


// ----------------------------------------------------------------------------------------------------------------------------------
// ---------------------------------------------------------------- C.R.U.D ---------------------------------------------------------
// ----------------------------------------------------------------------------------------------------------------------------------

// CREATE ---------------------------------------------------------------------------------------------------------------------------
echo "CREATE ------------------------------------------------------------------------------------------------------------------<br>";

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

if ($person1->insert() !== -1)
    echo "<br>Person added: " . json_encode($person1->getData()) . "<br>";


$otherWayToConstructObject = new Person([
    "name" => "Dohn Joe",
    "age" => 69,
    "role" => "other Way To Construct Object"
]);

if ($otherWayToConstructObject->insert() !== -1)
    echo "<br>Person added: " . json_encode($otherWayToConstructObject->getData()) . "<br>";

echo "<br>-------------------------------------------------------------------------------------------------------------------------------<br><br>";
// ----------------------------------------------------------------------------------------------------------------------------------


// READ -----------------------------------------------------------------------------------------------------------------------------
echo "READ --------------------------------------------------------------------------------------------------------------------<br>";

echo "<br>All people: <br>";
$all = Person::all();
foreach ($all as $value) {
    echo json_encode($value->getData()) . "<br>";
}

$personId1 = Person::first(1);
echo "<br>Person with id == 1: " . json_encode($personId1->getData()) . "<br>";

$personId2 = Person::first(2);
echo "<br>Person with id == 2: " . json_encode($personId2->getData()) . "<br>";

$firstOver99 = Person::first(["age", ">=", 100]);
echo "<br>First Person with age >= 100: " . json_encode($firstOver99->getData()) . "<br>";

echo "<br>All people with age >= 69: <br>";
$peopleOver69 = Person::find(["age", ">=", 69]);
foreach ($peopleOver69 as $value) {
    echo json_encode($value->getData()) . "<br>";
}

echo "<br>-------------------------------------------------------------------------------------------------------------------------------<br><br>";
// ----------------------------------------------------------------------------------------------------------------------------------


// UPDATE ---------------------------------------------------------------------------------------------------------------------------
echo "UPDATE -------------------------------------------------------------------------------------------------------------------<br>";

// Added new Person for test
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

echo "<br>-------------------------------------------------------------------------------------------------------------------------------<br><br>";
// ----------------------------------------------------------------------------------------------------------------------------------


// DELETE ---------------------------------------------------------------------------------------------------------------------------
echo "DELETE -------------------------------------------------------------------------------------------------------------------<br>";

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

echo "<br>-------------------------------------------------------------------------------------------------------------------------------<br><br>";
// ----------------------------------------------------------------------------------------------------------------------------------


// ASSOCIATIONS ---------------------------------------------------------------------------------------------------------------------
echo "ASSOCIATIONS ------------------------------------------------------------------------------------------------------------<br>";

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

echo "<br>-------------------------------------------------------------------------------------------------------------------------------<br><br>";
// ----------------------------------------------------------------------------------------------------------------------------------

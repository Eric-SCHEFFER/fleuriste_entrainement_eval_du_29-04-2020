<?php


namespace App\Models;

class UserTools
{

    private $databaseTools;
    private $houseTools;

    public function __construct($databaseTools)
    {
        $this->databaseTools = $databaseTools;
        $this->houseTools = new HouseTools($databaseTools);
    }

    public function addNewUser($user){
        $params = ["name" => $user->getName()];
        $this->databaseTools->insertQuery('INSERT INTO User (user_name) VALUES (:name)', $params);
    }


    function hydrateUser($user, $datas){
        $user->setId($datas['user_id']);
        $user->setName($datas['user_name']);
        return $user;
    }

    public function getAllUsers(){
        $results = $this->databaseTools->executeQuery('SELECT * FROM User INNER JOIN House ON User.user_house = House.house_id');
        $users = [];
        foreach ($results as $result) {
            $user = new User();
            $user = $this->hydrateUser($user, $result);
            $house = new House();
            $house = $this->houseTools->hydrateHouse($house, $result);
            $user->setHouse($house);
            array_push($users, $user);
        }
        return $users;
    }

    public function getAllUsersByHouseId($houseId) {
        $results = $this->databaseTools->executeQuery("SELECT * FROM User WHERE user_house = $houseId");
        $users = [];
        foreach ($results as $result) {
            $user = new User();
            $user = $this->hydrateUser($user, $result);
            $user->setHouse($result['user_house']);
            array_push($users, $user);
        }
        return $users;
    }

    public function getUserByName($name){
        $result = $this->databaseTools->selectByNameInTable('User', $name);
        $user = new User();
        $user = $this->hydrateUser($user, $result);
        return $user;
    }
}
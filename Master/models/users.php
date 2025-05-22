<?php

// User Base Class
class User {
    public $userId;
    public $firstName;
    public $lastName;
    public $loginName;
    public $userType;

    public function __construct($userId, $firstName, $lastName, $loginName, $userType) {
        $this->userId = $userId;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->loginName = $loginName;
        $this->userType = $userType;

    }
    public function __toString() {
        return $this->firstName . ' ' . $this->lastName . ' (' . $this->loginName . ')';
    }
    public function getEmail() {
        return $this->loginName;
    }
}

// Admin Class
class Admin extends User {
    
    public function __construct($userId, $firstName, $lastName, $loginName) {
        parent::__construct($userId, $firstName, $lastName, $loginName, 2);
    }
}

// Teacher Class
class Teacher extends User {
    public $consultations = [];

    public function __construct($userId, $firstName, $lastName, $loginName) {
        parent::__construct($userId, $firstName, $lastName, $loginName, 1);
    }
}

// Student Class
class Student extends User {
    public $reservations = [];

    public function __construct($userId, $firstName, $lastName, $loginName) {
        parent::__construct($userId, $firstName, $lastName, $loginName, 0);
    }
}
?>

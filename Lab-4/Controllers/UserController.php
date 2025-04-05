<?php

namespace Controllers;
use Models\UserModel;
class UserController {
    private $user;

    public function __construct($name) {
        echo "UserController створено! <br>";
        $this->user = new UserModel($name);
    }

    public function getUserName() {
        return $this->user->getName();
    }
}

<?php

namespace Models;
class UserModel {
    private $name;

    public function __construct($name) {
        $this->name = $name;
        echo "UserModel створено для користувача: $name <br>";
    }

    public function getName() {
        return $this->name;
    }
}
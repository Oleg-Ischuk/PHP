<?php

namespace task8;

abstract class Human implements CleanHouse
{
    public function __construct($name, $age, $height, $weight) {
        $this->name = $name;
        $this->age = $age;
        $this->height = $height;
        $this->weight = $weight;
    }

    protected $name;
    public function getName(){
        return $this->name;
    }
    public function setName($name){
        $this->name = $name;
    }

    protected $age;
    public function getAge() {
        return $this->age;
    }
    public function setAge($age) {
        $this->age = $age;
    }

    protected $height;
    public function getHeight() {
        return $this->height;
    }
    public function setHeight($height) {
        $this->height = $height;
    }

    protected $weight;
    public function getWeight() {
        return $this->weight;
    }
    public function setWeight($weight) {
        $this->weight = $weight;
    }

    public function birthChild($name, $height, $weight) {
        echo "<br>Народилася дитина: $name, Зріст: $height см, Вага: $weight кг.<br>";
        $this->onBirth();
    }

    protected abstract function onBirth();
}
<?php

namespace task8;

class Student extends Human
{
    public $university;
    public function getUniversity() {
        return $this->university;
    }
    public function setUniversity($university) {
        $this->university = $university;
    }

    public $course;
    public function getCourse() {
        return $this->course;
    }
    public function setCourse($course) {
        $this->course = $course;
    }

    public function transferToNewCourse() {
        $this->course++;
    }

    protected function onBirth() {
        echo "Повідомлення: поява нового студента.<br>";
    }

    public function cleanRoom(){
        echo "Студент прибирає кімнату<br>";
    }

    public function cleanKitchen(){
        echo "Студент прибирає кухню<br>";
    }
}
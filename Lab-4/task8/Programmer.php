<?php

namespace task8;

class Programmer extends Human
{
    private $languages = [];
    public function getLanguages() {
        return $this->languages;
    }
    public function setLanguages(array $languages) {
        $this->languages = $languages;
    }

    private $experience;
    public function getExperience() {
        return $this->experience;
    }
    public function setExperience($experience) {
        $this->experience = $experience;
    }

    public function addLanguage($language) {
        if (!in_array($language, $this->languages)) {
            $this->languages[] = $language;
        }
    }

    protected function onBirth() {
        echo "Повідомлення: створення нового програміста.<br>";
    }

    public function cleanRoom(){
    echo "Програміст прибирає кімнату<br>";
    }

    public function cleanKitchen(){
        echo "Програміст прибирає кухню<br>";
    }
}
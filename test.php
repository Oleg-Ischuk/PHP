<?php
class Person
{
    private $privateA = "private";
    public $publicA = "public";
    protected $protectedA = "protected";
}

$person = new Person();

echo $person->publicA;
echo $person->publicA;
echo $person->protectedА;
?>

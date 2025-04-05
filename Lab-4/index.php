<?php
require_once 'autoload.php';

use Controllers\UserController;
use Views\UserView;
use Models\Circle;
use text\TextTasks;
use task8\Student;
use task8\Programmer;

echo "<h3>Завдання 1</h3>";
$controller = new UserController("Олег");
$view = new UserView();
$view->render($controller->getUserName());

echo "<h3>Завдання 5</h3>";
$circle = new Circle(3.0, 11.0, 4);
echo $circle;

$circle->setX(5.0);
$circle->setY(14.0);
$circle->setRadius(6.0);
echo "<br>Оновлене: " . $circle . "<br><br>";

$circle1 = new Circle(6.0, 10.0, 3.5);
$circle2 = new Circle(6.0, 12.0, 4.0);
echo "<br>Чи перетинаються кола? " . ($circle1->circlesIntersect($circle2) ? "Так<br>" : "Ні<br>");

echo "<h3>Завдання 7</h3>";
TextTasks::writeText("test1.txt", "Рядок у файлі");
TextTasks::writeText("test2.txt", "Рядок у файлі");
TextTasks::writeText("test3.txt", "Рядок у файлі");

echo "<br>Вміст файлу test1.txt:<br>" . nl2br(TextTasks::readText("test1.txt")) . "<br>";
echo "<br>Вміст файлу test2.txt:<br>" . nl2br(TextTasks::readText("test2.txt")) . "<br>";
echo "<br>Вміст файлу test3.txt:<br>" . nl2br(TextTasks::readText("test3.txt")) . "<br>";

TextTasks::clearText("test1.txt");
echo "Вміст test1 після очищення:<br>" . nl2br(TextTasks::readText("test1.txt")) . "<br>";

TextTasks::clearText("test2.txt");
echo "Вміст test2 після очищення:<br>" . nl2br(TextTasks::readText("test2.txt")) . "<br>";

echo "<h3>Завдання 8</h3>";
$student = new Student("Олег", 18, 189, 70);
$student->setUniversity(" Житомирська політехніка");
$student->setCourse(2);
$student->transferToNewCourse();
$student->setHeight(187);
$student->setWeight(70);

echo "Студент: {$student->getUniversity()}, Курс: {$student->getCourse()}, Зріст: {$student->getHeight()}, Вага: {$student->getWeight()}<br><br>";

$programmer = new Programmer("Олена", 25, 165, 60);
$programmer->setExperience(5);
$programmer->setLanguages(["PHP", "JavaScript"]);
$programmer->addLanguage("Python");
$programmer->setHeight(168);
$programmer->setWeight(62);

echo "Програміст: Досвід: {$programmer->getExperience()} років, Мови: " . implode(", ", $programmer->getLanguages()) . ", Зріст: {$programmer->getHeight()}, Вага: {$programmer->getWeight()}";

$student->birthChild("Михайло", 50, 3.5);
$student->cleanRoom();
$student->cleanKitchen();

$programmer->birthChild("Анастасія", 47, 3.2);
$programmer->cleanRoom();
$programmer->cleanKitchen();
?>
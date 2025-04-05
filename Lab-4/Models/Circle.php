<?php

namespace Models;

class Circle
{
    private $x;
    public function getX() {
        return $this->x;
    }

    public function setX($x) {
        $this->x = $x;
    }

    private $y;
    public function getY() {
        return $this->y;
    }
    public function setY($y) {
        $this->y = $y;
    }

    private $radius;
    public function getRadius() {
        return $this->radius;
    }
    public function setRadius($radius) {
        $this->radius = $radius;
    }

    public function __construct($x, $y, $radius) {
        $this->x = $x;
        $this->y = $y;
        $this->radius = $radius;
        echo "Коло створено з центром ($x, $y) та радіусом $radius <br>";
    }

    public function __toString() {
        return "<br>Коло з центром в ({$this->x}, {$this->y}) і радіусом {$this->radius}";
    }

    public function circlesIntersect(Circle $otherCircle) {
        $distance = sqrt(pow($this->x - $otherCircle->getX(), 2) + pow($this->y - $otherCircle->getY(), 2));
        return $distance <= ($this->radius + $otherCircle->getRadius());
    }
}
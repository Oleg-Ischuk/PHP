<?php

namespace Views;
class UserView {
    public function render($name) {
        echo "<h2>Користувач: $name</h2>";
    }
}
#/bin/bash

env php src/check.php &
php -S 0.0.0.0:8080

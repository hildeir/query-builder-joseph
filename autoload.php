<?php
spl_autoload_register(function ($className){
    $change = '/';
    $r = str_replace("\\",$change,$className);
    include $r . ".php";
});
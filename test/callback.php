<?php
$msg = "Word";

function callback(){
    $callback1 = function(){
        echo 'hello';
    };
    $callback1();
}

callback();

$example = function ($arg) use ($msg){
    var_dump($arg.' '.$msg);
};

$example('Hello');
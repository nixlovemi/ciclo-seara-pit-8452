<?php

function dd()
{
    ob_clean();
    echo '<pre>';
    foreach (func_get_args() as $x) {
        var_dump($x);
    }
    die;
}

function readConfig()
{
    $file = file_get_contents('config.json');
    return json_decode($file, true);
}
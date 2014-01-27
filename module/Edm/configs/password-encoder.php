<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require('module.config.php');

if (!isset($argv[0])) {
    echo "Please pass in a human readable password.";
    return;
}

file_put_contents('_generated_password.txt', hash('sha256', EDM_SALT . $argv[0] . EDM_PEPPER));


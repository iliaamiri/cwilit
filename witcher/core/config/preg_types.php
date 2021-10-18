<?php
return array(
    'username' => '/^[a-zA-Z0-9-_]*$/i' ,
    'password' => '/^[a-zA-Z0-9-_+?!$@#*\s]*$/i' ,
    'text' => '/^[a-zA-Z0-9آ-ی-.،,*:;()<>"+=@&?؟ !#\/_\s]*$/u' ,
    'number' => '/^[0-9]*$/u' ,
    'alphabet' => '/^[a-zA-Z]*$/i' ,
    'alphabet-space' => '/^[a-zA-Z\s]*$/i' ,
    'alphabet-persian' => '/^[a-zA-Zآ-ی]*$/u' ,
    'alphabet-persian-space' => '/^[a-zA-Zآ-ی\s]*$/u' ,
    'md5' => '/^[a-z0-9]*$/i'
);
<?php 
    function getDB():PDO 
    { 
        $database=new PDO("mysql:host=localhost;dbname=ecom;charset=utf8",'root','');
        return $database;
    }
    

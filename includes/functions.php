<?php
    require_once(__DIR__.'/db.php');

    function getProducts(string $url):array{
        $data=file_get_contents($url);
        $response=json_decode($data,true);
        return $response["products"];
    }

    function getUsers():array 
    {
        $database=getDB();
        $stmt=$database->query("SELECT * FROM users");
        $users=$stmt->fetchAll(PDO::FETCH_ASSOC);
        return $users;
    }

    
    
    function getProductById($id) {
        $url = "https://dummyjson.com/products/" . urlencode($id);
        $json = file_get_contents($url);
        if ($json === false) {
            return null;
        }
        return json_decode($json, true);
    }

    function getOrders():array{
        $database=getDB();
        $stmt=$database->prepare("SELECT * FROM orders WHERE user_id=? ");
        $stmt->execute([$_SESSION["LOGGED_USER"]["id"]]);
        $orders=$stmt->fetchAll(PDO::FETCH_ASSOC);
        return $orders;
    }

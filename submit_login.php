<?php
    session_start();
    require_once(__DIR__."/includes/functions.php");
    require_once(__DIR__."/includes/db.php");
    $users=getUsers();
    try{
        if(isset($_POST['email'],$_POST['password'])){
            foreach($users as $user){
                if($user["email"]===$_POST["email"]&&$user["password"]===$_POST["password"]){
                    $_SESSION["LOGGED_USER"]=$user;
                    break;
                }
            }
        if(isset($_SESSION["LOGGED_USER"])){
            $database=getDB();
            $stmt=$database->prepare("SELECT * FROM cart WHERE user_id=?");
            $stmt->execute([$_SESSION["LOGGED_USER"]["id"]]);
            $cart=$stmt->fetch();
            if($cart){
                $stmt=$database->prepare("SELECT SUM(quantity) FROM cart_item WHERE cart_id=?");
                $stmt->execute([$cart["id"]]);
                $_SESSION['product_number']=$stmt->fetchColumn()??0;
                header("Location:index.php");
            }
        }
    }
    }catch(Exception $e){
        $errorMessage=$e->getMessage();
    }
?>
<?php if(isset($errorMessage)):?>
    <?php $title="Error Page" ;
      ob_start();?>
    <div class="alert alert-danger" role="alert">
         <?= $errorMessage?>
    </div>
    <?php $content=ob_get_clean();
          require_once(__DIR__.'/layout.php');
    ?>
<?php endif;?>

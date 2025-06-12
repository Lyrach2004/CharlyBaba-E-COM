<?php
    session_start();
    try{
        require_once(__DIR__.'/includes/db.php');
        if(isset($_GET['cart_id'])){
            $database=getDB();
            $stmt=$database->prepare("UPDATE cart SET is_validated=1 WHERE id=?");
            $stmt->execute([(int)$_GET['cart_id']]);
            if(isset($_GET['action'])&&$_GET['action']==='order'){
                if(isset($_GET['cart_item'])&&(int)$_GET['cart_item']>0){
                    $stmt=$database ->prepare("SELECT * FROM cart_item WHERE id=?"); 
                    $stmt->execute([$_GET["cart_item"]]);
                    $cart=$stmt->fetch(PDO::FETCH_ASSOC);
                    $_SESSION["product_number"]-=$cart['quantity'];
                    $stmt=$database->prepare("INSERT INTO orders(user_id,product_id,total) VALUES(:user_id,:product_id,:total)");
                    $stmt->execute([
                        "user_id"=>$_SESSION["LOGGED_USER"]["id"],
                        "product_id"=>$cart["product_id"],
                        "total"=>$cart['total']
                    ]);
                    $stmt=$database->prepare("DELETE FROM  cart_item WHERE id=?");
                    $stmt->execute([$_GET['cart_item']]);    
                    header('Location:profile.php?ordered=1');
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

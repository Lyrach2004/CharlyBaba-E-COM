<?php
    require_once(__DIR__.'/includes/db.php');
    try{
        if(isset($_POST['name'],$_POST['surname'],$_POST['email'],$_POST['password'])){
            if(filter_var($_POST["email"],FILTER_VALIDATE_EMAIL)){   
                    $database=getDB();
                    $stmt=$database->prepare("INSERT INTO users(name,surname,email,password) VALUES (:name,:surname,:email,:password)");
                    $stmt->execute([
                        'name'=>htmlspecialchars($_POST['name']),
                        'surname'=>htmlspecialchars($_POST['surname']),
                        'email'=>htmlspecialchars($_POST['email']),
                        'password'=>htmlspecialchars($_POST['password'])
                    ]);
                    header("Location:login.php");
            }else{
                $_SESSION["errorMessage"]="Email invalide";
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

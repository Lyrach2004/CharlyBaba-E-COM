<?php
    session_start();
    require_once(__DIR__.'/includes/db.php');
    require_once(__DIR__.'/includes/functions.php');

if (!isset($_SESSION['LOGGED_USER'])) {
    header('Location: login.php');
    exit();
}

$database = getDB();


if(isset($_GET['action'])){
    if($_GET['action']==='ClearCart'){
        //Vider le panier
        $stmt=$database->query('DELETE FROM cart_item');
        $_SESSION['product_number']=0;
    }
    elseif($_GET['action']==='DeleteItem'){
        if(isset($_GET['cart_item'])&&(int)$_GET['cart_item']>0){
            $stmt=$database ->prepare("SELECT quantity FROM cart_item WHERE id=?"); 
            $stmt->execute([$_GET["cart_item"]]);
            $_SESSION["product_number"]-=$stmt->fetchColumn();  
            $stmt=$database->prepare("DELETE FROM cart_item WHERE id=?");
            $stmt->execute([$_GET['cart_item']]);      
        }
    }
}

// Récupérer le panier de l'utilisateur
$stmt = $database->prepare("SELECT * FROM cart WHERE user_id = :user_id ORDER BY created_at DESC");
$stmt->execute(['user_id' => $_SESSION['LOGGED_USER']['id']]);
$cart = $stmt->fetch();

$cartItems = [];
if ($cart) {
    $stmt = $database->prepare("SELECT * FROM cart_item WHERE cart_id = :cart_id AND is_ordered= :is_ordered");
    $stmt->execute([
        'cart_id' => $cart['id'],
        'is_ordered'=>0

]);
    $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
?>

<?php $title="Mon Panier";
  ob_start();
  require_once(__DIR__.'/includes/header.php');
?>

<?php if (!empty($cartItems)): ?>
    <div class="container with-nav d-flex justify-content-between">
        <h1 class="display-6 fw-bold">
            <i class="fa-solid fa-cart-shopping"></i> Mon panier</h1>
        <a class="btn btn-outline-danger text-decoration-none" href="?action=<?=urlencode('ClearCart')?>"><i class="fa-solid fa-trash text-danger"></i>
        Vider le panier</a>
    </div>
    <?php foreach ($cartItems as $cartItem): ?>
        <?php $product = getProductById($cartItem["product_id"]); ?>
        <div class="container">
            <div class="card mt-3">
                <div class="d-flex">
                    <div class="flex-shrink-0">
                        <img src="<?= $product["thumbnail"] ?>" class="img-fluid rounded-start" alt="product-img" style="width: 150px; height: auto;">
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <div class="card-body">
                            <h5 class="card-title"><?= $product["title"] ?></h5>
                            <p class="card-text"><?= $product["description"] ?></p>
                            <p>
                               <span class="fw-bold me-3 product-total"><?= $cartItem["total"] ?>€</span>
                                <input type="number"
                                min="1"
                                max="<?= $product["stock"] ?>"
                                value="<?= $cartItem["quantity"] ?>"
                                class="me-3 quantity-input"
                                data-price="<?= $product["price"] ?>"
                                 data-id="<?= $cartItem["id"] ?>">
                                <a class="text-decoration-none" href="?action=<?=urlencode('DeleteItem')?>&cart_item=<?=urlencode($cartItem['id'])?>">
                                    <i class="fa-solid fa-trash text-danger"></i>
                                </a>
                            </p>
                            <a class="btn btn-primary text-decoration-none" href="validate_command.php?cart_id=<?=urlencode($cartItem['cart_id'])?>&action=<?=urlencode('order')?>&cart_item=<?=urlencode($cartItem['id'])?>"> Commander</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php else:?>
    <div class="container">
         <div class="card mb-3 with-nav p-3">
            <div class="row g-0">
                <div class="col-md-4">
                    <span class="text-primary"><i class="fa-solid fa-cart-shopping"></i> Votre panier est vide </span>         
                </div>
                <div class="col-md-8">
                <div class="card-body">
                    <p class="card-text text-center">
                        Découvrez nos produits et ajoutez-les à votre panier
                    </p>
                    <div class="text-center mt-2">
                        <a href="index.php" class="btn btn-outline-primary">Continuer mes achats</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
<?php endif; ?>
<script>
    document.querySelectorAll('.quantity-input').forEach(input => {
    input.addEventListener('change',(e) =>{
        const newQuantity = parseInt(e.target.value);
        const price = parseFloat(e.target.dataset.price);
        const totalElement = e.target.closest('p').querySelector('.product-total');

        if (!isNaN(newQuantity) && !isNaN(price)) {
            const newTotal = (newQuantity * price).toFixed(2);
            totalElement.textContent = newTotal + '€';
            }
        });
    }); 
</script>

<?php 
    $content=ob_get_clean();
    require_once(__DIR__.'/layout.php');
?>

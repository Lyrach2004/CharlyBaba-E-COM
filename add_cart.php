<?php 

    session_start();
    require_once(__DIR__.'/includes/db.php');
    require_once(__DIR__.'/includes/functions.php');

try {
    if (!isset($_SESSION["LOGGED_USER"])) {
        header("Location: login.php");
        exit;
    }

    if (isset($_POST["product_id"], $_POST["quantity"], $_POST["price"])) {
        $product_id = (int) $_POST["product_id"];
        $quantity = (int) $_POST["quantity"];
        $price = (float) $_POST["price"];

        if ($product_id > 0 && $quantity > 0 && $price > 0) {
            $database = getDB();
            $user_id = $_SESSION["LOGGED_USER"]["id"];

            // Vérifier s'il existe déjà un panier actif
            $stmt = $database->prepare("SELECT * FROM cart WHERE user_id = :user_id AND status = 'active'");
            $stmt->execute(["user_id" => $user_id]);
            $cart = $stmt->fetch();

            if (!$cart) {
                $stmt = $database->prepare("INSERT INTO cart(user_id, status) VALUES(:user_id, 'active')");
                $stmt->execute(["user_id" => $user_id]);
                $cart_id = $database->lastInsertId();
            } else {
                $cart_id = $cart["id"];
            }

            // Vérifier si le produit est déjà dans le panier
            $stmt = $database->prepare("SELECT * FROM cart_item WHERE cart_id = :cart_id AND product_id = :product_id");
            $stmt->execute([
                "cart_id" => $cart_id,
                "product_id" => $product_id
            ]);
            $item = $stmt->fetch();

            if ($item) {
                // Mise à jour de la quantité et du total
                $new_quantity = $item["quantity"] + $quantity;
                $new_total = $new_quantity * $price;

                $stmt = $database->prepare("UPDATE cart_item SET quantity = :quantity, total = :total WHERE id = :id");
                $stmt->execute([
                    "quantity" => $new_quantity,
                    "total" => $new_total,
                    "id" => $item["id"]
                ]);
            } else {
                // Insertion d'un nouvel article
                $total = $quantity * $price;
                $stmt = $database->prepare("INSERT INTO cart_item(cart_id, product_id, quantity, price,total) VALUES(:cart_id, :product_id, :quantity, :price, :total)");
                $stmt->execute([
                    "cart_id" => $cart_id,
                    "product_id" => $product_id,
                    "quantity" => $quantity,
                    "price" => $price,
                    "total" => $total,
                ]);
            }
            $stmt=$database->prepare("SELECT SUM(quantity) FROM cart_item WHERE cart_id=?");
            $stmt->execute([$cart['id']]);
            $_SESSION['product_number']=$stmt->fetchColumn();
            header('Location:index.php?added='.urlencode('1'));
            exit;
        }
    }
} catch (Exception $e) {
    $errorMessage = $e->getMessage();
}
?>

<?php if (isset($errorMessage)): ?>
    <?php $title = "Erreur"; ob_start(); ?>
    <div class="alert alert-danger" role="alert">
        <?= htmlspecialchars($errorMessage) ?>
    </div>
    <?php $content = ob_get_clean(); require_once(__DIR__.'/layout.php'); ?>
<?php endif; ?>

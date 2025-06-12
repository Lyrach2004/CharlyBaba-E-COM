<?php
    session_start();
    require_once(__DIR__.'/includes/functions.php');
    $orders=getOrders();
    ob_start();
    // Détecter si la redirection a passé le paramètre "ordered=1"
    $ordered = isset($_GET['ordered']) && $_GET['ordered'] == 1;
    require_once(__DIR__."/includes/header.php");
    $title="Profile";
?>
<div class="container">
    <div class="card with-nav mb-3">
        <div class="card-body">
            <h4 class="card-title mb-3">Mon profil</h4>
            <h6 class="card-subtitle mb-3">
                <?= $_SESSION["LOGGED_USER"]["name"].' '. $_SESSION["LOGGED_USER"]["surname"]?>
            </h6>
            <p class="card-text d-flex justify-content-between">
                <span>Membre depuis</span>
                <span><?= $_SESSION["LOGGED_USER"]["created_at"]?></span>
            </p>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <h4 class="card-title mb-3">Mes commandes</h4>
            <div class="accordion" id="ordersAccordion">
                <?php foreach($orders as $index => $order): ?>
                    <?php
                        // Récupération des données du produit lié à cette commande
                        $product = getProductById($order["product_id"]);
                        // Générer un ID unique pour chaque bloc accordion
                        $accordionId = 'order' . $order['id'];
                    ?>
                    <div class="accordion-item mb-2">
                        <!-- Header de l'accordéon -->
                        <h2 class="accordion-header" id="heading<?= $index ?>">
                            <button class="accordion-button collapsed" type="button"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#<?= $accordionId ?>"
                                    aria-expanded="false"
                                    aria-controls="<?= $accordionId ?>">
                                <!-- Ligne résumée -->
                                <div class="d-flex w-100 justify-content-between">
                                    <span><?= 'Commande#' . $order['id'] ?></span>

                                    <?php if (!$order["is_delivered"]): ?>
                                        <span class="badge bg-warning text-dark">En attente</span>
                                    <?php else: ?>
                                        <span class="badge bg-success">Livré</span>
                                    <?php endif; ?>

                                    <span><?= $order['total'] ?>€</span>
                                    <span><?= $order['created_at'] ?></span>
                                </div>
                            </button>
                        </h2>
                        <!-- Contenu déplié -->
                        <div id="<?= $accordionId ?>" class="accordion-collapse collapse" aria-labelledby="heading<?= $index ?>" data-bs-parent="#ordersAccordion">
                            <div class="accordion-body">
                                <div class="d-flex align-items-center">
                                    <!-- Image produit -->
                                    <img src="<?= htmlspecialchars($product['thumbnail']) ?>" alt="Produit" class="img-thumbnail me-3" style="width: 100px; height: auto;">
                                    <!-- Infos produit -->
                                    <div>
                                        <h5><?= htmlspecialchars($product['title']) ?></h5>
                                        <p>Quantité : <?= $order['quantity'] ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
<?php if ($ordered): ?>
    <div class="position-fixed top-0 end-0 p-3" style="z-index: 1055;">
        <div id="orderToast" class="toast align-items-center text-white bg-success border-0 show" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    🎉 Votre commande a bien été enregistrée !
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

    <script>
        // Ferme le toast après 3 secondes
        setTimeout(function () {
            var toast = new bootstrap.Toast(document.getElementById('orderToast'));
            toast.hide();
             // Supprime le paramètre 'ordered' de l'URL
            const url = new URL(window.location);
            url.searchParams.delete('ordered');
            window.history.replaceState({}, document.title, url);
        }, 3000); // 3000 ms = 3 secondes
    </script>
<?php endif; ?>

<?php
    $content=ob_get_clean();
    require_once(__DIR__.'/layout.php');
?>

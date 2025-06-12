<?php
    session_start();
    require_once(__DIR__.'/includes/functions.php');
    $products = getProducts('https://dummyjson.com/products?limit=183');
    $productsPerPage = 12;
    $totalProducts = count($products);
    $totalPages = ceil($totalProducts / $productsPerPage);
    // Récupérer le numéro de la page actuelle depuis l'URL, sinon page 1
    $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $currentPage = max(1, min($totalPages, $currentPage)); // sécurité : entre 1 et $totalPages

    // Calcul de l'index de départ
    $startIndex = ($currentPage - 1) * $productsPerPage;
    $productsToShow = array_slice($products, $startIndex, $productsPerPage);
    $title="Charly Baba Homepage";
    ob_start();
    require_once(__DIR__.'/includes/header.php');
?>
<section class="with-nav">
    <div class="container">
        <div class="row gy-3">
            <?php foreach ($productsToShow as $product): ?>
                <div class="col-md-4 col-lg-3 d-flex">
                    <div class="card d-flex flex-column">
                        <img src="<?= htmlspecialchars($product["thumbnail"]) ?>" class="card-img-top" alt="..." style="height: 180px; object-fit: cover;">
                        <div class="card-body d-flex flex-column">
                            <h6 class="card-title mb-3"><?= htmlspecialchars($product["title"]) ?></h6>
                            <p class="card-text">
                                <span class="text-white bg-primary rounded-3 p-1"><?= htmlspecialchars($product["price"]) ?> €</span>
                            </p>
                            <p class="card-text"><?= mb_substr(htmlspecialchars($product["description"]), 0, 90, 'UTF-8') ?></p>
                            <a href="#" 
                                class="btn btn-primary w-100 mt-auto"
                                data-bs-toggle="modal" 
                                data-bs-target="#details"
                                data-id="<?= htmlspecialchars($product["id"]) ?>"
                                data-title="<?= htmlspecialchars($product["title"]) ?>"
                                data-description="<?= htmlspecialchars($product["description"]) ?>"
                                data-price="<?= htmlspecialchars($product["price"]) ?>"
                                data-stock="<?= htmlspecialchars($product["stock"]) ?>"
                                data-images='<?=  (count($product["images"])>1) ?json_encode($product["images"]):json_encode([$product["images"][0],$product["images"][0]]) ?>'>
                                Voir les détails
                            </a>


                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <!-- Pagination -->
    <nav aria-label="Pagination des produits" class="mt-4">
        <ul class="pagination justify-content-center">
            <li class="page-item <?= $currentPage == 1 ? 'disabled' : '' ?>">
                <a class="page-link" href="?page=1" aria-label="Début">
                    <span aria-hidden="true"><i class="fas fa-angle-double-left"></i></span>
                </a>
            </li>
            <li class="page-item <?= $currentPage <= 1 ? 'disabled' : '' ?>">
                <a class="page-link" href="?page=<?= $currentPage - 1 ?>" aria-label="Précédent">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
            <?php for ($i = 1; $i <= min(5, $totalPages); $i++): ?>
                <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
                    <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>
            <?php if ($totalPages > 6): ?>
            <li class="page-item disabled">
                <span class="page-link">...</span>
            </li>
            <li class="page-item <?= $totalPages == $currentPage ? 'active' : '' ?>">
                <a class="page-link" href="?page=<?= $totalPages ?>"><?= $totalPages ?></a>
            </li>
            <?php endif; ?>
                <li class="page-item <?= $currentPage >= $totalPages ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page=<?= $currentPage + 1 ?>" aria-label="Suivant">
                                <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
                <li class="page-item <?= $currentPage == $totalPages ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page=<?= $totalPages ?>" aria-label="Fin">
                        <span aria-hidden="true"><i class="fas fa-angle-double-right"></i></span>
                    </a>
                </li>
        </ul>
    </nav>
    <!-- Modal -->
    <div class="modal fade" id="details" tabindex="-1" aria-hidden="true" >
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Titre</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="carouselImages" class="carousel  slide" data-bs-ride="carousel">
                <div class="carousel-inner" id="carouselInner"></div>
                <button class="carousel-control-prev" type="button" data-bs-target="#carouselImages" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon"></span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#carouselImages" data-bs-slide="next">
                    <span class="carousel-control-next-icon"></span>
                </button>
                </div>
                <p class="mt-2 d-flex justify-content-between">
                    <span class="text-white bg-primary p-2 rounded-2"><span id="modalPrice"></span>€</span>
                    <span class="text-white bg-success p-2 rounded-2">Stock:<span id="modalStock"></span></span>
                 </p>
                <p class="mt-3" id="modalDescription" style="word-break: break-word;"></p>
                <form action="add_cart.php" method="POST">
                    <input type="hidden" name="product_id" id="productId">
                    <input type="hidden" name="price" id="priceId">
                    <div class="mb-3 row d-flex align-items-end">
                        <div class="col">
                            <label for="quantity" class="form-label">Quantité</label>
                            <input type="number" min="1"  class="form-control" id="quantity" name="quantity" required>
                        </div>
                        <div class="col">
                            <button type="submit" class="btn btn-primary w-100">Ajouter au panier</button>
                        </div>
                    </div>
                </form>
            </div>
            </div>
        </div>
    </div>
        <!--Toast-->
        <div class="position-fixed top-0 end-0 p-3" style="z-index: 1100">
            <div id="liveToast" class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="d-flex">
                    <div class="toast-body">
                        Le produit a été ajouté avec succès
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Fermer"></button>
                </div>
            </div>
        </div>
</section>

<?php if (isset($_GET['added'])&&(int)$_GET['added']===1): ?>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const toast = new bootstrap.Toast(document.getElementById('liveToast'));
        toast.show();

        // Utilisation de setTimeout pour masquer le toast après 3 secondes (3000 ms)
        setTimeout(() => {
            toast.hide(); // cache le toast
        }, 3000); // délai en millisecondes (ici 3 secondes)

        // Supprimer le paramètre ?added=1 de l'URL
        if (window.history.replaceState) {
            const url = new URL(window.location);
            url.searchParams.delete('added');
            window.history.replaceState({}, document.title, url.toString());
        }
    });
    </script>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const detailsModal = document.getElementById('details');

  detailsModal.addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    const id = button.getAttribute('data-id');
    const title = button.getAttribute('data-title');
    const description = button.getAttribute('data-description');
    const price = button.getAttribute('data-price');
    const stock = button.getAttribute('data-stock');
    const images = JSON.parse(button.getAttribute('data-images'));

    // Injecte les infos dans la modal
    document.getElementById('modalTitle').textContent = title;
    document.getElementById('modalDescription').textContent = description;
    document.getElementById('modalPrice').textContent = price;
    document.getElementById('modalStock').textContent = stock;
    document.getElementById('quantity').setAttribute('max', stock);

    // Met à jour les champs cachés et input
    document.getElementById('productId').value = id;
    document.getElementById('priceId').value=price;

    // Injecte les images dans le carrousel
    const carouselInner = document.getElementById('carouselInner');
    carouselInner.innerHTML = ''; // reset

    images.forEach((img, index) => {
      const div = document.createElement('div');
      div.className = 'carousel-item' + (index === 0 ? ' active' : '');
      div.innerHTML = `<img src="${img}" class="d-block w-100" style="max-height:300px; object-fit:contain;" alt="Image produit">`;
      carouselInner.appendChild(div);
    });
  });
});
</script>


<?php $content=ob_get_clean();
      require_once(__DIR__.'/layout.php');
      ?>

   
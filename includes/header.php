<nav class="navbar navbar-expand-lg bg-white fixed-top border-bottom">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">
        <img src="../assets/images/logo.jpg" alt="logo-charlybaba" style="width:180px;height:80px;
        ">
        <span class="fw-bold">CharlyBaba</span>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0 d-md-flex flex-md-row justify-content-evenly w-100">
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="../index.php">
            <i class="fa-solid fa-house me-2"></i>
            Accueil
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="../cart.php">
            <?php if($_SESSION['product_number']!==0&&isset($_SESSION["LOGGED_USER"],$_SESSION["product_number"])) :?>
              <span class="position-relative"><i class="fa-solid fa-cart-shopping"></i>Panier
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary ms-3">
                  <?= $_SESSION['product_number'] ?>
                </span>
              </span>
            <?php else: ?>
              <i class="fa-solid fa-cart-shopping"></i>Panier          
            <?php endif ?>
          </a>
        </li>
         <li class="nav-item">
          <a class="nav-link" href="../profile.php">
            <i class="fa-solid fa-user"></i>
            Profil
          </a>
        </li>
        <?php if(!isset($_SESSION["LOGGED_USER"])):?>
          <li class="nav-item">
            <a  class="nav-link" href="../login.php">
              <span class="text-primary fw-bold">Se connecter</span>
            </a>
          </li>
       <?php else: ?>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <span class="bg-primary text-white rounded-5 p-2 me-2"><?= strtoupper(substr($_SESSION["LOGGED_USER"]["surname"], 0, 2)) ?></span>
            <?= $_SESSION["LOGGED_USER"]["name"] . ' ' . $_SESSION["LOGGED_USER"]["surname"] ?>
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item text-danger" href="logout.php">Se déconnecter</a></li>
          </ul>
        </li>
      <?php endif; ?>

      </ul>
    </div>
  </div>
</nav>

<?php 
    $title="Login Charly Baba";
    ob_start();
    require_once(__DIR__.'/includes/header.php');
?>
<div class="container with-nav">
    <div class="row d-flex justify-content-center">
        <div class="col-12 col-md-6 border p-5 rounded-5">
             <form action="submit_login.php" method="POST">
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" aria-describedby="emailHelp" name="email" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary w-100 rounded-3">Se connecter</button>
            </form>
            <div class="d-flex justify-content-center mt-5">
                <p >Pas encore de compte?
                <a href="register.php" class="text-decoration-none text-white bg-primary p-2 rounded-2">S'inscrire</a>
            </p>
            </div>
        </div>
    </div>
</div>
<?php
    $content=ob_get_clean();
    require_once(__DIR__.'/layout.php');
?>
-

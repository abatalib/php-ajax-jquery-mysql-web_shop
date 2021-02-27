<form method="post" action="">
<input type="hidden" name="frm" value="frmLogin">
    <!-- mail -->
    <div class="input-group mb-3">
        <div class="input-group-append">
            <span class="input-group-text"><i class="fas fa-at"></i></span>
        </div>
        <input type="text" name="mail" class="form-control" placeholder="e-mail" autocomplete = off value="<?php if(isset($_POST['mail'])){echo $_POST['mail'];}?>" required>
    </div>

    <!-- mdp -->
    <div class="input-group mb-2">
        <div class="input-group-append">
            <span class="input-group-text"><i class="fas fa-key"></i></span>
        </div>
        <input type="password" name="pwd" class="form-control" value="" placeholder="mot de passe" required>
    </div>
    <div class="d-flex justify-content-center mt-3 login_container">
        <button type="submit" class="btn btn-outline-primary mt-4 text-white border border-white w-100">Connexion</button>
    </div>
</form>
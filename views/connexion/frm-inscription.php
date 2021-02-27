<form method="post" action="">
<input type="hidden" name="frm" value="frmInscr">
    <!-- username -->
    <div class="input-group mb-2">
        <div class="input-group-append">
            <span class="input-group-text"><i class="fas fa-user"></i></span>
        </div>
        <input type="text" name="username" class="form-control" placeholder="username" autocomplete = off value="<?php if(isset($_POST['username'])){echo $_POST['username'];}?>" required>
    </div>

    <!-- mail -->
    <div class="input-group mb-2">
        <div class="input-group-append">
            <span class="input-group-text"><i class="fas fa-at"></i></span>
        </div>
        <input type="text" name="mail_inscr" class="form-control" placeholder="e-mail" autocomplete = off value="<?php if(isset($_POST['mail_inscr'])){echo $_POST['mail_inscr'];}?>" required>
    </div>

    <!-- mdp -->
    <div class="input-group mb-2">
        <div class="input-group-append">
            <span class="input-group-text"><i class="fas fa-key"></i></span>
        </div>
        <input type="password" name="pwd_inscr" class="form-control" value="" placeholder="Mot de passe" required>
    </div>

    <!-- confirm mdp -->
    <div class="input-group mb-2">
        <div class="input-group-append">
            <span class="input-group-text"><i class="fas fa-key"></i></span>
        </div>
        <input type="password" name="pwd_conf_inscr" class="form-control" value="" placeholder="Confirmation mot de passe" required>
    </div>

    <div class="d-flex justify-content-center mt-3 login_container">
        <button type="submit" class="btn btn-outline-primary mt-4 text-white border border-white w-100">S'inscrire</button>
    </div>
</form>
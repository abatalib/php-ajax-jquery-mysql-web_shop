<?php
$error = '';

if(!empty($_POST)):
    if(isset($_POST['mail']) && !empty($_POST['mail']) &&
        isset($_POST['pwd']) && !empty($_POST['pwd']) &&
        $_POST['frm']=='frmLogin'):

        $mail = strip_tags($_POST['mail']);
        $pwd = strip_tags($_POST['pwd']);

        if (!filter_var($mail, FILTER_VALIDATE_EMAIL)):
            $error = "Format du courriel invalide";
        else:
            $rep = db::login(compact('mail','pwd'));

            //si la réponse comprend @
            //on récupère l'id du client
            $rep = explode('@',$rep);
            if($rep[0]=='ok'):
                $id=$rep[1];
            endif;
            $rep=$rep[0];

            switch($rep)
            {
                case 'not_active':
                    // $link = db::recuperateConfirmationLink($mail);
                    $error = "Compte pas encore activé!<br><button class='btn' id='resend' data='".$mail."'>Cliquer pour renvoyer le lien d'activation</button>";
                    break;
                case  'Err':
                    $error = "Compte ou mot de passe erroné!";
                    break;
                case 'ok':
                    header('location:.');
                    break;
            }
        endif;
    elseif(isset($_POST['mail_inscr']) && !empty($_POST['mail_inscr']) &&
            isset($_POST['pwd_inscr']) && !empty($_POST['pwd_inscr']) &&
            isset($_POST['username']) && !empty($_POST['username']) &&
            $_POST['frm']=='frmInscr'):

        $mail = strip_tags($_POST['mail_inscr']);
        $username = strip_tags($_POST['username']);
        $pwd = strip_tags($_POST['pwd_inscr']);
        $pwd_confirm = strip_tags($_POST['pwd_conf_inscr']);

        if (!filter_var($mail, FILTER_VALIDATE_EMAIL)):
            $error = "Format du courriel invalide";
        else:
            
            if(strlen($pwd)<6)://vérification de la longueur du mdp
                $error = "Longueur incorrect du mot de passe (min 6 caractères)";
            elseif($pwd_confirm!=$pwd): //vérification du mdp et sa confirmation
                $error = "Mot de passe et sa confirmation sont différents!";
            else:
                $rep = db::inscription(compact('mail','username','pwd'));

                switch($rep)
                {
                    case 'mail_already_exist':
                        $error = "Adresse courriel existe déjà!";
                        break;
                    case 'username_already_exist':
                        $error = "Username existe déjà!";
                        break;
                    case  'Err':
                        $error = "Erreur lors de l'ajout!";
                        break;
                    case 'ok':
                        ?><script>
                        window.history.replaceState( null, null, window.location.href );
                        </script><?php
                        break;
                    default:
                        $error=$rep;
                }
            endif;
        endif;
    else:
        $error = "Tous les champs devront être renseignés!";
    endif;
        
endif;

require_once PATH_COMPOSANTS . 'header.php';
// $mdp = password_hash('123456', PASSWORD_BCRYPT);
// echo $mdp;
?>

<link rel="stylesheet" href=<?= PATH_ASSETS . "css".SEP."login.css"?>>

<script src=<?= PATH_ASSETS . "js" . SEP . "functions.js" ?>></script>

<style>
a {
    color: #f2f2f2;
}

a:hover {
    color: #ccc;
}

.container-form {
    min-height: 300px;
    padding-top: 20px;
}

.error-msg {
    color: #dc3545;
    background: white;
    font-size: 13px;
}
#resend {
    font-size: 13px;
}
</style>
<div class="container h-100">
    <div class="d-flex justify-content-center h-100">
        <div class="user_card">
            <div class="d-flex justify-content-center">
                <div class="brand_logo_container">
                    <i class="fas fa-user-circle"></i>
                </div>
            </div>
            <div class="d-flex justify-content-center" style="margin-top: 100px">
                &nbsp;
                <?php
                    if(!empty($error)){
                        echo '<div class="w-100 text-center error-msg">'.$error.'</div>';
                    };
                ?>
            </div>

            <nav class="mt-3">
                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                    <a class="nav-item nav-link <?= ($_POST['frm']!="frmInscr") ? 'active' : '' ?>" id="nav-login-tab"
                        data-toggle="tab" href="#nav-login" role="tab" aria-controls="nav-login" aria-selected="true">
                        Login
                    </a>

                    <a class="nav-item nav-link <?= ($_POST['frm']=="frmInscr") ? 'active' : '' ?>"
                        id="nav-inscription-tab" data-toggle="tab" href="#nav-inscription" role="tab"
                        aria-controls="nav-inscription" aria-selected="false">
                        Inscription
                    </a>
                </div>
            </nav>

            <div class="tab-content" id="nav-tabContent">

                <div class="tab-pane fade <?= ($_POST['frm']!="frmInscr") ? 'show active' : '' ?>" id="nav-login"
                    role="tabpanel" aria-labelledby="nav-login-tab">
                    <div class="d-flex justify-content-center container-form">
                        <?php require 'frm-login.php' ?>
                    </div>
                </div>

                <div class="tab-pane fade <?= ($_POST['frm']=="frmInscr") ? 'show active' : '' ?>" id="nav-inscription"
                    role="tabpanel" aria-labelledby="nav-inscription-tab">
                    <div class="d-flex justify-content-center container-form">
                        <?php require 'frm-inscription.php' ?>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>
<?php
require_once PATH_COMPOSANTS . 'footer.php';
<?php

if(isset($_GET) && !empty($_GET)):
    $mail = $_GET['id'];
    $token = $_GET['token'];
else:
    header("location:login");
    die();
endif;

$rslt = db::activationUser(compact('mail','token'));


include_once PATH_COMPOSANTS . 'header.php';

if($rslt == 1):
    ?>
    <div class="container">
        <div class="row mt-5 mb-5">
            <div class="col-12 text-center">
                <i class="fa fa-check-circle-o" style="font-size: 140px; margin-bottom: 20px; color: #02769F"></i>
                <h4 style="font-weight: bold">Votre compte est activé</h1><br>
                <a href="connexion">Accéder à mon compte</a> ou 
                <a href=".">Allons faire des achats !</a>
            </div>
        </div>
    </div>
    <?php
else:
    ?>
    <div class="container">
        <div class="row mt-5 mb-5">
            <div class="col-12 text-center">
                <img src='assets/images/empty-cart1.png' alt='ZAZ vêtements de femmes, hommes et enfants' style="width: 300px; height: auto">
                <h4 style="font-weight: bold">Compte n'est pas activé</h1><br>
                <a href=".">Allons faire des achats !</a>
            </div>
        </div>
    </div>
    <?php
endif;

include_once PATH_COMPOSANTS . 'footer.php';
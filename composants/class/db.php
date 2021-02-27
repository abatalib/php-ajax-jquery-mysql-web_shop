<?php
/**
 * connexion()
 * connect()
 * deconnect()
 * getRecords()
 * saveProductsJsonToMySQL()
 * saveCategoryJsonToMySQL()
 * saveRelationJsonToMySQL()
 * getProductsBasket()
 * saveCde()
 * login()
 * inscription()
 * resendMail()
 */
class db 
{
    private static $bdd;
    private static $strCon = 'mysql:host=localhost; dbname=db_shop; charset=utf8';
    private static $user = 'root';
    private static $pwd = '';
    private static $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
        ];


    private static function connexion()
    {
        try {
        self::$bdd = new PDO(self::$strCon,self::$user, self::$pwd, self::$options);

            return self::$bdd;
        
        } catch (PDOException $e) {
            print "Erreur !: " . $e->getMessage() . "<br/>";
            die();
        }
    }

    //demande de connexion, si n'y est pas
    //appel de la fonction pour connecter bdd
    function connect()
    {
        if (self::$bdd==null):
            self::connexion();
        endif;

        return self::$bdd;
    }

    //déconnexion de a bd
    function deconnect()
    {
        if (self::$bdd!=null):
            unset($bdd);
        endif;
    }

    function getRecords($table,$one){
        try{
            self::connect();

            $rslt=self::$bdd->prepare($table);
            $rslt->execute();
        
            // Déconnexion de la BDD
            self::deconnect();
            if($one):
                return $rslt->fetch();
            else:
                return $rslt->fetchAll();
            endif;

        } catch (PDOException $e) {
            print "Erreur !: " . $e->getMessage() . "<br/>";
            // die();
        }
    }
    


    function saveProductsJsonToMySQL($params)
    {
        $bdd = self::connect();
        extract($params);

        $sql='INSERT INTO product (ref, name, type, price, shipping, description, manufacturer, image) VALUES(?,?,?,?,?,?,?,?)';

        $stmt = $bdd->prepare($sql);
        $stmt->bindParam(1,$ref, PDO::PARAM_INT);
        $stmt->bindParam(2,$name, PDO::PARAM_STR);
        $stmt->bindParam(3,$type, PDO::PARAM_STR);
        $stmt->bindParam(4,$price, PDO::PARAM_INT);
        $stmt->bindParam(5,$shipping, PDO::PARAM_INT);
        $stmt->bindParam(6,$description, PDO::PARAM_STR);
        $stmt->bindParam(7,$manufacturer, PDO::PARAM_STR);
        $stmt->bindParam(8,$image, PDO::PARAM_STR);

        $stmt->execute();

        self::deconnect();
    }

    function saveCategoryJsonToMySQL($params)
    {
        $bdd = self::connect();
        extract($params);

        $sql='INSERT INTO category (id, name) VALUES(?,?)';

        $stmt = $bdd->prepare($sql);
        $stmt->bindParam(1,$id, PDO::PARAM_STR);
        $stmt->bindParam(2,$name, PDO::PARAM_STR);

        $stmt->execute();

        self::deconnect();
    }

    function saveRelationJsonToMySQL($params)
    {
        $bdd = self::connect();
        extract($params);

        $sql='INSERT INTO r_category_product (id, ref) VALUES(?,?)';

        $stmt = $bdd->prepare($sql);
        $stmt->bindParam(1,$id, PDO::PARAM_STR);
        $stmt->bindParam(2,$ref, PDO::PARAM_INT);

        $stmt->execute();

        self::deconnect();
    }

    function getProductsBasket($refs)
    {
        try
        {
        $bdd = self::connect();

        $sql = "SELECT * FROM product WHERE ref in ($refs)";
        
        $rslt = $bdd->query($sql);

        return $rslt->fetchAll();

        } catch (PDOException $e) {
            die("<div class='container mt-5' style='text-align: center'><h4>Erreur !: " . $e->getMessage() . "</h4><br/><a href='.'>Retour</a></div>");
        }
    }

    function saveCde($params)
    {
        try
        {
            $bdd=self::connect();
            extract($params);

            $dateTime = new DateTime("now", new DateTimeZone('Africa/Casablanca'));
            $order_date = $dateTime->format("Y-m-d H:i:s");

            $sql = "INSERT INTO commande (id_client, id_product, qty, commande_date) VALUES(?,?,?,?)";
            
            $rslt = $bdd->prepare($sql);
            $rslt->bindParam(1, $id_client, PDO::PARAM_INT);
            $rslt->bindParam(2, $ref, PDO::PARAM_INT);
            $rslt->bindParam(3, $qty, PDO::PARAM_INT);
            $rslt->bindParam(4, $order_date, PDO::PARAM_STR);

            $rslt->execute();
            
            self::deconnect();

        } catch (PDOException $e) {
            print "Erreur !: " . $e->getMessage() . "<br/>";
        }
    }


    function login($params)
    {
        try
        {
             
        $bdd=self::connect();
        extract($params);

        $sql = "SELECT * FROM client WHERE mail = ?";
        
        $rslt = $bdd->prepare($sql);
        $rslt->bindParam(1, $mail, PDO::PARAM_STR);

        $rslt->execute();
        $user = $rslt->fetch();

        // Déconnexion de la BDD
        unset($bdd);
        self::deconnect();

        //si pas de user dans la bd, message erreur user ou mdp incorrect
        if(!empty($user)):
            //tester si le compte est activé
            if($user->confirmation_at==''):
                return 'not_active';
            endif;
            //si le pwd est incorrect message d'erreur
            if(!password_verify($pwd,$user->pwd)):
                return 'Err';
            else:
                if (session_status() == PHP_SESSION_NONE)
                session_start();

                //session pour lui affecter panier
                $_SESSION['client']['id_client'] = $user->id;
                $_SESSION['client']['username'] = $user->username;
                $_SESSION['client']['name'] = $user->name;
                return 'ok@'.$user->id;
            endif;
        else:
            return 'Err';
        endif;

        } catch (PDOException $e) {
            print "Erreur !: " . $e->getMessage() . "<br/>";
        }
    } 

    function inscription($params)
    {
        try
        {
             
        $bdd=self::connect();
        extract($params);

        //vérification si adresse existe déjà
        if(self::verifExistUser('mail',$mail)>0):
            return "mail_already_exist";
        endif;

        //vérification username existe déjà
        if(self::verifExistUser('username',$username)>0):
            return "username_already_exist";
        endif;

        //hashage du mdp
        $hash = password_hash($pwd, PASSWORD_BCRYPT);
        //token d'activation du compte
        $token = Functions::generateToken();
        //date d'ajout
        $dateTime = new DateTime("now", new DateTimeZone('Africa/Casablanca'));
        $created_at = $dateTime->format("Y-m-d H:i:s");

        $sql = "INSERT INTO client (username, mail, pwd, created_at, confirmation_token) values(?,?,?,?,?)";

        $rslt = $bdd->prepare($sql);
        $rslt->bindParam(1, $username, PDO::PARAM_STR);
        $rslt->bindParam(2, $mail, PDO::PARAM_STR);
        $rslt->bindParam(3, $hash, PDO::PARAM_STR);
        $rslt->bindParam(4, $created_at, PDO::PARAM_STR);
        $rslt->bindParam(5, $token, PDO::PARAM_STR);

        $rslt = $rslt->execute();

        if($rslt===true):
            //envoyer mail de confirmation
            return Functions::sendActivationMail($mail, $username, $token);
        else:
            return 'Err';
        endif;

        } catch (PDOException $e) {
            print "Erreur !: " . $e->getMessage() . "<br/>";
        }
    }

    function verifExistUser($fld, $valFld)
    {
        $bdd=self::connect();

        $sql = "SELECT * FROM client WHERE $fld = ?";
        
        $rslt = $bdd->prepare($sql);
        $rslt->bindParam(1, $valFld, PDO::PARAM_STR);

        $rslt->execute();
        
        // Déconnexion de la BDD
        unset($bdd);
        self::deconnect();

        return $rslt->rowCount();
    }

    
    function activationUser($params){
        try
        {
            $bdd = self::connect();
            extract($params);
            $sql = 'SELECT * FROM client WHERE mail = ? AND confirmation_token = ?';

            $rslt = $bdd->prepare($sql);
            $rslt->execute([$mail, $token]);
            if($rslt->fetch()):
                return self::activateUser($params);
            endif;

        } catch (PDOException $e) {
            print "<div class='container mt-5' style='text-align: center'><h4>Erreur !: " . $e->getMessage() . "</h4><br/><a href='accueil'>Retour</a></div>";
            die();
        }
    }

    function activateUser($params)
    {
        try
        {
            $bdd = self::connect();
            extract($params);
            
            $date = new DateTime();
            $date_confirmation = $date->format('Y-m-d');

            $sql = 'UPDATE client SET confirmation_at = ? WHERE mail = ?';

            $rslt = $bdd->prepare($sql);
            return $rslt->execute([$date_confirmation, $mail]);    

        } catch (PDOException $e) {
            print "<div class='container mt-5' style='text-align: center'><h4>Erreur !: " . $e->getMessage() . "</h4><br/><a href='accueil'>Retour</a></div>";
            die();
        }  
    }

    function resendMail($mail)
    {
        try
        {
            $bdd = self::connect();

            $sql = 'SELECT * FROM client WHERE mail = ?';

            $rslt = $bdd->prepare($sql);
            $rslt->execute([$mail]);
            $user = $rslt->fetch();
            $token = $user->confirmation_token;
            $username = $user->username;
            
            return compact('username', 'token');

        } catch (PDOException $e) {
            print "<div class='container my-5' style='text-align: center'><h4>Erreur !: " . $e->getMessage() . "</h4><br/><a href='connexion'>Retour</a></div>";
            die();
        } 
    }
}
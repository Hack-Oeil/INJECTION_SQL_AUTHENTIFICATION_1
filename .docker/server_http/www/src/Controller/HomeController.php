<?php
 
namespace App\Controller;

use Yoop\AbstractController;

class HomeController extends AbstractController
{
    public function print() 
    {  
        $user = $this->getUser();
        if($user) {
            if(
                ($user->getId()==3 && $user->getEmail()=='maxime@toitoimontoit.fr')
                ||
                ($user->getId()==8 && $user->getEmail()=='heloise@toitoimontoit.fr')
                ||
                ($user->getId()==2 && $user->getEmail()=='michel@toitoimontoit.fr')
            ) {
                $flag = $this->getFlag();
            }
        }
        return $this->render('home', ['flag' => $flag??null]);
    }

    public function auth() 
    {
        // si authentifié on ne peut plus venir ici
        if($this->isAuthenticated()) return $this->redirectToRoute("/"); 
        return $this->render('auth');
    }

    public function authProcess() 
    {
        // si authentifié on ne peut plus venir ici
        if($this->isAuthenticated()) return $this->redirectToRoute("/"); 

        if(sizeof($_POST)) {
            // Pour éviter le bruteforce en attend 2 secondes par requete
            sleep(2);
            if(!empty($_POST['email']) && is_string($_POST['email']) &&
                !empty($_POST['password']) && is_string($_POST['password'])
            ) {
                $email = $_POST['email'];
                $hash = SHA1($_POST['password']);
                $pdo =  $this->getRepository('User')->getPDO();
                $statement = $pdo->prepare("SELECT id, email, first_name, last_name FROM users WHERE email='$email' AND password='$hash'");
                $statement->execute();
                $statement->setFetchMode(\PDO::FETCH_CLASS, 'App\Entity\User');
                $user = $statement->fetch();
                if($user) {
                    $this->connectUser($user);
                    return $this->redirectToRoute("/"); 
                }
            }
        } 
        return $this->render('auth', ["error" => "Echec d'authentification."]);        
    }

    public function deconnect() 
    {
        unset($_SESSION["user"]);
        $this->redirectToRoute("/"); 
    }    

}
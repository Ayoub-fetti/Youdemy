<?php 
require_once __DIR__ . '/../config/database.php';

class User {
    protected $pdo;
    protected $id;
    protected $nom;  
    protected $email;
    protected $role;
    protected $status;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        if (isset($_SESSION['user_id'])) {
            $this->id = $_SESSION['user_id'];
            // Charger les informations de l'utilisateur
            $stmt = $this->pdo->prepare("SELECT * FROM utilisateurs WHERE id = ?");
            $stmt->execute([$this->id]);
            $user = $stmt->fetch();
            if ($user) {
                $this->nom = $user['nom'];
                $this->email = $user['email'];
                $this->role = $user['role'];
                $this->status = $user['status'];
            }
        }
    }

    // getters 
    public function getId()  { return $this->id; }
    public function getName()  { return $this->nom; }
    public function getEmail()  { return $this->email; }
    public function getRole()  { return $this->role; }
    public function getstatus() { return $this->status; }

    // setters 
    public function setName($nom) { $this->nom = $nom; }
    public function setEmail($email) { $this->email = $email; }
    public function setRole($role) { $this->role = $role; }
    public function setstatus($status) { $this->status = $status; }


    // login method

    public function login($email, $password) {
        $stmt = $this->pdo->prepare("SELECT * FROM utilisateurs WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            $isValid = false;
            if ($user['role'] == 'admin' && $password == $user['password']) {
                $isValid = true;
            } else {
                $isValid = ($password == $user['password']);
            }

            if ($isValid) {
                $this->id = $user['id'];
                $this->nom = $user['nom'];
                $this->email = $user['email'];
                $this->role = $user['role'];
                $this->status = $user['status'];
                
                // Ajoutons le rôle dans la session
                $_SESSION['user_id'] = $this->id;
                $_SESSION['user_email'] = $this->email;
                $_SESSION['role'] = $user['role'];  // Important !
                $_SESSION['status'] = $this->status;
                
                return true;
            }
        }
        return false;
    }

    
   
}

?>
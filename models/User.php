<?php 
require_once __DIR__ . '/../config/database.php';

class User {
    protected $pdo;
    protected $id;
    protected $nom;  
    protected $email;
    protected $role;
    protected $status;
    protected $date_creation;

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
                $this->date_creation = $user['date_creation'];
            }
        }
    }

    // getters 
    public function getId()  { return $this->id; }
    public function getName()  { return $this->nom; }
    public function getEmail()  { return $this->email; }
    public function getRole()  { return $this->role; }
    public function getstatus() { return $this->status; }
    public function getDate_creation() { return $this->date_creation; }

    // setters 
    public function setId($id) { $this->id = $id; }
    public function setName($nom) { $this->nom = $nom; }
    public function setEmail($email) { $this->email = $email; }
    public function setRole($role) { $this->role = $role; }
    public function setstatus($status) { $this->status = $status; }
    public function setDate_creation($date_creation) { $this->date_creation = $date_creation; }


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
                $isValid = password_verify($password, $user['password']);
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

    public function register($nom, $email, $password, $role) {
        try {
            // VErifier si l'email existe dEjA
            $stmt = $this->pdo->prepare("SELECT id FROM utilisateurs WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->rowCount() > 0) {
                return ['success' => false, 'message' => "Cet email est déjà utilisé"];
            }
    
            // Determine default status based on role
            $status = ($role === 'enseignant') ? 'inactif' : 'actif';
    
            // Creer le nouvel utilisateur
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $this->pdo->prepare("INSERT INTO utilisateurs (nom, email, password, role, status) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$nom, $email, $hashedPassword, $role, $status]);
    
            return ['success' => true, 'message' => "Inscription réussie!"];
        } catch(PDOException $e) {
            return ['success' => false, 'message' => "Erreur lors de l'inscription: " . $e->getMessage()];
        }
    }

 
}

?>
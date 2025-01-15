<?php 
class Etudiant extends User {
    public function __construct() {
        $db = new Database();
        $pdo = $db->connect();
        parent::__construct($pdo);
    }
    // fonction pour s'inscrire a un cours
    public function inscrireAuCours($coursId) {
        try {
            // Verifier si l'etudiant est deja inscrit au cours
            $checkQuery = "SELECT COUNT(*) FROM inscriptions WHERE etudiant_id = ? AND cours_id = ?";
            $checkStmt = $this->pdo->prepare($checkQuery);
            $checkStmt->execute([$this->id, $coursId]);
            
            if ($checkStmt->fetchColumn() > 0) {
                return ['success' => false, 'message' => 'Vous êtes déjà inscrit à ce cours'];
            }
            
            // Inserer l'inscription
            $query = "INSERT INTO inscriptions (etudiant_id, cours_id, date_inscription) VALUES (?, ?, NOW())";
            $stmt = $this->pdo->prepare($query);
            $success = $stmt->execute([$this->id, $coursId]);
            
            if ($success) {
                return ['success' => true, 'message' => 'Inscription réussie'];
            } else {
                return ['success' => false, 'message' => 'Erreur lors de l\'inscription'];
            }
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Une erreur est survenue'];
        }
    }
}
?>
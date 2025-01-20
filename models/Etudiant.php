
<?php
require_once 'User.php'; 
class Etudiant extends User {
    public function __construct($pdo) {
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

    // fonction pour afficher les cours de l'etudiant

    public function getCoursInscrit() {
        try {
            $query = "SELECT cours.*, utilisateurs.nom as nom , categories.nom as categorie FROM cours  
            INNER JOIN inscriptions ON cours.id = inscriptions.cours_id 
            INNER JOIN utilisateurs  ON cours.enseignant_id = utilisateurs.id 
            INNER JOIN categories ON cours.categorie_id = categories.id
            WHERE inscriptions.etudiant_id = ?
            ORDER BY inscriptions.date_inscription DESC";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([$this->id]);
                        
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        } catch (PDOException $e) {
            error_log("Exception PDO: " . $e->getMessage());
            return [];
        }
    }

       // fonction pour changer le status d'inscription (cours terminer)
       public function terminerCours($coursId){
        try {
            $query = "UPDATE inscriptions SET status = 'terminer' WHERE etudiant_id = ? AND cours_id = ?";
            $stmt = $this->pdo->prepare($query);
            $success = $stmt->execute([$this->id, $coursId]);
            
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Une erreur est survenue'];
        }
    }
}
?>
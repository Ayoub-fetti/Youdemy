<?php 
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
            $query = "SELECT c.*, u.nom as enseignant_nom, cat.nom as categorie_nom FROM cours c 
                     JOIN inscriptions i ON c.id = i.cours_id 
                     JOIN utilisateurs u ON c.enseignant_id = u.id 
                     JOIN categories cat ON c.categorie_id = cat.id
                     WHERE i.etudiant_id = ?
                     ORDER BY i.date_inscription DESC";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([$this->id]);
                        
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        } catch (PDOException $e) {
            error_log("Exception PDO: " . $e->getMessage());
            return [];
        }
    }
}
?>
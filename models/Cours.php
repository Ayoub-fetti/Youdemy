<?php
require_once __DIR__ . '/../config/database.php';

class Cours {
    private $pdo;
    private $id;
    private $titre;
    private $description;
    private $contenu;
    private $categorie_id;
    private $enseignant_id;
    private $date_creation;

    public function __construct() {
        $database = new Database();
        $this->pdo = $database->connect();
    }

    // fonction pour recuperer tout les cours 
    public function getAllCours() {
        try {
            $query = "SELECT c.*, u.nom as enseignant_nom, cat.nom as categorie_nom 
                     FROM cours c 
                     LEFT JOIN utilisateurs u ON c.enseignant_id = u.id 
                     LEFT JOIN categories cat ON c.categorie_id = cat.id 
                     WHERE u.role = :role
                     ORDER BY c.date_creation DESC";
            
            $stmt = $this->pdo->prepare($query);
            $stmt->bindValue(':role', 'enseignant', PDO::PARAM_STR);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Gérer l'erreur de manière appropriée
            error_log("Erreur dans getAllCours: " . $e->getMessage());
            return [];
        }
    }



// fonction pour recuperer le nom  d'un enseignant qui a cree un cours
  public function getCoursByEnseignant($enseignant_id) {
        try {
            $query = "SELECT c.*, u.nom as enseignant_nom, cat.nom as categorie_nom 
                     FROM cours c 
                     LEFT JOIN utilisateurs u ON c.enseignant_id = u.id 
                     LEFT JOIN categories cat ON c.categorie_id = cat.id 
                     WHERE c.enseignant_id = :enseignant_id 
                     AND u.role = :role
                     ORDER BY c.date_creation DESC";
            
            $stmt = $this->pdo->prepare($query);
            $stmt->bindValue(':enseignant_id', $enseignant_id, PDO::PARAM_INT);
            $stmt->bindValue(':role', 'enseignant', PDO::PARAM_STR);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur dans getCoursByEnseignant: " . $e->getMessage());
            return [];
        }
    }

    // fonction pour recuperer les cours par categorie
    public function getCoursByCategorie($categorie_id) {
        try {
            $query = "SELECT c.*, u.nom as enseignant_nom, cat.nom as categorie_nom 
                     FROM cours c 
                     LEFT JOIN utilisateurs u ON c.enseignant_id = u.id 
                     LEFT JOIN categories cat ON c.categorie_id = cat.id 
                     WHERE c.categorie_id = :categorie_id 
                     AND u.role = :role
                     ORDER BY c.date_creation DESC";
            
            $stmt = $this->pdo->prepare($query);
            $stmt->bindValue(':categorie_id', $categorie_id, PDO::PARAM_INT);
            $stmt->bindValue(':role', 'enseignant', PDO::PARAM_STR);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur dans getCoursByCategorie: " . $e->getMessage());
            return [];
        }
    }

    // Fonction de pagination des cours
    public function getCoursWithPagination($page = 1, $limit = 6, $searchTerm = '') {
        try {
            // Calculer l'offset
            $offset = ($page - 1) * $limit;

            // Requête pour obtenir le nombre total de cours
            $countQuery = "SELECT COUNT(*) as total FROM cours c 
                          LEFT JOIN utilisateurs u ON c.enseignant_id = u.id 
                          LEFT JOIN categories cat ON c.categorie_id = cat.id 
                          WHERE u.role = :role";
            
            // Requête pour obtenir les cours
            $query = "SELECT c.*, u.nom as enseignant_nom, cat.nom as categorie_nom 
                     FROM cours c 
                     LEFT JOIN utilisateurs u ON c.enseignant_id = u.id 
                     LEFT JOIN categories cat ON c.categorie_id = cat.id 
                     WHERE u.role = :role";

            $params = [':role' => 'enseignant'];

            // Ajouter la condition de recherche si un terme est fourni
            if (!empty($searchTerm)) {
                $countQuery .= " AND (c.titre LIKE :search 
                                OR c.description LIKE :search 
                                OR u.nom LIKE :search)";
                
                $query .= " AND (c.titre LIKE :search 
                          OR c.description LIKE :search 
                          OR u.nom LIKE :search)";
                
                $params[':search'] = "%{$searchTerm}%";
            }

            // Ajouter l'ordre et la pagination
            $query .= "ORDER BY c.date_creation DESC LIMIT :limit OFFSET :offset";

            // Executer la requête de comptage
            $stmtCount = $this->pdo->prepare($countQuery);
            foreach ($params as $key => $value) {
                $stmtCount->bindValue($key, $value);
            }
            $stmtCount->execute();
            $totalCours = $stmtCount->fetch(PDO::FETCH_ASSOC)['total'];

            // Executer la requête principale
            $stmt = $this->pdo->prepare($query);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            return [
                'cours' => $stmt->fetchAll(PDO::FETCH_ASSOC),
                'total' => $totalCours,
                'pages' => ceil($totalCours / $limit),
                'current_page' => $page
            ];
        } catch (PDOException $e) {
            error_log("Erreur dans getCoursWithPagination: " . $e->getMessage());
            return [
                'cours' => [],
                'total' => 0,
                'pages' => 0,
                'current_page' => 1
            ];
        }
    }

    // Fonction pour mettre à jour un cours
    // public function updateCours($id, $titre, $description, $contenu, $categorie_id) {
    //     try {
    //         $query = "UPDATE cours 
    //                  SET titre = :titre, 
    //                      description = :description, 
    //                      contenu = :contenu, 
    //                      categorie_id = :categorie_id 
    //                  WHERE id = :id";
            
    //         $stmt = $this->pdo->prepare($query);
    //         $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    //         $stmt->bindValue(':titre', $titre, PDO::PARAM_STR);
    //         $stmt->bindValue(':description', $description, PDO::PARAM_STR);
    //         $stmt->bindValue(':contenu', $contenu, PDO::PARAM_STR);
    //         $stmt->bindValue(':categorie_id', $categorie_id, PDO::PARAM_INT);
            
    //         return $stmt->execute();
    //     } catch (PDOException $e) {
    //         error_log("Erreur dans updateCours: " . $e->getMessage());
    //         return false;
    //     }
    // }
}
?>
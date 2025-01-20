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
            $query = "SELECT cours.*, utilisateurs.nom as enseignant_nom, categories.nom as categorie_nom FROM cours  
                     INNER JOIN utilisateurs ON cours.enseignant_id = utilisateurs.id 
                     LEFT JOIN categories ON cours.categorie_id = categories.id 
                     WHERE utilisateurs.role = 'enseignant'
                     ORDER BY cours.date_creation DESC";
            
            $stmt = $this->pdo->prepare($query);
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
            $query = "SELECT cours.*, utilisateurs.nom as enseignant_nom, categories.nom as categorie_nom 
                     FROM cours 
                     LEFT JOIN utilisateurs  ON cours.enseignant_id = utilisateurs.id 
                     LEFT JOIN categories ON cours.categorie_id = categories.id 
                     WHERE cours.enseignant_id = :enseignant_id
                     AND utilisateurs.role = :role
                     ORDER BY cours.date_creation DESC";
            
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
            $query = "SELECT cours.*, utilisateurs.nom as enseignant_nom, categories.nom as categorie_nom 
                     FROM cours 
                     LEFT JOIN utilisateurs ON cours.enseignant_id = utilisateurs.id 
                     LEFT JOIN categories ON cours.categorie_id = categories.id 
                     WHERE cours.categorie_id = :categorie_id 
                     AND utilisateurs.role = :role
                     ORDER BY cours.date_creation DESC";
            
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
            $countQuery = "SELECT COUNT(DISTINCT cours.id) as total 
                          FROM cours  
                          INNER JOIN utilisateurs ON cours.enseignant_id = utilisateurs.id 
                          LEFT JOIN categories ON cours.categorie_id = categories.id 
                          LEFT JOIN cours_tags ON cours.id = cours_tags.cours_id
                          LEFT JOIN tags ON cours_tags.tag_id = tags.id
                          WHERE utilisateurs.role = 'enseignant'";
            
            // Requête pour obtenir les cours
            $query = "SELECT DISTINCT cours.*, utilisateurs.nom as enseignant_nom, categories.nom as categorie_nom,
                     GROUP_CONCAT(DISTINCT tags.nom) as tags
                     FROM cours  
                     INNER JOIN utilisateurs ON cours.enseignant_id = utilisateurs.id 
                     LEFT JOIN categories ON cours.categorie_id = categories.id 
                     LEFT JOIN cours_tags ON cours.id = cours_tags.cours_id
                     LEFT JOIN tags ON cours_tags.tag_id = tags.id
                     WHERE utilisateurs.role = 'enseignant'";

            $params = [];
            
            // Ajouter la condition de recherche si un terme est fourni
            if (!empty($searchTerm)) {
                $searchCondition = " AND (cours.titre LIKE :search 
                                   OR cours.description LIKE :search 
                                   OR utilisateurs.nom LIKE :search 
                                   OR categories.nom LIKE :search 
                                   OR tags.nom LIKE :search)";
                $countQuery .= $searchCondition;
                $query .= $searchCondition;
                $params[':search'] = "%$searchTerm%";
            }

            $query .= " GROUP BY cours.id, utilisateurs.nom, categories.nom 
                       ORDER BY cours.date_creation DESC 
                       LIMIT :limit OFFSET :offset";

            // Exécuter la requête de comptage
            $countStmt = $this->pdo->prepare($countQuery);
            foreach ($params as $key => $value) {
                $countStmt->bindValue($key, $value);
            }
            $countStmt->execute();
            $totalCours = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];

            // Exécuter la requête principale
            $stmt = $this->pdo->prepare($query);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            $cours = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Calculer le nombre total de pages
            $totalPages = ceil($totalCours / $limit);

            return [
                'cours' => $cours,
                'total' => $totalCours,
                'pages' => $totalPages,
                'current_page' => $page
            ];

        } catch (PDOException $e) {
            error_log("Erreur dans getCoursWithPagination: " . $e->getMessage());
            return [
                'cours' => [],
                'total' => 0,
                'pages' => 0,
                'current_page' => $page
            ];
        }
    }
}
?>
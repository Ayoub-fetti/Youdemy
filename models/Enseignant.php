<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/CoursSpecifique.php';
require_once __DIR__ . '/CoursVideo.php';
require_once __DIR__ . '/CoursPDF.php';
require_once __DIR__ . '/User.php';

class Enseignant extends User {
    private $specialite;
    private $description;
    private $db;

    public function __construct($id = null) {
        parent::__construct($id);
        $database = new Database();
        $this->db = $database->connect();
    }

    // Fonction pour ajouter un cours avec polymorphisme
    public function ajouterCours() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $titre = $_POST['titre'];
                $description = $_POST['description'];
                $categorie_id = $_POST['categorie_id'];
                $type_cours = $_POST['type_cours'];

                // Créer l'instance appropriée selon le type de cours
                if ($type_cours === 'pdf' && isset($_FILES['fichier_pdf'])) {
                    $fichier = $_FILES['fichier_pdf'];
                    $dossier_upload = __DIR__ . '/../uploads/pdf/';
                    
                    if (!file_exists($dossier_upload)) {
                        mkdir($dossier_upload, 0777, true);
                    }

                    $extension = pathinfo($fichier['name'], PATHINFO_EXTENSION);
                    $nom_fichier = uniqid() . '.' . $extension;
                    $chemin_fichier = $dossier_upload . $nom_fichier;

                    if (move_uploaded_file($fichier['tmp_name'], $chemin_fichier)) {
                        $cours = new CoursPDF($titre, $description, $categorie_id, $this->getId(), '/uploads/pdf/' . $nom_fichier);
                    } else {
                        throw new Exception("Erreur lors du téléchargement du fichier PDF");
                    }
                } elseif ($type_cours === 'video' && !empty($_POST['url_video'])) {
                    $url_video = $_POST['url_video'];
                    $cours = new CoursVideo($titre, $description, $categorie_id, $this->getId(), $url_video);
                } else {
                    throw new Exception("Type de cours invalide ou données manquantes");
                }

                // Sauvegarder le cours dans la base de données
                $this->sauvegarderCours($cours);
                
                header('Location: /views/enseignant/enseignant_dash.php?success=1');
                exit();
            } catch (Exception $e) {
                header('Location: /views/enseignant/enseignant_dash.php?error=' . urlencode($e->getMessage()));
                exit();
            }
        }
    }

    private function sauvegarderCours(CoursSpecifique $cours) {
        try {
            $this->db->beginTransaction();
            
            $query = "INSERT INTO cours (titre, description, contenu, type, categorie_id, enseignant_id) 
                     VALUES (:titre, :description, :contenu, :type, :categorie_id, :enseignant_id)";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                ':titre' => $cours->getTitre(),
                ':description' => $cours->getDescription(),
                ':contenu' => $cours->getContenu(),
                ':type' => $cours->getType(),
                ':categorie_id' => $cours->getCategorieId(),
                ':enseignant_id' => $this->getId()
            ]);
            
            $this->db->commit();
            return $this->db->lastInsertId();
        } catch (Exception $e) {
            $this->db->rollBack();
            throw new Exception("Erreur lors de la sauvegarde du cours: " . $e->getMessage());
        }
    }

    public function getMesCours() {
        $query = "SELECT * FROM cours WHERE enseignant_id = :enseignant_id";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':enseignant_id' => $this->getId()]);
        $cours = [];
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if ($row['type'] === 'pdf') {
                $cours[] = new CoursPDF(
                    $row['titre'],
                    $row['description'],
                    $row['categorie_id'],
                    $row['enseignant_id'],
                    $row['contenu']
                );
            } elseif ($row['type'] === 'video') {
                $cours[] = new CoursVideo(
                    $row['titre'],
                    $row['description'],
                    $row['categorie_id'],
                    $row['enseignant_id'],
                    $row['contenu']
                );
            }
        }
        
        return $cours;
    }

    public function getCategories() {
        $query = "SELECT * FROM categories ORDER BY nom";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getNomCategorie($categorie_id) {
        $query = "SELECT nom FROM categories WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':id' => $categorie_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['nom'] : '';
    }

    public function getModifyCoursUrl($cours_id) {
        return "/views/cours/modifier.php?id=" . $cours_id;
    }

    public function getDeleteCoursUrl($cours_id) {
        return "/controllers/cours/supprimer_cours.php?id=" . $cours_id;
    }
}
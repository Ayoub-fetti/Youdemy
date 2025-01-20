<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/CoursSpecifique.php';
require_once __DIR__ . '/CoursVideo.php';
require_once __DIR__ . '/CoursPDF.php';
require_once __DIR__ . '/User.php';

class Enseignant extends User {
    private $specialite;
    private $description;
    protected $db;

    public function __construct($id = null, $pdo = null) {
        if ($pdo === null) {
            $database = new Database();
            $pdo = $database->connect();
        }
        $this->db = $pdo;
        parent::__construct($pdo);
        
        if ($id !== null) {
            $this->loadById($id);
        }
    }

    public function loadById($id) {
        if ($id !== null) {
            $stmt = $this->db->prepare("SELECT * FROM utilisateurs WHERE id = ?");
            $stmt->execute([$id]);
            $user = $stmt->fetch();
            if ($user) {
                $this->id = $user['id'];
                $this->nom = $user['nom'];
                $this->email = $user['email'];
                $this->role = $user['role'];
                $this->status = $user['status'];
                $this->date_creation = $user['date_creation'];
                return true;
            }
        }
        return false;
    }

    // Fonction pour ajouter un cours avec polymorphisme
    public function ajouterCours() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->db->beginTransaction();

                $titre = $_POST['titre'];
                $description = $_POST['description'];
                $categorie_id = $_POST['categorie_id'];
                $type_cours = $_POST['type_cours'];

                // Creer l'instance appropriee selon le type de cours
                if ($type_cours === 'pdf' && isset($_FILES['fichier_pdf'])) {
                    $fichier = $_FILES['fichier_pdf'];
                    $dossier_upload = __DIR__ . '/../public/uploads/pdfs/';
                    
                    if (!file_exists($dossier_upload)) {
                        mkdir($dossier_upload, 0777, true);
                    }

                    $nom_fichier = basename($fichier['name']);
                    $chemin_fichier = $dossier_upload . $nom_fichier;

                    if (move_uploaded_file($fichier['tmp_name'], $chemin_fichier)) {
                        $cours = new CoursPDF($titre, $description, $categorie_id, $this->getId(), '/uploads/pdfs/' . $nom_fichier);
                    } else {
                        throw new Exception("Erreur lors du téléchargement du fichier PDF");
                    }
                } elseif ($type_cours === 'video' && !empty($_POST['url_video'])) {
                    $url_video = $_POST['url_video'];
                    $cours = new CoursVideo($titre, $description, $categorie_id, $this->getId(), $url_video);
                } else {
                    throw new Exception("Type de cours invalide ou données manquantes");
                }

                // Sauvegarder le cours
                $query = "INSERT INTO cours (titre, description, contenu, categorie_id, enseignant_id) 
                         VALUES (:titre, :description, :contenu, :categorie_id, :enseignant_id)";
                
                $stmt = $this->db->prepare($query);
                $stmt->execute([
                    ':titre' => $cours->getTitre(),
                    ':description' => $cours->getDescription(),
                    ':contenu' => $cours->getContenu(),
                    ':categorie_id' => $cours->getCategorieId(),
                    ':enseignant_id' => $cours->getEnseignantId()
                ]);

                $cours_id = $this->db->lastInsertId();

                // Gerer les tags si il y a des tags 
                if (isset($_POST['tags']) && !empty($_POST['tags'])) {
                    $tags = explode(',', $_POST['tags']);  // pour le vergule entre les tags 
                    $this->handleTags($cours_id, $tags);
                }
                

                $this->db->commit();
                header('Location: /views/enseignant/enseignant_dash.php?success=1');
                exit();
            } catch (Exception $e) {
                $this->db->rollBack();
                error_log("Erreur lors de l'ajout du cours: " . $e->getMessage());
                header('Location: /views/enseignant/enseignant_dash.php?error=' . urlencode($e->getMessage()));
                exit();
            }
        }
    }

    private function sauvegarderCours(CoursSpecifique $cours) {
        try {
            $this->db->beginTransaction();
            
            $query = "INSERT INTO cours (titre, description, contenu, categorie_id, enseignant_id) 
                     VALUES (:titre, :description, :contenu, :categorie_id, :enseignant_id)";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                ':titre' => $cours->getTitre(),
                ':description' => $cours->getDescription(),
                ':contenu' => $cours->getContenu(),
                ':categorie_id' => $cours->getCategorieId(),
                ':enseignant_id' => $cours->getEnseignantId()
            ]);

            $cours_id = $this->db->lastInsertId();
            $this->db->commit();
            return $cours_id;

        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
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

    // fonction pour modifier le cours par l'enseignant
    public function modifierCours($cours_id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->db->beginTransaction();

                $titre = $_POST['titre'];
                $description = $_POST['description'];
                $categorie_id = $_POST['categorie_id'];
                $type_cours = $_POST['type_cours'];

                // Verifier si le cours appartient a l'enseignant
                $stmt = $this->db->prepare("SELECT * FROM cours WHERE id = ? AND enseignant_id = ?");
                $stmt->execute([$cours_id, $this->getId()]);
                $cours = $stmt->fetch();

                if (!$cours) {
                    throw new Exception("Cours non trouvé ou vous n'avez pas les droits pour le modifier");
                }

                // modifier des informations de base
                $query = "UPDATE cours SET titre = :titre, description = :description, categorie_id = :categorie_id";
                $params = [
                    ':titre' => $titre,
                    ':description' => $description,
                    ':categorie_id' => $categorie_id,
                    ':id' => $cours_id
                ];

                // Gestion du contenu selon le type
                if ($type_cours === 'pdf' && isset($_FILES['fichier_pdf']) && $_FILES['fichier_pdf']['size'] > 0) {
                    $fichier = $_FILES['fichier_pdf'];
                    $dossier_upload = __DIR__ . '/../public/uploads/pdfs/';
                    
                    if (!file_exists($dossier_upload)) {
                        mkdir($dossier_upload, 0777, true);
                    }

                    $nom_fichier = basename($fichier['name']);
                    $chemin_fichier = $dossier_upload . $nom_fichier;

                    if (move_uploaded_file($fichier['tmp_name'], $chemin_fichier)) {
                        $query .= ", contenu = :contenu";
                        $params[':contenu'] = '/uploads/pdfs/' . $nom_fichier;
                        
                        // Supprimer l'ancien fichier PDF si il existe
                        if ($cours['contenu'] && file_exists(__DIR__ . '/..' . $cours['contenu'])) {
                            unlink(__DIR__ . '/..' . $cours['contenu']);
                        }
                    }
                } elseif ($type_cours === 'video' && !empty($_POST['url_video'])) {
                    $query .= ", contenu = :contenu";
                    $params[':contenu'] = $_POST['url_video'];
                }

                $query .= " WHERE id = :id";
                $stmt = $this->db->prepare($query);
                $stmt->execute($params);

                // Mise à jour des tags
                if (isset($_POST['tags'])) {
                    // Supprimer les anciens tags
                    $stmt = $this->db->prepare("DELETE FROM cours_tags WHERE cours_id = ?");
                    $stmt->execute([$cours_id]);

                    // Ajouter les nouveaux tags
                    if (!empty($_POST['tags'])) {
                        $tags = explode(',', $_POST['tags']);
                        $this->handleTags($cours_id, $tags);
                    }
                }

                $this->db->commit();
                header('Location: /Youdemy/views/enseignant/enseignant_dash.php?edit_success=1');
                exit();
            } catch (Exception $e) {
                $this->db->rollBack();
                error_log("Erreur lors de la modification du cours: " . $e->getMessage());
                header('Location: /Youdemy/views/enseignant/edit_course.php?id=' . $cours_id . '&error=' . urlencode($e->getMessage()));
                exit();
            }
        }
    }

    public function handleTags($cours_id, $tags) {
        foreach ($tags as $tag_name) {
            $tag_name = strtolower(trim($tag_name)); // Normalize tag name
            if (!empty($tag_name)) {
                $stmt = $this->db->prepare("SELECT id FROM tags WHERE nom = ?");
                $stmt->execute([$tag_name]);
                $tag = $stmt->fetch();
                if (!$tag) {
                    $stmt = $this->db->prepare("INSERT INTO tags (nom) VALUES (?)");
                    $stmt->execute([$tag_name]);
                    $tag_id = $this->db->lastInsertId();
                } else {
                    $tag_id = $tag['id'];
                }
                $stmt = $this->db->prepare("INSERT INTO cours_tags (cours_id, tag_id) VALUES (?, ?)");
                $stmt->execute([$cours_id, $tag_id]);
            }
        }
    }

    // Méthodes pour les statistiques
    public function getTotalCours($enseignant_id) {
        $query = "SELECT COUNT(*) as total FROM cours WHERE enseignant_id = :enseignant_id";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['enseignant_id' => $enseignant_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public function getCoursLesPlusUtilises($enseignant_id, $limit = 5) {
        $query = "SELECT cours.titre, COUNT(inscriptions.id) as nombre_inscriptions FROM cours 
        LEFT JOIN inscriptions ON cours.id = inscriptions.cours_id 
        WHERE cours.enseignant_id = :enseignant_id 
        GROUP BY cours.id, cours.titre 
        ORDER BY nombre_inscriptions DESC 
        LIMIT :limit";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':enseignant_id', $enseignant_id, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

        // pour voir les inscription par mois
    public function getInscriptionsParMois($enseignant_id, $limit = 12) {
        $query = "SELECT DATE_FORMAT(inscriptions.date_inscription, '%Y-%m') as mois, COUNT(*) as nombre_inscriptions FROM inscriptions
                  INNER JOIN cours ON inscriptions.cours_id = cours.id
                  WHERE cours.enseignant_id = :enseignant_id
                  GROUP BY DATE_FORMAT(inscriptions.date_inscription, '%Y-%m')
                  ORDER BY mois DESC
                  LIMIT :limit";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':enseignant_id', $enseignant_id, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getStatistiquesParCategorie($enseignant_id) {
        $query = "SELECT categories.nom as categorie, COUNT(cours.id) as nombre_cours, COUNT(inscriptions.id) as nombre_inscriptions FROM categories
                    LEFT JOIN cours ON categories.id = cours.categorie_id AND cours.enseignant_id = :enseignant_id
                    LEFT JOIN inscriptions ON cours.id = inscriptions.cours_id
                    WHERE cours.id IS NOT NULL
                    GROUP BY categories.id, categories.nom
                    ORDER BY nombre_cours DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['enseignant_id' => $enseignant_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


}
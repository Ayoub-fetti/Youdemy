<?php
require_once 'User.php';

class Admin extends User {

            // fonction pour recuperer tout les utilisateurs
            public function getAllUsers() {
                $stmt = $this->pdo->prepare("SELECT * FROM utilisateurs");
                $stmt->execute();
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            }

            // fonction pour recuperer tout les cours

            public function getAllCours(){
                $stmt = $this->pdo->prepare("SELECT cours.*, utilisateurs.nom as nom_enseignant, GROUP_CONCAT(tags.nom) as tags
                    FROM cours
                    INNER JOIN utilisateurs ON cours.enseignant_id = utilisateurs.id
                    LEFT JOIN cours_tags ON cours.id = cours_tags.cours_id
                    LEFT JOIN tags ON cours_tags.tag_id = tags.id
                    GROUP BY cours.id
                ");
                $stmt->execute();
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            }

            // fonction pour supprimer un cours 
            public function deleteCourse($id){
                try {
                    $this->pdo->beginTransaction();
                    
                    // First delete related records in cours_tags
                    $stmt = $this->pdo->prepare("DELETE FROM cours_tags WHERE cours_id = :id");
                    $stmt->execute([':id' => $id]);
                    
                    // Then delete the course
                    $stmt = $this->pdo->prepare("DELETE FROM cours WHERE id = :id");
                    $stmt->execute([':id' => $id]);
                    
                    $this->pdo->commit();
                    return true;
                } catch (PDOException $e) {
                    $this->pdo->rollBack();
                    error_log("Error deleting course: " . $e->getMessage());
                    return false;
                }
            }


            
    // fonction pour calculer tout les Etudiants
    public function totalEtudiant() {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM utilisateurs WHERE role = 'etudiant'");
        $stmt->execute();
        return $stmt->fetchColumn();
    }


    // fonction pour calculer tout les enseignants
    public function totalEnseignant(){
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM utilisateurs WHERE role = 'enseignant'");
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    // fonctions  pour calculer le total des cours
    public function totalCours(){
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM cours");
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    // obtenir la repartition des cours par categorie
    public function getCoursParCategorie() {
        $stmt = $this->pdo->prepare("SELECT categories.nom as categorie, COUNT(cours.id) as count  FROM cours  
                                    JOIN categories ON cours.categorie_id = categories.id
                                    GROUP BY categories.id, categories.nom ORDER BY count DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // le cours le plus populaire
    public function getCoursLePlusPopulaire() {
        $stmt = $this->pdo->prepare("SELECT cours.titre, COUNT(inscriptions.etudiant_id) as nb_etudiants FROM cours  
                                    LEFT JOIN inscriptions ON cours.id = inscriptions.cours_id
                                    GROUP BY cours.id, cours.titre ORDER BY nb_etudiants DESC LIMIT 1");
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // obtenir les top enseignants
    public function getTopEnseignants($limit = 3) {
        $stmt = $this->pdo->prepare("SELECT utilisateurs.nom, COUNT(cOURS.id) as nb_cours FROM utilisateurs  
                                    INNER JOIN cours ON utilisateurs.id = cours.enseignant_id
                                    WHERE utilisateurs.role = 'enseignant' GROUP BY utilisateurs.id, utilisateurs.nom ORDER BY nb_cours DESC LIMIT :limit");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // fonction pour supprimer un utilisateur
    public function deleteUser($id) {
         $stmt = $this->pdo->prepare("DELETE FROM utilisateurs WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetchColumn();
    }


    // fonction pour changer le statut d'un utilisateur (suspendre)
    public function toggleStatus($userId) {
        // Recuperer le statut actuel
        $stmt = $this->pdo->prepare("SELECT status FROM utilisateurs WHERE id = ?");
        $stmt->execute([$userId]);
        $currentStatus = $stmt->fetchColumn();

        // Determiner le nouveau statut
        $newStatus = ($currentStatus === 'actif') ? 'inactif' : 'actif';

        // Mettre e jour le statut
        $stmt = $this->pdo->prepare("UPDATE utilisateurs SET status = ? WHERE id = ?");
        $stmt->execute([$newStatus, $userId]);

        return $newStatus;
    }

    // pour ajouter  une categories 
    public function addCategory($nom) {
        try {
            $stmt = $this->pdo->prepare("INSERT INTO categories (nom) VALUES (:nom)");
            return $stmt->execute(['nom' => $nom,]);
        } catch (PDOException $e) {
            return false;
        }
    }



    // pour supprimer une categorie
    public function deleteCategory($categoryId) {
        try {
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM cours WHERE categorie_id = :id"); // rechercher  s'il y a des cours dans cette categorie
            $stmt->execute(['id' => $categoryId]);
            $count = $stmt->fetchColumn();

            if ($count > 0) {
                return false; // ne pas supprimer si des cours dans cette categorie
            }

            $stmt = $this->pdo->prepare("DELETE FROM categories WHERE id = :id");
            return $stmt->execute(['id' => $categoryId]);
        } catch (PDOException $e) {
            return false;
        }
    }

    // pour modifier une categories 
    public function modifierCategorie ($categoryId ,$nom) {
        try{
            $stmt = $this->pdo->prepare("UPDATE categories SET nom = :nom WHERE id = :id");
            return $stmt->execute(['nom' => $nom, 'id' => $categoryId]);
        } catch (PDOException $e) {
            return false;
        }
    }
    // pour recuperer tout les categories
    public function getAllCategories() {
        $stmt = $this->pdo->prepare("SELECT * FROM categories");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Fonction pour mettre à jour les tags d'un cours
    public function updateCourseTags($cours_id, $tags) {
        try {
            $this->pdo->beginTransaction();
            
            // Supprimer les tags existants
            $stmt = $this->pdo->prepare("DELETE FROM cours_tags WHERE cours_id = :cours_id");
            $stmt->execute([':cours_id' => $cours_id]);
            
            // Ajouter les nouveaux tags
            if (!empty($tags)) {
                $stmt = $this->pdo->prepare("INSERT INTO cours_tags (cours_id, tag_id) VALUES (:cours_id, :tag_id)");
                foreach ($tags as $tag_id) {
                    $stmt->execute([
                        ':cours_id' => $cours_id,
                        ':tag_id' => $tag_id
                    ]);
                }
            }
            
            $this->pdo->commit();
            return true;
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            error_log("Error updating course tags: " . $e->getMessage());
            return false;
        }
    }
    
    // Fonction pour recuperer tous les tags disponibles
    public function getAllTags() {
        $stmt = $this->pdo->prepare("SELECT * FROM tags ORDER BY nom");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Fonction pour supprimer un tag
    public function deleteTag($tag_id) {
        try {
            $this->pdo->beginTransaction();
            
            // D'abord supprimer les associations dans cours_tags
            $stmt = $this->pdo->prepare("DELETE FROM cours_tags WHERE tag_id = :tag_id");
            $stmt->execute([':tag_id' => $tag_id]);
            
            // Ensuite supprimer le tag
            $stmt = $this->pdo->prepare("DELETE FROM tags WHERE id = :tag_id");
            $stmt->execute([':tag_id' => $tag_id]);
            
            $this->pdo->commit();
            return true;
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            error_log("Error deleting tag: " . $e->getMessage());
            return false;
        }
    }

    // Fonction pour ajouter un nouveau tag
    public function addTag($nom) {
        try {
            $stmt = $this->pdo->prepare("INSERT INTO tags (nom) VALUES (:nom)");
            return $stmt->execute(['nom' => $nom]);
        } catch (PDOException $e) {
            error_log("Error adding tag: " . $e->getMessage());
            return false;
        }
    }
}
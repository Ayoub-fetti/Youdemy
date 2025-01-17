<?php
require_once 'User.php';

class Admin extends User {

            // fonction pour recuperer tout les utilisateurs
            public function getAllUsers() {
                $stmt = $this->pdo->prepare("SELECT * FROM utilisateurs");
                $stmt->execute();
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
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
        $stmt = $this->pdo->prepare("SELECT cat.nom as categorie, COUNT(c.id) as count  FROM cours c  JOIN categories cat ON c.categorie_id = cat.id
            GROUP BY cat.id, cat.nom ORDER BY count DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // le cours le plus populaire
    public function getCoursLePlusPopulaire() {
        $stmt = $this->pdo->prepare("SELECT c.titre, COUNT(i.etudiant_id) as nb_etudiants FROM cours c LEFT JOIN inscriptions i ON c.id = i.cours_id
            GROUP BY c.id, c.titre ORDER BY nb_etudiants DESC LIMIT 1");
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // obtenir les top enseignants
    public function getTopEnseignants($limit = 3) {
        $stmt = $this->pdo->prepare("SELECT u.nom, COUNT(c.id) as nb_cours FROM utilisateurs u JOIN cours c ON u.id = c.enseignant_id
            WHERE u.role = 'enseignant' GROUP BY u.id, u.nom ORDER BY nb_cours DESC LIMIT :limit");
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

}
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
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM COURS");
        $stmt->execute();
        return $stmt->fetchColumn();
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

}
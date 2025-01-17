<?php

abstract class CoursSpecifique {
    protected $titre;
    protected $description;
    protected $categorie_id;
    protected $enseignant_id;
    
    public function __construct($titre, $description, $categorie_id, $enseignant_id) {
        $this->titre = $titre;
        $this->description = $description;
        $this->categorie_id = $categorie_id;
        $this->enseignant_id = $enseignant_id;
    }
    
    // Getters
    public function getTitre() {
        return $this->titre;
    }
    
    public function getDescription() {
        return $this->description;
    }
    
    public function getCategorieId() {
        return $this->categorie_id;
    }
    
    public function getEnseignantId() {
        return $this->enseignant_id;
    }
    
    abstract public function getType();
    abstract public function getContenu();

    public function save($pdo) {
        $query = "INSERT INTO cours (titre, description, contenu, categorie_id, enseignant_id) 
                VALUES (:titre, :description, :contenu, :categorie_id, :enseignant_id)";
        
        $stmt = $pdo->prepare($query);
        return $stmt->execute([
            ':titre' => $this->getTitre(),
            ':description' => $this->getDescription(),
            ':contenu' => $this->getContenu(),
            ':categorie_id' => $this->getCategorieId(),
            ':enseignant_id' => $this->getEnseignantId()
        ]);
    }
}

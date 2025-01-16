<?php
require_once 'CoursSpecifique.php';

class CoursPDF extends CoursSpecifique {
    private $chemin_fichier;
    
    public function __construct($titre, $description, $categorie_id, $enseignant_id, $chemin_fichier) {
        parent::__construct($titre, $description, $categorie_id, $enseignant_id);
        $this->chemin_fichier = $chemin_fichier;
    }
    
    public function getType() {
        return 'pdf';
    }
    
    public function getContenu() {
        return $this->chemin_fichier;
    }
}

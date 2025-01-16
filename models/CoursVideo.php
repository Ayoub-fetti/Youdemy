<?php
require_once 'CoursSpecifique.php';

class CoursVideo extends CoursSpecifique {
    private $url_cdn;
    
    public function __construct($titre, $description, $categorie_id, $enseignant_id, $url_cdn) {
        parent::__construct($titre, $description, $categorie_id, $enseignant_id);
        $this->url_cdn = $url_cdn;
    }
    
    public function getType() {
        return 'video';
    }
    
    public function getContenu() {
        return $this->url_cdn;
    }
}

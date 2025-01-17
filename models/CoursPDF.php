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

    public static function uploadFile($file) {
        if (!isset($file) || $file["error"] !== UPLOAD_ERR_OK) {
            throw new Exception("Erreur : Veuillez sélectionner un fichier PDF valide.");
        }

        $target_dir = __DIR__ . "/../uploads/pdfs/";
        if (!file_exists($target_dir)) {
            if (!mkdir($target_dir, 0777, true)) {
                throw new Exception("Erreur système : Impossible de créer le dossier de destination.");
            }
        }

        $target_file = $target_dir . basename($file["name"]);
        if (!move_uploaded_file($file["tmp_name"], $target_file)) {
            throw new Exception("Erreur lors du téléchargement du fichier.");
        }

        return $target_file;
    }
}

<?php
class Boisson {
    private $id;
    private $nom;
    private $marque;
    private $prix;
    private $quantite;
    private $image;

    public function __construct($id = null, $nom = null, $marque = null, $prix = null, $quantite = null, $image = null) {
        $this->id = $id;
        $this->nom = $nom;
        $this->marque = $marque;
        $this->prix = $prix;
        $this->quantite = $quantite;
        $this->image = $image;
    }

    // Getters
    public function getId() { return $this->id; }
    public function getNom() { return $this->nom; }
    public function getMarque() { return $this->marque; }
    public function getPrix() { return $this->prix; }
    public function getQuantite() { return $this->quantite; }
    public function getImage() { return $this->image; }

    // Setters
    public function setId($id) { $this->id = $id; }
    public function setNom($nom) { $this->nom = $nom; }
    public function setMarque($marque) { $this->marque = $marque; }
    public function setPrix($prix) { $this->prix = $prix; }
    public function setQuantite($quantite) { $this->quantite = $quantite; }
    public function setImage($image) { $this->image = $image; }
}
?>

<?php
class BoissonDAO {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAll() {
        try {
            $sql = "SELECT id, nom, marque, prix, quantite, image FROM boissons";
            $stmt = $this->db->query($sql);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $boissons = [];
            foreach ($rows as $row) {
                $boissons[] = new Boisson(
                    $row['id'] ?? null,
                    $row['nom'] ?? null,
                    $row['marque'] ?? null,
                    $row['prix'] ?? null,
                    $row['quantite'] ?? null,
                    $row['image'] ?? null
                );
            }
            return $boissons;

        } catch (PDOException $e) {
            error_log('Erreur dans getAll() : ' . $e->getMessage());
            return [];
        }
    }
    public function getByMarque($marque) {
        $stmt = $this->db->prepare("SELECT * FROM boissons WHERE marque = :marque ORDER BY nom ASC");
        $stmt->execute([':marque' => $marque]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        $boissons = [];
        foreach ($rows as $row) {
            $boissons[] = new Boisson(
                $row['id'],
                $row['nom'],
                $row['marque'],
                $row['prix'],
                $row['quantite'],
                $row['image']
            );
        }
        return $boissons;
    }
    
}
?>

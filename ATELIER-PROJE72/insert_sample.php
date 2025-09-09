<?php
include 'config.php';

try {
    // Insert sample room types
    $pdo->exec("INSERT INTO types_chambres (code, libelle, capacite, prix_base, description) VALUES
    ('STD', 'Standard', 2, 25.00, 'Chambre standard confortable avec lit double, salle de bain privée, Wi-fi gratuit et climatisation.'),
    ('VIP1', 'VIP 1', 2, 45.00, 'Chambre VIP niveau 1 avec équipements premium, vue sur jardin, service de chambre 24h.'),
    ('VIP2', 'VIP 2', 2, 65.00, 'Chambre VIP niveau 2 avec salon privé, mini-bar, balcon avec vue panoramique.'),
    ('VIP3', 'VIP 3', 2, 85.00, 'Suite VIP niveau 3 avec jacuzzi privé, service majordome, équipements high-tech.'),
    ('LUX1', 'Lux avec un seul lit', 1, 55.00, 'Chambre de luxe avec un grand lit king-size, décoration élégante, salle de bain en marbre.'),
    ('LUX2', 'Lux avec 2 lit', 2, 75.00, 'Chambre de luxe avec deux lits doubles, espace salon, terrasse privée avec vue.')");

    // Insert sample rooms
    $pdo->exec("INSERT INTO chambres (numero, etage, type_chambre_id, statut) VALUES
    ('101', 1, 1, 'DISPONIBLE'),
    ('102', 1, 1, 'DISPONIBLE'),
    ('103', 1, 1, 'OCCUPEE'),
    ('201', 2, 2, 'DISPONIBLE'),
    ('202', 2, 2, 'MAINTENANCE'),
    ('203', 2, 3, 'DISPONIBLE'),
    ('301', 3, 4, 'DISPONIBLE'),
    ('302', 3, 5, 'DISPONIBLE'),
    ('303', 3, 6, 'NETTOYAGE'),
    ('401', 4, 6, 'DISPONIBLE')");

    echo "Données d'exemple insérées avec succès.";
} catch (PDOException $e) {
    echo "Erreur: " . $e->getMessage();
}
?>

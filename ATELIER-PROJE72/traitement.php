<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // Validation des données
        $nom = trim($_POST['nom'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $telephone = trim($_POST['telephone'] ?? '');
        $type_chambre = $_POST['type_chambre'] ?? '';
        $date_arrivee = $_POST['date_arrivee'] ?? '';
        $date_depart = $_POST['date_depart'] ?? '';
        $message = trim($_POST['message'] ?? '');

        if (empty($nom) || empty($email) || empty($telephone) || empty($type_chambre) || empty($date_arrivee) || empty($date_depart)) {
            throw new Exception("Tous les champs obligatoires doivent être remplis.");
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Adresse email invalide.");
        }

        if (strtotime($date_arrivee) >= strtotime($date_depart)) {
            throw new Exception("La date d'arrivée doit être antérieure à la date de départ.");
        }

        // Insérer client
        $stmt = $pdo->prepare("INSERT INTO clients (prenom, nom, email, telephone) VALUES (?, ?, ?, ?)");
        $noms = explode(' ', $nom, 2);
        $prenom = $noms[0] ?? '';
        $nom_famille = $noms[1] ?? '';
        $stmt->execute([$prenom, $nom_famille, $email, $telephone]);
        $client_id = $pdo->lastInsertId();

        // Insérer réservation
        $stmt = $pdo->prepare("INSERT INTO reservations (client_id, date_arrivee, date_depart, adultes, notes) VALUES (?, ?, ?, 1, ?)");
        $stmt->execute([$client_id, $date_arrivee, $date_depart, $message]);

        // Redirection vers page de confirmation
        header("Location: reservation.php?success=1");
        exit();
    } catch (Exception $e) {
        echo "Erreur: " . htmlspecialchars($e->getMessage());
    }
} else {
    echo "Méthode non autorisée.";
}
?>

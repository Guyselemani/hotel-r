<?php include 'header.php'; ?>
<?php include 'config.php'; ?>

<main>
    <div class="container">
        <h2>Formulaire de Réservation</h2>
        <form action="traitement.php" method="POST">
            <label for="nom">Nom complet: </label>
            <input type="text" id="nom" name="nom" required>

            <label for="email">Adresse email: </label>
            <input type="email" id="email" name="email" required>

            <label for="telephone">Téléphone</label>
            <input type="tel" id="telephone" name="telephone" required>

            <label for="type_chambre">Type de chambre: </label>
            <select id="type_chambre" name="type_chambre" required>
                <option value="">--Sélectionner--</option>
                <?php
                $stmt = $pdo->query("SELECT type_chambre_id, libelle FROM types_chambres");
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<option value='" . $row['type_chambre_id'] . "'>" . htmlspecialchars($row['libelle']) . "</option>";
                }
                ?>
            </select>

            <label for="date_arrivee">Date d'arrivée: </label>
            <input type="date" id="date_arrivee" name="date_arrivee" required>
            <label for="date_depart">Date de départ: </label>
            <input type="date" id="date_depart" name="date_depart" required>
            <label for="message">Message ou demande spéciale: </label>
            <textarea id="message" name="message" rows="4"></textarea>
            <button type="submit" name="reserver" value="reserver" class="btn-primary">Réserver</button>
        </form>
    </div>
</main>

<?php include 'footer.php'; ?>

<?php include 'header.php'; ?>
<?php include 'config.php'; ?>

<main>
    <div class="container">
        <h2>Nos Chambres</h2>
        <p>Découvrez nos différentes catégories de chambres. Les chambres marquées comme disponibles peuvent être réservées immédiatement.</p>

        <div class="room-list">
            <?php
            $stmt = $pdo->query("SELECT tc.libelle, tc.description, tc.prix_base, c.numero, c.statut, c.etage FROM types_chambres tc JOIN chambres c ON tc.type_chambre_id = c.type_chambre_id ORDER BY tc.prix_base, c.numero");
            $current_type = '';
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                if ($current_type !== $row['libelle']) {
                    if ($current_type !== '') {
                        echo "</div>"; // Close previous type section
                    }
                    $current_type = $row['libelle'];
                    echo "<div class='room-type-section'>";
                    echo "<h3>" . htmlspecialchars($row['libelle']) . "</h3>";
                    echo "<p class='type-description'>" . htmlspecialchars($row['description']) . "</p>";
                    echo "<div class='rooms-grid'>";
                }

                $status_class = 'status-' . strtolower(str_replace('_', '-', $row['statut']));
                $status_text = $row['statut'] === 'DISPONIBLE' ? 'Disponible' : ($row['statut'] === 'OCCUPEE' ? 'Occupée' : $row['statut']);

                echo "<div class='card room-card'>";
                echo "<div class='card-content'>";
                echo "<h4>Chambre " . htmlspecialchars($row['numero']) . "</h4>";
                echo "<span class='room-status " . $status_class . "'>" . htmlspecialchars($status_text) . "</span>";
                echo "<p><strong>Étage:</strong> " . htmlspecialchars($row['etage']) . "</p>";
                echo "<p><strong>Prix:</strong> " . htmlspecialchars($row['prix_base']) . "€/nuit</p>";
                if ($row['statut'] === 'DISPONIBLE') {
                    echo "<a href='reservation.php?chambre=" . htmlspecialchars($row['numero']) . "' class='btn-primary'>Réserver</a>";
                }
                echo "</div>";
                echo "</div>";
            }
            if ($current_type !== '') {
                echo "</div></div>"; // Close last type section
            }
            ?>
        </div>

        <div class="legend">
            <h3>Légende des Statuts</h3>
            <div class="status-legend">
                <span class="status-item status-disponible">Disponible</span>
                <span class="status-item status-occupee">Occupée</span>
                <span class="status-item status-maintenance">Maintenance</span>
                <span class="status-item status-nettoyage">Nettoyage</span>
                <span class="status-item status-hors_service">Hors Service</span>
            </div>
        </div>
    </div>
</main>



<?php include 'footer.php'; ?>

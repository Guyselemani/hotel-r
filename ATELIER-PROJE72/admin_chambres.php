<?php
include 'header.php';
include 'config.php';

// Handle form submissions
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['add_room'])) {
            // Add new room
            $stmt = $pdo->prepare("INSERT INTO chambres (numero, etage, type_chambre_id, statut) VALUES (?, ?, ?, ?)");
            $stmt->execute([
                $_POST['numero'],
                $_POST['etage'],
                $_POST['type_chambre_id'],
                $_POST['statut']
            ]);
            $message = "Chambre ajoutée avec succès.";
        } elseif (isset($_POST['update_room'])) {
            // Update room
            $stmt = $pdo->prepare("UPDATE chambres SET numero = ?, etage = ?, type_chambre_id = ?, statut = ? WHERE chambre_id = ?");
            $stmt->execute([
                $_POST['numero'],
                $_POST['etage'],
                $_POST['type_chambre_id'],
                $_POST['statut'],
                $_POST['chambre_id']
            ]);
            $message = "Chambre mise à jour avec succès.";
        } elseif (isset($_POST['delete_room'])) {
            // Delete room
            $stmt = $pdo->prepare("DELETE FROM chambres WHERE chambre_id = ?");
            $stmt->execute([$_POST['chambre_id']]);
            $message = "Chambre supprimée avec succès.";
        } elseif (isset($_POST['change_status'])) {
            // Change room status
            $stmt = $pdo->prepare("UPDATE chambres SET statut = ? WHERE chambre_id = ?");
            $stmt->execute([$_POST['new_statut'], $_POST['chambre_id']]);
            $message = "Statut de la chambre mis à jour.";
        }
    } catch (PDOException $e) {
        $message = "Erreur: " . $e->getMessage();
    }
}

// Get all rooms with type information
$stmt = $pdo->query("
    SELECT c.*, tc.libelle as type_libelle, tc.prix_base
    FROM chambres c
    JOIN types_chambres tc ON c.type_chambre_id = tc.type_chambre_id
    ORDER BY c.numero
");
$chambres = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get room types for dropdown
$stmt = $pdo->query("SELECT * FROM types_chambres ORDER BY libelle");
$types_chambres = $stmt->fetchAll(PDO::FETCH_ASSOC);



// Status options
$statuts = ['DISPONIBLE', 'OCCUPEE', 'MAINTENANCE', 'NETTOYAGE', 'HORS_SERVICE'];
?>

<main>
    <div class="container">
        <h2>Gestion des Chambres</h2>

        <?php if ($message): ?>
            <div class="message <?php echo strpos($message, 'Erreur') === 0 ? 'error' : 'success'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <!-- Add New Room Form -->
        <div class="admin-section">
            <h3>Ajouter une Nouvelle Chambre</h3>
            <form method="POST" class="admin-form">
                <div class="form-row">
                    <div class="form-group">
                        <label for="numero">Numéro de Chambre:</label>
                        <input type="text" id="numero" name="numero" required>
                    </div>
                    <div class="form-group">
                        <label for="etage">Étage:</label>
                        <input type="number" id="etage" name="etage" required>
                    </div>
                    <div class="form-group">
                        <label for="type_chambre_id">Type de Chambre:</label>
                        <select id="type_chambre_id" name="type_chambre_id" required>
                            <option value="">Sélectionner un type</option>
                            <?php foreach ($types_chambres as $type): ?>
                                <option value="<?php echo $type['type_chambre_id']; ?>">
                                    <?php echo htmlspecialchars($type['libelle']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="statut">Statut:</label>
                        <select id="statut" name="statut" required>
                            <?php foreach ($statuts as $statut): ?>
                                <option value="<?php echo $statut; ?>"><?php echo $statut; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <button type="submit" name="add_room" class="btn-primary">Ajouter la Chambre</button>
            </form>
        </div>

        <!-- Rooms List -->
        <div class="admin-section">
            <h3>Liste des Chambres</h3>
            <div class="rooms-table-container">
                <table class="rooms-table">
                    <thead>
                        <tr>
                            <th>Numéro</th>
                            <th>Étage</th>
                            <th>Type</th>
                            <th>Prix Base</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($chambres as $chambre): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($chambre['numero']); ?></td>
                                <td><?php echo htmlspecialchars($chambre['etage']); ?></td>
                                <td><?php echo htmlspecialchars($chambre['type_libelle']); ?></td>
                                <td><?php echo htmlspecialchars($chambre['prix_base']); ?>€</td>
                                <td>
                                    <span class="status status-<?php echo strtolower($chambre['statut']); ?>">
                                        <?php echo htmlspecialchars($chambre['statut']); ?>
                                    </span>
                                </td>
                                <td class="actions">
                                    <!-- Quick Status Change -->
                                    <form method="POST" class="inline-form">
                                        <input type="hidden" name="chambre_id" value="<?php echo $chambre['chambre_id']; ?>">
                                        <select name="new_statut" onchange="this.form.submit()">
                                            <option value="">Changer statut</option>
                                            <?php foreach ($statuts as $statut): ?>
                                                <option value="<?php echo $statut; ?>" <?php echo $statut === $chambre['statut'] ? 'disabled' : ''; ?>>
                                                    <?php echo $statut; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <input type="hidden" name="change_status" value="1">
                                    </form>

                                    <!-- Edit Button -->
                                    <button onclick="editRoom(<?php echo $chambre['chambre_id']; ?>, '<?php echo htmlspecialchars($chambre['numero']); ?>', <?php echo $chambre['etage']; ?>, <?php echo $chambre['type_chambre_id']; ?>, '<?php echo $chambre['statut']; ?>')" class="btn-edit">Modifier</button>

                                    <!-- Delete Button -->
                                    <form method="POST" class="inline-form" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette chambre ?')">
                                        <input type="hidden" name="chambre_id" value="<?php echo $chambre['chambre_id']; ?>">
                                        <button type="submit" name="delete_room" class="btn-delete">Supprimer</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Edit Room Modal/Form -->
        <div id="editModal" class="modal" style="display: none;">
            <div class="modal-content">
                <span class="close" onclick="closeModal()">&times;</span>
                <h3>Modifier la Chambre</h3>
                <form method="POST" class="admin-form">
                    <input type="hidden" id="edit_chambre_id" name="chambre_id">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="edit_numero">Numéro de Chambre:</label>
                            <input type="text" id="edit_numero" name="numero" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_etage">Étage:</label>
                            <input type="number" id="edit_etage" name="etage" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_type_chambre_id">Type de Chambre:</label>
                            <select id="edit_type_chambre_id" name="type_chambre_id" required>
                                <?php foreach ($types_chambres as $type): ?>
                                    <option value="<?php echo $type['type_chambre_id']; ?>">
                                        <?php echo htmlspecialchars($type['libelle']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="edit_statut">Statut:</label>
                            <select id="edit_statut" name="statut" required>
                                <?php foreach ($statuts as $statut): ?>
                                    <option value="<?php echo $statut; ?>"><?php echo $statut; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <button type="submit" name="update_room" class="btn-primary">Mettre à Jour</button>
                </form>
            </div>
        </div>
    </div>
</main>

<script>
function editRoom(id, numero, etage, typeId, statut) {
    document.getElementById('edit_chambre_id').value = id;
    document.getElementById('edit_numero').value = numero;
    document.getElementById('edit_etage').value = etage;
    document.getElementById('edit_type_chambre_id').value = typeId;
    document.getElementById('edit_statut').value = statut;
    document.getElementById('editModal').style.display = 'block';
}

function closeModal() {
    document.getElementById('editModal').style.display = 'none';
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('editModal');
    if (event.target === modal) {
        modal.style.display = 'none';
    }
}
</script>

<style>
.admin-section {
    background: #f8f9fa;
    padding: 20px;
    margin: 20px 0;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.admin-form {
    background: white;
    padding: 20px;
    border-radius: 8px;
    margin-top: 10px;
}

.form-row {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    margin-bottom: 15px;
}

.form-group {
    flex: 1;
    min-width: 200px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
}

.form-group input, .form-group select {
    width: 100%;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.rooms-table-container {
    overflow-x: auto;
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.rooms-table {
    width: 100%;
    border-collapse: collapse;
}

.rooms-table th, .rooms-table td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

.rooms-table th {
    background: #f8f9fa;
    font-weight: bold;
}

.status {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.9em;
    font-weight: bold;
}

.status-disponible { background: #d4edda; color: #155724; }
.status-occupee { background: #f8d7da; color: #721c24; }
.status-maintenance { background: #fff3cd; color: #856404; }
.status-nettoyage { background: #cce5ff; color: #004085; }
.status-hors_service { background: #e2e3e5; color: #383d41; }

.actions {
    white-space: nowrap;
}

.inline-form {
    display: inline;
    margin-right: 5px;
}

.btn-edit, .btn-delete {
    padding: 4px 8px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 0.9em;
}

.btn-edit { background: #007bff; color: white; }
.btn-delete { background: #dc3545; color: white; }

.message {
    padding: 10px;
    margin: 10px 0;
    border-radius: 4px;
}

.message.success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
.message.error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }

.modal {
    display: none;
    position: fixed;
    z-index: 1;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.4);
}

.modal-content {
    background-color: #fefefe;
    margin: 15% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 80%;
    max-width: 600px;
    border-radius: 8px;
}

.close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
}

.close:hover { color: black; }
</style>

<?php include 'footer.php'; ?>

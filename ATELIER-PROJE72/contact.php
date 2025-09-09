<?php include 'header.php'; ?>

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nom = $_POST['nom'];
    $email = $_POST['email'];
    $message = $_POST['message'];
    // Here you can send email or store in database
    echo "<p>Merci $nom, votre message a été envoyé.</p>";
}
?>

<main>
    <h2>Contactez-nous</h2>
    <p>Address: Centre ville, Goma, RDC</p>
    <P>Telephone: +243 0979972891</P>
    <p>Email: contact@hotelroyal.com</p>

    <form action="contact.php" method="POST">
        <label for="nom">Votre nom: </label>
        <input type="text" id="nom" name="nom" required>

        <label for="email">Votre email: </label>
        <input type="email" id="email" name="email" required>

        <label for="message">Message: </label>
        <textarea id="message" name="message" rows="5" required></textarea>
        <button type="submit">Envoyer</button>
    </form>
</main>

<?php include 'footer.php'; ?>

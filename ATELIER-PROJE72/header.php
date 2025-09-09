<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Bienvenue à l'Hotel Royal - Un séjour inoubliable avec luxe et confort. Réservez vos chambres et profitez de nos services haut de gamme.">
    <title>Hotel Royal</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="styles2.css">
</head>
<body>
    <header>
        <h1>Bienvenue à l'hotel Royal</h1>
        <nav>
            <ul>
                <li><a href="index.php">Accueil</a></li>
                <li><a href="services.php">Services</a></li>
                <li><a href="chambres.php">Chambres</a></li>
                <li><a href="reservation.php">Reservation</a></li>
                <li><a href="contact.php">Contact</a></li>
                <li><a href="admin_chambres.php">Admin Chambres</a></li>
                <li>
                    <button id="mode-toggle" aria-label="Toggle light/dark mode" style="cursor:pointer; background:none; border:none; color: var(--accent-color); font-weight:700; font-size:1rem; padding:0.5rem 1rem; border-radius: 20px; transition: color 0.3s ease;">
                        Mode Nuit
                    </button>
                </li>
            </ul>
        </nav>
    </header>
<script>
    (function() {
        const toggleButton = document.getElementById('mode-toggle');
        const body = document.body;

        // Load saved mode from localStorage
        const savedMode = localStorage.getItem('mode');
        if (savedMode === 'light') {
            body.classList.add('light-mode');
            toggleButton.textContent = 'Mode Éclairé';
        } else {
            toggleButton.textContent = 'Mode Nuit';
        }

        toggleButton.addEventListener('click', () => {
            if (body.classList.contains('light-mode')) {
                body.classList.remove('light-mode');
                toggleButton.textContent = 'Mode Nuit';
                localStorage.setItem('mode', 'dark');
            } else {
                body.classList.add('light-mode');
                toggleButton.textContent = 'Mode Éclairé';
                localStorage.setItem('mode', 'light');
            }
        });
    })();
</script>

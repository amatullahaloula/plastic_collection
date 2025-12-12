<?php
// views/register.php
session_start();
if (isset($_SESSION['user'])) {
    // Si déjà connecté : redirection par rôle
    $role = $_SESSION['user']['role'] ?? 'student';
    header("Location: /plastic_collection/views/dashboard_{$role}.php");
    exit;
}
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Inscription - Ashesi Plastic</title>
    
    <link rel="stylesheet" href="/plastic_collection/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        /* La couleur principale du bouton "Sign up" est #1b3d6d */
        const PRIMARY_COLOR = '#1b3d6d';
        
        /* ------------------------------------------- */
        /* FUSION ET NETTOYAGE CSS */
        /* ------------------------------------------- */
        
        body {
            margin: 0;
            padding: 0;
            font-family: "Inter", sans-serif;
            background: #f6f6f6;
            min-height: 100vh;
            display: flex; /* Utilisation de flex pour centrer le contenu */
            justify-content: center;
            align-items: center;
        }

        /* Arrière-plan (utilise l'URL Unsplash pour la cohérence avec le CSS inséré) */
        .page-background {
            background: url('../css/asset/empty_bottle_picture .png');
            position: fixed;
            inset: 0;
            /* opacity: 0.35; */
            /* filter: blur(6px); */
            z-index: -1;
        }

        /* Transparent container behavior (Wrapper autour du formulaire) */
        .transparent-wrapper {
            background: rgba(255, 255, 255, 0.45);
            backdrop-filter: blur(1px);
            border-radius: 18px;
            padding: 20px;
            margin: 20px;
            z-index: 10;
        }

        /* Conteneur principal et carte de formulaire */
        .container.center {
            max-width: 500px;
            width: 100%;
            margin: auto;
        }
        
        .card.form-container {
            background: white;
            padding: 30px;
            border-radius: 18px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }

        /* Mise en page en deux colonnes pour le nom/prénom */
        .two-cols {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 10px;
        }

        /* Styles de formulaire de base */
        label {
            display: block;
            margin-top: 10px;
            margin-bottom: 5px;
            font-weight: 600;
        }
        input, select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-sizing: border-box;
            margin-bottom: 5px;
        }
        
        /* Boutons */
        .btn {
            padding: 10px 20px;
            background: #1b3d6d; /* Couleur de base */
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.3s;
        }
        .btn:hover { background: #224c87; }

        .row.between {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
        }

        /* === FEEDBACK APPLIQUÉ ICI === */
        /* 1. Titre du formulaire (H2) */
        .card.form-container h2 {
            color: #1b3d6d; /* Couleur du bouton "Sign up" */
            font-size: 24px;
        }
        /* 2. Lien "Back to login" */
        .row.between a {
            color: #1b3d6d; /* Couleur du bouton "Sign up" */
            text-decoration: none;
            font-weight: 600;
        }
        .row.between a:hover {
             color: #224c87;
             text-decoration: underline;
        }
        /* ============================ */


        /* Messages */
        .msg {
            margin-top: 15px;
            padding: 10px;
            border-radius: 8px;
            font-weight: 600;
            text-align: center;
        }
        .error {
            background: #ffe6e6; 
            color: red; 
            border: 1px solid red;
        }
    </style>
</head>

<body>

<div class="page-background"></div>

<div class="transparent-wrapper">
    <div class="container center">
        <div class="card form-container">
            <h2> Welcome to  Aloula lastic  webpage 
               register now</h2>

            <form id="regForm" autocomplete="off">
                <div class="two-cols">
                    <div>
                        <label for="firstName">First name </label>
                        <input name="first_name" id="firstName" required>
                    </div>
                    <div>
                        <label for="lastName">Last name </label>
                        <input name="last_name" id="lastName" required>
                    </div>
                </div>

                <label for="nickname">Nickname (optional)</label>
                <input name="nickname" id="nickname" placeholder="How people will call you">

                <label for="email">Ashesi Email </label>
                <input name="email" id="email" type="email" placeholder="you@ashesi.edu.gh" required>

                <label for="roleSelect">Role </label>
                <select name="role" id="roleSelect" required>
                    <option value="">Select role </option>
                    <option value="student">Student</option>
                    <option value="cleaner">Cleaner</option>
                    <option value="admin">Admin</option>
                </select>

                <div id="degreeBlock" style="display:none;">
                    <label for="degreeSelect">Class / Year </label>
                    <select name="degree" id="degreeSelect">
                        <option value=""> Select year </option>
                        <option value="Year 1">Year 1</option>
                        <option value="Year 2">Year 2</option>
                        <option value="Year 3">Year 3</option>
                        <option value="Year 4">Year 4</option>
                    </select>
                </div>

                <label for="password">Password </label>
                <input name="password" type="password" id="password" required>

                <label for="password_confirm">Confirm Password </label>
                <input name="password_confirm" type="password" id="password_confirm" required>

                <label for="phone">Phone (optional)</label>
                <input name="phone" id="phone" type="tel" placeholder="+233...">

                <div class="row between">
                    <button type="submit" class="btn" id="submitBtn">Sign up</button>
                    <a href="/plastic_collection/views/login.php">Back to login</a>
                </div>

                <div id="msg" class="msg"></div>
            </form>
        </div>
    </div>
</div> 
<script>
// show/hide degree when role changes
const roleSelect = document.getElementById('roleSelect');
const degreeBlock = document.getElementById('degreeBlock');
const degreeSelect = document.getElementById('degreeSelect');

// Fonction pour mettre à jour la visibilité et l'obligation du champ 'degree'
const updateDegreeVisibility = () => {
    if (roleSelect.value === 'student') {
        degreeBlock.style.display = 'block';
        degreeSelect.required = true;
    } else {
        degreeBlock.style.display = 'none';
        degreeSelect.required = false;
        degreeSelect.value = ""; // Réinitialiser la valeur si le rôle change
    }
};

roleSelect.addEventListener('change', updateDegreeVisibility);

// Exécuter au chargement initial (en cas de rechargement avec valeur pré-sélectionnée)
document.addEventListener('DOMContentLoaded', updateDegreeVisibility);


// handle form submit via fetch JSON
document.getElementById('regForm').addEventListener('submit', async function(e){
    e.preventDefault();
    
    const msgEl = document.getElementById('msg');
    const submitBtn = document.getElementById('submitBtn');
    
    msgEl.textContent = 'Processing...';
    msgEl.classList.remove('error');
    submitBtn.disabled = true; // Désactive le bouton

    const form = new FormData(this);
    const p1 = form.get('password');
    const p2 = form.get('password_confirm');
    
    // 1. Vérification des mots de passe
    if (p1 !== p2) {
        msgEl.textContent = 'Passwords do not match.';
        msgEl.classList.add('error');
        submitBtn.disabled = false;
        return;
    }

    // 2. Vérification de l'e-mail Ashesi
    const email = form.get('email') || '';
    if (!/@ashesi\.edu\.gh$/i.test(email)) {
        msgEl.textContent = 'Please use a valid Ashesi email (example@ashesi.edu.gh).';
        msgEl.classList.add('error');
        submitBtn.disabled = false;
        return;
    }

    // 3. Vérification de l'année pour les étudiants (redondant car le champ est 'required' mais bonne garde-fou)
    if (form.get('role') === 'student' && !form.get('degree')) {
        msgEl.textContent = 'Please select your class / year.';
        msgEl.classList.add('error');
        submitBtn.disabled = false;
        return;
    }

    try {
        const res = await fetch('../api/register.php', {
            method: 'POST',
            body: form
        });
        
        // Gérer les erreurs HTTP
        if (!res.ok) {
            throw new Error(`HTTP error! status: ${res.status}`);
        }
        
        const j = await res.json();
        
        if (j.success) {
            msgEl.textContent = 'Compte créé avec succès. Redirection vers la page de connexion...';
            // Redirection vers la page de connexion
            setTimeout(()=> window.location.href = '/plastic_collection/views/login.php', 900);
        } else {
            msgEl.textContent = j.error || 'Erreur serveur. Échec de la création du compte.';
            msgEl.classList.add('error');
            submitBtn.disabled = false;
        }
    } catch (err) {
        msgEl.textContent = `Network error: ${err.message}.`;
        msgEl.classList.add('error');
        submitBtn.disabled = false;
    }
});
</script>
</body>
</html>
<?php
// views/login.php
session_start();

// Si déjà connecté → rediriger par rôle
if (isset($_SESSION['user'])) {
    $role = $_SESSION['user']['role'];
    if ($role === 'student') header('Location: /plastic_collection/views/dashboard_student.php');
    else if ($role === 'cleaner') header('Location: /plastic_collection/views/dashboard_cleaner.php');
    else if ($role === 'admin') header('Location: /plastic_collection/views/dashboard_admin.php');
    // Assurer l'arrêt après la redirection
    exit; 
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Login - Plastic Collection</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link rel="stylesheet" href="/plastic_collection/css/auth.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">

<style>
    /* ------------------------------------------- */
    /* FUSION ET NETTOYAGE CSS */
    /* ------------------------------------------- */
    
    body {
        margin: 0;
        padding: 0;
        font-family: "Inter", sans-serif;
        /*background: #f6f6f6; /* On remet une couleur de fond simple ou 'transparent' */
        
        /* S'assurer que le body prend au moins toute la hauteur de la vue */
        min-height: 100vh; 
        display: flex;
        justify-content: center; /* Centrer horizontalement le contenu */
        align-items: center; /* Centrer verticalement le contenu */
    }

    /* Arrière-plan (utilise l'URL Unsplash pour la cohérence avec le CSS inséré) */
    .page-background {
        /* *** CORRECTION APPLIQUÉE ICI *** */
        /* Utilisation du chemin direct puisque l'image est dans le dossier CSS */
        background-image: url("../css/asset/empty_bottle_picture .png");
        /* ******************************* */
        
        position: fixed;
        inset: 0;
        /* opacity: 0.35; */
        /* filter: blur(6px); */
        z-index: -1;
    }

    /* Transparent container behavior (Pour envelopper l'auth-card si nécessaire) */
    .transparent-wrapper {
        background: rgba(255, 255, 255, 0.45);
        backdrop-filter: blur(12px);
        border-radius: 18px;
        padding: 20px;
        margin: 20px;
    }
    
    /* Centrage de la carte si le body est flex */
    .auth-card {
        max-width: 400px;
        width: 90%;
        margin: 0 auto; /* Centrage à l'intérieur du wrapper */
        z-index: 10;
        position: relative;
    }
    
    /* Styles pour les messages */
    .msg { 
        margin-top: 15px; 
        padding: 10px;
        border-radius: 8px;
        font-weight: 600;
        text-align: center;
    }
    .success { 
        background: #e6ffe6; 
        color: green; 
        border: 1px solid green;
    }
    .error { 
        background: #ffe6e6; 
        color: red; 
        border: 1px solid red;
    }
    
    /* Amélioration des éléments de formulaire si auth.css est simple */
    label {
        display: block;
        margin-top: 10px;
        margin-bottom: 5px;
        font-weight: 600;
        color: #333;
    }
    input[type="email"], input[type="password"] {
        width: 100%;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 8px;
        box-sizing: border-box;
    }
    .btn {
        width: 100%;
        padding: 12px;
        margin-top: 20px;
        background: #1b3d6d;
        color: white;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-size: 16px;
        transition: background 0.3s;
    }
    .btn:hover {
        background: #224c87; /* Correction: le hover doit changer de couleur */
    }
</style>
</head>

<body>

<div class="page-background"></div>

<div class="transparent-wrapper">
    
    <div class="auth-card fade-in">
        <h2 class="title">Ashesi Plastic<br>Collection Portal</h2>

        <form id="loginForm">

            <label for="email">Email (Ashesi)</label>
            <input type="email" name="email" id="email" required placeholder="name@ashesi.edu.gh">

            <label for="password">Password</label>
            <input type="password" name="password" id="password" required>

            <div class="row spaced">
                <label for="remember">
                    <input type="checkbox" id="remember" name="remember"> Remember me
                </label>
                <a href="./register.php" class="link">Sign up</a>
            </div>

            <button type="submit" class="btn">Login</button>

            <div id="msg" class="msg"></div>
        </form>
    </div>
</div> 
<script>
document.getElementById('loginForm').addEventListener('submit', async function(e){
    e.preventDefault();

    const fd = new FormData(this);
    const msg = document.getElementById('msg');
    const btn = this.querySelector('.btn');
    
    msg.textContent = 'Logging in...';
    msg.classList.remove('success', 'error');
    btn.disabled = true;

    try {
        const res = await fetch('../api/login.php', {
            method: 'POST',
            body: fd
        });
        
        // Vérifie si la réponse HTTP est OK avant de lire le JSON
        if (!res.ok) {
            throw new Error(`HTTP error! status: ${res.status}`);
        }

        const j = await res.json();
        
        if (j.success) {
            msg.textContent = "Login successful! Redirecting...";
            msg.classList.add("success");
            // Redirection vers le dashboard approprié
            setTimeout(() => window.location.href = j.redirect, 600); 
        } else {
            msg.textContent = j.error || "Invalid credentials. Please try again.";
            msg.classList.add("error");
            btn.disabled = false; // Réactiver le bouton en cas d'échec
        }
    } catch (error) {
        msg.textContent = `Network error: ${error.message}. Cannot connect to API.`;
        msg.classList.add("error");
        btn.disabled = false; // Réactiver le bouton en cas d'erreur réseau
    }
});
</script>

</body>
</html>
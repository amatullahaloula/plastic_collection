<?php
// views/register.php
session_start();
if (isset($_SESSION['user'])) {
    $role = $_SESSION['user']['role'] ?? 'student';
    header("Location: ./dashboard_{$role}.php");
    exit;
}
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Inscription - Ashesi Plastic</title>
    
    <link rel="stylesheet" href="../css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        /* ------------------------------------------- */
        /* FUSION ET NETTOYAGE CSS - MAROON THEME */
        /* ------------------------------------------- */
        
        body {
            margin: 0;
            padding: 0;
            font-family: "Inter", sans-serif;
            background: linear-gradient(135deg, #800020 0%, #4a0012 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* Arrière-plan */
        .page-background {
            background: url('../css/asset/empty_bottle_picture .png');
            background-size: cover;
            background-position: center;
            position: fixed;
            inset: 0;
            opacity: 0.15;
            filter: blur(6px);
            z-index: -1;
        }

        /* Transparent container behavior (Wrapper autour du formulaire) */
        .transparent-wrapper {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(12px);
            border-radius: 18px;
            padding: 20px;
            margin: 20px;
            z-index: 10;
            box-shadow: 0 20px 60px rgba(128, 0, 32, 0.4);
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
            box-shadow: 0 4px 20px rgba(128, 0, 32, 0.15);
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
            background: #800020;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.3s;
            font-weight: 600;
        }
        .btn:hover { 
            background: #4a0012; 
        }

        .row.between {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
        }

        /* Titre du formulaire (H2) */
        .card.form-container h2 {
            color: #800020;
            font-size: 24px;
            margin-bottom: 20px;
        }
        
        /* Lien "Back to login" */
        .row.between a {
            color: #800020;
            text-decoration: none;
            font-weight: 600;
        }
        .row.between a:hover {
             color: #4a0012;
             text-decoration: underline;
        }

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
            <h2>🌍 Welcome to Ashesi Plastic<br>Register now</h2>

            <form id="regForm" autocomplete="off">
                <div class="two-cols">
                    <div>
                        <label for="firstName">First name</label>
                        <input name="first_name" id="firstName" required>
                    </div>
                    <div>
                        <label for="lastName">Last name</label>
                        <input name="last_name" id="lastName" required>
                    </div>
                </div>

                <label for="nickname">Nickname (optional)</label>
                <input name="nickname" id="nickname" placeholder="How people will call you">

                <label for="email">Ashesi Email</label>
                <input name="email" id="email" type="email" placeholder="you@ashesi.edu.gh" required>

                <label for="roleSelect">Role</label>
                <select name="role" id="roleSelect" required>
                    <option value="">Select role</option>
                    <option value="student">Student</option>
                    <option value="cleaner">Cleaner</option>
                    <option value="admin">Admin</option>
                </select>

                <div id="degreeBlock" style="display:none;">
                    <label for="degreeSelect">Class / Year</label>
                    <select name="degree" id="degreeSelect">
                        <option value="">Select year</option>
                        <option value="Year 1">Year 1</option>
                        <option value="Year 2">Year 2</option>
                        <option value="Year 3">Year 3</option>
                        <option value="Year 4">Year 4</option>
                    </select>
                </div>

                <label for="password">Password</label>
                <input name="password" type="password" id="password" required>

                <label for="password_confirm">Confirm Password</label>
                <input name="password_confirm" type="password" id="password_confirm" required>

                <label for="phone">Phone (optional)</label>
                <input name="phone" id="phone" type="tel" placeholder="+233...">

                <div class="row between">
                    <button type="submit" class="btn" id="submitBtn">Sign up</button>
                    <a href="./login.php">Back to login</a>
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

const updateDegreeVisibility = () => {
    if (roleSelect.value === 'student') {
        degreeBlock.style.display = 'block';
        degreeSelect.required = true;
    } else {
        degreeBlock.style.display = 'none';
        degreeSelect.required = false;
        degreeSelect.value = "";
    }
};

roleSelect.addEventListener('change', updateDegreeVisibility);
document.addEventListener('DOMContentLoaded', updateDegreeVisibility);

// handle form submit via fetch JSON
document.getElementById('regForm').addEventListener('submit', async function(e){
    e.preventDefault();
    
    const msgEl = document.getElementById('msg');
    const submitBtn = document.getElementById('submitBtn');
    
    msgEl.textContent = 'Processing...';
    msgEl.classList.remove('error');
    submitBtn.disabled = true;

    const form = new FormData(this);
    const p1 = form.get('password');
    const p2 = form.get('password_confirm');
    
    if (p1 !== p2) {
        msgEl.textContent = 'Passwords do not match.';
        msgEl.classList.add('error');
        submitBtn.disabled = false;
        return;
    }

    const email = form.get('email') || '';
    if (!/@ashesi\.edu\.gh$/i.test(email)) {
        msgEl.textContent = 'Please use a valid Ashesi email (example@ashesi.edu.gh).';
        msgEl.classList.add('error');
        submitBtn.disabled = false;
        return;
    }

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
        
        if (!res.ok) {
            throw new Error(`HTTP error! status: ${res.status}`);
        }
        
        const j = await res.json();
        
        if (j.success) {
            msgEl.textContent = 'Account created successfully. Redirecting to login...';
            setTimeout(()=> window.location.href = './login.php', 900);
        } else {
            msgEl.textContent = j.error || 'Server error. Account creation failed.';
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
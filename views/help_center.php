<?php
// views/help_center.php
session_start();
// Le centre d'aide n'exige pas de connexion, il suffit d'inclure la session si l'on souhaite y accéder.
// Si vous voulez une barre de navigation pour revenir au dashboard :
$is_logged_in = isset($_SESSION['user']);
$user_role = $_SESSION['user']['role'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Help Center - Ashesi Plastic</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="../css/help.css"> 
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
        background: #f6f6f6;
    }

    /* Arrière-plan (utilise l'URL Unsplash pour la cohérence avec le CSS inséré) */
    .page-background {
        background: url("https://images.unsplash.com/photo-1503264116251-35a269479413?auto=format&fit=crop&w=1500&q=80")
            center/cover no-repeat;
        position: fixed;
        inset: 0;
        opacity: 0.35;
        filter: blur(6px);
        z-index: -1;
    }

    /* Transparent container behavior (Wrapper principal) */
    .transparent-wrapper {
        background: rgba(255, 255, 255, 0.45);
        backdrop-filter: blur(12px);
        border-radius: 18px;
        padding: 20px;
        margin: 20px auto;
        max-width: 900px; /* Ajout d'une largeur maximale pour la lisibilité */
    }

    /* Conteneur de la page (à l'intérieur du wrapper) */
    .container {
        padding: 10px; 
    }

    .title {
        color: #1b3d6d;
        font-size: 36px;
        margin-bottom: 10px;
    }
    
    .subtitle {
        color: #555;
        margin-bottom: 25px;
        border-bottom: 2px solid #ddd;
        padding-bottom: 15px;
    }

    .card {
        background: white;
        border-radius: 12px;
        padding: 25px;
        margin-bottom: 20px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }

    .card h2 {
        color: #5a0016; /* Utilisation de la couleur du tableau de bord étudiant pour les titres */
        border-bottom: 1px solid #eee;
        padding-bottom: 10px;
        margin-top: 0;
    }

    /* Styles pour la section FAQ (collapse) */
    .collapse-btn {
        background: #f8f8f8;
        color: #1b3d6d;
        cursor: pointer;
        padding: 12px 18px;
        width: 100%;
        border: none;
        text-align: left;
        outline: none;
        font-size: 16px;
        transition: 0.4s;
        border-radius: 6px;
        margin-bottom: 5px;
    }

    .collapse-btn:hover {
        background-color: #e6e6e6;
    }

    .collapse-content {
        padding: 0 18px;
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease-out;
        background-color: #f1f1f1;
        border-radius: 0 0 6px 6px;
        margin-bottom: 10px;
    }

    /* Styles pour le formulaire */
    .card form label {
        display: block;
        margin-top: 10px;
        margin-bottom: 5px;
        font-weight: 600;
        color: #333;
    }

    .card form input, 
    .card form textarea {
        width: 100%;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 8px;
        box-sizing: border-box;
        font-family: inherit;
    }

    .send-btn {
        padding: 12px 20px;
        background: #5a0016; /* Couleur différente pour le bouton de contact */
        color: white;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        margin-top: 15px;
        transition: background 0.3s;
    }

    .send-btn:hover {
        background: #7a0026;
    }
</style>

</head>

<body>

<header style="padding: 10px 20px; text-align: right;">
    <?php if ($is_logged_in): ?>
        <a href="./dashboard_<?php echo $user_role; ?>.php" style="color:#1b3d6d; text-decoration: none; font-weight: 600;">← Back to Dashboard</a>
    <?php else: ?>
        <a href="./login.php" style="color:#1b3d6d; text-decoration: none; font-weight: 600;">← Login</a>
    <?php endif; ?>
</header>


<div class="page-background"></div>

<div class="transparent-wrapper">
    <div class="container">

        <h1 class="title"> Help Center</h1>
        <p class="subtitle">Find answers, contact support and learn how recycling works.</p>

        <div class="card">
            <h2>Frequently Asked Questions </h2>

            <div class="collapse">
                <button class="collapse-btn">• How do I earn rewards? (Tap to expand)</button>
                <div class="collapse-content">
                    <p>You earn **<?php echo number_format(1.00, 2); ?> GHS** for each plastic bottle you recycle on campus. Rewards are credited to your account after a cleaner verifies the collection.</p>
                </div>
            </div>

            <div class="collapse">
                <button class="collapse-btn">• Where do I drop my plastic bottles?</button>
                <div class="collapse-content">
                    <p>When submitting a request on your dashboard, select your **Location** (e.g., dormitory, lecture hall) and **Room Number**. A cleaner will come to this specified location to collect the bottles.</p>
                </div>
            </div>

            <div class="collapse">
                <button class="collapse-btn">• How do cleaners verify my bottles?</button>
                <div class="collapse-content">
                    <p>When the cleaner arrives at your location, they will use their app to **scan your student ID** and confirm the number of bottles collected. The rewards are instantly approved at this point.</p>
                </div>
            </div>

            <div class="collapse">
                <button class="collapse-btn">• How do I withdraw my rewards?</button>
                <div class="collapse-content">
                    <p>Go to the **Payment Info** section on your dashboard, add your bank account or **MoMo number** (Mobile Money). You can then request a payout for your balance.</p>
                </div>
            </div>

        </div>


       


        <div class="card">
            <h2>Contact Support </h2>

            <p><strong>Email:</strong> ashesi-recycling@support.com</p>
            <p><strong>Phone:</strong> +233 55 123 4567</p>
            <p><strong>Location:</strong> Ashesi University — Eco Office (Open M-F, 9am-4pm)</p>
        </div>


        <div class="card">
            <h2>Send us a message </h2>
            <form id="supportForm" action="/plastic_collection/api/send_support.php" method="POST">
                
                <label for="supportEmail">Your Email</label>
                <input type="email" id="supportEmail" name="email" placeholder="yourname@ashesi.edu.gh" required value="<?php echo htmlspecialchars($_SESSION['user']['email'] ?? ''); ?>">

                <label for="supportSubject">Subject (optional)</label>
                <input type="text" id="supportSubject" name="subject" placeholder="Issue with my last collection...">
                
                <label for="supportMessage">Your Message</label>
                <textarea id="supportMessage" name="message" rows="4" placeholder="Describe your issue..." required></textarea>

                <button type="submit" class="send-btn" id="sendBtn">Send Message</button>
                <div id="supportMsg" style="margin-top: 10px;"></div>
            </form>
        </div>

    </div>
</div> <script src="../js/help.js"></script> 
<script>
    // ----------------------------------------------------
    // Simulating Form Submission (Requires API /plastic_collection/api/send_support.php)
    // ----------------------------------------------------
    
    document.getElementById('supportForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const form = e.target;
        const msgEl = document.getElementById('supportMsg');
        const sendBtn = document.getElementById('sendBtn');
        
        msgEl.textContent = 'Sending...';
        msgEl.style.color = '#1b3d6d';
        sendBtn.disabled = true;

        const formData = new FormData(form);

        try {
            // Note: Since the backend API for sending support messages is not provided, 
            // this is a simulated call. You must implement /api/send_support.php.
            const res = await fetch(form.action, {
                method: 'POST',
                body: formData
            });

            // Assuming a successful response structure like { success: true }
            const j = await res.json(); 

            if (j.success) {
                msgEl.textContent = 'Message sent successfully! We will get back to you soon.';
                msgEl.style.color = 'green';
                form.reset(); // Vider le formulaire après succès
            } else {
                msgEl.textContent = j.error || 'Failed to send message. Please try again.';
                msgEl.style.color = 'red';
            }
        } catch (error) {
            msgEl.textContent = 'Network error. Please check your connection.';
            msgEl.style.color = 'red';
        } finally {
            sendBtn.disabled = false;
        }
    });

    // ----------------------------------------------------
    // Collapse Logic (Alternative if ../js/help.js is unavailable or incomplete)
    // ----------------------------------------------------
    /*
    document.querySelectorAll('.collapse-btn').forEach(button => {
        button.addEventListener('click', () => {
            const content = button.nextElementSibling;
            if (content.style.maxHeight) {
                content.style.maxHeight = null;
                content.style.padding = '0 18px';
            } else {
                content.style.maxHeight = content.scrollHeight + "px";
                content.style.padding = '10px 18px 18px 18px';
            }
        });
    });
    */
</script>
</body>
</html>
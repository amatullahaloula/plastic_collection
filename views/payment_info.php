<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'student') {
    header('Location: /plastic_collection/views/login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Payment Info</title>

<!-- Global transparent background -->
<link rel="stylesheet" href="/plastic_collection/css/global_bg.css">

<!-- Main Ashesi theme -->
<link rel="stylesheet" href="/plastic_collection/css/style.css">

<style>
    .payment-container {
        max-width: 600px;
        margin: 40px auto;
        padding: 25px;
        background: rgba(255,255,255,0.80);
        border-radius: 15px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        backdrop-filter: blur(6px);
    }

    h2 {
        margin-bottom: 15px;
        color: var(--accent);
    }

    label {
        font-weight: 600;
        display: block;
        margin-top: 15px;
        color: #333;
    }

    input, select {
        width: 100%;
        margin-top: 6px;
        padding: 12px;
        border-radius: 8px;
        border: 1px solid #bbb;
    }

    .btn-save {
        margin-top: 25px;
        width: 100%;
        padding: 12px;
        border: none;
        background: var(--accent);
        color: white;
        font-size: 16px;
        border-radius: 10px;
        cursor: pointer;
    }

    .btn-save:hover {
        background: var(--accent-dark);
    }

    #msg {
        margin-top: 15px;
        font-weight: bold;
    }
</style>
</head>

<body>

<div class="payment-container">
    <h2>Payment Information</h2>
    <p>Enter your payment details to receive your rewards safely.</p>

    <form id="paymentForm">

        <label for="method">Payment Method</label>
        <select name="method" id="method" required>
            <option value="">Choose method</option>
            <option value="momo">Mobile Money (MoMo)</option>
            <option value="bank">Bank Transfer</option>
        </select>

        <div id="dynamicFields"></div>

        <button type="submit" class="btn-save">Save Payment Info</button>

        <div id="msg"></div>
    </form>
</div>

<script>
// Generate fields depending on method
document.getElementById('method').addEventListener('change', function () {
    let box = document.getElementById('dynamicFields');

    if (this.value === "momo") {
        box.innerHTML = `
            <label>MoMo Number</label>
            <input type="text" name="momo_number" placeholder="eg: 0551234567" required>

            <label>Network</label>
            <select name="network" required>
                <option value="MTN">MTN</option>
                <option value="Vodafone">Vodafone</option>
                <option value="AirtelTigo">AirtelTigo</option>
            </select>
        `;
    }

    else if (this.value === "bank") {
        box.innerHTML = `
            <label>Bank Name</label>
            <input type="text" name="bank_name" placeholder="eg: CalBank" required>

            <label>Account Number</label>
            <input type="text" name="account_number" placeholder="xxxxxxxxxx" required>
        `;
    }

    else {
        box.innerHTML = "";
    }
});

// Submit form
document.getElementById('paymentForm').addEventListener('submit', async function(e){
    e.preventDefault();
    const form = new FormData(this);

    const res = await fetch('/plastic_collection/api/save_payment.php', {
        method: 'POST',
        body: form
    });

    const j = await res.json();
    const msg = document.getElementById('msg');

    if (j.success) {
        msg.style.color = "green";
        msg.textContent = "Payment info saved successfully!";
    } else {
        msg.style.color = "red";
        msg.textContent = j.error || "Something went wrong";
    }
});
</script>

</body>
</html>

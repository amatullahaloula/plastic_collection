<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'student') {
    header('Location: ./login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Payment Info - Ashesi Plastic</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<!-- Global transparent background -->
<link rel="stylesheet" href="../css/global_bg.css">

<!-- Main Ashesi theme -->
<link rel="stylesheet" href="../css/style.css">

<style>
    body {
        margin: 0;
        padding: 0;
        font-family: "Inter", sans-serif;
        background: linear-gradient(135deg, #800020 0%, #4a0012 100%);
        min-height: 100vh;
        padding: 20px;
    }

    .payment-container {
        max-width: 600px;
        margin: 40px auto;
        padding: 30px;
        background: rgba(255, 255, 255, 0.98);
        border-radius: 18px;
        box-shadow: 0 20px 60px rgba(128, 0, 32, 0.4);
        backdrop-filter: blur(12px);
    }

    h2 {
        margin-bottom: 15px;
        color: #800020;
        font-size: 28px;
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
        box-sizing: border-box;
    }

    .btn-save {
        margin-top: 25px;
        width: 100%;
        padding: 12px;
        border: none;
        background: #800020;
        color: white;
        font-size: 16px;
        font-weight: 600;
        border-radius: 10px;
        cursor: pointer;
        transition: background 0.3s;
    }

    .btn-save:hover {
        background: #4a0012;
    }

    #msg {
        margin-top: 15px;
        font-weight: bold;
    }

    /* FIXED BACK BUTTON */
    .back-btn {
        display: inline-block;
        margin-bottom: 20px;
        padding: 10px 18px;
        background: #800020;
        color: white;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        box-shadow: 0 4px 12px rgba(128, 0, 32, 0.3);
        transition: background 0.3s;
    }

    .back-btn:hover {
        background: #4a0012;
    }

    p {
        color: #555;
        margin-bottom: 20px;
    }
</style>
</head>

<body>

<div class="payment-container">

    <!-- BACK BUTTON -->
    <a href="./dashboard_student.php" class="back-btn">
        ← Back to Dashboard
    </a>

    <h2>💳 Payment Information</h2>
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
// Load existing payment info on page load
async function loadPaymentInfo() {
    try {
        const res = await fetch('../api/get_payment.php');
        const data = await res.json();

        if (data.success && data.data) {
            const payment = data.data;
            
            // Pre-select the method
            document.getElementById('method').value = payment.method;
            
            // Trigger change event to load fields
            document.getElementById('method').dispatchEvent(new Event('change'));

            // Pre-fill the fields after a short delay
            setTimeout(() => {
                if (payment.method === 'momo') {
                    document.querySelector('[name="momo_number"]').value = payment.momo_number || '';
                    document.querySelector('[name="network"]').value = payment.network || '';
                } else if (payment.method === 'bank') {
                    document.querySelector('[name="bank_name"]').value = payment.bank_name || '';
                    document.querySelector('[name="account_number"]').value = payment.account_number || '';
                }
            }, 100);
        }
    } catch (error) {
        console.error('Error loading payment info:', error);
    }
}

// Load payment info when page loads
loadPaymentInfo();

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

    const res = await fetch('../api/save_payment.php', {
        method: 'POST',
        body: form
    });

    const j = await res.json();
    const msg = document.getElementById('msg');

    if (j.success) {
        msg.style.color = "green";
        msg.textContent = "Payment info saved successfully!";
        loadPaymentInfo();
    } else {
        msg.style.color = "red";
        msg.textContent = j.error || "Something went wrong";
    }
});
</script>

</body>
</html>
<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: user_login.php");
    exit;
}

/* TEST KEYS ONLY */
$keyId = "rzp_test_SEJ56DpnPdihCJ";

$name   = $_SESSION['name']  ?? 'User';
$email  = $_SESSION['email'] ?? 'test@example.com';
$amount = ($_SESSION['amount'] ?? 1) * 100; // paise
?>

<!DOCTYPE html>
<html>
<head>
    <title>Pay Now</title>
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
</head>
<body>

<script>
var options = {
    "key": "<?= $keyId ?>",
    "amount": "<?= $amount ?>",
    "currency": "INR",
    "name": "Alimento",
    "description": "Food Order",
    "handler": function (response){
        window.location.href =
            "verify.php?razorpay_payment_id=" + response.razorpay_payment_id;
    },
    "prefill": {
        "name": "<?= $name ?>",
        "email": "<?= $email ?>"
    },
    "theme": {
        "color": "#F37254"
    }
};

var rzp = new Razorpay(options);
rzp.open();
</script>

</body>
</html>

<?php
session_start();

// connect to database
include 'partials/_dbconnect.php';

/* ---------- SESSION SAFETY ---------- */
$order   = $_SESSION['Order']   ?? [];
$amount  = (float)($_SESSION['amount'] ?? 0);
$userId  = $_SESSION['user_id'] ?? null;
$rid     = $_SESSION['rest_id'] ?? null;

if ($amount <= 0 || empty($order)) {
    die("Invalid order session");
}

/* ---------- ORDER ID ---------- */
$oid = mt_rand(10000, 99999);
$_SESSION['orderid'] = str_pad($oid, 5, '0', STR_PAD_LEFT);

/* ---------- LOGIN STATUS ---------- */
$login_status = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;

/* ---------- BILLING CALCULATION ---------- */
$subtotal = $amount;

// 5% tax
$tax = round($subtotal * 0.05, 2);

// Delivery logic
$deliveryCharge = ($subtotal >= 100) ? 0 : 20;

// Final total
$totalAmount = round($subtotal + $tax + $deliveryCharge, 2);

// Save for payment page
$_SESSION['final_amount'] = $totalAmount;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Checkout</title>

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>

  <link rel="stylesheet" href="output.css">
</head>

<body class="font-poppins bg-gray-50">

<!-- NAVBAR -->
<nav class="hidden lg:flex max-w-7xl mx-auto items-center justify-between py-4">
  <a href="index.php"><img src="./images/logo/logo.webp" class="w-36"></a>
  <div class="flex gap-2">
    <a href="home.php" class="px-4 py-2 rounded-full hover:bg-gray-200">Restaurants</a>
    <a href="new_track_order.php" class="px-4 py-2 rounded-full hover:bg-gray-200">Orders</a>
    <?php if ($login_status): ?>
      <a href="profile.php" class="px-4 py-2 rounded-full hover:bg-gray-200">Account</a>
      <a href="user_logout.php" class="bg-red-500 text-white px-6 py-2 rounded-full">Logout</a>
    <?php else: ?>
      <a href="user_login.php" class="bg-gray-900 text-white px-6 py-2 rounded-full">Login</a>
    <?php endif; ?>
  </div>
</nav>

<!-- CHECKOUT -->
<form action="pay.php" method="post">
<div class="max-w-7xl mx-auto mt-10 p-4 flex flex-col md:flex-row gap-6">

  <!-- LEFT FORM -->
  <div class="bg-white p-6 rounded-xl shadow w-full md:w-1/2">
    <h3 class="font-bold text-xl mb-4">Contact Information</h3>

    <input name="email" type="email" required placeholder="Email"
      class="w-full p-2 border rounded mb-3">

    <input name="phone" type="text" required placeholder="Phone"
      class="w-full p-2 border rounded mb-3">

    <div class="flex gap-3">
      <input name="fname" type="text" placeholder="First Name"
        class="w-full p-2 border rounded mb-3">
      <input name="lname" type="text" placeholder="Last Name"
        class="w-full p-2 border rounded mb-3">
    </div>

    <h3 class="font-bold text-xl mt-6 mb-3">Billing & Shipping</h3>

    <input name="address1" type="text" required placeholder="Address"
      class="w-full p-2 border rounded mb-3">

    <input name="city" type="text" required placeholder="City"
      class="w-full p-2 border rounded mb-3">

    <select name="state" required class="w-full p-2 border rounded mb-3">
      <option value="">Select State</option>
      <?php
      $states = [
        "Andhra Pradesh","Arunachal Pradesh","Assam","Bihar","Chhattisgarh","Goa",
        "Gujarat","Haryana","Himachal Pradesh","Jharkhand","Karnataka","Kerala",
        "Madhya Pradesh","Maharashtra","Manipur","Meghalaya","Mizoram","Nagaland",
        "Odisha","Punjab","Rajasthan","Sikkim","Tamil Nadu","Telangana","Tripura",
        "Uttar Pradesh","Uttarakhand","West Bengal"
      ];
      foreach ($states as $state) {
        echo "<option value=\"$state\">$state</option>";
      }
      ?>
    </select>

    <input name="zip" type="text" placeholder="ZIP Code"
      class="w-full p-2 border rounded mb-3">

    <textarea name="notes" rows="3" placeholder="Delivery notes"
      class="w-full p-2 border rounded"></textarea>
  </div>

  <!-- RIGHT SUMMARY -->
  <div class="bg-white p-6 rounded-xl shadow w-full md:w-1/2">
    <h3 class="font-bold text-xl mb-4">Order Summary</h3>

    <?php foreach ($order as $item): ?>
      <div class="flex justify-between mb-2">
        <span><?= htmlspecialchars($item) ?></span>
      </div>
    <?php endforeach; ?>

    <hr class="my-4">

    <div class="flex justify-between font-semibold">
      <span>Subtotal</span>
      <span>₹<?= number_format($subtotal, 2) ?></span>
    </div>

    <div class="flex justify-between font-semibold mt-2">
      <span>Delivery</span>
      <span><?= $deliveryCharge == 0 ? "Free" : "₹20.00" ?></span>
    </div>

    <div class="flex justify-between font-semibold mt-2">
      <span>Tax (5%)</span>
      <span>₹<?= number_format($tax, 2) ?></span>
    </div>

    <hr class="my-4">

    <div class="flex justify-between text-lg font-bold">
      <span>Total</span>
      <span>₹<?= number_format($totalAmount, 2) ?></span>
    </div>

    <button type="submit"
  style="background:#ea580c;color:white"
  class="w-full mt-6 py-3 rounded-lg font-semibold hover:opacity-90 transition">
  Place Order
</button>

  </div>

</div>
</form>

</body>
</html>

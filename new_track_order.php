<?php
session_start();

/* ---------------- AUTH CHECK ---------------- */
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: user_login.php");
    exit;
}

$login_status = true;

include 'partials/_dbconnect.php';

/* ---------------- FIX: ENSURE user_id EXISTS ---------------- */
// Case 1: user_id already in session (normal case)
if (isset($_SESSION['user_id'])) {
    $uid = $_SESSION['user_id'];
}
// Case 2: user_id missing but email exists → recover from DB
elseif (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];

    $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($uid);
    $stmt->fetch();
    $stmt->close();

    if ($uid) {
        $_SESSION['user_id'] = $uid; // restore session
    } else {
        // corrupted session → force re-login
        session_destroy();
        header("location: user_login.php");
        exit;
    }
}
// Case 3: completely broken session
else {
    session_destroy();
    header("location: user_login.php");
    exit;
}

$feedback = false;

/* ---------------- HANDLE RATING SUBMIT ---------------- */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $r_id = (int)$_POST['r_id'];
    $order_id = (int)$_POST['order_id'];
    $rating = (int)$_POST['rating'];

    // update order rating
    $stmt = $conn->prepare(
        "UPDATE orders SET rating = ? WHERE r_id = ? AND order_id = ?"
    );
    $stmt->bind_param("iii", $rating, $r_id, $order_id);
    $stmt->execute();
    $stmt->close();

    // recalculate restaurant rating
    $stmt = $conn->prepare(
        "SELECT COUNT(*) AS total, SUM(rating) AS sum_rating
         FROM orders WHERE r_id = ? AND rating != 0"
    );
    $stmt->bind_param("i", $r_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    $stmt->close();

    if ($data['total'] > 0) {
        $new_rating = $data['sum_rating'] / $data['total'];

        $stmt = $conn->prepare(
            "UPDATE restaurant SET r_rating = ? WHERE r_id = ?"
        );
        $stmt->bind_param("di", $new_rating, $r_id);
        $stmt->execute();
        $stmt->close();
    }
}

/* ---------------- FETCH USER ORDERS ---------------- */
$stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ?");
$stmt->bind_param("i", $uid);
$stmt->execute();
$result = $stmt->get_result();
$orders = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Your Orders</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <!-- Tailwind CDN (safe, no npm needed) -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body class="bg-[#F0EAEA] font-[Poppins]">

<!-- NAVBAR -->
<nav class="hidden lg:flex max-w-7xl mx-auto items-center justify-between py-4">
    <a href="index.php"><img src="./images/logo/logo.webp" class="w-36"></a>
    <div class="flex gap-2">
        <a href="home.php" class="px-4 py-2 rounded-full hover:bg-gray-200">Restaurants</a>
        <a href="new_track_order.php" class="px-4 py-2 rounded-full bg-gray-200">Orders</a>
        <a href="profile.php" class="px-4 py-2 rounded-full hover:bg-gray-200">Account</a>
    </div>
    <a href="user_logout.php" class="bg-red-500 text-white px-6 py-2 rounded-full">Logout</a>
</nav>

<!-- ORDERS -->
<section class="max-w-7xl mx-auto p-5">
    <h2 class="text-3xl font-bold mb-6">Your Orders</h2>

    <div class="overflow-x-auto bg-white rounded-2xl shadow p-5">
        <table class="w-full border-collapse">
            <thead>
                <tr class="bg-gray-100 text-left">
                    <th class="p-3">Item</th>
                    <th class="p-3">Order #</th>
                    <th class="p-3">Date</th>
                    <th class="p-3">Amount</th>
                    <th class="p-3">Payment</th>
                    <th class="p-3">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($orders) === 0): ?>
                    <tr>
                        <td colspan="6" class="text-center p-6 text-gray-500">
                            No orders found
                        </td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($orders as $order): 
                    $payment = strtolower($order['payment']) === 'done' ? 'Completed' : 'Pending';
                ?>
                <tr class="border-t">
                    <td class="p-3"><?= htmlspecialchars($order['order']) ?></td>
                    <td class="p-3">#<?= $order['order_id'] ?></td>
                    <td class="p-3"><?= date('M d, Y', strtotime($order['dt'])) ?></td>
                    <td class="p-3">₹<?= $order['amount'] ?></td>
                    <td class="p-3">
                        <span class="<?= $payment === 'Completed'
                            ? 'bg-green-100 text-green-800'
                            : 'bg-yellow-100 text-yellow-800'
                        ?> px-3 py-1 rounded-md">
                            <?= $payment ?>
                        </span>
                    </td>
                    <td class="p-3">Delivered</td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>

</body>
</html>

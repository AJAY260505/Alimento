<?php
session_start();

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header("Location: home.php");
    exit;
}

$showAlert = false;
$showError = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    include 'partials/_dbconnect.php';

    $name = trim($_POST["name"] ?? '');
    $email = trim($_POST["email"] ?? '');
    $password = $_POST["password"] ?? '';
    $cpassword = $_POST["cpassword"] ?? '';

    // ✅ Email validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $showError = "Invalid email address.";
    }
    // ✅ Password strength
    elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $password)) {
        $showError = "Password must be at least 8 characters and include uppercase, lowercase, number & special character.";
    }
    // ✅ Password match
    elseif ($password !== $cpassword) {
        $showError = "Passwords do not match.";
    }
    else {
        // ✅ Check if email already exists
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $showError = "Email already registered. Please login.";
        } else {
            // ✅ Insert user
            $hash = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare(
                "INSERT INTO users (name, email, password, date, account_status, resetcode)
                 VALUES (?, ?, ?, NOW(), 'Verified', '0')"
            );
            $stmt->bind_param("sss", $name, $email, $hash);

            if ($stmt->execute()) {
                // ✅ Auto login after signup
                $_SESSION['loggedin'] = true;
                $_SESSION['email'] = $email;
                $_SESSION['name'] = $name;

                header("Location: home.php");
                exit;
            } else {
                $showError = "Something went wrong. Please try again.";
            }
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Alimento • Signup</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- ✅ Tailwind CDN -->
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
</head>

<body class="font-[Poppins] bg-gray-100">

<div class="min-h-screen flex items-center justify-center">
  <div class="bg-white shadow-lg rounded-xl w-full max-w-md p-8">

    <h1 class="text-2xl font-bold mb-6 text-center">Create Account</h1>

    <?php if ($showError): ?>
      <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
        <?= htmlspecialchars($showError) ?>
      </div>
    <?php endif; ?>

    <form method="POST" class="flex flex-col gap-4">

      <div>
        <label class="text-sm font-medium">Name</label>
        <input type="text" name="name" required
               class="w-full border px-3 py-2 rounded focus:outline-none focus:ring">
      </div>

      <div>
        <label class="text-sm font-medium">Email</label>
        <input type="email" name="email" required
               class="w-full border px-3 py-2 rounded focus:outline-none focus:ring">
      </div>

      <div>
        <label class="text-sm font-medium">Password</label>
        <input type="password" name="password" required
               class="w-full border px-3 py-2 rounded focus:outline-none focus:ring">
      </div>

      <div>
        <label class="text-sm font-medium">Confirm Password</label>
        <input type="password" name="cpassword" required
               class="w-full border px-3 py-2 rounded focus:outline-none focus:ring">
      </div>

      <button type="submit"
              class="bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition">
        Create Account
      </button>
    </form>

    <p class="text-center text-sm mt-4">
      Already have an account?
      <a href="user_login.php" class="text-blue-600 hover:underline">Login</a>
    </p>

  </div>
</div>

</body>
</html>

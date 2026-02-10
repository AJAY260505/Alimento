<?php
session_start();
$login_status = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
  <meta charset="UTF-8">
  <title>Alimento</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- Tailwind CDN -->
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- Tailwind Dark Mode Config -->
  <script>
    tailwind.config = {
      darkMode: 'class',
      theme: {
        extend: {
          fontFamily: {
            poppins: ['Poppins', 'sans-serif'],
          }
        }
      }
    }
  </script>

  <!-- Fonts & Icons -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body class="font-poppins bg-[#FAFAF7] text-gray-800 dark:bg-[#0F172A] dark:text-gray-100 transition-colors duration-300">

<!-- 🌙 DARK MODE SCRIPT -->
<script>
  const themeToggle = () => {
    document.documentElement.classList.toggle('dark');
    localStorage.theme = document.documentElement.classList.contains('dark') ? 'dark' : 'light';
  };

  if (
    localStorage.theme === 'dark' ||
    (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)
  ) {
    document.documentElement.classList.add('dark');
  }
</script>

<!-- NAVBAR -->
<nav class="hidden lg:flex sticky top-0 z-50 backdrop-blur bg-white/80 dark:bg-[#020617]/80
            max-w-7xl mx-auto px-6 py-4 rounded-b-3xl shadow">
  <a href="index.php">
    <img src="./images/logo/logo.webp" class="w-36" alt="Alimento">
  </a>

  <div class="flex gap-3">
    <a href="home.php" class="px-4 py-2 rounded-full hover:bg-gray-200 dark:hover:bg-gray-700">Restaurants</a>
    <a href="new_track_order.php" class="px-4 py-2 rounded-full hover:bg-gray-200 dark:hover:bg-gray-700">Orders</a>
    <a href="#" class="px-4 py-2 rounded-full hover:bg-gray-200 dark:hover:bg-gray-700">Contact</a>
    <?php if ($login_status): ?>
      <a href="profile.php" class="px-4 py-2 rounded-full hover:bg-gray-200 dark:hover:bg-gray-700">Account</a>
    <?php endif; ?>
  </div>

  <div class="flex items-center gap-3">
    <!-- Dark Mode Button -->
    <button onclick="themeToggle()" class="text-xl hover:scale-110 transition">
      🌙
    </button>

    <?php if ($login_status): ?>
      <a href="user_logout.php" class="bg-red-500 text-white px-6 py-2 rounded-full hover:bg-red-600">Logout</a>
    <?php else: ?>
      <a href="user_login.php" class="bg-gray-900 text-white px-6 py-2 rounded-full hover:bg-gray-800">Login</a>
    <?php endif; ?>
  </div>
</nav>

<!-- HERO -->
<section class="max-w-7xl mx-auto px-5 mt-10">
  <div class="bg-[#E6E8DD] dark:bg-[#020617]
              rounded-3xl shadow p-10
              flex flex-wrap-reverse md:flex-nowrap gap-10">

    <div class="flex-1">
      <h1 class="text-4xl lg:text-5xl font-extrabold">
        Discover the best food at your place
      </h1>
      <p class="mt-4 text-gray-600 dark:text-gray-300 text-lg">
        Fresh homemade food from trusted local chefs.
      </p>

      <form action="pin_search.php" method="post"
            class="mt-8 flex bg-white dark:bg-gray-800 rounded-full px-5 py-3 shadow">
        <input type="number" name="pincode" placeholder="Search by pincode"
               class="flex-1 bg-transparent outline-none text-lg" required>
        <button><i class="bi bi-crosshair text-xl"></i></button>
      </form>

      <div class="flex gap-4 mt-6">
        <a href="home.php"
           class="bg-[#6E725E] text-white w-full py-3 rounded-full text-center hover:opacity-90">
          Delivery
        </a>
        <a href="home.php"
           class="border w-full py-3 rounded-full text-center hover:bg-gray-100 dark:hover:bg-gray-800">
          Dine In
        </a>
      </div>
    </div>

    <div class="flex flex-col items-center gap-6">
      <img src="./images/pizza-hero.webp"
           class="w-48 rounded-full bg-[#D7DACB] p-3">
      <img src="./images/dish1-hero.webp"
           class="w-80 rounded-full bg-[#D7DACB] p-3">
    </div>
  </div>
</section>

<!-- SERVICES -->
<section class="max-w-7xl mx-auto px-5 mt-20 grid md:grid-cols-3 gap-10">
  <div class="bg-white dark:bg-gray-900 p-8 rounded-3xl shadow text-center">
    <i class="bi bi-cart-check text-4xl"></i>
    <h3 class="text-xl font-semibold mt-4">Easy Ordering</h3>
    <p class="text-gray-600 dark:text-gray-300 mt-2">Smooth & simple UI</p>
  </div>
  <div class="bg-white dark:bg-gray-900 p-8 rounded-3xl shadow text-center">
    <i class="bi bi-truck text-4xl"></i>
    <h3 class="text-xl font-semibold mt-4">Safe Delivery</h3>
    <p class="text-gray-600 dark:text-gray-300 mt-2">Fast & hygienic</p>
  </div>
  <div class="bg-white dark:bg-gray-900 p-8 rounded-3xl shadow text-center">
    <i class="bi bi-award text-4xl"></i>
    <h3 class="text-xl font-semibold mt-4">Best Quality</h3>
    <p class="text-gray-600 dark:text-gray-300 mt-2">Top-rated chefs</p>
  </div>
</section>

<!-- CTA -->
<section class="mt-24 bg-gradient-to-r from-red-500 to-orange-400 text-white text-center py-24">
  <h2 class="text-4xl font-bold">Want to list your restaurant?</h2>
  <p class="mt-4 text-lg">Grow your business with Alimento</p>
  <a href="vendor/vendor_signup.php">
    <button class="mt-8 bg-white text-gray-800 px-8 py-3 rounded-full font-semibold hover:scale-105 transition">
      Register as Vendor
    </button>
  </a>
</section>

<!-- FOOTER -->
<footer class="bg-[#E6E8DD] dark:bg-[#020617] py-14 mt-20">
  <div class="max-w-7xl mx-auto px-5 grid md:grid-cols-3 gap-10">
    <div>
      <img src="./images/logo/logo.webp" class="w-36">
      <p class="text-gray-600 dark:text-gray-400 mt-3">
        Premium homemade food delivery.
      </p>
    </div>
    <div>
      <h4 class="font-semibold">Links</h4>
      <a href="#" class="block hover:underline">Privacy Policy</a>
      <a href="#" class="block hover:underline">Terms</a>
    </div>
    <div>
      <h4 class="font-semibold">Contact</h4>
      <p>hello@alimento.com</p>
      <p>+91 9820223338</p>
    </div>
  </div>
</footer>

</body>
</html>

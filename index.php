<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>College Management System</title>

  <!-- AOS (Scroll Animation) CSS -->
  <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">

  <!-- Google Font + Your Custom CSS -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>
<body>

  <!-- Navigation Bar -->
  <nav>
    <div class="logo">Smart Campus College</div>
    <ul>
      <li><a href="#">Home</a></li>
      <li><a href="#">Students</a></li>
      <li><a href="#">Academics</a></li>
      <li><a href="#">Results</a></li>
      <li><a href="#">Admission</a></li>
      <li><a href="#">Contact</a></li>
      <li><a href="login.php">Login</a></li>
    </ul>
  </nav>

  <!-- Hero Section -->
  <section class="section1">
    <video autoplay muted loop playsinline id="bg-video">
      <source src="images/classroom-bg.mp4" type="video/mp4">
    </video>

    <div class="hero-content" data-aos="fade-up">
      <h1>Welcome to Smart Campus College</h1>
      <p>All-in-one College Management System to simplify academics and records.</p>
      <button onclick="window.location.href='registration.php'">Register Here</button>
    </div>
  </section>

  <!-- About Section -->
  <section class="form_deg" data-aos="fade-right">
    <h2 style="text-align:center; color:#ccc;">About the System</h2>
    <p style="text-align:center; margin-top:10px; line-height:1.6; color:#aaa;">
      SmartCampus+ helps educational institutions manage student data, academic records,
      attendance, and performance reports efficiently — all in one secure digital platform.
    </p>
  </section>

  <!-- Info Cards Section -->
  <div class="container" style="display:flex; flex-wrap:wrap; justify-content:center; gap:30px; margin-bottom:60px;">
    <div class="card" data-aos="fade-up" data-aos-delay="100" style="background:white; padding:30px; width:280px; border-radius:15px; box-shadow:0 4px 10px rgba(0,0,0,0.1); text-align:center; transition:transform 0.3s ease;">
      <img src="images/student.png" alt="Students" style="width:80px; margin-bottom:15px;">
      <h3>Manage Students</h3>
      <p style="color:#666;">Add, edit, and view student profiles with real-time updates and a clean dashboard.</p>
    </div>

    <div class="card" data-aos="fade-up" data-aos-delay="200" style="background:white; padding:30px; width:280px; border-radius:15px; box-shadow:0 4px 10px rgba(0,0,0,0.1); text-align:center; transition:transform 0.3s ease;">
      <img src="images/course.png" alt="Courses" style="width:80px; margin-bottom:15px;">
      <h3>Course Records</h3>
      <p style="color:#666;">Maintain course details, enrollment lists, and manage attendance data easily.</p>
    </div>

    <div class="card" data-aos="fade-up" data-aos-delay="300" style="background:white; padding:30px; width:280px; border-radius:15px; box-shadow:0 4px 10px rgba(0,0,0,0.1); text-align:center; transition:transform 0.3s ease;">
      <img src="images/result.png" alt="Results" style="width:80px; margin-bottom:15px;">
      <h3>Track Performance</h3>
      <p style="color:#666;">Generate and view student marks, internal assessments, and results summary.</p>
    </div>
  </div>

  <!-- Footer -->
  <footer>
    <p>&copy; 2025 SmartCampus+ | All Rights Reserved</p>
	
  </footer>

  <!-- Card hover animation -->
  <script>
    document.querySelectorAll('.card').forEach(card => {
      card.addEventListener('mouseenter', () => card.style.transform = 'translateY(-5px)');
      card.addEventListener('mouseleave', () => card.style.transform = 'translateY(0)');
    });
  </script>

  <!-- AOS Script -->
  <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
  <script>
    AOS.init({
      duration: 1000,
      offset: 100,
      once: true
    });
  </script>

</body>
</html>
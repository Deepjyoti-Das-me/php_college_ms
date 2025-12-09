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
  
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  
  <link rel="stylesheet" href="style.css">
</head>
<body>

  <!-- Navigation Bar -->
  <nav>
    <div class="logo">Smart Campus College</div>
    <ul>
      <li><a href="#home">Home</a></li>
      <li><a href="#students">Students</a></li>
      <li><a href="#academics">Academics</a></li>
      <li><a href="#results">Results</a></li>
      <li><a href="#admission">Admission</a></li>
      <li><a href="#contact">Contact</a></li>
      <li><a href="login.php">Login</a></li>
    </ul>
  </nav>

  <!-- Hero Section -->
  <section class="section1" id="home">
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
    <p style="text-align:center; margin-top:10px; line-height:1.6; color:#aaa; padding: 0 20px;">
      SmartCampus+ helps educational institutions manage student data, academic records,
      attendance, and performance reports efficiently — all in one secure digital platform.
    </p>
  </section>

  <!-- Info Cards Section -->
  <div class="container" style="display:flex; flex-wrap:wrap; justify-content:center; gap:30px; margin:60px auto; padding: 0 20px;">
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

  <!-- Features Section -->
  <section class="features-section" id="students">
    <div class="container" style="max-width:1200px; margin:0 auto; padding:60px 20px;">
      <h2 style="text-align:center; color:#ccc; font-size:36px; margin-bottom:50px;" data-aos="fade-up">Key Features</h2>
      <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(300px, 1fr)); gap:30px;">
        <div class="feature-box" data-aos="fade-up" data-aos-delay="100">
          <div class="feature-icon"><i class="fas fa-calendar-check"></i></div>
          <h3>Attendance Tracking</h3>
          <p style="color:#aaa;">Real-time attendance monitoring with automated reports and notifications for students and faculty.</p>
        </div>
        <div class="feature-box" data-aos="fade-up" data-aos-delay="200">
          <div class="feature-icon"><i class="fas fa-tasks"></i></div>
          <h3>Assignment Management</h3>
          <p style="color:#aaa;">Create, distribute, and grade assignments with deadline tracking and submission management.</p>
        </div>
        <div class="feature-box" data-aos="fade-up" data-aos-delay="300">
          <div class="feature-icon"><i class="fas fa-chart-line"></i></div>
          <h3>Performance Analytics</h3>
          <p style="color:#aaa;">Comprehensive analytics and reports to track student progress and academic performance.</p>
        </div>
        <div class="feature-box" data-aos="fade-up" data-aos-delay="400">
          <div class="feature-icon"><i class="fas fa-bell"></i></div>
          <h3>Notifications</h3>
          <p style="color:#aaa;">Send instant notifications to students, faculty, and staff about important updates and announcements.</p>
        </div>
        <div class="feature-box" data-aos="fade-up" data-aos-delay="500">
          <div class="feature-icon"><i class="fas fa-user-shield"></i></div>
          <h3>Secure Access</h3>
          <p style="color:#aaa;">Role-based access control ensuring data security and privacy for all users.</p>
        </div>
        <div class="feature-box" data-aos="fade-up" data-aos-delay="600">
          <div class="feature-icon"><i class="fas fa-clock"></i></div>
          <h3>Class Schedule</h3>
          <p style="color:#aaa;">Manage class timetables, schedules, and room assignments efficiently.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- User Types Section -->
  <section class="user-types-section" id="academics">
    <div class="container" style="max-width:1200px; margin:0 auto; padding:60px 20px;">
      <h2 style="text-align:center; color:#ccc; font-size:36px; margin-bottom:20px;" data-aos="fade-up">For Everyone</h2>
      <p style="text-align:center; color:#aaa; margin-bottom:50px; font-size:18px;" data-aos="fade-up">Tailored dashboards for different user roles</p>
      <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(250px, 1fr)); gap:30px;">
        <div class="user-card" data-aos="fade-up" data-aos-delay="100">
          <div class="user-icon" style="background:linear-gradient(135deg, #E64A4A, #f44336);">
            <i class="fas fa-user-shield"></i>
          </div>
          <h3>Administrators</h3>
          <p style="color:#aaa;">Complete control over the system. Manage all aspects including users, courses, and settings.</p>
          <a href="login.php" style="display:inline-block; margin-top:15px; color:#f44336; text-decoration:none; font-weight:600;">Admin Login →</a>
        </div>
        <div class="user-card" data-aos="fade-up" data-aos-delay="200">
          <div class="user-icon" style="background:linear-gradient(135deg, #4CAF50, #45a049);">
            <i class="fas fa-user-graduate"></i>
          </div>
          <h3>Students</h3>
          <p style="color:#aaa;">Access your academic information, track attendance, view grades, and submit assignments.</p>
          <a href="login.php" style="display:inline-block; margin-top:15px; color:#4CAF50; text-decoration:none; font-weight:600;">Student Login →</a>
        </div>
        <div class="user-card" data-aos="fade-up" data-aos-delay="300">
          <div class="user-icon" style="background:linear-gradient(135deg, #2196F3, #1976D2);">
            <i class="fas fa-chalkboard-teacher"></i>
          </div>
          <h3>Teachers</h3>
          <p style="color:#aaa;">Manage your classes, take attendance, grade assignments, and track student performance.</p>
          <a href="login.php" style="display:inline-block; margin-top:15px; color:#2196F3; text-decoration:none; font-weight:600;">Teacher Login →</a>
        </div>
        <div class="user-card" data-aos="fade-up" data-aos-delay="400">
          <div class="user-icon" style="background:linear-gradient(135deg, #FF9800, #F57C00);">
            <i class="fas fa-user-tie"></i>
          </div>
          <h3>Staff</h3>
          <p style="color:#aaa;">Manage administrative tasks, handle reports, and support college operations efficiently.</p>
          <a href="login.php" style="display:inline-block; margin-top:15px; color:#FF9800; text-decoration:none; font-weight:600;">Staff Login →</a>
        </div>
      </div>
    </div>
  </section>

  <!-- Statistics Section -->
  <section class="stats-section" id="results">
    <div class="container" style="max-width:1200px; margin:0 auto; padding:60px 20px;">
      <h2 style="text-align:center; color:#ccc; font-size:36px; margin-bottom:50px;" data-aos="fade-up">Our Achievements</h2>
      <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:30px;">
        <div class="stat-box" data-aos="fade-up" data-aos-delay="100">
          <div class="stat-number">10+</div>
          <div class="stat-label">Years Experience</div>
        </div>
        <div class="stat-box" data-aos="fade-up" data-aos-delay="200">
          <div class="stat-number">500+</div>
          <div class="stat-label">Active Students</div>
        </div>
        <div class="stat-box" data-aos="fade-up" data-aos-delay="300">
          <div class="stat-number">50+</div>
          <div class="stat-label">Faculty Members</div>
        </div>
        <div class="stat-box" data-aos="fade-up" data-aos-delay="400">
          <div class="stat-number">99%</div>
          <div class="stat-label">Satisfaction Rate</div>
        </div>
      </div>
    </div>
  </section>

  <!-- Contact Section -->
  <section class="contact-section" id="contact">
    <div class="container" style="max-width:1200px; margin:0 auto; padding:60px 20px;">
      <h2 style="text-align:center; color:#ccc; font-size:36px; margin-bottom:50px;" data-aos="fade-up">Get In Touch</h2>
      <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(300px, 1fr)); gap:40px;">
        <div class="contact-box" data-aos="fade-right">
          <div class="contact-icon"><i class="fas fa-map-marker-alt"></i></div>
          <h3>Address</h3>
          <p style="color:#aaa;">123 Education Street<br>Campus City, CC 12345</p>
        </div>
        <div class="contact-box" data-aos="fade-up">
          <div class="contact-icon"><i class="fas fa-phone"></i></div>
          <h3>Phone</h3>
          <p style="color:#aaa;">+1 (555) 123-4567<br>+1 (555) 123-4568</p>
        </div>
        <div class="contact-box" data-aos="fade-left">
          <div class="contact-icon"><i class="fas fa-envelope"></i></div>
          <h3>Email</h3>
          <p style="color:#aaa;">info@smartcampus.edu<br>support@smartcampus.edu</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer>
    <p>&copy; 2025 SmartCampus+ | All Rights Reserved</p>
  </footer>

  <!-- Deep AI Chatbot - Floating Button -->
  <div id="deep-ai-helper" class="deep-ai-helper">
    <!-- Floating Button -->
    <button class="deep-ai-toggle" id="deepAiToggle" title="Deep AI Assistant">
      <i class="fas fa-robot"></i>
    </button>
    
    <!-- Chat Window -->
    <div class="deep-ai-window" id="deepAiWindow">
      <div class="deep-ai-header">
        <div class="deep-ai-header-content">
          <div class="deep-ai-title-section">
            <div class="deep-ai-avatar-small">
              <i class="fas fa-brain"></i>
            </div>
            <div class="deep-ai-title-text">
              <div class="deep-ai-title-main">Deep AI</div>
              <div class="deep-ai-title-sub">Smart Campus Assistant</div>
            </div>
          </div>
          <button class="deep-ai-close" id="deepAiClose" title="Close">
            <i class="fas fa-times"></i>
          </button>
        </div>
        <div class="deep-ai-quick-actions">
          <button class="quick-action-btn" onclick="window.location.href='registration.php'">
            <i class="fas fa-user-plus"></i> Register
          </button>
          <button class="quick-action-btn" onclick="window.location.href='login.php'">
            <i class="fas fa-sign-in-alt"></i> Login
          </button>
        </div>
      </div>
      
      <div class="deep-ai-messages" id="deepAiMessages">
        <div class="deep-ai-message deep-ai-bot">
          <div class="deep-ai-avatar">
            <i class="fas fa-robot"></i>
          </div>
          <div class="deep-ai-text">
            <p><strong>Welcome to College Management System!</strong></p>
            <p style="margin-top: 8px;">I'm Deep AI, your smart campus assistant. I can help you with:</p>
            <ul style="margin-top: 8px; padding-left: 20px; font-size: 13px; line-height: 1.8;">
              <li>Registration and login assistance</li>
              <li>Information about system features</li>
              <li>Student, Teacher, Staff, and Admin functions</li>
              <li>General queries about the platform</li>
            </ul>
            <p style="margin-top: 10px; font-size: 12px; opacity: 0.8;">Type your question below or use the quick action buttons above.</p>
          </div>
        </div>
      </div>
      
      <div class="deep-ai-input">
        <div class="deep-ai-input-wrapper">
          <input type="text" id="deepAiInput" placeholder="Type your question here..." autocomplete="off">
          <button class="deep-ai-mic-btn" id="deepAiMic" title="Voice Input">
            <i class="fas fa-microphone"></i>
          </button>
          <button class="deep-ai-send-btn" id="deepAiSend" title="Send">
            <i class="fas fa-paper-plane"></i>
          </button>
        </div>
      </div>
      
      <div class="deep-ai-footer">
        <a href="#" class="deep-ai-footer-link">Terms of Use</a>
        <span class="deep-ai-footer-separator">|</span>
        <a href="#" class="deep-ai-footer-link">Privacy Policy</a>
        <span class="deep-ai-footer-separator">|</span>
        <a href="#" class="deep-ai-footer-link">Help & Support</a>
      </div>
    </div>
  </div>

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

  <!-- Deep AI Chatbot Script -->
  <script>
    const deepAiToggle = document.getElementById('deepAiToggle');
    const deepAiWindow = document.getElementById('deepAiWindow');
    const deepAiClose = document.getElementById('deepAiClose');
    const deepAiInput = document.getElementById('deepAiInput');
    const deepAiSend = document.getElementById('deepAiSend');
    const deepAiMic = document.getElementById('deepAiMic');
    const deepAiMessages = document.getElementById('deepAiMessages');

    // Toggle chat window
    deepAiToggle.addEventListener('click', () => {
      deepAiWindow.classList.toggle('active');
      if (deepAiWindow.classList.contains('active')) {
        deepAiInput.focus();
      }
    });

    deepAiClose.addEventListener('click', () => {
      deepAiWindow.classList.remove('active');
    });

    // Close on outside click (optional)
    document.addEventListener('click', (e) => {
      if (!deepAiWindow.contains(e.target) && !deepAiToggle.contains(e.target)) {
        if (deepAiWindow.classList.contains('active')) {
          // Uncomment to close on outside click
          // deepAiWindow.classList.remove('active');
        }
      }
    });

    // AI responses database
    const deepAiResponses = {
      'hello': 'Hello! How can I help you with the College Management System today?',
      'hi': 'Hi there! I\'m Deep AI, your smart campus assistant. What would you like to know?',
      'help': 'I can help you with:\n• Registration process\n• Login issues\n• Using the dashboard\n• Student/Teacher/Staff features\n• General questions about the system',
      'registration': 'To register:\n1. Click "Register Here" button on homepage\n2. Fill in your personal details\n3. Select your user type (Student/Teacher/Staff)\n4. Complete the CAPTCHA\n5. Submit the form\n\nNote: Admin accounts are created by administrators only.',
      'login': 'To login:\n1. Go to the Login page\n2. Select your user type (Admin/Student/Teacher/Staff)\n3. Enter your email and password\n4. Click Login\n\nMake sure you select the correct user type matching your account.',
      'student': 'Student features include:\n• View attendance records\n• Check grades and results\n• Submit assignments\n• Request leave\n• View class schedule\n• Give feedback\n• View notifications',
      'teacher': 'Teacher features include:\n• Take attendance for classes\n• Create and manage assignments\n• Enter and manage grades\n• View assigned classes\n• View students\n• View class schedule\n• View notifications',
      'admin': 'Admin features include:\n• Manage students, teachers, and staff\n• Add/Edit/Delete users\n• Manage courses and subjects\n• View attendance statistics\n• Send notifications\n• Manage leave requests\n• Manage feedback\n• Update profile',
      'staff': 'Staff features include:\n• View profile\n• Request leave\n• Submit feedback\n• View notifications',
      'attendance': 'Attendance can be tracked by:\n• Teachers can mark attendance for their classes\n• Students can view their attendance records\n• Admins can view overall attendance statistics\n\nAttendance is recorded by subject and date.',
      'default': 'I\'m Deep AI, your smart campus assistant! I can help you with:\n• Registration and login\n• How to use different features\n• Student, Teacher, Staff, or Admin functions\n• General questions about the system\n\nTry asking: "How do I register?" or "What can students do?" or "Tell me about teacher features"'
    };

    function addDeepAiMessage(text, isUser = false) {
      const messageDiv = document.createElement('div');
      messageDiv.className = `deep-ai-message ${isUser ? 'deep-ai-user' : 'deep-ai-bot'}`;
      
      const avatar = document.createElement('div');
      avatar.className = 'deep-ai-avatar';
      avatar.innerHTML = `<i class="fas ${isUser ? 'fa-user' : 'fa-robot'}"></i>`;
      
      const textDiv = document.createElement('div');
      textDiv.className = 'deep-ai-text';
      textDiv.innerHTML = `<p>${text.replace(/\n/g, '<br>')}</p>`;
      
      messageDiv.appendChild(avatar);
      messageDiv.appendChild(textDiv);
      deepAiMessages.appendChild(messageDiv);
      deepAiMessages.scrollTop = deepAiMessages.scrollHeight;
    }

    function showDeepAiTyping() {
      const typingDiv = document.createElement('div');
      typingDiv.className = 'deep-ai-message deep-ai-bot';
      typingDiv.id = 'deep-ai-typing-indicator';
      
      const avatar = document.createElement('div');
      avatar.className = 'deep-ai-avatar';
      avatar.innerHTML = '<i class="fas fa-robot"></i>';
      
      const typing = document.createElement('div');
      typing.className = 'deep-ai-text deep-ai-typing';
      typing.innerHTML = '<span></span><span></span><span></span>';
      
      typingDiv.appendChild(avatar);
      typingDiv.appendChild(typing);
      deepAiMessages.appendChild(typingDiv);
      deepAiMessages.scrollTop = deepAiMessages.scrollHeight;
    }

    function removeDeepAiTyping() {
      const typing = document.getElementById('deep-ai-typing-indicator');
      if (typing) typing.remove();
    }

    function getDeepAiResponse(userMessage) {
      const lowerMessage = userMessage.toLowerCase().trim();
      
      // Check for keywords
      for (const [key, response] of Object.entries(deepAiResponses)) {
        if (lowerMessage.includes(key) && key !== 'default') {
          return response;
        }
      }
      
      // Check for specific patterns
      if (lowerMessage.includes('register') || lowerMessage.includes('sign up') || lowerMessage.includes('registration')) {
        return deepAiResponses['registration'];
      }
      if (lowerMessage.includes('log in') || lowerMessage.includes('sign in') || lowerMessage.includes('login')) {
        return deepAiResponses['login'];
      }
      if (lowerMessage.includes('student')) {
        return deepAiResponses['student'];
      }
      if (lowerMessage.includes('teacher')) {
        return deepAiResponses['teacher'];
      }
      if (lowerMessage.includes('admin')) {
        return deepAiResponses['admin'];
      }
      if (lowerMessage.includes('staff')) {
        return deepAiResponses['staff'];
      }
      if (lowerMessage.includes('attendance')) {
        return deepAiResponses['attendance'];
      }
      
      return deepAiResponses['default'];
    }

    function sendDeepAiMessage() {
      const message = deepAiInput.value.trim();
      if (!message) return;
      
      // Add user message
      addDeepAiMessage(message, true);
      deepAiInput.value = '';
      
      // Show typing indicator
      showDeepAiTyping();
      
      // Simulate AI thinking
      setTimeout(() => {
        removeDeepAiTyping();
        const response = getDeepAiResponse(message);
        addDeepAiMessage(response);
      }, 800);
    }

    // Send message on Enter key
    deepAiInput.addEventListener('keypress', (e) => {
      if (e.key === 'Enter') {
        sendDeepAiMessage();
      }
    });

    // Send button click
    deepAiSend.addEventListener('click', sendDeepAiMessage);

    // Microphone button (for future voice input)
    deepAiMic.addEventListener('click', () => {
      // Voice input functionality can be added here
      addDeepAiMessage('Voice input feature coming soon! For now, please type your question.', false);
    });
  </script>

</body>
</html>

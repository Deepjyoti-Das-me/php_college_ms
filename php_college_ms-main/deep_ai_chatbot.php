<!-- Deep AI Chatbot - Floating Button (Include this file on all pages) -->
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
        <?php 
        // Determine base path based on current directory
        $basePath = (strpos($_SERVER['PHP_SELF'], '/admin/') !== false || 
                     strpos($_SERVER['PHP_SELF'], '/student/') !== false || 
                     strpos($_SERVER['PHP_SELF'], '/teacher/') !== false || 
                     strpos($_SERVER['PHP_SELF'], '/staff/') !== false) ? '../' : '';
        ?>
        <button class="quick-action-btn" onclick="window.location.href='<?php echo $basePath; ?>registration.php'">
          <i class="fas fa-user-plus"></i> Register
        </button>
        <button class="quick-action-btn" onclick="window.location.href='<?php echo $basePath; ?>login.php'">
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

<script>
  // Deep AI Chatbot Script
  (function() {
    const deepAiToggle = document.getElementById('deepAiToggle');
    const deepAiWindow = document.getElementById('deepAiWindow');
    const deepAiClose = document.getElementById('deepAiClose');
    const deepAiInput = document.getElementById('deepAiInput');
    const deepAiSend = document.getElementById('deepAiSend');
    const deepAiMic = document.getElementById('deepAiMic');
    const deepAiMessages = document.getElementById('deepAiMessages');

    if (!deepAiToggle || !deepAiWindow) return; // Exit if elements don't exist

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

    // Microphone button
    deepAiMic.addEventListener('click', () => {
      addDeepAiMessage('Voice input feature coming soon! For now, please type your question.', false);
    });
  })();
</script>


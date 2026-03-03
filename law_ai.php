<?php require_once 'lib/db.php'; requireAuth(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Legal Assistant - Law Connectors</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {margin: 0; padding: 0; box-sizing: border-box;}
        body {font-family: 'Inter', sans-serif; background: #fff; color: #0a0a0a;}
        
        /* Navbar */
        .navbar {background: #0a0a0a; padding: 15px 0; position: sticky; top: 0; z-index: 100;}
        .nav-container {max-width: 1200px; margin: 0 auto; padding: 0 20px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;}
        .logo {font-family: 'Playfair Display', serif; font-size: 24px; color: #fff; font-weight: 700; text-decoration: none;}
        .nav-links {display: flex; gap: 0; align-items: center; flex-wrap: wrap;}
        .nav-links a {color: #fff; text-decoration: none; padding: 10px 20px; font-size: 14px; font-weight: 500; transition: all 0.2s; border-radius: 2px;}
        .nav-links a:hover {background: #1a1a1a;}
        .nav-links a.active {background: #fff; color: #0a0a0a;}
        
        /* Container */
        .container {max-width: 1100px; margin: 0 auto; padding: 40px 20px;}
        
        /* Chat Container */
        .chat-container {background: #fafafa; border-radius: 4px; height: calc(100vh - 180px); min-height: 600px; display: flex; flex-direction: column; border: 1px solid #e8e8e4; overflow: hidden;}
        .chat-header {padding: 25px 30px; border-bottom: 1px solid #e8e8e4; background: #0a0a0a; color: #fff;}
        .chat-header h2 {font-family: 'Playfair Display', serif; font-size: 24px; display: flex; align-items: center; gap: 12px; margin-bottom: 8px;}
        .chat-header p {font-size: 13px; color: #888; font-weight: 400;}
        
        /* Messages */
        .chat-messages {flex: 1; padding: 30px; overflow-y: auto; background: #fff;}
        .message {margin-bottom: 25px; display: flex; gap: 15px; animation: fadeIn 0.3s ease;}
        @keyframes fadeIn {from {opacity: 0; transform: translateY(15px);} to {opacity: 1; transform: translateY(0);}}
        .message-avatar {width: 42px; height: 42px; border-radius: 2px; display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0;}
        .user-avatar {background: #0a0a0a; color: #fff;}
        .ai-avatar {background: #f5f5f3; color: #0a0a0a; border: 1px solid #e8e8e4;}
        .message-content {flex: 1; max-width: 75%;}
        .message-content strong {color: #0a0a0a; margin-bottom: 8px; display: block; font-weight: 600; font-size: 14px;}
        .message-content p {color: #333; line-height: 1.7; white-space: pre-wrap; font-size: 14.5px;}
        
        /* Input */
        .chat-input {padding: 25px 30px; border-top: 1px solid #e8e8e4; background: #fafafa; display: flex; gap: 12px;}
        .chat-input textarea {flex: 1; padding: 14px 16px; border: 1px solid #ddd; border-radius: 2px; resize: none; font-family: 'Inter', sans-serif; font-size: 14px; transition: border 0.2s;}
        .chat-input textarea:focus {outline: none; border-color: #0a0a0a;}
        .chat-input button {padding: 14px 30px; background: #0a0a0a; color: #fff; border: none; border-radius: 2px; cursor: pointer; font-weight: 600; transition: all 0.2s; font-size: 14px; display: inline-flex; align-items: center; gap: 8px;}
        .chat-input button:hover {background: #1a1a1a;}
        .chat-input button:disabled {background: #ddd; cursor: not-allowed; color: #888;}
        
        /* Welcome */
        .welcome-message {text-align: center; padding: 60px 30px; color: #888;}
        .welcome-message i {font-size: 72px; color: #0a0a0a; margin-bottom: 25px; opacity: 0.9;}
        .welcome-message h3 {font-family: 'Playfair Display', serif; color: #0a0a0a; margin-bottom: 12px; font-size: 28px;}
        .welcome-message p {font-size: 15px; line-height: 1.6;}
        
        /* Typing Indicator */
        .typing-indicator {display: none; padding: 15px 18px; background: #f5f5f3; border-radius: 2px; width: fit-content; border: 1px solid #e8e8e4;}
        .typing-indicator span {display: inline-block; width: 7px; height: 7px; border-radius: 50%; background: #0a0a0a; margin: 0 2px; animation: typing 1.4s infinite;}
        .typing-indicator span:nth-child(2) {animation-delay: 0.2s;}
        .typing-indicator span:nth-child(3) {animation-delay: 0.4s;}
        @keyframes typing {0%, 60%, 100% {transform: translateY(0); opacity: 0.3;} 30% {transform: translateY(-8px); opacity: 1;}}
        
        /* Sample Questions */
        .sample-questions {display: flex; flex-wrap: wrap; gap: 10px; justify-content: center; margin-top: 25px;}
        .sample-btn {padding: 10px 18px; background: #f5f5f3; color: #0a0a0a; border: 1px solid #e8e8e4; border-radius: 2px; cursor: pointer; font-size: 13px; font-weight: 500; transition: all 0.2s; font-family: 'Inter', sans-serif;}
        .sample-btn:hover {background: #0a0a0a; color: #fff; border-color: #0a0a0a;}
        
        @media (max-width: 768px) {
            .chat-container {height: calc(100vh - 140px); min-height: 500px;}
            .message-content {max-width: 85%;}
            .welcome-message {padding: 40px 20px;}
            .welcome-message i {font-size: 56px;}
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="nav-container">
            <a href="mainhome.php" class="logo">Law Connectors</a>
            <div class="nav-links">
                <a href="mainhome.php"><i class="fas fa-home"></i> Dashboard</a>
                <a href="experts.php"><i class="fas fa-users"></i> Experts</a>
                <a href="sessions.php"><i class="fas fa-calendar"></i> Sessions</a>
                <a href="law_ai.php" class="active"><i class="fas fa-robot"></i> AI Assistant</a>
                <a href="wallet.php"><i class="fas fa-wallet"></i> Wallet</a>
                <a href="lib/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>
    </nav>

    <!-- Main Container -->
    <div class="container">
        <div class="chat-container">
            <div class="chat-header">
                <h2><i class="fas fa-scale-balanced"></i> AI Legal Assistant</h2>
                <p>Get instant answers to your legal questions. Available 24/7. For complex matters, consult our expert lawyers.</p>
            </div>
            
            <div class="chat-messages" id="chatMessages">
                <div class="welcome-message">
                    <i class="fas fa-balance-scale"></i>
                    <h3>Welcome to Law AI Assistant!</h3>
                    <p>I'm here to help you with legal information and guidance.</p>
                    <p style="margin-top: 10px;">Ask me anything about Indian law, legal procedures, or your rights.</p>
                </div>
            </div>
            
            <div class="chat-input">
                <textarea id="messageInput" placeholder="Ask your legal question here..." rows="2"></textarea>
                <button onclick="sendMessage()" id="sendBtn"><i class="fas fa-paper-plane"></i> Send</button>
            </div>
        </div>
    </div>

    <script>
        let isProcessing = false;

        function addMessage(content, isUser = false) {
            const chatMessages = document.getElementById('chatMessages');
            const welcomeMsg = chatMessages.querySelector('.welcome-message');
            if (welcomeMsg) welcomeMsg.remove();

            const messageDiv = document.createElement('div');
            messageDiv.className = 'message';
            
            const avatarDiv = document.createElement('div');
            avatarDiv.className = isUser ? 'message-avatar user-avatar' : 'message-avatar ai-avatar';
            avatarDiv.innerHTML = isUser ? '<i class="fas fa-user"></i>' : '<i class="fas fa-robot"></i>';
            
            const contentDiv = document.createElement('div');
            contentDiv.className = 'message-content';
            contentDiv.innerHTML = `<strong>${isUser ? 'You' : 'Law AI Assistant'}</strong><p>${content}</p>`;
            
            messageDiv.appendChild(avatarDiv);
            messageDiv.appendChild(contentDiv);
            chatMessages.appendChild(messageDiv);
            
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }

        function showTyping() {
            const chatMessages = document.getElementById('chatMessages');
            const typingDiv = document.createElement('div');
            typingDiv.id = 'typingIndicator';
            typingDiv.className = 'message';
            typingDiv.innerHTML = `
                <div class="message-avatar ai-avatar"><i class="fas fa-robot"></i></div>
                <div class="typing-indicator" style="display: block;">
                    <span></span><span></span><span></span>
                </div>
            `;
            chatMessages.appendChild(typingDiv);
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }

        function hideTyping() {
            const typing = document.getElementById('typingIndicator');
            if (typing) typing.remove();
        }

        async function sendMessage() {
            if (isProcessing) return;

            const input = document.getElementById('messageInput');
            const message = input.value.trim();
            
            if (!message) return;

            isProcessing = true;
            document.getElementById('sendBtn').disabled = true;
            
            // Add user message
            addMessage(message, true);
            input.value = '';
            
            // Show typing indicator
            showTyping();

            try {
                const response = await fetch('lib/ai/ai_chat.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({message: message})
                });

                const result = await response.json();

                hideTyping();

                if (result.error) {
                    addMessage('Sorry, I encountered an error. Please try again.', false);
                } else {
                    addMessage(result.data.response, false);
                }
            } catch (error) {
                hideTyping();
                addMessage('Sorry, I\'m having trouble connecting. Please try again later.', false);
            }

            isProcessing = false;
            document.getElementById('sendBtn').disabled = false;
            input.focus();
        }

        // Allow Enter to send (Shift+Enter for new line)
        document.getElementById('messageInput').addEventListener('keydown', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });

        // Add sample questions on load
        window.addEventListener('load', () => {
            const chatMessages = document.getElementById('chatMessages');
            const welcomeMsg = chatMessages.querySelector('.welcome-message');
            
            const samplesDiv = document.createElement('div');
            samplesDiv.innerHTML = `
                <div style="margin-top: 30px;">
                    <p style="color: #888; margin-bottom: 20px; font-size: 14px;">Try asking:</p>
                    <div class="sample-questions">
                        <button onclick="askSample('How do I file for divorce in India?')" class="sample-btn">Divorce process in India</button>
                        <button onclick="askSample('What are my rights as a tenant?')" class="sample-btn">Tenant rights & laws</button>
                        <button onclick="askSample('How to file an FIR?')" class="sample-btn">File FIR procedure</button>
                        <button onclick="askSample('What is a legal notice?')" class="sample-btn">Legal notice guide</button>
                        <button onclick="askSample('Property dispute resolution')" class="sample-btn">Property disputes</button>
                        <button onclick="askSample('Consumer rights in India')" class="sample-btn">Consumer protection</button>
                    </div>
                </div>
            `;
            welcomeMsg.appendChild(samplesDiv);
        });

        function askSample(question) {
            document.getElementById('messageInput').value = question;
            sendMessage();
        }
    </script>
</body>
</html>

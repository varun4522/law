<?php require_once 'lib/db.php'; requireAuth(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Law AI Assistant - Law Connectors</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {margin: 0; padding: 0; box-sizing: border-box;}
        body {font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; padding: 20px;}
        .container {max-width: 1000px; margin: 0 auto;}
        .header {background: white; padding: 20px 30px; border-radius: 15px; margin-bottom: 30px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;}
        .logo {font-size: 24px; font-weight: bold; color: #667eea; display: flex; align-items: center; gap: 10px;}
        .nav-buttons {display: flex; gap: 10px; flex-wrap: wrap;}
        .btn {padding: 10px 20px; border: none; border-radius: 8px; cursor: pointer; font-size: 14px; font-weight: 600; transition: all 0.3s; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;}
        .btn-secondary {background: #f0f0f0; color: #333;}
        
        .chat-container {background: white; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); height: 600px; display: flex; flex-direction: column;}
        .chat-header {padding: 20px 30px; border-bottom: 2px solid #f0f0f0; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 15px 15px 0 0;}
        .chat-header h2 {font-size: 20px; display: flex; align-items: center; gap: 10px;}
        .chat-messages {flex: 1; padding: 20px; overflow-y: auto; background: #f8f9fa;}
        .message {margin-bottom: 20px; display: flex; gap: 15px; animation: fadeIn 0.3s;}
        @keyframes fadeIn {from {opacity: 0; transform: translateY(10px);} to {opacity: 1; transform: translateY(0);}}
        .message-avatar {width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0;}
        .user-avatar {background: #667eea; color: white;}
        .ai-avatar {background: #28a745; color: white;}
        .message-content {flex: 1; background: white; padding: 15px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);}
        .message-content strong {color: #667eea; margin-bottom: 5px; display: block;}
        .message-content p {color: #333; line-height: 1.6; white-space: pre-wrap;}
        .chat-input {padding: 20px; border-top: 2px solid #f0f0f0; display: flex; gap: 10px;}
        .chat-input textarea {flex: 1; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; resize: none; font-family: inherit; font-size: 14px;}
        .chat-input button {padding: 12px 25px; background: #667eea; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; transition: all 0.3s;}
        .chat-input button:hover {background: #5568d3;}
        .chat-input button:disabled {background: #ccc; cursor: not-allowed;}
        
        .welcome-message {text-align: center; padding: 40px 20px; color: #999;}
        .welcome-message i {font-size: 64px; color: #667eea; margin-bottom: 20px;}
        .welcome-message h3 {color: #333; margin-bottom: 10px;}
        
        .typing-indicator {display: none; padding: 15px; background: white; border-radius: 10px; width: fit-content; box-shadow: 0 2px 5px rgba(0,0,0,0.1);}
        .typing-indicator span {display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: #667eea; margin: 0 2px; animation: typing 1.4s infinite;}
        .typing-indicator span:nth-child(2) {animation-delay: 0.2s;}
        .typing-indicator span:nth-child(3) {animation-delay: 0.4s;}
        @keyframes typing {0%, 60%, 100% {transform: translateY(0);} 30% {transform: translateY(-10px);}}
        
        @media (max-width: 768px) {.chat-container {height: calc(100vh - 200px);}}
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo"><i class="fas fa-robot"></i> Law AI Assistant</div>
            <div class="nav-buttons">
                <a href="mainhome.php" class="btn btn-secondary"><i class="fas fa-home"></i> Dashboard</a>
                <a href="experts.php" class="btn btn-secondary"><i class="fas fa-users"></i> Talk to Real Expert</a>
            </div>
        </div>

        <div class="chat-container">
            <div class="chat-header">
                <h2><i class="fas fa-gavel"></i> Free Legal AI Assistant - 24/7 Available</h2>
                <p style="font-size: 13px; margin-top: 5px; opacity: 0.9;">Get instant answers to your legal questions. For complex matters, consult with our expert lawyers.</p>
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
                const response = await fetch('lib/ai_chat.php', {
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

        // Add some sample questions on load
        window.addEventListener('load', () => {
            const chatMessages = document.getElementById('chatMessages');
            const welcomeMsg = chatMessages.querySelector('.welcome-message');
            
            const samplesDiv = document.createElement('div');
            samplesDiv.innerHTML = `
                <div style="text-align: center; margin-top: 20px;">
                    <p style="color: #666; margin-bottom: 15px; font-size: 14px;">Try asking:</p>
                    <div style="display: flex; flex-wrap: wrap; gap: 10px; justify-content: center;">
                        <button onclick="askSample('How do I file for divorce in India?')" class="btn btn-secondary" style="font-size: 12px; padding: 8px 15px;">Divorce process</button>
                        <button onclick="askSample('What are my rights as a tenant?')" class="btn btn-secondary" style="font-size: 12px; padding: 8px 15px;">Tenant rights</button>
                        <button onclick="askSample('How to file an FIR?')" class="btn btn-secondary" style="font-size: 12px; padding: 8px 15px;">File FIR</button>
                        <button onclick="askSample('What is a legal notice?')" class="btn btn-secondary" style="font-size: 12px; padding: 8px 15px;">Legal notice</button>
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

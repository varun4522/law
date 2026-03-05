<?php require_once __DIR__ . '/../lib/db.php'; requireAuth(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Legal Assistant - Law Connectors</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@600;700&family=Dancing+Script:wght@500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        *{margin:0;padding:0;box-sizing:border-box;}
        body{font-family:'Inter',sans-serif;background:#fafafa;min-height:100vh;color:#0a0a0a;padding-bottom:90px;}
        .navbar{background:#fff;border-bottom:1px solid #e8e8e4;position:sticky;top:0;z-index:100;box-shadow:0 2px 8px rgba(0,0,0,0.04);}
        .navbar-container{max-width:1400px;margin:0 auto;padding:0 24px;display:flex;justify-content:space-between;align-items:center;height:70px;}
        .logo{font-family:'Playfair Display',serif;font-size:22px;font-weight:700;color:#0a0a0a;display:flex;align-items:center;gap:10px;text-decoration:none;}
        .logout-btn{padding:8px 16px;background:#fff;color:#666;border:1.5px solid #ddd;border-radius:2px;cursor:pointer;font-weight:600;font-size:14px;transition:all 0.2s;display:inline-flex;align-items:center;gap:6px;}
        .logout-btn:hover{background:#0a0a0a;color:white;border-color:#0a0a0a;}
        .nav-right{display:flex;align-items:center;gap:16px;}

        .page-container{max-width:900px;margin:0 auto;padding:32px 24px;}
        .page-header{background:#0a0a0a;border-radius:4px;padding:40px 40px 36px;margin-bottom:32px;color:#fff;position:relative;overflow:hidden;}
        .page-header::before{content:'';position:absolute;top:-50px;right:-50px;width:220px;height:220px;background:rgba(255,255,255,0.04);border-radius:50%;}
        .page-header h1{font-family:'Playfair Display',serif;font-size:30px;font-weight:700;margin-bottom:8px;position:relative;}
        .page-header p{font-size:15px;opacity:0.7;position:relative;max-width:480px;}
        .page-header .header-icon{font-size:40px;margin-bottom:16px;display:block;position:relative;}

        /* Quick prompts */
        .quick-prompts{display:flex;flex-wrap:wrap;gap:10px;margin-bottom:24px;}
        .prompt-chip{padding:8px 16px;background:#fff;border:1.5px solid #e8e8e4;border-radius:20px;font-size:13px;font-weight:500;cursor:pointer;transition:all 0.2s;color:#444;}
        .prompt-chip:hover{border-color:#0a0a0a;color:#0a0a0a;background:#f5f5f3;}

        /* Chat window */
        .chat-window{background:#fff;border:1px solid #e8e8e4;border-radius:4px;display:flex;flex-direction:column;height:500px;}
        .chat-messages{flex:1;overflow-y:auto;padding:24px;display:flex;flex-direction:column;gap:16px;}
        .chat-messages::-webkit-scrollbar{width:4px;}
        .chat-messages::-webkit-scrollbar-thumb{background:#ddd;border-radius:2px;}
        .msg{display:flex;gap:12px;align-items:flex-start;max-width:85%;}
        .msg.user{align-self:flex-end;flex-direction:row-reverse;}
        .msg-avatar{width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:16px;flex-shrink:0;}
        .msg.ai .msg-avatar{background:#0a0a0a;color:#fff;}
        .msg.user .msg-avatar{background:#f0f0f0;color:#0a0a0a;}
        .msg-bubble{padding:13px 16px;border-radius:4px;font-size:14px;line-height:1.65;}
        .msg.ai .msg-bubble{background:#f5f5f3;color:#0a0a0a;border-radius:0 4px 4px 4px;}
        .msg.user .msg-bubble{background:#0a0a0a;color:#fff;border-radius:4px 0 4px 4px;}
        .msg-time{font-size:11px;color:#bbb;margin-top:4px;}
        .msg.user .msg-time{text-align:right;}
        .typing-indicator{display:none;gap:6px;align-items:center;padding:13px 16px;background:#f5f5f3;border-radius:0 4px 4px 4px;width:fit-content;}
        .typing-dot{width:8px;height:8px;background:#888;border-radius:50%;animation:typingBounce 1.2s infinite;}
        .typing-dot:nth-child(2){animation-delay:0.2s;}
        .typing-dot:nth-child(3){animation-delay:0.4s;}
        @keyframes typingBounce{0%,60%,100%{transform:translateY(0);}30%{transform:translateY(-8px);}}

        /* Chat input */
        .chat-input-area{border-top:1px solid #e8e8e4;padding:16px 20px;display:flex;gap:12px;align-items:center;}
        .chat-input{flex:1;padding:12px 16px;border:1.5px solid #e8e8e4;border-radius:4px;font-size:14px;font-family:'Inter',sans-serif;resize:none;outline:none;transition:border-color 0.2s;max-height:100px;}
        .chat-input:focus{border-color:#0a0a0a;}
        .send-btn{width:44px;height:44px;background:#0a0a0a;color:#fff;border:none;border-radius:4px;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:18px;transition:all 0.2s;flex-shrink:0;}
        .send-btn:hover{background:#333;}
        .send-btn:disabled{background:#ccc;cursor:not-allowed;}

        /* Disclaimer */
        .disclaimer{margin-top:16px;padding:14px 18px;background:#fffbeb;border-left:3px solid #f59e0b;border-radius:4px;font-size:13px;color:#92400e;}

        /* Bottom Nav */
        .bottom-nav{position:fixed;bottom:0;left:0;right:0;background:#fff;border-top:1px solid #e8e8e4;padding:12px 0;z-index:1000;box-shadow:0 -4px 12px rgba(0,0,0,0.08);}
        .bottom-nav-container{max-width:600px;margin:0 auto;display:flex;justify-content:space-around;align-items:center;position:relative;padding:0 20px;}
        .nav-item{display:flex;flex-direction:column;align-items:center;gap:4px;text-decoration:none;color:#888;transition:all 0.2s;padding:8px 12px;border-radius:4px;min-width:70px;}
        .nav-item i{font-size:22px;}
        .nav-item span{font-size:11px;font-weight:500;}
        .nav-item:hover,.nav-item.active{color:#0a0a0a;}
        .nav-item.center-ai{position:relative;top:-20px;background:#0a0a0a;color:#fff;width:70px;height:70px;border-radius:50%;box-shadow:0 8px 20px rgba(0,0,0,0.2);padding:0;min-width:unset;border:4px solid #fff;}
        .nav-item.center-ai i{font-size:28px;}
        .nav-item.center-ai span{position:absolute;bottom:-20px;left:50%;transform:translateX(-50%);font-size:10px;white-space:nowrap;color:#0a0a0a;font-weight:600;}
        @media(min-width:769px){.bottom-nav{display:none;}body{padding-bottom:0;}}
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="navbar-container">
            <a href="mainhome.php" class="logo"><i class="fas fa-balance-scale"></i> Law Connectors</a>
            <div class="nav-right">
                <span id="userName" style="font-weight:500;font-size:14px;color:#444;"></span>
                <button class="logout-btn" onclick="logout()"><i class="fas fa-sign-out-alt"></i> Logout</button>
            </div>
        </div>
    </nav>

    <div class="page-container">
        <div class="page-header">
            <span class="header-icon">🤖</span>
            <h1>AI Legal Assistant</h1>
            <p>Ask any legal question and get instant, informative answers powered by AI.</p>
        </div>

        <div class="quick-prompts">
            <span class="prompt-chip" onclick="sendPrompt(this)">What are my tenant rights?</span>
            <span class="prompt-chip" onclick="sendPrompt(this)">How to file an FIR?</span>
            <span class="prompt-chip" onclick="sendPrompt(this)">Explain IPC Section 420</span>
            <span class="prompt-chip" onclick="sendPrompt(this)">What is bail and how to get it?</span>
            <span class="prompt-chip" onclick="sendPrompt(this)">How to file for divorce in India?</span>
            <span class="prompt-chip" onclick="sendPrompt(this)">Rights of an arrested person</span>
            <span class="prompt-chip" onclick="sendPrompt(this)">What is a legal notice?</span>
        </div>

        <div class="chat-window">
            <div class="chat-messages" id="chatMessages">
                <div class="msg ai">
                    <div class="msg-avatar"><i class="fas fa-robot"></i></div>
                    <div>
                        <div class="msg-bubble">Hello! I'm your AI Legal Assistant. I can help you understand legal concepts, Indian laws, your rights, and legal procedures. What would you like to know today?</div>
                        <div class="msg-time">Just now</div>
                    </div>
                </div>
            </div>
            <div class="chat-input-area">
                <textarea class="chat-input" id="chatInput" placeholder="Type your legal question here..." rows="1" onkeydown="handleKey(event)"></textarea>
                <button class="send-btn" id="sendBtn" onclick="sendMessage()"><i class="fas fa-paper-plane"></i></button>
            </div>
        </div>

        <div class="disclaimer">
            <i class="fas fa-exclamation-triangle"></i> <strong>Disclaimer:</strong> This AI provides general legal information only. It is not a substitute for professional legal advice. Always consult a qualified lawyer for your specific situation.
        </div>
    </div>

    <!-- Bottom Navigation -->
    <nav class="bottom-nav">
        <div class="bottom-nav-container">
            <a href="mainhome.php" class="nav-item"><i class="fas fa-home"></i><span>Home</span></a>
            <a href="connect.php" class="nav-item"><i class="fas fa-user-tie"></i><span>Connect</span></a>
            <a href="aitool.php" class="nav-item center-ai active"><i class="fas fa-robot"></i><span>AI Tool</span></a>
            <a href="community.php" class="nav-item"><i class="fas fa-comments"></i><span>Community</span></a>
            <a href="profile.php" class="nav-item"><i class="fas fa-user-circle"></i><span>Profile</span></a>
        </div>
    </nav>

    <script>
        // Static AI responses for demo
        const aiResponses = {
            "tenant": "As a tenant in India, you have several key rights:\n• **Right to live peacefully** without landlord interference\n• Protection from unlawful eviction (must receive proper notice — 30-90 days depending on state)\n• Right to essential services like water and electricity\n• Right to a written rent agreement\n• Right to get your security deposit back (usually within 30 days)\n\nThe Rent Control Act varies by state, so check your local version. Always insist on a registered rent agreement.",
            "fir": "To file an FIR (First Information Report):\n1. **Visit the nearest police station** in the area where the incident occurred\n2. You have the **right to get an FIR registered** — police cannot refuse if the offence is cognizable\n3. Provide all details: what happened, when, where, who was involved\n4. The officer will record it, read it to you, and you must **sign it**\n5. Demand a **free copy** of the FIR — this is your legal right under Section 154 CRPC\n\nIf police refuse, you can approach the Superintendent of Police or file an e-FIR online.",
            "420": "IPC Section 420 deals with **Cheating and dishonestly inducing delivery of property**.\n\n**What it covers:** When someone deceives another person and dishonestly convinces them to deliver property, money, or sign/destroy a document.\n\n**Punishment:** Up to 7 years imprisonment and/or fine.\n\n**Key difference from Section 415:** Section 420 specifically involves delivery of property, making it more serious.\n\nCommon examples: selling fake products, investment fraud, advance-fee scams.",
            "bail": "**Bail** is the temporary release of an accused person from custody while their trial is pending.\n\n**Types:**\n• **Regular Bail** (Section 437/439 CRPC) — applied in Sessions Court after arrest\n• **Anticipatory Bail** (Section 438 CRPC) — sought before arrest\n• **Interim Bail** — temporary short-term bail\n\n**How to get bail:**\n1. Hire a criminal lawyer\n2. Lawyer files a bail application in the appropriate court\n3. Factors considered: gravity of offence, past criminal record, flight risk\n4. If granted, surety/bond must be furnished\n\nFor bailable offences, bail is a right. For non-bailable, it's at court's discretion.",
            "divorce": "**Divorce in India** — How to file:\n\n**Mutual Consent Divorce (Easiest):**\n1. Both spouses must agree\n2. File jointly in Family Court\n3. First motion hearing, 6-month cooling period, second motion hearing\n4. Can be waived if living separately for 18+ months\n\n**Contested Divorce:**\nGrounds include: cruelty, adultery, desertion (2 years), mental disorder\n\n**Documents needed:** Marriage certificate, address proof, photos, salary slips\n\n**Hindu couples:** Hindu Marriage Act 1955 | **Muslim:** Personal law | **Others:** Special Marriage Act",
            "arrested": "**Rights of an arrested person under Indian law:**\n\n1. **Right to know the grounds of arrest** — police must inform you why you're being arrested\n2. **Right to a lawyer** — you can consult and be defended by a legal practitioner (Article 22)\n3. **Right to be produced before a magistrate within 24 hours**\n4. **Right to inform a friend/relative** of your arrest\n5. **Right against self-incrimination** — you cannot be forced to testify against yourself (Article 20)\n6. **Right to medical examination** if you claim assault during arrest\n7. **Right to free legal aid** if you cannot afford a lawyer (Sections 304A and 304B CRPC)\n\nRemember: Stay calm, do not resist, ask for a lawyer immediately.",
            "legal notice": "A **Legal Notice** is a formal written communication sent by one party to another indicating their intention to take legal action.\n\n**When to use it:**\n• Recovery of money/dues\n• Property disputes\n• Employment disputes\n• Breach of contract\n• Consumer complaints\n\n**How to send:**\n1. Draft the notice clearly stating the grievance and demand\n2. Send via **Registered Post with Acknowledgement Due (RPAD)**\n3. Keep a copy for your records\n4. Give the other party reasonable time to respond (usually 15-30 days)\n\n**Effect:** It creates a legal record and often resolves disputes without going to court.\n\nIt's recommended to have a lawyer draft or review the notice.",
        };

        function getAIResponse(userMessage) {
            const msg = userMessage.toLowerCase();
            if (msg.includes('tenant') || msg.includes('rent') || msg.includes('landlord')) return aiResponses["tenant"];
            if (msg.includes('fir') || msg.includes('police complaint')) return aiResponses["fir"];
            if (msg.includes('420') || msg.includes('cheating')) return aiResponses["420"];
            if (msg.includes('bail')) return aiResponses["bail"];
            if (msg.includes('divorce') || msg.includes('separation')) return aiResponses["divorce"];
            if (msg.includes('arrest') || msg.includes('arrested') || msg.includes('rights of')) return aiResponses["arrested"];
            if (msg.includes('legal notice') || msg.includes('notice')) return aiResponses["legal notice"];
            return "Thank you for your question. This is a general AI assistant for legal information. For a detailed answer about \"" + userMessage + "\", I recommend:\n\n1. Consulting a qualified lawyer through our **Connect** section\n2. Posting your question in the **Community** forum for peer input\n3. Visiting official legal resources like India Code (indiacode.nic.in)\n\nRemember, every legal situation is unique — always seek professional advice for your specific case.";
        }

        function formatResponse(text) {
            return text.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>').replace(/\n/g, '<br>');
        }

        function getTime() {
            return new Date().toLocaleTimeString([], {hour:'2-digit',minute:'2-digit'});
        }

        function addMessage(text, sender) {
            const container = document.getElementById('chatMessages');
            const isAI = sender === 'ai';
            const div = document.createElement('div');
            div.className = `msg ${sender}`;
            div.innerHTML = `
                <div class="msg-avatar">${isAI ? '<i class="fas fa-robot"></i>' : '<i class="fas fa-user"></i>'}</div>
                <div>
                    <div class="msg-bubble">${isAI ? formatResponse(text) : text}</div>
                    <div class="msg-time">${getTime()}</div>
                </div>`;
            container.appendChild(div);
            container.scrollTop = container.scrollHeight;
        }

        function showTyping() {
            const container = document.getElementById('chatMessages');
            const div = document.createElement('div');
            div.className = 'msg ai';
            div.id = 'typingIndicator';
            div.innerHTML = `
                <div class="msg-avatar"><i class="fas fa-robot"></i></div>
                <div class="typing-indicator" style="display:flex;">
                    <div class="typing-dot"></div><div class="typing-dot"></div><div class="typing-dot"></div>
                </div>`;
            container.appendChild(div);
            container.scrollTop = container.scrollHeight;
        }

        function removeTyping() {
            const el = document.getElementById('typingIndicator');
            if (el) el.remove();
        }

        async function sendMessage() {
            const input = document.getElementById('chatInput');
            const text = input.value.trim();
            if (!text) return;

            addMessage(text, 'user');
            input.value = '';
            input.style.height = 'auto';
            document.getElementById('sendBtn').disabled = true;

            showTyping();
            await new Promise(r => setTimeout(r, 900 + Math.random() * 600));
            removeTyping();
            addMessage(getAIResponse(text), 'ai');
            document.getElementById('sendBtn').disabled = false;
        }

        function sendPrompt(el) {
            document.getElementById('chatInput').value = el.textContent;
            sendMessage();
        }

        function handleKey(e) {
            if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); sendMessage(); }
        }

        async function logout() {
            try { await fetch('../lib/logout.php'); } catch(e){}
            window.location.href = '../index.php';
        }

        async function loadUser() {
            try {
                const r = await fetch('../lib/db.php');
                // just load name
            } catch(e){}
        }
    </script>
</body>
</html>

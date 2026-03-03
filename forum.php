<?php require_once 'lib/db.php'; requireAuth(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forum - Law Connectors</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {margin: 0; padding: 0; box-sizing: border-box;}
        body {font-family: 'Inter', sans-serif; background: #fff; color: #0a0a0a;}
        
        .navbar {background: #0a0a0a; padding: 15px 0; position: sticky; top: 0; z-index: 100;}
        .nav-container {max-width: 1200px; margin: 0 auto; padding: 0 20px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;}
        .logo {font-family: 'Playfair Display', serif; font-size: 24px; color: #fff; font-weight: 700; text-decoration: none;}
        .nav-links {display: flex; gap: 0; align-items: center; flex-wrap: wrap;}
        .nav-links a {color: #fff; text-decoration: none; padding: 10px 20px; font-size: 14px; font-weight: 500; transition: all 0.2s; border-radius: 2px;}
        .nav-links a:hover {background: #1a1a1a;}
        .nav-links a.active {background: #fff; color: #0a0a0a;}
        
        .container {max-width: 1200px; margin: 0 auto; padding: 40px 20px;}
        
        .page-header {margin-bottom: 40px; display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 20px;}
        .page-header-text h1 {font-family: 'Playfair Display', serif; font-size: 42px; color: #0a0a0a; margin-bottom: 10px;}
        .page-header-text p {color: #888; font-size: 16px;}
        
        .btn {padding: 12px 28px; border: none; border-radius: 2px; cursor: pointer; font-size: 14px; font-weight: 600; transition: all 0.2s; display: inline-flex; align-items: center; gap: 8px; text-decoration: none; font-family: 'Inter', sans-serif;}
        .btn-primary {background: #0a0a0a; color: #fff;}
        .btn-primary:hover {background: #1a1a1a;}
        .btn-secondary {background: #f5f5f3; color: #0a0a0a;}
        .btn-secondary:hover {background: #e8e8e4;}
        
        .content-box {background: #fafafa; padding: 30px; border-radius: 4px; margin-bottom: 25px; border: 1px solid #e8e8e4;}
        
        .question-card {background: #fff; padding: 25px; border-radius: 4px; margin-bottom: 20px; cursor: pointer; transition: all 0.2s; border: 1px solid #e8e8e4;}
        .question-card:hover {border-color: #0a0a0a;}
        .question-header {display: flex; justify-content: space-between; align-items: flex-start; gap: 20px; margin-bottom: 15px;}
        .question-title {font-size: 20px; font-weight: 600; color: #0a0a0a; margin-bottom: 10px;}
        .question-meta {display: flex; gap: 20px; font-size: 13px; color: #888; flex-wrap: wrap;}
        .question-meta span {display: flex; align-items: center; gap: 6px;}
        
        .badge {padding: 6px 14px; border-radius: 2px; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;}
        .badge-answered {background: #0a0a0a; color: #fff;}
        .badge-open {background: #fff3cd; color: #856404;}
        
        .modal {display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); z-index: 1000; align-items: center; justify-content: center; padding: 20px;}
        .modal.active {display: flex;}
        .modal-content {background: #fff; border-radius: 4px; padding: 40px; max-width: 700px; width: 100%; max-height: 90vh; overflow-y: auto;}
        .modal-header {margin-bottom: 30px;}
        .modal-header h2 {font-family: 'Playfair Display', serif; font-size: 28px; color: #0a0a0a; margin-bottom: 10px;}
        .modal-header p {color: #888; font-size: 14px;}
        
        .form-group {margin-bottom: 25px;}
        .form-group label {display: block; margin-bottom: 8px; font-weight: 600; color: #0a0a0a; font-size: 14px;}
        .form-group input, .form-group select, .form-group textarea {width: 100%; padding: 12px 16px; border: 1px solid #ddd; border-radius: 2px; font-size: 14px; font-family: 'Inter', sans-serif;}
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus {outline: none; border-color: #0a0a0a;}
        .checkbox-group {display: flex; align-items: center; gap: 10px;}
        .checkbox-group input[type="checkbox"] {width: auto;}
        
        hr {border: none; border-top: 1px solid #e8e8e4; margin: 30px 0;}
        
        @media (max-width: 768px) {
            .navbar-container {
                padding: 12px 16px;
            }
            .logo {
                font-size: 18px;
            }
            .nav-links {
                display: none;
            }
            .container {
                padding: 20px 16px;
            }
            .page-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 16px;
            }
            .page-header-text h1 {
                font-size: 26px;
            }
            .page-header-text p {
                font-size: 14px;
            }
            .question-card {
                padding: 16px;
            }
            .question-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
            }
            .question-title {
                font-size: 17px;
            }
            .question-meta {
                flex-wrap: wrap;
                gap: 12px;
            }
            .modal-content {
                padding: 24px 20px;
                margin: 0 16px;
            }
            .modal-header h2 {
                font-size: 22px;
            }
            .page-header {flex-direction: column;}
            .page-header-text h1 {font-size: 32px;}
            .nav-container {flex-direction: column; align-items: stretch;}
            .nav-links {flex-direction: column; width: 100%;}
            .nav-links a {text-align: center;}
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
                <a href="forum.php" class="active"><i class="fas fa-comments"></i> Forum</a>
                <a href="wallet.php"><i class="fas fa-wallet"></i> Wallet</a>
                <a href="lib/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>
    </nav>

    <!-- Main Container -->
    <div class="container">
        <!-- Page Header -->
        <div class="page-header">
            <div class="page-header-text">
                <h1>Ask a Lawyer Forum</h1>
                <p>Get answers to your legal questions from expert lawyers</p>
            </div>
            <button class="btn btn-primary" onclick="openAskQuestionModal()">
                <i class="fas fa-plus"></i> Ask Question
            </button>
        </div>

        <!-- Questions List -->
        <div class="content-box">
            <div id="questionsContainer">
                <p style="text-align: center; padding: 40px; color: #888;">Loading questions...</p>
            </div>
        </div>
    </div>

    <!-- Ask Question Modal -->
    <div id="askQuestionModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Ask Your Question</h2>
                <p>Our expert lawyers will answer your legal queries</p>
            </div>
            <form id="askQuestionForm">
                <div class="form-group">
                    <label>Question Title</label>
                    <input type="text" id="questionTitle" required placeholder="Brief summary of your legal question">
                </div>
                <div class="form-group">
                    <label>Legal Category</label>
                    <select id="questionCategory" required>
                        <option value="General">General Law</option>
                        <option value="Family Law">Family Law</option>
                        <option value="Criminal Law">Criminal Law</option>
                        <option value="Corporate Law">Corporate & Business Law</option>
                        <option value="Property Law">Property & Real Estate</option>
                        <option value="Tax Law">Tax & Financial Law</option>
                        <option value="Labour Law">Labour & Employment</option>
                        <option value="Consumer Law">Consumer Protection</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Detailed Question</label>
                    <textarea id="questionText" rows="8" required placeholder="Describe your legal question in detail. Include relevant facts and circumstances..."></textarea>
                </div>
                <div class="form-group checkbox-group">
                    <input type="checkbox" id="isAnonymous">
                    <label for="isAnonymous" style="margin: 0; font-weight: 400;">Post anonymously</label>
                </div>
                <div style="display: flex; gap: 10px;">
                    <button type="submit" class="btn btn-primary" style="flex: 1;">
                        <i class="fas fa-paper-plane"></i> Post Question
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="closeAskModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- View Question Modal -->
    <div id="viewQuestionModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Question Details</h2>
            </div>
            <div id="questionDetails"></div>
            <hr>
            <h3 style="margin-bottom: 20px; font-size: 20px; font-weight: 600;">Submit Your Answer</h3>
            <form id="answerForm">
                <div class="form-group">
                    <textarea id="answerText" rows="6" required placeholder="Provide a detailed legal answer..."></textarea>
                </div>
                <div style="display: flex; gap: 10px;">
                    <button type="submit" class="btn btn-primary" style="flex: 1;">
                        <i class="fas fa-reply"></i> Submit Answer
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="closeViewModal()">Close</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let currentQuestionId = null;

        async function loadQuestions() {
            try {
                const response = await fetch('lib/forum_get_questions.php');
                const result = await response.json();

                if (result.error) throw new Error(result.error);

                displayQuestions(result.data || []);
            } catch (error) {
                console.error('Error:', error);
                document.getElementById('questionsContainer').innerHTML = '<p style="text-align: center; color: #dc3545;">Error loading questions</p>';
            }
        }

        function displayQuestions(questions) {
            const container = document.getElementById('questionsContainer');
            
            if (questions.length === 0) {
                container.innerHTML = '<p style="text-align: center; padding: 40px; color: #888;">No questions yet. Be the first to ask!</p>';
                return;
            }

            let html = '';
            questions.forEach(q => {
                const statusBadge = q.status === 'answered' ? 'badge-answered' : 'badge-open';
                const statusText = q.status === 'answered' ? 'Answered' : 'Open';
                
                html += `
                    <div class="question-card" onclick="viewQuestion(${q.id})">
                        <div class="question-header">
                            <div style="flex: 1;">
                                <div class="question-title">${q.title}</div>
                                <div class="question-meta">
                                    <span><i class="fas fa-user"></i> ${q.author_name || 'Anonymous'}</span>
                                    <span><i class="fas fa-tag"></i> ${q.category}</span>
                                    <span><i class="fas fa-eye"></i> ${q.views} views</span>
                                    <span><i class="fas fa-comments"></i> ${q.answer_count} answers</span>
                                </div>
                            </div>
                            <span class="badge ${statusBadge}">${statusText}</span>
                        </div>
                    </div>
                `;
            });

            container.innerHTML = html;
        }

        function openAskQuestionModal() {
            document.getElementById('askQuestionModal').classList.add('active');
        }

        function closeAskModal() {
            document.getElementById('askQuestionModal').classList.remove('active');
        }

        function closeViewModal() {
            document.getElementById('viewQuestionModal').classList.remove('active');
        }

        function viewQuestion(questionId) {
            currentQuestionId = questionId;
            // In a real app, fetch question details
            document.getElementById('viewQuestionModal').classList.add('active');
        }

        document.getElementById('askQuestionForm').addEventListener('submit', async (e) => {
            e.preventDefault();

            const formData = {
                title: document.getElementById('questionTitle').value,
                question: document.getElementById('questionText').value,
                category: document.getElementById('questionCategory').value,
                is_anonymous: document.getElementById('isAnonymous').checked
            };

            try {
                const response = await fetch('lib/student/forum_post_question.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify(formData)
                });

                const result = await response.json();

                if (result.error) {
                    alert('Error: ' + result.error);
                } else {
                    alert('Question posted successfully!');
                    closeAskModal();
                    document.getElementById('askQuestionForm').reset();
                    loadQuestions();
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            }
        });

        window.addEventListener('load', loadQuestions);
    </script>

    <?php include 'includes/bottom_nav.php'; ?>
</body>
</html>

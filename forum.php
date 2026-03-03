<?php require_once 'lib/db.php'; requireAuth(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forum - Law Connectors</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {margin: 0; padding: 0; box-sizing: border-box;}
        body {font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; padding: 20px;}
        .container {max-width: 1200px; margin: 0 auto;}
        .header {background: white; padding: 20px 30px; border-radius: 15px; margin-bottom: 30px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;}
        .logo {font-size: 24px; font-weight: bold; color: #667eea; display: flex; align-items: center; gap: 10px;}
        .nav-buttons {display: flex; gap: 10px; flex-wrap: wrap;}
        .btn {padding: 10px 20px; border: none; border-radius: 8px; cursor: pointer; font-size: 14px; font-weight: 600; transition: all 0.3s; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;}
        .btn-primary {background: #667eea; color: white;}
        .btn-primary:hover {background: #5568d3; transform: translateY(-2px);}
        .btn-secondary {background: #f0f0f0; color: #333;}
        .btn-success {background: #28a745; color: white;}
        .content-box {background: white; padding: 25px; border-radius: 15px; margin-bottom: 25px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);}
        .question-card {background: #f8f9fa; padding: 20px; border-radius: 10px; margin-bottom: 15px; cursor: pointer; transition: all 0.3s;}
        .question-card:hover {transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,0,0,0.1);}
        .question-header {display: flex; justify-content: space-between; align-items: start; margin-bottom: 10px;}
        .question-title {font-size: 18px; font-weight: 600; color: #333;}
        .question-meta {display: flex; gap: 15px; font-size: 13px; color: #999; margin-top: 10px;}
        .badge {padding: 5px 12px; border-radius: 15px; font-size: 12px; font-weight: 600;}
        .badge-success {background: #d4edda; color: #28a745;}
        .badge-warning {background: #fff3cd; color: #ffc107;}
        .badge-info {background: #d1ecf1; color: #17a2b8;}
        .modal {display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center; padding: 20px;}
        .modal.active {display: flex;}
        .modal-content {background: white; border-radius: 15px; padding: 30px; max-width: 600px; width: 100%; max-height: 90vh; overflow-y: auto;}
        .modal-header {display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;}
        .close-btn {font-size: 28px; cursor: pointer; color: #999;}
        .form-group {margin-bottom: 20px;}
        .form-group label {display: block; margin-bottom: 8px; font-weight: 600; color: #333;}
        .form-group input, .form-group select, .form-group textarea {width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 14px;}
        .checkbox-group {display: flex; align-items: center; gap: 10px;}
        .checkbox-group input[type="checkbox"] {width: auto;}
        @media (max-width: 768px) {.header {flex-direction: column; align-items: stretch;} .nav-buttons {justify-content: center;}}
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo"><i class="fas fa-comments"></i> Law Connectors - Ask a Lawyer Forum</div>
            <div class="nav-buttons">
                <a href="mainhome.php" class="btn btn-secondary"><i class="fas fa-home"></i> Dashboard</a>
                <a href="experts.php" class="btn btn-secondary"><i class="fas fa-users"></i> Experts</a>
                <button class="btn btn-success" onclick="openAskQuestionModal()"><i class="fas fa-plus"></i> Ask Question</button>
            </div>
        </div>

        <div class="content-box">
            <h2 style="margin-bottom: 20px; color: #333;">Recent Questions</h2>
            <div id="questionsContainer">
                <p style="text-align: center; padding: 40px; color: #999;">Loading questions...</p>
            </div>
        </div>
    </div>

    <!-- Ask Question Modal -->
    <div id="askQuestionModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Ask a Question</h2>
                <span class="close-btn" onclick="closeAskModal()">&times;</span>
            </div>
            <form id="askQuestionForm">
                <div class="form-group">
                    <label>Title</label>
                    <input type="text" id="questionTitle" required placeholder="Brief summary of your question">
                </div>
                <div class="form-group">
                    <label>Category</label>
                    <select id="questionCategory" required>
                        <option value="General">General</option>
                        <option value="Family Law">Family Law</option>
                        <option value="Criminal Law">Criminal Law</option>
                        <option value="Corporate Law">Corporate Law</option>
                        <option value="Property Law">Property Law</option>
                        <option value="Tax Law">Tax Law</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Your Question</label>
                    <textarea id="questionText" rows="6" required placeholder="Describe your legal question in detail..."></textarea>
                </div>
                <div class="form-group checkbox-group">
                    <input type="checkbox" id="isAnonymous">
                    <label for="isAnonymous" style="margin: 0;">Post anonymously</label>
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%;"><i class="fas fa-paper-plane"></i> Post Question</button>
            </form>
        </div>
    </div>

    <!-- View Question Modal -->
    <div id="viewQuestionModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Question Details</h2>
                <span class="close-btn" onclick="closeViewModal()">&times;</span>
            </div>
            <div id="questionDetails"></div>
            <hr style="margin: 25px 0;">
            <h3 style="margin-bottom: 15px;">Your Answer</h3>
            <form id="answerForm">
                <div class="form-group">
                    <textarea id="answerText" rows="4" required placeholder="Type your answer here..."></textarea>
                </div>
                <button type="submit" class="btn btn-primary"><i class="fas fa-reply"></i> Submit Answer</button>
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
                container.innerHTML = '<p style="text-align: center; color: #999;">No questions yet. Be the first to ask!</p>';
                return;
            }

            let html = '';
            questions.forEach(q => {
                const statusBadge = q.status === 'answered' ? 'badge-success' : 'badge-warning';
                const statusText = q.status === 'answered' ? 'Answered' : 'Open';
                
                html += `
                    <div class="question-card" onclick="viewQuestion(${q.id})">
                        <div class="question-header">
                            <div>
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
                const response = await fetch('lib/forum_post_question.php', {
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
</body>
</html>

<?php require_once __DIR__ . '/../lib/db.php'; requireAuth(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Community Forum - Law Connectors</title>
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
        .page-header{background:#0a0a0a;border-radius:4px;padding:40px;margin-bottom:28px;color:#fff;position:relative;overflow:hidden;}
        .page-header::before{content:'';position:absolute;top:-50px;right:-50px;width:220px;height:220px;background:rgba(255,255,255,0.04);border-radius:50%;}
        .page-header h1{font-family:'Playfair Display',serif;font-size:30px;font-weight:700;margin-bottom:8px;position:relative;}
        .page-header p{font-size:15px;opacity:0.7;position:relative;}
        .page-header .header-icon{font-size:40px;margin-bottom:16px;display:block;position:relative;}

        /* Filters */
        .filters{display:flex;gap:10px;flex-wrap:wrap;margin-bottom:24px;}
        .filter-btn{padding:8px 18px;border:1.5px solid #e8e8e4;border-radius:20px;font-size:13px;font-weight:500;cursor:pointer;background:#fff;color:#666;transition:all 0.2s;}
        .filter-btn:hover,.filter-btn.active{background:#0a0a0a;color:#fff;border-color:#0a0a0a;}

        /* Ask Button */
        .ask-btn{display:inline-flex;align-items:center;gap:8px;padding:12px 24px;background:#0a0a0a;color:#fff;border:none;border-radius:4px;font-size:14px;font-weight:600;cursor:pointer;margin-bottom:24px;transition:all 0.2s;}
        .ask-btn:hover{background:#333;}

        /* Post cards */
        .posts-list{display:flex;flex-direction:column;gap:16px;}
        .post-card{background:#fff;border:1px solid #e8e8e4;border-radius:4px;padding:24px;transition:all 0.25s;cursor:pointer;}
        .post-card:hover{border-color:#0a0a0a;box-shadow:0 4px 16px rgba(0,0,0,0.06);}
        .post-top{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:12px;}
        .post-category{display:inline-block;padding:4px 12px;border-radius:12px;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;}
        .cat-family{background:#fef3c7;color:#92400e;}
        .cat-criminal{background:#fee2e2;color:#991b1b;}
        .cat-civil{background:#dbeafe;color:#1e40af;}
        .cat-property{background:#d1fae5;color:#065f46;}
        .cat-consumer{background:#ede9fe;color:#5b21b6;}
        .cat-labour{background:#fce7f3;color:#9d174d;}
        .post-title{font-size:17px;font-weight:600;color:#0a0a0a;margin-bottom:8px;line-height:1.45;}
        .post-excerpt{font-size:14px;color:#666;line-height:1.6;margin-bottom:16px;}
        .post-meta{display:flex;align-items:center;gap:20px;font-size:13px;color:#888;}
        .post-author{display:flex;align-items:center;gap:8px;}
        .author-avatar{width:28px;height:28px;border-radius:50%;background:#0a0a0a;color:#fff;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:600;}
        .post-stats{display:flex;gap:16px;margin-left:auto;}
        .stat-item{display:flex;align-items:center;gap:5px;font-size:13px;color:#888;}
        .stat-item i{font-size:13px;}
        .answered-badge{background:#dcfce7;color:#166534;padding:3px 10px;border-radius:10px;font-size:11px;font-weight:600;}

        /* Ask Question Modal */
        .modal-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:9999;align-items:center;justify-content:center;}
        .modal-overlay.show{display:flex;}
        .modal{background:#fff;border-radius:4px;width:90%;max-width:580px;padding:36px;max-height:90vh;overflow-y:auto;}
        .modal h2{font-family:'Playfair Display',serif;font-size:24px;margin-bottom:20px;}
        .form-group{margin-bottom:18px;}
        .form-group label{display:block;font-size:14px;font-weight:500;margin-bottom:6px;}
        .form-group input,.form-group textarea,.form-group select{width:100%;padding:11px 14px;border:1.5px solid #e5e5e5;border-radius:4px;font-size:14px;font-family:'Inter',sans-serif;outline:none;transition:border-color 0.2s;}
        .form-group input:focus,.form-group textarea:focus,.form-group select:focus{border-color:#0a0a0a;}
        .form-group textarea{resize:vertical;min-height:100px;}
        .modal-actions{display:flex;gap:12px;justify-content:flex-end;margin-top:8px;}
        .btn-cancel{padding:10px 20px;background:#fff;border:1.5px solid #ddd;border-radius:4px;cursor:pointer;font-size:14px;font-weight:500;}
        .btn-submit{padding:10px 24px;background:#0a0a0a;color:#fff;border:none;border-radius:4px;cursor:pointer;font-size:14px;font-weight:600;}

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
                <button class="logout-btn" onclick="logout()"><i class="fas fa-sign-out-alt"></i> Logout</button>
            </div>
        </div>
    </nav>

    <div class="page-container">
        <div class="page-header">
            <span class="header-icon">💬</span>
            <h1>Community Forum</h1>
            <p>Ask legal questions, get expert answers, and help others with your knowledge.</p>
        </div>

        <button class="ask-btn" onclick="document.getElementById('askModal').classList.add('show')">
            <i class="fas fa-plus"></i> Ask a Question
        </button>

        <div class="filters">
            <button class="filter-btn active" onclick="filterPosts('all',this)">All Topics</button>
            <button class="filter-btn" onclick="filterPosts('family',this)">Family Law</button>
            <button class="filter-btn" onclick="filterPosts('criminal',this)">Criminal</button>
            <button class="filter-btn" onclick="filterPosts('civil',this)">Civil</button>
            <button class="filter-btn" onclick="filterPosts('property',this)">Property</button>
            <button class="filter-btn" onclick="filterPosts('consumer',this)">Consumer</button>
            <button class="filter-btn" onclick="filterPosts('labour',this)">Labour</button>
        </div>

        <div class="posts-list" id="postsList"></div>
    </div>

    <!-- Ask Question Modal -->
    <div class="modal-overlay" id="askModal">
        <div class="modal">
            <h2>Ask a Legal Question</h2>
            <div class="form-group">
                <label>Question Title *</label>
                <input type="text" id="qTitle" placeholder="e.g. Can a landlord evict without notice?">
            </div>
            <div class="form-group">
                <label>Category *</label>
                <select id="qCategory">
                    <option value="">Select category</option>
                    <option value="family">Family Law</option>
                    <option value="criminal">Criminal Law</option>
                    <option value="civil">Civil Law</option>
                    <option value="property">Property Law</option>
                    <option value="consumer">Consumer Rights</option>
                    <option value="labour">Labour Law</option>
                </select>
            </div>
            <div class="form-group">
                <label>Describe your situation *</label>
                <textarea id="qBody" placeholder="Provide details about your legal situation..."></textarea>
            </div>
            <div class="modal-actions">
                <button class="btn-cancel" onclick="document.getElementById('askModal').classList.remove('show')">Cancel</button>
                <button class="btn-submit" onclick="submitQuestion()">Post Question</button>
            </div>
        </div>
    </div>

    <nav class="bottom-nav">
        <div class="bottom-nav-container">
            <a href="mainhome.php" class="nav-item"><i class="fas fa-home"></i><span>Home</span></a>
            <a href="connect.php" class="nav-item"><i class="fas fa-user-tie"></i><span>Connect</span></a>
            <a href="aitool.php" class="nav-item center-ai"><i class="fas fa-robot"></i><span>AI Tool</span></a>
            <a href="community.php" class="nav-item active"><i class="fas fa-comments"></i><span>Community</span></a>
            <a href="profile.php" class="nav-item"><i class="fas fa-user-circle"></i><span>Profile</span></a>
        </div>
    </nav>

    <script>
        const posts = [
            {id:1,category:'family',title:'Can my wife take the children out of India without my consent?',excerpt:'We are going through a divorce and I am worried my wife might take our two children abroad without informing me. What are my legal rights and what steps can I take?',author:'Rajesh M.',answers:4,views:312,upvotes:18,answered:true,time:'2 hours ago'},
            {id:2,category:'criminal',title:'I received a police notice under Section 41A CRPC. Do I have to go?',excerpt:'Got a notice asking me to appear at the police station. I have not been arrested. Am I legally obligated to appear and what should I do?',author:'Priya K.',answers:6,views:487,upvotes:24,answered:true,time:'5 hours ago'},
            {id:3,category:'property',title:'Builder is refusing to give possession after 3 years — what can I do?',excerpt:'I booked a flat in 2021, paid 90% of the amount but the builder keeps delaying possession. Is there any legal remedy? Can I approach RERA?',author:'Amit S.',answers:3,views:891,upvotes:41,answered:true,time:'1 day ago'},
            {id:4,category:'consumer',title:'Online seller sent defective product and is not responding to refund request',excerpt:'Ordered electronics worth ₹15,000. Received broken item. Seller on Amazon is not processing refund. What options do I have under Consumer Protection Act 2019?',author:'Neha R.',answers:5,views:256,upvotes:12,answered:true,time:'2 days ago'},
            {id:5,category:'labour',title:'Employer terminated me during probation without notice — is this legal?',excerpt:'I was in the 3rd month of my 6-month probation when my employer terminated me verbally with no written notice or reason. I haven\'t received my advance salary back either.',author:'Vikram P.',answers:2,views:198,upvotes:9,answered:false,time:'3 days ago'},
            {id:6,category:'civil',title:'Neighbour built a wall encroaching on my land — what are my options?',excerpt:'My neighbour has constructed a boundary wall extending 2 feet into my registered property. I have the original sale deed and survey documents. What legal action can I take?',author:'Sunita T.',answers:7,views:643,upvotes:35,answered:true,time:'4 days ago'},
            {id:7,category:'family',title:'Father passed away without a will — how will property be distributed?',excerpt:'My father passed away intestate (without a will). We have a house and some savings. There are 3 siblings including me and our mother. What does the Hindu Succession Act say?',author:'Ananya G.',answers:8,views:1120,upvotes:52,answered:true,time:'1 week ago'},
            {id:8,category:'criminal',title:'False dowry harassment case filed against me — how do I defend myself?',excerpt:'My wife and her family have filed a case under Section 498A IPC against me and my parents. The allegations are completely false. We have evidence. What should I do first?',author:'Karan B.',answers:11,views:2340,upvotes:78,answered:true,time:'1 week ago'},
        ];

        const catLabels = {family:'Family Law',criminal:'Criminal',civil:'Civil',property:'Property',consumer:'Consumer',labour:'Labour'};
        const catClass  = {family:'cat-family',criminal:'cat-criminal',civil:'cat-civil',property:'cat-property',consumer:'cat-consumer',labour:'cat-labour'};

        let activeFilter = 'all';

        function renderPosts(filter) {
            const list = document.getElementById('postsList');
            const filtered = filter === 'all' ? posts : posts.filter(p => p.category === filter);
            list.innerHTML = filtered.map(p => `
                <div class="post-card" data-cat="${p.category}">
                    <div class="post-top">
                        <span class="post-category ${catClass[p.category]}">${catLabels[p.category]}</span>
                        ${p.answered ? '<span class="answered-badge"><i class="fas fa-check"></i> Answered</span>' : ''}
                    </div>
                    <div class="post-title">${p.title}</div>
                    <div class="post-excerpt">${p.excerpt}</div>
                    <div class="post-meta">
                        <div class="post-author">
                            <div class="author-avatar">${p.author[0]}</div>
                            <span>${p.author}</span>
                            <span style="color:#ccc;">·</span>
                            <span>${p.time}</span>
                        </div>
                        <div class="post-stats">
                            <span class="stat-item"><i class="fas fa-arrow-up"></i>${p.upvotes}</span>
                            <span class="stat-item"><i class="fas fa-reply"></i>${p.answers} answers</span>
                            <span class="stat-item"><i class="fas fa-eye"></i>${p.views}</span>
                        </div>
                    </div>
                </div>`).join('');
        }

        function filterPosts(cat, btn) {
            activeFilter = cat;
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            renderPosts(cat);
        }

        function submitQuestion() {
            const title = document.getElementById('qTitle').value.trim();
            const cat   = document.getElementById('qCategory').value;
            const body  = document.getElementById('qBody').value.trim();
            if (!title || !cat || !body) { alert('Please fill all fields.'); return; }
            const newPost = {id:Date.now(),category:cat,title:title,excerpt:body.slice(0,180)+'...',author:'You',answers:0,views:1,upvotes:0,answered:false,time:'Just now'};
            posts.unshift(newPost);
            document.getElementById('askModal').classList.remove('show');
            document.getElementById('qTitle').value='';
            document.getElementById('qCategory').value='';
            document.getElementById('qBody').value='';
            renderPosts(activeFilter);
        }

        async function logout() {
            try { await fetch('../lib/logout.php'); } catch(e){}
            window.location.href = '../index.php';
        }

        renderPosts('all');
    </script>
</body>
</html>

// ===== STUDENT PANEL - COMMON JAVASCRIPT =====

// Toggle user menu
function toggleUserMenu() {
    const dropdown = document.getElementById('userDropdown');
    if (dropdown) {
        dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
    }
}

// Close menu when clicking outside
document.addEventListener('click', function(e) {
    const userMenu = document.querySelector('.user-menu');
    if (userMenu && !userMenu.contains(e.target)) {
        const dropdown = document.getElementById('userDropdown');
        if (dropdown) dropdown.style.display = 'none';
    }
});

// Search functionality
function openSearchModal() {
    const modal = document.getElementById('searchModal');
    if (modal) {
        modal.style.display = 'flex';
        document.getElementById('searchInput').focus();
    }
}

function closeSearchModal() {
    const modal = document.getElementById('searchModal');
    if (modal) {
        modal.style.display = 'none';
    }
}

function performSearch(query) {
    if (query.length < 2) {
        document.getElementById('searchResults').innerHTML = '';
        return;
    }

    fetch(`api/search.php?q=${encodeURIComponent(query)}`)
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                displaySearchResults(data.results);
            }
        })
        .catch(e => console.error('Search error:', e));
}

function displaySearchResults(results) {
    const resultsDiv = document.getElementById('searchResults');
    if (results.length === 0) {
        resultsDiv.innerHTML = '<p class="no-results">No results found</p>';
        return;
    }

    resultsDiv.innerHTML = results.map(r => `
        <a href="${r.url}" class="search-result">
            <i class="fas fa-${r.icon}"></i>
            <div>
                <div class="result-title">${r.title}</div>
                <div class="result-type">${r.type}</div>
            </div>
        </a>
    `).join('');
}

// Close search modal when clicking outside
window.addEventListener('click', function(e) {
    const modal = document.getElementById('searchModal');
    if (modal && e.target === modal) {
        closeSearchModal();
    }
});

// Close modals on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const modals = document.querySelectorAll('.modal');
        modals.forEach(modal => {
            modal.style.display = 'none';
        });
    }
});

// Format currency
function formatCurrency(amount) {
    return new Intl.NumberFormat('en-IN', {
        style: 'currency',
        currency: 'INR',
        minimumFractionDigits: 0
    }).format(amount);
}

// Format date
function formatDate(dateString) {
    return new Date(dateString).toLocaleDateString('en-IN', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
}

// Show notification
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
        ${message}
    `;
    document.body.appendChild(notification);

    setTimeout(() => {
        notification.classList.add('show');
    }, 100);

    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// API call helper
async function apiCall(url, method = 'GET', data = null) {
    const options = {
        method: method,
        headers: {
            'Content-Type': 'application/json'
        }
    };

    if (data) {
        options.body = JSON.stringify(data);
    }

    try {
        const response = await fetch(url, options);
        const result = await response.json();
        return result;
    } catch (error) {
        console.error('API error:', error);
        showNotification('An error occurred', 'error');
        throw error;
    }
}

// Load more functionality
let loadMoreOffset = 0;
const loadMoreLimit = 10;

function loadMore() {
    loadMoreOffset += loadMoreLimit;
    // Implementation depends on context
}

// Star rating interaction
function setupStarRating(containerId) {
    const container = document.getElementById(containerId);
    if (!container) return;

    const stars = container.querySelectorAll('i');
    let selectedRating = 0;

    stars.forEach(star => {
        star.addEventListener('click', function() {
            selectedRating = this.dataset.rating;
            updateStars(stars, selectedRating);
        });

        star.addEventListener('mouseenter', function() {
            updateStars(stars, this.dataset.rating);
        });
    });

    container.addEventListener('mouseleave', function() {
        updateStars(stars, selectedRating);
    });
}

function updateStars(stars, rating) {
    stars.forEach((star, index) => {
        if (index < rating) {
            star.classList.add('filled');
        } else {
            star.classList.remove('filled');
        }
    });
}

// Smooth scroll
function smoothScroll(target) {
    const element = document.querySelector(target);
    if (element) {
        element.scrollIntoView({ behavior: 'smooth' });
    }
}

// Form validation
function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return true;

    const inputs = form.querySelectorAll('input[required], textarea[required], select[required]');
    let isValid = true;

    inputs.forEach(input => {
        if (!input.value.trim()) {
            input.classList.add('error');
            isValid = false;
        } else {
            input.classList.remove('error');
        }
    });

    return isValid;
}

// Toggle tab visibility
function switchTab(tabName) {
    const tabs = document.querySelectorAll('.tab-content');
    tabs.forEach(tab => {
        tab.classList.remove('active');
    });

    const tab = document.getElementById(tabName + '-tab');
    if (tab) {
        tab.classList.add('active');
    }
}

// Copy to clipboard
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        showNotification('Copied to clipboard', 'success');
    }).catch(() => {
        showNotification('Failed to copy', 'error');
    });
}

// Session timer (for live sessions)
function startSessionTimer(durationMinutes, onComplete) {
    let remaining = durationMinutes * 60;
    const timerElement = document.getElementById('sessionTimer');

    if (!timerElement) return;

    const interval = setInterval(() => {
        remaining--;

        const minutes = Math.floor(remaining / 60);
        const seconds = remaining % 60;

        timerElement.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;

        if (remaining <= 0) {
            clearInterval(interval);
            if (onComplete) onComplete();
        }
    }, 1000);

    return interval;
}

// Lazy load images
function setupLazyLoad() {
    const images = document.querySelectorAll('img[data-src]');

    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.removeAttribute('data-src');
                observer.unobserve(img);
            }
        });
    });

    images.forEach(img => imageObserver.observe(img));
}

// Debounce function
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Throttle function
function throttle(func, limit) {
    let inThrottle;
    return function(...args) {
        if (!inThrottle) {
            func.apply(this, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    };
}

// Get query parameters
function getQueryParam(param) {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get(param);
}

// Check if user is online
function checkOnlineStatus() {
    return navigator.onLine;
}

window.addEventListener('online', () => {
    showNotification('You are online', 'success');
});

window.addEventListener('offline', () => {
    showNotification('You are offline', 'error');
});

// Initialize animations on page load
document.addEventListener('DOMContentLoaded', () => {
    // Setup lazy loading
    setupLazyLoad();

    // Add intersection observer for fade-in animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('fade-in');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    document.querySelectorAll('.card, .session-card, .expert-card, .article-card').forEach(el => {
        observer.observe(el);
    });
});

// Add CSS for fade-in animation
const style = document.createElement('style');
style.textContent = `
    .fade-in {
        animation: fadeIn 0.6s ease-in-out;
    }
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    .notification {
        position: fixed;
        bottom: 20px;
        right: 20px;
        padding: 14px 20px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 14px;
        font-weight: 600;
        max-width: 400px;
        opacity: 0;
        transform: translateX(400px);
        transition: all 0.3s ease;
        z-index: 3000;
    }
    .notification.show {
        opacity: 1;
        transform: translateX(0);
    }
    .notification-success {
        background: #dcfce7;
        color: #166534;
        border: 1px solid #bbf7d0;
    }
    .notification-error {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }
    .notification-info {
        background: #dbeafe;
        color: #1e40af;
        border: 1px solid #bfdbfe;
    }
    @media (max-width: 480px) {
        .notification {
            bottom: 10px;
            right: 10px;
            left: 10px;
            max-width: none;
        }
    }
`;
document.head.appendChild(style);

// ===== NAVBAR SCROLL EFFECT =====
window.addEventListener('scroll', () => {
    const navbar = document.querySelector('.navbar');
    if (window.scrollY > 50) {
        navbar.style.background = 'rgba(10, 10, 10, 0.98)';
        navbar.style.boxShadow = '0 4px 30px rgba(0,0,0,0.5)';
    } else {
        navbar.style.background = 'rgba(10, 10, 10, 0.95)';
        navbar.style.boxShadow = 'none';
    }
});

// ===== SCROLL REVEAL ANIMATION =====
const revealElements = document.querySelectorAll('.product-card, .feature-card, .section-title');

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.style.opacity = '1';
            entry.target.style.transform = 'translateY(0)';
        }
    });
}, { threshold: 0.1 });

revealElements.forEach(el => {
    el.style.opacity = '0';
    el.style.transform = 'translateY(30px)';
    el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
    observer.observe(el);
});

// ===== TOAST NOTIFICATION =====
function showToast(message) {
    const existingToast = document.querySelector('.toast');
    if (existingToast) existingToast.remove();

    const toast = document.createElement('div');
    toast.className = 'toast';
    toast.textContent = message;

    toast.style.cssText = `
        position: fixed;
        bottom: 30px;
        left: 50%;
        transform: translateX(-50%) translateY(20px);
        background: #161616;
        border: 1px solid #c8a96e;
        color: #f5f5f0;
        padding: 16px 28px;
        font-size: 14px;
        z-index: 9999;
        opacity: 0;
        transition: all 0.4s ease;
        font-family: 'DM Sans', sans-serif;
        letter-spacing: 1px;
        white-space: nowrap;
    `;

    document.body.appendChild(toast);

    setTimeout(() => {
        toast.style.opacity = '1';
        toast.style.transform = 'translateX(-50%) translateY(0)';
    }, 10);

    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(-50%) translateY(20px)';
        setTimeout(() => toast.remove(), 400);
    }, 3000);
}

// ===== ACTIVE NAV LINK =====
const currentPage = window.location.pathname;
document.querySelectorAll('.nav-links a').forEach(link => {
    if (link.getAttribute('href') === currentPage) {
        link.style.color = '#c8a96e';
        link.style.opacity = '1';
    }
});
function toggleWishlist(productId, btn) {
    fetch('../php/wishlist_toggle.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'product_id=' + productId
    })
    .then(r => r.json())
    .then(data => { 
     if(data.status === 'added') {
     btn.textContent = '❤️';
     showToast('❤️ Added to Wishlist!');
     const wc = document.getElementById('wishlist-count');
     if(wc) wc.textContent = parseInt(wc.textContent) + 1;
 } else if(data.status === 'removed') {
     btn.textContent = '🤍';
     showToast('💔 Removed from Wishlist!');
     const wc = document.getElementById('wishlist-count');
     if(wc) wc.textContent = Math.max(0, parseInt(wc.textContent) - 1);
        } else if(data.status === 'login') {
            showToast('⚠️ Please login first!');
            setTimeout(() => window.location.href = 'login.php', 1500);
        }
    });
}

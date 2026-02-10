console.log("JavaScript connected successfully!");

// Modal elements
const formProject = document.getElementById("formProject");
const modal = document.getElementById("formModal");
const closeBtn = document.querySelector(".close");
const form = document.getElementById("validationForm");

// Open form (works for both project links)
document.querySelectorAll('.project-link').forEach(link => {
    link.addEventListener("click", (e) => {
        e.preventDefault();
        modal.style.display = "flex";
    });
});

// Close form
closeBtn.addEventListener("click", () => {
    modal.style.display = "none";
});

// Close on outside click
window.addEventListener("click", (e) => {
    if (e.target === modal) {
        modal.style.display = "none";
    }
});

// YOUR EXACT VALIDATION CODE (enhanced with visual feedback)
form.addEventListener("submit", (e) => {
    e.preventDefault();

    const name = document.getElementById("name");
    const email = document.getElementById("email");
    const phone = document.getElementById("phone");
    const password = document.getElementById("password");

    let isValid = true;

    // Regex patterns
    const nameRegex = /^[A-Za-z ]{3,}$/;
    const emailRegex = /^[^ ]+@[^ ]+\.[a-z]{2,}$/;
    const phoneRegex = /^[0-9]{10}$/;
    const passwordRegex = /^(?=.*[A-Z])(?=.*\d).{8,}$/;

    isValid &= validateField(name, nameRegex, "Enter valid name (3+ letters)");
    isValid &= validateField(email, emailRegex, "Enter valid email");
    isValid &= validateField(phone, phoneRegex, "Enter 10-digit number");
    isValid &= validateField(password, passwordRegex, "Min 8 chars, 1 capital & number");

    if (isValid) {
        alert("Form submitted successfully ✅");
        form.reset();
        modal.style.display = "none";
    }
});

function validateField(input, regex, message) {
    const small = input.nextElementSibling;
    if (!regex.test(input.value)) {
        small.innerText = message;
        input.style.borderColor = "#ff4444";
        input.style.boxShadow = "0 0 10px rgba(255,68,68,0.3)";
        return false;
    } else {
        small.innerText = "";
        input.style.borderColor = "#44ff44";
        input.style.boxShadow = "0 0 10px rgba(68,255,68,0.3)";
        return true;
    }
}

// Smooth scrolling for anchor links (except project-links)
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        if (!this.classList.contains('project-link')) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        }
    });
});

// Navbar background on scroll
window.addEventListener('scroll', () => {
    const navbar = document.querySelector('.navbar');
    if (window.scrollY > 100) {
        navbar.style.background = 'rgba(10,10,10,0.98)';
        navbar.style.backdropFilter = 'blur(20px)';
    } else {
        navbar.style.background = 'rgba(10,10,10,0.95)';
        navbar.style.backdropFilter = 'blur(10px)';
    }
});

// Parallax effect
window.addEventListener('scroll', () => {
    const scrolled = window.pageYOffset;
    const parallax = document.querySelector('.bg-animation');
    if (parallax) {
        parallax.style.transform = `translateY(${scrolled * 0.5}px)`;
    }
});

// Mouse cursor trail
const cursor = document.createElement('div');
cursor.className = 'custom-cursor';
document.body.appendChild(cursor);

document.addEventListener('mousemove', (e) => {
    cursor.style.left = e.clientX - 10 + 'px';
    cursor.style.top = e.clientY - 10 + 'px';
});

// Profile photo fallback
document.addEventListener('DOMContentLoaded', () => {
    const photo = document.getElementById('profile-photo');
    if (photo) {
        photo.onerror = () => {
            photo.src = 'https://via.placeholder.com/300x350/8a2be2/fff?text=Mayank+Rane';
        };
    }
});

// Contact form (simple)
const contactForm = document.getElementById('contactForm');
if (contactForm) {
    contactForm.addEventListener('submit', (e) => {
        e.preventDefault();
        alert('Thank you for your message! I will get back to you soon.');
    });
}

// Skills cards animation on scroll
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.style.opacity = '1';
            entry.target.style.transform = 'translateY(0)';
        }
    });
}, observerOptions);

// Observe all cards
document.querySelectorAll('.card, .project').forEach(el => {
    el.style.opacity = '0';
    el.style.transform = 'translateY(30px)';
    el.style.transition = 'all 0.6s ease';
    observer.observe(el);
});

console.log("Portfolio fully loaded with all features! 🚀");

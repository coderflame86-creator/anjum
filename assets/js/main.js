// assets/js/main.js - Interactive behaviors for Anjum Iqbal Website

document.addEventListener('DOMContentLoaded', () => {
    // 1. Mobile Menu Toggle
    const mobileBtn = document.getElementById('mobileMenuBtn');
    const navLinks = document.getElementById('navLinks');

    if (mobileBtn && navLinks) {
        mobileBtn.addEventListener('click', () => {
            const isOpen = navLinks.classList.toggle('open');
            mobileBtn.setAttribute('aria-expanded', isOpen);
            mobileBtn.innerHTML = isOpen 
                ? '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>'
                : '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 6h16M4 12h16M4 18h16"/></svg>';
        });

        // Close on link click
        navLinks.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth <= 768) {
                    navLinks.classList.remove('open');
                    mobileBtn.setAttribute('aria-expanded', 'false');
                    mobileBtn.innerHTML = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 6h16M4 12h16M4 18h16"/></svg>';
                }
            });
        });

        // Close on tap outside
        document.addEventListener('click', (e) => {
            if (navLinks.classList.contains('open') && !navLinks.contains(e.target) && !mobileBtn.contains(e.target)) {
                navLinks.classList.remove('open');
                mobileBtn.setAttribute('aria-expanded', 'false');
                mobileBtn.innerHTML = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 6h16M4 12h16M4 18h16"/></svg>';
            }
        });
    }

    // 2. Smooth Scroll for internal hash links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const targetId = this.getAttribute('href');
            if (targetId === '#' || targetId === '') return;
            const targetEl = document.querySelector(targetId);
            if (targetEl) {
                e.preventDefault();
                targetEl.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // 3. Contact Form Submission (Asynchronous with fallback)
    const contactForm = document.getElementById('contactForm');
    const formAlert = document.getElementById('formAlert');

    if (contactForm && formAlert) {
        contactForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const submitBtn = contactForm.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;

            submitBtn.disabled = true;
            submitBtn.innerHTML = 'Sending message...';
            formAlert.style.display = 'none';

            const formData = new FormData(contactForm);

            try {
                const response = await fetch('contact_submit.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    formAlert.className = 'form-alert success';
                    formAlert.innerHTML = '<strong>Thank you!</strong> ' + result.message;
                    formAlert.style.display = 'block';
                    contactForm.reset();
                } else {
                    formAlert.className = 'form-alert error';
                    formAlert.innerHTML = '<strong>Error:</strong> ' + (result.message || 'Unable to send message.');
                    formAlert.style.display = 'block';
                }
            } catch (err) {
                // If JSON fetch fails, submit traditionally
                formAlert.className = 'form-alert error';
                formAlert.innerHTML = '<strong>Notice:</strong> Please try again or email directly at contact.anjum3@gmail.com.';
                formAlert.style.display = 'block';
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
            }
        });
    }
});

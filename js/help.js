// js/help.js

// Collapse/Expand FAQ functionality
document.addEventListener('DOMContentLoaded', function() {
    const collapseButtons = document.querySelectorAll('.collapse-btn');
    
    collapseButtons.forEach(button => {
        button.addEventListener('click', function() {
            const content = this.nextElementSibling;
            
            // Toggle the content
            if (content.style.maxHeight) {
                // Close it
                content.style.maxHeight = null;
                content.style.padding = '0 18px';
            } else {
                // Open it
                content.style.maxHeight = content.scrollHeight + "px";
                content.style.padding = '10px 18px 18px 18px';
            }
        });
    });

    // Handle support form submission
    const supportForm = document.getElementById('supportForm');
    
    if (supportForm) {
        supportForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const msgEl = document.getElementById('supportMsg');
            const sendBtn = document.getElementById('sendBtn');
            
            // Show loading state
            msgEl.textContent = 'Sending...';
            msgEl.style.color = '#1b3d6d';
            msgEl.style.fontWeight = '600';
            sendBtn.disabled = true;
            sendBtn.textContent = 'Sending...';

            const formData = new FormData(this);

            try {
                const response = await fetch(this.action, {
                    method: 'POST',
                    body: formData
                });

                // Check if response is ok
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const result = await response.json();

                if (result.success) {
                    msgEl.textContent = '✓ Message sent successfully! We will get back to you soon.';
                    msgEl.style.color = '#10b981';
                    this.reset(); // Clear the form
                    
                    // Hide success message after 5 seconds
                    setTimeout(() => {
                        msgEl.textContent = '';
                    }, 5000);
                } else {
                    msgEl.textContent = '✗ ' + (result.error || 'Failed to send message. Please try again.');
                    msgEl.style.color = '#dc2626';
                }
            } catch (error) {
                console.error('Form submission error:', error);
                msgEl.textContent = '✗ Network error. Please check your connection and try again.';
                msgEl.style.color = '#dc2626';
            } finally {
                sendBtn.disabled = false;
                sendBtn.textContent = 'Send Message';
            }
        });
    }
});
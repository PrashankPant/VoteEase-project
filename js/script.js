
// Add form validation
document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('form');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(event) {
            const requiredFields = form.querySelectorAll('[required]');
            
            requiredFields.forEach(field => {
                if (!field.value) {
                    event.preventDefault();
                    alert('Please fill in all required fields');
                    return;
                }
            });
            
            if (form.querySelector('input[type="datetime-local"]')) {
                const startTime = form.querySelector('[name="start_time"]').value;
                const endTime = form.querySelector('[name="end_time"]').value;
                
                if (startTime >= endTime) {
                    event.preventDefault();
                    alert('End time must be after start time');
                    return;
                }
            }
        });
    });
});

// Add password strength validation
const passwordFields = document.querySelectorAll('input[type="password"]');
passwordFields.forEach(field => {
    field.addEventListener('input', function() {
        if (this.value.length < 6) {
            this.setCustomValidity('Password must be at least 6 characters long');
        } else {
            this.setCustomValidity('');
        }
    });
});
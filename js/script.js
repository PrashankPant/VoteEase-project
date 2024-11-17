
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

document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('form');
    const startTimeInput = document.querySelector('input[name="start_time"]');
    const endTimeInput = document.querySelector('input[name="end_time"]');
    const errorDiv = document.createElement('div');
    errorDiv.classList.add('error');
    form.insertBefore(errorDiv, form.firstChild);

    form.addEventListener('submit', function (e) {
        errorDiv.textContent = ''; // Clear previous errors
        const now = new Date();
        const startTime = new Date(startTimeInput.value);
        const endTime = new Date(endTimeInput.value);

        // Validation
        if (startTime < now) {
            e.preventDefault();
            errorDiv.textContent = 'Start time cannot be in the past.';
            return;
        }
        if (endTime <= now) {
            e.preventDefault();
            errorDiv.textContent = 'End time cannot be in the past.';
            return;
        }
        if (startTime >= endTime) {
            e.preventDefault();
            errorDiv.textContent = 'End time must be later than start time.';
            return;
        }
    });
});

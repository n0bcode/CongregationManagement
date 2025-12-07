import './bootstrap';
import './smart-form';
import Alpine from 'alpinejs';

// Make Alpine available globally
window.Alpine = Alpine;

// Alpine.js Components
Alpine.data('toast', () => ({
    show: false,
    message: '',
    type: 'success',
    
    showToast(message, type = 'success', duration = 3000) {
        this.message = message;
        this.type = type;
        this.show = true;
        
        setTimeout(() => {
            this.show = false;
        }, duration);
    }
}));

// Celebration animation helper
window.celebrateMilestone = function() {
    // Simple confetti-like animation using CSS
    const celebration = document.createElement('div');
    celebration.className = 'fixed inset-0 pointer-events-none z-50';
    celebration.innerHTML = `
        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2">
            <div class="text-6xl animate-bounce">🎉</div>
        </div>
    `;
    document.body.appendChild(celebration);
    
    setTimeout(() => {
        celebration.remove();
    }, 2000);
};

// Form helpers
window.confirmDelete = function(message = 'Are you sure you want to delete this item?') {
    return confirm(message);
};

// Loading state helper
window.showLoading = function(button) {
    button.disabled = true;
    button.innerHTML = '<span class="loading-spinner mr-2"></span> Processing...';
};

// Start Alpine
Alpine.start();

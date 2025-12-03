/**
 * Celebration animations for milestone events
 * Following UX Spec: "Celebrate the Lifecycle"
 */

export function celebrateMilestone(options = {}) {
    const {
        duration = 2000,
        particleCount = 50,
        colors = ['#D4AF37', '#10B981', '#334155'] // Sanctuary colors
    } = options;

    // Create celebration container
    const container = document.createElement('div');
    container.className = 'fixed inset-0 pointer-events-none z-50 overflow-hidden';
    document.body.appendChild(container);

    // Create particles
    for (let i = 0; i < particleCount; i++) {
        createParticle(container, colors);
    }

    // Remove after animation
    setTimeout(() => {
        container.remove();
    }, duration);
}

function createParticle(container, colors) {
    const particle = document.createElement('div');
    const color = colors[Math.floor(Math.random() * colors.length)];
    const size = Math.random() * 10 + 5;
    const startX = Math.random() * window.innerWidth;
    const startY = -20;
    const endX = startX + (Math.random() - 0.5) * 200;
    const endY = window.innerHeight + 20;
    const duration = Math.random() * 2000 + 1000;
    const delay = Math.random() * 500;

    particle.style.cssText = `
        position: absolute;
        width: ${size}px;
        height: ${size}px;
        background: ${color};
        border-radius: 50%;
        left: ${startX}px;
        top: ${startY}px;
        opacity: 0.8;
        animation: fall ${duration}ms ease-in ${delay}ms forwards;
    `;

    container.appendChild(particle);

    // Animate
    setTimeout(() => {
        particle.style.transform = `translate(${endX - startX}px, ${endY - startY}px) rotate(${Math.random() * 360}deg)`;
        particle.style.opacity = '0';
        particle.style.transition = `all ${duration}ms ease-in`;
    }, delay);
}

export function showSuccessToast(message, duration = 3000) {
    const toast = document.createElement('div');
    toast.className = 'fixed top-4 right-4 z-50 bg-emerald-500 text-white px-6 py-4 rounded-lg shadow-lg flex items-center space-x-3 animate-slide-in';
    toast.innerHTML = `
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <span>${message}</span>
    `;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(100%)';
        setTimeout(() => toast.remove(), 300);
    }, duration);
}

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

// Smart Pagination Component
Alpine.data('smartPagination', (config) => ({
    currentPage: config.currentPage,
    lastPage: config.lastPage,
    perPage: config.perPage,
    total: config.total,
    jumpToPageInput: '',
    loading: false,
    showInfiniteScroll: config.showInfiniteScroll,
    
    init() {
        // Load saved page size from localStorage
        const savedPerPage = localStorage.getItem('pagination_perPage');
        if (savedPerPage) {
            this.perPage = parseInt(savedPerPage);
        }
        
        // Restore scroll position if coming back
        if (config.showInfiniteScroll) {
            const savedScroll = sessionStorage.getItem('scroll_position');
            if (savedScroll) {
                setTimeout(() => {
                    window.scrollTo(0, parseInt(savedScroll));
                    sessionStorage.removeItem('scroll_position');
                }, 100);
            }
        }
    },
    
    get visiblePages() {
        const pages = [];
        const delta = 2; // Number of pages to show on each side of current
        
        for (let i = 1; i <= this.lastPage; i++) {
            if (
                i === 1 || // First page
                i === this.lastPage || // Last page
                (i >= this.currentPage - delta && i <= this.currentPage + delta) // Around current
            ) {
                pages.push(i);
            } else if (pages[pages.length - 1] !== '...') {
                pages.push('...');
            }
        }
        
        return pages;
    },
    
    goToPage(page) {
        if (page < 1 || page > this.lastPage || page === this.currentPage) {
            return;
        }
        
        // Save scroll position for infinite scroll
        if (this.showInfiniteScroll) {
            sessionStorage.setItem('scroll_position', window.scrollY.toString());
        }
        
        // Update via Livewire
        this.$wire.set('page', page);
        
        // Scroll to top
        if (!this.showInfiniteScroll) {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    },
    
    changePageSize() {
        // Save to localStorage
        localStorage.setItem('pagination_perPage', this.perPage.toString());
        
        // Update via Livewire (assuming there's a perPage property)
        if (this.$wire.perPage !== undefined) {
            this.$wire.set('perPage', this.perPage);
        }
        
        // Reset to page 1
        this.goToPage(1);
    },
    
    jumpToPage() {
        const page = parseInt(this.jumpToPageInput);
        if (!isNaN(page) && page >= 1 && page <= this.lastPage) {
            this.goToPage(page);
            this.jumpToPageInput = '';
        }
    },
    
    async loadMore() {
        if (this.loading || this.currentPage >= this.lastPage) {
            return;
        }
        
        this.loading = true;
        
        try {
            await this.$wire.loadMore();
        } catch (error) {
            console.error('Failed to load more:', error);
        } finally {
            this.loading = false;
        }
    }
}));

// Enhanced Search Component
Alpine.data('enhancedSearch', (config) => ({
    query: '',
    searching: false,
    showDropdown: false,
    selectedIndex: -1,
    recentSearches: [],
    suggestions: config.suggestions || [],
    minChars: config.minChars || 2,
    
    init() {
        // Load recent searches from localStorage
        const stored = localStorage.getItem('recentSearches');
        if (stored) {
            try {
                this.recentSearches = JSON.parse(stored);
            } catch (e) {
                this.recentSearches = [];
            }
        }
        
        // Watch for query changes
        this.$watch('query', (value) => {
            if (value.length >= this.minChars) {
                this.searching = true;
                // Searching state will be cleared by Livewire response
                setTimeout(() => {
                    this.searching = false;
                }, 500);
            }
        });
    },
    
    get filteredSuggestions() {
        if (this.query.length < this.minChars) {
            return [];
        }
        
        const lowerQuery = this.query.toLowerCase();
        return this.suggestions.filter(s => 
            s.toLowerCase().includes(lowerQuery)
        ).slice(0, 5);
    },
    
    onInput() {
        if (this.query.length >= this.minChars) {
            this.showDropdown = true;
            this.selectedIndex = -1;
        }
    },
    
    selectSuggestion(suggestion) {
        this.query = suggestion;
        this.$wire.set(config.model, suggestion);
        this.showDropdown = false;
        this.addToRecentSearches(suggestion);
        
        // Focus back on input
        this.$refs.input.focus();
    },
    
    addToRecentSearches(search) {
        if (!search || search.length < this.minChars) return;
        
        // Remove if already exists
        this.recentSearches = this.recentSearches.filter(s => s !== search);
        
        // Add to beginning
        this.recentSearches.unshift(search);
        
        // Keep only last 5
        this.recentSearches = this.recentSearches.slice(0, 5);
        
        // Save to localStorage
        localStorage.setItem('recentSearches', JSON.stringify(this.recentSearches));
    },
    
    clearSearch() {
        this.query = '';
        this.$wire.set(config.model, '');
        this.showDropdown = false;
        this.selectedIndex = -1;
        this.$refs.input.focus();
    },
    
    handleKeydown(event) {
        const suggestions = this.filteredSuggestions;
        
        if (event.key === 'ArrowDown') {
            event.preventDefault();
            this.selectedIndex = Math.min(this.selectedIndex + 1, suggestions.length - 1);
        } else if (event.key === 'ArrowUp') {
            event.preventDefault();
            this.selectedIndex = Math.max(this.selectedIndex - 1, -1);
        } else if (event.key === 'Enter' && this.selectedIndex >= 0) {
            event.preventDefault();
            this.selectSuggestion(suggestions[this.selectedIndex]);
        } else if (event.key === 'Escape') {
            this.showDropdown = false;
            this.selectedIndex = -1;
        }
    },
    
    highlightMatch(text, query) {
        if (!query || query.length < this.minChars) return text;
        
        const regex = new RegExp(`(${query})`, 'gi');
        return text.replace(regex, '<mark class="bg-amber-200 text-slate-900 font-medium">$1</mark>');
    }
}));

// Bulk Actions Component
Alpine.data('bulkActions', (initialCount = 0) => ({
    selectedCount: initialCount,
    processing: false,
    processed: 0,
    total: 0,
    progress: 0,
    completed: false,
    errors: [],
    cancelled: false,
    currentAction: null,
    
    init() {
        // Listen for selection changes
        this.$watch('selectedCount', (value) => {
            if (value === 0) {
                this.reset();
            }
        });
        
        // Listen for clear selection event
        this.$el.addEventListener('clear-selection', () => {
            this.selectedCount = 0;
            this.reset();
        });
    },
    
    async executeAction(method) {
        if (this.processing) return;
        
        this.processing = true;
        this.processed = 0;
        this.total = this.selectedCount;
        this.progress = 0;
        this.errors = [];
        this.cancelled = false;
        this.completed = false;
        this.currentAction = method;
        
        try {
            // Call the Livewire method
            if (this.$wire && typeof this.$wire[method] === 'function') {
                await this.$wire[method]();
                this.processed = this.total;
                this.progress = 100;
            } else {
                throw new Error(`Method ${method} not found`);
            }
        } catch (error) {
            this.errors.push({
                id: 'general',
                message: error.message || 'An error occurred'
            });
        } finally {
            this.processing = false;
            this.completed = true;
            
            // Auto-hide success message after 3 seconds
            if (this.errors.length === 0) {
                setTimeout(() => {
                    this.completed = false;
                    this.$dispatch('clear-selection');
                }, 3000);
            }
        }
    },
    
    cancelProcessing() {
        this.cancelled = true;
        this.processing = false;
        this.completed = true;
    },
    
    reset() {
        this.processing = false;
        this.processed = 0;
        this.total = 0;
        this.progress = 0;
        this.completed = false;
        this.errors = [];
        this.cancelled = false;
        this.currentAction = null;
    }
}));

// Inline Edit Component
Alpine.data('inlineEdit', (config) => ({
    editing: false,
    originalValue: config.value,
    editValue: config.value,
    saving: false,
    error: null,
    
    init() {
        // Watch for external value changes
        this.$watch('editValue', (value) => {
            if (this.editing && value !== this.originalValue) {
                this.error = null;
            }
        });
    },
    
    startEdit() {
        if (config.canEdit !== false) {
            this.editing = true;
            this.editValue = this.originalValue;
            this.error = null;
            
            // Focus input on next tick
            this.$nextTick(() => {
                const input = this.$refs.input || this.$refs.select;
                if (input) input.focus();
            });
        }
    },
    
    async save() {
        if (this.editValue === this.originalValue) {
            this.cancel();
            return;
        }
        
        this.saving = true;
        this.error = null;
        
        try {
            // Call the update method (Livewire or API)
            if (config.livewireMethod) {
                // Livewire method
                await this.$wire[config.livewireMethod](config.id, config.field, this.editValue);
            } else if (config.endpoint) {
                // API endpoint
                const response = await fetch(config.endpoint, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({
                        [config.field]: this.editValue
                    })
                });
                
                if (!response.ok) {
                    throw new Error('Update failed');
                }
            }
            
            // Update successful
            this.originalValue = this.editValue;
            this.editing = false;
            
            // Show success feedback
            if (config.onSuccess) {
                config.onSuccess(this.editValue);
            }
            
        } catch (err) {
            this.error = config.errorMessage || 'Failed to update. Please try again.';
            console.error('Inline edit error:', err);
        } finally {
            this.saving = false;
        }
    },
    
    cancel() {
        this.editValue = this.originalValue;
        this.editing = false;
        this.error = null;
    },
    
    handleKeydown(event) {
        if (event.key === 'Enter' && !event.shiftKey) {
            event.preventDefault();
            this.save();
        } else if (event.key === 'Escape') {
            event.preventDefault();
            this.cancel();
        }
    },
    
    get displayValue() {
        if (config.formatter) {
            return config.formatter(this.originalValue);
        }
        return this.originalValue || config.placeholder || '-';
    },
    
    get hasChanged() {
        return this.editValue !== this.originalValue;
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

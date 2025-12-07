document.addEventListener('alpine:init', () => {
    Alpine.data('smartForm', () => ({
        isSubmitting: false,
        hasUnsavedChanges: false,

        init() {
            window.addEventListener('beforeunload', (e) => {
                if (this.hasUnsavedChanges && !this.isSubmitting) {
                    e.preventDefault();
                    e.returnValue = '';
                }
            });
        }
    }));
});

document.addEventListener('alpine:init', () => {
    Alpine.data('smartForm', (config = {}) => ({
        isSubmitting: false,
        hasUnsavedChanges: false,
        errors: {},
        currentStep: 1,
        totalSteps: config.totalSteps || 1,
        formData: {},

        init() {
            // Unsaved changes warning
            window.addEventListener('beforeunload', (e) => {
                if (this.hasUnsavedChanges && !this.isSubmitting) {
                    e.preventDefault();
                    e.returnValue = '';
                }
            });

            // Watch for form changes
            this.$watch('formData', () => {
                this.hasUnsavedChanges = true;
            });
        },

        async validate(field, value, rules, id = null) {
            if (!rules) return;

            // Clear previous error
            delete this.errors[field];

            // Basic client-side required check
            if (rules.includes('required') && !value) {
                this.errors[field] = `The ${field.replace('_', ' ')} field is required.`;
                return;
            }

            // Server-side validation
            try {
                const response = await fetch('/api/validate', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ field, value, rules, id })
                });

                const data = await response.json();

                if (!data.valid) {
                    this.errors[field] = data.message;
                }
            } catch (error) {
                console.error('Validation error:', error);
            }
        },

        hasError(field) {
            return !!this.errors[field];
        },

        getError(field) {
            return this.errors[field];
        },

        nextStep() {
            if (this.currentStep < this.totalSteps) {
                // Optional: Validate current step fields before moving
                this.currentStep++;
            }
        },

        prevStep() {
            if (this.currentStep > 1) {
                this.currentStep--;
            }
        },

        goToStep(step) {
            if (step >= 1 && step <= this.totalSteps) {
                this.currentStep = step;
            }
        },

        submit() {
            this.isSubmitting = true;
            // Allow form submission to proceed
        }
    }));
});

<x-app-layout>
    <x-slot name="header">
        <h2 class="text-3xl font-bold text-stone-800">
            {{ __('AI Project Manager') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900" id="wizard-app">
                    
                    <!-- Notification Area -->
                    <div id="notification" class="hidden mb-4 p-4 rounded-md"></div>

                    <!-- STEP 1: Input -->
                    <div id="step-input">
                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-900">Describe your project</h3>
                            <p class="text-sm text-gray-500 mb-2">Tell the AI what you want to build. Be specific about features, platforms, and goals.</p>
                            <textarea id="input-description" rows="6" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="e.g. Build a CRM system for a real estate agency with client management, property listings, and email automation..."></textarea>
                        </div>
                        <div class="flex justify-end">
                            <button onclick="generatePlan()" class="inline-flex items-center justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                                Generate Plan with AI
                            </button>
                        </div>
                    </div>

                    <!-- Step 1b: Manual API Key Input (if missing) -->
                    @if(!$hasApiKey)
                    <div id="api-key-section" class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-md">
                        <label for="manual-api-key" class="block text-sm font-medium text-yellow-800">Google Gemini API Key Required</label>
                        <p class="text-xs text-yellow-700 mb-2">
                            System does not have a configured API Key. 
                            <a href="https://aistudio.google.com/app/apikey" target="_blank" class="underline font-bold">Get a key here</a>.
                        </p>
                        <input type="password" id="manual-api-key" class="mt-1 block w-full rounded-md border-yellow-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 sm:text-sm" placeholder="Paste your AI Studio Key (AIzaSy...)">
                    </div>
                    @endif

                    <!-- STEP 2: Loading -->
                    <div id="step-loading" class="hidden py-12 text-center">
                        <div class="inline-block animate-spin rounded-full h-12 w-12 border-4 border-indigo-500 border-t-transparent"></div>
                        <h3 class="mt-4 text-lg font-medium text-gray-900">Consulting AI Architect...</h3>
                        <p class="text-gray-500">Drafting tasks, estimating timelines, and organizing epics.</p>
                    </div>

                    <!-- STEP 3: Review -->
                    <div id="step-review" class="hidden">
                        <div class="border-b pb-4 mb-4">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Review Project Plan</h3>
                            <div class="grid grid-cols-1 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Project Name</label>
                                    <input type="text" id="review-name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Executive Summary</label>
                                    <textarea id="review-description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="mb-6">
                            <h4 class="text-md font-medium text-gray-800 mb-3">Proposed Tasks & Epics</h4>
                            <div id="tasks-container" class="space-y-3">
                                <!-- Tasks will be injected here -->
                            </div>
                        </div>

                        <div class="flex justify-end space-x-3 pt-4 border-t">
                            <button onclick="resetWizard()" class="inline-flex justify-center rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                Cancel & Retry
                            </button>
                            <button onclick="saveProject()" id="btn-save" class="inline-flex justify-center rounded-md border border-transparent bg-green-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                                Approve & Create Project
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script>
        let currentPlan = null;

        function showStep(stepId) {
            ['step-input', 'step-loading', 'step-review'].forEach(id => {
                document.getElementById(id).classList.add('hidden');
            });
            document.getElementById(stepId).classList.remove('hidden');
        }

        function showNotification(message, type = 'error') {
            const el = document.getElementById('notification');
            el.className = `mb-4 p-4 rounded-md \${type === 'error' ? 'bg-red-50 text-red-700' : 'bg-green-50 text-green-700'}`;
            el.innerText = message;
            el.classList.remove('hidden');
        }

        async function generatePlan() {
            const description = document.getElementById('input-description').value;
            if (description.length < 10) {
                showNotification('Please provide a longer description (at least 10 characters).');
                return;
            }

            // UI State -> Loading
            showStep('step-loading');
            document.getElementById('notification').classList.add('hidden');

            try {
                const apiKeyInput = document.getElementById('manual-api-key');
                const apiKey = apiKeyInput ? apiKeyInput.value : null;

                const response = await fetch("{{ route('projects.ai.generate') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({ 
                        description,
                        api_key: apiKey
                    })
                });

                const result = await response.json();

                if (!result.success) throw new Error(result.message || 'Unknown error');

                // Success
                currentPlan = result.data;
                renderReview();
                showStep('step-review');

            } catch (error) {
                console.error(error);
                showNotification(error.message);
                showStep('step-input');
            }
        }

        function renderReview() {
            document.getElementById('review-name').value = currentPlan.project_name || '';
            document.getElementById('review-description').value = currentPlan.description || '';
            
            const container = document.getElementById('tasks-container');
            container.innerHTML = '';

            currentPlan.tasks.forEach((task, index) => {
                const isEpic = task.type === 'epic';
                const div = document.createElement('div');
                div.className = `flex items-center space-x-4 p-3 rounded-md border \${isEpic ? 'bg-indigo-50 border-indigo-200' : 'bg-white border-gray-200'}`;
                
                div.innerHTML = `
                    <div class="flex-1">
                        <input type="text" value="\${task.title}" onchange="updateTask(\${index}, 'title', this.value)" 
                            class="block w-full border-0 bg-transparent p-0 text-sm focus:ring-0 \${isEpic ? 'font-bold text-indigo-700' : 'text-gray-900'}">
                    </div>
                    <div class="w-24">
                        <select onchange="updateTask(\${index}, 'type', this.value)" class="block w-full border-none bg-transparent py-0 pl-0 pr-7 text-gray-500 focus:ring-0 sm:text-xs">
                            <option value="epic" \${task.type === 'epic' ? 'selected' : ''}>Epic</option>
                            <option value="story" \${task.type === 'story' ? 'selected' : ''}>Story</option>
                            <option value="task" \${task.type === 'task' ? 'selected' : ''}>Task</option>
                            <option value="bug" \${task.type === 'bug' ? 'selected' : ''}>Bug</option>
                        </select>
                    </div>
                    <div class="w-24">
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium \${getPriorityClass(task.priority)}">
                            \${task.priority}
                        </span>
                    </div>
                `;
                container.appendChild(div);
            });
        }

        function updateTask(index, field, value) {
            if(currentPlan && currentPlan.tasks[index]) {
                currentPlan.tasks[index][field] = value;
            }
        }

        function getPriorityClass(priority) {
            switch(priority) {
                case 'high': return 'bg-red-100 text-red-800';
                case 'urgent': return 'bg-red-200 text-red-900';
                case 'medium': return 'bg-yellow-100 text-yellow-800';
                default: return 'bg-green-100 text-green-800';
            }
        }

        async function saveProject() {
            const btn = document.getElementById('btn-save');
            const originalText = btn.innerText;
            btn.disabled = true;
            btn.innerText = 'Saving...';

            // Gather Data
            const payload = {
                project_name: document.getElementById('review-name').value,
                description: document.getElementById('review-description').value,
                tasks: currentPlan.tasks
            };

            try {
                const response = await fetch("{{ route('projects.ai.store') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    body: JSON.stringify(payload)
                });

                const result = await response.json();

                if (!result.success) throw new Error(result.message || 'Save failed');

                window.location.href = result.redirect_url;

            } catch (error) {
                showNotification(error.message);
                btn.disabled = false;
                btn.innerText = originalText;
            }
        }

        function resetWizard() {
            if(confirm('Are you sure you want to discard this plan?')) {
                currentPlan = null;
                showStep('step-input');
            }
        }
    </script>
</x-app-layout>

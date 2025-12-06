<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h3 class="text-lg font-medium text-gray-900">Project Members</h3>
        <button onclick="document.getElementById('invite-member-modal').showModal()" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
            Invite Member
        </button>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-md">
        <ul role="list" class="divide-y divide-gray-200">
            @forelse($project->members as $member)
                <li>
                    <div class="px-4 py-4 sm:px-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <img class="h-10 w-10 rounded-full" src="https://ui-avatars.com/api/?name={{ urlencode($member->first_name . ' ' . $member->last_name) }}" alt="">
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-indigo-600 truncate">
                                        {{ $member->first_name }} {{ $member->last_name }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $member->email ?? 'No email' }}
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center space-x-4">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $member->pivot->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ ucfirst($member->pivot->status) }}
                                </span>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                    {{ ucfirst($member->pivot->role) }}
                                </span>
                                
                                <form action="{{ route('projects.members.destroy', [$project, $member->pivot->id]) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 text-sm">Remove</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </li>
            @empty
                <li class="px-4 py-4 sm:px-6 text-center text-gray-500">
                    No members found. Invite someone to get started!
                </li>
            @endforelse
        </ul>
    </div>

    <!-- Invite Modal -->
    <dialog id="invite-member-modal" class="modal p-0 rounded-lg shadow-xl w-full max-w-md backdrop:bg-gray-500/50">
        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <div class="sm:flex sm:items-start">
                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                        Invite Member
                    </h3>
                    <div class="mt-2">
                        <form action="{{ route('projects.members.store', $project) }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <label for="member_id" class="block text-sm font-medium text-gray-700">Select Member</label>
                                <select name="member_id" id="member_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                    @foreach(\App\Models\Member::whereDoesntHave('projects', function($q) use ($project) { $q->where('project_id', $project->id); })->get() as $potentialMember)
                                        <option value="{{ $potentialMember->id }}">
                                            {{ $potentialMember->first_name }} {{ $potentialMember->last_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-4">
                                <label for="role" class="block text-sm font-medium text-gray-700">Role</label>
                                <select name="role" id="role" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                    <option value="member">Member</option>
                                    <option value="admin">Admin</option>
                                    <option value="viewer">Viewer</option>
                                </select>
                            </div>
                            <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                                <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                                    Invite
                                </button>
                                <button type="button" onclick="document.getElementById('invite-member-modal').close()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </dialog>
</div>

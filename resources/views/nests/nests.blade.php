<x-app-layout :assets="$assets ?? []">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex flex-wrap align-items-center justify-content-between">
                        <div class="d-flex flex-wrap align-items-center">
                            <div class="profile-img position-relative me-3 mb-3 mb-lg-0">
                                <img src="{{ $nest->profile_image ? asset('storage/nests/profiles/' . $nest->profile_image) : asset('images/avatars/01.png') }}"
                                    alt="User-Profile" class="theme-color-default-img img-fluid rounded-pill avatar-100">
                            </div>
                            <div class="d-flex flex-wrap align-items-center mb-3 mb-sm-0">
                                <h4 class="me-2 h3">n/{{ $nest->name }}</h4>
                            </div>
                        </div>
                        <div class="d-flex gap-2 mb-3">
                            @if ($canCreate && $isJoined)
                                <!-- Create Post Button triggers modal -->
                                <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal"
                                    data-bs-target="#createPostModal">Create Post</button>
                                <!-- Modal for Create Post Form -->
                                <div class="modal fade" id="createPostModal" tabindex="-1"
                                    aria-labelledby="createPostModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="createPostModalLabel">Create Post</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <form method="POST"
                                                action="{{ route('posts.store', ['nest' => $nest->name]) }}"
                                                enctype="multipart/form-data">
                                                @csrf
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label for="title" class="form-label">Title <span
                                                                class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" id="title"
                                                            name="title" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="description" class="form-label">Description</label>
                                                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="media" class="form-label">Media</label>
                                                        <input type="file" class="form-control" id="media"
                                                            name="media" accept="image/*,video/*">
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-primary">Submit</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            @if ($isOwner || (isset($isModerator) && $isModerator))
                                <button type="button" class="btn btn-outline-info" data-bs-toggle="modal"
                                    data-bs-target="#manageMembersModal">Manage Members</button>
                                <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal"
                                    data-bs-target="#moderatePostsModal">Moderate Posts</button>
                            @endif
                            @if ($isOwner)
                                <a href="{{ route('nests.edit', $nest->name) }}" class="btn btn-outline-warning">Edit
                                    Nest</a>
                                <form method="POST" action="{{ route('nests.destroy', $nest->name) }}"
                                    style="display:inline;"
                                    onsubmit="return confirm('Are you sure you want to delete this nest? This action cannot be undone.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger">Delete Nest</button>
                                </form>
                            @endif
                            <div class="modal fade" id="moderatePostsModal" tabindex="-1" aria-labelledby="moderatePostsModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="moderatePostsModalLabel">Moderate Posts</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <table id="postsTable" class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Title</th>
                                                        <th>Author</th>
                                                        <th>Created</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($nest->posts as $post)
                                                        <tr>
                                                            <td>{{ $post->title }}</td>
                                                            <td>{{ $post->user->username ?? $post->user->name ?? $post->user->email }}</td>
                                                            <td>{{ $post->created_at->format('M d, Y H:i') }}</td>
                                                            <td>
                                                                <form method="POST" action="{{ route('nests.posts.destroy', [$nest->name, $post->id]) }}" class="delete-post-form" style="display:inline;">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="button" class="btn btn-sm btn-danger delete-post-btn">Delete</button>
                                                                </form>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal fade" id="manageMembersModal" tabindex="-1"
                                aria-labelledby="manageMembersModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="manageMembersModalLabel">Nest Members</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <table id="membersTable" class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Username</th>
                                                        <th>Role</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($nest->users as $member)
                                                        <tr>
                                                            <td>{{ $member->username ?? ($member->name ?? $member->email) }}
                                                            </td>
                                                            <td>
                                                                @if ($member->id === $nest->owner_id)
                                                                    Owner
                                                                @elseif($nest->moderators->contains($member->id))
                                                                    Moderator
                                                                @else
                                                                    Member
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if ($isOwner && $member->id !== $nest->owner_id)
                                                                    @if (!$nest->moderators->contains($member->id))
                                                                        <form method="POST"
                                                                            action="{{ route('nests.promote', [$nest->name, $member->id]) }}"
                                                                            style="display:inline;">
                                                                            @csrf
                                                                            <button type="submit"
                                                                                class="btn btn-sm btn-success">Make
                                                                                Moderator</button>
                                                                        </form>
                                                                    @endif
                                                                    <form method="POST"
                                                                        action="{{ route('nests.kick', [$nest->name, $member->id]) }}"
                                                                        style="display:inline;" class="kick-form">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="button"
                                                                            class="btn btn-sm btn-danger kick-btn">Kick</button>
                                                                    </form>
                                                                @elseif(isset($isModerator) && $isModerator && $member->id !== $nest->owner_id && !$nest->moderators->contains($member->id))
                                                                    <form method="POST"
                                                                        action="{{ route('nests.kick', [$nest->name, $member->id]) }}"
                                                                        style="display:inline;"
                                                                        onsubmit="return confirm('Kick this member?');">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit"
                                                                            class="btn btn-sm btn-danger">Kick</button>
                                                                    </form>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @if (isset($isBanned) && $isBanned)
                                <div class="alert alert-danger mb-0">You are banned from this nest</div>
                            @elseif($isJoined == 0)
                                @if ($canJoin)
                                    <button type="button" class="btn btn-primary" id="join-nest-btn" data-nest="{{ $nest->name }}">Join</button>
                                @else
                                    <a href="{{ route('register') }}" class="btn btn-primary">Join</a>
                                @endif
                            @else
                                @if ($isOwner)
                                    <button type="button" class="btn btn-outline-danger" id="leave-nest-btn" data-nest="{{ $nest->name }}">Leave Nest</button>
                                @else
                                    <button type="button" class="btn btn-outline-primary" id="join-nest-btn" data-nest="{{ $nest->name }}">Joined</button>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="card sticky-top" style="top: 80px;">
                <div class="card-header">
                    <div class="header-title">
                        <h4 class="card-title">About</h4>
                    </div>
                </div>
                <div class="card-body">
                    <p>{{ $nest->description }}</p>
                    <div class="mb-1">Created {{ \Carbon\Carbon::parse($nest->created_at)->format('M d, Y') }}</div>
                    {{-- <div class="mb-1">Phone: <a href="#" class="ms-3">001 2351 256 12</a></div> --}}
                    {{-- <div>Location: <span class="ms-3">USA</span></div> --}}
                    <hr class="hr-horizontal my-5">
                    <div class="mb-1 text-center">
                        <div class="fw-bold" style="font-size:1.5rem;line-height:1;">{{ $memberCount }}</div>
                        <div class="text-muted" style="font-size:1rem;">Member</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="profile-content tab-content">
                <div class="card">
                    <div class="card-header d-flex p-3">
                        <select id="sort-dropdown" class="form-select w-auto ms-2">
                            <option value="best" selected>Best</option>
                            <option value="latest">Latest</option>
                        </select>
                    </div>
                </div>
                <div id="posts-container" class="tab-pane fade active show">
                    {{-- Post moderation modal (delete only) --}}
                    <div class="modal fade" id="deletePostModal" tabindex="-1" aria-labelledby="deletePostModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="deletePostModalLabel">Delete Post</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    Are you sure you want to delete this post? This action cannot be undone.
                                </div>
                                <div class="modal-footer">
                                    <form id="deletePostForm" method="POST" action="#">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-danger">Delete</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="loading" style="display:none;text-align:center;">Loading...</div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="card sticky-top" style="top: 80px;">
                <div class="card-header">
                    <div class="header-title">
                        <h4 class="card-title">Moderators</h4>
                    </div>
                </div>
                <div class="card-body">
                    <ul class="list-inline m-0 p-0">
                        <li class="d-flex mb-4 align-items-center">
                            <div class="img-fluid bg-soft-warning rounded-pill">
                                <img src="{{ asset('images/icons/05.png') }}" alt="owner-img"
                                    class="rounded-pill avatar-40">
                            </div>
                            <div class="ms-3 flex-grow-1">
                                <h6>{{ $ownerUsername }}</h6>
                                <p class="mb-0">Nest Owner</p>
                            </div>
                        </li>
                        {{-- Moderators --}}
                        @foreach ($moderatorUsernames as $moderatorUsername)
                            <li class="d-flex mb-4 align-items-center">
                                <div class="img-fluid bg-soft-primary rounded-pill">
                                    <img src="{{ asset('images/icons/07.png') }}" alt="moderator-img"
                                        class="rounded-pill avatar-40">
                                </div>
                                <div class="ms-3 flex-grow-1">
                                    <h6>{{ $moderatorUsername }}</h6>
                                    <p class="mb-0">Moderator</p>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>

<script src="{{ asset('js/nest-posts.js') }}"></script>
<script src="{{ asset('js/nest-detail.js') }}"></script>
<script>
    window.nestName = @json($nest->name);
</script>

@include('partials.components.share-offcanvas')
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Join/Leave button logic
    const joinBtn = document.getElementById('join-nest-btn');
    if (joinBtn) {
        joinBtn.addEventListener('click', function() {
            joinBtn.disabled = true;
            fetch(`/nests/${joinBtn.dataset.nest}/join`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
            })
            .then(res => {
                if (res.status === 401) {
                    window.location.href = '/login?redirect=' + encodeURIComponent(window.location.href);
                    return;
                }
                if (!res.ok) throw new Error('Failed to join/leave');
                return res.json();
            })
            .then(data => {
                if (!data) return;
                if (data.message && data.message.includes('Left')) {
                    // If owner left, page may need reload for ownership transfer
                    window.location.reload();
                } else if (data.message && data.message.includes('Joined')) {
                    window.location.reload();
                } else if (data.message && data.message.includes('Nest deleted')) {
                    window.location.href = '/';
                }
            })
            .catch(() => {
                joinBtn.disabled = false;
                alert('Failed to join/leave nest.');
            });
        });
    }

    // Post moderation: delete post (owner/moderator only)
    // This assumes posts are rendered dynamically in #posts-container, so delegate event
    @if ($isOwner || (isset($isModerator) && $isModerator))
    document.addEventListener('click', function(e) {
        if (e.target && e.target.classList.contains('delete-post-btn')) {
            e.preventDefault();
            const postId = e.target.getAttribute('data-post-id');
            const form = document.getElementById('deletePostForm');
            form.action = `/posts/${postId}`;
            const modal = new bootstrap.Modal(document.getElementById('deletePostModal'));
            modal.show();
        }
    });
    @endif

    // Leave nest for owner: must transfer ownership first
    const leaveBtn = document.getElementById('leave-nest-btn');
    if (leaveBtn) {
        leaveBtn.addEventListener('click', function() {
            Swal.fire({
                title: 'Transfer Ownership',
                text: 'Leaving will transfer ownership to a moderator or member. Continue?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Leave and Transfer',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    leaveBtn.disabled = true;
                    fetch(`/nests/${leaveBtn.dataset.nest}/join`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        },
                    })
                    .then(res => {
                        if (res.status === 401) {
                            window.location.href = '/login?redirect=' + encodeURIComponent(window.location.href);
                            return;
                        }
                        if (!res.ok) throw new Error('Failed to leave');
                        return res.json();
                    })
                    .then(data => {
                        if (!data) return;
                        if (data.message && data.message.includes('Nest deleted')) {
                            window.location.href = '/';
                        } else {
                            window.location.reload();
                        }
                    })
                    .catch(() => {
                        leaveBtn.disabled = false;
                        alert('Failed to leave nest.');
                    });
                }
            });
        });
    }
});
</script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let postsModal = document.getElementById('moderatePostsModal');
            if (postsModal) {
                postsModal.addEventListener('shown.bs.modal', function() {
                    if (!$.fn.DataTable.isDataTable('#postsTable')) {
                        $('#postsTable').DataTable({
                            searching: true,
                            paging: false,
                            info: false
                        });
                    }
                });
            }
            // Delete post confirmation
            document.querySelectorAll('.delete-post-btn').forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Are you sure?',
                        text: 'This will delete the post permanently.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Yes, delete post',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            btn.closest('form').submit();
                        }
                    });
                });
            });
        });
    </script>
    <script>
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let modal = document.getElementById('manageMembersModal');
            modal.addEventListener('shown.bs.modal', function() {
                if (!$.fn.DataTable.isDataTable('#membersTable')) {
                    $('#membersTable').DataTable({
                        searching: true,
                        paging: false,
                        info: false
                    });
                }
            });
        });
    </script>
@endpush
</x-app-layout>
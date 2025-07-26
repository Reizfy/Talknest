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
                            @if($canCreate)
                                <!-- Create Post Button triggers modal -->
                                <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#createPostModal">Create Post</button>
                                <!-- Modal for Create Post Form -->
                                <div class="modal fade" id="createPostModal" tabindex="-1" aria-labelledby="createPostModalLabel" aria-hidden="true">
                                  <div class="modal-dialog">
                                    <div class="modal-content">
                                      <div class="modal-header">
                                        <h5 class="modal-title" id="createPostModalLabel">Create Post</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                      </div>
                                      <form method="POST" action="{{ route('posts.store', ['nest' => $nest->name]) }}" enctype="multipart/form-data">
                                        @csrf
                                        <div class="modal-body">
                                          <div class="mb-3">
                                            <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="title" name="title" required>
                                          </div>
                                          <div class="mb-3">
                                            <label for="description" class="form-label">Description</label>
                                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                                          </div>
                                          <div class="mb-3">
                                            <label for="media" class="form-label">Media</label>
                                            <input type="file" class="form-control" id="media" name="media" accept="image/*,video/*">
                                          </div>
                                        </div>
                                        <div class="modal-footer">
                                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                          <button type="submit" class="btn btn-primary">Submit</button>
                                        </div>
                                      </form>
                                    </div>
                                  </div>
                                </div>
                            @else
                                <a href="{{ route('register') }}" class="btn btn-outline-primary">Create Post</a>
                            @endif
                            @if($isOwner || (isset($isModerator) && $isModerator))
                                <button type="button" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#manageMembersModal">Manage Members</button>
                            @endif
                            @if($isOwner)
                                <a href="{{ route('nests.edit', $nest->name) }}" class="btn btn-outline-warning">Edit Nest</a>
                                <form method="POST" action="{{ route('nests.destroy', $nest->name) }}" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this nest? This action cannot be undone.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger">Delete Nest</button>
                                </form>
                            @endif
                            <!-- Manage Members Modal -->
                            <div class="modal fade" id="manageMembersModal" tabindex="-1" aria-labelledby="manageMembersModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="manageMembersModalLabel">Nest Members</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
                                                    @foreach($nest->users as $member)
                                                        <tr>
                                                            <td>{{ $member->username ?? $member->name ?? $member->email }}</td>
                                                            <td>
                                                                @if($member->id === $nest->owner_id)
                                                                    Owner
                                                                @elseif($nest->moderators->contains($member->id))
                                                                    Moderator
                                                                @else
                                                                    Member
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if($isOwner && $member->id !== $nest->owner_id)
                                                                    @if(!$nest->moderators->contains($member->id))
                                                                        <form method="POST" action="{{ route('nests.promote', [$nest->name, $member->id]) }}" style="display:inline;">
                                                                            @csrf
                                                                            <button type="submit" class="btn btn-sm btn-success">Make Moderator</button>
                                                                        </form>
                                                                    @endif
                                                                    <form method="POST" action="{{ route('nests.kick', [$nest->name, $member->id]) }}" style="display:inline;" onsubmit="return confirm('Kick this member?');">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit" class="btn btn-sm btn-danger">Kick</button>
                                                                    </form>
                                                                @elseif(isset($isModerator) && $isModerator && $member->id !== $nest->owner_id && !$nest->moderators->contains($member->id))
                                                                    <form method="POST" action="{{ route('nests.kick', [$nest->name, $member->id]) }}" style="display:inline;" onsubmit="return confirm('Kick this member?');">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit" class="btn btn-sm btn-danger">Kick</button>
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
                            @if($isJoined == 0)
                                @if($canJoin)
                                    <button type="button" class="btn btn-primary">Join</button>
                                @else
                                    <a href="{{ route('register') }}" class="btn btn-primary">Join</a>
                                @endif
                            @else
                                <button type="button" class="btn btn-outline-primary">Joined</button>
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
                                <img src="{{ asset('images/icons/05.png') }}" alt="owner-img" class="rounded-pill avatar-40">
                            </div>
                            <div class="ms-3 flex-grow-1">
                                <h6>{{ $ownerUsername }}</h6>
                                <p class="mb-0">Nest Owner</p>
                            </div>
                            <a href="javascript:void(0);" class="btn btn-outline-primary rounded-circle btn-icon btn-sm p-2">
                                <span class="btn-inner">
                                <svg class="icon-32" width="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">                                    <path d="M10.33 16.5928H4.0293" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>                                    <path d="M13.1406 6.90042H19.4413" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M8.72629 6.84625C8.72629 5.5506 7.66813 4.5 6.36314 4.5C5.05816 4.5 4 5.5506 4 6.84625C4 8.14191 5.05816 9.19251 6.36314 9.19251C7.66813 9.19251 8.72629 8.14191 8.72629 6.84625Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M20.0002 16.5538C20.0002 15.2581 18.9429 14.2075 17.6379 14.2075C16.3321 14.2075 15.2739 15.2581 15.2739 16.5538C15.2739 17.8494 16.3321 18.9 17.6379 18.9C18.9429 18.9 20.0002 17.8494 20.0002 16.5538Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>                                </svg>                            
                                </span>
                            </a>
                        </li>
                        {{-- Moderators --}}
                        @foreach($moderatorUsernames as $moderatorUsername)
                        <li class="d-flex mb-4 align-items-center">
                            <div class="img-fluid bg-soft-primary rounded-pill">
                                <img src="{{ asset('images/icons/07.png') }}" alt="moderator-img" class="rounded-pill avatar-40">
                            </div>
                            <div class="ms-3 flex-grow-1">
                                <h6>{{ $moderatorUsername }}</h6>
                                <p class="mb-0">Moderator</p>
                            </div>
                            <a href="javascript:void(0);" class="btn btn-outline-primary rounded-circle btn-icon btn-sm p-2">
                                <span class="btn-inner">
                                <svg class="icon-32" width="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">                                    <path d="M10.33 16.5928H4.0293" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>                                    <path d="M13.1406 6.90042H19.4413" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M8.72629 6.84625C8.72629 5.5506 7.66813 4.5 6.36314 4.5C5.05816 4.5 4 5.5506 4 6.84625C4 8.14191 5.05816 9.19251 6.36314 9.19251C7.66813 9.19251 8.72629 8.14191 8.72629 6.84625Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M20.0002 16.5538C20.0002 15.2581 18.9429 14.2075 17.6379 14.2075C16.3321 14.2075 15.2739 15.2581 15.2739 16.5538C15.2739 17.8494 16.3321 18.9 17.6379 18.9C18.9429 18.9 20.0002 17.8494 20.0002 16.5538Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>                                </svg>                            
                                </span>
                            </a>
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
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        let modal = document.getElementById('manageMembersModal');
        modal.addEventListener('shown.bs.modal', function () {
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

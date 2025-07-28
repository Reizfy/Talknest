<x-app-layout>
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">Superadmin - Comments Management</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="datatable-comments" class="table table-striped" data-toggle="data-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Post</th>
                                    <th>Author</th>
                                    <th>Content</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($comment as $c)
                                    <tr>
                                        <td>{{ $c->id }}</td>
                                        <td>{{ $c->post->title }}</td>
                                        <td>{{ $c->user->username }}</td>
                                        <td>{{ $c->content }}</td>
                                        <td>{{ $c->created_at->format('Y-m-d') }}</td>
                                        <td style="display: flex; gap: 5px;">
                                            <form action="{{ route('superadmin.comments.destroy',['id'=>$c->id]) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
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
    </div>
</x-app-layout>

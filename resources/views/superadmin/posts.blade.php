<x-app-layout>
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">Superadmin - Posts Management</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="datatable-posts" class="table table-striped" data-toggle="data-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Title</th>
                                    <th>Nest</th>
                                    <th>Author</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($post as $p)
                                    <tr>
                                        <td>{{ $p->id }}</td>
                                        <td>{{ $p->title }}</td>
                                        <td>{{ $p->nest->name }}</td>
                                        <td>{{ $p->user->username }}</td>
                                        <td>{{ $p->created_at->format('Y-m-d') }}</td>
                                        <td style="display: flex; gap: 5px;">
                                            <button class="btn btn-sm btn-primary" onclick="window.location.href='{{ route('superadmin.posts.edit', ['id'=>$p->id]) }}'">Edit</button>
                                            <form action="{{ route('superadmin.posts.destroy',['id'=>$p->id]) }}" method="POST">
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

<x-app-layout>
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">Superadmin - Nests Management</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="datatable-nests" class="table table-striped" data-toggle="data-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Owner</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($nest as $n)
                                    <tr>
                                        <td>{{ $n->id }}</td>
                                        <td>{{ $n->name }}</td>
                                        <td>{{ $n->owner->username }}</td>
                                        <td>{{ $n->created_at->format('Y-m-d') }}</td>
                                        <td style="display: flex; gap: 5px;">
                                            <button class="btn btn-sm btn-primary" onclick="window.location.href='{{ route('superadmin.nests.edit', ['id'=>$n->id]) }}'">Edit</button>
                                            <form action="{{ route('superadmin.nests.destroy',['id'=>$n->id]) }}" method="POST">
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

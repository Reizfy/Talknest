<x-app-layout :assets="$assets ?? []">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Edit Nest</div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('superadmin.nests.update', $nest->id) }}">
                            @csrf
                            @method('PUT')
                            <div class="form-group mb-3">
                                <label for="name">Nest Name</label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ $nest->name }}" required>
                            </div>
                            <button type="submit" class="btn btn-warning">Update Nest</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('partials.components.share-offcanvas')
</x-app-layout>

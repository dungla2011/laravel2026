@extends('testmongo1.layout')

@section('title', 'TestMongo1 List')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-list"></i> TestMongo1 Records
                </h5>
                <a href="{{ route('testmongo1.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Create New
                </a>
            </div>
            <div class="card-body">
                <!-- Search and Filter Form -->
                <form method="GET" action="{{ route('testmongo1.index') }}" class="search-form">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" 
                                       class="form-control" 
                                       name="search" 
                                       value="{{ request('search') }}" 
                                       placeholder="Search by name, email, or phone...">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <select name="sort_by" class="form-select">
                                <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Created Date</option>
                                <option value="name" {{ request('sort_by') == 'name' ? 'selected' : '' }}>Name</option>
                                <option value="email" {{ request('sort_by') == 'email' ? 'selected' : '' }}>Email</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="sort_order" class="form-select">
                                <option value="desc" {{ request('sort_order') == 'desc' ? 'selected' : '' }}>Descending</option>
                                <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>Ascending</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="per_page" class="form-select">
                                <option value="10" {{ request('per_page') == '10' ? 'selected' : '' }}>10 per page</option>
                                <option value="25" {{ request('per_page') == '25' ? 'selected' : '' }}>25 per page</option>
                                <option value="50" {{ request('per_page') == '50' ? 'selected' : '' }}>50 per page</option>
                                <option value="100" {{ request('per_page') == '100' ? 'selected' : '' }}>100 per page</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-outline-primary">
                                    <i class="fas fa-search"></i> Search
                                </button>
                                <a href="{{ route('testmongo1.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times"></i> Clear
                                </a>
                            </div>
                        </div>
                    </div>
                </form>

                <!-- Results Info -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="text-muted">
                        Showing {{ $testMongo1s->firstItem() ?? 0 }} to {{ $testMongo1s->lastItem() ?? 0 }} 
                        of {{ $testMongo1s->total() }} results
                    </div>
                    <div class="text-muted">
                        Page {{ $testMongo1s->currentPage() }} of {{ $testMongo1s->lastPage() }}
                    </div>
                </div>

                <!-- Data Table -->
                @if($testMongo1s->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="10%">Image</th>
                                    <th width="20%">Name</th>
                                    <th width="25%">Email</th>
                                    <th width="15%">Phone</th>
                                    <th width="15%">Created Date</th>
                                    <th width="10%">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($testMongo1s as $index => $item)
                                    <tr>
                                        <td>{{ $testMongo1s->firstItem() + $index }}</td>
                                        <td>
                                            @if($item->image)
                                                <img src="{{ asset('storage/' . $item->image) }}" 
                                                     alt="Image" 
                                                     class="image-preview rounded"
                                                     data-bs-toggle="modal" 
                                                     data-bs-target="#imageModal{{ $item->_id }}"
                                                     style="cursor: pointer;">
                                                
                                                <!-- Image Modal -->
                                                <div class="modal fade" id="imageModal{{ $item->_id }}" tabindex="-1">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">{{ $item->name }}'s Image</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body text-center">
                                                                <img src="{{ asset('storage/' . $item->image) }}" 
                                                                     alt="Image" 
                                                                     class="img-fluid">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="bg-light rounded d-flex align-items-center justify-content-center image-preview">
                                                    <i class="fas fa-user text-muted"></i>
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <strong>{{ $item->name }}</strong>
                                        </td>
                                        <td>
                                            <a href="mailto:{{ $item->email }}" class="text-decoration-none">
                                                {{ $item->email }}
                                            </a>
                                        </td>
                                        <td>
                                            @if($item->phone)
                                                <a href="tel:{{ $item->phone }}" class="text-decoration-none">
                                                    {{ $item->phone }}
                                                </a>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                {{ $item->created_at ? $item->created_at->format('d/m/Y H:i') : '-' }}
                                            </small>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('testmongo1.show', $item->_id) }}" 
                                                   class="btn btn-sm btn-outline-info btn-action"
                                                   title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('testmongo1.edit', $item->_id) }}" 
                                                   class="btn btn-sm btn-outline-warning btn-action"
                                                   title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-danger btn-action"
                                                        title="Delete"
                                                        onclick="confirmDelete('{{ route('testmongo1.destroy', $item->_id) }}', 'Delete {{ $item->name }}?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="pagination-info text-muted">
                            Showing {{ $testMongo1s->firstItem() }} to {{ $testMongo1s->lastItem() }} 
                            of {{ $testMongo1s->total() }} entries
                        </div>
                        <div>
                            {{ $testMongo1s->appends(request()->query())->links() }}
                        </div>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No records found</h5>
                        <p class="text-muted">
                            @if(request('search'))
                                No records match your search criteria.
                                <a href="{{ route('testmongo1.index') }}" class="text-decoration-none">Clear search</a>
                            @else
                                Get started by creating your first record.
                            @endif
                        </p>
                        <a href="{{ route('testmongo1.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Create First Record
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Auto-submit form when sort or per_page changes
        $('select[name="sort_by"], select[name="sort_order"], select[name="per_page"]').change(function() {
            $(this).closest('form').submit();
        });

        // Clear search on Escape key
        $('input[name="search"]').keyup(function(e) {
            if (e.keyCode === 27) { // Escape key
                $(this).val('');
            }
        });

        // Highlight search terms
        @if(request('search'))
            const searchTerm = '{{ request('search') }}';
            if (searchTerm) {
                $('table tbody').highlight(searchTerm, {
                    className: 'bg-warning'
                });
            }
        @endif
    });

    // Simple highlight function
    jQuery.fn.highlight = function(str, options) {
        var regex = new RegExp(str, "gi");
        return this.each(function() {
            $(this).contents().filter(function() {
                return this.nodeType == 3 && regex.test(this.nodeValue);
            }).replaceWith(function() {
                return (this.nodeValue || "").replace(regex, function(match) {
                    return "<span class='" + (options.className || 'highlight') + "'>" + match + "</span>";
                });
            });
        });
    };
</script>
@endsection 
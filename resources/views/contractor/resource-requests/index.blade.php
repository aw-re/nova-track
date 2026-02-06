@extends('layouts.app')

@section('title', 'Resource Requests - CPMS')

@section('page_title', 'Resource Requests')



@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Resource Requests</h1>
        <a href="{{ route('contractor.resource-requests.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Create New Request
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs" id="requestTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button" role="tab" aria-controls="all" aria-selected="true">All</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button" role="tab" aria-controls="pending" aria-selected="false">Pending</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="approved-tab" data-bs-toggle="tab" data-bs-target="#approved" type="button" role="tab" aria-controls="approved" aria-selected="false">Approved</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="rejected-tab" data-bs-toggle="tab" data-bs-target="#rejected" type="button" role="tab" aria-controls="rejected" aria-selected="false">Rejected</button>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content" id="requestTabsContent">
                <div class="tab-pane fade show active" id="all" role="tabpanel" aria-labelledby="all-tab">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Project</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Requested Date</th>
                                    <th>Required By</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $allRequests = collect()->merge($pendingRequests ?? [])->merge($fulfilledRequests ?? [])->merge($rejectedRequests ?? []);
                                @endphp
                                @forelse($allRequests as $request)
                                    <tr>
                                        <td>{{ $request->resource_name ?? 'N/A' }}</td>
                                        <td>{{ ucfirst(str_replace('_', ' ', $request->resource_type ?? 'N/A')) }}</td>
                                        <td>
                                                <
                                        </td>
                                        
                                        <td>
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No resource requests found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade" id="pending" role="tabpanel" aria-labelledby="pending-tab">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Project</th>
                                    <th>Type</th>
                                    <th>Requested Date</th>
                                    <th>Required By</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pendingRequests ?? [] as $request)
                                    <tr>
                                        <td>{{ $request->resource_name ?? 'N/A' }}</td>
                                        <td>{{ $request->project && is_object($request->project) ? $request->project->name : 'N/A' }}</td>
                                        <td>{{ ucfirst(str_replace('_', ' ', $request->resource_type ?? 'N/A')) }}</td>
                                        <td>{{ $request->created_at ? date('M d, Y', strtotime($request->created_at)) : 'N/A' }}</td>
                                        <td>{{ $request->required_by ? date('M d, Y', strtotime($request->required_by)) : 'N/A' }}</td>
                                        <td>
                                            <a href="{{ route('contractor.resource-requests.show', $request) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">No pending resource requests found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if(isset($pendingRequests) && $pendingRequests->hasPages())
                        <div class="mt-4">
                            {{ $pendingRequests->appends(['fulfilled_page' => request('fulfilled_page'), 'rejected_page' => request('rejected_page')])->links() }}
                        </div>
                    @endif
                </div>
                <div class="tab-pane fade" id="approved" role="tabpanel" aria-labelledby="approved-tab">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Project</th>
                                    <th>Type</th>
                                    <th>Approved Date</th>
                                    <th>Required By</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($fulfilledRequests ?? [] as $request)
                                    <tr>
                                        <td>{{ $request->resource_name ?? 'N/A' }}</td>
                                        <td>{{ $request->project && is_object($request->project) ? $request->project->name : 'N/A' }}</td>
                                        <td>{{ ucfirst(str_replace('_', ' ', $request->resource_type ?? 'N/A')) }}</td>
                                        <td>{{ $request->updated_at ? date('M d, Y', strtotime($request->updated_at)) : 'N/A' }}</td>
                                        <td>{{ $request->required_by ? date('M d, Y', strtotime($request->required_by)) : 'N/A' }}</td>
                                        <td>
                                            <a href="{{ route('contractor.resource-requests.show', $request) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">No approved resource requests found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if(isset($fulfilledRequests) && $fulfilledRequests->hasPages())
                        <div class="mt-4">
                            {{ $fulfilledRequests->appends(['pending_page' => request('pending_page'), 'rejected_page' => request('rejected_page')])->links() }}
                        </div>
                    @endif
                </div>
                <div class="tab-pane fade" id="rejected" role="tabpanel" aria-labelledby="rejected-tab">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Project</th>
                                    <th>Type</th>
                                    <th>Rejected Date</th>
                                    <th>Required By</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($rejectedRequests ?? [] as $request)
                                    <tr>
                                        <td>{{ $request->resource_name ?? 'N/A' }}</td>
                                        <td>{{ $request->project && is_object($request->project) ? $request->project->name : 'N/A' }}</td>
                                        <td>{{ ucfirst(str_replace('_', ' ', $request->resource_type ?? 'N/A')) }}</td>
                                        <td>{{ $request->updated_at ? date('M d, Y', strtotime($request->updated_at)) : 'N/A' }}</td>
                                        <td>{{ $request->required_by ? date('M d, Y', strtotime($request->required_by)) : 'N/A' }}</td>
                                        <td>
                                            <a href="{{ route('contractor.resource-requests.show', $request) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">No rejected resource requests found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if(isset($rejectedRequests) && $rejectedRequests->hasPages())
                        <div class="mt-4">
                            {{ $rejectedRequests->appends(['pending_page' => request('pending_page'), 'fulfilled_page' => request('fulfilled_page')])->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Any resource request-specific JavaScript can go here
    </script>
@endsection
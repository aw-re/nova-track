@extends('layouts.app')

@section('title', 'Resource Requests - CPMS')

@section('page_title', 'Resource Requests')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Resource Requests</h1>
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
                <!-- تبويب الكل -->
                <div class="tab-pane fade show active" id="all" role="tabpanel" aria-labelledby="all-tab">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Project</th>
                                    <th>Requested By</th>
                                    <th>Resource Type</th>
                                    <th>Status</th>
                                    <th>Requested Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($resourceRequests as $request)
                                    <tr>
                                        <td>{{ $request->title }}</td>
                                        <td>{{ $request->project->name }}</td>
                                        <td>{{ $request->requestedBy->name }}</td>
                                        <td>{{ ucfirst(str_replace('_', ' ', $request->resource_type)) }}</td>
                                        <td>
                                            @if($request->status == 'pending')
                                                <span class="badge bg-warning">Pending</span>
                                            @elseif($request->status == 'approved')
                                                <span class="badge bg-success">Approved</span>
                                            @elseif($request->status == 'rejected')
                                                <span class="badge bg-danger">Rejected</span>
                                            @endif
                                        </td>
                                        <td>{{ date('M d, Y', strtotime($request->created_at)) }}</td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('owner.resource-requests.show', $request) }}" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @if($request->status == 'pending')
                                                    <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#approveModal{{ $request->id }}">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $request->id }}">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                @endif
                                            </div>
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

                <!-- تبويب المعلقة -->
                <div class="tab-pane fade" id="pending" role="tabpanel" aria-labelledby="pending-tab">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Project</th>
                                    <th>Requested By</th>
                                    <th>Resource Type</th>
                                    <th>Requested Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $pendingRequests = $resourceRequests->where('status', 'pending');
                                @endphp
                                @forelse($pendingRequests as $request)
                                    <tr>
                                        <td>{{ $request->title }}</td>
                                        <td>{{ $request->project->name }}</td>
                                        <td>{{ $request->requestedBy->name }}</td>
                                        <td>{{ ucfirst(str_replace('_', ' ', $request->resource_type)) }}</td>
                                        <td>{{ date('M d, Y', strtotime($request->created_at)) }}</td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('owner.resource-requests.show', $request) }}" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#approveModal{{ $request->id }}">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $request->id }}">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
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
                </div>

                <!-- تبويب الموافق عليها -->
                <div class="tab-pane fade" id="approved" role="tabpanel" aria-labelledby="approved-tab">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Project</th>
                                    <th>Requested By</th>
                                    <th>Resource Type</th>
                                    <th>Approved Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $approvedRequests = $resourceRequests->where('status', 'approved');
                                @endphp
                                @forelse($approvedRequests as $request)
                                    <tr>
                                        <td>{{ $request->title }}</td>
                                        <td>{{ $request->project->name }}</td>
                                        <td>{{ $request->requestedBy->name }}</td>
                                        <td>{{ ucfirst(str_replace('_', ' ', $request->resource_type)) }}</td>
                                        <td>{{ date('M d, Y', strtotime($request->approved_at ?? $request->updated_at)) }}</td>
                                        <td>
                                            <a href="{{ route('owner.resource-requests.show', $request) }}" class="btn btn-sm btn-primary">
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
                </div>

                <!-- تبويب المرفوضة -->
                <div class="tab-pane fade" id="rejected" role="tabpanel" aria-labelledby="rejected-tab">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Project</th>
                                    <th>Requested By</th>
                                    <th>Resource Type</th>
                                    <th>Rejected Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $rejectedRequests = $resourceRequests->where('status', 'rejected');
                                @endphp
                                @forelse($rejectedRequests as $request)
                                    <tr>
                                        <td>{{ $request->title }}</td>
                                        <td>{{ $request->project->name }}</td>
                                        <td>{{ $request->requestedBy->name }}</td>
                                        <td>{{ ucfirst(str_replace('_', ' ', $request->resource_type)) }}</td>
                                        <td>{{ date('M d, Y', strtotime($request->rejected_at ?? $request->updated_at)) }}</td>
                                        <td>
                                            <a href="{{ route('owner.resource-requests.show', $request) }}" class="btn btn-sm btn-primary">
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
                </div>
            </div>
        </div>
    </div>

    <!-- نماذج الموافقة والرفض -->
    @foreach($resourceRequests as $request)
        @if($request->status == 'pending')
            <!-- نموذج الموافقة -->
            <div class="modal fade" id="approveModal{{ $request->id }}" tabindex="-1" aria-labelledby="approveModalLabel{{ $request->id }}" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="approveModalLabel{{ $request->id }}">Approve Resource Request</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="{{ route('owner.resource-requests.approve', $request) }}" method="POST">
                            @csrf
                            <div class="modal-body">
                                <p>Are you sure you want to approve this resource request?</p>
                                <div class="mb-3">
                                    <label for="approval_comment{{ $request->id }}" class="form-label">Comment (Optional)</label>
                                    <textarea class="form-control" id="approval_comment{{ $request->id }}" name="comment" rows="3"></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-success">Approve</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- نموذج الرفض -->
            <div class="modal fade" id="rejectModal{{ $request->id }}" tabindex="-1" aria-labelledby="rejectModalLabel{{ $request->id }}" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="rejectModalLabel{{ $request->id }}">Reject Resource Request</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="{{ route('owner.resource-requests.reject', $request) }}" method="POST">
                            @csrf
                            <div class="modal-body">
                                <p>Are you sure you want to reject this resource request?</p>
                                <div class="mb-3">
                                    <label for="rejection_comment{{ $request->id }}" class="form-label">Reason for Rejection <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="rejection_comment{{ $request->id }}" name="comment" rows="3" required></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-danger">Reject</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    @endforeach
@endsection

@section('scripts')
    <script>
        // أي أكواد جافاسكريبت خاصة بصفحة طلبات الموارد
    </script>
@endsection

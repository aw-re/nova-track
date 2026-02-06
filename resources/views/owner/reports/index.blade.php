@extends('layouts.app')

@section('title', 'Reports - CPMS')

@section('page_title', 'Reports')



@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Reports</h1>
    </div>

    <div class="card">
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs" id="reportTabs" role="tablist">
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
            <div class="tab-content" id="reportTabsContent">
                <div class="tab-pane fade show active" id="all" role="tabpanel" aria-labelledby="all-tab">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Project</th>
                                    <th>Submitted By</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Submitted Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $allReports = collect()->merge($pendingReports)->merge($approvedReports)->merge($rejectedReports);
                                @endphp
                                @forelse($allReports as $report)
                                    <tr>
                                        <td>{{ is_object($report) ? $report->title : 'Unknown' }}</td>
                                        <td>{{ is_object($report) && $report->project ? $report->project->name : 'N/A' }}</td>
                                        <td>{{ is_object($report) && $report->createdBy ? $report->createdBy->name : 'N/A' }}</td>
                                        <td>{{ is_object($report) ? ucfirst(str_replace('_', ' ', $report->type)) : 'Unknown' }}</td>
                                        <td>
                                            @if(is_object($report))
                                                @if($report->status == 'pending')
                                                    <span class="badge bg-warning">Pending</span>
                                                @elseif($report->status == 'approved')
                                                    <span class="badge bg-success">Approved</span>
                                                @elseif($report->status == 'rejected')
                                                    <span class="badge bg-danger">Rejected</span>
                                                @endif
                                            @else
                                                <span class="badge bg-secondary">Unknown</span>
                                            @endif
                                        </td>
                                        <td>{{ is_object($report) ? date('M d, Y', strtotime($report->created_at)) : 'N/A' }}</td>
                                        <td>
                                            @if(is_object($report))
                                            <div class="btn-group">
                                                <a href="{{ route('owner.reports.show', $report) }}" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @if($report->status == 'pending')
                                                    <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#approveModal{{ $report->id }}">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $report->id }}">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                @endif
                                            </div>
                                            
                                            <!-- Approve Modal -->
                                            <div class="modal fade" id="approveModal{{ $report->id }}" tabindex="-1" aria-labelledby="approveModalLabel{{ $report->id }}" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="approveModalLabel{{ $report->id }}">Approve Report</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <form action="{{ route('owner.reports.approve', $report) }}" method="POST">
                                                            @csrf
                                                            <div class="modal-body">
                                                                <p>Are you sure you want to approve this report?</p>
                                                                <div class="mb-3">
                                                                    <label for="approval_comment" class="form-label">Comment (Optional)</label>
                                                                    <textarea class="form-control" id="approval_comment" name="comment" rows="3"></textarea>
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
                                            
                                            <!-- Reject Modal -->
                                            <div class="modal fade" id="rejectModal{{ $report->id }}" tabindex="-1" aria-labelledby="rejectModalLabel{{ $report->id }}" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="rejectModalLabel{{ $report->id }}">Reject Report</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <form action="{{ route('owner.reports.reject', $report) }}" method="POST">
                                                            @csrf
                                                            <div class="modal-body">
                                                                <p>Are you sure you want to reject this report?</p>
                                                                <div class="mb-3">
                                                                    <label for="rejection_comment" class="form-label">Reason for Rejection <span class="text-danger">*</span></label>
                                                                    <textarea class="form-control" id="rejection_comment" name="comment" rows="3" required></textarea>
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
                                            @else
                                                <span class="text-muted">No actions available</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No reports found.</td>
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
                                    <th>Submitted By</th>
                                    <th>Type</th>
                                    <th>Submitted Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pendingReports as $report)
                                    <tr>
                                        <td>{{ is_object($report) ? $report->title : 'Unknown' }}</td>
                                        <td>{{ is_object($report) && $report->project ? $report->project->name : 'N/A' }}</td>
                                        <td>{{ is_object($report) && $report->createdBy ? $report->createdBy->name : 'N/A' }}</td>
                                        <td>{{ is_object($report) ? ucfirst(str_replace('_', ' ', $report->type)) : 'Unknown' }}</td>
                                        <td>{{ is_object($report) ? date('M d, Y', strtotime($report->created_at)) : 'N/A' }}</td>
                                        <td>
                                            @if(is_object($report))
                                            <div class="btn-group">
                                                <a href="{{ route('owner.reports.show', $report) }}" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#approveModal{{ $report->id }}">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $report->id }}">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                            @else
                                                <span class="text-muted">No actions available</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">No pending reports found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade" id="approved" role="tabpanel" aria-labelledby="approved-tab">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Project</th>
                                    <th>Submitted By</th>
                                    <th>Type</th>
                                    <th>Approved Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($approvedReports as $report)
                                    <tr>
                                        <td>{{ is_object($report) ? $report->title : 'Unknown' }}</td>
                                        <td>{{ is_object($report) && $report->project ? $report->project->name : 'N/A' }}</td>
                                        <td>{{ is_object($report) && $report->createdBy ? $report->createdBy->name : 'N/A' }}</td>
                                        <td>{{ is_object($report) ? ucfirst(str_replace('_', ' ', $report->type)) : 'Unknown' }}</td>
                                        <td>{{ is_object($report) ? date('M d, Y', strtotime($report->approved_at ?? $report->updated_at)) : 'N/A' }}</td>
                                        <td>
                                            @if(is_object($report))
                                            <a href="{{ route('owner.reports.show', $report) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @else
                                            <span class="text-muted">No actions available</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">No approved reports found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade" id="rejected" role="tabpanel" aria-labelledby="rejected-tab">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Project</th>
                                    <th>Submitted By</th>
                                    <th>Type</th>
                                    <th>Rejected Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($rejectedReports as $report)
                                    <tr>
                                        <td>{{ is_object($report) ? $report->title : 'Unknown' }}</td>
                                        <td>{{ is_object($report) && $report->project ? $report->project->name : 'N/A' }}</td>
                                        <td>{{ is_object($report) && $report->createdBy ? $report->createdBy->name : 'N/A' }}</td>
                                        <td>{{ is_object($report) ? ucfirst(str_replace('_', ' ', $report->type)) : 'Unknown' }}</td>
                                        <td>{{ is_object($report) ? date('M d, Y', strtotime($report->rejected_at ?? $report->updated_at)) : 'N/A' }}</td>
                                        <td>
                                            @if(is_object($report))
                                            <a href="{{ route('owner.reports.show', $report) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @else
                                            <span class="text-muted">No actions available</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">No rejected reports found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="mt-4">
                <div class="row">
                    <div class="col-md-4">
                        {{ $pendingReports->appends(['approved_page' => $approvedReports->currentPage(), 'rejected_page' => $rejectedReports->currentPage()])->links() }}
                    </div>
                    <div class="col-md-4">
                        {{ $approvedReports->appends(['pending_page' => $pendingReports->currentPage(), 'rejected_page' => $rejectedReports->currentPage()])->links() }}
                    </div>
                    <div class="col-md-4">
                        {{ $rejectedReports->appends(['pending_page' => $pendingReports->currentPage(), 'approved_page' => $approvedReports->currentPage()])->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Any report-specific JavaScript can go here
    </script>
@endsection
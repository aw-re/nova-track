@extends('layouts.app')

@section('title', 'Reports - CPMS')

@section('page_title', 'Reports')

@section('sidebar')
    <li class="nav-item">
        <a class="nav-link" href="{{ route('engineer.dashboard') }}">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('engineer.projects.index') }}">
            <i class="fas fa-project-diagram"></i> Projects
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('engineer.tasks.index') }}">
            <i class="fas fa-tasks"></i> Tasks
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link active" href="{{ route('engineer.reports.index') }}">
            <i class="fas fa-file-alt"></i> Reports
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('engineer.resource-requests.index') }}">
            <i class="fas fa-tools"></i> Resource Requests
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('engineer.files.index') }}">
            <i class="fas fa-file"></i> Files
        </a>
    </li>
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Reports</h1>
        <a href="{{ route('engineer.reports.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Create New Report
        </a>
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
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Submitted Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($reports as $report)
                                    <tr>
                                        <td>{{ $report->title }}</td>
                                        <td>{{ $report->project->name }}</td>
                                        <td>{{ ucfirst(str_replace('_', ' ', $report->type)) }}</td>
                                        <td>
                                            @if($report->status == 'pending')
                                                <span class="badge bg-warning">Pending</span>
                                            @elseif($report->status == 'approved')
                                                <span class="badge bg-success">Approved</span>
                                            @elseif($report->status == 'rejected')
                                                <span class="badge bg-danger">Rejected</span>
                                            @endif
                                        </td>
                                        <td>{{ date('M d, Y', strtotime($report->created_at)) }}</td>
                                        <td>
                                            <a href="{{ route('engineer.reports.show', $report) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">No reports found.</td>
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
                                    <th>Submitted Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $pendingReports = $reports->where('status', 'pending');
                                @endphp
                                @forelse($pendingReports as $report)
                                    <tr>
                                        <td>{{ $report->title }}</td>
                                        <td>{{ $report->project->name }}</td>
                                        <td>{{ ucfirst(str_replace('_', ' ', $report->type)) }}</td>
                                        <td>{{ date('M d, Y', strtotime($report->created_at)) }}</td>
                                        <td>
                                            <a href="{{ route('engineer.reports.show', $report) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No pending reports found.</td>
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
                                    <th>Type</th>
                                    <th>Approved Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $approvedReports = $reports->where('status', 'approved');
                                @endphp
                                @forelse($approvedReports as $report)
                                    <tr>
                                        <td>{{ $report->title }}</td>
                                        <td>{{ $report->project->name }}</td>
                                        <td>{{ ucfirst(str_replace('_', ' ', $report->type)) }}</td>
                                        <td>{{ date('M d, Y', strtotime($report->approved_at ?? $report->updated_at)) }}</td>
                                        <td>
                                            <a href="{{ route('engineer.reports.show', $report) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No approved reports found.</td>
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
                                    <th>Type</th>
                                    <th>Rejected Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $rejectedReports = $reports->where('status', 'rejected');
                                @endphp
                                @forelse($rejectedReports as $report)
                                    <tr>
                                        <td>{{ $report->title }}</td>
                                        <td>{{ $report->project->name }}</td>
                                        <td>{{ ucfirst(str_replace('_', ' ', $report->type)) }}</td>
                                        <td>{{ date('M d, Y', strtotime($report->rejected_at ?? $report->updated_at)) }}</td>
                                        <td>
                                            <a href="{{ route('engineer.reports.show', $report) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No rejected reports found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            @if($reports->hasPages())
                <div class="mt-4">
                    {{ $reports->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Any report-specific JavaScript can go here
    </script>
@endsection

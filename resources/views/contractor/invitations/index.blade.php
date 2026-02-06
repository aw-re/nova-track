@extends('layouts.app')

@section('title', 'Project Invitations - CPMS')

@section('page_title', 'Project Invitations')



@section('content')
    <div class="container-fluid">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Pending Invitations</h5>
            </div>
            <div class="card-body">
                @if($pendingInvitations->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Project</th>
                                    <th>Invited By</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pendingInvitations as $invitation)
                                    <tr>
                                        <td>
                                            <strong>{{ $invitation->project->name }}</strong>
                                            <div class="text-muted small">{{ Str::limit($invitation->project->description, 50) }}</div>
                                        </td>
                                        <td>{{ $invitation->invitedBy->name }}</td>
                                        <td>{{ $invitation->created_at->format('M d, Y') }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <form action="{{ route('contractor.invitations.accept', $invitation) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Are you sure you want to accept this invitation?')">
                                                        <i class="fas fa-check"></i> Accept
                                                    </button>
                                                </form>
                                                <form action="{{ route('contractor.invitations.reject', $invitation) }}" method="POST" class="d-inline ms-1">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to reject this invitation?')">
                                                        <i class="fas fa-times"></i> Reject
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info">
                        You have no pending invitations.
                    </div>
                @endif
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Accepted Invitations</h5>
            </div>
            <div class="card-body">
                @if($acceptedInvitations->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Project</th>
                                    <th>Invited By</th>
                                    <th>Accepted Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($acceptedInvitations as $invitation)
                                    <tr>
                                        <td>
                                            <strong>{{ $invitation->project->name }}</strong>
                                            <div class="text-muted small">{{ Str::limit($invitation->project->description, 50) }}</div>
                                        </td>
                                        <td>{{ $invitation->invitedBy->name }}</td>
                                        <td>{{ $invitation->accepted_at->format('M d, Y') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info">
                        You have no accepted invitations.
                    </div>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Rejected Invitations</h5>
            </div>
            <div class="card-body">
                @if($rejectedInvitations->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Project</th>
                                    <th>Invited By</th>
                                    <th>Rejected Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($rejectedInvitations as $invitation)
                                    <tr>
                                        <td>
                                            <strong>{{ $invitation->project->name }}</strong>
                                            <div class="text-muted small">{{ Str::limit($invitation->project->description, 50) }}</div>
                                        </td>
                                        <td>{{ $invitation->invitedBy->name }}</td>
                                        <td>{{ $invitation->rejected_at->format('M d, Y') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info">
                        You have no rejected invitations.
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
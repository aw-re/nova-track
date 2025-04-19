@extends('layouts.app')

@section('title', $report->title . ' - تفاصيل التقرير')

@section('content')
<div class="container py-4">
    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="mb-0">
                    <i class="fas fa-file-alt"></i> {{ $report->title }}
                </h3>
                <span class="badge bg-white text-primary fs-6">
                    {{ $report->created_at->format('Y-m-d') }}
                </span>
            </div>
        </div>

        <div class="card-body">
            <!-- معلومات التقرير الأساسية -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="info-box">
                        <h5><i class="fas fa-project-diagram"></i> المشروع:</h5>
                        <p>{{ $report->project->name ?? 'غير محدد' }}</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-box">
                        <h5><i class="fas fa-user-tie"></i> مقدم التقرير:</h5>
                        <p>{{ $report->creator->name ?? 'غير معروف' }}</p>
                    </div>
                </div>
            </div>

            <!-- نوع التقرير وحالته -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="info-box">
                        <h5><i class="fas fa-tag"></i> نوع التقرير:</h5>
                        <span class="badge bg-{{ $report->type_color }} fs-6">
                            {{ $report->type_name }}
                        </span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-box">
                        <h5><i class="fas fa-info-circle"></i> الحالة:</h5>
                        <span class="badge bg-{{ $report->status_color }} fs-6">
                            {{ $report->status_name }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- محتوى التقرير -->
            <div class="mb-4 p-3 border rounded bg-light">
                <h4 class="border-bottom pb-2"><i class="fas fa-align-left"></i> المحتوى:</h4>
                <div class="py-2">
                    {!! nl2br(e($report->content)) !!}
                </div>
            </div>

            <!-- معلومات الإعتماد -->
            @if($report->status == 'approved')
            <div class="alert alert-success">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5><i class="fas fa-check-circle"></i> تم الاعتماد بواسطة:</h5>
                        <p class="mb-1">{{ $report->approver->name ?? '$report->approver->name ' }}</p>
                        <small class="text-muted">
                            في {{ $report->approved_at->format('Y-m-d H:i') }}
                        </small>
                    </div>
                </div>
            </div>
            @endif

            <!-- أزرار التحكم -->
            <div class="d-flex justify-content-between mt-4">
                <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> رجوع للقائمة
                </a>

                <div class="btn-group">
                    <a href="{{ route('admin.reports.edit', $report->id) }}" 
                       class="btn btn-primary">
                       <i class="fas fa-edit"></i> تعديل
                    </a>

                    @if($report->status == 'pending')
                    <form action="{{ route('admin.reports.approve', $report->id) }}" 
                          method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success mx-1">
                            <i class="fas fa-check"></i> اعتماد
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .info-box {
        background: #f8f9fa;
        padding: 1rem;
        border-radius: 5px;
        margin-bottom: 1rem;
    }
    .info-box h5 {
        color: #495057;
        font-size: 1rem;
        margin-bottom: 0.5rem;
    }
    .info-box p {
        margin-bottom: 0;
        font-size: 1.1rem;
    }
</style>
@endsection
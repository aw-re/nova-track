@extends('layouts.app')

@section('title', 'Resources Overview - CPMS')

@section('page_title', 'Resources Overview')



@section('content')
    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-tools me-2"></i> Materials</span>
                    <a href="{{ route('admin.resources.create', ['type' => 'material']) }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Add Material
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>Quantity</th>
                                    <th>Unit</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                    <tr>
                                        
                                        <td>
                                            <div class="btn-group" role="group">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>

                                            <!-- Delete Modal -->
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-danger">Delete</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" class="text-center">No materials found.</td>
                                    </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="d-flex justify-content-center mt-3">
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-truck me-2"></i> Equipment</span>
                    <a href="{{ route('admin.resources.create', ['type' => 'equipment']) }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Add Equipment
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                    <tr>
                                        
                                        <td>
                                            
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>

                                            <!-- Delete Modal -->
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-danger">Delete</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" class="text-center">No equipment found.</td>
                                    </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="d-flex justify-content-center mt-3">
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="fas fa-clipboard-list me-2"></i> Resource Requests</span>
                <i class="fas fa-list"></i> View All Requests
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Project</th>
                            <th>Requested By</th>
                            <th>Resource Type</th>
                            <th>Resource Name</th>
                            <th>Quantity</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                            <tr>
                                
                                <td>
                                   
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                                <i class="fas fa-check"></i>
                                            </button>
                                                <i class="fas fa-times"></i>
                                            </button>
                                    </div>

                                    <!-- Approve Modal -->
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                    @csrf
                                                    <div class="modal-body">
                                                        <p>Are you sure you want to approve this resource request?</p>
                                                        <div class="mb-3">
                                                            <label for="notes" class="form-label">Notes (Optional)</label>
                                                            <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
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
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                    @csrf
                                                    <div class="modal-body">
                                                        <p>Are you sure you want to reject this resource request?</p>
                                                        <div class="mb-3">
                                                            <label for="reason" class="form-label">Reason <span class="text-danger">*</span></label>
                                                            <textarea class="form-control" id="reason" name="reason" rows="3" required></textarea>
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
                                </td>
                            </tr>
                            <tr>
                                <td colspan="8" class="text-center">No resource requests found.</td>
                            </tr>
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-center mt-3">
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-chart-pie me-2"></i> Materials by Category
                </div>
                <div class="card-body">
                    <canvas id="materialsCategoryChart" width="400" height="300"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-chart-pie me-2"></i> Equipment Status
                </div>
                <div class="card-body">
                    <canvas id="equipmentStatusChart" width="400" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Materials by Category Chart
            const materialsCtx = document.getElementById('materialsCategoryChart').getContext('2d');
            
            
            const materialColors = [
                '#3498db',
                '#2ecc71',
                '#f39c12',
                '#e74c3c',
                '#9b59b6',
                '#1abc9c',
                '#d35400',
                '#34495e'
            ];
            
            // Calculate percentages for materials
            const materialTotal = materialCounts.reduce((acc, count) => acc + count, 0);
            const materialPercentages = materialCounts.map(count => ((count / materialTotal) * 100).toFixed(1));
            
            new Chart(materialsCtx, {
                type: 'pie',
                data: {
                    labels: materialCategories.map((category, index) => `${category}: ${materialPercentages[index]}%`),
                    datasets: [{
                        data: materialCounts,
                        backgroundColor: materialColors.slice(0, materialCategories.length),
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.raw || 0;
                                    const percentage = materialPercentages[context.dataIndex];
                                    return `${label.split(':')[0]}: ${value} (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });

            // Equipment Status Chart
            const equipmentCtx = document.getElementById('equipmentStatusChart').getContext('2d');
            
            const equipmentStatus = {
                'Available': {{ $equipmentByStatus['available'] ?? 0 }},
                'In Use': {{ $equipmentByStatus['in_use'] ?? 0 }},
                'Maintenance': {{ $equipmentByStatus['maintenance'] ?? 0 }},
                'Out of Order': {{ $equipmentByStatus['out_of_order'] ?? 0 }}
            };
            
            const statusColors = {
                'Available': '#28a745',
                'In Use': '#007bff',
                'Maintenance': '#ffc107',
                'Out of Order': '#dc3545'
            };
            
            // Calculate percentages for equipment status
            const equipmentValues = Object.values(equipmentStatus);
            const equipmentTotal = equipmentValues.reduce((acc, count) => acc + count, 0);
            const equipmentPercentages = equipmentValues.map(count => ((count / equipmentTotal) * 100).toFixed(1));
            
            new Chart(equipmentCtx, {
                type: 'pie',
                data: {
                    labels: Object.keys(equipmentStatus).map((status, index) => `${status}: ${equipmentPercentages[index]}%`),
                    datasets: [{
                        data: Object.values(equipmentStatus),
                        backgroundColor: Object.keys(equipmentStatus).map(status => statusColors[status]),
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.raw || 0;
                                    const percentage = equipmentPercentages[context.dataIndex];
                                    return `${label.split(':')[0]}: ${value} (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
@endsection
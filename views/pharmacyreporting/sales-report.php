<?php
$this->title = 'Sales Analytics Report - Pharmacy Reports';
?>

<div class="row" style="margin-top: 3%; min-height: 88vh; overflow: hidden;">
    <!-- Left Sidebar - Modules -->
    <div class="col-md-3" style="padding-right: 20px;">
        <?php include(__DIR__ . '/../pharmacy/_modules_sidebar.php') ?>
    </div>

    <!-- Main Content Area -->
    <div class="col-md-9" style="padding-left: 20px;">
        <!-- Header Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="mb-1" style="color: #232360; font-weight: 700;">
                            <i class="fas fa-chart-line me-2" style="color: #22C55E;"></i>
                            Sales Analytics Report
                        </h2>
                        <p class="text-muted mb-0">Sales performance, revenue analysis, and top-selling medicines</p>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-download me-1"></i> Export PDF
                        </button>
                        <button class="btn btn-outline-success btn-sm">
                            <i class="fas fa-file-excel me-1"></i> Export Excel
                        </button>
                        <a href="index.php?r=pharmacyreporting/dashboard" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left me-1"></i> Back to Reports
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Report Content -->
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm" style="border-radius: 16px;">
                    <div class="card-body p-4">
                        <div class="alert alert-info border-0"
                            style="border-radius: 12px; background: linear-gradient(135deg, #F0FDF4 0%, #DCFCE7 100%);">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Report Generated:</strong> <?= date('F j, Y \a\t g:i A') ?>
                        </div>

                        <div class="text-center py-5">
                            <i class="fas fa-chart-line"
                                style="font-size: 64px; color: #22C55E; margin-bottom: 20px;"></i>
                            <h4 style="color: #232360; margin-bottom: 16px;">Sales Analytics Report</h4>
                            <p style="color: #6B7280; margin-bottom: 24px;">
                                This report provides comprehensive sales analytics including revenue trends and
                                top-performing medicines.
                            </p>
                            <div class="alert alert-warning border-0"
                                style="border-radius: 12px; display: inline-block; padding: 12px 24px;">
                                <i class="fas fa-tools me-2"></i>
                                Report functionality is under development. Sales data will be populated from the
                                database.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
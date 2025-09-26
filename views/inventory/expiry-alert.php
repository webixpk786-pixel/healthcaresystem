<?php

use app\components\SystemComponents;

$systemComponents = new SystemComponents();
$can = $systemComponents->checkModulePermission('25,32');
$canEdit = $can['canEdit'];
$canExport = $can['canExport'];
$canDelete = $can['canDelete'];
$canAdd = $can['canAdd'];
?>
<div class="expiry-alert" style="min-height: 95vh;margin: -25px;margin-bottom: 24px;">
    <!-- Header Section -->
    <div class="page-header" style="margin-bottom: 24px;">
        <div class="row">
            <div class="col-md-7">
                <h1 style="color: #1e293b; font-size: 1.8rem; font-weight: 700; margin-bottom: 8px;">
                    <i class="fas fa-exclamation-triangle" style="color: #f59e0b; margin-right: 12px;"></i>
                    Expiry Alerts
                </h1>
                <p style="color: #64748b; font-size: 0.95rem; margin: 0;">
                    Monitor medicines approaching expiration and take necessary actions
                </p>
            </div>
            <div class="col-md-5 text-right">
                <div class="header-actions" style="display: flex; gap: 8px; justify-content: flex-end;">
                    <button class="btn btn-sm btn-outline-secondary" onclick="toggleColumns()"
                        style="display: flex;align-items: center;gap: 8px; border: 1px solid #e2e8f0; padding: 6px 12px; border-radius: 6px; font-size: 0.8rem; background: white; color: #64748b; margin-right: 8px;">
                        <i class="fas fa-columns"></i> Columns
                    </button>
                    <?php if ($canExport): ?>
                        <button class="btn btn-sm btn-outline-secondary" onclick="exportAlerts()"
                            style="border: 1px solid #e2e8f0; padding: 6px 12px; border-radius: 6px; font-size: 0.8rem; background: white; color: #64748b;">
                            <i class="fas fa-download"></i> Export
                        </button>
                    <?php endif; ?>
                    <?php if ($canAdd): ?>
                        <button class="btn btn-primary" onclick="setAlertSettings()"
                            style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); border: none; padding: 8px 16px; border-radius: 8px; font-size: 0.85rem; font-weight: 500; box-shadow: 0 2px 8px rgba(245, 158, 11, 0.25);">
                            <i class="fas fa-bell"></i> Set Alerts
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Summary Cards -->
    <div class="alert-summary" style="margin-bottom: 20px;">
        <div class="row g-2">
            <!-- Card 1 - Critical Alerts -->
            <div class="col-lg-3 col-md-6">
                <div class="summary-card h-100"
                    style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); border-radius: 12px; padding: 12px; box-shadow: 0 4px 16px rgba(239, 68, 68, 0.25); border: none; position: relative; overflow: hidden;">
                    <div style="display: flex; align-items: center; font-size: 1.4rem; font-weight: 700; color: white;">
                        <i class="fas fa-exclamation-circle"
                            style="font-size: 1.2rem; opacity: 0.9; margin-right: 8px;"></i>
                        <span id="criticalAlerts">0</span>
                        <small style="font-size: 0.7rem; font-weight: 500; margin: 0 8px;">Critical</small>
                        <div
                            style="margin-left: auto; background: rgba(255,255,255,0.2); padding: 2px 6px; border-radius: 8px; backdrop-filter: blur(10px); font-size: 0.6rem; font-weight: 600;">
                            ≤30 DAYS
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 2 - Warning Alerts -->
            <div class="col-lg-3 col-md-6">
                <div class="summary-card h-100"
                    style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); border-radius: 12px; padding: 12px; box-shadow: 0 4px 16px rgba(245, 158, 11, 0.25); border: none; position: relative; overflow: hidden;">
                    <div style="display: flex; align-items: center; font-size: 1.4rem; font-weight: 700; color: white;">
                        <i class="fas fa-exclamation-triangle"
                            style="font-size: 1.2rem; opacity: 0.9; margin-right: 8px;"></i>
                        <span id="warningAlerts">0</span>
                        <small style="font-size: 0.7rem; font-weight: 500; margin: 0 8px;">Warning</small>
                        <div
                            style="margin-left: auto; background: rgba(255,255,255,0.2); padding: 2px 6px; border-radius: 8px; backdrop-filter: blur(10px); font-size: 0.6rem; font-weight: 600;">
                            31-90 DAYS
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 3 - Notice Alerts -->
            <div class="col-lg-3 col-md-6">
                <div class="summary-card h-100"
                    style="background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); border-radius: 12px; padding: 12px; box-shadow: 0 4px 16px rgba(59, 130, 246, 0.25); border: none; position: relative; overflow: hidden;">
                    <div style="display: flex; align-items: center; font-size: 1.4rem; font-weight: 700; color: white;">
                        <i class="fas fa-info-circle" style="font-size: 1.2rem; opacity: 0.9; margin-right: 8px;"></i>
                        <span id="noticeAlerts">0</span>
                        <small style="font-size: 0.7rem; font-weight: 500; margin: 0 8px;">Notice</small>
                        <div
                            style="margin-left: auto; background: rgba(255,255,255,0.2); padding: 2px 6px; border-radius: 8px; backdrop-filter: blur(10px); font-size: 0.6rem; font-weight: 600;">
                            91-180 DAYS
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 4 - Total Alerts -->
            <div class="col-lg-3 col-md-6">
                <div class="summary-card h-100"
                    style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); border-radius: 12px; padding: 12px; box-shadow: 0 4px 16px rgba(139, 92, 246, 0.25); border: none; position: relative; overflow: hidden;">
                    <div style="display: flex; align-items: center; font-size: 1.4rem; font-weight: 700; color: white;">
                        <i class="fas fa-bell" style="font-size: 1.2rem; opacity: 0.9; margin-right: 8px;"></i>
                        <span id="totalAlerts">0</span>
                        <small style="font-size: 0.7rem; font-weight: 500; margin: 0 8px;">Total</small>
                        <div
                            style="margin-left: auto; background: rgba(255,255,255,0.2); padding: 2px 6px; border-radius: 8px; backdrop-filter: blur(10px); font-size: 0.6rem; font-weight: 600;">
                            ALERTS
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="medicines-table-container"
        style="margin-top: -17px;margin-bottom: 24px; background: white; border-radius: 12px; padding: 5px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e2e8f0; display: flex; flex-direction: column; height: 60vh;">

        <!-- Filter Section -->
        <div class="filter-section"
            style="padding: 12px; border-bottom: 1px solid #e2e8f0; background: #f8fafc; border-radius: 8px 8px 0 0;">
            <div class="row g-2">
                <div class="col-md-3">
                    <div class="form-group mb-0">
                        <label for="filterMedicine"
                            style="font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 0.75rem;">Medicine</label>
                        <input type="text" class="form-control" id="filterMedicine" placeholder="Search medicine..."
                            style="border: 1px solid #d1d5db; border-radius: 4px; padding: 6px 8px; font-size: 0.75rem; height: 32px;">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group mb-0">
                        <label for="filterStatus"
                            style="font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 0.75rem;">Status</label>
                        <select class="form-control" id="filterStatus"
                            style="border: 1px solid #d1d5db; border-radius: 4px; padding: 6px 8px; font-size: 0.75rem; height: 32px;">
                            <option value="">All Status</option>
                            <option value="expired">Expired</option>
                            <option value="critical">Critical</option>
                            <option value="warning">Warning</option>
                            <option value="notice">Notice</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group mb-0">
                        <label for="filterDays"
                            style="font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 0.75rem;">Days
                            Left</label>
                        <select class="form-control" id="filterDays"
                            style="border: 1px solid #d1d5db; border-radius: 4px; padding: 6px 8px; font-size: 0.75rem; height: 32px;">
                            <option value="">All</option>
                            <option value="expired">Expired (≤0 days)</option>
                            <option value="critical">Critical (1-30 days)</option>
                            <option value="warning">Warning (31-90 days)</option>
                            <option value="notice">Notice (91-180 days)</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group mb-0">
                        <label for="filterLocation"
                            style="font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 0.75rem;">Location</label>
                        <select class="form-control" id="filterLocation"
                            style="border: 1px solid #d1d5db; border-radius: 4px; padding: 6px 8px; font-size: 0.75rem; height: 32px;">
                            <option value="">All Locations</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group mb-0">
                        <label for="filterBatch"
                            style="font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 0.75rem;">Batch</label>
                        <input type="text" class="form-control" id="filterBatch" placeholder="Batch number..."
                            style="border: 1px solid #d1d5db; border-radius: 4px; padding: 6px 8px; font-size: 0.75rem; height: 32px;">
                    </div>
                </div>
                <div class="col-md-1">
                    <div class="form-group mb-0">
                        <label
                            style="font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 0.75rem;">&nbsp;</label>
                        <div style="display: flex; gap: 4px;">
                            <button class="btn btn-sm btn-outline-primary" onclick="applyFilters()"
                                style="border: 1px solid #3b82f6; padding: 4px 8px; border-radius: 4px; font-size: 0.7rem; background: white; color: #3b82f6; height: 32px;">
                                <i class="fas fa-search"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-secondary" onclick="clearFilters()"
                                style="border: 1px solid #6b7280; padding: 4px 8px; border-radius: 4px; font-size: 0.7rem; background: white; color: #6b7280; height: 32px;">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Scrollable Table Container -->
        <div class="table-scroll-wrapper" style="flex: 1; overflow: hidden; display: flex; flex-direction: column;">
            <div class="table-responsive" style="flex: 1; overflow-y: auto; overflow-x: hidden;">
                <table class="table" id="expiryTable" style="width: 100%; border-collapse: collapse; margin: 0;">
                    <thead style="position: sticky; top: 0; z-index: 10; background: #f8fafc;">
                        <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                            <th
                                style="padding: 12px; text-align: left; font-weight: 600; color: #1e293b; font-size: 0.9rem;">
                                Medicine</th>
                            <th
                                style="padding: 12px; text-align: left; font-weight: 600; color: #1e293b; font-size: 0.9rem;">
                                Batch Number</th>
                            <th
                                style="padding: 12px; text-align: center; font-weight: 600; color: #1e293b; font-size: 0.9rem;">
                                Expiry Date</th>
                            <th
                                style="padding: 12px; text-align: center; font-weight: 600; color: #1e293b; font-size: 0.9rem;">
                                Days Left</th>
                            <th
                                style="padding: 12px; text-align: left; font-weight: 600; color: #1e293b; font-size: 0.9rem;">
                                Location</th>
                            <th
                                style="padding: 12px; text-align: center; font-weight: 600; color: #1e293b; font-size: 0.9rem;">
                                Status</th>

                            <th
                                style="padding: 12px; text-align: center; font-weight: 600; color: #1e293b; font-size: 0.9rem;">
                                Actions</th>

                        </tr>
                    </thead>
                    <tbody id="expiryTableBody">
                        <!-- Table content will be populated by JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination Section -->
        <div class="pagination-section"
            style="padding: 12px; border-top: 1px solid #e2e8f0; background: #f8fafc; border-radius: 0 0 8px 8px;">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <span style="color: #6b7280; font-size: 0.8rem;">Show:</span>
                        <select id="itemsPerPage" class="form-control"
                            style="width: auto; border: 1px solid #d1d5db; border-radius: 4px; padding: 4px 8px; font-size: 0.75rem; height: 28px;">
                            <option value="10">10</option>
                            <option value="25" selected>25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                        <span style="color: #6b7280; font-size: 0.8rem;">items per page</span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span id="paginationInfo" style="color: #6b7280; font-size: 0.8rem;">
                            Loading...
                        </span>
                        <nav>
                            <ul class="pagination pagination-sm mb-0" id="pagination">
                                <!-- Pagination buttons will be generated by JavaScript -->
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Alert Details Modal -->
<div class="modal fade" id="alertDetailsModal" tabindex="-1" role="dialog" aria-labelledby="alertDetailsModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 20px 40px rgba(0,0,0,0.15);">
            <div class="modal-header"
                style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white; border-radius: 12px 12px 0 0; border-bottom: none; padding: 12px 16px;">
                <h5 class="modal-title" id="alertDetailsModalLabel"
                    style="font-size: 1rem; font-weight: 600; margin: 0;">
                    <i class="fas fa-exclamation-triangle me-2"></i>Expiry Alert Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"
                    style="opacity: 0.8; font-size: 1rem;"></button>
            </div>
            <div class="modal-body" id="alertDetailsBody" style="padding: 16px; max-height: 60vh; overflow-y: auto;">
                <!-- Alert details will be loaded here -->
            </div>
            <div class="modal-footer"
                style="border-top: 1px solid #e2e8f0; padding: 12px 16px; background: #f8fafc; border-radius: 0 0 12px 12px;">
                <div style="display: flex; gap: 6px; margin-left: auto;">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal"
                        style="padding: 6px 12px; border-radius: 6px; font-weight: 500; font-size: 0.8rem;">
                        Close
                    </button>
                    <button type="button" class="btn btn-warning" onclick="extendExpiryFromModal()"
                        style="padding: 6px 12px; border-radius: 6px; font-weight: 500; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); border: none; font-size: 0.8rem;">
                        Extend Expiry
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Extend Expiry Modal -->
<div class="modal fade" id="extendExpiryModal" tabindex="-1" role="dialog" aria-labelledby="extendExpiryModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 20px 40px rgba(0,0,0,0.15);">
            <div class="modal-header"
                style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; border-radius: 12px 12px 0 0; border-bottom: none; padding: 12px 16px;">
                <h5 class="modal-title" id="extendExpiryModalLabel"
                    style="font-size: 1rem; font-weight: 600; margin: 0;">
                    <i class="fas fa-clock me-2"></i>Extend Expiry Date
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"
                    style="opacity: 0.8; font-size: 1rem;"></button>
            </div>
            <div class="modal-body" style="padding: 16px;">
                <form id="extendExpiryForm">
                    <input type="hidden" id="extendAlertId" name="stock_id">

                    <div class="form-group mb-3">
                        <label for="currentExpiryDate"
                            style="font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 0.8rem;">Current
                            Expiry Date</label>
                        <input type="text" class="form-control" id="currentExpiryDate" readonly
                            style="border: 1px solid #d1d5db; border-radius: 4px; padding: 8px 12px; font-size: 0.8rem; background: #f9fafb;">
                    </div>

                    <div class="form-group mb-3">
                        <label for="newExpiryDate"
                            style="font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 0.8rem;">New
                            Expiry Date *</label>
                        <input type="date" class="form-control" id="newExpiryDate" name="new_expiry_date" required
                            style="border: 1px solid #d1d5db; border-radius: 4px; padding: 8px 12px; font-size: 0.8rem;">
                    </div>

                    <div class="form-group">
                        <label for="extendReason"
                            style="font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 0.8rem;">Reason
                            for Extension</label>
                        <textarea class="form-control" id="extendReason" name="reason" rows="3"
                            style="border: 1px solid #d1d5db; border-radius: 4px; padding: 8px 12px; font-size: 0.8rem; resize: vertical;"
                            placeholder="Enter reason for extending expiry date..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer"
                style="border-top: 1px solid #e2e8f0; padding: 12px 16px; background: #f8fafc; border-radius: 0 0 12px 12px;">
                <div style="display: flex; gap: 6px; margin-left: auto;">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal"
                        style="padding: 6px 12px; border-radius: 6px; font-weight: 500; font-size: 0.8rem;">Cancel</button>
                    <button type="button" class="btn btn-success" onclick="confirmExtendExpiry()"
                        style="padding: 6px 12px; border-radius: 6px; font-weight: 500; background: linear-gradient(135deg, #10b981 0%, #059669 100%); border: none; font-size: 0.8rem;">Extend
                        Expiry</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Alert Settings Modal -->
<div class="modal fade" id="alertSettingsModal" tabindex="-1" role="dialog" aria-labelledby="alertSettingsModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 20px 40px rgba(0,0,0,0.15);">
            <div class="modal-header"
                style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white; border-radius: 12px 12px 0 0; border-bottom: none; padding: 12px 16px;">
                <h5 class="modal-title" id="alertSettingsModalLabel"
                    style="font-size: 1rem; font-weight: 600; margin: 0;">
                    <i class="fas fa-bell me-2"></i>Alert Settings
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"
                    style="opacity: 0.8; font-size: 1rem;"></button>
            </div>
            <div class="modal-body" style="padding: 16px; max-height: 70vh; overflow-y: auto;">
                <form id="alertSettingsForm">
                    <!-- Alert Thresholds Section -->
                    <div class="form-section"
                        style="background: white; border-radius: 6px; padding: 12px; margin-bottom: 12px; border: 1px solid #e2e8f0;">
                        <h6
                            style="color: #374151; font-weight: 600; margin-bottom: 12px; display: flex; align-items: center; font-size: 0.8rem;">
                            <i class="fas fa-sliders-h me-2" style="color: #f59e0b; font-size: 0.7rem;"></i>Alert
                            Thresholds
                        </h6>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group mb-2">
                                    <label for="criticalThreshold"
                                        style="font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 0.75rem;">Critical
                                        Alert (days)</label>
                                    <input type="number" class="form-control" id="criticalThreshold"
                                        name="critical_threshold" value="30" min="1" max="365"
                                        style="border: 1px solid #d1d5db; border-radius: 4px; padding: 6px 8px; font-size: 0.75rem; transition: all 0.2s; height: 32px;">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-2">
                                    <label for="warningThreshold"
                                        style="font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 0.75rem;">Warning
                                        Alert (days)</label>
                                    <input type="number" class="form-control" id="warningThreshold"
                                        name="warning_threshold" value="90" min="1" max="365"
                                        style="border: 1px solid #d1d5db; border-radius: 4px; padding: 6px 8px; font-size: 0.75rem; transition: all 0.2s; height: 32px;">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-2">
                                    <label for="noticeThreshold"
                                        style="font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 0.75rem;">Notice
                                        Alert (days)</label>
                                    <input type="number" class="form-control" id="noticeThreshold"
                                        name="notice_threshold" value="180" min="1" max="365"
                                        style="border: 1px solid #d1d5db; border-radius: 4px; padding: 6px 8px; font-size: 0.75rem; transition: all 0.2s; height: 32px;">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Notification Settings Section -->
                    <div class="form-section"
                        style="background: white; border-radius: 6px; padding: 12px; margin-bottom: 12px; border: 1px solid #e2e8f0;">
                        <h6
                            style="color: #374151; font-weight: 600; margin-bottom: 12px; display: flex; align-items: center; font-size: 0.8rem;">
                            <i class="fas fa-bell me-2" style="color: #f59e0b; font-size: 0.7rem;"></i>Notification
                            Settings
                        </h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="emailNotifications" checked
                                        style="transform: scale(0.8);">
                                    <label class="form-check-label" for="emailNotifications"
                                        style="font-weight: 500; font-size: 0.75rem;">Email Notifications</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="smsNotifications"
                                        style="transform: scale(0.8);">
                                    <label class="form-check-label" for="smsNotifications"
                                        style="font-weight: 500; font-size: 0.75rem;">SMS Notifications</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="dailyReports" checked
                                        style="transform: scale(0.8);">
                                    <label class="form-check-label" for="dailyReports"
                                        style="font-weight: 500; font-size: 0.75rem;">Daily Reports</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="weeklyReports"
                                        style="transform: scale(0.8);">
                                    <label class="form-check-label" for="weeklyReports"
                                        style="font-weight: 500; font-size: 0.75rem;">Weekly Reports</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Auto Actions Section -->
                    <div class="form-section"
                        style="background: white; border-radius: 6px; padding: 12px; border: 1px solid #e2e8f0;">
                        <h6
                            style="color: #374151; font-weight: 600; margin-bottom: 12px; display: flex; align-items: center; font-size: 0.8rem;">
                            <i class="fas fa-cog me-2" style="color: #f59e0b; font-size: 0.7rem;"></i>Auto Actions
                        </h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="autoReorder"
                                        style="transform: scale(0.8);">
                                    <label class="form-check-label" for="autoReorder"
                                        style="font-weight: 500; font-size: 0.75rem;">Auto Reorder on Low Stock</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="autoTransfer"
                                        style="transform: scale(0.8);">
                                    <label class="form-check-label" for="autoTransfer"
                                        style="font-weight: 500; font-size: 0.75rem;">Auto Transfer Expiring
                                        Items</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="autoDiscount"
                                        style="transform: scale(0.8);">
                                    <label class="form-check-label" for="autoDiscount"
                                        style="font-weight: 500; font-size: 0.75rem;">Auto Apply Discounts</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="autoArchive"
                                        style="transform: scale(0.8);">
                                    <label class="form-check-label" for="autoArchive"
                                        style="font-weight: 500; font-size: 0.75rem;">Auto Archive Expired Items</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer"
                style="border-top: 1px solid #e2e8f0; padding: 12px 16px; background: #f8fafc; border-radius: 0 0 12px 12px;">
                <div style="display: flex; gap: 6px; margin-left: auto;">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal"
                        style="padding: 6px 12px; border-radius: 6px; font-weight: 500; font-size: 0.8rem;">Cancel</button>
                    <button type="button" class="btn btn-warning" onclick="saveAlertSettings()"
                        style="padding: 6px 12px; border-radius: 6px; font-weight: 500; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); border: none; font-size: 0.8rem;">Save
                        Settings</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Column Visibility Modal -->
<div class="modal fade" id="columnModal" tabindex="-1" role="dialog" aria-labelledby="columnModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content" style="border-radius: 10px; border: none; box-shadow: 0 20px 40px rgba(0,0,0,0.15);">
            <div class="modal-header"
                style="background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); color: white; border-radius: 10px 10px 0 0; border-bottom: none; padding: 10px 14px;">
                <h5 class="modal-title" id="columnModalLabel" style="font-size: 0.9rem; font-weight: 600; margin: 0;">
                    <i class="fas fa-columns me-2"></i>Column Visibility
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"
                    style="opacity: 0.8; font-size: 0.9rem;"></button>
            </div>
            <div class="modal-body" style="padding: 12px; max-height: 50vh; overflow-y: auto;">
                <p style="color: #6b7280; margin-bottom: 12px; font-size: 0.7rem;">Select which columns you want to
                    display in the expiry alerts table:</p>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-check mb-1">
                            <input class="form-check-input" type="checkbox" id="colMedicine" checked
                                style="transform: scale(0.8);">
                            <label class="form-check-label" for="colMedicine"
                                style="font-weight: 500; font-size: 0.7rem;">Medicine</label>
                        </div>
                        <div class="form-check mb-1">
                            <input class="form-check-input" type="checkbox" id="colBatch" checked
                                style="transform: scale(0.8);">
                            <label class="form-check-label" for="colBatch"
                                style="font-weight: 500; font-size: 0.7rem;">Batch Number</label>
                        </div>
                        <div class="form-check mb-1">
                            <input class="form-check-input" type="checkbox" id="colExpiry" checked
                                style="transform: scale(0.8);">
                            <label class="form-check-label" for="colExpiry"
                                style="font-weight: 500; font-size: 0.7rem;">Expiry Date</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check mb-1">
                            <input class="form-check-input" type="checkbox" id="colDaysLeft" checked
                                style="transform: scale(0.8);">
                            <label class="form-check-label" for="colDaysLeft"
                                style="font-weight: 500; font-size: 0.7rem;">Days Left</label>
                        </div>
                        <div class="form-check mb-1">
                            <input class="form-check-input" type="checkbox" id="colLocation" checked
                                style="transform: scale(0.8);">
                            <label class="form-check-label" for="colLocation"
                                style="font-weight: 500; font-size: 0.7rem;">Location</label>
                        </div>
                        <div class="form-check mb-1">
                            <input class="form-check-input" type="checkbox" id="colStatus" checked
                                style="transform: scale(0.8);">
                            <label class="form-check-label" for="colStatus"
                                style="font-weight: 500; font-size: 0.7rem;">Status</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer"
                style="border-top: 1px solid #e2e8f0; padding: 10px 14px; background: #f8fafc; border-radius: 0 0 10px 10px;">
                <div style="display: flex; gap: 6px; margin-left: auto;">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal"
                        style="padding: 5px 10px; border-radius: 4px; font-weight: 500; font-size: 0.7rem;">Close</button>
                    <button type="button" class="btn btn-primary" onclick="applyColumnVisibility()"
                        style="padding: 5px 10px; border-radius: 4px; font-weight: 500; background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); border: none; font-size: 0.7rem;">Apply</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content" style="border-radius: 10px; border: none; box-shadow: 0 20px 40px rgba(0,0,0,0.15);">
            <div class="modal-header"
                style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: white; border-radius: 10px 10px 0 0; border-bottom: none; padding: 10px 14px;">
                <h5 class="modal-title" id="deleteModalLabel" style="font-size: 0.9rem; font-weight: 600; margin: 0;">
                    <i class="fas fa-exclamation-triangle me-2"></i>Confirm Action
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"
                    style="opacity: 0.8; font-size: 0.9rem;"></button>
            </div>
            <div class="modal-body" style="padding: 12px; text-align: center;">
                <div style="color: #ef4444; font-size: 1.5rem; margin-bottom: 8px;">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h6 style="color: #374151; font-weight: 600; margin-bottom: 4px; font-size: 0.8rem;"
                    id="deleteModalTitle">
                    Are you sure you want to perform this action?
                </h6>
                <p style="color: #6b7280; margin-bottom: 12px; font-size: 0.7rem;" id="deleteModalMessage">
                    This action cannot be undone.
                </p>
                <div id="deleteAlertInfo"></div>
            </div>
            <div class="modal-footer"
                style="border-top: 1px solid #e2e8f0; padding: 10px 14px; background: #f8fafc; border-radius: 0 0 10px 10px;">
                <div style="display: flex; gap: 6px; margin-left: auto;">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal"
                        style="padding: 5px 10px; border-radius: 4px; font-weight: 500; font-size: 0.7rem;">
                        Cancel
                    </button>
                    <button type="button" class="btn btn-danger" onclick="confirmDelete()"
                        style="padding: 5px 10px; border-radius: 4px; font-weight: 500; background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); border: none; font-size: 0.7rem;">
                        Confirm
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .expiry-alert .summary-card {
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .expiry-alert .summary-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .expiry-alert .table tbody tr:hover {
        background: #f8fafc;
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        transition: all 0.2s ease;
    }

    .expiry-alert .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        transition: all 0.2s ease;
    }

    /* Fixed table header styles */
    .expiry-alert .table thead {
        position: sticky;
        top: 0;
        z-index: 10;
        background: #f8fafc;
    }

    .expiry-alert .table thead th {
        position: sticky;
        top: 0;
        background: #f8fafc;
        z-index: 10;
    }

    /* Scrollable table container */
    .table-scroll-wrapper {
        overflow: hidden;
    }

    .table-scroll-wrapper .table-responsive {
        scrollbar-width: thin;
        scrollbar-color: #cbd5e0 #f7fafc;
    }

    .table-scroll-wrapper .table-responsive::-webkit-scrollbar {
        width: 8px;
    }

    .table-scroll-wrapper .table-responsive::-webkit-scrollbar-track {
        background: #f7fafc;
        border-radius: 4px;
    }

    .table-scroll-wrapper .table-responsive::-webkit-scrollbar-thumb {
        background: #cbd5e0;
        border-radius: 4px;
    }

    .table-scroll-wrapper .table-responsive::-webkit-scrollbar-thumb:hover {
        background: #a0aec0;
    }

    /* Alert row styling */
    .alert-row {
        border-bottom: 1px solid #e2e8f0;
    }

    .alert-row:last-child {
        border-bottom: none;
    }

    /* Modal styles */
    .modal-content {
        border-radius: 16px;
        border: none;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        overflow: hidden;
    }

    .modal-header {
        border-bottom: none;
        padding: 20px 24px;
    }

    .form-control:focus {
        border-color: #f59e0b;
        box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1);
        outline: none;
    }

    .form-check-input:checked {
        background-color: #f59e0b;
        border-color: #f59e0b;
    }

    .form-check-input:focus {
        box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1);
    }

    .btn {
        transition: all 0.2s ease;
    }

    .btn:active {
        transform: translateY(0);
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .expiry-alert .alert-summary .col-md-3 {
            margin-bottom: 16px;
        }

        .expiry-alert .filters-section .row>div {
            margin-bottom: 12px;
        }

        .expiry-alert .table-responsive {
            font-size: 0.8rem;
        }

        .modal-dialog {
            margin: 10px;
        }

        .medicines-table-container {
            height: 60vh !important;
        }
    }

    @media (max-width: 576px) {
        .medicines-table-container {
            height: 50vh !important;
        }

        .expiry-alert .table thead th,
        .expiry-alert .table tbody td {
            padding: 8px !important;
            font-size: 0.8rem !important;
        }
    }
</style>

<script>
    let currentAlertId = null;
    let expiryAlertsData = [];
    let filteredAlertsData = [];
    let currentPage = 1;
    let itemsPerPage = 25;
    let totalPages = 1;
    let alertSettings = {};
    let currentAction = null; // Track current action type

    $(document).ready(function() {
        // Initialize the page
        initializePage();
    });

    async function initializePage() {
        try {
            // Load locations for filter
            await loadLocations();

            // Load alert settings
            await loadAlertSettings();

            // Load expiry alerts data
            await loadExpiryAlerts();

            // Initialize column visibility
            initializeColumnVisibility();

            // Add event listeners for modals
            $('#alertDetailsModal').on('hidden.bs.modal', function() {
                currentAlertId = null;
            });
            $('#extendExpiryModal').on('hidden.bs.modal', function() {
                $('#extendExpiryForm')[0].reset();
                currentAlertId = null;
            });
            $('#alertSettingsModal').on('hidden.bs.modal', function() {
                // No specific reset needed for settings modal
            });
            $('#columnModal').on('hidden.bs.modal', function() {
                // No specific reset needed for column modal
            });
            $('#deleteModal').on('hidden.bs.modal', function() {
                currentAlertId = null;
                currentAction = null;
            });

            // Add event listeners for filter inputs
            $('#filterMedicine, #filterStatus, #filterDays, #filterLocation, #filterBatch').on('input change',
                function() {
                    applyFilters();
                });

            // Add event listener for items per page change
            $('#itemsPerPage').on('change', function() {
                itemsPerPage = parseInt($(this).val());
                currentPage = 1;
                refreshTable();
            });

            // Initialize stats and table
            updateStats();
            refreshTable();
        } catch (error) {
            console.error('Error initializing page:', error);
            showNotification('Error loading page data', 'error');
        }
    }

    async function loadLocations() {
        try {
            const response = await fetch('index.php?r=inventory/get-locations-for-filter');
            const result = await response.json();

            if (result.success) {
                const locationSelect = $('#filterLocation');
                locationSelect.empty();
                locationSelect.append('<option value="">All Locations</option>');

                result.data.forEach(location => {
                    locationSelect.append(`<option value="${location}">${location}</option>`);
                });
            }
        } catch (error) {
            console.error('Error loading locations:', error);
        }
    }

    async function loadAlertSettings() {
        try {
            const response = await fetch('index.php?r=inventory/get-alert-settings');
            const result = await response.json();

            if (result.success) {
                alertSettings = result.data;

                // Update threshold labels in summary cards
                $('.summary-card .col-lg-3:nth-child(1) .summary-card div:last-child').text(
                    `≤${alertSettings.critical_threshold} DAYS`);
                $('.summary-card .col-lg-3:nth-child(2) .summary-card div:last-child').text(
                    `${alertSettings.critical_threshold + 1}-${alertSettings.warning_threshold} DAYS`);
                $('.summary-card .col-lg-3:nth-child(3) .summary-card div:last-child').text(
                    `${alertSettings.warning_threshold + 1}-${alertSettings.notice_threshold} DAYS`);
            }
        } catch (error) {
            console.error('Error loading alert settings:', error);
        }
    }

    async function loadExpiryAlerts() {
        try {
            showLoading(true);

            const response = await fetch('index.php?r=inventory/get-expiry-alerts');
            const result = await response.json();

            if (result.success) {
                expiryAlertsData = result.data;
                filteredAlertsData = [...expiryAlertsData];

                if (result.settings) {
                    alertSettings = result.settings;
                }

                updateStats();
                refreshTable();
            } else {
                showNotification(result.message || 'Error loading expiry alerts', 'error');
            }
        } catch (error) {
            console.error('Error loading expiry alerts:', error);
            showNotification('Error loading expiry alerts', 'error');
        } finally {
            showLoading(false);
        }
    }

    function showLoading(show) {
        const tbody = $('#expiryTableBody');
        if (show) {
            tbody.html(`
                <tr>
                    <td colspan="7" style="text-align: center; padding: 40px; color: #6b7280;">
                        <i class="fas fa-spinner fa-spin" style="font-size: 2rem; margin-bottom: 12px; display: block;"></i>
                        Loading expiry alerts...
                    </td>
                </tr>
            `);
        }
    }

    function updateStats() {
        let criticalCount = 0;
        let warningCount = 0;
        let noticeCount = 0;
        let expiredCount = 0;

        expiryAlertsData.forEach(alert => {
            const daysLeft = parseInt(alert.days_left);
            const status = alert.status;

            switch (status) {
                case 'expired':
                    expiredCount++;
                    break;
                case 'critical':
                    criticalCount++;
                    break;
                case 'warning':
                    warningCount++;
                    break;
                case 'notice':
                    noticeCount++;
                    break;
            }
        });

        // Update stats cards
        $('#criticalAlerts').text(criticalCount + expiredCount);
        $('#warningAlerts').text(warningCount);
        $('#noticeAlerts').text(noticeCount);
        $('#totalAlerts').text(expiryAlertsData.length);
    }

    function applyFilters() {
        const medicineFilter = $('#filterMedicine').val().toLowerCase();
        const statusFilter = $('#filterStatus').val();
        const daysFilter = $('#filterDays').val();
        const locationFilter = $('#filterLocation').val();
        const batchFilter = $('#filterBatch').val().toLowerCase();

        filteredAlertsData = expiryAlertsData.filter(alert => {
            // Medicine filter
            if (medicineFilter &&
                !alert.medicine_name.toLowerCase().includes(medicineFilter) &&
                !alert.generic_name.toLowerCase().includes(medicineFilter)) {
                return false;
            }

            // Status filter
            if (statusFilter && alert.status !== statusFilter) {
                return false;
            }

            // Days filter
            if (daysFilter && alert.status !== daysFilter) {
                return false;
            }

            // Location filter
            if (locationFilter && alert.location !== locationFilter) {
                return false;
            }

            // Batch filter
            if (batchFilter && !alert.batch_number.toLowerCase().includes(batchFilter)) {
                return false;
            }

            return true;
        });

        currentPage = 1;
        refreshTable();
        showNotification(`Showing ${filteredAlertsData.length} of ${expiryAlertsData.length} alerts`, 'info');
    }

    function clearFilters() {
        $('#filterMedicine').val('');
        $('#filterStatus').val('');
        $('#filterDays').val('');
        $('#filterLocation').val('');
        $('#filterBatch').val('');

        filteredAlertsData = [...expiryAlertsData];
        currentPage = 1;
        refreshTable();
        showNotification('Filters cleared', 'info');
    }

    function refreshTable() {
        const tbody = $('#expiryTableBody');
        tbody.empty();

        // Calculate pagination
        totalPages = Math.ceil(filteredAlertsData.length / itemsPerPage);
        const startIndex = (currentPage - 1) * itemsPerPage;
        const endIndex = Math.min(startIndex + itemsPerPage, filteredAlertsData.length);
        const pageData = filteredAlertsData.slice(startIndex, endIndex);

        if (pageData.length === 0) {
            tbody.append(`
                <tr>
                    <td colspan="7" style="text-align: center; padding: 40px; color: #6b7280;">
                        <i class="fas fa-search" style="font-size: 2rem; margin-bottom: 12px; display: block;"></i>
                        No expiry alerts found matching your criteria
                    </td>
                </tr>
            `);
        } else {
            pageData.forEach(alert => {
                const daysLeft = parseInt(alert.days_left);
                const status = alert.status;

                let statusColor = '#10b981';
                let statusBg = '#f0fdf4';
                let statusText = 'Good';

                switch (status) {
                    case 'expired':
                        statusColor = '#dc2626';
                        statusBg = '#fef2f2';
                        statusText = 'Expired';
                        break;
                    case 'critical':
                        statusColor = '#dc2626';
                        statusBg = '#fef2f2';
                        statusText = 'Critical';
                        break;
                    case 'warning':
                        statusColor = '#f59e0b';
                        statusBg = '#fef3c7';
                        statusText = 'Warning';
                        break;
                    case 'notice':
                        statusColor = '#3b82f6';
                        statusBg = '#eff6ff';
                        statusText = 'Notice';
                        break;
                }

                const daysLeftColor = daysLeft <= 0 ? '#dc2626' : (daysLeft <= 30 ? '#ea580c' : (daysLeft <= 90 ?
                    '#f59e0b' : '#10b981'));

                const row = `
                    <tr class="alert-row" data-id="${alert.id}" style="border-bottom: 1px solid #f1f5f9; transition: background 0.2s;">
                        <td style="padding: 12px; font-weight: 500; color: #1e293b;">
                            <div style="display: flex; align-items: center;">
                                <div style="width: 8px; height: 8px; background: ${statusColor}; border-radius: 50%; margin-right: 8px;"></div>
                                <div>
                                    <div style="font-weight: 600; color: #1e293b;">${alert.medicine_name}</div>
                                    <div style="color: #64748b; font-size: 0.8rem;">${alert.generic_name}</div>
                                </div>
                            </div>
                        </td>
                        <td style="padding: 12px; color: #64748b; font-size: 0.9rem; font-family: monospace;">
                            ${alert.batch_number}
                        </td>
                        <td style="padding: 12px; text-align: center; color: #374151; font-size: 0.9rem;">
                            ${alert.expiry_date}
                        </td>
                        <td style="padding: 12px; text-align: center;">
                            <div style="font-weight: 600; color: ${daysLeftColor}; font-size: 0.9rem;">
                                ${daysLeft <= 0 ? 'Expired' : daysLeft + ' days'}
                            </div>
                        </td>
                        <td style="padding: 12px; color: #64748b; font-size: 0.9rem;">
                            ${alert.location || 'N/A'}
                        </td>
                        <td style="padding: 12px; text-align: center;">
                            <span style="background: ${statusBg}; color: ${statusColor}; padding: 4px 8px; border-radius: 12px; font-size: 0.8rem; font-weight: 500;">
                                ${statusText}
                            </span>
                        </td>
                        
                        <td style="padding: 12px; text-align: center;">
                            <div style="display: flex; gap: 4px; justify-content: center;">
                                <button class="btn btn-sm btn-outline-info" onclick="viewAlertDetails(${alert.id})"
                                    style="border: 1px solid #06b6d4; padding: 4px 8px; border-radius: 4px; font-size: 0.75rem; background: white; color: #06b6d4;">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <?php if ($canEdit): ?>
                                <button class="btn btn-sm btn-outline-warning" onclick="extendExpiry(${alert.id})"
                                    style="border: 1px solid #f59e0b; padding: 4px 8px; border-radius: 4px; font-size: 0.75rem; background: white; color: #f59e0b;">
                                    <i class="fas fa-clock"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-success" onclick="markAsHandled(${alert.id})"
                                    style="border: 1px solid #10b981; padding: 4px 8px; border-radius: 4px; font-size: 0.75rem; background: white; color: #10b981;">
                                    <i class="fas fa-check"></i>
                                </button>
                                <?php endif; ?>
                            </div>
                        </td>
                        
                    </tr>
                `;
                tbody.append(row);
            });
        }

        updatePagination();
    }

    function updatePagination() {
        const pagination = $('#pagination');
        const paginationInfo = $('#paginationInfo');

        // Update pagination info
        const startIndex = (currentPage - 1) * itemsPerPage + 1;
        const endIndex = Math.min(currentPage * itemsPerPage, filteredAlertsData.length);
        paginationInfo.text(`Showing ${startIndex} to ${endIndex} of ${filteredAlertsData.length} entries`);

        // Clear existing pagination
        pagination.empty();

        if (totalPages <= 1) {
            return;
        }

        // Previous button
        const prevButton = `
            <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" onclick="changePage(${currentPage - 1})" style="font-size: 0.75rem; padding: 4px 8px;">
                    <i class="fas fa-chevron-left"></i>
                </a>
            </li>
        `;
        pagination.append(prevButton);

        // Page numbers
        const maxVisiblePages = 5;
        let startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
        let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);

        if (endPage - startPage + 1 < maxVisiblePages) {
            startPage = Math.max(1, endPage - maxVisiblePages + 1);
        }

        // First page and ellipsis
        if (startPage > 1) {
            pagination.append(`
                <li class="page-item">
                    <a class="page-link" href="#" onclick="changePage(1)" style="font-size: 0.75rem; padding: 4px 8px;">1</a>
                </li>
            `);
            if (startPage > 2) {
                pagination.append(`
                    <li class="page-item disabled">
                        <span class="page-link" style="font-size: 0.75rem; padding: 4px 8px;">...</span>
                    </li>
                `);
            }
        }

        // Page numbers
        for (let i = startPage; i <= endPage; i++) {
            const pageButton = `
                <li class="page-item ${i === currentPage ? 'active' : ''}">
                    <a class="page-link" href="#" onclick="changePage(${i})" style="font-size: 0.75rem; padding: 4px 8px;">${i}</a>
                </li>
            `;
            pagination.append(pageButton);
        }

        // Last page and ellipsis
        if (endPage < totalPages) {
            if (endPage < totalPages - 1) {
                pagination.append(`
                    <li class="page-item disabled">
                        <span class="page-link" style="font-size: 0.75rem; padding: 4px 8px;">...</span>
                    </li>
                `);
            }
            pagination.append(`
                <li class="page-item">
                    <a class="page-link" href="#" onclick="changePage(${totalPages})" style="font-size: 0.75rem; padding: 4px 8px;">${totalPages}</a>
                </li>
            `);
        }

        // Next button
        const nextButton = `
            <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                <a class="page-link" href="#" onclick="changePage(${currentPage + 1})" style="font-size: 0.75rem; padding: 4px 8px;">
                    <i class="fas fa-chevron-right"></i>
                </a>
            </li>
        `;
        pagination.append(nextButton);
    }

    function changePage(page) {
        if (page >= 1 && page <= totalPages && page !== currentPage) {
            currentPage = page;
            refreshTable();
        }
    }

    function viewAlertDetails(id) {
        const alert = expiryAlertsData.find(a => a.id == id);

        if (alert) {
            const daysLeft = parseInt(alert.days_left);
            const status = alert.status;

            const html = `
                <div class="row">
                    <div class="col-md-6">
                        <div style="background: #f8fafc; border-radius: 12px; padding: 20px; margin-bottom: 20px;">
                            <h6 style="color: #374151; font-weight: 600; margin-bottom: 16px; display: flex; align-items: center;">
                                <i class="fas fa-info-circle me-2" style="color: #f59e0b;"></i>Medicine Information
                            </h6>
                            <table class="table table-sm" style="margin: 0;">
                                <tr><td style="border: none; padding: 8px 0; font-weight: 600; color: #374151;">Medicine:</td><td style="border: none; padding: 8px 0; color: #6b7280;">${alert.medicine_name}</td></tr>
                                <tr><td style="border: none; padding: 8px 0; font-weight: 600; color: #374151;">Generic Name:</td><td style="border: none; padding: 8px 0; color: #6b7280;">${alert.generic_name}</td></tr>
                                <tr><td style="border: none; padding: 8px 0; font-weight: 600; color: #374151;">Batch Number:</td><td style="border: none; padding: 8px 0; color: #6b7280; font-family: monospace;">${alert.batch_number}</td></tr>
                                <tr><td style="border: none; padding: 8px 0; font-weight: 600; color: #374151;">Location:</td><td style="border: none; padding: 8px 0; color: #6b7280;">${alert.location || 'N/A'}</td></tr>
                                <tr><td style="border: none; padding: 8px 0; font-weight: 600; color: #374151;">Quantity:</td><td style="border: none; padding: 8px 0; color: #6b7280;">${alert.quantity} ${alert.unit}</td></tr>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div style="background: #f8fafc; border-radius: 12px; padding: 20px; margin-bottom: 20px;">
                            <h6 style="color: #374151; font-weight: 600; margin-bottom: 16px; display: flex; align-items: center;">
                                <i class="fas fa-exclamation-triangle me-2" style="color: #f59e0b;"></i>Expiry Information
                            </h6>
                            <table class="table table-sm" style="margin: 0;">
                                <tr><td style="border: none; padding: 8px 0; font-weight: 600; color: #374151;">Expiry Date:</td><td style="border: none; padding: 8px 0; color: #6b7280;">${alert.expiry_date}</td></tr>
                                <tr><td style="border: none; padding: 8px 0; font-weight: 600; color: #374151;">Days Left:</td><td style="border: none; padding: 8px 0;"><span class="text-${daysLeft <= 0 ? 'danger' : daysLeft <= 30 ? 'danger' : daysLeft <= 90 ? 'warning' : 'success'}">${daysLeft <= 0 ? 'Expired' : daysLeft + ' days'}</span></td></tr>
                                <tr><td style="border: none; padding: 8px 0; font-weight: 600; color: #374151;">Status:</td><td style="border: none; padding: 8px 0;"><span class="badge bg-${status === 'expired' ? 'danger' : status === 'critical' ? 'danger' : status === 'warning' ? 'warning' : 'info'}">${status.charAt(0).toUpperCase() + status.slice(1)}</span></td></tr>
                            </table>
                        </div>
                    </div>
                </div>
                ${alert.supplier_name ? `<div class="mt-3"><div style="background: #f8fafc; border-radius: 12px; padding: 20px;"><h6 style="color: #374151; font-weight: 600; margin-bottom: 12px; display: flex; align-items: center;"><i class="fas fa-truck me-2" style="color: #f59e0b;"></i>Supplier Information</h6><p style="color: #6b7280; margin: 0;">Supplier: ${alert.supplier_name}</p></div></div>` : ''}
            `;

            $('#alertDetailsBody').html(html);
            currentAlertId = id;

            const modal = new bootstrap.Modal(document.getElementById('alertDetailsModal'));
            modal.show();
        }
    }

    function extendExpiry(id) {
        const alert = expiryAlertsData.find(a => a.id == id);

        if (alert) {
            currentAlertId = id;
            $('#extendAlertId').val(alert.id);
            $('#currentExpiryDate').val(alert.expiry_date);
            $('#newExpiryDate').val('');
            $('#extendReason').val('');

            const modal = new bootstrap.Modal(document.getElementById('extendExpiryModal'));
            modal.show();
        }
    }

    async function confirmExtendExpiry() {
        const form = $('#extendExpiryForm')[0];

        if (form.checkValidity()) {
            try {
                const formData = new FormData(form);

                const response = await fetch('index.php?r=inventory/extend-expiry', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    showNotification(result.message || 'Expiry date extended successfully', 'success');
                    const modal = bootstrap.Modal.getInstance(document.getElementById('extendExpiryModal'));
                    modal.hide();

                    // Reload data
                    await loadExpiryAlerts();
                } else {
                    showNotification(result.message || 'Error extending expiry date', 'error');
                }
            } catch (error) {
                console.error('Error extending expiry:', error);
                showNotification('Error extending expiry date', 'error');
            }
        } else {
            form.reportValidity();
        }
    }

    async function markAsHandled(id) {
        const alert = expiryAlertsData.find(a => a.id == id);

        if (alert) {
            currentAlertId = id;
            currentAction = 'mark_handled';

            $('#deleteModalTitle').text('Mark Alert as Handled');
            $('#deleteModalMessage').text(
                'Are you sure you want to mark this alert as handled? This action will be logged in the system.');
            $('#deleteAlertInfo').html(`
                <div class="alert alert-warning" style="background: #fef3c7; border: 1px solid #f59e0b; color: #92400e; border-radius: 8px; padding: 16px;">
                    <div style="display: flex; align-items: center;">
                        <i class="fas fa-exclamation-triangle me-2" style="font-size: 1.2rem;"></i>
                        <div>
                            <strong>${alert.medicine_name}</strong><br>
                            <small>${alert.generic_name} - Batch: ${alert.batch_number}</small><br>
                            <small>Expires: ${alert.expiry_date} (${alert.days_left} days left)</small>
                        </div>
                    </div>
                </div>
            `);

            const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
            modal.show();
        }
    }

    function confirmDelete() {
        if (currentAlertId && currentAction) {
            switch (currentAction) {
                case 'mark_handled':
                    performMarkAsHandled();
                    break;
                default:
                    showNotification('Unknown action', 'error');
            }
        }
    }

    async function performMarkAsHandled() {
        try {
            const formData = new FormData();
            formData.append('stock_id', currentAlertId);
            formData.append('reason', 'Marked as handled by user');

            const response = await fetch('index.php?r=inventory/mark-alert-handled', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                showNotification(result.message || 'Alert marked as handled successfully', 'success');

                // Close modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('deleteModal'));
                modal.hide();

                // Reload data
                await loadExpiryAlerts();
            } else {
                showNotification(result.message || 'Error marking alert as handled', 'error');
            }
        } catch (error) {
            console.error('Error marking alert as handled:', error);
            showNotification('Error marking alert as handled', 'error');
        }
    }

    function setAlertSettings() {
        // Load current settings into the form
        if (alertSettings) {
            $('#criticalThreshold').val(alertSettings.critical_threshold || 30);
            $('#warningThreshold').val(alertSettings.warning_threshold || 90);
            $('#noticeThreshold').val(alertSettings.notice_threshold || 180);
            $('#emailNotifications').prop('checked', alertSettings.email_notifications == 1);
            $('#smsNotifications').prop('checked', alertSettings.sms_notifications == 1);
            $('#dailyReports').prop('checked', alertSettings.daily_reports == 1);
            $('#weeklyReports').prop('checked', alertSettings.weekly_reports == 1);
            $('#autoReorder').prop('checked', alertSettings.auto_reorder == 1);
            $('#autoTransfer').prop('checked', alertSettings.auto_transfer == 1);
            $('#autoDiscount').prop('checked', alertSettings.auto_discount == 1);
            $('#autoArchive').prop('checked', alertSettings.auto_archive == 1);
        }

        const modal = new bootstrap.Modal(document.getElementById('alertSettingsModal'));
        modal.show();
    }

    async function saveAlertSettings() {
        try {
            const formData = new FormData();
            formData.append('critical_threshold', $('#criticalThreshold').val());
            formData.append('warning_threshold', $('#warningThreshold').val());
            formData.append('notice_threshold', $('#noticeThreshold').val());
            formData.append('email_notifications', $('#emailNotifications').is(':checked') ? '1' : '0');
            formData.append('sms_notifications', $('#smsNotifications').is(':checked') ? '1' : '0');
            formData.append('daily_reports', $('#dailyReports').is(':checked') ? '1' : '0');
            formData.append('weekly_reports', $('#weeklyReports').is(':checked') ? '1' : '0');
            formData.append('auto_reorder', $('#autoReorder').is(':checked') ? '1' : '0');
            formData.append('auto_transfer', $('#autoTransfer').is(':checked') ? '1' : '0');
            formData.append('auto_discount', $('#autoDiscount').is(':checked') ? '1' : '0');
            formData.append('auto_archive', $('#autoArchive').is(':checked') ? '1' : '0');

            const response = await fetch('index.php?r=inventory/save-alert-settings', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                showNotification(result.message || 'Alert settings saved successfully', 'success');
                const modal = bootstrap.Modal.getInstance(document.getElementById('alertSettingsModal'));
                modal.hide();

                // Reload settings and data
                await loadAlertSettings();
                await loadExpiryAlerts();
            } else {
                showNotification(result.message || 'Error saving alert settings', 'error');
            }
        } catch (error) {
            console.error('Error saving alert settings:', error);
            showNotification('Error saving alert settings', 'error');
        }
    }

    async function exportAlerts() {
        try {
            // Get current filter values
            const filters = {
                medicine: $('#filterMedicine').val(),
                status: $('#filterStatus').val(),
                days: $('#filterDays').val(),
                location: $('#filterLocation').val(),
                batch: $('#filterBatch').val()
            };

            // Build query string
            const queryParams = new URLSearchParams();
            Object.keys(filters).forEach(key => {
                if (filters[key]) {
                    queryParams.append(key, filters[key]);
                }
            });

            const response = await fetch(`index.php?r=inventory/export-expiry-alerts&${queryParams.toString()}`);
            const result = await response.json();

            if (result.success) {
                // Create and download file
                const blob = new Blob([atob(result.data)], {
                    type: 'text/csv'
                });
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = result.filename;
                a.click();
                window.URL.revokeObjectURL(url);

                showNotification(result.message || 'Alerts exported successfully', 'success');
            } else {
                showNotification(result.message || 'Error exporting alerts', 'error');
            }
        } catch (error) {
            console.error('Error exporting alerts:', error);
            showNotification('Error exporting alerts', 'error');
        }
    }

    function toggleColumns() {
        const modal = new bootstrap.Modal(document.getElementById('columnModal'));
        modal.show();
    }

    function initializeColumnVisibility() {
        // Set up column visibility checkboxes
        $('#colMedicine').on('change', function() {
            toggleColumn(0, this.checked);
        });
        $('#colBatch').on('change', function() {
            toggleColumn(1, this.checked);
        });
        $('#colExpiry').on('change', function() {
            toggleColumn(2, this.checked);
        });
        $('#colDaysLeft').on('change', function() {
            toggleColumn(3, this.checked);
        });
        $('#colLocation').on('change', function() {
            toggleColumn(4, this.checked);
        });
        $('#colStatus').on('change', function() {
            toggleColumn(5, this.checked);
        });
    }

    function toggleColumn(columnIndex, visible) {
        const table = $('#expiryTable');
        table.find('th').eq(columnIndex).toggle(visible);
        table.find('td:nth-child(' + (columnIndex + 1) + ')').toggle(visible);
    }

    function applyColumnVisibility() {
        const modal = bootstrap.Modal.getInstance(document.getElementById('columnModal'));
        modal.hide();
        showNotification('Column visibility updated', 'success');
    }

    function extendExpiryFromModal() {
        const modal = bootstrap.Modal.getInstance(document.getElementById('alertDetailsModal'));
        modal.hide();
        if (currentAlertId) {
            extendExpiry(currentAlertId);
        }
    }

    function showNotification(message, type) {
        const notification = $(`
            <div style="position: fixed; top: 20px; right: 20px; padding: 16px 20px; border-radius: 8px; color: white; font-weight: 500; z-index: 1001; background: ${type === 'success' ? '#10b981' : type === 'info' ? '#3b82f6' : type === 'error' ? '#ef4444' : '#f59e0b'}; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
                <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'info' ? 'info-circle' : type === 'error' ? 'exclamation-triangle' : 'exclamation-triangle'} me-2"></i>
                ${message}
            </div>
        `);

        $('body').append(notification);
        setTimeout(() => {
            notification.fadeOut(300, function() {
                $(this).remove();
            });
        }, 3000);
    }
</script>
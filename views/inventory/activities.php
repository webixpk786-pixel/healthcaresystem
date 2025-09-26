<?php
// Ensure data arrays are defined
$activities = isset($activities) ? $activities : [];

use app\components\SystemComponents;

$systemComponents = new SystemComponents();
$can = $systemComponents->checkModulePermission('25,35');
$canEdit = $can['canEdit'];
$canExport = $can['canExport'];
$canDelete = $can['canDelete'];
$canAdd = $can['canAdd'];
?>

<div class="activities-container">
    <!-- Header Section -->
    <div class="page-header">
        <div class="header-content">
            <div class="header-text">
                <h1>
                    <i class="fas fa-history"></i>
                    Inventory Activities
                </h1>
                <p>View all activity logs related to inventory management operations</p>
            </div>
            <div class="header-actions">
                <?php if ($canExport): ?>
                    <button class="btn btn-outline-secondary" onclick="exportActivities()">
                        <i class="fas fa-download"></i> Export
                    </button>
                <?php endif; ?>
                <button class="btn btn-primary"
                    style="display: flex;align-items: center;gap: 8px;justify-content: space-around;"
                    onclick="refreshActivities()">
                    <i class="fas fa-sync-alt"></i> Refresh
                </button>
            </div>
        </div>
    </div>

    <!-- Activity Summary Cards -->
    <div class="activity-summary">
        <div class="row g-3">
            <!-- Total Activities Card -->
            <div class="col-lg-3 col-md-6">
                <div class="summary-card total-activities-card">
                    <div class="card-content">
                        <i class="fas fa-list-alt"></i>
                        <div class="card-info">
                            <span class="count" id="totalActivitiesCount">0</span>
                            <span class="label">Total Activities</span>
                        </div>
                        <div class="card-badge">ACTIVITIES</div>
                    </div>
                </div>
            </div>

            <!-- Today's Activities Card -->
            <div class="col-lg-3 col-md-6">
                <div class="summary-card today-activities-card">
                    <div class="card-content">
                        <i class="fas fa-calendar-day"></i>
                        <div class="card-info">
                            <span class="count" id="todayActivitiesCount">0</span>
                            <span class="label">Today's Activities</span>
                        </div>
                        <div class="card-badge">TODAY</div>
                    </div>
                </div>
            </div>

            <!-- This Week Card -->
            <div class="col-lg-3 col-md-6">
                <div class="summary-card week-activities-card">
                    <div class="card-content">
                        <i class="fas fa-calendar-week"></i>
                        <div class="card-info">
                            <span class="count" id="weekActivitiesCount">0</span>
                            <span class="label">This Week</span>
                        </div>
                        <div class="card-badge">WEEK</div>
                    </div>
                </div>
            </div>

            <!-- This Month Card -->
            <div class="col-lg-3 col-md-6">
                <div class="summary-card month-activities-card">
                    <div class="card-content">
                        <i class="fas fa-calendar-alt"></i>
                        <div class="card-info">
                            <span class="count" id="monthActivitiesCount">0</span>
                            <span class="label">This Month</span>
                        </div>
                        <div class="card-badge">MONTH</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="main-content">
        <!-- Left Column - Filter Options -->
        <div class="left-column">
            <div class="filter-navigation">
                <h5>
                    <i class="fas fa-filter"></i>
                    Filter Options
                </h5>

                <div class="filter-options">
                    <div class="filter-group">
                        <label class="filter-label">Action Type</label>
                        <select class="form-select" id="actionFilter">
                            <option value="">All Actions</option>
                            <option value="Create">Create</option>
                            <option value="Update">Update</option>
                            <option value="Delete">Delete</option>
                            <option value="View">View</option>
                            <option value="Export">Export</option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label class="filter-label">Time Period</label>
                        <select class="form-select" id="timeFilter">
                            <option value="">All Time</option>
                            <option value="today">Today</option>
                            <option value="week">This Week</option>
                            <option value="month">This Month</option>
                            <option value="year">This Year</option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label class="filter-label">User</label>
                        <select class="form-select" id="userFilter">
                            <option value="">All Users</option>
                            <!-- Users will be populated dynamically -->
                        </select>
                    </div>

                    <div class="filter-group">
                        <label class="filter-label">Location</label>
                        <select class="form-select" id="locationFilter">
                            <option value="">All Locations</option>
                            <option value="Purchase Orders">Purchase Orders</option>
                            <option value="Medicines">Medicines</option>
                            <option value="Stock">Stock</option>
                            <option value="Categories">Categories</option>
                            <option value="Suppliers">Suppliers</option>
                            <option value="Manufacturers">Manufacturers</option>
                            <option value="Medicine Forms">Medicine Forms</option>
                            <option value="Expiry Alert Settings">Expiry Alert Settings</option>
                            <option value="Expiry Alerts">Expiry Alerts</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column - Content Area -->
        <div class="right-column">
            <!-- Content Header -->
            <div class="content-header">
                <div class="header-left">
                    <h5>
                        <i class="fas fa-history"></i>
                        Activity Logs
                    </h5>
                </div>
                <div class="header-right">
                    <button class="btn btn-outline-secondary" onclick="clearAllFilters()">
                        <i class="fas fa-times"></i> Clear Filters
                    </button>
                </div>
            </div>

            <!-- Search and Filter Section -->
            <div class="filter-section">
                <div class="row g-2">
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" class="form-control" id="searchInput" placeholder="Search activities...">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="sortBy">
                            <option value="created_at">Sort by Date</option>
                            <option value="action">Sort by Action</option>
                            <option value="username">Sort by User</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="sortOrder">
                            <option value="desc">Newest First</option>
                            <option value="asc">Oldest First</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Content Body -->
            <div class="content-body">
                <div class="table-responsive">
                    <table class="table data-table">
                        <thead>
                            <tr>
                                <th style="width: 12%">Date & Time</th>
                                <th style="width: 8%">User</th>
                                <th style="width: 8%">Action</th>
                                <th style="width: 15%">Location</th>
                                <th style="width: 35%">Description</th>
                                <th style="width: 8%">Record ID</th>
                                <th style="width: 8%">IP Address</th>
                                <th style="width: 6%">Details</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                            <!-- Content will be dynamically generated -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Activity Details Modal -->
<div class="modal fade" id="activityModal" tabindex="-1" role="dialog" aria-labelledby="activityModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" id="modalHeader">
                <h5 class="modal-title" id="modalTitle">
                    <i class="fas fa-info-circle"></i>Activity Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modalBody">
                <!-- Activity details will be populated here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<style>
    .activities-container {
        min-height: 95vh;
        margin: -20px;
        margin-bottom: 24px;
    }

    .page-header {
        margin-bottom: 20px;
    }

    .header-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .header-text h1 {
        color: #1e293b;
        font-size: 1.4rem;
        font-weight: 600;
        margin-bottom: 6px;
        display: flex;
        align-items: center;
    }

    .header-text h1 i {
        color: #6366f1;
        margin-right: 8px;
        font-size: 1.2rem;
    }

    .header-text p {
        color: #64748b;
        font-size: 0.8rem;
        margin: 0;
    }

    .header-actions {
        display: flex;
        gap: 8px;
    }

    .activity-summary {
        margin-bottom: 24px;
    }

    .summary-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 10px;
        padding: 12px;
        box-shadow: 0 3px 12px rgba(102, 126, 234, 0.25);
        border: none;
        position: relative;
        overflow: hidden;
        min-height: 65px;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .summary-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.35);
    }

    .today-activities-card {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        box-shadow: 0 3px 12px rgba(16, 185, 129, 0.25);
    }

    .week-activities-card {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        box-shadow: 0 3px 12px rgba(245, 158, 11, 0.25);
    }

    .month-activities-card {
        background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
        box-shadow: 0 3px 12px rgba(139, 92, 246, 0.25);
    }

    .card-content {
        display: flex;
        align-items: center;
        font-size: 1.1rem;
        font-weight: 600;
        color: white;
    }

    .card-content i {
        font-size: 1rem;
        opacity: 0.9;
        margin-right: 6px;
    }

    .card-info {
        display: flex;
        flex-direction: column;
    }

    .card-info .count {
        font-size: 1.1rem;
        font-weight: 600;
    }

    .card-info .label {
        font-size: 0.65rem;
        font-weight: 500;
        opacity: 0.9;
    }

    .card-badge {
        margin-left: auto;
        background: rgba(255, 255, 255, 0.2);
        padding: 2px 6px;
        border-radius: 8px;
        backdrop-filter: blur(10px);
        font-size: 0.6rem;
        font-weight: 600;
    }

    .main-content {
        display: flex;
        gap: 16px;
        height: 65vh;
    }

    .left-column {
        flex: 0 0 280px;
        background: white;
        border-radius: 10px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        border: 1px solid #e2e8f0;
        display: flex;
        flex-direction: column;
        height: 60vh;
    }

    .filter-navigation {
        padding: 16px;
    }

    .filter-navigation h5 {
        margin: 0 0 16px 0;
        color: #374151;
        font-weight: 600;
        display: flex;
        align-items: center;
    }

    .filter-navigation h5 i {
        color: #6366f1;
        margin-right: 8px;
    }

    .filter-options {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .filter-group {
        display: flex;
        flex-direction: column;
    }

    .filter-label {
        font-size: 0.8rem;
        font-weight: 500;
        color: #374151;
        margin-bottom: 4px;
    }

    .form-select {
        padding: 6px 8px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        font-size: 0.8rem;
        background-color: white;
    }

    .form-select:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.1);
        outline: none;
    }

    .right-column {
        flex: 1;
        background: white;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        border: 1px solid #e2e8f0;
        display: flex;
        flex-direction: column;
        height: 62vh;
    }

    .content-header {
        padding: 16px;
        border-bottom: 1px solid #e2e8f0;
        background: #f8fafc;
        border-radius: 8px 8px 0 0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .content-header h5 {
        margin: 0;
        color: #374151;
        font-weight: 600;
        display: flex;
        align-items: center;
    }

    .content-header h5 i {
        color: #6366f1;
        margin-right: 8px;
    }

    .filter-section {
        padding: 12px;
        border-bottom: 1px solid #e2e8f0;
        background: #f8fafc;
    }

    .content-body {
        flex: 1;
        overflow-y: auto;
    }

    .data-table {
        margin-bottom: 0;
    }

    .data-table thead th {
        background: #f8fafc;
        position: sticky;
        top: 0;
        z-index: 10;
        padding: 10px;
        font-weight: 600;
        color: #1e293b;
        font-size: 0.8rem;
        border-bottom: 2px solid #e2e8f0;
    }

    .data-table tbody tr {
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .data-table tbody tr:hover {
        background: #f8fafc;
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .data-table tbody tr.selected {
        background: #e0e7ff;
        border-left: 4px solid #6366f1;
    }

    .data-table tbody td {
        padding: 10px;
        font-weight: 500;
        color: #1e293b;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.8rem;
    }

    .action-badge {
        padding: 3px 6px;
        border-radius: 8px;
        font-size: 0.7rem;
        font-weight: 500;
        text-transform: uppercase;
    }

    .action-create {
        background: #f0fdf4;
        color: #16a34a;
    }

    .action-update {
        background: #dbeafe;
        color: #3b82f6;
    }

    .action-delete {
        background: #fee2e2;
        color: #dc2626;
    }

    .action-view {
        background: #f3f4f6;
        color: #6b7280;
    }

    .action-export {
        background: #fef3c7;
        color: #d97706;
    }

    .btn {
        padding: 6px 12px;
        border-radius: 6px;
        font-weight: 500;
        font-size: 0.8rem;
        transition: all 0.2s ease;
        cursor: pointer;
        border: none;
    }

    .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .btn-primary {
        background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
        color: white;
    }

    .btn-outline-secondary {
        background: white;
        color: #64748b;
        border: 1px solid #e2e8f0;
    }

    .btn-light {
        background: #f8fafc;
        color: #64748b;
        border: 1px solid #e2e8f0;
    }

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

    .modal-body {
        padding: 20px 24px;
        max-height: 60vh;
        overflow-y: auto;
    }

    .modal-footer {
        border-top: 1px solid #e2e8f0;
        padding: 16px 24px;
        background: #f8fafc;
    }

    .empty-state {
        text-align: center;
        padding: 40px;
        color: #64748b;
    }

    .empty-state i {
        font-size: 2rem;
        margin-bottom: 16px;
        opacity: 0.5;
    }

    .notification {
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 12px 16px;
        border-radius: 8px;
        color: white;
        font-weight: 500;
        z-index: 9999;
        display: flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .notification.success {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    }

    .notification.error {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    }

    .notification.warning {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    }

    .notification.info {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .main-content {
            flex-direction: column !important;
            height: auto !important;
        }

        .left-column {
            flex: none !important;
            height: auto !important;
            margin-bottom: 20px;
        }

        .right-column {
            height: 50vh !important;
        }

        .header-content {
            flex-direction: column;
            gap: 16px;
            align-items: flex-start;
        }

        .header-actions {
            width: 100%;
            justify-content: flex-end;
        }
    }

    @media (max-width: 576px) {
        .activities-container {
            padding: 10px;
        }

        .data-table thead th,
        .data-table tbody td {
            padding: 6px !important;
            font-size: 0.7rem !important;
        }

        .modal-dialog {
            margin: 10px;
        }
    }
</style>

<script>
    // Global variables
    let activities = <?= json_encode($activities) ?>;
    let filteredActivities = [...activities];

    // Initialize the application
    $(document).ready(function() {
        initializeApp();
        setupEventListeners();
        applyFilters(); // Apply initial filters instead of showing all activities
        calculateSummaryStats();
        populateUserFilter();
    });

    function initializeApp() {
        // Ensure activities array exists
        if (!Array.isArray(activities)) {
            activities = [];
        }
        filteredActivities = [...activities];
    }

    function setupEventListeners() {
        // Search and filter events
        $('#searchInput').on('input', debounce(applyFilters, 300));
        $('#actionFilter, #timeFilter, #userFilter, #locationFilter, #sortBy, #sortOrder').on('change', applyFilters);
    }

    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    function applyFilters() {
        const searchTerm = $('#searchInput').val().toLowerCase();
        const actionFilter = $('#actionFilter').val();
        const timeFilter = $('#timeFilter').val();
        const userFilter = $('#userFilter').val();
        const locationFilter = $('#locationFilter').val();
        const sortBy = $('#sortBy').val();
        const sortOrder = $('#sortOrder').val();

        // Define inventory-related locations
        const inventoryLocations = [
            'Purchase Orders', 'Medicines', 'Stock', 'Categories',
            'Suppliers', 'Manufacturers', 'Medicine Forms',
            'Expiry Alert Settings', 'Expiry Alerts'
        ];

        filteredActivities = activities.filter(activity => {
            // First filter: Only show inventory-related activities
            const isInventoryActivity = inventoryLocations.includes(activity.location);
            if (!isInventoryActivity) {
                return false;
            }

            const matchesSearch = !searchTerm ||
                (activity.description && activity.description.toLowerCase().includes(searchTerm)) ||
                (activity.username && activity.username.toLowerCase().includes(searchTerm)) ||
                (activity.action && activity.action.toLowerCase().includes(searchTerm)) ||
                (activity.location && activity.location.toLowerCase().includes(searchTerm));

            const matchesAction = !actionFilter || activity.action === actionFilter;
            const matchesUser = !userFilter || activity.user_id == userFilter;
            const matchesLocation = !locationFilter || activity.location === locationFilter;

            let matchesTime = true;
            if (timeFilter) {
                const now = new Date();
                const activityDate = new Date(activity.created_at);

                switch (timeFilter) {
                    case 'today':
                        matchesTime = activityDate.toDateString() === now.toDateString();
                        break;
                    case 'week':
                        const weekAgo = new Date(now.getTime() - 7 * 24 * 60 * 60 * 1000);
                        matchesTime = activityDate >= weekAgo;
                        break;
                    case 'month':
                        const monthAgo = new Date(now.getTime() - 30 * 24 * 60 * 60 * 1000);
                        matchesTime = activityDate >= monthAgo;
                        break;
                    case 'year':
                        const yearAgo = new Date(now.getTime() - 365 * 24 * 60 * 60 * 1000);
                        matchesTime = activityDate >= yearAgo;
                        break;
                }
            }

            return matchesSearch && matchesAction && matchesUser && matchesLocation && matchesTime;
        });

        // Sort data
        filteredActivities.sort((a, b) => {
            let aVal = a[sortBy] || a.created_at;
            let bVal = b[sortBy] || b.created_at;

            if (sortBy === 'created_at') {
                aVal = new Date(aVal);
                bVal = new Date(bVal);
            } else if (typeof aVal === 'string') {
                aVal = aVal.toLowerCase();
                bVal = bVal.toLowerCase();
            }

            if (sortOrder === 'asc') {
                return aVal > bVal ? 1 : -1;
            } else {
                return aVal < bVal ? 1 : -1;
            }
        });

        console.log('Filtered activities:', filteredActivities.length, 'out of', activities.length);
        updateTable(filteredActivities);
    }

    function updateTable(data) {
        const tbody = $('#tableBody');
        tbody.empty();

        if (data.length === 0) {
            tbody.append(`
            <tr>
                <td colspan="8" style="text-align: center; padding: 40px; color: #64748b;">
                    <div class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <h6>No activities found</h6>
                        <p>Try adjusting your search or filters</p>
                    </div>
                </td>
            </tr>
        `);
            return;
        }

        data.forEach(activity => {
            const row = createTableRow(activity);
            tbody.append(row);
        });
    }

    function createTableRow(activity) {
        const date = new Date(activity.created_at);
        const formattedDate = date.toLocaleDateString() + ' ' + date.toLocaleTimeString();

        const actionClass = `action-${activity.action.toLowerCase()}`;
        const actionBadge = `<span class="action-badge ${actionClass}">${activity.action}</span>`;

        return `
            <tr onclick="showActivityDetails(${JSON.stringify(activity).replace(/"/g, '&quot;')})">
                <td>${formattedDate}</td>
                <td>${activity.username || 'Unknown'}</td>
                <td>${actionBadge}</td>
                <td>${activity.location}</td>
                <td>${activity.description || 'No description'}</td>
                <td>${activity.record_id || 'N/A'}</td>
                <td>${activity.ip_address || 'N/A'}</td>
                <td style="text-align: center;">
                    <button class="btn btn-outline-secondary btn-sm" onclick="event.stopPropagation(); showActivityDetails(${JSON.stringify(activity).replace(/"/g, '&quot;')})" title="View Details">
                        <i class="fas fa-eye"></i>
                    </button>
                </td>
            </tr>
        `;
    }

    function showActivityDetails(activity) {
        const date = new Date(activity.created_at);
        const formattedDate = date.toLocaleDateString() + ' ' + date.toLocaleTimeString();
        const timeAgo = getTimeAgo(date);

        const actionClass = `action-${activity.action.toLowerCase()}`;
        const actionBadge = `<span class="action-badge ${actionClass}">${activity.action}</span>`;

        // Get action icon
        const actionIcon = getActionIcon(activity.action);

        // Get location icon
        const locationIcon = getLocationIcon(activity.location);

        $('#modalTitle').html(`<i class="fas fa-info-circle"></i> Activity Details`);
        $('#modalHeader').css('background', 'linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%)');

        $('#modalBody').html(`
            <div class="activity-details-compact">
                <!-- Header Section -->
                <div class="activity-header-compact">
                    <div class="activity-icon-compact">
                        <i class="${actionIcon}"></i>
                    </div>
                    <div class="activity-title-compact">
                        <h4>${activity.action} Operation</h4>
                        <p class="activity-subtitle-compact">${activity.location}</p>
                    </div>
                    <div class="activity-badge-compact">
                        ${actionBadge}
                    </div>
                </div>

                <!-- Compact Info Layout -->
                <div class="info-layout-compact">
                    <!-- Left Column -->
                    <div class="info-column">
                        <div class="info-row">
                            <div class="info-label">
                                <i class="fas fa-calendar-alt"></i>
                                <span>Date & Time</span>
                            </div>
                            <div class="info-value-compact">
                                <div class="primary-value">${formattedDate}</div>
                                <div class="secondary-value">${timeAgo}</div>
                            </div>
                        </div>

                        <div class="info-row">
                            <div class="info-label">
                                <i class="fas fa-user"></i>
                                <span>User</span>
                            </div>
                            <div class="info-value-compact">
                                <div class="primary-value">${activity.username || 'Unknown'}</div>
                                <div class="secondary-value">${activity.email || 'No email'}</div>
                            </div>
                        </div>

                        <div class="info-row">
                            <div class="info-label">
                                <i class="${locationIcon}"></i>
                                <span>Location</span>
                            </div>
                            <div class="info-value-compact">
                                <div class="primary-value">${activity.location}</div>
                            </div>
                        </div>

                        <div class="info-row">
                            <div class="info-label">
                                <i class="fas fa-hashtag"></i>
                                <span>Record ID</span>
                            </div>
                            <div class="info-value-compact">
                                <div class="primary-value">${activity.record_id || 'N/A'}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="info-column">
                        <div class="info-row">
                            <div class="info-label">
                                <i class="fas fa-globe"></i>
                                <span>IP Address</span>
                            </div>
                            <div class="info-value-compact">
                                <div class="primary-value">${activity.ip_address || 'N/A'}</div>
                            </div>
                        </div>

                        <div class="info-row full-height">
                            <div class="info-label">
                                <i class="fas fa-align-left"></i>
                                <span>Description</span>
                            </div>
                            <div class="info-value-compact">
                                <div class="description-compact">${activity.description || 'No description available'}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- User Agent Row -->
                <div class="user-agent-row">
                    <div class="info-label">
                        <i class="fas fa-desktop"></i>
                        <span>User Agent</span>
                    </div>
                    <div class="user-agent-compact">${activity.user_agent || 'N/A'}</div>
                </div>
            </div>
        `);

        const modal = new bootstrap.Modal(document.getElementById('activityModal'));
        modal.show();
    }

    function getTimeAgo(date) {
        const now = new Date();
        const diffInSeconds = Math.floor((now - date) / 1000);

        if (diffInSeconds < 60) return 'Just now';
        if (diffInSeconds < 3600) return `${Math.floor(diffInSeconds / 60)} minutes ago`;
        if (diffInSeconds < 86400) return `${Math.floor(diffInSeconds / 3600)} hours ago`;
        if (diffInSeconds < 2592000) return `${Math.floor(diffInSeconds / 86400)} days ago`;
        return `${Math.floor(diffInSeconds / 2592000)} months ago`;
    }

    function getActionIcon(action) {
        const icons = {
            'Create': 'fas fa-plus-circle',
            'Update': 'fas fa-edit',
            'Delete': 'fas fa-trash-alt',
            'View': 'fas fa-eye',
            'Export': 'fas fa-download'
        };
        return icons[action] || 'fas fa-info-circle';
    }

    function getLocationIcon(location) {
        const icons = {
            'Purchase Orders': 'fas fa-shopping-cart',
            'Medicines': 'fas fa-pills',
            'Stock': 'fas fa-boxes',
            'Categories': 'fas fa-tags',
            'Suppliers': 'fas fa-truck',
            'Manufacturers': 'fas fa-industry',
            'Medicine Forms': 'fas fa-capsules',
            'Expiry Alert Settings': 'fas fa-cog',
            'Expiry Alerts': 'fas fa-exclamation-triangle'
        };
        return icons[location] || 'fas fa-map-marker-alt';
    }

    function calculateSummaryStats() {
        const now = new Date();
        const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
        const weekAgo = new Date(now.getTime() - 7 * 24 * 60 * 60 * 1000);
        const monthAgo = new Date(now.getTime() - 30 * 24 * 60 * 60 * 1000);

        // Define inventory-related locations
        const inventoryLocations = [
            'Purchase Orders', 'Medicines', 'Stock', 'Categories',
            'Suppliers', 'Manufacturers', 'Medicine Forms',
            'Expiry Alert Settings', 'Expiry Alerts'
        ];

        let totalCount = 0;
        let todayCount = 0;
        let weekCount = 0;
        let monthCount = 0;

        activities.forEach(activity => {
            // Only count inventory-related activities
            if (!inventoryLocations.includes(activity.location)) {
                return;
            }

            totalCount++;
            const activityDate = new Date(activity.created_at);

            if (activityDate >= today) {
                todayCount++;
            }
            if (activityDate >= weekAgo) {
                weekCount++;
            }
            if (activityDate >= monthAgo) {
                monthCount++;
            }
        });

        $('#totalActivitiesCount').text(totalCount);
        $('#todayActivitiesCount').text(todayCount);
        $('#weekActivitiesCount').text(weekCount);
        $('#monthActivitiesCount').text(monthCount);
    }


    function populateUserFilter() {
        const inventoryLocations = [
            'Purchase Orders', 'Medicines', 'Stock', 'Categories',
            'Suppliers', 'Manufacturers', 'Medicine Forms',
            'Expiry Alert Settings', 'Expiry Alerts'
        ];

        const inventoryActivities = activities.filter(activity =>
            inventoryLocations.includes(activity.location)
        );

        // Use a Map to ensure uniqueness by user_id
        const userMap = new Map();
        inventoryActivities.forEach(activity => {
            if (activity.user_id && activity.username) {
                userMap.set(activity.user_id, activity.username);
            }
        });

        const userFilter = $('#userFilter');
        userMap.forEach((username, id) => {
            userFilter.append(`<option value="${id}">${username}</option>`);
        });
    }


    function clearAllFilters() {
        $('#searchInput').val('');
        $('#actionFilter').val('');
        $('#timeFilter').val('');
        $('#userFilter').val('');
        $('#locationFilter').val('');
        $('#sortBy').val('created_at');
        $('#sortOrder').val('desc');
        applyFilters();
    }

    function refreshActivities() {
        const refreshBtn = $('button[onclick="refreshActivities()"]');
        const originalText = refreshBtn.html();
        refreshBtn.html('<i class="fas fa-spinner fa-spin"></i> Refreshing...').prop('disabled', true);

        // Reload the page to get fresh data
        setTimeout(() => {
            window.location.reload();
        }, 1000);
    }

    function exportActivities() {
        let csvContent = "Date,User,Action,Location,Description,Record ID,IP Address\n";

        filteredActivities.forEach(activity => {
            const date = new Date(activity.created_at);
            const formattedDate = date.toLocaleDateString() + ' ' + date.toLocaleTimeString();

            csvContent +=
                `"${formattedDate}","${activity.username || 'Unknown'}","${activity.action}","${activity.location}","${activity.description || ''}","${activity.record_id || ''}","${activity.ip_address || ''}"\n`;
        });

        const blob = new Blob([csvContent], {
            type: 'text/csv'
        });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'inventory_activities_export.csv';
        a.click();
        window.URL.revokeObjectURL(url);

        showNotification('Activities exported successfully', 'success');
    }

    function showNotification(message, type = 'info') {
        const notification = $(`
        <div class="notification ${type}">
            <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-triangle' : type === 'warning' ? 'exclamation-triangle' : 'info-circle'}"></i>
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

<style>
    /* Compact Activity Details Modal Styles - No Scroll */
    .activity-details-compact {
        padding: 0;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .activity-header-compact {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 16px;
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        border-radius: 8px;
        margin-bottom: 16px;
        border: 1px solid #e2e8f0;
    }

    .activity-icon-compact {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 18px;
        box-shadow: 0 3px 8px rgba(99, 102, 241, 0.3);
    }

    .activity-title-compact {
        flex: 1;
    }

    .activity-title-compact h4 {
        margin: 0;
        color: #1e293b;
        font-size: 1.1rem;
        font-weight: 700;
    }

    .activity-subtitle-compact {
        margin: 2px 0 0 0;
        color: #64748b;
        font-size: 0.8rem;
        font-weight: 500;
    }

    .activity-badge-compact {
        margin-left: auto;
    }

    .info-layout-compact {
        display: flex;
        gap: 16px;
        flex: 1;
        margin-bottom: 12px;
    }

    .info-column {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .info-row {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        padding: 8px 0;
        border-bottom: 1px solid #f1f5f9;
    }

    .info-row.full-height {
        flex: 1;
        align-items: flex-start;
    }

    .info-label {
        display: flex;
        align-items: center;
        gap: 6px;
        min-width: 100px;
        flex-shrink: 0;
    }

    .info-label i {
        color: #6366f1;
        font-size: 12px;
        width: 12px;
    }

    .info-label span {
        font-weight: 600;
        color: #374151;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .info-value-compact {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .primary-value {
        color: #1e293b;
        font-size: 0.85rem;
        font-weight: 600;
        line-height: 1.3;
    }

    .secondary-value {
        color: #64748b;
        font-size: 0.7rem;
        font-weight: 500;
    }

    .description-compact {
        background: #f8fafc;
        padding: 8px;
        border-radius: 6px;
        border-left: 3px solid #6366f1;
        font-style: italic;
        line-height: 1.4;
        font-size: 0.8rem;
        color: #374151;
        max-height: 60px;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .user-agent-row {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        padding: 8px 0;
        border-top: 1px solid #e2e8f0;
        margin-top: 8px;
    }

    .user-agent-compact {
        flex: 1;
        background: #f1f5f9;
        padding: 8px;
        border-radius: 6px;
        font-family: 'Courier New', monospace;
        font-size: 0.7rem;
        word-break: break-all;
        line-height: 1.3;
        border-left: 3px solid #64748b;
        color: #374151;
        max-height: 40px;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Modal Enhancements - Fixed Height */
    .modal-content {
        border-radius: 16px;
        border: none;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        overflow: hidden;
        max-height: 85vh;
        display: flex;
        flex-direction: column;
    }

    .modal-header {
        border-bottom: none;
        padding: 20px 20px 0 20px;
        background: transparent;
        flex-shrink: 0;
    }

    .modal-body {
        padding: 0 20px 20px 20px;
        overflow: hidden;
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .modal-footer {
        border-top: 1px solid #e2e8f0;
        padding: 16px 20px;
        background: #f8fafc;
        flex-shrink: 0;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .activity-header-compact {
            flex-direction: column;
            text-align: center;
            gap: 8px;
        }

        .activity-badge-compact {
            margin-left: 0;
        }

        .info-layout-compact {
            flex-direction: column;
            gap: 12px;
        }

        .info-column {
            gap: 6px;
        }

        .activity-icon-compact {
            width: 40px;
            height: 40px;
            font-size: 16px;
        }

        .modal-body {
            padding: 0 16px 16px 16px;
        }

        .info-label {
            min-width: 80px;
        }

        .info-label span {
            font-size: 0.7rem;
        }
    }

    /* Animation Effects */
    @keyframes slideInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .info-card {
        animation: slideInUp 0.3s ease forwards;
    }

    .info-card:nth-child(1) {
        animation-delay: 0.1s;
    }

    .info-card:nth-child(2) {
        animation-delay: 0.2s;
    }

    .info-card:nth-child(3) {
        animation-delay: 0.3s;
    }

    .info-card:nth-child(4) {
        animation-delay: 0.4s;
    }

    .info-card:nth-child(5) {
        animation-delay: 0.5s;
    }

    .info-card:nth-child(6) {
        animation-delay: 0.6s;
    }

    .info-card:nth-child(7) {
        animation-delay: 0.7s;
    }
</style>
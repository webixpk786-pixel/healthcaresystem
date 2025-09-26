<?php
// Ensure data arrays are defined
$categories = isset($categories) ? $categories : [];
$suppliers = isset($suppliers) ? $suppliers : [];
$manufacturers = isset($manufacturers) ? $manufacturers : [];
$medicineForms = isset($medicineForms) ? $medicineForms : [];

use app\components\SystemComponents;

$systemComponents = new SystemComponents();
$can = $systemComponents->checkModulePermission('25,34');
$canEdit = $can['canEdit'];
$canExport = $can['canExport'];
$canDelete = $can['canDelete'];
$canAdd = $can['canAdd'];
?>

<div class="configurations-container">
    <!-- Header Section -->
    <div class="page-header">
        <div class="header-content">
            <div class="header-text">
                <h1>
                    <i class="fas fa-cogs"></i>
                    System Configurations
                </h1>
                <p>Manage system settings, categories, suppliers, manufacturers, and medicine forms</p>
            </div>
            <div class="header-actions">
                <?php if ($canExport): ?>
                    <button class="btn btn-outline-secondary" onclick="exportData()">
                        <i class="fas fa-download"></i> Export
                    </button>
                <?php endif; ?>

                <button class="btn btn-primary"
                    style="display: flex;align-items: center;gap: 8px;justify-content: space-around;"
                    onclick="refreshData()">
                    <i class="fas fa-sync-alt"></i> Refresh
                </button>

            </div>
        </div>
    </div>

    <!-- Configuration Summary Cards -->
    <div class="config-summary">
        <div class="row g-3">
            <!-- Categories Card -->
            <div class="col-lg-3 col-md-6">
                <div class="summary-card categories-card">
                    <div class="card-content">
                        <i class="fas fa-tags"></i>
                        <div class="card-info">
                            <span class="count" id="categoriesCount"><?= count($categories) ?></span>
                            <span class="label">Categories</span>
                        </div>
                        <div class="card-badge">CATEGORIES</div>
                    </div>
                </div>
            </div>

            <!-- Suppliers Card -->
            <div class="col-lg-3 col-md-6">
                <div class="summary-card suppliers-card">
                    <div class="card-content">
                        <i class="fas fa-truck"></i>
                        <div class="card-info">
                            <span class="count" id="suppliersCount"><?= count($suppliers) ?></span>
                            <span class="label">Suppliers</span>
                        </div>
                        <div class="card-badge">SUPPLIERS</div>
                    </div>
                </div>
            </div>

            <!-- Manufacturers Card -->
            <div class="col-lg-3 col-md-6">
                <div class="summary-card manufacturers-card">
                    <div class="card-content">
                        <i class="fas fa-industry"></i>
                        <div class="card-info">
                            <span class="count" id="manufacturersCount"><?= count($manufacturers) ?></span>
                            <span class="label">Manufacturers</span>
                        </div>
                        <div class="card-badge">BRANDS</div>
                    </div>
                </div>
            </div>

            <!-- Forms Card -->
            <div class="col-lg-3 col-md-6">
                <div class="summary-card forms-card">
                    <div class="card-content">
                        <i class="fas fa-capsules"></i>
                        <div class="card-info">
                            <span class="count" id="formsCount"><?= count($medicineForms) ?></span>
                            <span class="label">Forms</span>
                        </div>
                        <div class="card-badge">FORMS</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="main-content">
        <!-- Left Column - Navigation Tabs -->
        <div class="left-column">
            <div class="tab-navigation">
                <h5>
                    <i class="fas fa-list"></i>
                    Configuration Types
                </h5>

                <div class="nav-tabs">
                    <button class="nav-tab active" data-tab="categories" onclick="switchTab('categories')">
                        <i class="fas fa-tags"></i>
                        <span>Categories</span>
                        <span class="badge" id="categoriesBadge"><?= count($categories) ?></span>
                    </button>
                    <button class="nav-tab" data-tab="suppliers" onclick="switchTab('suppliers')">
                        <i class="fas fa-truck"></i>
                        <span>Suppliers</span>
                        <span class="badge" id="suppliersBadge"><?= count($suppliers) ?></span>
                    </button>
                    <button class="nav-tab" data-tab="manufacturers" onclick="switchTab('manufacturers')">
                        <i class="fas fa-industry"></i>
                        <span>Manufacturers</span>
                        <span class="badge" id="manufacturersBadge"><?= count($manufacturers) ?></span>
                    </button>
                    <button class="nav-tab" data-tab="forms" onclick="switchTab('forms')">
                        <i class="fas fa-capsules"></i>
                        <span>Medicine Forms</span>
                        <span class="badge" id="formsBadge"><?= count($medicineForms) ?></span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Right Column - Content Area -->
        <div class="right-column">
            <!-- Content Header -->
            <div class="content-header">
                <div class="header-left">
                    <h5>
                        <i class="fas fa-tags" id="contentIcon"></i>
                        <span id="contentTitle">Categories</span>
                    </h5>
                </div>
                <div class="header-right">
                    <?php if ($canAdd): ?>
                        <button class="btn btn-primary" onclick="openAddModal()">
                            <i class="fas fa-plus"></i>Add New
                        </button>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Search and Filter Section -->
            <div class="filter-section">
                <div class="row g-2">
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" class="form-control" id="searchInput" placeholder="Search...">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="statusFilter">
                            <option value="">All Status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="sortBy">
                            <option value="name">Sort by Name</option>
                            <option value="created_at">Sort by Date</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-outline-secondary" onclick="clearFilters()">
                            <i class="fas fa-times"></i> Clear
                        </button>
                    </div>
                </div>
            </div>

            <!-- Content Body -->
            <div class="content-body">
                <div class="table-responsive">
                    <table class="table data-table">
                        <thead>
                            <tr id="tableHeader">
                                <!-- Headers will be dynamically generated -->
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

<!-- Add/Edit Modal -->
<div class="modal fade" id="itemModal" tabindex="-1" role="dialog" aria-labelledby="itemModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" id="modalHeader">
                <h5 class="modal-title" id="modalTitle">
                    <i class="fas fa-plus"></i>Add New Item
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="itemForm">
                    <input type="hidden" id="itemId" name="id">
                    <input type="hidden" id="itemType" name="type">
                    <div id="formFields">
                        <!-- Form fields will be dynamically generated -->
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveItem()">Save</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header" id="deleteModalHeader">
                <h5 class="modal-title" id="deleteTitle">
                    <i class="fas fa-exclamation-triangle"></i>Confirm Delete
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="delete-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h6 id="deleteTitle">Are you sure you want to delete this item?</h6>
                <p id="deleteMessage">This action cannot be undone and will permanently remove the item from your
                    system.</p>
                <div id="deleteItemInfo"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="confirmDelete()">Delete</button>
            </div>
        </div>
    </div>
</div>

<style>
    .configurations-container {
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

    .config-summary {
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

    .suppliers-card {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        box-shadow: 0 4px 16px rgba(16, 185, 129, 0.25);
    }

    .manufacturers-card {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        box-shadow: 0 4px 16px rgba(245, 158, 11, 0.25);
    }

    .forms-card {
        background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
        box-shadow: 0 4px 16px rgba(139, 92, 246, 0.25);
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
        /* box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1); */
        /* border: 1px solid #e2e8f0; */
        display: flex;
        flex-direction: column;
        height: 60vh;
    }

    .tab-navigation {
        padding: 16px;
        /* border-bottom: 1px solid #e2e8f0; */
        /* background: #f8fafc; */
        /* border-radius: 8px 8px 0 0; */
    }

    .tab-navigation h5 {
        margin: 0 0 16px 0;
        color: #374151;
        font-weight: 600;
        display: flex;
        align-items: center;
    }

    .tab-navigation h5 i {
        color: #6366f1;
        margin-right: 8px;
    }

    .nav-tabs {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .nav-tab {
        text-align: left;
        border: none;
        background: #f1f5f9;
        color: #64748b;
        padding: 12px 16px;
        border-radius: 8px;
        font-weight: 500;
        display: flex;
        align-items: center;
        transition: all 0.2s ease;
        cursor: pointer;
    }

    .nav-tab:hover {
        background: #e2e8f0;
        color: #374151;
    }

    .nav-tab.active {
        background: #6366f1;
        color: white;
    }

    .nav-tab i {
        margin-right: 8px;
    }

    .nav-tab .badge {
        margin-left: auto;
        background: rgba(255, 255, 255, 0.2);
        color: inherit;
        font-size: 0.7rem;
        padding: 2px 6px;
        border-radius: 4px;
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
        /* padding: 16px; */
    }

    .data-table {
        margin-bottom: 0;
    }

    .data-table thead th {
        background: #f8fafc;
        position: sticky;
        top: 0;
        z-index: 10;
        padding: 12px;
        font-weight: 600;
        color: #1e293b;
        font-size: 0.9rem;
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
        padding: 12px;
        font-weight: 500;
        color: #1e293b;
        border-bottom: 1px solid #f1f5f9;
    }

    .action-buttons {
        display: flex;
        gap: 4px;
        justify-content: center;
    }

    .btn-action {
        border: none;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 0.75rem;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-edit {
        background: #dbeafe;
        color: #3b82f6;
    }

    .btn-edit:hover {
        background: #3b82f6;
        color: white;
    }

    .btn-delete {
        background: #fee2e2;
        color: #ef4444;
    }

    .btn-delete:hover {
        background: #ef4444;
        color: white;
    }

    .status-badge {
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 0.8rem;
        font-weight: 500;
    }

    .status-active {
        background: #f0fdf4;
        color: #16a34a;
    }

    .status-inactive {
        background: #fef2f2;
        color: #dc2626;
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

    .form-group {
        margin-bottom: 16px;
    }

    .form-label {
        display: block;
        margin-bottom: 6px;
        font-weight: 500;
        color: #374151;
    }

    .form-control {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        font-size: 0.9rem;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .form-control:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        outline: none;
    }

    .btn {
        padding: 8px 16px;
        border-radius: 6px;
        font-weight: 500;
        font-size: 0.9rem;
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

    .btn-danger {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: white;
    }

    .delete-icon {
        color: #ef4444;
        font-size: 1.5rem;
        margin-bottom: 8px;
        text-align: center;
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
        .configurations-container {
            padding: 10px;
        }

        .data-table thead th,
        .data-table tbody td {
            padding: 8px !important;
            font-size: 0.8rem !important;
        }

        .modal-dialog {
            margin: 10px;
        }
    }
</style>

<script>
    // Global variables
    let currentTab = 'categories';
    let currentItemId = null;
    let currentItemType = null;
    let selectedItem = null;

    // Configuration data
    const configData = {
        categories: <?= json_encode($categories) ?>,
        suppliers: <?= json_encode($suppliers) ?>,
        manufacturers: <?= json_encode($manufacturers) ?>,
        forms: <?= json_encode($medicineForms) ?>
    };

    // Configuration definitions
    const configDefs = {
        categories: {
            title: 'Categories',
            icon: 'fas fa-tags',
            color: '#667eea',
            fields: [{
                    name: 'name',
                    label: 'Category Name',
                    type: 'text',
                    required: true
                },
                {
                    name: 'description',
                    label: 'Description',
                    type: 'textarea'
                }
            ],
            headers: [{
                    key: 'name',
                    label: 'Name',
                    width: '60%'
                },
                {
                    key: 'status',
                    label: 'Status',
                    width: '25%'
                },
                {
                    key: 'actions',
                    label: 'Actions',
                    width: '15%'
                }
            ]
        },
        suppliers: {
            title: 'Suppliers',
            icon: 'fas fa-truck',
            color: '#10b981',
            fields: [{
                    name: 'name',
                    label: 'Supplier Name',
                    type: 'text',
                    required: true
                },
                {
                    name: 'contact_person',
                    label: 'Contact Person',
                    type: 'text'
                },
                {
                    name: 'phone',
                    label: 'Phone',
                    type: 'text'
                },
                {
                    name: 'email',
                    label: 'Email',
                    type: 'email'
                },
                {
                    name: 'address',
                    label: 'Address',
                    type: 'textarea'
                },
                {
                    name: 'status',
                    label: 'Status',
                    type: 'select',
                    options: ['active', 'inactive']
                }
            ],
            headers: [{
                    key: 'name',
                    label: 'Name',
                    width: '40%'
                },
                {
                    key: 'contact_person',
                    label: 'Contact Person',
                    width: '25%'
                },
                {
                    key: 'status',
                    label: 'Status',
                    width: '20%'
                },
                {
                    key: 'actions',
                    label: 'Actions',
                    width: '15%'
                }
            ]
        },
        manufacturers: {
            title: 'Manufacturers',
            icon: 'fas fa-industry',
            color: '#f59e0b',
            fields: [{
                    name: 'name',
                    label: 'Manufacturer Name',
                    type: 'text',
                    required: true
                },
                {
                    name: 'contact_person',
                    label: 'Contact Person',
                    type: 'text'
                },
                {
                    name: 'phone',
                    label: 'Phone',
                    type: 'text'
                },
                {
                    name: 'email',
                    label: 'Email',
                    type: 'email'
                },
                {
                    name: 'website',
                    label: 'Website',
                    type: 'url'
                },
                {
                    name: 'country',
                    label: 'Country',
                    type: 'text'
                },
                {
                    name: 'description',
                    label: 'Description',
                    type: 'textarea'
                },
                {
                    name: 'status',
                    label: 'Status',
                    type: 'select',
                    options: ['active', 'inactive']
                }
            ],
            headers: [{
                    key: 'name',
                    label: 'Name',
                    width: '40%'
                },
                {
                    key: 'country',
                    label: 'Country',
                    width: '25%'
                },
                {
                    key: 'status',
                    label: 'Status',
                    width: '20%'
                },
                {
                    key: 'actions',
                    label: 'Actions',
                    width: '15%'
                }
            ]
        },
        forms: {
            title: 'Medicine Forms',
            icon: 'fas fa-capsules',
            color: '#8b5cf6',
            fields: [{
                    name: 'name',
                    label: 'Form Name',
                    type: 'text',
                    required: true
                },
                {
                    name: 'description',
                    label: 'Description',
                    type: 'textarea'
                },
                {
                    name: 'unit_type',
                    label: 'Unit Type',
                    type: 'select',
                    options: ['unit', 'tablet', 'capsule', 'ml', 'vial', 'tube', 'sachet', 'puff', 'patch',
                        'suppository', 'spray', 'lozenge'
                    ]
                },
                {
                    name: 'status',
                    label: 'Status',
                    type: 'select',
                    options: ['active', 'inactive']
                }
            ],
            headers: [{
                    key: 'name',
                    label: 'Form Name',
                    width: '50%'
                },
                {
                    key: 'unit_type',
                    label: 'Unit Type',
                    width: '25%'
                },
                {
                    key: 'status',
                    label: 'Status',
                    width: '10%'
                },
                {
                    key: 'actions',
                    label: 'Actions',
                    width: '15%'
                }
            ]
        }
    };

    // Initialize the application
    $(document).ready(function() {
        initializeApp();
        setupEventListeners();
        switchTab('categories');
    });

    function initializeApp() {
        // Ensure all data arrays exist
        Object.keys(configData).forEach(key => {
            if (!Array.isArray(configData[key])) {
                configData[key] = [];
            }
        });

        updateSummaryCards();
    }

    function setupEventListeners() {
        // Modal events
        $('#itemModal').on('hidden.bs.modal', function() {
            resetForm();
        });

        $('#deleteModal').on('hidden.bs.modal', function() {
            currentItemId = null;
            currentItemType = null;
        });

        // Search and filter events
        $('#searchInput').on('input', debounce(applyFilters, 300));
        $('#statusFilter, #sortBy').on('change', applyFilters);
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

    function switchTab(tabName) {
        currentTab = tabName;

        // Update navigation
        $('.nav-tab').removeClass('active');
        $(`.nav-tab[data-tab="${tabName}"]`).addClass('active');

        // Update content
        const config = configDefs[tabName];
        $('#contentTitle').text(config.title);
        $('#contentIcon').attr('class', config.icon);

        // Update table headers
        updateTableHeaders(config.headers);

        // Apply filters
        applyFilters();
    }

    function updateTableHeaders(headers) {
        const headerRow = $('#tableHeader');
        headerRow.empty();

        headers.forEach(header => {
            headerRow.append(`<th style="width: ${header.width}">${header.label}</th>`);
        });
    }

    function applyFilters() {
        const searchTerm = $('#searchInput').val().toLowerCase();
        const statusFilter = $('#statusFilter').val();
        const sortBy = $('#sortBy').val();

        let filteredData = configData[currentTab].filter(item => {
            const matchesSearch = !searchTerm ||
                item.name.toLowerCase().includes(searchTerm) ||
                (item.description && item.description.toLowerCase().includes(searchTerm)) ||
                (item.contact_person && item.contact_person.toLowerCase().includes(searchTerm)) ||
                (item.country && item.country.toLowerCase().includes(searchTerm));

            const matchesStatus = !statusFilter || (item.status || 'active') === statusFilter;

            return matchesSearch && matchesStatus;
        });

        // Sort data
        filteredData.sort((a, b) => {
            let aVal = a[sortBy] || a.name;
            let bVal = b[sortBy] || b.name;

            if (typeof aVal === 'string') {
                aVal = aVal.toLowerCase();
                bVal = bVal.toLowerCase();
            }

            return aVal > bVal ? 1 : -1;
        });

        updateTable(filteredData);
    }

    function updateTable(data) {
        const tbody = $('#tableBody');
        tbody.empty();

        if (data.length === 0) {
            tbody.append(`
            <tr>
                <td colspan="4" style="text-align: center; padding: 40px; color: #64748b;">
                    <div class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <h6>No items found</h6>
                        <p>Try adjusting your search or filters</p>
                    </div>
                </td>
            </tr>
        `);
            return;
        }

        data.forEach(item => {
            const row = createTableRow(item);
            tbody.append(row);
        });
    }

    function createTableRow(item) {
        const config = configDefs[currentTab];
        let row = `<tr data-id="${item.id}" onclick="selectItem(${JSON.stringify(item).replace(/"/g, '&quot;')})">`;

        config.headers.forEach(header => {
            if (header.key === 'actions') {
                row += `
                <td style="text-align: center;">
                    <div class="action-buttons">
                        <?php if ($canEdit): ?>
                        <button class="btn-action btn-edit" onclick="event.stopPropagation(); editItem(${item.id})" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <?php endif; ?>
                        <?php if ($canDelete): ?>
                        <button class="btn-action btn-delete" onclick="event.stopPropagation(); deleteItem(${item.id})" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                        <?php endif; ?>
                    </div>
                </td>
            `;
            } else if (header.key === 'status') {
                const status = item.status || 'active';
                row += `
                <td>
                    <span class="status-badge status-${status}">
                        ${status.charAt(0).toUpperCase() + status.slice(1)}
                    </span>
                </td>
            `;
            } else if (header.key === 'unit_type') {
                row += `
                <td>
                    <span class="status-badge" style="background: #e0e7ff; color: #3730a3;">
                        ${item.unit_type || 'unit'}
                    </span>
                </td>
            `;
            } else {
                row += `<td>${item[header.key] || 'N/A'}</td>`;
            }
        });

        row += '</tr>';
        return row;
    }

    function selectItem(item) {
        selectedItem = item;
        $('.data-table tbody tr').removeClass('selected');
        $(`tr[data-id="${item.id}"]`).addClass('selected');
    }

    function openAddModal() {
        currentItemId = null;
        currentItemType = currentTab;

        const config = configDefs[currentTab];
        $('#modalTitle').html(`<i class="${config.icon}"></i> Add New ${config.title.slice(0, -1)}`);
        $('#modalHeader').css('background', `linear-gradient(135deg, ${config.color} 0%, ${config.color}dd 100%)`);

        resetForm();
        generateFormFields();

        const modal = new bootstrap.Modal(document.getElementById('itemModal'));
        modal.show();
    }

    function editItem(id) {
        currentItemId = id;
        currentItemType = currentTab;

        const item = configData[currentTab].find(item => item.id == id);
        if (!item) {
            showNotification('Item not found', 'error');
            return;
        }

        const config = configDefs[currentTab];
        $('#modalTitle').html(`<i class="${config.icon}"></i> Edit ${config.title.slice(0, -1)}`);
        $('#modalHeader').css('background', `linear-gradient(135deg, ${config.color} 0%, ${config.color}dd 100%)`);

        resetForm();
        generateFormFields();

        // Populate form fields
        Object.keys(item).forEach(key => {
            const field = $(`[name="${key}"]`);
            if (field.length) {
                field.val(item[key]);
            }
        });

        const modal = new bootstrap.Modal(document.getElementById('itemModal'));
        modal.show();
    }

    function deleteItem(id) {
        currentItemId = id;
        currentItemType = currentTab;

        const item = configData[currentTab].find(item => item.id == id);
        if (!item) {
            showNotification('Item not found', 'error');
            return;
        }

        const config = configDefs[currentTab];
        $('#deleteTitle').text(`Delete ${config.title.slice(0, -1)}`);
        $('#deleteMessage').text(`Are you sure you want to delete "${item.name}"? This action cannot be undone.`);
        $('#deleteModalHeader').css('background', `linear-gradient(135deg, ${config.color} 0%, ${config.color}dd 100%)`);

        $('#deleteItemInfo').html(`
        <div class="alert alert-warning" style="background: #fef3c7; border: 1px solid #f59e0b; color: #92400e; border-radius: 8px; padding: 16px;">
            <div style="display: flex; align-items: center;">
                <i class="${config.icon}" style="font-size: 1.2rem; margin-right: 8px;"></i>
                <div>
                    <strong>${item.name}</strong><br>
                    <small>${item.description || 'No description available'}</small>
                </div>
            </div>
        </div>
    `);

        const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
        modal.show();
    }

    function generateFormFields() {
        const config = configDefs[currentTab];
        let fields = '';

        config.fields.forEach(field => {
            fields += `<div class="form-group">`;
            fields +=
                `<label class="form-label" for="${field.name}">${field.label}${field.required ? ' *' : ''}</label>`;

            if (field.type === 'textarea') {
                fields +=
                    `<textarea class="form-control" id="${field.name}" name="${field.name}" rows="3" ${field.required ? 'required' : ''}></textarea>`;
            } else if (field.type === 'select') {
                fields +=
                    `<select class="form-control" id="${field.name}" name="${field.name}" ${field.required ? 'required' : ''}>`;
                field.options.forEach(option => {
                    fields +=
                        `<option value="${option}">${option.charAt(0).toUpperCase() + option.slice(1)}</option>`;
                });
                fields += `</select>`;
            } else {
                fields +=
                    `<input type="${field.type}" class="form-control" id="${field.name}" name="${field.name}" ${field.required ? 'required' : ''}>`;
            }

            fields += `</div>`;
        });

        $('#formFields').html(fields);
    }

    function resetForm() {
        $('#itemForm')[0].reset();
        $('#itemId').val('');
        $('#itemType').val(currentTab);
    }

    function saveItem() {
        const form = $('#itemForm')[0];

        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        const formData = new FormData(form);
        const isEdit = currentItemId !== null;

        // Show loading state
        const saveBtn = $('button[onclick="saveItem()"]');
        const originalText = saveBtn.html();
        saveBtn.html('<i class="fas fa-spinner fa-spin"></i> Saving...').prop('disabled', true);

        // Determine URL
        let url = '';
        if (currentTab === 'forms') {
            url = isEdit ?
                '<?= Yii::$app->urlManager->createUrl(["inventory/update-medicine-form"]) ?>' :
                '<?= Yii::$app->urlManager->createUrl(["inventory/add-medicine-form"]) ?>';
        } else {
            const action = isEdit ? 'update' : 'add';
            const type = currentTab === 'categories' ? 'category' :
                currentTab === 'suppliers' ? 'supplier' :
                currentTab === 'manufacturers' ? 'manufacturer' : currentTab;
            url = `<?= Yii::$app->urlManager->createUrl(["inventory"]) ?>/${action}-${type}`;
        }

        fetch(url, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification(data.message || 'Item saved successfully', 'success');
                    const modal = bootstrap.Modal.getInstance(document.getElementById('itemModal'));
                    modal.hide();

                    // Update local data
                    if (isEdit) {
                        const index = configData[currentTab].findIndex(item => item.id == currentItemId);
                        if (index !== -1) {
                            const updatedItem = Object.fromEntries(formData.entries());
                            updatedItem.id = currentItemId;
                            configData[currentTab][index] = updatedItem;
                        }
                    } else {
                        const newItem = Object.fromEntries(formData.entries());
                        newItem.id = data.id || Date.now();
                        configData[currentTab].push(newItem);
                    }

                    updateSummaryCards();
                    applyFilters();
                } else {
                    showNotification(data.message || 'Error saving item', 'error');
                }
            })
            .catch(error => {
                console.error('Error saving item:', error);
                showNotification('Error saving item. Please try again.', 'error');
            })
            .finally(() => {
                saveBtn.html(originalText).prop('disabled', false);
            });
    }

    function confirmDelete() {
        if (!currentItemId || !currentItemType) return;

        // Show loading state
        const deleteBtn = $('button[onclick="confirmDelete()"]');
        const originalText = deleteBtn.html();
        deleteBtn.html('<i class="fas fa-spinner fa-spin"></i> Deleting...').prop('disabled', true);

        const formData = new FormData();
        formData.append('id', currentItemId);

        // Determine URL
        let url = '';
        if (currentTab === 'forms') {
            url = '<?= Yii::$app->urlManager->createUrl(["inventory/delete-medicine-form"]) ?>';
        } else {
            const type = currentTab === 'categories' ? 'category' :
                currentTab === 'suppliers' ? 'supplier' :
                currentTab === 'manufacturers' ? 'manufacturer' : currentTab;
            url = `<?= Yii::$app->urlManager->createUrl(["inventory"]) ?>/delete-${type}`;
        }

        fetch(url, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification(data.message || 'Item deleted successfully', 'success');
                    const modal = bootstrap.Modal.getInstance(document.getElementById('deleteModal'));
                    modal.hide();

                    // Remove from local data
                    configData[currentTab] = configData[currentTab].filter(item => item.id != currentItemId);

                    updateSummaryCards();
                    applyFilters();

                    if (selectedItem && selectedItem.id == currentItemId) {
                        selectedItem = null;
                    }
                } else {
                    showNotification(data.message || 'Error deleting item', 'error');
                }
            })
            .catch(error => {
                console.error('Error deleting item:', error);
                showNotification('Error deleting item. Please try again.', 'error');
            })
            .finally(() => {
                deleteBtn.html(originalText).prop('disabled', false);
            });
    }

    function updateSummaryCards() {
        $('#categoriesCount').text(configData.categories.length);
        $('#suppliersCount').text(configData.suppliers.length);
        $('#manufacturersCount').text(configData.manufacturers.length);
        $('#formsCount').text(configData.forms.length);

        $('#categoriesBadge').text(configData.categories.length);
        $('#suppliersBadge').text(configData.suppliers.length);
        $('#manufacturersBadge').text(configData.manufacturers.length);
        $('#formsBadge').text(configData.forms.length);
    }

    function clearFilters() {
        $('#searchInput').val('');
        $('#statusFilter').val('');
        $('#sortBy').val('name');
        applyFilters();
    }

    function refreshData() {
        const refreshBtn = $('button[onclick="refreshData()"]');
        const originalText = refreshBtn.html();
        refreshBtn.html('<i class="fas fa-spinner fa-spin"></i> Refreshing...').prop('disabled', true);

        fetch('<?= Yii::$app->urlManager->createUrl(["inventory/get-configurations"]) ?>', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Object.keys(configData).forEach(key => {
                        configData[key] = data.data[key] || [];
                    });

                    updateSummaryCards();
                    applyFilters();
                    showNotification('Data refreshed successfully', 'success');
                } else {
                    showNotification(data.message || 'Error refreshing data', 'error');
                }
            })
            .catch(error => {
                console.error('Error refreshing data:', error);
                showNotification('Error refreshing data. Please try again.', 'error');
            })
            .finally(() => {
                refreshBtn.html(originalText).prop('disabled', false);
            });
    }

    function exportData() {
        let csvContent = "Type,Name,Description,Status\n";

        Object.keys(configData).forEach(type => {
            const config = configDefs[type];
            configData[type].forEach(item => {
                csvContent +=
                    `${config.title},"${item.name}","${item.description || ''}","${item.status || 'active'}"\n`;
            });
        });

        const blob = new Blob([csvContent], {
            type: 'text/csv'
        });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'configurations_export.csv';
        a.click();
        window.URL.revokeObjectURL(url);

        showNotification('Configurations exported successfully', 'success');
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
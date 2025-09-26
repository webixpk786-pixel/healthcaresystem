<?php
// Fix the categories data structure
$categories = isset($categories) ? $categories : [];

use app\components\SystemComponents;

$systemComponents = new SystemComponents();
$can = $systemComponents->checkModulePermission('25,30');
$canEdit = $can['canEdit'];
$canExport = $can['canExport'];
$canDelete = $can['canDelete'];
$canAdd = $can['canAdd'];
?>

<div class="medicines-catalog" style="min-height: 95vh;margin: -25px;">



    <!-- Header Section -->
    <div class="page-header" style="margin-bottom: 24px;">
        <div class="row">
            <div class="col-md-7">
                <h1 style="color: #1e293b; font-size: 1.8rem; font-weight: 700; margin-bottom: 8px;">
                    <i class="fas fa-boxes" style="color: #6366f1; margin-right: 12px;"></i>
                    Medicines Catalog
                </h1>
                <p style="color: #64748b; font-size: 0.95rem; margin: 0;">
                    Manage your complete medicines inventory with detailed information
                </p>
            </div>
            <div class="col-md-5 text-right">
                <div class="header-actions" style="display: flex; gap: 8px; justify-content: flex-end;">
                    <button class="btn btn-sm btn-outline-secondary" onclick="toggleColumns()"
                        style="display: flex;align-items: center;gap: 8px; border: 1px solid #e2e8f0; padding: 6px 12px; border-radius: 6px; font-size: 0.8rem; background: white; color: #64748b; margin-right: 8px;">
                        <i class="fas fa-columns"></i> Columns
                    </button>
                    <?php if ($canExport): ?>
                        <button class="btn btn-sm btn-outline-secondary" onclick="exportMedicines()"
                            style="border: 1px solid #e2e8f0; padding: 6px 12px; border-radius: 6px; font-size: 0.8rem; background: white; color: #64748b;">
                            <i class="fas fa-download"></i> Export
                        </button>
                    <?php endif; ?>
                    <?php if ($canAdd): ?>
                        <button class="btn btn-primary" onclick="addMedicine()"
                            style="background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); border: none; padding: 8px 16px; border-radius: 8px; font-size: 0.85rem; font-weight: 500; box-shadow: 0 2px 8px rgba(99, 102, 241, 0.25);">
                            <i class="fas fa-plus"></i> Add New
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Medicines Summary Cards -->
    <div class="medicines-summary" style="margin-bottom: 20px;">
        <div class="row g-2">
            <!-- Card 1 - Total Medicines -->
            <div class="col-lg-3 col-md-6">
                <div class="summary-card h-100"
                    style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 12px; padding: 12px; box-shadow: 0 4px 16px rgba(102, 126, 234, 0.25); border: none; position: relative; overflow: hidden;">
                    <div style="display: flex; align-items: center; font-size: 1.4rem; font-weight: 700; color: white;">
                        <i class="fas fa-pills" style="font-size: 1.2rem; opacity: 0.9; margin-right: 8px;"></i>
                        <span id="totalMedicines"><?= count($medicines) ?></span>
                        <small style="font-size: 0.7rem; font-weight: 500; margin: 0 8px;">Medicines</small>
                        <div
                            style="margin-left: auto; background: rgba(255,255,255,0.2); padding: 2px 6px; border-radius: 8px; backdrop-filter: blur(10px); font-size: 0.6rem; font-weight: 600;">
                            TOTAL
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 2 - Active Medicines -->
            <div class="col-lg-3 col-md-6">
                <div class="summary-card h-100"
                    style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 12px; padding: 12px; box-shadow: 0 4px 16px rgba(16, 185, 129, 0.25); border: none; position: relative; overflow: hidden;">
                    <div style="display: flex; align-items: center; font-size: 1.4rem; font-weight: 700; color: white;">
                        <i class="fas fa-check-circle" style="font-size: 1.2rem; opacity: 0.9; margin-right: 8px;"></i>
                        <span id="activeMedicines"><?= count(array_filter($medicines, function ($m) {
                                                        return $m['status'] === 'active';
                                                    })) ?></span>
                        <small style="font-size: 0.7rem; font-weight: 500; margin: 0 8px;">Active</small>
                        <div
                            style="margin-left: auto; background: rgba(255,255,255,0.2); padding: 2px 6px; border-radius: 8px; backdrop-filter: blur(10px); font-size: 0.6rem; font-weight: 600;">
                            ACTIVE
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 3 - Categories -->
            <div class="col-lg-3 col-md-6">
                <div class="summary-card h-100"
                    style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); border-radius: 12px; padding: 12px; box-shadow: 0 4px 16px rgba(245, 158, 11, 0.25); border: none; position: relative; overflow: hidden;">
                    <div style="display: flex; align-items: center; font-size: 1.4rem; font-weight: 700; color: white;">
                        <i class="fas fa-tags" style="font-size: 1.2rem; opacity: 0.9; margin-right: 8px;"></i>
                        <span id="uniqueCategories"><?= count($categories) ?></span>
                        <small style="font-size: 0.7rem; font-weight: 500; margin: 0 8px;">Categories</small>
                        <div
                            style="margin-left: auto; background: rgba(255,255,255,0.2); padding: 2px 6px; border-radius: 8px; backdrop-filter: blur(10px); font-size: 0.6rem; font-weight: 600;">
                            CATEGORIES
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 4 - Manufacturers -->
            <div class="col-lg-3 col-md-6">
                <div class="summary-card h-100"
                    style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); border-radius: 12px; padding: 12px; box-shadow: 0 4px 16px rgba(139, 92, 246, 0.25); border: none; position: relative; overflow: hidden;">
                    <div style="display: flex; align-items: center; font-size: 1.4rem; font-weight: 700; color: white;">
                        <i class="fas fa-industry" style="font-size: 1.2rem; opacity: 0.9; margin-right: 8px;"></i>
                        <span
                            id="uniqueManufacturers"><?= count(array_unique(array_column($medicines, 'manufacturer'))) ?></span>
                        <small style="font-size: 0.7rem; font-weight: 500; margin: 0 8px;">Manufacturers</small>
                        <div
                            style="margin-left: auto; background: rgba(255,255,255,0.2); padding: 2px 6px; border-radius: 8px; backdrop-filter: blur(10px); font-size: 0.6rem; font-weight: 600;">
                            BRANDS
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
                        <label for="filterCategory"
                            style="font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 0.75rem;">Category</label>
                        <select class="form-control" id="filterCategory"
                            style="border: 1px solid #d1d5db; border-radius: 4px; padding: 6px 8px; font-size: 0.75rem; height: 32px;">
                            <option value="">All Categories</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= htmlspecialchars($category['id']) ?>">
                                    <?= htmlspecialchars($category['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group mb-0">
                        <label for="filterStatus"
                            style="font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 0.75rem;">Status</label>
                        <select class="form-control" id="filterStatus"
                            style="border: 1px solid #d1d5db; border-radius: 4px; padding: 6px 8px; font-size: 0.75rem; height: 32px;">
                            <option value="">All Status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group mb-0">
                        <label for="filterForm"
                            style="font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 0.75rem;">Form</label>
                        <select class="form-control" id="filterForm"
                            style="border: 1px solid #d1d5db; border-radius: 4px; padding: 6px 8px; font-size: 0.75rem; height: 32px;">
                            <option value="">All Forms</option>
                            <?php foreach (array_unique(array_column($medicines, 'form')) as $form): ?>
                                <option value="<?= htmlspecialchars($form) ?>"><?= htmlspecialchars($form) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group mb-0">
                        <label for="filterManufacturer"
                            style="font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 0.75rem;">Manufacturer</label>
                        <select class="form-control" id="filterManufacturer"
                            style="border: 1px solid #d1d5db; border-radius: 4px; padding: 6px 8px; font-size: 0.75rem; height: 32px;">
                            <option value="">All Manufacturers</option>
                            <?php foreach (array_unique(array_column($medicines, 'manufacturer')) as $manufacturer): ?>
                                <option value="<?= htmlspecialchars($manufacturer) ?>">
                                    <?= htmlspecialchars($manufacturer) ?></option>
                            <?php endforeach; ?>
                        </select>
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
                <table class="table" id="medicinesTable" style="width: 100%; border-collapse: collapse; margin: 0;">
                    <thead style="position: sticky; top: 0; z-index: 10; background: #f8fafc;">
                        <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                            <th
                                style="padding: 12px; text-align: left; font-weight: 600; color: #1e293b; font-size: 0.9rem;">
                                Medicine Name</th>
                            <th
                                style="padding: 12px; text-align: left; font-weight: 600; color: #1e293b; font-size: 0.9rem;">
                                Generic Name</th>
                            <th
                                style="padding: 12px; text-align: left; font-weight: 600; color: #1e293b; font-size: 0.9rem;">
                                Category</th>
                            <th
                                style="padding: 12px; text-align: left; font-weight: 600; color: #1e293b; font-size: 0.9rem;">
                                Manufacturer</th>
                            <th
                                style="padding: 12px; text-align: left; font-weight: 600; color: #1e293b; font-size: 0.9rem;">
                                Strength/Form</th>
                            <th
                                style="padding: 12px; text-align: left; font-weight: 600; color: #1e293b; font-size: 0.9rem;">
                                Unit</th>
                            <th
                                style="padding: 12px; text-align: left; font-weight: 600; color: #1e293b; font-size: 0.9rem;">
                                Pack</th>
                            <th
                                style="padding: 12px; text-align: left; font-weight: 600; color: #1e293b; font-size: 0.9rem;">
                                Status</th>
                            <?php if ($canEdit || $canDelete): ?>
                                <th
                                    style="padding: 12px; text-align: center; font-weight: 600; color: #1e293b; font-size: 0.9rem;">
                                    Actions</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody id="medicinesTableBody">
                        <?php foreach ($medicines as $medicine): ?>
                            <tr class="medicine-row" data-category="<?= htmlspecialchars($medicine['category']) ?>"
                                data-status="<?= htmlspecialchars($medicine['status']) ?>"
                                data-form="<?= htmlspecialchars($medicine['form']) ?>" data-id="<?= $medicine['id'] ?>">
                                <td style="padding: 12px; font-weight: 500; color: #1e293b;">
                                    <div style="display: flex; align-items: center;">
                                        <div
                                            style="width: 8px; height: 8px; background: #10b981; border-radius: 50%; margin-right: 8px;">
                                        </div>
                                        <?= htmlspecialchars($medicine['name']) ?>
                                    </div>
                                </td>
                                <td style="padding: 12px; color: #64748b; font-size: 0.9rem;">
                                    <?= htmlspecialchars($medicine['generic_name']) ?>
                                </td>
                                <td style="padding: 12px;">
                                    <span
                                        style="background: #f0f9ff; color: #0369a1; padding: 4px 8px; border-radius: 12px; font-size: 0.8rem; font-weight: 500;">
                                        <?= htmlspecialchars($medicine['category']) ?>
                                    </span>
                                </td>
                                <td style="padding: 12px; color: #64748b; font-size: 0.9rem;">
                                    <?= htmlspecialchars($medicine['manufacturer']) ?>
                                </td>
                                <td style="padding: 12px;">
                                    <div style="font-weight: 500; color: #1e293b;">
                                        <?= htmlspecialchars($medicine['strength']) ?>
                                    </div>
                                    <div style="color: #64748b; font-size: 0.8rem;">
                                        <?= htmlspecialchars($medicine['form']) ?>
                                    </div>
                                </td>
                                <td style="padding: 12px; font-weight: 600; color: #1e293b;">
                                    <?= number_format($medicine['unit_price'], 2) ?>
                                </td>
                                <td style="padding: 12px; font-weight: 600; color: #1e293b;">
                                    <?= number_format($medicine['pack_price'], 2) ?>
                                </td>
                                <td style="padding: 12px;">
                                    <span
                                        style="background: <?= $medicine['status'] === 'active' ? '#f0fdf4' : '#fef2f2' ?>; color: <?= $medicine['status'] === 'active' ? '#16a34a' : '#dc2626' ?>; padding: 4px 8px; border-radius: 12px; font-size: 0.8rem; font-weight: 500;">
                                        <?= ucfirst($medicine['status']) ?>
                                    </span>
                                </td>
                                <?php if ($canEdit || $canDelete): ?>
                                    <td style="padding: 12px; text-align: center;">
                                        <div style="display: flex; gap: 4px; justify-content: center;">
                                            <?php if ($canEdit): ?>
                                                <button class="btn btn-sm btn-outline-primary"
                                                    onclick="editMedicine(<?= $medicine['id'] ?>)"
                                                    style="border: 1px solid #3b82f6; padding: 4px 8px; border-radius: 4px; font-size: 0.75rem; background: white; color: #3b82f6;">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                            <?php endif; ?>

                                            <button class="btn btn-sm btn-outline-info"
                                                onclick="viewMedicine(<?= $medicine['id'] ?>)"
                                                style="border: 1px solid #06b6d4; padding: 4px 8px; border-radius: 4px; font-size: 0.75rem; background: white; color: #06b6d4;">
                                                <i class="fas fa-eye"></i>
                                            </button>

                                            <?php if ($canDelete): ?>
                                                <button class="btn btn-sm btn-outline-danger"
                                                    onclick="deleteMedicine(<?= $medicine['id'] ?>)"
                                                    style="border: 1px solid #ef4444; padding: 4px 8px; border-radius: 4px; font-size: 0.75rem; background: white; color: #ef4444;">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
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
                            Showing 1 to <?= min(25, count($medicines)) ?> of <?= count($medicines) ?> entries
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

<!-- Medicine Details Modal -->
<div class="modal fade" id="medicineModal" tabindex="-1" role="dialog" aria-labelledby="medicineModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 20px 40px rgba(0,0,0,0.15);">
            <div class="modal-header"
                style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 12px 12px 0 0; border-bottom: none; padding: 12px 16px;">
                <h5 class="modal-title" id="medicineModalLabel" style="font-size: 1rem; font-weight: 600; margin: 0;">
                    <i class="fas fa-pills me-2"></i>Medicine Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"
                    style="opacity: 0.8; font-size: 1rem;"></button>
            </div>
            <div class="modal-body" id="medicineModalBody" style="padding: 16px; max-height: 60vh; overflow-y: auto;">
                <!-- Medicine details will be loaded here -->
            </div>
            <div class="modal-footer"
                style="border-top: 1px solid #e2e8f0; padding: 12px 16px; background: #f8fafc; border-radius: 0 0 12px 12px;">
                <div style="display: flex; gap: 6px; margin-left: auto;">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal"
                        style="padding: 6px 12px; border-radius: 6px; font-weight: 500; font-size: 0.8rem;">
                        Close
                    </button>
                    <button type="button" class="btn btn-primary" onclick="editMedicineFromModal()"
                        style="padding: 6px 12px; border-radius: 6px; font-weight: 500; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; font-size: 0.8rem;">
                        Update
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Medicine Modal -->
<div class="modal fade" id="addEditMedicineModal" tabindex="-1" role="dialog"
    aria-labelledby="addEditMedicineModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 20px 40px rgba(0,0,0,0.15);">
            <div class="modal-header"
                style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 12px 12px 0 0; border-bottom: none; padding: 10px 14px;">
                <h5 class="modal-title" id="addEditMedicineModalLabel"
                    style="font-size: 0.95rem; font-weight: 600; margin: 0;">
                    <i class="fas fa-plus me-2"></i>Add New Medicine
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"
                    style="opacity: 0.8; font-size: 0.9rem;"></button>
            </div>
            <div class="modal-body" style="padding: 12px; background: #fafbfc; max-height: 70vh; overflow-y: auto;">
                <form id="medicineForm">
                    <input type="hidden" id="medicineId" name="id">

                    <!-- Basic Information Section -->
                    <div class="form-section"
                        style="background: white; border-radius: 6px; padding: 8px; margin-bottom: 8px; border: 1px solid #e2e8f0;">
                        <h6
                            style="color: #374151; font-weight: 600; margin-bottom: 8px; display: flex; align-items: center; font-size: 0.75rem;">
                            <i class="fas fa-info-circle me-2" style="color: #6366f1; font-size: 0.7rem;"></i>Basic
                            Information
                        </h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-1">
                                    <label for="medicineName"
                                        style="font-weight: 500; color: #374151; margin-bottom: 2px; font-size: 0.7rem;">Medicine
                                        Name *</label>
                                    <input type="text" class="form-control" id="medicineName" name="name" required
                                        style="border: 1px solid #d1d5db; border-radius: 4px; padding: 4px 6px; font-size: 0.7rem; transition: all 0.2s; height: 28px;">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-1">
                                    <label for="genericName"
                                        style="font-weight: 500; color: #374151; margin-bottom: 2px; font-size: 0.7rem;">Generic
                                        Name *</label>
                                    <input type="text" class="form-control" id="genericName" name="generic_name"
                                        required
                                        style="border: 1px solid #d1d5db; border-radius: 4px; padding: 4px 6px; font-size: 0.7rem; transition: all 0.2s; height: 28px;">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-1">
                                    <label for="category"
                                        style="font-weight: 500; color: #374151; margin-bottom: 2px; font-size: 0.7rem;">Category
                                        *</label>
                                    <select class="form-control" id="category" name="category" required
                                        style="border: 1px solid #d1d5db; border-radius: 4px; padding: 4px 6px; font-size: 0.7rem; transition: all 0.2s; height: 28px;">
                                        <option value="">Select Category</option>
                                        <?php foreach ($categories as $category): ?>
                                            <option value="<?= htmlspecialchars($category['id']) ?>">
                                                <?= htmlspecialchars($category['name']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-1">
                                    <label for="manufacturer"
                                        style="font-weight: 500; color: #374151; margin-bottom: 2px; font-size: 0.7rem;">Manufacturer
                                        *</label>
                                    <input type="text" class="form-control" id="manufacturer" name="manufacturer"
                                        required
                                        style="border: 1px solid #d1d5db; border-radius: 4px; padding: 4px 6px; font-size: 0.7rem; transition: all 0.2s; height: 28px;">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Medicine Details Section -->
                    <div class="form-section"
                        style="background: white; border-radius: 6px; padding: 8px; margin-bottom: 8px; border: 1px solid #e2e8f0;">
                        <h6
                            style="color: #374151; font-weight: 600; margin-bottom: 8px; display: flex; align-items: center; font-size: 0.75rem;">
                            <i class="fas fa-capsules me-2" style="color: #6366f1; font-size: 0.7rem;"></i>Medicine
                            Details
                        </h6>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group mb-1">
                                    <label for="strength"
                                        style="font-weight: 500; color: #374151; margin-bottom: 2px; font-size: 0.7rem;">Strength
                                        *</label>
                                    <input type="text" class="form-control" id="strength" name="strength" required
                                        style="border: 1px solid #d1d5db; border-radius: 4px; padding: 4px 6px; font-size: 0.7rem; transition: all 0.2s; height: 28px;">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-1">
                                    <label for="form"
                                        style="font-weight: 500; color: #374151; margin-bottom: 2px; font-size: 0.7rem;">Form
                                        *</label>
                                    <select class="form-control" id="form" name="form" required
                                        style="border: 1px solid #d1d5db; border-radius: 4px; padding: 4px 6px; font-size: 0.7rem; transition: all 0.2s; height: 28px;">
                                        <option value="">Select Form</option>
                                        <option value="Tablet">Tablet</option>
                                        <option value="Capsule">Capsule</option>
                                        <option value="Syrup">Syrup</option>
                                        <option value="Injection">Injection</option>
                                        <option value="Cream">Cream</option>
                                        <option value="Ointment">Ointment</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-1">
                                    <label for="unitPrice"
                                        style="font-weight: 500; color: #374151; margin-bottom: 2px; font-size: 0.7rem;">Unit
                                        Price *</label>
                                    <input type="number" class="form-control" id="unitPrice" name="unit_price"
                                        step="0.01" required
                                        style="border: 1px solid #d1d5db; border-radius: 4px; padding: 4px 6px; font-size: 0.7rem; transition: all 0.2s; height: 28px;">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-1">
                                    <label for="packPrice"
                                        style="font-weight: 500; color: #374151; margin-bottom: 2px; font-size: 0.7rem;">Pack
                                        Price *</label>
                                    <input type="number" class="form-control" id="packPrice" name="pack_price"
                                        step="0.01" required
                                        style="border: 1px solid #d1d5db; border-radius: 4px; padding: 4px 6px; font-size: 0.7rem; transition: all 0.2s; height: 28px;">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-1">
                                    <label for="status"
                                        style="font-weight: 500; color: #374151; margin-bottom: 2px; font-size: 0.7rem;">Status
                                        *</label>
                                    <select class="form-control" id="status" name="status" required
                                        style="border: 1px solid #d1d5db; border-radius: 4px; padding: 4px 6px; font-size: 0.7rem; transition: all 0.2s; height: 28px;">
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="description"
                                style="font-weight: 500; color: #374151; margin-bottom: 2px; font-size: 0.7rem;">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="2"
                                style="border: 1px solid #d1d5db; border-radius: 4px; padding: 4px 6px; font-size: 0.7rem; transition: all 0.2s; resize: vertical;"></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer"
                style="border-top: 1px solid #e2e8f0; padding: 10px 14px; background: #f8fafc; border-radius: 0 0 12px 12px;">
                <div style="display: flex; gap: 6px; margin-left: auto;">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal"
                        style="padding: 5px 10px; border-radius: 4px; font-weight: 500; font-size: 0.7rem;">
                        Cancel
                    </button>
                    <button type="button" class="btn btn-primary" onclick="saveMedicine()"
                        style="padding: 5px 10px; border-radius: 4px; font-weight: 500; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; font-size: 0.7rem;">
                        Save
                    </button>
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
                    <i class="fas fa-exclamation-triangle me-2"></i>Confirm Delete
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"
                    style="opacity: 0.8; font-size: 0.9rem;"></button>
            </div>
            <div class="modal-body" style="padding: 12px; text-align: center;">
                <div style="color: #ef4444; font-size: 1.5rem; margin-bottom: 8px;">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h6 style="color: #374151; font-weight: 600; margin-bottom: 4px; font-size: 0.8rem;">Are you sure you
                    want to delete this medicine?</h6>
                <p style="color: #6b7280; margin-bottom: 12px; font-size: 0.7rem;">This action cannot be undone and will
                    permanently remove the medicine from your inventory.</p>
                <div id="deleteMedicineInfo"></div>
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
                        Delete
                    </button>
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
                    display in the medicines table:</p>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-check mb-1">
                            <input class="form-check-input" type="checkbox" id="colName" checked
                                style="transform: scale(0.8);">
                            <label class="form-check-label" for="colName"
                                style="font-weight: 500; font-size: 0.7rem;">Medicine Name</label>
                        </div>
                        <div class="form-check mb-1">
                            <input class="form-check-input" type="checkbox" id="colGeneric" checked
                                style="transform: scale(0.8);">
                            <label class="form-check-label" for="colGeneric"
                                style="font-weight: 500; font-size: 0.7rem;">Generic Name</label>
                        </div>
                        <div class="form-check mb-1">
                            <input class="form-check-input" type="checkbox" id="colCategory" checked
                                style="transform: scale(0.8);">
                            <label class="form-check-label" for="colCategory"
                                style="font-weight: 500; font-size: 0.7rem;">Category</label>
                        </div>
                        <div class="form-check mb-1">
                            <input class="form-check-input" type="checkbox" id="colManufacturer" checked
                                style="transform: scale(0.8);">
                            <label class="form-check-label" for="colManufacturer"
                                style="font-weight: 500; font-size: 0.7rem;">Manufacturer</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check mb-1">
                            <input class="form-check-input" type="checkbox" id="colStrength" checked
                                style="transform: scale(0.8);">
                            <label class="form-check-label" for="colStrength"
                                style="font-weight: 500; font-size: 0.7rem;">Strength/Form</label>
                        </div>
                        <div class="form-check mb-1">
                            <input class="form-check-input" type="checkbox" id="colUnitPrice" checked
                                style="transform: scale(0.8);">
                            <label class="form-check-label" for="colUnitPrice"
                                style="font-weight: 500; font-size: 0.7rem;">Unit Price</label>
                        </div>
                        <div class="form-check mb-1">
                            <input class="form-check-input" type="checkbox" id="colPackPrice" checked
                                style="transform: scale(0.8);">
                            <label class="form-check-label" for="colPackPrice"
                                style="font-weight: 500; font-size: 0.7rem;">Pack Price</label>
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
                        style="padding: 5px 10px; border-radius: 4px; font-weight: 500; font-size: 0.7rem;">
                        Close
                    </button>
                    <button type="button" class="btn btn-primary" onclick="applyColumnVisibility()"
                        style="padding: 5px 10px; border-radius: 4px; font-weight: 500; background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); border: none; font-size: 0.7rem;">
                        Apply
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .medicines-catalog .summary-card {
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .medicines-catalog .summary-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .medicines-catalog .table tbody tr:hover {
        background: #f8fafc;
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        transition: all 0.2s ease;
    }

    .medicines-catalog .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        transition: all 0.2s ease;
    }

    /* Fixed table header styles */
    .medicines-catalog .table thead {
        position: sticky;
        top: 0;
        z-index: 10;
        background: #f8fafc;
    }

    .medicines-catalog .table thead th {
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

    /* Medicine row styling */
    .medicine-row {
        border-bottom: 1px solid #e2e8f0;
    }

    .medicine-row:last-child {
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
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        outline: none;
    }

    .form-check-input:checked {
        background-color: #6366f1;
        border-color: #6366f1;
    }

    .form-check-input:focus {
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    }

    .btn {
        transition: all 0.2s ease;
    }

    .btn:active {
        transform: translateY(0);
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .medicines-catalog .medicines-summary .col-md-3 {
            margin-bottom: 16px;
        }

        .medicines-catalog .filters-section .row>div {
            margin-bottom: 12px;
        }

        .medicines-catalog .table-responsive {
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

        .medicines-catalog .table thead th,
        .medicines-catalog .table tbody td {
            padding: 8px !important;
            font-size: 0.8rem !important;
        }
    }
</style>

<script>
    let currentMedicineId = null;
    let medicinesData = <?= json_encode($medicines) ?>;
    let filteredMedicinesData = [...medicinesData];
    let currentPage = 1;
    let itemsPerPage = 25;
    let totalPages = 1;

    $(document).ready(function() {
        // Initialize column visibility
        initializeColumnVisibility();

        // Add event listeners for modals
        $('#addEditMedicineModal').on('hidden.bs.modal', function() {
            $('#medicineForm')[0].reset();
            currentMedicineId = null;
        });
        $('#medicineModal').on('hidden.bs.modal', function() {
            currentMedicineId = null;
        });
        $('#deleteModal').on('hidden.bs.modal', function() {
            currentMedicineId = null;
        });
        $('#columnModal').on('hidden.bs.modal', function() {
            // No specific reset needed for column modal
        });

        // Add event listeners for filter inputs
        $('#filterMedicine, #filterCategory, #filterStatus, #filterForm, #filterManufacturer').on('input change',
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
    });

    function updateStats() {
        let activeCount = 0;
        const uniqueCategories = new Set();
        const uniqueManufacturers = new Set();

        medicinesData.forEach(medicine => {
            // Count active medicines
            if (medicine.status === 'active') {
                activeCount++;
            }

            // Collect unique categories
            uniqueCategories.add(medicine.category);

            // Collect unique manufacturers
            uniqueManufacturers.add(medicine.manufacturer);
        });

        // Update stats cards
        $('#totalMedicines').text(medicinesData.length);
        $('#activeMedicines').text(activeCount);
        $('#uniqueCategories').text(uniqueCategories.size);
        $('#uniqueManufacturers').text(uniqueManufacturers.size);
    }

    function applyFilters() {
        const medicineFilter = $('#filterMedicine').val().toLowerCase();
        const categoryFilter = $('#filterCategory').val();
        const statusFilter = $('#filterStatus').val();
        const formFilter = $('#filterForm').val();
        const manufacturerFilter = $('#filterManufacturer').val();

        filteredMedicinesData = medicinesData.filter(medicine => {
            // Medicine filter
            if (medicineFilter && !medicine.name.toLowerCase().includes(medicineFilter) &&
                !medicine.generic_name.toLowerCase().includes(medicineFilter)) {
                return false;
            }

            // Category filter
            if (categoryFilter && medicine.category !== categoryFilter) {
                return false;
            }

            // Status filter
            if (statusFilter && medicine.status !== statusFilter) {
                return false;
            }

            // Form filter
            if (formFilter && medicine.form !== formFilter) {
                return false;
            }

            // Manufacturer filter
            if (manufacturerFilter && medicine.manufacturer !== manufacturerFilter) {
                return false;
            }

            return true;
        });

        currentPage = 1;
        refreshTable();
        showNotification(`Showing ${filteredMedicinesData.length} of ${medicinesData.length} medicines`, 'info');
    }

    function clearFilters() {
        $('#filterMedicine').val('');
        $('#filterCategory').val('');
        $('#filterStatus').val('');
        $('#filterForm').val('');
        $('#filterManufacturer').val('');

        filteredMedicinesData = [...medicinesData];
        currentPage = 1;
        refreshTable();
        showNotification('Filters cleared', 'info');
    }

    function refreshTable() {
        const tbody = $('#medicinesTableBody');
        tbody.empty();

        // Calculate pagination
        totalPages = Math.ceil(filteredMedicinesData.length / itemsPerPage);
        const startIndex = (currentPage - 1) * itemsPerPage;
        const endIndex = Math.min(startIndex + itemsPerPage, filteredMedicinesData.length);
        const pageData = filteredMedicinesData.slice(startIndex, endIndex);

        if (pageData.length === 0) {
            tbody.append(`
                <tr>
                    <td colspan="8" style="text-align: center; padding: 40px; color: #6b7280;">
                        <i class="fas fa-search" style="font-size: 2rem; margin-bottom: 12px; display: block;"></i>
                        No medicines found matching your criteria
                    </td>
                </tr>
            `);
        } else {
            pageData.forEach(medicine => {
                const row = `
                    <tr class="medicine-row" data-category="${medicine.category}"
                        data-status="${medicine.status}"
                        data-form="${medicine.form}" data-id="${medicine.id}">
                        <td style="padding: 12px; font-weight: 500; color: #1e293b;">
                            <div style="display: flex; align-items: center;">
                                <div style="width: 8px; height: 8px; background: #10b981; border-radius: 50%; margin-right: 8px;"></div>
                                ${medicine.name}
                            </div>
                        </td>
                        <td style="padding: 12px; color: #64748b; font-size: 0.9rem;">
                            ${medicine.generic_name}
                        </td>
                        <td style="padding: 12px;">
                            <span style="background: #f0f9ff; color: #0369a1; padding: 4px 8px; border-radius: 12px; font-size: 0.8rem; font-weight: 500;">
                                ${medicine.category}
                            </span>
                        </td>
                        <td style="padding: 12px; color: #64748b; font-size: 0.9rem;">
                            ${medicine.manufacturer}
                        </td>
                        <td style="padding: 12px;">
                            <div style="font-weight: 500; color: #1e293b;">
                                ${medicine.strength}
                            </div>
                            <div style="color: #64748b; font-size: 0.8rem;">
                                ${medicine.form}
                            </div>
                        </td>
                        <td style="padding: 12px; font-weight: 600; color: #1e293b;">
                            ${parseFloat(medicine.unit_price).toFixed(2)}
                        </td>
                        <td style="padding: 12px; font-weight: 600; color: #1e293b;">
                            ${parseFloat(medicine.pack_price).toFixed(2)}
                        </td>
                        <td style="padding: 12px;">
                            <span style="background: ${medicine.status === 'active' ? '#f0fdf4' : '#fef2f2'}; color: ${medicine.status === 'active' ? '#16a34a' : '#dc2626'}; padding: 4px 8px; border-radius: 12px; font-size: 0.8rem; font-weight: 500;">
                                ${medicine.status.charAt(0).toUpperCase() + medicine.status.slice(1)}
                            </span>
                        </td>
                        <?php if ($canEdit || $canDelete): ?>
                        <td style="padding: 12px; text-align: center;">
                            <div style="display: flex; gap: 4px; justify-content: center;">
                                <?php if ($canEdit): ?>
                            <button class="btn btn-sm btn-outline-primary" onclick="editMedicine(${medicine.id})"
                                    style="border: 1px solid #3b82f6; padding: 4px 8px; border-radius: 4px; font-size: 0.75rem; background: white; color: #3b82f6;">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <?php endif; ?>
                                
                                <button class="btn btn-sm btn-outline-info" onclick="viewMedicine(${medicine.id})"
                                    style="border: 1px solid #06b6d4; padding: 4px 8px; border-radius: 4px; font-size: 0.75rem; background: white; color: #06b6d4;">
                                    <i class="fas fa-eye"></i>
                                </button>
                                
                                <?php if ($canDelete): ?>
                                <button class="btn btn-sm btn-outline-danger" onclick="deleteMedicine(${medicine.id})"
                                    style="border: 1px solid #ef4444; padding: 4px 8px; border-radius: 4px; font-size: 0.75rem; background: white; color: #ef4444;">
                                    <i class="fas fa-trash"></i>
                                </button>
                                <?php endif; ?>
                            </div>
                        </td>
                        <?php endif; ?>
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
        const endIndex = Math.min(currentPage * itemsPerPage, filteredMedicinesData.length);
        paginationInfo.text(`Showing ${startIndex} to ${endIndex} of ${filteredMedicinesData.length} entries`);

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

    function addMedicine() {
        currentMedicineId = null;
        $('#addEditMedicineModalLabel').html('<i class="fas fa-plus me-2"></i>Add New Medicine');
        $('#medicineForm')[0].reset();
        $('#medicineId').val('');

        // Use Bootstrap 5 modal API
        const modal = new bootstrap.Modal(document.getElementById('addEditMedicineModal'));
        modal.show();
    }

    function editMedicine(id) {
        currentMedicineId = id;
        const medicine = medicinesData.find(m => m.id == id);

        if (medicine) {
            $('#addEditMedicineModalLabel').html('<i class="fas fa-edit me-2"></i>Edit Medicine');
            $('#medicineId').val(medicine.id);
            $('#medicineName').val(medicine.name);
            $('#genericName').val(medicine.generic_name);
            $('#category').val(medicine.category_id);
            $('#manufacturer').val(medicine.manufacturer);
            $('#strength').val(medicine.strength);
            $('#form').val(medicine.form);
            $('#unitPrice').val(medicine.unit_price);
            $('#packPrice').val(medicine.pack_price);
            $('#barcode').val(medicine.barcode || '');
            $('#status').val(medicine.status);
            $('#description').val(medicine.description || '');

            // Use Bootstrap 5 modal API
            const modal = new bootstrap.Modal(document.getElementById('addEditMedicineModal'));
            modal.show();
        }
    }

    function viewMedicine(id) {
        fetch(`<?= Yii::$app->urlManager->createUrl(['inventory/get-medicine-details']) ?>&id=${id}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const medicine = data.data;
                    const html = `
                        <div class="row">
                            <div class="col-md-6">
                                <div style="background: #f8fafc; border-radius: 12px; padding: 20px; margin-bottom: 20px;">
                                    <h6 style="color: #374151; font-weight: 600; margin-bottom: 16px; display: flex; align-items: center;">
                                        <i class="fas fa-info-circle me-2" style="color: #6366f1;"></i>Basic Information
                                    </h6>
                                    <table class="table table-sm" style="margin: 0;">
                                        <tr><td style="border: none; padding: 8px 0; font-weight: 600; color: #374151;">Name:</td><td style="border: none; padding: 8px 0; color: #6b7280;">${medicine.name}</td></tr>
                                        <tr><td style="border: none; padding: 8px 0; font-weight: 600; color: #374151;">Generic Name:</td><td style="border: none; padding: 8px 0; color: #6b7280;">${medicine.generic_name}</td></tr>
                                        <tr><td style="border: none; padding: 8px 0; font-weight: 600; color: #374151;">Category:</td><td style="border: none; padding: 8px 0;"><span class="badge bg-primary">${medicine.category}</span></td></tr>
                                        <tr><td style="border: none; padding: 8px 0; font-weight: 600; color: #374151;">Manufacturer:</td><td style="border: none; padding: 8px 0; color: #6b7280;">${medicine.manufacturer}</td></tr>
                                    </table>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div style="background: #f8fafc; border-radius: 12px; padding: 20px; margin-bottom: 20px;">
                                    <h6 style="color: #374151; font-weight: 600; margin-bottom: 16px; display: flex; align-items: center;">
                                        <i class="fas fa-capsules me-2" style="color: #6366f1;"></i>Medicine Details
                                    </h6>
                                    <table class="table table-sm" style="margin: 0;">
                                        <tr><td style="border: none; padding: 8px 0; font-weight: 600; color: #374151;">Strength:</td><td style="border: none; padding: 8px 0; color: #6b7280;">${medicine.strength}</td></tr>
                                        <tr><td style="border: none; padding: 8px 0; font-weight: 600; color: #374151;">Form:</td><td style="border: none; padding: 8px 0; color: #6b7280;">${medicine.form}</td></tr>
                                        <tr><td style="border: none; padding: 8px 0; font-weight: 600; color: #374151;">Unit Price:</td><td style="border: none; padding: 8px 0; color: #6b7280;">${parseFloat(medicine.unit_price).toFixed(2)}</td></tr>
                                        <tr><td style="border: none; padding: 8px 0; font-weight: 600; color: #374151;">Pack Price:</td><td style="border: none; padding: 8px 0; color: #6b7280;">${parseFloat(medicine.pack_price).toFixed(2)}</td></tr>
                                        <tr><td style="border: none; padding: 8px 0; font-weight: 600; color: #374151;">Status:</td><td style="border: none; padding: 8px 0;"><span class="badge bg-success">${medicine.status.charAt(0).toUpperCase() + medicine.status.slice(1)}</span></td></tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        ${medicine.description ? `<div class="mt-3"><div style="background: #f8fafc; border-radius: 12px; padding: 20px;"><h6 style="color: #374151; font-weight: 600; margin-bottom: 12px; display: flex; align-items: center;"><i class="fas fa-file-text me-2" style="color: #6366f1;"></i>Description</h6><p style="color: #6b7280; margin: 0;">${medicine.description}</p></div></div>` : ''}
                        ${medicine.barcode ? `<div class="mt-3"><div style="background: #f8fafc; border-radius: 12px; padding: 20px;"><h6 style="color: #374151; font-weight: 600; margin-bottom: 12px; display: flex; align-items: center;"><i class="fas fa-barcode me-2" style="color: #6366f1;"></i>Barcode</h6><p class="font-monospace" style="color: #6b7280; margin: 0; font-size: 1.1rem;">${medicine.barcode}</p></div></div>` : ''}
                    `;

                    $('#medicineModalBody').html(html);
                    currentMedicineId = id;

                    // Use Bootstrap 5 modal API
                    const modal = new bootstrap.Modal(document.getElementById('medicineModal'));
                    modal.show();
                } else {
                    showNotification(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error fetching medicine details:', error);
                showNotification('Error fetching medicine details', 'error');
            });
    }

    function deleteMedicine(id) {
        const medicine = medicinesData.find(m => m.id == id);

        if (medicine) {
            currentMedicineId = id;
            $('#deleteMedicineInfo').html(`
                <div class="alert alert-warning" style="background: #fef3c7; border: 1px solid #f59e0b; color: #92400e; border-radius: 8px; padding: 16px;">
                    <div style="display: flex; align-items: center;">
                        <i class="fas fa-pills me-2" style="font-size: 1.2rem;"></i>
                        <div>
                            <strong>${medicine.name}</strong><br>
                            <small>${medicine.generic_name} - ${medicine.strength} ${medicine.form}</small>
                        </div>
                    </div>
                </div>
            `);

            // Use Bootstrap 5 modal API
            const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
            modal.show();
        }
    }

    function confirmDelete() {
        if (currentMedicineId) {
            const formData = new FormData();
            formData.append('id', currentMedicineId);

            fetch('<?= Yii::$app->urlManager->createUrl(['inventory/delete-medicine']) ?>', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Remove from data array
                        medicinesData = medicinesData.filter(m => m.id != currentMedicineId);
                        filteredMedicinesData = filteredMedicinesData.filter(m => m.id != currentMedicineId);

                        // Update stats and refresh table
                        updateStats();
                        refreshTable();

                        showNotification(data.message, 'success');

                        // Use Bootstrap 5 modal API
                        const modal = bootstrap.Modal.getInstance(document.getElementById('deleteModal'));
                        modal.hide();
                        currentMedicineId = null;
                    } else {
                        showNotification(data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error deleting medicine:', error);
                    showNotification('Error deleting medicine', 'error');
                });
        }
    }

    function saveMedicine() {
        const form = $('#medicineForm')[0];

        if (form.checkValidity()) {
            const formData = new FormData(form);
            const isEdit = currentMedicineId !== null;
            const url = isEdit ?
                '<?= Yii::$app->urlManager->createUrl(['inventory/update-medicine']) ?>' :
                '<?= Yii::$app->urlManager->createUrl(['inventory/add-medicine']) ?>';

            fetch(url, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (isEdit) {
                            // Update existing medicine in data array
                            const index = medicinesData.findIndex(m => m.id == currentMedicineId);
                            if (index !== -1) {
                                const updatedMedicine = Object.fromEntries(formData.entries());
                                updatedMedicine.id = currentMedicineId;
                                medicinesData[index] = updatedMedicine;
                            }
                            showNotification(data.message, 'success');
                        } else {
                            // Add new medicine to data array
                            const newMedicine = Object.fromEntries(formData.entries());
                            newMedicine.id = data.medicine_id;
                            medicinesData.push(newMedicine);
                            showNotification(data.message, 'success');
                        }

                        // Update filtered data and refresh
                        filteredMedicinesData = [...medicinesData];
                        updateStats();
                        refreshTable();

                        // Use Bootstrap 5 modal API
                        const modal = bootstrap.Modal.getInstance(document.getElementById('addEditMedicineModal'));
                        modal.hide();
                    } else {
                        showNotification(data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error saving medicine:', error);
                    showNotification('Error saving medicine', 'error');
                });
        } else {
            form.reportValidity();
        }
    }

    function exportMedicines() {
        let csvContent = "Medicine Name,Generic Name,Category,Manufacturer,Strength,Form,Unit Price,Pack Price,Status\n";

        filteredMedicinesData.forEach(medicine => {
            csvContent +=
                `"${medicine.name}","${medicine.generic_name}","${medicine.category}","${medicine.manufacturer}","${medicine.strength}","${medicine.form}","${medicine.unit_price}","${medicine.pack_price}","${medicine.status}"\n`;
        });

        const blob = new Blob([csvContent], {
            type: 'text/csv'
        });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'medicines_catalog.csv';
        a.click();
        window.URL.revokeObjectURL(url);

        showNotification('Medicines exported successfully', 'success');
    }

    function toggleColumns() {
        // Use Bootstrap 5 modal API
        const modal = new bootstrap.Modal(document.getElementById('columnModal'));
        modal.show();
    }

    function initializeColumnVisibility() {
        // Set up column visibility checkboxes
        $('#colName').on('change', function() {
            toggleColumn(0, this.checked);
        });
        $('#colGeneric').on('change', function() {
            toggleColumn(1, this.checked);
        });
        $('#colCategory').on('change', function() {
            toggleColumn(2, this.checked);
        });
        $('#colManufacturer').on('change', function() {
            toggleColumn(3, this.checked);
        });
        $('#colStrength').on('change', function() {
            toggleColumn(4, this.checked);
        });
        $('#colUnitPrice').on('change', function() {
            toggleColumn(5, this.checked);
        });
        $('#colPackPrice').on('change', function() {
            toggleColumn(6, this.checked);
        });
        $('#colStatus').on('change', function() {
            toggleColumn(7, this.checked);
        });
    }

    function toggleColumn(columnIndex, visible) {
        const table = $('#medicinesTable');
        table.find('th').eq(columnIndex).toggle(visible);
        table.find('td:nth-child(' + (columnIndex + 1) + ')').toggle(visible);
    }

    function applyColumnVisibility() {
        // Use Bootstrap 5 modal API
        const modal = bootstrap.Modal.getInstance(document.getElementById('columnModal'));
        modal.hide();
        showNotification('Column visibility updated', 'success');
    }

    function editMedicineFromModal() {
        // Use Bootstrap 5 modal API
        const modal = bootstrap.Modal.getInstance(document.getElementById('medicineModal'));
        modal.hide();
        if (currentMedicineId) {
            editMedicine(currentMedicineId);
        }
    }

    function showNotification(message, type) {
        const notification = $(`
            <div style="position: fixed; top: 20px; right: 20px; padding: 16px 20px; border-radius: 8px; color: white; font-weight: 500; z-index: 1001; background: ${type === 'success' ? '#10b981' : type === 'info' ? '#3b82f6' : '#ef4444'}; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
                <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'info' ? 'info-circle' : 'exclamation-triangle'} me-2"></i>
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
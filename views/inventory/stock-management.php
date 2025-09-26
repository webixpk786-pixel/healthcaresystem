<?php

use app\components\SystemComponents;

$systemComponents = new SystemComponents();
$can = $systemComponents->checkModulePermission('25,31');
$canEdit = $can['canEdit'];
$canExport = $can['canExport'];
$canDelete = $can['canDelete'];
$canAdd = $can['canAdd'];
?>
<div class="stock-management" style="min-height: 95vh;margin: -25px;margin-bottom: 24px;">
    <!-- Header Section -->
    <div class="page-header" style="margin-bottom: 24px;">
        <div class="row">
            <div class="col-md-6">
                <h1 style="color: #1e293b; font-size: 1.8rem; font-weight: 700; margin-bottom: 8px;">
                    <i class="fas fa-boxes" style="color: #6366f1; margin-right: 12px;"></i>
                    Stock Management
                </h1>
                <p style="color: #64748b; font-size: 0.95rem; margin: 0;">
                    Monitor stock levels, track inventory movements, and manage warehouse locations
                </p>
            </div>
            <div class="col-md-6 text-right">
                <div class="header-actions" style="display: flex; gap: 8px; justify-content: flex-end;">
                    <button class="btn btn-sm btn-outline-secondary" onclick="manualRefresh()"
                        style="display: flex;align-items: center;gap: 8px; border: 1px solid #e2e8f0; padding: 6px 12px; border-radius: 6px; font-size: 0.8rem; background: white; color: #64748b; margin-right: 8px;">
                        <i class="fas fa-sync-alt"></i> Refresh
                    </button>
                    <button class="btn btn-sm btn-outline-secondary" onclick="toggleColumns()"
                        style="display: flex;align-items: center;gap: 8px; border: 1px solid #e2e8f0; padding: 6px 12px; border-radius: 6px; font-size: 0.8rem; background: white; color: #64748b; margin-right: 8px;">
                        <i class="fas fa-columns"></i> Columns
                    </button>
                    <?php if ($canExport): ?>
                        <button class="btn btn-sm btn-outline-secondary" onclick="exportStock()"
                            style="border: 1px solid #e2e8f0; padding: 6px 12px; border-radius: 6px; font-size: 0.8rem; background: white; color: #64748b;">
                            <i class="fas fa-download"></i> Export
                        </button>
                    <?php endif; ?>
                    <?php if ($canAdd): ?>
                        <button class="btn btn-primary" onclick="addStock()"
                            style="background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); border: none; padding: 8px 16px; border-radius: 8px; font-size: 0.85rem; font-weight: 500; box-shadow: 0 2px 8px rgba(99, 102, 241, 0.25);">
                            <i class="fas fa-plus"></i> Add
                        </button>
                    <?php endif; ?>
                    <?php if ($canEdit): ?>
                        <button class="btn btn-outline-secondary" onclick="transferStock()"
                            style="border: 1px solid #e2e8f0; padding: 8px 16px; border-radius: 8px; font-size: 0.85rem; background: white; color: #64748b;">
                            <i class="fas fa-exchange-alt"></i> Transfer
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Stock Summary Cards -->
    <div class="stock-summary" style="margin-bottom: 20px;">
        <div class="row g-2">
            <!-- Card 1 - Total Items -->
            <div class="col-lg-3 col-md-6">
                <div class="summary-card h-100"
                    style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 12px; padding: 12px; box-shadow: 0 4px 16px rgba(102, 126, 234, 0.25); border: none; position: relative; overflow: hidden;">
                    <div style="display: flex; align-items: center; font-size: 1.4rem; font-weight: 700; color: white;">
                        <i class="fas fa-boxes" style="font-size: 1.2rem; opacity: 0.9; margin-right: 8px;"></i>
                        <span id="totalItems"><?= count($stockItems) ?></span>
                        <small style="font-size: 0.7rem; font-weight: 500; margin: 0 8px;">Items</small>
                        <div
                            style="margin-left: auto; background: rgba(255,255,255,0.2); padding: 2px 6px; border-radius: 8px; backdrop-filter: blur(10px); font-size: 0.6rem; font-weight: 600;">
                            TOTAL
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 2 - Low Stock Items -->
            <div class="col-lg-3 col-md-6">
                <div class="summary-card h-100"
                    style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); border-radius: 12px; padding: 12px; box-shadow: 0 4px 16px rgba(240, 147, 251, 0.25); border: none; position: relative; overflow: hidden;">
                    <div style="display: flex; align-items: center; font-size: 1.4rem; font-weight: 700; color: white;">
                        <i class="fas fa-exclamation-triangle"
                            style="font-size: 1.2rem; opacity: 0.9; margin-right: 8px;"></i>
                        <span id="lowStockItems"><?= count($lowStockItems) ?></span>
                        <small style="font-size: 0.7rem; font-weight: 500; margin: 0 8px;">Low Stock</small>
                        <div
                            style="margin-left: auto; background: rgba(255,255,255,0.2); padding: 2px 6px; border-radius: 8px; backdrop-filter: blur(10px); font-size: 0.6rem; font-weight: 600;">
                            ALERT
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 3 - Expiring Soon -->
            <div class="col-lg-3 col-md-6">
                <div class="summary-card h-100"
                    style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); border-radius: 12px; padding: 12px; box-shadow: 0 4px 16px rgba(79, 172, 254, 0.25); border: none; position: relative; overflow: hidden;">
                    <div style="display: flex; align-items: center; font-size: 1.4rem; font-weight: 700; color: white;">
                        <i class="fas fa-clock" style="font-size: 1.2rem; opacity: 0.9; margin-right: 8px;"></i>
                        <span id="expiringSoon">0</span>
                        <small style="font-size: 0.7rem; font-weight: 500; margin: 0 8px;">Expiring Soon</small>
                        <div
                            style="margin-left: auto; background: rgba(255,255,255,0.2); padding: 2px 6px; border-radius: 8px; backdrop-filter: blur(10px); font-size: 0.6rem; font-weight: 600;">
                            URGENT
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 4 - Unique Locations -->
            <div class="col-lg-3 col-md-6">
                <div class="summary-card h-100"
                    style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); border-radius: 12px; padding: 12px; box-shadow: 0 4px 16px rgba(67, 233, 123, 0.25); border: none; position: relative; overflow: hidden;">
                    <div style="display: flex; align-items: center; font-size: 1.4rem; font-weight: 700; color: white;">
                        <i class="fas fa-warehouse" style="font-size: 1.2rem; opacity: 0.9; margin-right: 8px;"></i>
                        <span id="uniqueLocations"><?= count($locations) ?></span>
                        <small style="font-size: 0.7rem; font-weight: 500; margin: 0 8px;">Locations</small>
                        <div
                            style="margin-left: auto; background: rgba(255,255,255,0.2); padding: 2px 6px; border-radius: 8px; backdrop-filter: blur(10px); font-size: 0.6rem; font-weight: 600;">
                            LOCATIONS
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
                        <label for="filterLocation"
                            style="font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 0.75rem;">Location</label>
                        <select class="form-control" id="filterLocation"
                            style="border: 1px solid #d1d5db; border-radius: 4px; padding: 6px 8px; font-size: 0.75rem; height: 32px;">
                            <option value="">All Locations</option>
                            <?php foreach ($locations as $location): ?>
                                <option value="<?= $location['id'] ?>"><?= htmlspecialchars($location['name']) ?></option>
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
                            <option value="in_stock">In Stock</option>
                            <option value="low_stock">Low Stock</option>
                            <option value="critical">Critical</option>
                            <option value="out_of_stock">Out of Stock</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group mb-0">
                        <label for="filterExpiry"
                            style="font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 0.75rem;">Expiry</label>
                        <select class="form-control" id="filterExpiry"
                            style="border: 1px solid #d1d5db; border-radius: 4px; padding: 6px 8px; font-size: 0.75rem; height: 32px;">
                            <option value="">All</option>
                            <option value="expiring_soon">Expiring Soon (≤30 days)</option>
                            <option value="expiring_medium">Expiring Medium (31-90 days)</option>
                            <option value="expiring_later">Expiring Later (>90 days)</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group mb-0">
                        <label for="filterStockLevel"
                            style="font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 0.75rem;">Stock
                            Level</label>
                        <select class="form-control" id="filterStockLevel"
                            style="border: 1px solid #d1d5db; border-radius: 4px; padding: 6px 8px; font-size: 0.75rem; height: 32px;">
                            <option value="">All Levels</option>
                            <option value="high">High (>70%)</option>
                            <option value="medium">Medium (30-70%)</option>
                            <option value="low">Low (< 30%)</option>
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
                                Medicine</th>
                            <th
                                style="padding: 12px; text-align: left; font-weight: 600; color: #1e293b; font-size: 0.9rem;">
                                Batch Number</th>
                            <th
                                style="padding: 12px; text-align: center; font-weight: 600; color: #1e293b; font-size: 0.9rem;">
                                Quantity</th>
                            <th
                                style="padding: 12px; text-align: left; font-weight: 600; color: #1e293b; font-size: 0.9rem;">
                                Location</th>
                            <th
                                style="padding: 12px; text-align: center; font-weight: 600; color: #1e293b; font-size: 0.9rem;">
                                Stock Level</th>
                            <th
                                style="padding: 12px; text-align: center; font-weight: 600; color: #1e293b; font-size: 0.9rem;">
                                Expiry Date</th>
                            <th
                                style="padding: 12px; text-align: center; font-weight: 600; color: #1e293b; font-size: 0.9rem;">
                                Status</th>
                            <?php if ($canEdit || $canAdd || $canDelete): ?>
                                <th
                                    style="padding: 12px; text-align: center; font-weight: 600; color: #1e293b; font-size: 0.9rem;">
                                    Actions</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody id="stockTableBody">
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
                            Showing 1 to 25 of 100 entries
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

<!-- Add Stock Modal -->
<div class="modal fade" id="addStockModal" tabindex="-1" role="dialog" aria-labelledby="addStockModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 20px 40px rgba(0,0,0,0.15);">
            <div class="modal-header"
                style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 12px 12px 0 0; border-bottom: none; padding: 12px 16px;">
                <h5 class="modal-title" id="addStockModalLabel" style="font-size: 1rem; font-weight: 600; margin: 0;">
                    <i class="fas fa-plus me-2"></i>Add New Stock
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"
                    style="opacity: 0.8; font-size: 1rem;"></button>
            </div>
            <div class="modal-body" style="padding: 16px; max-height: 70vh; overflow-y: auto;">
                <form id="addStockForm">
                    <!-- Basic Information Section -->
                    <div class="form-section"
                        style="background: white; border-radius: 6px; padding: 12px; margin-bottom: 12px; border: 1px solid #e2e8f0;">
                        <h6
                            style="color: #374151; font-weight: 600; margin-bottom: 12px; display: flex; align-items: center; font-size: 0.8rem;">
                            <i class="fas fa-info-circle me-2" style="color: #6366f1; font-size: 0.7rem;"></i>Basic
                            Information
                        </h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-2">
                                    <label for="medicineSelect"
                                        style="font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 0.75rem;">Medicine
                                        *</label>
                                    <select class="form-control" id="medicineSelect" name="medicine_id" required
                                        style="border: 1px solid #d1d5db; border-radius: 4px; padding: 6px 8px; font-size: 0.75rem; transition: all 0.2s; height: 32px;">
                                        <option value="">Select Medicine</option>
                                        <?php foreach ($medicines as $medicine) { ?>
                                            <option value="<?php echo $medicine['id']; ?>">
                                                <?php echo htmlspecialchars($medicine['name']); ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-2">
                                    <label for="batchNumber"
                                        style="font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 0.75rem;">Batch
                                        Number *</label>
                                    <input type="text" class="form-control" id="batchNumber" name="batch_number"
                                        required
                                        style="border: 1px solid #d1d5db; border-radius: 4px; padding: 6px 8px; font-size: 0.75rem; transition: all 0.2s; height: 32px;">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-2">
                                    <label for="quantity"
                                        style="font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 0.75rem;">Quantity
                                        *</label>
                                    <input type="number" class="form-control" id="quantity" name="quantity" required
                                        style="border: 1px solid #d1d5db; border-radius: 4px; padding: 6px 8px; font-size: 0.75rem; transition: all 0.2s; height: 32px;">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-2">
                                    <label for="unit"
                                        style="font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 0.75rem;">Unit
                                        *</label>
                                    <select class="form-control" id="unit" name="unit" required
                                        style="border: 1px solid #d1d5db; border-radius: 4px; padding: 6px 8px; font-size: 0.75rem; transition: all 0.2s; height: 32px;">
                                        <option value="">Select Unit</option>
                                        <option value="Tablets">Tablets</option>
                                        <option value="Capsules">Capsules</option>
                                        <option value="Bottles">Bottles</option>
                                        <option value="Vials">Vials</option>
                                        <option value="Tubes">Tubes</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Stock Details Section -->
                    <div class="form-section"
                        style="background: white; border-radius: 6px; padding: 12px; margin-bottom: 12px; border: 1px solid #e2e8f0;">
                        <h6
                            style="color: #374151; font-weight: 600; margin-bottom: 12px; display: flex; align-items: center; font-size: 0.8rem;">
                            <i class="fas fa-warehouse me-2" style="color: #6366f1; font-size: 0.7rem;"></i>Stock
                            Details
                        </h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-2">
                                    <label for="location"
                                        style="font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 0.75rem;">Location
                                        *</label>
                                    <select class="form-control" id="location" name="location" required
                                        style="border: 1px solid #d1d5db; border-radius: 4px; padding: 6px 8px; font-size: 0.75rem; transition: all 0.2s; height: 32px;">
                                        <option value="">Select Location</option>
                                        <?php foreach ($locations as $location) { ?>
                                            <option value="<?php echo $location['id']; ?>">
                                                <?php echo htmlspecialchars($location['name']); ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-2">
                                    <label for="expiryDate"
                                        style="font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 0.75rem;">Expiry
                                        Date *</label>
                                    <input type="date" class="form-control" id="expiryDate" name="expiry_date" required
                                        style="border: 1px solid #d1d5db; border-radius: 4px; padding: 6px 8px; font-size: 0.75rem; transition: all 0.2s; height: 32px;">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-2">
                                    <label for="minStock"
                                        style="font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 0.75rem;">Min
                                        Stock Level</label>
                                    <input type="number" class="form-control" id="minStock" name="min_stock"
                                        style="border: 1px solid #d1d5db; border-radius: 4px; padding: 6px 8px; font-size: 0.75rem; transition: all 0.2s; height: 32px;">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-2">
                                    <label for="maxStock"
                                        style="font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 0.75rem;">Max
                                        Stock Level</label>
                                    <input type="number" class="form-control" id="maxStock" name="max_stock"
                                        style="border: 1px solid #d1d5db; border-radius: 4px; padding: 6px 8px; font-size: 0.75rem; transition: all 0.2s; height: 32px;">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Financial Information Section -->
                    <div class="form-section"
                        style="background: white; border-radius: 6px; padding: 12px; border: 1px solid #e2e8f0;">
                        <h6
                            style="color: #374151; font-weight: 600; margin-bottom: 12px; display: flex; align-items: center; font-size: 0.8rem;">
                            <i class="fas fa-dollar-sign me-2" style="color: #6366f1; font-size: 0.7rem;"></i>Financial
                            Information
                        </h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-2">
                                    <label for="purchasePrice"
                                        style="font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 0.75rem;">Purchase
                                        Price</label>
                                    <input type="number" class="form-control" id="purchasePrice" name="purchase_price"
                                        step="0.01"
                                        style="border: 1px solid #d1d5db; border-radius: 4px; padding: 6px 8px; font-size: 0.75rem; transition: all 0.2s; height: 32px;">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-2">
                                    <label for="sellingPrice"
                                        style="font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 0.75rem;">Selling
                                        Price</label>
                                    <input type="number" class="form-control" id="sellingPrice" name="selling_price"
                                        step="0.01"
                                        style="border: 1px solid #d1d5db; border-radius: 4px; padding: 6px 8px; font-size: 0.75rem; transition: all 0.2s; height: 32px;">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="supplier"
                                style="font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 0.75rem;">Supplier</label>
                            <select class="form-control" id="supplier" name="supplier_id"
                                style="border: 1px solid #d1d5db; border-radius: 4px; padding: 6px 8px; font-size: 0.75rem; transition: all 0.2s; height: 32px;">
                                <option value="">Select Supplier</option>
                                <!-- Suppliers will be loaded via AJAX -->
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer"
                style="border-top: 1px solid #e2e8f0; padding: 12px 16px; background: #f8fafc; border-radius: 0 0 12px 12px;">
                <div style="display: flex; gap: 6px; margin-left: auto;">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal"
                        style="padding: 6px 12px; border-radius: 6px; font-weight: 500; font-size: 0.8rem;">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="saveStock()"
                        style="padding: 6px 12px; border-radius: 6px; font-weight: 500; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; font-size: 0.8rem;">Save</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Stock Modal -->
<div class="modal fade" id="editStockModal" tabindex="-1" role="dialog" aria-labelledby="editStockModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 20px 40px rgba(0,0,0,0.15);">
            <div class="modal-header"
                style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 12px 12px 0 0; border-bottom: none; padding: 12px 16px;">
                <h5 class="modal-title" id="editStockModalLabel" style="font-size: 1rem; font-weight: 600; margin: 0;">
                    <i class="fas fa-edit me-2"></i>Edit Stock
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"
                    style="opacity: 0.8; font-size: 1rem;"></button>
            </div>
            <div class="modal-body" style="padding: 16px; max-height: 70vh; overflow-y: auto;">
                <form id="editStockForm">
                    <input type="hidden" id="editStockId" name="id">

                    <!-- Same form structure as Add Stock Modal -->
                    <div class="form-section"
                        style="background: white; border-radius: 6px; padding: 12px; margin-bottom: 12px; border: 1px solid #e2e8f0;">
                        <h6
                            style="color: #374151; font-weight: 600; margin-bottom: 12px; display: flex; align-items: center; font-size: 0.8rem;">
                            <i class="fas fa-info-circle me-2" style="color: #6366f1; font-size: 0.7rem;"></i>Basic
                            Information
                        </h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-2">
                                    <label for="editMedicineName"
                                        style="font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 0.75rem;">Medicine
                                        *</label>
                                    <input type="text" class="form-control" id="editMedicineName" name="medicine_name"
                                        readonly
                                        style="border: 1px solid #d1d5db; border-radius: 4px; padding: 6px 8px; font-size: 0.75rem; transition: all 0.2s; height: 32px; background: #f9fafb;">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-2">
                                    <label for="editBatchNumber"
                                        style="font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 0.75rem;">Batch
                                        Number *</label>
                                    <input type="text" class="form-control" id="editBatchNumber" name="batch_number"
                                        required
                                        style="border: 1px solid #d1d5db; border-radius: 4px; padding: 6px 8px; font-size: 0.75rem; transition: all 0.2s; height: 32px;">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-2">
                                    <label for="editQuantity"
                                        style="font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 0.75rem;">Quantity
                                        *</label>
                                    <input type="number" class="form-control" id="editQuantity" name="quantity" required
                                        style="border: 1px solid #d1d5db; border-radius: 4px; padding: 6px 8px; font-size: 0.75rem; transition: all 0.2s; height: 32px;">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-2">
                                    <label for="editLocation"
                                        style="font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 0.75rem;">Location
                                        *</label>
                                    <select class="form-control" id="editLocation" name="location" required
                                        style="border: 1px solid #d1d5db; border-radius: 4px; padding: 6px 8px; font-size: 0.75rem; transition: all 0.2s; height: 32px;">
                                        <option value="">Select Location</option>
                                        <?php foreach ($locations as $location) { ?>
                                            <option value="<?php echo $location['id']; ?>">
                                                <?php echo htmlspecialchars($location['name']); ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-2">
                                    <label for="editExpiryDate"
                                        style="font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 0.75rem;">Expiry
                                        Date *</label>
                                    <input type="date" class="form-control" id="editExpiryDate" name="expiry_date"
                                        required
                                        style="border: 1px solid #d1d5db; border-radius: 4px; padding: 6px 8px; font-size: 0.75rem; transition: all 0.2s; height: 32px;">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-2">
                                    <label for="editMinStock"
                                        style="font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 0.75rem;">Min
                                        Stock Level</label>
                                    <input type="number" class="form-control" id="editMinStock" name="min_stock"
                                        style="border: 1px solid #d1d5db; border-radius: 4px; padding: 6px 8px; font-size: 0.75rem; transition: all 0.2s; height: 32px;">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-2">
                                    <label for="editMaxStock"
                                        style="font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 0.75rem;">Max
                                        Stock Level</label>
                                    <input type="number" class="form-control" id="editMaxStock" name="max_stock"
                                        style="border: 1px solid #d1d5db; border-radius: 4px; padding: 6px 8px; font-size: 0.75rem; transition: all 0.2s; height: 32px;">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-2">
                                    <label for="editPurchasePrice"
                                        style="font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 0.75rem;">Purchase
                                        Price</label>
                                    <input type="number" class="form-control" id="editPurchasePrice"
                                        name="purchase_price" step="0.01"
                                        style="border: 1px solid #d1d5db; border-radius: 4px; padding: 6px 8px; font-size: 0.75rem; transition: all 0.2s; height: 32px;">
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
                    <button type="button" class="btn btn-primary" onclick="updateStock()"
                        style="padding: 6px 12px; border-radius: 6px; font-weight: 500; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; font-size: 0.8rem;">Update</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Quantity Modal -->
<div class="modal fade" id="addQuantityModal" tabindex="-1" role="dialog" aria-labelledby="addQuantityModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 20px 40px rgba(0,0,0,0.15);">
            <div class="modal-header"
                style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; border-radius: 12px 12px 0 0; border-bottom: none; padding: 12px 16px;">
                <h5 class="modal-title" id="addQuantityModalLabel"
                    style="font-size: 1rem; font-weight: 600; margin: 0;">
                    <i class="fas fa-plus me-2"></i>Add Stock Quantity
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"
                    style="opacity: 0.8; font-size: 1rem;"></button>
            </div>
            <div class="modal-body" style="padding: 16px;">
                <form id="addQuantityForm">
                    <input type="hidden" id="addQuantityStockId" name="stock_id">

                    <div class="form-group mb-3">
                        <label for="currentQuantity"
                            style="font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 0.8rem;">Current
                            Quantity</label>
                        <input type="text" class="form-control" id="currentQuantity" readonly
                            style="border: 1px solid #d1d5db; border-radius: 4px; padding: 8px 12px; font-size: 0.8rem; background: #f9fafb;">
                    </div>

                    <div class="form-group mb-3">
                        <label for="addQuantity"
                            style="font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 0.8rem;">Quantity to
                            Add *</label>
                        <input type="number" class="form-control" id="addQuantity" name="quantity" required
                            style="border: 1px solid #d1d5db; border-radius: 4px; padding: 8px 12px; font-size: 0.8rem;">
                    </div>

                    <div class="form-group">
                        <label for="addQuantityReason"
                            style="font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 0.8rem;">Reason</label>
                        <textarea class="form-control" id="addQuantityReason" name="reason" rows="2"
                            style="border: 1px solid #d1d5db; border-radius: 4px; padding: 8px 12px; font-size: 0.8rem; resize: vertical;"
                            placeholder="Enter reason for adding stock..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer"
                style="border-top: 1px solid #e2e8f0; padding: 12px 16px; background: #f8fafc; border-radius: 0 0 12px 12px;">
                <div style="display: flex; gap: 6px; margin-left: auto;">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal"
                        style="padding: 6px 12px; border-radius: 6px; font-weight: 500; font-size: 0.8rem;">Cancel</button>
                    <button type="button" class="btn btn-success" onclick="confirmAddQuantity()"
                        style="padding: 6px 12px; border-radius: 6px; font-weight: 500; background: linear-gradient(135deg, #10b981 0%, #059669 100%); border: none; font-size: 0.8rem;">Add
                        Quantity</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Transfer Stock Modal -->
<div class="modal fade" id="transferStockModal" tabindex="-1" role="dialog" aria-labelledby="transferStockModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 20px 40px rgba(0,0,0,0.15);">
            <div class="modal-header"
                style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white; border-radius: 12px 12px 0 0; border-bottom: none; padding: 12px 16px;">
                <h5 class="modal-title" id="transferStockModalLabel"
                    style="font-size: 1rem; font-weight: 600; margin: 0;">
                    <i class="fas fa-exchange-alt me-2"></i>Transfer Stock
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"
                    style="opacity: 0.8; font-size: 1rem;"></button>
            </div>
            <div class="modal-body" style="padding: 16px;">
                <form id="transferStockForm">
                    <input type="hidden" id="transferStockId" name="stock_id">

                    <div class="form-group mb-3">
                        <label for="transferMedicineName"
                            style="font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 0.8rem;">Medicine</label>
                        <input type="text" class="form-control" id="transferMedicineName" readonly
                            style="border: 1px solid #d1d5db; border-radius: 4px; padding: 8px 12px; font-size: 0.8rem; background: #f9fafb;">
                    </div>

                    <div class="form-group mb-3">
                        <label for="transferCurrentLocation"
                            style="font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 0.8rem;">Current
                            Location</label>
                        <input type="text" class="form-control" id="transferCurrentLocation" readonly
                            style="border: 1px solid #d1d5db; border-radius: 4px; padding: 8px 12px; font-size: 0.8rem; background: #f9fafb;">
                    </div>

                    <div class="form-group mb-3">
                        <label for="transferQuantity"
                            style="font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 0.8rem;">Quantity to
                            Transfer *</label>
                        <input type="number" class="form-control" id="transferQuantity" name="quantity" required
                            style="border: 1px solid #d1d5db; border-radius: 4px; padding: 8px 12px; font-size: 0.8rem;">
                    </div>

                    <div class="form-group mb-3">
                        <label for="transferToLocation"
                            style="font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 0.8rem;">Transfer To
                            Location *</label>
                        <select class="form-control" id="transferToLocation" name="to_location" required
                            style="border: 1px solid #d1d5db; border-radius: 4px; padding: 8px 12px; font-size: 0.8rem;">
                            <option value="">Select Destination</option>
                            <?php foreach ($locations as $location) { ?>
                                <option value="<?php echo $location['id']; ?>">
                                    <?php echo htmlspecialchars($location['name']); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="transferReason"
                            style="font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 0.8rem;">Transfer
                            Reason</label>
                        <textarea class="form-control" id="transferReason" name="reason" rows="2"
                            style="border: 1px solid #d1d5db; border-radius: 4px; padding: 8px 12px; font-size: 0.8rem; resize: vertical;"
                            placeholder="Enter reason for transfer..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer"
                style="border-top: 1px solid #e2e8f0; padding: 12px 16px; background: #f8fafc; border-radius: 0 0 12px 12px;">
                <div style="display: flex; gap: 6px; margin-left: auto;">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal"
                        style="padding: 6px 12px; border-radius: 6px; font-weight: 500; font-size: 0.8rem;">Cancel</button>
                    <button type="button" class="btn btn-warning" onclick="confirmTransfer()"
                        style="padding: 6px 12px; border-radius: 6px; font-weight: 500; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); border: none; font-size: 0.8rem;">Transfer</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Stock Details Modal -->
<div class="modal fade" id="stockDetailsModal" tabindex="-1" role="dialog" aria-labelledby="stockDetailsModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 20px 40px rgba(0,0,0,0.15);">
            <div class="modal-header"
                style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 12px 12px 0 0; border-bottom: none; padding: 16px 20px;">
                <h5 class="modal-title" id="stockDetailsModalLabel"
                    style="font-size: 1.1rem; font-weight: 600; margin: 0;">
                    <i class="fas fa-boxes me-2"></i>Stock Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"
                    style="opacity: 0.8; font-size: 1rem;"></button>
            </div>
            <div class="modal-body" id="stockDetailsBody" style="padding: 20px; overflow: visible;">
                <!-- Stock details will be loaded here -->
            </div>
            <div class="modal-footer"
                style="border-top: 1px solid #e2e8f0; padding: 16px 20px; background: #f8fafc; border-radius: 0 0 12px 12px;">
                <div style="display: flex; gap: 8px; margin-left: auto;">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal"
                        style="padding: 8px 16px; border-radius: 6px; font-weight: 500; font-size: 0.85rem;">Close</button>
                    <?php if ($canEdit): ?>
                        <button type="button" class="btn btn-primary" onclick="editStockFromDetails()"
                            style="padding: 8px 16px; border-radius: 6px; font-weight: 500; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; font-size: 0.85rem;">Edit</button>
                    <?php endif; ?>
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
                    display in the stock table:</p>
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
                            <input class="form-check-input" type="checkbox" id="colQuantity" checked
                                style="transform: scale(0.8);">
                            <label class="form-check-label" for="colQuantity"
                                style="font-weight: 500; font-size: 0.7rem;">Quantity</label>
                        </div>
                        <div class="form-check mb-1">
                            <input class="form-check-input" type="checkbox" id="colLocation" checked
                                style="transform: scale(0.8);">
                            <label class="form-check-label" for="colLocation"
                                style="font-weight: 500; font-size: 0.7rem;">Location</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check mb-1">
                            <input class="form-check-input" type="checkbox" id="colStockLevel" checked
                                style="transform: scale(0.8);">
                            <label class="form-check-label" for="colStockLevel"
                                style="font-weight: 500; font-size: 0.7rem;">Stock Level</label>
                        </div>
                        <div class="form-check mb-1">
                            <input class="form-check-input" type="checkbox" id="colExpiry" checked
                                style="transform: scale(0.8);">
                            <label class="form-check-label" for="colExpiry"
                                style="font-weight: 500; font-size: 0.7rem;">Expiry Date</label>
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
<div class="modal fade" id="deleteStockModal" tabindex="-1" role="dialog" aria-labelledby="deleteStockModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content" style="border-radius: 10px; border: none; box-shadow: 0 20px 40px rgba(0,0,0,0.15);">
            <div class="modal-header"
                style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: white; border-radius: 10px 10px 0 0; border-bottom: none; padding: 10px 14px;">
                <h5 class="modal-title" id="deleteStockModalLabel"
                    style="font-size: 0.9rem; font-weight: 600; margin: 0;">
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
                    want to delete this stock item?</h6>
                <p style="color: #6b7280; margin-bottom: 12px; font-size: 0.7rem;">This action cannot be undone and will
                    permanently remove the stock item from your inventory.</p>
                <div id="deleteStockInfo"></div>
            </div>
            <div class="modal-footer"
                style="border-top: 1px solid #e2e8f0; padding: 10px 14px; background: #f8fafc; border-radius: 0 0 10px 10px;">
                <div style="display: flex; gap: 6px; margin-left: auto;">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal"
                        style="padding: 5px 10px; border-radius: 4px; font-weight: 500; font-size: 0.7rem;">
                        Cancel
                    </button>
                    <button type="button" class="btn btn-danger" onclick="confirmDeleteStock()"
                        style="padding: 5px 10px; border-radius: 4px; font-weight: 500; background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); border: none; font-size: 0.7rem;">
                        Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .stock-management .summary-card {
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .stock-management .summary-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .stock-management .table tbody tr:hover {
        background: #f8fafc;
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        transition: all 0.2s ease;
    }

    .stock-management .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
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

    @media (max-width: 768px) {
        .stock-management .stock-summary .col-md-3 {
            margin-bottom: 16px;
        }

        .modal-dialog {
            margin: 10px;
        }
    }
</style>

<script>
    let stockData = [
        <?php foreach ($stockItems as $item): ?> {
                id: <?= $item['id'] ?>,
                medicine_name: "<?= htmlspecialchars($item['medicine_name']) ?>",
                batch_number: "<?= htmlspecialchars($item['batch_number']) ?>",
                quantity: <?= $item['quantity'] ?>,
                unit: "<?= htmlspecialchars($item['unit']) ?>",
                location: "<?= htmlspecialchars($item['location']) ?>",
                location_id: <?= $item['location_id'] ?>,
                max_stock: <?= $item['max_stock'] ?>,
                min_stock: <?= $item['min_stock'] ?>,
                purchase_price: <?= $item['purchase_price'] ?>,
                selling_price: <?= $item['selling_price'] ?>,
                supplier: "<?= htmlspecialchars($item['supplier_name'] ?? 'N/A') ?>",
                expiry_date: "<?= $item['expiry_date'] ?>",
                status: "<?= htmlspecialchars($item['status']) ?>",
                last_updated: "<?= $item['updated_at'] ?? 'N/A' ?>"
            },
        <?php endforeach; ?>
    ];

    let currentStockId = null;
    let filteredData = [...stockData];
    let currentPage = 1;
    let itemsPerPage = 25;
    let totalPages = 1;

    $(document).ready(function() {
        // Initialize column visibility
        initializeColumnVisibility();

        // Add event listeners for modals
        $('#addStockModal').on('hidden.bs.modal', function() {
            $('#addStockForm')[0].reset();
            currentStockId = null;
        });
        $('#editStockModal').on('hidden.bs.modal', function() {
            $('#editStockForm')[0].reset();
            currentStockId = null;
        });
        $('#addQuantityModal').on('hidden.bs.modal', function() {
            $('#addQuantityForm')[0].reset();
            currentStockId = null;
        });
        $('#transferStockModal').on('hidden.bs.modal', function() {
            $('#transferStockForm')[0].reset();
            currentStockId = null;
        });
        $('#stockDetailsModal').on('hidden.bs.modal', function() {
            currentStockId = null;
        });
        $('#columnModal').on('hidden.bs.modal', function() {
            // No specific reset needed for column modal
        });

        // Add event listeners for modal saves
        $('#addStockForm').on('submit', function(e) {
            e.preventDefault();
            saveStock();
        });
        $('#editStockForm').on('submit', function(e) {
            e.preventDefault();
            updateStock();
        });
        $('#addQuantityForm').on('submit', function(e) {
            e.preventDefault();
            confirmAddQuantity();
        });
        $('#transferStockForm').on('submit', function(e) {
            e.preventDefault();
            confirmTransfer();
        });

        // Add event listeners for filter inputs
        $('#filterMedicine, #filterLocation, #filterStatus, #filterExpiry, #filterStockLevel').on('input change',
            function() {
                applyFilters();
            });

        // Add event listener for items per page change
        $('#itemsPerPage').on('change', function() {
            itemsPerPage = parseInt($(this).val());
            currentPage = 1;
            refreshStockTable();
        });

        // Load suppliers for add stock modal
        loadSuppliers();

        // Initialize stats and table
        updateStats();
        refreshStockTable();
    });

    function loadSuppliers() {
        $.ajax({
            url: '<?= Yii::$app->urlManager->createUrl(['inventory/get-suppliers']) ?>',
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    const supplierSelect = $('#supplier');
                    supplierSelect.empty().append('<option value="">Select Supplier</option>');
                    response.data.forEach(function(supplier) {
                        supplierSelect.append(
                            `<option value="${supplier.id}">${supplier.name}</option>`);
                    });
                }
            },
            error: function() {
                showNotification('Error loading suppliers', 'error');
            }
        });
    }

    function updateStats() {
        const now = new Date();
        let lowStockCount = 0;
        let expiringSoonCount = 0;
        const uniqueLocations = new Set();

        stockData.forEach(item => {
            // Count low stock items
            const stockPercentage = (item.quantity / item.max_stock) * 100;
            if (stockPercentage <= 30) {
                lowStockCount++;
            }

            // Count expiring soon items (within 30 days)
            const expiryDate = new Date(item.expiry_date);
            const daysUntilExpiry = Math.ceil((expiryDate - now) / (1000 * 60 * 60 * 24));
            if (daysUntilExpiry <= 30) {
                expiringSoonCount++;
            }

            // Collect unique locations
            uniqueLocations.add(item.location);
        });

        // Update stats cards
        $('#totalItems').text(stockData.length);
        $('#lowStockItems').text(lowStockCount);
        $('#expiringSoon').text(expiringSoonCount);
        $('#uniqueLocations').text(uniqueLocations.size);
    }

    function applyFilters() {
        const medicineFilter = $('#filterMedicine').val().toLowerCase();
        const locationFilter = $('#filterLocation').val();
        const statusFilter = $('#filterStatus').val();
        const expiryFilter = $('#filterExpiry').val();
        const stockLevelFilter = $('#filterStockLevel').val();

        filteredData = stockData.filter(item => {
            // Medicine filter
            if (medicineFilter && !item.medicine_name.toLowerCase().includes(medicineFilter)) {
                return false;
            }

            // Location filter
            if (locationFilter && item.location_id != locationFilter) {
                return false;
            }

            // Status filter
            if (statusFilter && item.status !== statusFilter) {
                return false;
            }

            // Expiry filter
            if (expiryFilter) {
                const expiryDate = new Date(item.expiry_date);
                const now = new Date();
                const daysUntilExpiry = Math.ceil((expiryDate - now) / (1000 * 60 * 60 * 24));

                switch (expiryFilter) {
                    case 'expiring_soon':
                        if (daysUntilExpiry > 30) return false;
                        break;
                    case 'expiring_medium':
                        if (daysUntilExpiry <= 30 || daysUntilExpiry > 90) return false;
                        break;
                    case 'expiring_later':
                        if (daysUntilExpiry <= 90) return false;
                        break;
                }
            }

            // Stock level filter
            if (stockLevelFilter) {
                const stockPercentage = (item.quantity / item.max_stock) * 100;

                switch (stockLevelFilter) {
                    case 'high':
                        if (stockPercentage <= 70) return false;
                        break;
                    case 'medium':
                        if (stockPercentage <= 30 || stockPercentage > 70) return false;
                        break;
                    case 'low':
                        if (stockPercentage >= 30) return false;
                        break;
                }
            }

            return true;
        });

        currentPage = 1;
        refreshStockTable();
        showNotification(`Showing ${filteredData.length} of ${stockData.length} items`, 'info');
    }

    function clearFilters() {
        $('#filterMedicine').val('');
        $('#filterLocation').val('');
        $('#filterStatus').val('');
        $('#filterExpiry').val('');
        $('#filterStockLevel').val('');

        filteredData = [...stockData];
        currentPage = 1;
        refreshStockTable();
        showNotification('Filters cleared', 'info');
    }

    function refreshStockTable() {
        const tbody = $('#stockTableBody');
        tbody.empty();

        // Calculate pagination
        totalPages = Math.ceil(filteredData.length / itemsPerPage);
        const startIndex = (currentPage - 1) * itemsPerPage;
        const endIndex = Math.min(startIndex + itemsPerPage, filteredData.length);
        const pageData = filteredData.slice(startIndex, endIndex);

        if (pageData.length === 0) {
            tbody.append(`
                <tr>
                    <td colspan="8" style="text-align: center; padding: 40px; color: #6b7280;">
                        <i class="fas fa-search" style="font-size: 2rem; margin-bottom: 12px; display: block;"></i>
                        No stock items found matching your criteria
                    </td>
                </tr>
            `);
        } else {
            pageData.forEach(item => {
                const stockPercentage = (item.quantity / item.max_stock) * 100;
                const stockColor = stockPercentage > 70 ? '#10b981' : (stockPercentage > 30 ? '#f59e0b' :
                    '#ef4444');

                const expiryDate = new Date(item.expiry_date);
                const now = new Date();
                const daysUntilExpiry = Math.ceil((expiryDate - now) / (1000 * 60 * 60 * 24));
                const expiryColor = daysUntilExpiry > 90 ? '#10b981' : (daysUntilExpiry > 30 ? '#f59e0b' :
                    '#ef4444');

                const statusColor = item.status === 'in_stock' ? '#10b981' : (item.status === 'low_stock' ?
                    '#f59e0b' : '#ef4444');

                const row = `
                    <tr class="stock-row" data-id="${item.id}" style="border-bottom: 1px solid #f1f5f9; transition: background 0.2s;">
                        <td style="padding: 12px; font-weight: 500; color: #1e293b;">
                            <div style="display: flex; align-items: center;">
                                <div style="width: 8px; height: 8px; background: #10b981; border-radius: 50%; margin-right: 8px;"></div>
                                ${item.medicine_name}
                            </div>
                        </td>
                        <td style="padding: 12px; color: #64748b; font-size: 0.9rem; font-family: monospace;">
                            ${item.batch_number}
                        </td>
                        <td style="padding: 12px; text-align: center;">
                            <div style="font-weight: 600; color: #1e293b;">${item.quantity.toLocaleString()}</div>
                            <div style="color: #64748b; font-size: 0.8rem;">${item.unit}</div>
                        </td>
                        <td style="padding: 12px; color: #64748b; font-size: 0.9rem;">
                            ${item.location}
                        </td>
                        <td style="padding: 12px; text-align: center;">
                            <div style="width: 60px; height: 6px; background: #e2e8f0; border-radius: 3px; margin: 0 auto 4px;">
                                <div style="width: ${stockPercentage}%; height: 100%; background: ${stockColor}; border-radius: 3px;"></div>
                            </div>
                            <div style="font-size: 0.8rem; color: #64748b;">${Math.round(stockPercentage)}%</div>
                        </td>
                        <td style="padding: 12px; text-align: center;">
                            <div style="color: ${expiryColor}; font-weight: 500; font-size: 0.9rem;">
                                ${item.expiry_date}
                            </div>
                            <div style="color: #64748b; font-size: 0.8rem;">${daysUntilExpiry} days</div>
                        </td>
                        <td style="padding: 12px; text-align: center;">
                            <span style="background: ${statusColor === '#10b981' ? '#f0fdf4' : (statusColor === '#f59e0b' ? '#fef3c7' : '#fef2f2')}; color: ${statusColor}; padding: 4px 8px; border-radius: 12px; font-size: 0.8rem; font-weight: 500;">
                                ${item.status.replace('_', ' ').toUpperCase()}
                            </span>
                        </td>
                        <?php if ($canEdit || $canAdd || $canDelete): ?>
                        <td style="padding: 12px; text-align: center;">
                            <div style="display: flex; gap: 4px; justify-content: center;">
                                <?php if ($canEdit): ?>
                            <button class="btn btn-sm btn-outline-primary" onclick="editStock(${item.id})"
                                    style="border: 1px solid #3b82f6; padding: 4px 8px; border-radius: 4px; font-size: 0.75rem; background: white; color: #3b82f6;">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <?php endif; ?>
                                <?php if ($canAdd): ?>
                                <button class="btn btn-sm btn-outline-success" onclick="addStockQuantity(${item.id})"
                                    style="border: 1px solid #10b981; padding: 4px 8px; border-radius: 4px; font-size: 0.75rem; background: white; color: #10b981;">
                                    <i class="fas fa-plus"></i>
                                </button>
                                <?php endif; ?>
                                <?php if ($canEdit): ?>
                                <button class="btn btn-sm btn-outline-warning" onclick="transferStockItem(${item.id})"
                                    style="border: 1px solid #f59e0b; padding: 4px 8px; border-radius: 4px; font-size: 0.75rem; background: white; color: #f59e0b;">
                                    <i class="fas fa-exchange-alt"></i>
                                </button>
                                <?php endif; ?>
                                <button class="btn btn-sm btn-outline-info" onclick="viewStockDetails(${item.id})"
                                    style="border: 1px solid #06b6d4; padding: 4px 8px; border-radius: 4px; font-size: 0.75rem; background: white; color: #06b6d4;">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <?php if ($canDelete): ?>
                                <button class="btn btn-sm btn-outline-danger" onclick="deleteStock(${item.id})"
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
        const endIndex = Math.min(currentPage * itemsPerPage, filteredData.length);
        paginationInfo.text(`Showing ${startIndex} to ${endIndex} of ${filteredData.length} entries`);

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
            refreshStockTable();
        }
    }

    function addStock() {
        currentStockId = null;
        const modal = new bootstrap.Modal(document.getElementById('addStockModal'));
        modal.show();
    }

    function editStock(id) {
        const stock = stockData.find(s => s.id == id);

        if (stock) {
            currentStockId = id;
            $('#editStockId').val(stock.id);
            $('#editMedicineName').val(stock.medicine_name);
            $('#editBatchNumber').val(stock.batch_number);
            $('#editQuantity').val(stock.quantity);
            $('#editLocation').val(stock.location_id);
            $('#editExpiryDate').val(stock.expiry_date);
            $('#editMinStock').val(stock.min_stock);
            $('#editMaxStock').val(stock.max_stock);
            $('#editPurchasePrice').val(stock.purchase_price);

            const modal = new bootstrap.Modal(document.getElementById('editStockModal'));
            modal.show();
        }
    }

    function addStockQuantity(id) {
        const stock = stockData.find(s => s.id == id);

        if (stock) {
            currentStockId = id;
            $('#addQuantityStockId').val(stock.id);
            $('#currentQuantity').val(stock.quantity + ' ' + stock.unit);
            $('#addQuantity').val('');

            const modal = new bootstrap.Modal(document.getElementById('addQuantityModal'));
            modal.show();
        }
    }

    function transferStockItem(id) {
        const stock = stockData.find(s => s.id == id);

        if (stock) {
            currentStockId = id;
            $('#transferStockId').val(stock.id);
            $('#transferMedicineName').val(stock.medicine_name);
            $('#transferCurrentLocation').val(stock.location);
            $('#transferQuantity').val('');
            $('#transferToLocation').val('');

            const modal = new bootstrap.Modal(document.getElementById('transferStockModal'));
            modal.show();
        }
    }

    function viewStockDetails(id) {
        $.ajax({
            url: '<?= Yii::$app->urlManager->createUrl(['inventory/get-stock-details']) ?>',
            type: 'GET',
            data: {
                id: id
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    const stock = response.data.stock;
                    const expiryDate = new Date(stock.expiry_date);
                    const now = new Date();
                    const daysUntilExpiry = Math.ceil((expiryDate - now) / (1000 * 60 * 60 * 24));
                    const stockPercentage = (stock.quantity / stock.max_stock) * 100;

                    const html = `
                    <div class="row g-3">
                        <!-- Basic Information -->
                        <div class="col-lg-4 col-md-6">
                            <div style="background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); border-radius: 12px; padding: 16px; border: 1px solid #e2e8f0; height: 100%;">
                                <h6 style="color: #374151; font-weight: 600; margin-bottom: 12px; display: flex; align-items: center; font-size: 0.9rem;">
                                    <i class="fas fa-info-circle me-2" style="color: #6366f1; font-size: 0.8rem;"></i>Basic Information
                                </h6>
                                <div style="space-y: 8px;">
                                    <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                                        <span style="font-weight: 600; color: #374151; font-size: 0.8rem;">Medicine:</span>
                                        <span style="color: #6b7280; font-size: 0.8rem; text-align: right; max-width: 60%;">${stock.medicine_name}</span>
                                    </div>
                                    <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                                        <span style="font-weight: 600; color: #374151; font-size: 0.8rem;">Batch:</span>
                                        <span style="color: #6b7280; font-size: 0.8rem; font-family: monospace;">${stock.batch_number}</span>
                                    </div>
                                    <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                                        <span style="font-weight: 600; color: #374151; font-size: 0.8rem;">Quantity:</span>
                                        <span style="color: #6b7280; font-size: 0.8rem;">${stock.quantity} ${stock.unit}</span>
                                    </div>
                                    <div style="display: flex; justify-content: space-between;">
                                        <span style="font-weight: 600; color: #374151; font-size: 0.8rem;">Location:</span>
                                        <span style="color: #6b7280; font-size: 0.8rem; text-align: right; max-width: 60%;">${stock.location_name}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Stock Status -->
                        <div class="col-lg-4 col-md-6">
                            <div style="background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); border-radius: 12px; padding: 16px; border: 1px solid #e2e8f0; height: 100%;">
                                <h6 style="color: #374151; font-weight: 600; margin-bottom: 12px; display: flex; align-items: center; font-size: 0.9rem;">
                                    <i class="fas fa-warehouse me-2" style="color: #6366f1; font-size: 0.8rem;"></i>Stock Status
                                </h6>
                                <div style="space-y: 8px;">
                                    <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                                        <span style="font-weight: 600; color: #374151; font-size: 0.8rem;">Status:</span>
                                        <span class="badge bg-${stock.status === 'in_stock' ? 'success' : stock.status === 'low_stock' ? 'warning' : 'danger'}" style="font-size: 0.7rem;">${stock.status.replace('_', ' ').toUpperCase()}</span>
                                    </div>
                                    <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                                        <span style="font-weight: 600; color: #374151; font-size: 0.8rem;">Level:</span>
                                        <span style="color: #6b7280; font-size: 0.8rem;">${Math.round(stockPercentage)}%</span>
                                    </div>
                                    <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                                        <span style="font-weight: 600; color: #374151; font-size: 0.8rem;">Min Stock:</span>
                                        <span style="color: #6b7280; font-size: 0.8rem;">${stock.min_stock}</span>
                                    </div>
                                    <div style="display: flex; justify-content: space-between;">
                                        <span style="font-weight: 600; color: #374151; font-size: 0.8rem;">Max Stock:</span>
                                        <span style="color: #6b7280; font-size: 0.8rem;">${stock.max_stock}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Expiry Information -->
                        <div class="col-lg-4 col-md-6">
                            <div style="background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); border-radius: 12px; padding: 16px; border: 1px solid #e2e8f0; height: 100%;">
                                <h6 style="color: #374151; font-weight: 600; margin-bottom: 12px; display: flex; align-items: center; font-size: 0.9rem;">
                                    <i class="fas fa-clock me-2" style="color: #6366f1; font-size: 0.8rem;"></i>Expiry Information
                                </h6>
                                <div style="space-y: 8px;">
                                    <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                                        <span style="font-weight: 600; color: #374151; font-size: 0.8rem;">Expiry Date:</span>
                                        <span style="color: #6b7280; font-size: 0.8rem;">${stock.expiry_date}</span>
                                    </div>
                                    <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                                        <span style="font-weight: 600; color: #374151; font-size: 0.8rem;">Days Left:</span>
                                        <span class="text-${daysUntilExpiry <= 30 ? 'danger' : daysUntilExpiry <= 90 ? 'warning' : 'success'}" style="font-size: 0.8rem; font-weight: 600;">${daysUntilExpiry} days</span>
                                    </div>
                                    <div style="margin-top: 12px;">
                                        <div style="width: 100%; height: 8px; background: #e2e8f0; border-radius: 4px; overflow: hidden;">
                                            <div style="width: ${Math.min(100, Math.max(0, (90 - daysUntilExpiry) / 90 * 100))}%; height: 100%; background: ${daysUntilExpiry <= 30 ? '#ef4444' : daysUntilExpiry <= 90 ? '#f59e0b' : '#10b981'}; border-radius: 4px; transition: all 0.3s ease;"></div>
                                        </div>
                                        <div style="text-align: center; margin-top: 4px;">
                                            <small style="color: #6b7280; font-size: 0.7rem;">Expiry Progress</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Financial Information -->
                        <div class="col-lg-4 col-md-6">
                            <div style="background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); border-radius: 12px; padding: 16px; border: 1px solid #e2e8f0; height: 100%;">
                                <h6 style="color: #374151; font-weight: 600; margin-bottom: 12px; display: flex; align-items: center; font-size: 0.9rem;">
                                    <i class="fas fa-dollar-sign me-2" style="color: #6366f1; font-size: 0.8rem;"></i>Financial Info
                                </h6>
                                <div style="space-y: 8px;">
                                    <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                                        <span style="font-weight: 600; color: #374151; font-size: 0.8rem;">Purchase:</span>
                                        <span style="color: #6b7280; font-size: 0.8rem;">$${parseFloat(stock.purchase_price || 0).toFixed(2)}</span>
                                    </div>
                                    <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                                        <span style="font-weight: 600; color: #374151; font-size: 0.8rem;">Selling:</span>
                                        <span style="color: #6b7280; font-size: 0.8rem;">$${parseFloat(stock.selling_price || 0).toFixed(2)}</span>
                                    </div>
                                    <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                                        <span style="font-weight: 600; color: #374151; font-size: 0.8rem;">Supplier:</span>
                                        <span style="color: #6b7280; font-size: 0.8rem; text-align: right; max-width: 60%;">${stock.supplier_name || 'N/A'}</span>
                                    </div>
                                    <div style="display: flex; justify-content: space-between;">
                                        <span style="font-weight: 600; color: #374151; font-size: 0.8rem;">Updated:</span>
                                        <span style="color: #6b7280; font-size: 0.8rem;">${stock.last_updated || 'N/A'}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Stock Level Visualization -->
                        <div class="col-lg-8 col-md-12">
                            <div style="background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); border-radius: 12px; padding: 16px; border: 1px solid #e2e8f0;">
                                <h6 style="color: #374151; font-weight: 600; margin-bottom: 12px; display: flex; align-items: center; font-size: 0.9rem;">
                                    <i class="fas fa-chart-bar me-2" style="color: #6366f1; font-size: 0.8rem;"></i>Stock Level Visualization
                                </h6>
                                <div style="display: flex; align-items: center; gap: 16px;">
                                    <div style="flex: 1;">
                                        <div class="progress" style="height: 24px; border-radius: 12px; background: #e2e8f0;">
                                            <div class="progress-bar ${stockPercentage > 70 ? 'bg-success' : stockPercentage > 30 ? 'bg-warning' : 'bg-danger'}" 
                                                 role="progressbar" style="width: ${stockPercentage}%; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 0.8rem;" 
                                                 aria-valuenow="${stockPercentage}" aria-valuemin="0" aria-valuemax="100">
                                                ${Math.round(stockPercentage)}%
                                            </div>
                                        </div>
                                    </div>
                                    <div style="text-align: center; min-width: 80px;">
                                        <div style="font-size: 1.2rem; font-weight: 700; color: #374151;">${stock.quantity}</div>
                                        <div style="font-size: 0.7rem; color: #6b7280;">${stock.unit}</div>
                                    </div>
                                </div>
                                <div style="display: flex; justify-content: space-between; margin-top: 8px; font-size: 0.7rem; color: #6b7280;">
                                    <span>Min: ${stock.min_stock}</span>
                                    <span>Max: ${stock.max_stock}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                `;

                    $('#stockDetailsBody').html(html);
                    const modal = new bootstrap.Modal(document.getElementById('stockDetailsModal'));
                    modal.show();
                } else {
                    showNotification(response.message || 'Error loading stock details', 'error');
                }
            },
            error: function() {
                showNotification('Error loading stock details', 'error');
            }
        });
    }

    // Update the viewStockDetails function to use the new getStockDetails function
    function viewStockDetails(id) {
        currentStockId = id;
        getStockDetails(id);
    }

    // Add real-time data refresh functionality
    function refreshStockData() {
        $.ajax({
            url: '<?= Yii::$app->urlManager->createUrl(['inventory/stockmanagement']) ?>',
            type: 'GET',
            success: function(response) {
                // Update the stock data and refresh the table
                location.reload();
            },
            error: function() {
                showNotification('Error refreshing data', 'error');
            }
        });
    }

    // Manual refresh function (removed auto-refresh to prevent errors)
    function manualRefresh() {
        refreshStockData();
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

    function saveStock() {
        const form = $('#addStockForm')[0];

        if (form.checkValidity()) {
            const formData = new FormData(form);

            // Show loading state
            const saveBtn = $('button[onclick="saveStock()"]');
            const originalText = saveBtn.html();
            saveBtn.html('<i class="fas fa-spinner fa-spin"></i> Saving...').prop('disabled', true);

            $.ajax({
                url: '<?= Yii::$app->urlManager->createUrl(['inventory/add-stock']) ?>',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        showNotification('Stock added successfully', 'success');
                        const modal = bootstrap.Modal.getInstance(document.getElementById('addStockModal'));
                        modal.hide();
                        // Reload the page to get updated data
                        location.reload();
                    } else {
                        showNotification(response.message || 'Error adding stock', 'error');
                    }
                },
                error: function() {
                    showNotification('Error adding stock', 'error');
                },
                complete: function() {
                    saveBtn.html(originalText).prop('disabled', false);
                }
            });
        } else {
            form.reportValidity();
        }
    }

    function updateStock() {
        const form = $('#editStockForm')[0];

        if (form.checkValidity()) {
            const formData = new FormData(form);

            // Show loading state
            const updateBtn = $('button[onclick="updateStock()"]');
            const originalText = updateBtn.html();
            updateBtn.html('<i class="fas fa-spinner fa-spin"></i> Updating...').prop('disabled', true);

            $.ajax({
                url: '<?= Yii::$app->urlManager->createUrl(['inventory/update-stock']) ?>',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        showNotification('Stock updated successfully', 'success');
                        const modal = bootstrap.Modal.getInstance(document.getElementById('editStockModal'));
                        modal.hide();
                        // Reload the page to get updated data
                        location.reload();
                    } else {
                        showNotification(response.message || 'Error updating stock', 'error');
                    }
                },
                error: function() {
                    showNotification('Error updating stock', 'error');
                },
                complete: function() {
                    updateBtn.html(originalText).prop('disabled', false);
                }
            });
        } else {
            form.reportValidity();
        }
    }

    function confirmAddQuantity() {
        const form = $('#addQuantityForm')[0];

        if (form.checkValidity()) {
            const formData = new FormData(form);

            // Show loading state
            const addBtn = $('button[onclick="confirmAddQuantity()"]');
            const originalText = addBtn.html();
            addBtn.html('<i class="fas fa-spinner fa-spin"></i> Adding...').prop('disabled', true);

            $.ajax({
                url: '<?= Yii::$app->urlManager->createUrl(['inventory/add-stock-quantity']) ?>',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        showNotification(`Added ${$('#addQuantity').val()} units successfully`, 'success');
                        const modal = bootstrap.Modal.getInstance(document.getElementById('addQuantityModal'));
                        modal.hide();
                        // Reload the page to get updated data
                        location.reload();
                    } else {
                        showNotification(response.message || 'Error adding quantity', 'error');
                    }
                },
                error: function() {
                    showNotification('Error adding quantity', 'error');
                },
                complete: function() {
                    addBtn.html(originalText).prop('disabled', false);
                }
            });
        } else {
            form.reportValidity();
        }
    }

    function confirmTransfer() {
        const form = $('#transferStockForm')[0];

        if (form.checkValidity()) {
            const formData = new FormData(form);

            // Show loading state
            const transferBtn = $('button[onclick="confirmTransfer()"]');
            const originalText = transferBtn.html();
            transferBtn.html('<i class="fas fa-spinner fa-spin"></i> Transferring...').prop('disabled', true);

            $.ajax({
                url: '<?= Yii::$app->urlManager->createUrl(['inventory/transfer-stock']) ?>',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        showNotification(
                            `Transferred ${$('#transferQuantity').val()} units to ${$('#transferToLocation').val()}`,
                            'success');
                        const modal = bootstrap.Modal.getInstance(document.getElementById(
                            'transferStockModal'));
                        modal.hide();
                        // Reload the page to get updated data
                        location.reload();
                    } else {
                        showNotification(response.message || 'Error transferring stock', 'error');
                    }
                },
                error: function() {
                    showNotification('Error transferring stock', 'error');
                },
                complete: function() {
                    transferBtn.html(originalText).prop('disabled', false);
                }
            });
        } else {
            form.reportValidity();
        }
    }

    function deleteStock(id) {
        const stock = stockData.find(s => s.id == id);

        if (stock) {
            currentStockId = id;
            $('#deleteStockInfo').html(`
                <div class="alert alert-warning" style="background: #fef3c7; border: 1px solid #f59e0b; color: #92400e; border-radius: 8px; padding: 16px;">
                    <div style="display: flex; align-items: center;">
                        <i class="fas fa-boxes me-2" style="font-size: 1.2rem;"></i>
                        <div>
                            <strong>${stock.medicine_name}</strong><br>
                            <small>Batch: ${stock.batch_number} - Quantity: ${stock.quantity} ${stock.unit}</small><br>
                            <small>Location: ${stock.location}</small>
                        </div>
                    </div>
                </div>
            `);

            const modal = new bootstrap.Modal(document.getElementById('deleteStockModal'));
            modal.show();
        }
    }

    function confirmDeleteStock() {
        if (currentStockId) {
            $.ajax({
                url: '<?= Yii::$app->urlManager->createUrl(['inventory/delete-stock']) ?>',
                type: 'POST',
                data: {
                    id: currentStockId
                },
                success: function(response) {
                    if (response.success) {
                        // Remove from data array
                        stockData = stockData.filter(s => s.id != currentStockId);
                        filteredData = filteredData.filter(s => s.id != currentStockId);

                        // Update stats and refresh table
                        updateStats();
                        refreshStockTable();

                        showNotification('Stock deleted successfully', 'success');

                        const modal = bootstrap.Modal.getInstance(document.getElementById('deleteStockModal'));
                        modal.hide();
                        currentStockId = null;
                    } else {
                        showNotification(response.message || 'Error deleting stock', 'error');
                    }
                },
                error: function() {
                    showNotification('Error deleting stock', 'error');
                }
            });
        }
    }

    function getStockDetails(id) {
        $.ajax({
            url: '<?= Yii::$app->urlManager->createUrl(['inventory/get-stock-details']) ?>',
            type: 'GET',
            data: {
                id: id
            },
            success: function(response) {
                if (response.success) {
                    const stock = response.data.stock;
                    const expiryDate = new Date(stock.expiry_date);
                    const now = new Date();
                    const daysUntilExpiry = Math.ceil((expiryDate - now) / (1000 * 60 * 60 * 24));
                    const stockPercentage = (stock.quantity / stock.max_stock) * 100;

                    const html = `
                    <div class="row g-3">
                        <!-- Basic Information -->
                        <div class="col-lg-4 col-md-6">
                            <div style="background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); border-radius: 12px; padding: 16px; border: 1px solid #e2e8f0; height: 100%;">
                                <h6 style="color: #374151; font-weight: 600; margin-bottom: 12px; display: flex; align-items: center; font-size: 0.9rem;">
                                    <i class="fas fa-info-circle me-2" style="color: #6366f1; font-size: 0.8rem;"></i>Basic Information
                                </h6>
                                <div style="space-y: 8px;">
                                    <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                                        <span style="font-weight: 600; color: #374151; font-size: 0.8rem;">Medicine:</span>
                                        <span style="color: #6b7280; font-size: 0.8rem; text-align: right; max-width: 60%;">${stock.medicine_name}</span>
                                    </div>
                                    <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                                        <span style="font-weight: 600; color: #374151; font-size: 0.8rem;">Batch:</span>
                                        <span style="color: #6b7280; font-size: 0.8rem; font-family: monospace;">${stock.batch_number}</span>
                                    </div>
                                    <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                                        <span style="font-weight: 600; color: #374151; font-size: 0.8rem;">Quantity:</span>
                                        <span style="color: #6b7280; font-size: 0.8rem;">${stock.quantity} ${stock.unit}</span>
                                    </div>
                                    <div style="display: flex; justify-content: space-between;">
                                        <span style="font-weight: 600; color: #374151; font-size: 0.8rem;">Location:</span>
                                        <span style="color: #6b7280; font-size: 0.8rem; text-align: right; max-width: 60%;">${stock.location_name}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Stock Status -->
                        <div class="col-lg-4 col-md-6">
                            <div style="background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); border-radius: 12px; padding: 16px; border: 1px solid #e2e8f0; height: 100%;">
                                <h6 style="color: #374151; font-weight: 600; margin-bottom: 12px; display: flex; align-items: center; font-size: 0.9rem;">
                                    <i class="fas fa-warehouse me-2" style="color: #6366f1; font-size: 0.8rem;"></i>Stock Status
                                </h6>
                                <div style="space-y: 8px;">
                                    <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                                        <span style="font-weight: 600; color: #374151; font-size: 0.8rem;">Status:</span>
                                        <span class="badge bg-${stock.status === 'in_stock' ? 'success' : stock.status === 'low_stock' ? 'warning' : 'danger'}" style="font-size: 0.7rem;">${stock.status.replace('_', ' ').toUpperCase()}</span>
                                    </div>
                                    <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                                        <span style="font-weight: 600; color: #374151; font-size: 0.8rem;">Level:</span>
                                        <span style="color: #6b7280; font-size: 0.8rem;">${Math.round(stockPercentage)}%</span>
                                    </div>
                                    <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                                        <span style="font-weight: 600; color: #374151; font-size: 0.8rem;">Min Stock:</span>
                                        <span style="color: #6b7280; font-size: 0.8rem;">${stock.min_stock}</span>
                                    </div>
                                    <div style="display: flex; justify-content: space-between;">
                                        <span style="font-weight: 600; color: #374151; font-size: 0.8rem;">Max Stock:</span>
                                        <span style="color: #6b7280; font-size: 0.8rem;">${stock.max_stock}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Expiry Information -->
                        <div class="col-lg-4 col-md-6">
                            <div style="background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); border-radius: 12px; padding: 16px; border: 1px solid #e2e8f0; height: 100%;">
                                <h6 style="color: #374151; font-weight: 600; margin-bottom: 12px; display: flex; align-items: center; font-size: 0.9rem;">
                                    <i class="fas fa-clock me-2" style="color: #6366f1; font-size: 0.8rem;"></i>Expiry Information
                                </h6>
                                <div style="space-y: 8px;">
                                    <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                                        <span style="font-weight: 600; color: #374151; font-size: 0.8rem;">Expiry Date:</span>
                                        <span style="color: #6b7280; font-size: 0.8rem;">${stock.expiry_date}</span>
                                    </div>
                                    <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                                        <span style="font-weight: 600; color: #374151; font-size: 0.8rem;">Days Left:</span>
                                        <span class="text-${daysUntilExpiry <= 30 ? 'danger' : daysUntilExpiry <= 90 ? 'warning' : 'success'}" style="font-size: 0.8rem; font-weight: 600;">${daysUntilExpiry} days</span>
                                    </div>
                                    <div style="margin-top: 12px;">
                                        <div style="width: 100%; height: 8px; background: #e2e8f0; border-radius: 4px; overflow: hidden;">
                                            <div style="width: ${Math.min(100, Math.max(0, (90 - daysUntilExpiry) / 90 * 100))}%; height: 100%; background: ${daysUntilExpiry <= 30 ? '#ef4444' : daysUntilExpiry <= 90 ? '#f59e0b' : '#10b981'}; border-radius: 4px; transition: all 0.3s ease;"></div>
                                        </div>
                                        <div style="text-align: center; margin-top: 4px;">
                                            <small style="color: #6b7280; font-size: 0.7rem;">Expiry Progress</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Financial Information -->
                        <div class="col-lg-4 col-md-6">
                            <div style="background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); border-radius: 12px; padding: 16px; border: 1px solid #e2e8f0; height: 100%;">
                                <h6 style="color: #374151; font-weight: 600; margin-bottom: 12px; display: flex; align-items: center; font-size: 0.9rem;">
                                    <i class="fas fa-dollar-sign me-2" style="color: #6366f1; font-size: 0.8rem;"></i>Financial Info
                                </h6>
                                <div style="space-y: 8px;">
                                    <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                                        <span style="font-weight: 600; color: #374151; font-size: 0.8rem;">Purchase:</span>
                                        <span style="color: #6b7280; font-size: 0.8rem;">$${parseFloat(stock.purchase_price || 0).toFixed(2)}</span>
                                    </div>
                                    <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                                        <span style="font-weight: 600; color: #374151; font-size: 0.8rem;">Selling:</span>
                                        <span style="color: #6b7280; font-size: 0.8rem;">$${parseFloat(stock.selling_price || 0).toFixed(2)}</span>
                                    </div>
                                    <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                                        <span style="font-weight: 600; color: #374151; font-size: 0.8rem;">Supplier:</span>
                                        <span style="color: #6b7280; font-size: 0.8rem; text-align: right; max-width: 60%;">${stock.supplier_name || 'N/A'}</span>
                                    </div>
                                    <div style="display: flex; justify-content: space-between;">
                                        <span style="font-weight: 600; color: #374151; font-size: 0.8rem;">Updated:</span>
                                        <span style="color: #6b7280; font-size: 0.8rem;">${stock.last_updated || 'N/A'}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Stock Level Visualization -->
                        <div class="col-lg-8 col-md-12">
                            <div style="background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); border-radius: 12px; padding: 16px; border: 1px solid #e2e8f0;">
                                <h6 style="color: #374151; font-weight: 600; margin-bottom: 12px; display: flex; align-items: center; font-size: 0.9rem;">
                                    <i class="fas fa-chart-bar me-2" style="color: #6366f1; font-size: 0.8rem;"></i>Stock Level Visualization
                                </h6>
                                <div style="display: flex; align-items: center; gap: 16px;">
                                    <div style="flex: 1;">
                                        <div class="progress" style="height: 24px; border-radius: 12px; background: #e2e8f0;">
                                            <div class="progress-bar ${stockPercentage > 70 ? 'bg-success' : stockPercentage > 30 ? 'bg-warning' : 'bg-danger'}" 
                                                 role="progressbar" style="width: ${stockPercentage}%; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 0.8rem;" 
                                                 aria-valuenow="${stockPercentage}" aria-valuemin="0" aria-valuemax="100">
                                                ${Math.round(stockPercentage)}%
                                            </div>
                                        </div>
                                    </div>
                                    <div style="text-align: center; min-width: 80px;">
                                        <div style="font-size: 1.2rem; font-weight: 700; color: #374151;">${stock.quantity}</div>
                                        <div style="font-size: 0.7rem; color: #6b7280;">${stock.unit}</div>
                                    </div>
                                </div>
                                <div style="display: flex; justify-content: space-between; margin-top: 8px; font-size: 0.7rem; color: #6b7280;">
                                    <span>Min: ${stock.min_stock}</span>
                                    <span>Max: ${stock.max_stock}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                `;

                    $('#stockDetailsBody').html(html);
                    const modal = new bootstrap.Modal(document.getElementById('stockDetailsModal'));
                    modal.show();
                } else {
                    showNotification(response.message || 'Error loading stock details', 'error');
                }
            },
            error: function() {
                showNotification('Error loading stock details', 'error');
            }
        });
    }

    // Update the viewStockDetails function to use the new getStockDetails function
    function viewStockDetails(id) {
        currentStockId = id;
        getStockDetails(id);
    }

    // Add real-time data refresh functionality
    function refreshStockData() {
        $.ajax({
            url: '<?= Yii::$app->urlManager->createUrl(['inventory/stockmanagement']) ?>',
            type: 'GET',
            success: function(response) {
                // Update the stock data and refresh the table
                location.reload();
            },
            error: function() {
                showNotification('Error refreshing data', 'error');
            }
        });
    }

    // Manual refresh function (removed auto-refresh to prevent errors)
    function manualRefresh() {
        refreshStockData();
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

    function exportStock() {
        // Create CSV content
        let csvContent = "Medicine,Batch Number,Quantity,Unit,Location,Stock Level,Expiry Date,Status,Supplier\n";

        filteredData.forEach(stock => {
            const stockPercentage = (stock.quantity / stock.max_stock) * 100;
            csvContent +=
                `"${stock.medicine_name}","${stock.batch_number}","${stock.quantity}","${stock.unit}","${stock.location}","${Math.round(stockPercentage)}%","${stock.expiry_date}","${stock.status}","${stock.supplier_name}"\n`;
        });

        // Download CSV
        const blob = new Blob([csvContent], {
            type: 'text/csv'
        });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'stock_inventory.csv';
        a.click();
        window.URL.revokeObjectURL(url);

        showNotification('Stock data exported successfully', 'success');
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
        $('#colQuantity').on('change', function() {
            toggleColumn(2, this.checked);
        });
        $('#colLocation').on('change', function() {
            toggleColumn(3, this.checked);
        });
        $('#colStockLevel').on('change', function() {
            toggleColumn(4, this.checked);
        });
        $('#colExpiry').on('change', function() {
            toggleColumn(5, this.checked);
        });
        $('#colStatus').on('change', function() {
            toggleColumn(6, this.checked);
        });
    }

    function toggleColumn(columnIndex, visible) {
        const table = $('#medicinesTable');
        table.find('th').eq(columnIndex).toggle(visible);
        table.find('td:nth-child(' + (columnIndex + 1) + ')').toggle(visible);
    }

    function applyColumnVisibility() {
        const modal = bootstrap.Modal.getInstance(document.getElementById('columnModal'));
        modal.hide();
        showNotification('Column visibility updated', 'success');
    }

    function editStockFromDetails() {
        const modal = bootstrap.Modal.getInstance(document.getElementById('stockDetailsModal'));
        modal.hide();
        if (currentStockId) {
            editStock(currentStockId);
        }
    }

    function transferStock() {
        showNotification('Bulk transfer functionality coming soon', 'info');
    }
</script>
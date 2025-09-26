<?php
// Fix the categories data structure
$categories = isset($categories) ? $categories : [];

use app\components\SystemComponents;

$systemComponents = new SystemComponents();
$can = $systemComponents->checkModulePermission('25,33');
$canEdit = $can['canEdit'];
$canExport = $can['canExport'];
$canDelete = $can['canDelete'];
$canAdd = $can['canAdd'];
?>
<div class="purchase-orders" style="min-height: 95vh;margin: -25px;margin-bottom: 24px;">
    <!-- Header Section -->
    <div class="page-header" style="margin-bottom: 24px;">
        <div class="row">
            <div class="col-md-6">
                <h1 style="color: #1e293b; font-size: 1.8rem; font-weight: 700; margin-bottom: 8px;">
                    <i class="fas fa-shopping-cart" style="color: #6366f1; margin-right: 12px;"></i>
                    Purchase Orders
                </h1>
                <p style="color: #64748b; font-size: 0.95rem; margin: 0;">
                    Manage purchase orders, track deliveries, and monitor supplier relationships
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
                    <button class="btn btn-sm btn-outline-secondary" onclick="exportOrders()"
                        style="border: 1px solid #e2e8f0; padding: 6px 12px; border-radius: 6px; font-size: 0.8rem; background: white; color: #64748b;">
                        <i class="fas fa-download"></i> Export
                    </button>
                    <?php endif; ?>
                    <?php if ($canAdd): ?>
                    <button class="btn btn-primary" onclick="showCreateOrderModal()"
                        style="background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); border: none; padding: 8px 16px; border-radius: 8px; font-size: 0.85rem; font-weight: 500; box-shadow: 0 2px 8px rgba(99, 102, 241, 0.25);">
                        <i class="fas fa-plus"></i> Create Order
                    </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="orders-summary" style="margin-bottom: 20px;">
        <div class="row g-2">
            <!-- Card 1 - Total Orders -->
            <div class="col-lg-3 col-md-6">
                <div class="summary-card h-100"
                    style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 12px; padding: 12px; box-shadow: 0 4px 16px rgba(102, 126, 234, 0.25); border: none; position: relative; overflow: hidden;">
                    <div style="display: flex; align-items: center; font-size: 1.4rem; font-weight: 700; color: white;">
                        <i class="fas fa-file-invoice" style="font-size: 1.2rem; opacity: 0.9; margin-right: 8px;"></i>
                        <span id="totalOrders"><?= count($purchaseOrders) ?></span>
                        <small style="font-size: 0.7rem; font-weight: 500; margin: 0 8px;">Orders</small>
                        <div
                            style="margin-left: auto; background: rgba(255,255,255,0.2); padding: 2px 6px; border-radius: 8px; backdrop-filter: blur(10px); font-size: 0.6rem; font-weight: 600;">
                            TOTAL
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 2 - Completed Orders -->
            <div class="col-lg-3 col-md-6">
                <div class="summary-card h-100"
                    style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 12px; padding: 12px; box-shadow: 0 4px 16px rgba(16, 185, 129, 0.25); border: none; position: relative; overflow: hidden;">
                    <div style="display: flex; align-items: center; font-size: 1.4rem; font-weight: 700; color: white;">
                        <i class="fas fa-check-circle" style="font-size: 1.2rem; opacity: 0.9; margin-right: 8px;"></i>
                        <span id="completedOrders"><?= count(array_filter($purchaseOrders, function ($o) {
                                                        return $o['status'] === 'delivered';
                                                    })) ?></span>
                        <small style="font-size: 0.7rem; font-weight: 500; margin: 0 8px;">Completed</small>
                        <div
                            style="margin-left: auto; background: rgba(255,255,255,0.2); padding: 2px 6px; border-radius: 8px; backdrop-filter: blur(10px); font-size: 0.6rem; font-weight: 600;">
                            DONE
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 3 - Pending Orders -->
            <div class="col-lg-3 col-md-6">
                <div class="summary-card h-100"
                    style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); border-radius: 12px; padding: 12px; box-shadow: 0 4px 16px rgba(245, 158, 11, 0.25); border: none; position: relative; overflow: hidden;">
                    <div style="display: flex; align-items: center; font-size: 1.4rem; font-weight: 700; color: white;">
                        <i class="fas fa-clock" style="font-size: 1.2rem; opacity: 0.9; margin-right: 8px;"></i>
                        <span id="pendingOrders"><?= count(array_filter($purchaseOrders, function ($o) {
                                                        return $o['status'] === 'pending';
                                                    })) ?></span>
                        <small style="font-size: 0.7rem; font-weight: 500; margin: 0 8px;">Pending</small>
                        <div
                            style="margin-left: auto; background: rgba(255,255,255,0.2); padding: 2px 6px; border-radius: 8px; backdrop-filter: blur(10px); font-size: 0.6rem; font-weight: 600;">
                            WAITING
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 4 - Cancelled Orders -->
            <div class="col-lg-3 col-md-6">
                <div class="summary-card h-100"
                    style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); border-radius: 12px; padding: 12px; box-shadow: 0 4px 16px rgba(239, 68, 68, 0.25); border: none; position: relative; overflow: hidden;">
                    <div style="display: flex; align-items: center; font-size: 1.4rem; font-weight: 700; color: white;">
                        <i class="fas fa-times-circle" style="font-size: 1.2rem; opacity: 0.9; margin-right: 8px;"></i>
                        <span id="cancelledOrders"><?= count(array_filter($purchaseOrders, function ($o) {
                                                        return $o['status'] === 'cancelled';
                                                    })) ?></span>
                        <small style="font-size: 0.7rem; font-weight: 500; margin: 0 8px;">Cancelled</small>
                        <div
                            style="margin-left: auto; background: rgba(255,255,255,0.2); padding: 2px 6px; border-radius: 8px; backdrop-filter: blur(10px); font-size: 0.6rem; font-weight: 600;">
                            CANCELLED
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="orders-table-container"
        style="margin-top: -17px;margin-bottom: 24px; background: white; border-radius: 12px; padding: 5px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e2e8f0; display: flex; flex-direction: column; height: 60vh;">

        <!-- Filter Section -->
        <div class="filter-section"
            style="padding: 12px; border-bottom: 1px solid #e2e8f0; background: #f8fafc; border-radius: 8px 8px 0 0;">
            <div class="row g-2">
                <div class="col-md-3">
                    <div class="form-group mb-0">
                        <label for="searchOrders"
                            style="font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 0.75rem;">Search
                            Orders</label>
                        <input type="text" class="form-control" id="searchOrders"
                            placeholder="Search by order number, supplier..."
                            style="border: 1px solid #d1d5db; border-radius: 4px; padding: 6px 8px; font-size: 0.75rem; height: 32px;">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group mb-0">
                        <label for="statusFilter"
                            style="font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 0.75rem;">Status</label>
                        <select class="form-control" id="statusFilter"
                            style="border: 1px solid #d1d5db; border-radius: 4px; padding: 6px 8px; font-size: 0.75rem; height: 32px;">
                            <option value="">All Status</option>
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="ordered">Ordered</option>
                            <option value="delivered">Delivered</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group mb-0">
                        <label for="supplierFilter"
                            style="font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 0.75rem;">Supplier</label>
                        <select class="form-control" id="supplierFilter"
                            style="border: 1px solid #d1d5db; border-radius: 4px; padding: 6px 8px; font-size: 0.75rem; height: 32px;">
                            <option value="">All Suppliers</option>
                            <?php foreach (array_unique(array_column($purchaseOrders, 'supplier_name')) as $supplier): ?>
                            <option value="<?= htmlspecialchars($supplier) ?>"><?= htmlspecialchars($supplier) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group mb-0">
                        <label for="dateFilter"
                            style="font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 0.75rem;">Date
                            Range</label>
                        <select class="form-control" id="dateFilter"
                            style="border: 1px solid #d1d5db; border-radius: 4px; padding: 6px 8px; font-size: 0.75rem; height: 32px;">
                            <option value="">All Time</option>
                            <option value="today">Today</option>
                            <option value="week">This Week</option>
                            <option value="month">This Month</option>
                            <option value="quarter">This Quarter</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group mb-0">
                        <label for="amountFilter"
                            style="font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 0.75rem;">Amount
                            Range</label>
                        <select class="form-control" id="amountFilter"
                            style="border: 1px solid #d1d5db; border-radius: 4px; padding: 6px 8px; font-size: 0.75rem; height: 32px;">
                            <option value="">All Amounts</option>
                            <option value="low">Under 100</option>
                            <option value="medium">100 - 500</option>
                            <option value="high">Over 500</option>
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
                <table class="table" id="ordersTable" style="width: 100%; border-collapse: collapse; margin: 0;">
                    <thead style="position: sticky; top: 0; z-index: 10; background: #f8fafc;">
                        <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                            <th
                                style="padding: 12px; text-align: left; font-weight: 600; color: #1e293b; font-size: 0.9rem;">
                                Order #</th>
                            <th
                                style="padding: 12px; text-align: left; font-weight: 600; color: #1e293b; font-size: 0.9rem;">
                                Supplier</th>
                            <th
                                style="padding: 12px; text-align: center; font-weight: 600; color: #1e293b; font-size: 0.9rem;">
                                Items</th>
                            <th
                                style="padding: 12px; text-align: center; font-weight: 600; color: #1e293b; font-size: 0.9rem;">
                                Total Amount</th>
                            <th
                                style="padding: 12px; text-align: center; font-weight: 600; color: #1e293b; font-size: 0.9rem;">
                                Order Date</th>
                            <th
                                style="padding: 12px; text-align: center; font-weight: 600; color: #1e293b; font-size: 0.9rem;">
                                Expected Delivery</th>
                            <th
                                style="padding: 12px; text-align: center; font-weight: 600; color: #1e293b; font-size: 0.9rem;">
                                Status</th>
                            <th
                                style="padding: 12px; text-align: center; font-weight: 600; color: #1e293b; font-size: 0.9rem;">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody id="ordersTableBody">
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

<!-- Create Order Modal -->
<div class="modal fade" id="createOrderModal" tabindex="-1" role="dialog" aria-labelledby="createOrderModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 20px 40px rgba(0,0,0,0.15);">
            <div class="modal-header"
                style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 12px 12px 0 0; border-bottom: none; padding: 12px 16px;">
                <h5 class="modal-title" id="createOrderModalLabel"
                    style="font-size: 1rem; font-weight: 600; margin: 0;">
                    <i class="fas fa-plus me-2"></i>Create New Purchase Order
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"
                    style="opacity: 0.8; font-size: 1rem;"></button>
            </div>
            <div class="modal-body" style="padding: 16px; max-height: 70vh; overflow-y: auto;">
                <form id="createOrderForm">
                    <!-- Basic Information Section -->
                    <div class="form-section"
                        style="background: white; border-radius: 6px; padding: 12px; margin-bottom: 12px; border: 1px solid #e2e8f0;">
                        <h6
                            style="color: #374151; font-weight: 600; margin-bottom: 12px; display: flex; align-items: center; font-size: 0.8rem;">
                            <i class="fas fa-info-circle me-2" style="color: #6366f1; font-size: 0.7rem;"></i>Order
                            Information
                        </h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-2">
                                    <label for="orderSupplier"
                                        style="font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 0.75rem;">Supplier
                                        *</label>
                                    <select class="form-control" id="orderSupplier" required
                                        style="border: 1px solid #d1d5db; border-radius: 4px; padding: 6px 8px; font-size: 0.75rem; transition: all 0.2s; height: 32px;">
                                        <option value="">Select Supplier</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-2">
                                    <label for="expectedDelivery"
                                        style="font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 0.75rem;">Expected
                                        Delivery Date *</label>
                                    <input type="date" class="form-control" id="expectedDelivery" required
                                        style="border: 1px solid #d1d5db; border-radius: 4px; padding: 6px 8px; font-size: 0.75rem; transition: all 0.2s; height: 32px;">
                                </div>
                            </div>
                        </div>
                        <div class="form-group mb-2">
                            <label for="orderNotes"
                                style="font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 0.75rem;">Notes</label>
                            <textarea class="form-control" id="orderNotes" rows="2"
                                style="border: 1px solid #d1d5db; border-radius: 4px; padding: 6px 8px; font-size: 0.75rem; resize: vertical;"
                                placeholder="Additional notes for this order..."></textarea>
                        </div>
                    </div>

                    <!-- Order Items Section -->
                    <div class="form-section"
                        style="background: white; border-radius: 6px; padding: 12px; border: 1px solid #e2e8f0;">
                        <h6
                            style="color: #374151; font-weight: 600; margin-bottom: 12px; display: flex; align-items: center; font-size: 0.8rem;">
                            <i class="fas fa-boxes me-2" style="color: #6366f1; font-size: 0.7rem;"></i>Order Items
                        </h6>
                        <div id="orderItems">
                            <div class="order-item row mb-2">
                                <div class="col-md-4">
                                    <select class="form-control medicine-select"
                                        style="border: 1px solid #d1d5db; border-radius: 4px; padding: 6px 8px; font-size: 0.75rem; height: 32px;">
                                        <option value="">Select Medicine</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <input type="number" class="form-control quantity-input" placeholder="Qty" min="1"
                                        style="border: 1px solid #d1d5db; border-radius: 4px; padding: 6px 8px; font-size: 0.75rem; height: 32px;">
                                </div>
                                <div class="col-md-2">
                                    <input type="number" class="form-control price-input" placeholder="Price"
                                        step="0.01" min="0"
                                        style="border: 1px solid #d1d5db; border-radius: 4px; padding: 6px 8px; font-size: 0.75rem; height: 32px;">
                                </div>
                                <div class="col-md-3">
                                    <input type="text" class="form-control total-input" placeholder="Total" readonly
                                        style="border: 1px solid #d1d5db; border-radius: 4px; padding: 6px 8px; font-size: 0.75rem; height: 32px; background-color: #f9fafb;">
                                </div>
                                <div class="col-md-1">
                                    <button type="button" class="btn btn-outline-danger btn-sm"
                                        onclick="removeOrderItem(this)"
                                        style="border: 1px solid #ef4444; border-radius: 4px; padding: 4px 8px; font-size: 0.7rem; height: 32px;">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="addOrderItem()"
                            style="border: 1px solid #3b82f6; border-radius: 4px; padding: 6px 12px; font-size: 0.75rem; background: white; color: #3b82f6;">
                            <i class="fas fa-plus me-1"></i>Add Item
                        </button>

                        <div class="row mt-3">
                            <div class="col-md-8"></div>
                            <div class="col-md-4">
                                <div class="d-flex justify-content-between"
                                    style="background: #f8fafc; padding: 8px 12px; border-radius: 6px; border: 1px solid #e2e8f0;">
                                    <strong style="color: #374151; font-size: 0.8rem;">Total Amount:</strong>
                                    <strong style="color: #6366f1; font-size: 0.8rem;" id="orderTotal">0.00</strong>
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
                    <button type="button" class="btn btn-primary" onclick="createOrder()"
                        style="padding: 6px 12px; border-radius: 6px; font-weight: 500; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; font-size: 0.8rem;">Create
                        Order</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Order Details Modal -->
<div class="modal fade" id="orderDetailsModal" tabindex="-1" role="dialog" aria-labelledby="orderDetailsModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 20px 40px rgba(0,0,0,0.15);">
            <div class="modal-header"
                style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 12px 12px 0 0; border-bottom: none; padding: 12px 16px;">
                <h5 class="modal-title" id="orderDetailsModalLabel"
                    style="font-size: 1rem; font-weight: 600; margin: 0;">
                    <i class="fas fa-file-invoice me-2"></i>Order Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"
                    style="opacity: 0.8; font-size: 1rem;"></button>
            </div>
            <div class="modal-body" id="orderDetailsBody" style="padding: 16px; max-height: 60vh; overflow-y: auto;">
                <!-- Order details will be loaded here -->
            </div>
            <div class="modal-footer"
                style="border-top: 1px solid #e2e8f0; padding: 12px 16px; background: #f8fafc; border-radius: 0 0 12px 12px;">
                <div style="display: flex; gap: 6px; margin-left: auto;">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal"
                        style="padding: 6px 12px; border-radius: 6px; font-weight: 500; font-size: 0.8rem;">Close</button>
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
                    display in the orders table:</p>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-check mb-1">
                            <input class="form-check-input" type="checkbox" id="colOrderNumber" checked
                                style="transform: scale(0.8);">
                            <label class="form-check-label" for="colOrderNumber"
                                style="font-weight: 500; font-size: 0.7rem;">Order #</label>
                        </div>
                        <div class="form-check mb-1">
                            <input class="form-check-input" type="checkbox" id="colSupplier" checked
                                style="transform: scale(0.8);">
                            <label class="form-check-label" for="colSupplier"
                                style="font-weight: 500; font-size: 0.7rem;">Supplier</label>
                        </div>
                        <div class="form-check mb-1">
                            <input class="form-check-input" type="checkbox" id="colItems" checked
                                style="transform: scale(0.8);">
                            <label class="form-check-label" for="colItems"
                                style="font-weight: 500; font-size: 0.7rem;">Items</label>
                        </div>
                        <div class="form-check mb-1">
                            <input class="form-check-input" type="checkbox" id="colAmount" checked
                                style="transform: scale(0.8);">
                            <label class="form-check-label" for="colAmount"
                                style="font-weight: 500; font-size: 0.7rem;">Total Amount</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check mb-1">
                            <input class="form-check-input" type="checkbox" id="colOrderDate" checked
                                style="transform: scale(0.8);">
                            <label class="form-check-label" for="colOrderDate"
                                style="font-weight: 500; font-size: 0.7rem;">Order Date</label>
                        </div>
                        <div class="form-check mb-1">
                            <input class="form-check-input" type="checkbox" id="colDelivery" checked
                                style="transform: scale(0.8);">
                            <label class="form-check-label" for="colDelivery"
                                style="font-weight: 500; font-size: 0.7rem;">Expected Delivery</label>
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
<div class="modal fade" id="deleteOrderModal" tabindex="-1" role="dialog" aria-labelledby="deleteOrderModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content" style="border-radius: 10px; border: none; box-shadow: 0 20px 40px rgba(0,0,0,0.15);">
            <div class="modal-header"
                style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: white; border-radius: 10px 10px 0 0; border-bottom: none; padding: 10px 14px;">
                <h5 class="modal-title" id="deleteOrderModalLabel"
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
                    want to delete this purchase order?</h6>
                <p style="color: #6b7280; margin-bottom: 12px; font-size: 0.7rem;">This action cannot be undone and will
                    permanently remove the order from your system.</p>
                <div id="deleteOrderInfo"></div>
            </div>
            <div class="modal-footer"
                style="border-top: 1px solid #e2e8f0; padding: 10px 14px; background: #f8fafc; border-radius: 0 0 10px 10px;">
                <div style="display: flex; gap: 6px; margin-left: auto;">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal"
                        style="padding: 5px 10px; border-radius: 4px; font-weight: 500; font-size: 0.7rem;">
                        Cancel
                    </button>
                    <button type="button" class="btn btn-danger" onclick="confirmDeleteOrder()"
                        style="padding: 5px 10px; border-radius: 4px; font-weight: 500; background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); border: none; font-size: 0.7rem;">
                        Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.purchase-orders .summary-card {
    transition: transform 0.2s, box-shadow 0.2s;
}

.purchase-orders .summary-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.purchase-orders .table tbody tr:hover {
    background: #f8fafc;
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    transition: all 0.2s ease;
}

.purchase-orders .btn:hover {
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
    .purchase-orders .orders-summary .col-md-3 {
        margin-bottom: 16px;
    }

    .modal-dialog {
        margin: 10px;
    }
}
</style>

<script>
let ordersData = [
    <?php foreach ($purchaseOrders as $order): ?> {
        id: <?= $order['id'] ?>,
        order_number: "<?= htmlspecialchars($order['order_number']) ?>",
        supplier_name: "<?= htmlspecialchars($order['supplier_name']) ?>",
        item_count: <?= $order['item_count'] ?>,
        total_amount: <?= $order['total_amount'] ?>,
        order_date: "<?= $order['order_date'] ?>",
        expected_delivery: "<?= $order['expected_delivery'] ?>",
        status: "<?= htmlspecialchars($order['status']) ?>",
        created_by_name: "<?= htmlspecialchars($order['created_by_name'] ?? 'N/A') ?>",
        approved_by_name: "<?= htmlspecialchars($order['approved_by_name'] ?? 'N/A') ?>",
        notes: "<?= htmlspecialchars($order['notes'] ?? '') ?>"
    },
    <?php endforeach; ?>
];

let medicines = [];
let suppliers = [];
let currentOrderId = null;
let deleteOrderId = null; // Add this variable to track which order to delete
let filteredData = [...ordersData];
let currentPage = 1;
let itemsPerPage = 25;
let totalPages = 1;

$(document).ready(function() {
    // Initialize column visibility
    initializeColumnVisibility();

    // Add event listeners for modals
    $('#createOrderModal').on('hidden.bs.modal', function() {
        $('#createOrderForm')[0].reset();
        currentOrderId = null;
    });
    $('#orderDetailsModal').on('hidden.bs.modal', function() {
        currentOrderId = null;
    });
    $('#columnModal').on('hidden.bs.modal', function() {
        // No specific reset needed for column modal
    });
    $('#deleteOrderModal').on('hidden.bs.modal', function() {
        deleteOrderId = null;
    });

    // Add event listeners for filter inputs
    $('#searchOrders, #statusFilter, #supplierFilter, #dateFilter, #amountFilter').on('input change',
        function() {
            applyFilters();
        });

    // Add event listener for items per page change
    $('#itemsPerPage').on('change', function() {
        itemsPerPage = parseInt($(this).val());
        currentPage = 1;
        refreshOrdersTable();
    });

    // Initialize table
    refreshOrdersTable();
});

// Helper function to get status badge
function getStatusBadge(status) {
    const badges = {
        'pending': '<span class="badge" style="background: linear-gradient(135deg, #f59e0b 0%, #f97316 100%); color: white; padding: 4px 8px; border-radius: 12px; font-size: 0.7rem; font-weight: 600;">PENDING</span>',
        'approved': '<span class="badge" style="background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); color: white; padding: 4px 8px; border-radius: 12px; font-size: 0.7rem; font-weight: 600;">APPROVED</span>',
        'ordered': '<span class="badge" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); color: white; padding: 4px 8px; border-radius: 12px; font-size: 0.7rem; font-weight: 600;">ORDERED</span>',
        'delivered': '<span class="badge" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; padding: 4px 8px; border-radius: 12px; font-size: 0.7rem; font-weight: 600;">DELIVERED</span>',
        'cancelled': '<span class="badge" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: white; padding: 4px 8px; border-radius: 12px; font-size: 0.7rem; font-weight: 600;">CANCELLED</span>'
    };
    return badges[status] || badges['pending'];
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
}

function showCreateOrderModal() {
    loadSuppliers();
    loadMedicines();
    $('#createOrderModal').modal('show');
}

function loadSuppliers() {
    fetch('<?= Yii::$app->urlManager->createUrl(['inventory/get-suppliers']) ?>', {
            method: 'POST'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                suppliers = data.data;
                const supplierSelect = document.getElementById('orderSupplier');
                supplierSelect.innerHTML = '<option value="">Select Supplier</option>';
                suppliers.forEach(supplier => {
                    supplierSelect.innerHTML += `<option value="${supplier.id}">${supplier.name}</option>`;
                });
            }
        })
        .catch(error => {
            console.error('Error loading suppliers:', error);
            showNotification('Error loading suppliers', 'error');
        });
}

function loadMedicines() {
    fetch('<?= Yii::$app->urlManager->createUrl(['inventory/get-medicines']) ?>')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                medicines = data.data;
                updateMedicineSelects();
            }
        })
        .catch(error => {
            console.error('Error loading medicines:', error);
            showNotification('Error loading medicines', 'error');
        });
}

function updateMedicineSelects() {
    document.querySelectorAll('.medicine-select').forEach(select => {
        const currentValue = select.value;
        select.innerHTML = '<option value="">Select Medicine</option>';
        medicines.forEach(medicine => {
            select.innerHTML +=
                `<option value="${medicine.id}">${medicine.name} (${medicine.strength})</option>`;
        });
        select.value = currentValue;
    });
}

function addOrderItem() {
    const itemsContainer = document.getElementById('orderItems');
    const newItem = document.createElement('div');
    newItem.className = 'order-item row mb-2';
    newItem.innerHTML = `
            <div class="col-md-4">
                <select class="form-control medicine-select" style="border: 1px solid #d1d5db; border-radius: 4px; padding: 6px 8px; font-size: 0.75rem; height: 32px;">
                    <option value="">Select Medicine</option>
                </select>
            </div>
            <div class="col-md-2">
                <input type="number" class="form-control quantity-input" placeholder="Qty" min="1" style="border: 1px solid #d1d5db; border-radius: 4px; padding: 6px 8px; font-size: 0.75rem; height: 32px;">
            </div>
            <div class="col-md-2">
                <input type="number" class="form-control price-input" placeholder="Price" step="0.01" min="0" style="border: 1px solid #d1d5db; border-radius: 4px; padding: 6px 8px; font-size: 0.75rem; height: 32px;">
            </div>
            <div class="col-md-3">
                <input type="text" class="form-control total-input" placeholder="Total" readonly style="border: 1px solid #d1d5db; border-radius: 4px; padding: 6px 8px; font-size: 0.75rem; height: 32px; background-color: #f9fafb;">
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeOrderItem(this)" style="border: 1px solid #ef4444; border-radius: 4px; padding: 4px 8px; font-size: 0.7rem; height: 32px;">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        `;
    itemsContainer.appendChild(newItem);
    updateMedicineSelects();
    addItemEventListeners(newItem);
}

function removeOrderItem(button) {
    button.closest('.order-item').remove();
    calculateOrderTotal();
}

function addItemEventListeners(itemElement) {
    const quantityInput = itemElement.querySelector('.quantity-input');
    const priceInput = itemElement.querySelector('.price-input');
    const totalInput = itemElement.querySelector('.total-input');

    quantityInput.addEventListener('input', calculateItemTotal);
    priceInput.addEventListener('input', calculateItemTotal);

    function calculateItemTotal() {
        const quantity = parseFloat(quantityInput.value) || 0;
        const price = parseFloat(priceInput.value) || 0;
        const total = quantity * price;
        totalInput.value = total.toFixed(2);
        calculateOrderTotal();
    }
}

function calculateOrderTotal() {
    const totalInputs = document.querySelectorAll('.total-input');
    let total = 0;
    totalInputs.forEach(input => {
        total += parseFloat(input.value) || 0;
    });
    document.getElementById('orderTotal').textContent = `${total.toFixed(2)}`;
}

function createOrder() {
    const supplierId = document.getElementById('orderSupplier').value;
    const expectedDelivery = document.getElementById('expectedDelivery').value;
    const notes = document.getElementById('orderNotes').value;

    if (!supplierId || !expectedDelivery) {
        showNotification('Please fill in all required fields', 'error');
        return;
    }

    const items = [];
    const itemElements = document.querySelectorAll('.order-item');
    let hasValidItems = false;

    itemElements.forEach(item => {
        const medicineId = item.querySelector('.medicine-select').value;
        const quantity = parseInt(item.querySelector('.quantity-input').value);
        const price = parseFloat(item.querySelector('.price-input').value);

        if (medicineId && quantity && price) {
            items.push({
                medicine_id: medicineId,
                quantity: quantity,
                unit_price: price
            });
            hasValidItems = true;
        }
    });

    if (!hasValidItems) {
        showNotification('Please add at least one valid item', 'error');
        return;
    }

    const totalAmount = items.reduce((sum, item) => sum + (item.quantity * item.unit_price), 0);

    const formData = new FormData();
    formData.append('supplier_id', supplierId);
    formData.append('expected_delivery', expectedDelivery);
    formData.append('notes', notes);
    formData.append('items', JSON.stringify(items));
    formData.append('total_amount', totalAmount);

    fetch('<?= Yii::$app->urlManager->createUrl(['inventory/create-purchase-order']) ?>', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message, 'success');
                $('#createOrderModal').modal('hide');
                location.reload();
            } else {
                showNotification(data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error creating order:', error);
            showNotification('Error creating purchase order', 'error');
        });
}

function viewOrder(id) {
    currentOrderId = id;
    fetch(`<?= Yii::$app->urlManager->createUrl(['inventory/get-order-details']) ?>&order_id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const order = data.data.order;
                const items = data.data.items;

                const content = `
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <h6 style="color: #374151; font-weight: 600; margin-bottom: 8px; font-size: 0.8rem;">Order Information</h6>
                                <p style="margin: 4px 0; color: #64748b; font-size: 0.75rem;"><strong>Order Number:</strong> ${order.order_number}</p>
                                <p style="margin: 4px 0; color: #64748b; font-size: 0.75rem;"><strong>Supplier:</strong> ${order.supplier_name}</p>
                                <p style="margin: 4px 0; color: #64748b; font-size: 0.75rem;"><strong>Order Date:</strong> ${formatDate(order.order_date)}</p>
                                <p style="margin: 4px 0; color: #64748b; font-size: 0.75rem;"><strong>Expected Delivery:</strong> ${formatDate(order.expected_delivery)}</p>
                                <p style="margin: 4px 0; color: #64748b; font-size: 0.75rem;"><strong>Status:</strong> ${getStatusBadge(order.status)}</p>
                            </div>
                            <div class="col-md-6">
                                <h6 style="color: #374151; font-weight: 600; margin-bottom: 8px; font-size: 0.8rem;">Order Summary</h6>
                                <p style="margin: 4px 0; color: #64748b; font-size: 0.75rem;"><strong>Total Items:</strong> ${items.length}</p>
                                <p style="margin: 4px 0; color: #64748b; font-size: 0.75rem;"><strong>Total Amount:</strong> $${parseFloat(order.total_amount).toFixed(2)}</p>
                                <p style="margin: 4px 0; color: #64748b; font-size: 0.75rem;"><strong>Created By:</strong> ${order.created_by_name || 'N/A'}</p>
                                <p style="margin: 4px 0; color: #64748b; font-size: 0.75rem;"><strong>Approved By:</strong> ${order.approved_by_name || 'N/A'}</p>
                                <p style="margin: 4px 0; color: #64748b; font-size: 0.75rem;"><strong>Notes:</strong> ${order.notes || 'None'}</p>
                            </div>
                        </div>
                        
                        <h6 style="color: #374151; font-weight: 600; margin-bottom: 12px; font-size: 0.8rem;">Order Items</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered" style="font-size: 0.75rem;">
                                <thead style="background: #f8fafc;">
                                    <tr>
                                        <th style="border: 1px solid #e5e7eb; padding: 8px; color: #374151; font-weight: 600;">Medicine</th>
                                        <th style="border: 1px solid #e5e7eb; padding: 8px; color: #374151; font-weight: 600;">Quantity</th>
                                        <th style="border: 1px solid #e5e7eb; padding: 8px; color: #374151; font-weight: 600;">Unit Price</th>
                                        <th style="border: 1px solid #e5e7eb; padding: 8px; color: #374151; font-weight: 600;">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${items.map(item => `
                                        <tr>
                                            <td style="border: 1px solid #e5e7eb; padding: 8px; color: #64748b;">${item.medicine_name}</td>
                                            <td style="border: 1px solid #e5e7eb; padding: 8px; color: #64748b;">${item.quantity}</td>
                                            <td style="border: 1px solid #e5e7eb; padding: 8px; color: #64748b;">${parseFloat(item.unit_price).toFixed(2)}</td>
                                            <td style="border: 1px solid #e5e7eb; padding: 8px; color: #1e293b; font-weight: 600;">${parseFloat(item.total_price).toFixed(2)}</td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        </div>
                    `;
                document.getElementById('orderDetailsBody').innerHTML = content;
                $('#orderDetailsModal').modal('show');
            } else {
                showNotification(data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error fetching order details:', error);
            showNotification('Error fetching order details', 'error');
        });
}

function updateOrderStatus(id) {
    const statusOptions = ['pending', 'approved', 'ordered', 'delivered', 'cancelled'];
    const order = ordersData.find(o => o.id === id);

    if (!order) {
        showNotification('Order not found', 'error');
        return;
    }

    const currentIndex = statusOptions.indexOf(order.status);
    const nextStatus = statusOptions[currentIndex + 1] || statusOptions[0];

    const formData = new FormData();
    formData.append('order_id', id);
    formData.append('status', nextStatus);

    fetch('<?= Yii::$app->urlManager->createUrl(['inventory/update-order-status']) ?>', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message, 'success');
                // Update the order status in local data
                order.status = nextStatus;
                refreshOrdersTable();
            } else {
                showNotification(data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error updating order status:', error);
            showNotification('Error updating order status', 'error');
        });
}

function deleteOrder(id) {
    const order = ordersData.find(o => o.id === id);

    if (order) {
        deleteOrderId = id;
        $('#deleteOrderInfo').html(`
                <div class="alert alert-warning" style="background: #fef3c7; border: 1px solid #f59e0b; color: #92400e; border-radius: 8px; padding: 16px;">
                    <div style="display: flex; align-items: center;">
                        <i class="fas fa-file-invoice me-2" style="font-size: 1.2rem;"></i>
                        <div>
                            <strong>Order #${order.order_number}</strong><br>
                            <small>${order.supplier_name} - ${parseFloat(order.total_amount).toFixed(2)}</small>
                        </div>
                    </div>
                </div>
            `);

        // Use Bootstrap 5 modal API
        const modal = new bootstrap.Modal(document.getElementById('deleteOrderModal'));
        modal.show();
    }
}

function confirmDeleteOrder() {
    if (deleteOrderId) {
        const formData = new FormData();
        formData.append('order_id', deleteOrderId);

        fetch('<?= Yii::$app->urlManager->createUrl(['inventory/delete-order']) ?>', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification(data.message, 'success');
                    // Remove order from local data
                    ordersData = ordersData.filter(order => order.id !== deleteOrderId);
                    filteredData = [...ordersData];
                    refreshOrdersTable();
                    updateStats();

                    // Use Bootstrap 5 modal API
                    const modal = bootstrap.Modal.getInstance(document.getElementById('deleteOrderModal'));
                    modal.hide();
                    deleteOrderId = null;
                } else {
                    showNotification(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error deleting order:', error);
                showNotification('Error deleting order', 'error');
            });
    }
}

function applyFilters() {
    const searchTerm = document.getElementById('searchOrders').value.toLowerCase();
    const statusFilter = document.getElementById('statusFilter').value;
    const supplierFilter = document.getElementById('supplierFilter').value;
    const dateFilter = document.getElementById('dateFilter').value;
    const amountFilter = document.getElementById('amountFilter').value;

    filteredData = ordersData.filter(order => {
        let show = true;

        // Search filter
        if (searchTerm && !order.order_number.toLowerCase().includes(searchTerm) &&
            !order.supplier_name.toLowerCase().includes(searchTerm)) {
            show = false;
        }

        // Status filter
        if (statusFilter && order.status !== statusFilter) {
            show = false;
        }

        // Supplier filter
        if (supplierFilter && order.supplier_name !== supplierFilter) {
            show = false;
        }

        // Date filter
        if (dateFilter) {
            const today = new Date();
            const orderDate = new Date(order.order_date);

            switch (dateFilter) {
                case 'today':
                    if (orderDate.toDateString() !== today.toDateString()) show = false;
                    break;
                case 'week':
                    const weekAgo = new Date(today.getTime() - 7 * 24 * 60 * 60 * 1000);
                    if (orderDate < weekAgo) show = false;
                    break;
                case 'month':
                    if (orderDate.getMonth() !== today.getMonth() || orderDate.getFullYear() !== today
                        .getFullYear()) show = false;
                    break;
                case 'quarter':
                    const quarterStart = new Date(today.getFullYear(), Math.floor(today.getMonth() / 3) * 3, 1);
                    if (orderDate < quarterStart) show = false;
                    break;
            }
        }

        // Amount filter
        if (amountFilter) {
            switch (amountFilter) {
                case 'low':
                    if (order.total_amount >= 100) show = false;
                    break;
                case 'medium':
                    if (order.total_amount < 100 || order.total_amount > 500) show = false;
                    break;
                case 'high':
                    if (order.total_amount <= 500) show = false;
                    break;
            }
        }

        return show;
    });

    currentPage = 1;
    refreshOrdersTable();
}

function clearFilters() {
    document.getElementById('searchOrders').value = '';
    document.getElementById('statusFilter').value = '';
    document.getElementById('supplierFilter').value = '';
    document.getElementById('dateFilter').value = '';
    document.getElementById('amountFilter').value = '';

    filteredData = [...ordersData];
    currentPage = 1;
    refreshOrdersTable();
}

function refreshOrdersTable() {
    const startIndex = (currentPage - 1) * itemsPerPage;
    const endIndex = startIndex + itemsPerPage;
    const pageData = filteredData.slice(startIndex, endIndex);

    const tbody = document.getElementById('ordersTableBody');
    tbody.innerHTML = '';

    if (pageData.length === 0) {
        tbody.innerHTML = `
                <tr>
                    <td colspan="8" style="text-align: center; padding: 40px; color: #6b7280;">
                        <i class="fas fa-inbox" style="font-size: 2rem; margin-bottom: 12px; display: block;"></i>
                        No orders found
                    </td>
                </tr>
            `;
    } else {
        pageData.forEach(order => {
            const row = document.createElement('tr');
            row.style.borderBottom = '1px solid #f1f5f9';
            row.innerHTML = `
                    <td style="padding: 12px; color: #1e293b; font-weight: 600; font-size: 0.8rem;">${order.order_number}</td>
                    <td style="padding: 12px; color: #64748b; font-size: 0.8rem;">${order.supplier_name}</td>
                    <td style="padding: 12px; color: #64748b; text-align: center; font-size: 0.8rem;">${order.item_count} items</td>
                    <td style="padding: 12px; color: #1e293b; font-weight: 600; text-align: center; font-size: 0.8rem;">${parseFloat(order.total_amount).toFixed(2)}</td>
                    <td style="padding: 12px; color: #64748b; text-align: center; font-size: 0.8rem;">${formatDate(order.order_date)}</td>
                    <td style="padding: 12px; color: #64748b; text-align: center; font-size: 0.8rem;">${formatDate(order.expected_delivery)}</td>
                    <td style="padding: 12px; text-align: center;">${getStatusBadge(order.status)}</td>
                    <td style="padding: 12px; text-align: center;">
                        <div style="display: flex; gap: 4px; justify-content: center;">
                            <button class="btn btn-outline-primary btn-sm" onclick="viewOrder(${order.id})" 
                                style="border: 1px solid #3b82f6; border-radius: 4px; padding: 4px 8px; font-size: 0.7rem; background: white; color: #3b82f6;">
                                <i class="fas fa-eye"></i>
                            </button>
                            <?php if ($canEdit): ?>
                            <button class="btn btn-outline-success btn-sm" onclick="updateOrderStatus(${order.id})" 
                                style="border: 1px solid #10b981; border-radius: 4px; padding: 4px 8px; font-size: 0.7rem; background: white; color: #10b981;">
                                <i class="fas fa-edit"></i>
                            </button>
                            <?php endif; ?>
                            <?php if ($canDelete): ?>
                            <button class="btn btn-outline-danger btn-sm" onclick="deleteOrder(${order.id})" 
                                style="border: 1px solid #ef4444; border-radius: 4px; padding: 4px 8px; font-size: 0.7rem; background: white; color: #ef4444;">
                                <i class="fas fa-trash"></i>
                            </button>
                            <?php endif; ?>
                        </div>
                    </td>
                `;
            tbody.appendChild(row);
        });
    }

    updatePagination();
    updateStats();
}

function updatePagination() {
    totalPages = Math.ceil(filteredData.length / itemsPerPage);
    const pagination = document.getElementById('pagination');
    const paginationInfo = document.getElementById('paginationInfo');

    // Update pagination info
    const startItem = (currentPage - 1) * itemsPerPage + 1;
    const endItem = Math.min(currentPage * itemsPerPage, filteredData.length);
    paginationInfo.textContent = `Showing ${startItem} to ${endItem} of ${filteredData.length} entries`;

    // Generate pagination buttons
    pagination.innerHTML = '';

    if (totalPages <= 1) return;

    // Previous button
    const prevButton = document.createElement('li');
    prevButton.className = `page-item ${currentPage === 1 ? 'disabled' : ''}`;
    prevButton.innerHTML =
        `<a class="page-link" href="#" onclick="changePage(${currentPage - 1})" style="font-size: 0.7rem; padding: 4px 8px;">Previous</a>`;
    pagination.appendChild(prevButton);

    // Page numbers
    const startPage = Math.max(1, currentPage - 2);
    const endPage = Math.min(totalPages, currentPage + 2);

    for (let i = startPage; i <= endPage; i++) {
        const pageButton = document.createElement('li');
        pageButton.className = `page-item ${i === currentPage ? 'active' : ''}`;
        pageButton.innerHTML =
            `<a class="page-link" href="#" onclick="changePage(${i})" style="font-size: 0.7rem; padding: 4px 8px;">${i}</a>`;
        pagination.appendChild(pageButton);
    }

    // Next button
    const nextButton = document.createElement('li');
    nextButton.className = `page-item ${currentPage === totalPages ? 'disabled' : ''}`;
    nextButton.innerHTML =
        `<a class="page-link" href="#" onclick="changePage(${currentPage + 1})" style="font-size: 0.7rem; padding: 4px 8px;">Next</a>`;
    pagination.appendChild(nextButton);
}

function changePage(page) {
    if (page >= 1 && page <= totalPages) {
        currentPage = page;
        refreshOrdersTable();
    }
}

function updateStats() {
    document.getElementById('totalOrders').textContent = ordersData.length;
    document.getElementById('completedOrders').textContent = ordersData.filter(o => o.status === 'delivered').length;
    document.getElementById('pendingOrders').textContent = ordersData.filter(o => o.status === 'pending').length;
    document.getElementById('cancelledOrders').textContent = ordersData.filter(o => o.status === 'cancelled').length;
}

function initializeColumnVisibility() {
    // Add event listeners for column visibility checkboxes
    document.querySelectorAll('input[id^="col"]').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const columnName = this.id.replace('col', '').toLowerCase();
            const columnIndex = getColumnIndex(columnName);
            const table = document.getElementById('ordersTable');

            if (columnIndex !== -1) {
                // Toggle header
                const header = table.querySelectorAll('thead th')[columnIndex];
                header.style.display = this.checked ? '' : 'none';

                // Toggle body cells
                const rows = table.querySelectorAll('tbody tr');
                rows.forEach(row => {
                    const cell = row.cells[columnIndex];
                    if (cell) cell.style.display = this.checked ? '' : 'none';
                });
            }
        });
    });
}

function getColumnIndex(columnName) {
    const columnMap = {
        'ordernumber': 0,
        'supplier': 1,
        'items': 2,
        'amount': 3,
        'orderdate': 4,
        'delivery': 5,
        'status': 6
    };
    return columnMap[columnName] || -1;
}

function toggleColumns() {
    $('#columnModal').modal('show');
}

function applyColumnVisibility() {
    $('#columnModal').modal('hide');
}

function manualRefresh() {
    location.reload();
}

function exportOrders() {
    // Create CSV content
    const headers = ['Order Number', 'Supplier', 'Items', 'Total Amount', 'Order Date', 'Expected Delivery', 'Status'];
    const csvContent = [
        headers.join(','),
        ...filteredData.map(order => [
            order.order_number,
            order.supplier_name,
            order.item_count,
            order.total_amount,
            order.order_date,
            order.expected_delivery,
            order.status
        ].join(','))
    ].join('\n');

    // Download CSV
    const blob = new Blob([csvContent], {
        type: 'text/csv'
    });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `purchase_orders_${new Date().toISOString().split('T')[0]}.csv`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    window.URL.revokeObjectURL(url);

    showNotification('Orders exported successfully', 'success');
}

function showNotification(message, type = 'info') {
    const alertClass = type === 'error' ? 'alert-danger' :
        type === 'success' ? 'alert-success' :
        type === 'warning' ? 'alert-warning' : 'alert-info';

    const notification = $(`
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert" 
                 style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px; font-size: 0.8rem;">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `);
    $('body').append(notification);
    setTimeout(() => notification.alert('close'), 5000);
}

// Initialize event listeners for existing order items
document.querySelectorAll('.order-item').forEach(item => {
    addItemEventListeners(item);
});
</script>
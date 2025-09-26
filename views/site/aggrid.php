<?php

$is_sessioned = false;

session_start();

if (isset($_SESSION['user']) && !empty($_SESSION['user'])) {
    $is_sessioned = true;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>REMS Dashboard</title>
    <link rel="stylesheet" href="assets/css/all.min.css" />
    <link rel="stylesheet" href="assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="assets/css/style.css" />
    <script src="assets/js/jquery-3.6.0.min.js"></script>

    <!-- AG Grid CSS -->
    <link rel="stylesheet" href="assets/css/ag-grid.css">
    <link rel="stylesheet" href="assets/css/ag-theme-alpine.css">
    <script src="assets/js/ag-grid-community.min.js"></script>

</head>

<body>
    <div class="bg-primary text-white p-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0">REMS - Dashboard</h5>
        <div class="dropdown"> <i class="fas fa-cog fa-lg" data-bs-toggle="dropdown"></i>
            <ul class="dropdown-menu dropdown-menu-end" style="height: auto;">
                <li><a class="dropdown-item" href="#">Settings</a></li>
                <li><a class="dropdown-item" href="#">Profile</a></li>
                <?php if ($is_sessioned): ?>
                <li><a class="dropdown-item" href="#" id="logoutBtn">Logout</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
    <div class="container my-3">
        <div class="tabs-wrapper pb-2 mb-3">
            <div class="d-flex justify-content-between align-items-end flex-wrap">
                <!-- Tabs on the left -->
                <ul class="nav nav-tabs rems-tabs" id="projectTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link d-flex align-items-center gap-2 fs-5 active" id="welfare-tab"
                            data-bs-toggle="tab" data-bs-target="#welfare" type="button" aria-selected="true"
                            role="tab"> <i class="fa-solid fa-handshake-angle"></i> -- </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link d-flex align-items-center gap-2 fs-5" id="housing-tab"
                            data-bs-toggle="tab" data-bs-target="#housing" type="button" aria-selected="false"
                            tabindex="-1" role="tab"> <i class="fas fa-house"></i> -- </button>
                    </li>
                </ul>

                <!-- Locations on the right -->
                <div class="d-flex gap-2 flex-wrap mt-2 mt-md-0 locations-container"></div>
            </div>
        </div>
        <div class="row g-2 mt-1">
            <div class="col-md-3 mt-1">
                <div class="card-info bg-light-blue text-dark" data-bs-toggle="modal" data-bs-target="#totalPriceModal">
                    <div>
                        <div>Total Price</div>
                        <h5 class="mb-0" id="totalPrice"> -- </h5>
                    </div>
                    <i class="fa-solid fa-money-bill-wave fa-2x"></i>
                </div>
            </div>
            <div class="col-md-3 mt-1">
                <div class="card-info bg-light-green text-dark" data-bs-toggle="modal"
                    data-bs-target="#paidTillDateModal">
                    <div>
                        <div>Paid Till Date</div>
                        <h5 class="mb-0" id="paidTillDate"> -- </h5>
                    </div>
                    <i class="fas fa-check-circle fa-2x"></i>
                </div>
            </div>
            <div class="col-md-3 mt-1">
                <div class="card-info bg-light-orange text-dark" data-bs-toggle="modal"
                    data-bs-target="#dueTillDateModal">
                    <div>
                        <div>Due Till Date</div>
                        <h5 class="mb-0" id="dueTillDate"> -- </h5>
                    </div>
                    <i class="fas fa-exclamation-circle fa-2x"></i>
                </div>
            </div>
            <div class="col-md-3 mt-1">
                <div class="card-info bg-light-purple text-dark" data-bs-toggle="modal"
                    data-bs-target="#totalBalanceModal">
                    <div>
                        <div>Total Balance</div>
                        <h5 class="mb-0" id="totalBalance"> -- </h5>
                    </div>
                    <i class="fas fa-calculator fa-2x"></i>
                </div>
            </div>
        </div>
        <br>
        <!-- Plot Sizes Section -->
        <div id="selectedSizesSummary" class="mb-2" style="display:none;"></div>
        <div class="d-flex justify-content-between align-items-center mt-4">
            <div class="toggle-switch shadow-sm">
                <button class="active" id="officerBtn"><i class="fas fa-user-tie me-1"></i> Officer</button>
                <button id="civilianBtn"><i class="fas fa-user me-1"></i> Civilian</button>
                <button id="retiredBtn"><i class="fas fa-user-clock me-1"></i> Retired</button>
            </div>

            <div class="" id="plotDropdown">
                <div class="dropdown" data-bs-toggle="dropdown">
                    <div class="d-float">Select Plot Size <i class="fa-solid fa-ellipsis-vertical fa-lg"></i></div>
                    <ul class="dropdown-menu dropdown-menu-end">

                    </ul>
                </div>
            </div>
        </div>


        <!-- AG Grid Table Section -->
        <div class="container bg-white p-4 rounded-lg shadow-sm my-4 container-table">
            <h5 class="mb-3" id="table_title">Lahore - 2667</h5>

            <!-- AG Grid Container -->
            <div id="allocationGrid" class="ag-theme-alpine" style="height: 500px; width: 100%;"></div>
        </div>

    </div>

    <!-- ... existing modals ... -->
    <!-- Total Price Modal -->
    <div class="modal fade" id="totalPriceModal" tabindex="-1" aria-labelledby="totalPriceModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-light-blue text-dark">
                <div class="modal-header">
                    <h5 class="modal-title" id="totalPriceModalLabel">
                        <i class="fa-solid fa-money-bill-wave"></i> Total Price Details
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <h6>PKR 175,500,000.</h6>
                    The total price of the project is PKR 548,000,000. You can add a detailed breakdown here if
                    required.
                </div>
            </div>
        </div>
    </div>

    <!-- Paid Till Date Modal -->
    <div class="modal fade" id="paidTillDateModal" tabindex="-1" aria-labelledby="paidTillDateModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-light-green text-dark">
                <div class="modal-header">
                    <h5 class="modal-title" id="paidTillDateModalLabel">
                        <i class="fas fa-check-circle"></i> Paid Till Date Details
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <h6>PKR 175,500,000.</h6>
                    Paid till date: PKR 372,500,000. Add payment history or remarks here if needed.
                </div>
            </div>
        </div>
    </div>

    <!-- Due Till Date Modal -->
    <div class="modal fade" id="dueTillDateModal" tabindex="-1" aria-labelledby="dueTillDateModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-light-orange text-dark">
                <div class="modal-header">
                    <h5 class="modal-title" id="dueTillDateModalLabel">
                        <i class="fas fa-exclamation-circle"></i> Due Till Date Details
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <h6>PKR 175,500,000.</h6>
                    Due till date: PKR 175,500,000. Add due breakdown or payment plan notes here.
                </div>
            </div>
        </div>
    </div>

    <!-- Total Balance Modal -->
    <div class="modal fade" id="totalBalanceModal" tabindex="-1" aria-labelledby="totalBalanceModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-light-purple text-dark">
                <div class="modal-header">
                    <h5 class="modal-title" id="totalBalanceModalLabel">
                        <i class="fas fa-calculator"></i> Total Balance Details
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <h6>PKR 175,500,000.</h6>
                    Total balance remaining: PKR 175,500,000. Insert cash flow projections or further notes here.
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="accommodationModal" tabindex="-1" aria-labelledby="accommodationModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content bg-dark text-light p-3 rounded">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold" id="accommodationModalLabel">Accommodation Facilities</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">

                        <div class="col-md-6 col-lg-4 aos-init aos-animate" data-aos="fade-down" data-bs-toggle="modal"
                            data-bs-target="#accommodationModal">
                            <div class="card-blue-shade card-custom h-100 d-flex flex-column justify-content-between">
                                <div class="d-flex justify-content-between align-items-top">
                                    <div>
                                        <h6 class="mb-0">Married Officers Accomodation </h6>
                                        <div class="small-text">Waiting List</div>
                                    </div>

                                    <div class="icon-circle">
                                        <i class="bi bi-download"></i>
                                    </div>
                                </div>
                                <div>

                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4 aos-init aos-animate" data-aos="fade-down" data-bs-toggle="modal"
                            data-bs-target="#accommodationModal">
                            <div class="card-blue-shade card-custom h-100 d-flex flex-column justify-content-between">
                                <div class="d-flex justify-content-between align-items-top">
                                    <div>
                                        <h6 class="mb-0">JC0's, Airmen & Civilians</h6>
                                        <div class="small-text"> Waiting List</div>
                                    </div>

                                    <div class="icon-circle">
                                        <i class="bi bi-download"></i>
                                    </div>
                                </div>
                                <div>


                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4 aos-init aos-animate" data-aos="fade-down" data-bs-toggle="modal"
                            data-bs-target="#accommodationModal">
                            <div class="card-blue-shade card-custom h-100 d-flex flex-column justify-content-between">
                                <div class="d-flex justify-content-between align-items-top">
                                    <div>
                                        <h6 class="mb-0">AD Admin(U) Married Officers</h6>
                                        <div class="small-text">Waiting List</div>
                                    </div>

                                    <div class="icon-circle">
                                        <i class="bi bi-download"></i>
                                    </div>
                                </div>
                                <div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Login Modal -->
    <?php if (!$is_sessioned): ?>
    <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true"
        data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="loginModalLabel">Login Required</h5>
                </div>
                <div class="modal-body">
                    <form id="loginForm">
                        <div class="mb-3">
                            <label for="loginUsername" class="form-label">Username</label>
                            <input type="text" class="form-control" id="loginUsername" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="loginPassword" class="form-label">Password</label>
                            <input type="password" class="form-control" id="loginPassword" name="password" required>
                        </div>
                        <div class="text-danger mb-2" id="loginError" style="display:none;"></div>
                        <button type="submit" class="btn btn-primary w-100">Login</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/script.js"></script>

    <script src="assets/js/crypto-js.js"></script>

    <script>
    // AG Grid Configuration
    let gridApi;

    // Column definitions for AG Grid (Community version compatible)
    const columnDefs = [{
            headerName: "Plot #",
            field: "plotno",
            width: 100,
            filter: 'agTextColumnFilter',
            sortable: true
        },
        {
            headerName: "Image",
            field: "mimg",
            width: 120,
            cellRenderer: function(params) {
                if (params.value && params.value.trim() !== '') {
                    return `<div style="position: relative; width: 60px; height: 40px;">
                                <img src="${params.value}" alt="Plot Image" style="width: 60px; height: 40px; object-fit: cover; border-radius: 4px; border: 1px solid #ddd;" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <div style="display: none; width: 60px; height: 40px; background: #f0f0f0; border-radius: 4px; align-items: center; justify-content: center; font-size: 10px; color: #666; position: absolute; top: 0; left: 0;">
                                    <div style="width: 60px; height: 40px; background: #f0f0f0; border-radius: 4px; display: flex; align-items: center; justify-content: center; font-size: 10px; color: #666;"></div>
                                </div>
                            </div>`;
                } else {
                    return `<div style="width: 60px; height: 40px; background: #f0f0f0; border-radius: 4px; display: flex; align-items: center; justify-content: center;">
                                <div style="width: 60px; height: 40px; background: #f0f0f0; border-radius: 4px; display: flex; align-items: center; justify-content: center; font-size: 10px; color: #666;">No Image</div>
                            </div>`;
                }
            },
            filter: false,
            sortable: false
        },
        {
            headerName: "Owner Name",
            field: "owner_name",
            width: 200,
            filter: 'agTextColumnFilter',
            sortable: true
        },
        {
            headerName: "Category",
            field: "category",
            width: 120,
            cellRenderer: function(params) {
                let flag = "";
                if (params.value === "Officers") {
                    flag = "officer";
                } else if (params.value === "Civilian") {
                    flag = "civilian";
                } else if (params.value === "Retired") {
                    flag = "retired";
                }
                return `<span class="badge badge-${flag}">${params.value || 'N/A'}</span>`;
            },
            filter: 'agTextColumnFilter',
            sortable: true
        },
        {
            headerName: "Location",
            field: "location",
            width: 150,
            filter: 'agTextColumnFilter',
            sortable: true
        },
        {
            headerName: "Plot Size",
            field: "plot_size",
            width: 120,
            filter: 'agTextColumnFilter',
            sortable: true
        },
        {
            headerName: "Total Price",
            field: "total_price",
            width: 150,
            cellRenderer: function(params) {
                return params.value ? Number(params.value).toLocaleString() : '0';
            },
            filter: 'agNumberColumnFilter',
            sortable: true
        },
        {
            headerName: "Paid Till Date",
            field: "paid_till_date",
            width: 150,
            cellRenderer: function(params) {
                return params.value ? Number(params.value).toLocaleString() : '0';
            },
            filter: 'agNumberColumnFilter',
            sortable: true
        },
        {
            headerName: "Due Till Date",
            field: "due_till_date",
            width: 150,
            cellRenderer: function(params) {
                return params.value ? Number(params.value).toLocaleString() : '0';
            },
            filter: 'agNumberColumnFilter',
            sortable: true
        },
        {
            headerName: "Total Balance",
            field: "total_balance",
            width: 150,
            cellRenderer: function(params) {
                return params.value ? Number(params.value).toLocaleString() : '0';
            },
            filter: 'agNumberColumnFilter',
            sortable: true
        },
        {
            headerName: "Allocation Date",
            field: "allocation_date",
            width: 150,
            filter: 'agDateColumnFilter',
            sortable: true
        },
        {
            headerName: "Status",
            field: "status",
            width: 120,
            cellRenderer: function(params) {
                let status = "active";
                if (params.value === "Cancel") {
                    status = "pending";
                }
                return `<span class="status-${status}">${params.value || 'N/A'}</span>`;
            },
            filter: 'agTextColumnFilter',
            sortable: true
        }
    ];

    // Grid options (Community version compatible)
    const gridOptions = {
        columnDefs: columnDefs,
        defaultColDef: {
            resizable: true,
            sortable: true,
            filter: true
        },
        rowData: [],
        pagination: true,
        paginationPageSize: 20,
        paginationPageSizeSelector: [10, 20, 50, 100, 200],
        suppressRowClickSelection: true,
        rowSelection: 'multiple',
        animateRows: true,
        onGridReady: function(params) {
            gridApi = params.api;
        }
    };

    // Initialize AG Grid using new API
    function initializeGrid() {
        const gridDiv = document.querySelector('#allocationGrid');
        gridApi = agGrid.createGrid(gridDiv, gridOptions);
    }

    // Enhanced shimmer CSS for different backgrounds
    if (!$('style#shimmer-style').length) {
        $('head').append(`
                <style id="shimmer-style">
                .shimmer-light {
                    position: relative;
                    overflow: hidden;
                    background: #e0e0e0 !important;
                    border-radius: 6px;
                }
                .shimmer-light::after {
                    content: '';
                    position: absolute;
                    top: 0; left: 0; right: 0; bottom: 0;
                    background: linear-gradient(90deg, #e0e0e0 0%, #f5f5f5 50%, #e0e0e0 100%);
                    opacity: 0.8;
                    animation: shimmer 1.2s infinite;
                }
                .shimmer-amount {
                    position: relative;
                    overflow: hidden;
                    background: rgba(255,255,255,0.5) !important;
                    border-radius: 6px;
                }
                .shimmer-amount::after {
                    content: '';
                    position: absolute;
                    top: 0; left: 0; right: 0; bottom: 0;
                    background: linear-gradient(90deg, rgba(255,255,255,0.5) 0%, #f5f5f5 50%, rgba(255,255,255,0.5) 100%);
                    opacity: 0.7;
                    animation: shimmer 1.2s infinite;
                }
                .shimmer-location {
                    position: relative;
                    overflow: hidden;
                    background: #d1d5db !important;
                    border-radius: 8px;
                }
                .shimmer-location::after {
                    content: '';
                    position: absolute;
                    top: 0; left: 0; right: 0; bottom: 0;
                    background: linear-gradient(90deg, #d1d5db 0%, #f3f4f6 50%, #d1d5db 100%);
                    opacity: 0.8;
                    animation: shimmer 1.2s infinite;
                }
                @keyframes shimmer {
                    0% { transform: translateX(-100%); }
                    100% { transform: translateX(100%); }
                }
                
                /* Enhanced AG Grid Styling */
                .ag-theme-alpine {
                    --ag-header-background-color: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    --ag-header-foreground-color: #ffffff;
                    --ag-header-cell-hover-background-color: rgba(255, 255, 255, 0.1);
                    --ag-header-cell-moving-background-color: rgba(255, 255, 255, 0.1);
                    --ag-border-color: #e1e5e9;
                    --ag-row-hover-color: #f8f9ff;
                    --ag-selected-row-background-color: #e3f2fd;
                    --ag-odd-row-background-color: #ffffff;
                    --ag-even-row-background-color: #fafbff;
                    --ag-cell-horizontal-border: solid #f0f0f0;
                    --ag-row-border-color: #f0f0f0;
                    --ag-font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                    --ag-font-size: 14px;
                    --ag-header-height: 48px;
                    --ag-row-height: 42px;
                    --ag-cell-horizontal-padding: 16px;
                    --ag-header-cell-horizontal-padding: 16px;
                    border-radius: 12px;
                    overflow: hidden;
                    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
                    border: 1px solid #e1e5e9;
                }
                
                .ag-theme-alpine .ag-header {
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    border-bottom: 2px solid #5a67d8;
                    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
                }
                
                .ag-theme-alpine .ag-header-cell {
                    font-weight: 600;
                    font-size: 13px;
                    text-transform: uppercase;
                    letter-spacing: 0.5px;
                    color: #ffffff;
                    border-right: 1px solid rgba(255, 255, 255, 0.2);
                    transition: all 0.3s ease;
                }
                
                .ag-theme-alpine .ag-header-cell:hover {
                    background-color: rgba(255, 255, 255, 0.1);
                    transform: translateY(-1px);
                }
                
                .ag-theme-alpine .ag-header-cell-label {
                    color: #ffffff;
                    font-weight: 600;
                }
                
                .ag-theme-alpine .ag-header-cell-menu-button {
                    color: #ffffff;
                }
                
                .ag-theme-alpine .ag-header-cell-filter-button {
                    color: #ffffff;
                }
                
                .ag-theme-alpine .ag-row {
                    border-bottom: 1px solid #f0f0f0;
                    transition: all 0.2s ease;
                }
                
                .ag-theme-alpine .ag-row:hover {
                    background-color: #f8f9ff !important;
                    transform: translateY(-1px);
                    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
                }
                
                .ag-theme-alpine .ag-row-selected {
                    background-color: #e3f2fd !important;
                    border-left: 4px solid #2196f3;
                }
                
                .ag-theme-alpine .ag-row-selected:hover {
                    background-color: #e3f2fd !important;
                }
                
                .ag-theme-alpine .ag-cell {
                    border-right: 1px solid #f0f0f0;
                    display: flex;
                    align-items: center;
                    font-size: 14px;
                    color: #374151;
                    line-height: 1.5;
                }
                
                .ag-theme-alpine .ag-cell-focus {
                    border: 2px solid #667eea !important;
                    border-radius: 4px;
                }
                
                /* Custom Badge Styling */
                .badge-officer {
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    color: white;
                    padding: 6px 12px;
                    border-radius: 20px;
                    font-size: 0.75em;
                    font-weight: 600;
                    text-transform: uppercase;
                    letter-spacing: 0.5px;
                    box-shadow: 0 2px 4px rgba(102, 126, 234, 0.3);
                    border: none;
                }
                
                .badge-civilian {
                    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
                    color: white;
                    padding: 6px 12px;
                    border-radius: 20px;
                    font-size: 0.75em;
                    font-weight: 600;
                    text-transform: uppercase;
                    letter-spacing: 0.5px;
                    box-shadow: 0 2px 4px rgba(79, 172, 254, 0.3);
                    border: none;
                }
                
                .badge-retired {
                    background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
                    color: white;
                    padding: 6px 12px;
                    border-radius: 20px;
                    font-size: 0.75em;
                    font-weight: 600;
                    text-transform: uppercase;
                    letter-spacing: 0.5px;
                    box-shadow: 0 2px 4px rgba(250, 112, 154, 0.3);
                    border: none;
                }
                
                /* Status Styling */
                .status-active {
                    background: linear-gradient(135deg, #56ab2f 0%, #a8e6cf 100%);
                    color: white;
                    padding: 6px 12px;
                    border-radius: 20px;
                    font-size: 0.75em;
                    font-weight: 600;
                    text-transform: uppercase;
                    letter-spacing: 0.5px;
                    box-shadow: 0 2px 4px rgba(86, 171, 47, 0.3);
                    border: none;
                }
                
                .status-pending {
                    background: linear-gradient(135deg, #ff416c 0%, #ff4b2b 100%);
                    color: white;
                    padding: 6px 12px;
                    border-radius: 20px;
                    font-size: 0.75em;
                    font-weight: 600;
                    text-transform: uppercase;
                    letter-spacing: 0.5px;
                    box-shadow: 0 2px 4px rgba(255, 65, 108, 0.3);
                    border: none;
                }
                
                /* Filter Panel Styling */
                .ag-theme-alpine .ag-filter-toolpanel-header {
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    color: white;
                    border-radius: 8px 8px 0 0;
                }
                
                .ag-theme-alpine .ag-filter-toolpanel-group-title {
                    background: #f8f9ff;
                    color: #374151;
                    font-weight: 600;
                    border-radius: 6px;
                    margin: 4px 0;
                }
                
                /* Pagination Styling */
                .ag-theme-alpine .ag-paging-panel {
                    background: #f8f9ff;
                    border-top: 1px solid #e1e5e9;
                    padding: 16px;
                    border-radius: 0 0 12px 12px;
                }
                
                .ag-theme-alpine .ag-paging-button {
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    color: white;
                    border: none;
                    border-radius: 6px;
                    padding: 8px 12px;
                    margin: 0 4px;
                    font-weight: 600;
                    transition: all 0.3s ease;
                }
                
                .ag-theme-alpine .ag-paging-button:hover {
                    transform: translateY(-2px);
                    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
                }
                
                .ag-theme-alpine .ag-paging-button:disabled {
                    background: #e5e7eb;
                    color: #9ca3af;
                    transform: none;
                    box-shadow: none;
                }
                
                .ag-theme-alpine .ag-paging-row-summary-panel {
                    color: #6b7280;
                    font-weight: 500;
                }
                
                /* Loading Overlay */
                .ag-theme-alpine .ag-overlay-loading-wrapper {
                    background: rgba(255, 255, 255, 0.9);
                    border-radius: 12px;
                }
                
                .ag-theme-alpine .ag-overlay-loading-center {
                    color: #667eea;
                    font-weight: 600;
                }
                
                /* Scrollbar Styling */
                .ag-theme-alpine ::-webkit-scrollbar {
                    width: 8px;
                    height: 8px;
                }
                
                .ag-theme-alpine ::-webkit-scrollbar-track {
                    background: #f1f1f1;
                    border-radius: 4px;
                }
                
                .ag-theme-alpine ::-webkit-scrollbar-thumb {
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    border-radius: 4px;
                }
                
                .ag-theme-alpine ::-webkit-scrollbar-thumb:hover {
                    background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%);
                }
                
                /* Container Styling */
                .container-table {
                    background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%);
                    border-radius: 16px;
                    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
                    border: 1px solid #e1e5e9;
                    overflow: hidden;
                }
                
                .container-table h5 {
                    color: #374151;
                    font-weight: 700;
                    margin-bottom: 20px;
                    padding-bottom: 12px;
                    border-bottom: 2px solid #e1e5e9;
                    position: relative;
                }
                
                .container-table h5::after {
                    content: '';
                    position: absolute;
                    bottom: -2px;
                    left: 0;
                    width: 60px;
                    height: 2px;
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                }
                
                /* Responsive Design */
                @media (max-width: 768px) {
                    .ag-theme-alpine {
                        --ag-header-height: 40px;
                        --ag-row-height: 36px;
                        --ag-cell-horizontal-padding: 12px;
                        --ag-header-cell-horizontal-padding: 12px;
                        --ag-font-size: 12px;
                    }
                    
                    .badge-officer, .badge-civilian, .badge-retired,
                    .status-active, .status-pending {
                        padding: 4px 8px;
                        font-size: 0.7em;
                    }
                }
                
                /* Animation for smooth transitions */
                .ag-theme-alpine * {
                    transition: all 0.2s ease;
                }
                
                /* Custom focus styles */
                .ag-theme-alpine .ag-header-cell:focus,
                .ag-theme-alpine .ag-cell:focus {
                    outline: 2px solid #667eea;
                    outline-offset: 2px;
                }
                </style>
            `);
    }

    // Utility to show shimmer in project tabs (light background)
    function showProjectTabsShimmer() {
        const projectTabs = $('#projectTabs');
        projectTabs.empty();
        for (let i = 0; i < 2; i++) {
            projectTabs.append(
                '<li class="nav-item"><button class="nav-link shimmer-light" style="height:38px;width:160px;margin:4px 0;border-radius:6px;"></button></li>'
            );
        }
    }

    // Utility to show shimmer in locations (location background)
    function showLocationsShimmer() {
        const locationsContainer = $('.locations-container');
        locationsContainer.empty();
        for (let i = 0; i < 4; i++) {
            locationsContainer.append(
                '<div class="location-btn shimmer-location" style="width:110px;height:48px;border-radius:8px;margin:2px 8px 2px 0;"></div>'
            );
        }
    }

    // Utility to show shimmer in AG Grid
    function showGridShimmer() {
        if (gridApi) {
            gridApi.showLoadingOverlay();
        }
    }

    // Utility to hide shimmer in AG Grid
    function hideGridShimmer() {
        if (gridApi) {
            gridApi.hideOverlay();
        }
    }

    // Utility to show shimmer in amount cards (colored background)
    function showAmountShimmer() {
        $('#totalPrice, #paidTillDate, #dueTillDate, #totalBalance').each(function() {
            $(this).html(
                '<span class="shimmer-amount" style="display:inline-block;width:80px;height:1em;border-radius:4px;"></span>'
            );
        });
    }

    // Store the latest amounts globally for modal use
    let latestAmounts = {};

    function hideAmountShimmer(amounts) {
        if (amounts) {
            latestAmounts = amounts; // Save for modal use
            $('#totalPrice').text(amounts.total_price.toLocaleString());
            $('#paidTillDate').text(amounts.paid_till_date.toLocaleString());
            $('#dueTillDate').text(amounts.due_till_date.toLocaleString());
            $('#totalBalance').text(amounts.total_balance.toLocaleString());
        }
    }

    // Update modals when cards are clicked
    $(document).on('click', '[data-bs-target="#totalPriceModal"]', function() {
        $('#totalPriceModal .modal-body h6').text((latestAmounts.total_price ? latestAmounts
            .total_price.toLocaleString() : '--'));
        $('#totalPriceModal .modal-body').contents().filter(function() {
            return this.nodeType === 3;
        }).remove();
        $('#totalPriceModal .modal-body').append('The total price of the project is PKR ' + (latestAmounts
                .total_price ? latestAmounts.total_price.toLocaleString() : '--') +
            '. You can add a detailed breakdown here if required.');
    });
    $(document).on('click', '[data-bs-target="#paidTillDateModal"]', function() {
        $('#paidTillDateModal .modal-body h6').text((latestAmounts.paid_till_date ? latestAmounts
            .paid_till_date.toLocaleString() : '--'));
        $('#paidTillDateModal .modal-body').contents().filter(function() {
            return this.nodeType === 3;
        }).remove();
        $('#paidTillDateModal .modal-body').append('Paid till date: PKR ' + (latestAmounts.paid_till_date ?
                latestAmounts.paid_till_date.toLocaleString() : '--') +
            '. Add payment history or remarks here if needed.');
    });
    $(document).on('click', '[data-bs-target="#dueTillDateModal"]', function() {
        $('#dueTillDateModal .modal-body h6').text((latestAmounts.due_till_date ? latestAmounts
            .due_till_date.toLocaleString() : '--'));
        $('#dueTillDateModal .modal-body').contents().filter(function() {
            return this.nodeType === 3;
        }).remove();
        $('#dueTillDateModal .modal-body').append('Due till date: PKR ' + (latestAmounts.due_till_date ?
                latestAmounts.due_till_date.toLocaleString() : '--') +
            '. Add due breakdown or payment plan notes here.');
    });
    $(document).on('click', '[data-bs-target="#totalBalanceModal"]', function() {
        $('#totalBalanceModal .modal-body h6').text((latestAmounts.total_balance ? latestAmounts
            .total_balance.toLocaleString() : '--'));
        $('#totalBalanceModal .modal-body').contents().filter(function() {
            return this.nodeType === 3;
        }).remove();
        $('#totalBalanceModal .modal-body').append('Total balance remaining: PKR ' + (latestAmounts
                .total_balance ? latestAmounts.total_balance.toLocaleString() : '--') +
            '. Insert cash flow projections or further notes here.');
    });

    // Utility to filter grid by active categories
    function filterGridByCategory() {
        let activeBtns = [];
        try {
            activeBtns = JSON.parse(localStorage.getItem('active_btns') || '[]');
        } catch (e) {
            activeBtns = [];
        }

        // Map button IDs to category names
        const btnToCategory = {
            officerBtn: 'Officers',
            civilianBtn: 'Civilian',
            retiredBtn: 'Retired'
        };

        const activeCategories = activeBtns.map(id => btnToCategory[id]).filter(Boolean);

        if (activeCategories.length === 0) {
            // Show all rows
            if (gridApi) {
                gridApi.setFilterModel(null);
            }
            return;
        }

        // Apply category filter to AG Grid
        if (gridApi) {
            const currentFilterModel = gridApi.getFilterModel() || {};

            if (activeCategories.length === 1) {
                // Single category - use exact match
                currentFilterModel.category = {
                    filterType: 'text',
                    type: 'equals',
                    filter: activeCategories[0]
                };
            } else {
                // Multiple categories - use contains with OR logic
                currentFilterModel.category = {
                    filterType: 'text',
                    type: 'contains',
                    filter: activeCategories.join('|')
                };
            }

            gridApi.setFilterModel(currentFilterModel);
        }
    }

    // Utility to filter grid by plot sizes
    function filterGridBySizes() {
        let selectedSizes = [];
        try {
            selectedSizes = JSON.parse(localStorage.getItem('active_sizes') || '[]');
        } catch (e) {
            selectedSizes = [];
        }

        // Apply size filter to AG Grid
        if (gridApi) {
            const currentFilterModel = gridApi.getFilterModel() || {};

            if (selectedSizes.length === 0) {
                // If no sizes selected, remove size filter
                delete currentFilterModel.plot_size;
            } else if (selectedSizes.length === 1) {
                // Single size - use exact match
                currentFilterModel.plot_size = {
                    filterType: 'text',
                    type: 'equals',
                    filter: selectedSizes[0].name
                };
            } else {
                // Multiple sizes - use array filter
                currentFilterModel.plot_size = {
                    filterType: 'text',
                    type: 'equals',
                    filter: selectedSizes.map(size => size.name)
                };
            }

            gridApi.setFilterModel(currentFilterModel);
        }
    }


    $(document).ready(function() {
        // Initialize AG Grid
        initializeGrid();

        // Restore active buttons from localStorage on page load
        restoreActiveButtons();

        projects();

        function restoreActiveButtons() {
            try {
                const activeBtns = JSON.parse(localStorage.getItem('active_btns') || '[]');
                if (activeBtns.length > 0) {
                    // Remove active class from all buttons first
                    $('.toggle-switch button').removeClass('active');
                    // Set the saved button as active
                    const activeBtnId = activeBtns[0]; // Get the first (and only) active button
                    $(`#${activeBtnId}`).addClass('active');
                } else {
                    // If no saved buttons, set the first button (officerBtn) as active by default
                    $('.toggle-switch button').removeClass('active');
                    $('#officerBtn').addClass('active');
                    localStorage.setItem('active_btns', JSON.stringify(['officerBtn']));
                }
            } catch (e) {
                // If there's an error parsing, set officerBtn as default
                $('.toggle-switch button').removeClass('active');
                $('#officerBtn').addClass('active');
                localStorage.setItem('active_btns', JSON.stringify(['officerBtn']));
            }
        }

        function projects() {
            showProjectTabsShimmer();
            const formData = new FormData();
            formData.append('action', 'projects');
            fetch('post_requests/post_apis.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    const projectTabs = $('#projectTabs');
                    projectTabs.empty();
                    if (data.status === 'success') {
                        let activeProjectId = localStorage.getItem('active_pro');
                        if (!activeProjectId && data.data.length > 0) {
                            activeProjectId = data.data[0].id;
                            localStorage.setItem('active_pro', activeProjectId);
                        }
                        data.data.forEach((project, idx) => {
                            const isActive = (activeProjectId == project.id) ? 'active' : '';
                            const tabId = `project-tab-${project.id}`;
                            const tabHtml = `
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link d-flex align-items-center gap-2 fs-5 ${isActive}" 
                                        id="${tabId}"
                                        data-project-id="${project.id}"
                                        data-bs-toggle="tab" 
                                        data-bs-target="#project-content-${project.id}" 
                                        type="button" 
                                        aria-selected="${isActive ? 'true' : 'false'}"
                                        role="tab">
                                        ${project.icon ? project.icon : ''} ${project.name}
                                    </button>
                                </li>
                            `;
                            projectTabs.append(tabHtml);
                        });

                        // Attach click handler for project tab change
                        $('#projectTabs .nav-link').on('click', function() {
                            const projectId = $(this).data('project-id');
                            localStorage.setItem('active_pro', projectId);
                            $('#projectTabs .nav-link').removeClass('active');
                            $(this).addClass('active');
                            fetchLocations(projectId);
                        });

                        // Fetch locations for the active project
                        fetchLocations(activeProjectId);
                    }
                });
        }

        function fetchLocations(projectId) {
            showLocationsShimmer();

            const formData = new FormData();
            formData.append('action', 'locations');
            formData.append('project', projectId);

            fetch('post_requests/post_apis.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    const locationsContainer = $('.locations-container');
                    locationsContainer.empty();

                    if (data.status === 'success') {
                        const locations = data.data || [];
                        let activeLocation = localStorage.getItem('active_location');

                        // Set the first location as active if none is set
                        if (!activeLocation && locations.length > 0) {
                            activeLocation = locations[0].name;
                            localStorage.setItem('active_location', activeLocation);
                        }

                        locations.forEach(location => {
                            const isActive = (activeLocation === location.name) ? 'active' : '';

                            // If current location is active, update the table title
                            if (isActive) {
                                document.getElementById('table_title').innerHTML =
                                    `${location.name} - ${location.count || ''}`;
                            }

                            const locationHtml = `
                    <div class="location-btn ${isActive}" data-location-name="${location.name}">
                        <div class="text-start">
                            <small>${location.count || ''}</small><br>
                            ${location.name}
                        </div>
                        <i class="fas fa-map-marker-alt text-primary"></i>
                    </div>
                `;
                            locationsContainer.append(locationHtml);
                        });

                        // Attach click handler for location buttons
                        $('.locations-container .location-btn').on('click', function() {
                            const locationName = $(this).data('location-name');
                            localStorage.setItem('active_location', locationName);

                            $('.locations-container .location-btn').removeClass('active');
                            $(this).addClass('active');

                            // Update table title on click
                            const count = $(this).find('small').text();
                            document.getElementById('table_title').innerHTML =
                                `${locationName} - ${count}`;

                            fetchAmounts(projectId, locationName);
                            fetchSizes(projectId, locationName);
                        });

                        // Initial fetch for amounts and sizes
                        if (activeLocation && projectId) {
                            fetchAmounts(projectId, activeLocation);
                            fetchSizes(projectId, activeLocation);
                        }
                    } else {
                        // Optionally handle error status
                        document.getElementById('table_title').innerHTML = 'No locations found';
                    }
                })
                .catch(error => {
                    console.error('Error fetching locations:', error);
                    document.getElementById('table_title').innerHTML = 'Error loading locations';
                });
        }

        function fetchSizes(projectId, locationName) {
            const formData = new FormData();
            formData.append('action', 'get_sizes');
            formData.append('project', projectId);
            formData.append('location', locationName);

            fetch('post_requests/post_apis.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        const sizes = data.data;
                        const selectedSizesSummary = $('#selectedSizesSummary');
                        const plotDropdownMenu = $('#plotDropdown .dropdown-menu');
                        plotDropdownMenu.empty();
                        // Restore selected sizes from localStorage
                        let selectedSizes = [];
                        let savedSizes = [];
                        try {
                            savedSizes = JSON.parse(localStorage.getItem('active_sizes') || '[]');
                        } catch (e) {
                            savedSizes = [];
                        }
                        if (sizes && sizes.length > 0) {
                            sizes.forEach(item => {
                                const size = item.size;
                                const count = item.count_plots;
                                const sizeId = size.id;
                                const sizeName = size.size;
                                const dropdownItem = $(`
                                        <li><a class="dropdown-item" href="#" data-size-id="${sizeId}" data-size-name="${sizeName}">
                                            <strong>${count}</strong> <br> ${sizeName}
                                        </a></li>
                                    `);
                                // If this size is in savedSizes, mark as active
                                if (savedSizes.some(s => s.id == sizeId)) {
                                    dropdownItem.find('a').addClass('active');
                                    selectedSizes.push({
                                        id: sizeId,
                                        name: sizeName
                                    });
                                }
                                plotDropdownMenu.append(dropdownItem);
                            });
                            // Click handler for dropdown selection
                            plotDropdownMenu.off('click').on('click', '.dropdown-item', function(e) {
                                e.preventDefault();
                                const sizeId = $(this).data('size-id');
                                const sizeName = $(this).data('size-name');
                                $(this).toggleClass('active');

                                // Update selectedSizes array and localStorage
                                let currentSizes = [];
                                try {
                                    currentSizes = JSON.parse(localStorage.getItem(
                                        'active_sizes') || '[]');
                                } catch (e) {
                                    currentSizes = [];
                                }

                                if ($(this).hasClass('active')) {
                                    // Add to selection if not already present
                                    if (!currentSizes.some(s => s.id == sizeId)) {
                                        currentSizes.push({
                                            id: sizeId,
                                            name: sizeName
                                        });
                                    }
                                } else {
                                    // Remove from selection
                                    currentSizes = currentSizes.filter(s => s.id != sizeId);
                                }

                                localStorage.setItem('active_sizes', JSON.stringify(currentSizes));
                                updateSelectedSizesSummary(currentSizes);

                                // Apply size filter to grid
                                // filterGridBySizes(); // Removed - server handles filtering

                                // Call memberships API with current selections
                                const projectId = localStorage.getItem('active_pro');
                                const locationName = localStorage.getItem('active_location');
                                fetchMemberships(projectId, locationName, currentSizes);
                            });
                            // Initial summary and memberships
                            localStorage.setItem('active_sizes', JSON.stringify(selectedSizes));
                            updateSelectedSizesSummary(selectedSizes);
                            const projectId = localStorage.getItem('active_pro');
                            const locationName = localStorage.getItem('active_location');
                            fetchMemberships(projectId, locationName, selectedSizes);
                        } else {
                            selectedSizesSummary.hide();
                        }
                    } else {
                        $('#selectedSizesSummary').hide();
                    }
                });
        }

        function updateSelectedSizesSummary(selectedSizes) {
            const summary = $('#selectedSizesSummary');
            if (!selectedSizes || selectedSizes.length === 0) {
                // Try to restore from localStorage if not provided
                try {
                    selectedSizes = JSON.parse(localStorage.getItem('active_sizes') || '[]');
                } catch (e) {
                    selectedSizes = [];
                }
            }
            if (selectedSizes.length > 0) {
                summary.html('Selected Size: ' + selectedSizes.map(s =>
                    `<span class="badge bg-info text-dark mx-1 selected-size-badge" data-size-id="${s.id}" style="cursor:pointer;">${s.name} <span style=\"font-weight:bold;\">&times;</span></span>`
                ).join(' '));
                summary.show();
                // Add click handler to allow unselecting from summary
                $('.selected-size-badge').off('click').on('click', function() {
                    const sizeId = $(this).data('size-id');
                    // Remove from localStorage
                    let sizes = [];
                    try {
                        sizes = JSON.parse(localStorage.getItem('active_sizes') || '[]');
                    } catch (e) {
                        sizes = [];
                    }
                    sizes = sizes.filter(s => s.id != sizeId);
                    localStorage.setItem('active_sizes', JSON.stringify(sizes));
                    // Unselect in dropdown
                    $('#plotDropdown .dropdown-menu .dropdown-item[data-size-id="' + sizeId + '"]')
                        .removeClass('active');

                    // Apply size filter to grid
                    // filterGridBySizes(); // Removed - server handles filtering

                    // Update summary and call memberships
                    updateSelectedSizesSummary(sizes);
                    const projectId = localStorage.getItem('active_pro');
                    const locationName = localStorage.getItem('active_location');
                    fetchMemberships(projectId, locationName, sizes);
                });
            } else {
                summary.hide();
            }
        }

        function fetchMemberships(projectId, locationName, selectedSizes) {
            showGridShimmer();
            const formData = new FormData();
            formData.append('action', 'get_memberships');
            formData.append('project', projectId);
            formData.append('location', locationName);
            formData.append('sizes', JSON.stringify(selectedSizes.map(s => s.id)));

            // Get active_btns directly from localStorage inside the function
            const activeBtns = localStorage.getItem('active_btns');
            formData.append('active_btns', activeBtns);

            fetch('post_requests/post_apis.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    hideGridShimmer();

                    if (data.status === 'success' && Array.isArray(data.data) && data.data.length > 0) {
                        // Display data directly without any client-side filtering
                        if (gridApi) {
                            gridApi.setGridOption('rowData', data.data);
                        }
                    } else {
                        // Show empty state
                        if (gridApi) {
                            gridApi.setGridOption('rowData', []);
                        }
                    }
                    // Removed client-side filtering - server handles all filtering
                })
                .catch(error => {
                    hideGridShimmer();
                    console.error('Error fetching memberships:', error);
                    if (gridApi) {
                        gridApi.setGridOption('rowData', []);
                    }
                });
        }

        function fetchAmounts(projectId, locationName) {
            showAmountShimmer();
            const formData = new FormData();
            formData.append('action', 'get_amounts');
            formData.append('project', projectId);
            formData.append('location', locationName);

            fetch('post_requests/post_apis.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        // Update the UI with the returned amounts
                        const amounts = data.data;
                        hideAmountShimmer(amounts);
                    }
                });
        }

        $('.toggle-switch button').on('click', function() {
            // Remove active class from all buttons
            $('.toggle-switch button').removeClass('active');
            // Add active class only to the clicked button
            $(this).addClass('active');

            // Update array in localStorage with only the clicked button
            let btns = [$(this).attr('id')];
            localStorage.setItem('active_btns', JSON.stringify(btns));

            // Apply both filters immediately
            filterGridByCategory();
            // filterGridBySizes(); // Removed - server handles filtering

            // Call memberships API with current selections
            const projectId = localStorage.getItem('active_pro');
            const locationName = localStorage.getItem('active_location');
            let selectedSizes = [];
            try {
                selectedSizes = JSON.parse(localStorage.getItem('active_sizes') || '[]');
            } catch (e) {
                selectedSizes = [];
            }
            fetchMemberships(projectId, locationName, selectedSizes);
        });

        <?php if (!$is_sessioned): ?>
        // Show login modal and prevent dashboard interaction
        var loginModal = new bootstrap.Modal(document.getElementById('loginModal'), {
            backdrop: 'static',
            keyboard: false
        });
        loginModal.show();
        // Disable dashboard content
        $('.container, .bg-primary').css('filter', 'blur(2px)').css('pointer-events', 'none');
        // Handle login form submit
        $('#loginForm').on('submit', function(e) {
            e.preventDefault();
            var username = $('#loginUsername').val();
            var password = $('#loginPassword').val();
            $.ajax({
                url: 'post_requests/post_apis.php',
                method: 'POST',
                data: {
                    action: 'login',
                    username: username,
                    password: password
                },
                dataType: 'json',
                success: function(res) {
                    if (res.status === 'success') {
                        // Hide modal, enable dashboard, reload
                        loginModal.hide();
                        $('.container, .bg-primary').css('filter', '').css('pointer-events',
                            '');
                        location.reload();
                    } else {
                        $('#loginError').text(res.message || 'Invalid credentials').show();
                    }
                },
                error: function() {
                    $('#loginError').text('Login failed. Please try again.').show();
                }
            });
        });
        <?php endif; ?>

        $('#logoutBtn').on('click', function(e) {
            e.preventDefault();
            $.ajax({
                url: 'post_requests/post_apis.php',
                method: 'POST',
                data: {
                    action: 'logout'
                },
                dataType: 'json',
                success: function(res) {
                    if (res.status === 'success') {
                        location.reload();
                    }
                },
                error: function() {
                    alert('Logout failed. Please try again.');
                }
            });
        });
    });
    </script>

</body>

</html>
<div class="row" style="margin-top: 3%; min-height: 88vh; overflow: hidden;">
    <!-- Left Sidebar - Modules -->
    <div class="col-md-3" style="padding-right: 20px;">
        <?php include(__DIR__ . '/../pharmacy/_modules_sidebar.php') ?>
    </div>

    <!-- Main Content Area - Reports -->
    <div class="col-md-9" style="padding-left: 20px;">
        <div style="">


            <!-- Reports Grid - 5 per row -->
            <div class="row" id="reportsGrid">
                <?php foreach ($reports as $index => $report): ?>
                <div class="col-xl-2 col-lg-2 col-md-4 col-sm-6 mb-3 report-card"
                    data-category="<?= htmlspecialchars($report['category']) ?>"
                    style="animation-delay: <?= $index * 0.05 ?>s;">
                    <div class="report-item" style="
                            background: white;
                            border-radius: 16px;
                            padding: 16px;
                            border: 2px solid #f8fafc;
                            cursor: pointer;
                            transition: all 0.3s ease;
                            position: relative;
                            overflow: hidden;
                            height: 100%;
                            text-align: center;
                         " onclick="openReport('<?= htmlspecialchars($report['url']) ?>')"
                        onmouseover="this.style.transform='translateY(-4px)'; this.style.borderColor='<?= $report['color'] ?>'; this.style.boxShadow='0 8px 25px rgba(0,0,0,0.1)'"
                        onmouseout="this.style.transform='translateY(0)'; this.style.borderColor='#f8fafc'; this.style.boxShadow='none'">

                        <!-- Top Color Bar -->
                        <div
                            style="position: absolute; top: 0; left: 0; right: 0; height: 3px; background: linear-gradient(90deg, <?= $report['color'] ?> 0%, <?= $report['color'] ?>80 100%);">
                        </div>

                        <!-- Icon -->
                        <div style="
                            width: 48px;
                            height: 48px;
                            background: linear-gradient(135deg, <?= $report['color'] ?>20 0%, <?= $report['color'] ?>10 100%);
                            border-radius: 12px;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            margin: 0 auto 12px auto;
                            transition: all 0.3s ease;
                        ">
                            <i class="<?= htmlspecialchars($report['icon']) ?>"
                                style="font-size: 20px; color: <?= $report['color'] ?>;"></i>
                        </div>

                        <!-- Title -->
                        <h6 style="
                            color: #1e293b;
                            font-weight: 600;
                            font-size: 13px;
                            margin-bottom: 8px;
                            line-height: 1.3;
                            height: 34px;
                            overflow: hidden;
                            display: -webkit-box;
                            -webkit-line-clamp: 2;
                            -webkit-box-orient: vertical;
                        ">
                            <?= htmlspecialchars($report['title']) ?>
                        </h6>

                        <!-- Category Badge -->
                        <div class="mb-2">
                            <span style="
                                background: linear-gradient(135deg, <?= $report['color'] ?>20 0%, <?= $report['color'] ?>10 100%);
                                color: <?= $report['color'] ?>;
                                font-size: 9px;
                                font-weight: 600;
                                padding: 3px 8px;
                                border-radius: 12px;
                                border: 1px solid <?= $report['color'] ?>30;
                            ">
                                <?= htmlspecialchars($report['category']) ?>
                            </span>
                        </div>

                        <!-- Description (truncated) -->
                        <p style="
                            color: #64748b;
                            font-size: 10px;
                            line-height: 1.4;
                            margin-bottom: 12px;
                            height: 28px;
                            overflow: hidden;
                            display: -webkit-box;
                            -webkit-line-clamp: 2;
                            -webkit-box-orient: vertical;
                        ">
                            <?= htmlspecialchars($report['description']) ?>
                        </p>

                        <!-- Footer -->
                        <div style="border-top: 1px solid #f1f5f9; padding-top: 8px;">
                            <div class="d-flex justify-content-between align-items-center">
                                <div style="font-size: 9px; color: #94a3b8;">
                                    <i class="fas fa-clock me-1"></i>
                                    <?= htmlspecialchars($report['frequency']) ?>
                                </div>
                                <div style="
                                    width: 24px;
                                    height: 24px;
                                    background: linear-gradient(135deg, #8B5CF6 0%, #7C3AED 100%);
                                    border-radius: 50%;
                                    display: flex;
                                    align-items: center;
                                    justify-content: center;
                                    color: white;
                                    font-size: 10px;
                                    transition: all 0.3s ease;
                                ">
                                    <i class="fas fa-arrow-right"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- No Reports Message -->
            <div id="noReportsMessage" class="text-center py-4" style="display: none;">
                <div
                    style="width: 80px; height: 80px; background: #f8fafc; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 16px;">
                    <i class="fas fa-search" style="font-size: 32px; color: #94a3b8;"></i>
                </div>
                <h6 style="color: #64748b; margin-bottom: 4px;">No reports found</h6>
                <p style="color: #94a3b8; font-size: 12px; margin-bottom: 0;">Try adjusting your search criteria</p>
            </div>
        </div>
    </div>
</div>

<style>
/* Report Card Animations */
@keyframes slideInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.report-card {
    animation: slideInUp 0.4s ease forwards;
    opacity: 0;
}

.report-card.hidden {
    display: none;
}

/* Loading Spinner */
.loading-spinner {
    border: 2px solid #f3f3f3;
    border-top: 2px solid #8B5CF6;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% {
        transform: rotate(0deg);
    }

    100% {
        transform: rotate(360deg);
    }
}

/* Responsive Design */
@media (max-width: 1200px) {
    .report-card {
        flex: 0 0 20%;
    }
}

@media (max-width: 992px) {
    .report-card {
        flex: 0 0 33.333%;
    }
}

@media (max-width: 768px) {
    .report-card {
        flex: 0 0 50%;
    }
}

@media (max-width: 576px) {
    .report-card {
        flex: 0 0 100%;
    }
}
</style>

<script>
function openReport(url) {
    const reportItem = event.currentTarget;
    reportItem.style.opacity = '0.7';
    reportItem.style.pointerEvents = 'none';

    const loadingSpinner = document.createElement('div');
    loadingSpinner.className = 'loading-spinner';
    loadingSpinner.style.position = 'absolute';
    loadingSpinner.style.top = '50%';
    loadingSpinner.style.left = '50%';
    loadingSpinner.style.transform = 'translate(-50%, -50%)';
    loadingSpinner.style.zIndex = '10';
    reportItem.style.position = 'relative';
    reportItem.appendChild(loadingSpinner);

    setTimeout(() => {
        window.location.href = 'index.php?r=' + url;
    }, 300);
}

document.addEventListener('DOMContentLoaded', function() {
    const reportCards = document.querySelectorAll('.report-card');

    // Add smooth animations on load
    reportCards.forEach((card, index) => {
        card.style.animationDelay = `${index * 0.05}s`;
        card.style.animation = 'slideInUp 0.4s ease forwards';
    });
});
</script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
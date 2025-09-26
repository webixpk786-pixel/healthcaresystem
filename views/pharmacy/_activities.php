<link rel="stylesheet" href="css/styles.css" />
<style>
    .shimmer-wrapper {
        width: 100%;
        height: 120px;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .shimmer {
        background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 37%, #f0f0f0 63%);
        background-size: 400% 100%;
        animation: shimmer 1.2s linear infinite;
        border-radius: 8px;
        height: 32px;
        width: 100%;
    }

    @keyframes shimmer {
        0% {
            background-position: -400px 0;
        }

        100% {
            background-position: 400px 0;
        }
    }

    .activity-card {
        background: #fff;
        border-radius: 12px;
        padding: 16px;
        margin-bottom: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        border-left: 4px solid #646cff;
        transition: all 0.3s ease;
    }

    .activity-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
    }

    .activity-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 8px;
    }

    .activity-time {
        font-size: 11px;
        color: #888;
        background: #f5f5f5;
        padding: 2px 8px;
        border-radius: 12px;
    }

    .inventory-alert {
        border-left-color: #f59e0b;
        background: linear-gradient(135deg, #fef3c7 0%, #ffffff 100%);
    }

    .expiry-alert {
        border-left-color: #ef4444;
        background: linear-gradient(135deg, #fecaca 0%, #ffffff 100%);
    }

    .sales-activity {
        border-left-color: #22c55e;
        background: linear-gradient(135deg, #dcfce7 0%, #ffffff 100%);
    }

    .stock-update {
        border-left-color: #3b82f6;
        background: linear-gradient(135deg, #dbeafe 0%, #ffffff 100%);
    }
</style>

<div class="col-md-3">
    <div class="dashboard-flex-col dashboard-flex-right"
        style="background: #fff; border-radius: 18px; padding: 0px; margin: 0px 40px 24px 0px; display: flex; flex-direction: column; position: relative; overflow: hidden; max-height: 600px;">
        <div
            style="padding: 20px 24px 16px; border-bottom: 1px solid #eee; display: flex; align-items: center; justify-content: space-between;">
            <h3 style="margin: 0; color: #232360; font-size: 18px; font-weight: 700;">Recent Activities</h3>
            <div class="row" style="margin-right:0; margin-left:0;">
                <div class="col-7 pr-1" style="padding-right:4px;">
                    <input id="activity-search" type="text" class="input form-control-sm" placeholder="Search...">
                </div>
                <div class="col-5 pl-1" style="padding-left:4px;">
                    <input id="activity-month" type="month" value="<?= date('Y-m') ?>" class="input form-control-sm">
                </div>
            </div>
        </div>
        <div id="activities-list" style="flex: 2 1 0%; padding: 16px 24px 24px; max-height: 400px; overflow: auto;">
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function renderPharmacyActivity(activity) {
        let name = (activity.first_name || '') + ' ' + (activity.last_name || '');
        let icon, iconColor, cardClass = 'activity-card';

        // Determine activity type and styling
        switch (activity.action) {
            case 'Login':
                icon = 'fa-sign-in-alt';
                iconColor = '#4caf50';
                break;
            case 'Logout':
                icon = 'fa-sign-out-alt';
                iconColor = '#ff9800';
                break;
            case 'Update':
                icon = 'fa-edit';
                iconColor = '#2196f3';
                break;
            case 'Delete':
                icon = 'fa-trash';
                iconColor = '#f44336';
                break;
            case 'View':
                icon = 'fa-eye';
                iconColor = '#9e9e9e';
                break;
            case 'Stock Update':
                icon = 'fa-boxes';
                iconColor = '#3b82f6';
                cardClass += ' stock-update';
                break;
            case 'Low Stock':
                icon = 'fa-exclamation-triangle';
                iconColor = '#f59e0b';
                cardClass += ' inventory-alert';
                break;
            case 'Expiry Alert':
                icon = 'fa-clock';
                iconColor = '#ef4444';
                cardClass += ' expiry-alert';
                break;
            case 'Sale':
                icon = 'fa-cash-register';
                iconColor = '#22c55e';
                cardClass += ' sales-activity';
                break;
            default:
                icon = 'fa-info-circle';
                iconColor = '#9e9e9e';
                break;
        }

        return `<div class="${cardClass}">
            <div class="activity-header">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="width: 36px; height: 36px; border-radius: 50%; background: ${iconColor}20; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <i class="fas ${icon}" style="color: ${iconColor}; font-size: 16px;"></i>
                    </div>
                    <div>
                        <div style="font-weight: 600; color: #232360; font-size: 14px; line-height: 1.3;">${activity.action}</div>
                        <div style="color: #666; font-size: 12px; line-height: 1.4;">${name ? name + ' ' : ''}${activity.description || ''}</div>
                    </div>
                </div>
                <div class="activity-time">${activity.created_at ? formatDate(activity.created_at) : ''}</div>
            </div>
        </div>`;
    }

    function showShimmer() {
        const shimmer = `<div class="shimmer-wrapper">
            <div class="shimmer"></div>
            <div class="shimmer" style="width: 80%;"></div>
            <div class="shimmer" style="width: 60%;"></div>
        </div>`;
        $('#activities-list').html(shimmer);
    }

    function formatDate(dt) {
        const d = new Date(dt.replace(' ', 'T'));
        if (isNaN(d)) return dt;
        const options = {
            month: 'short',
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit'
        };
        return d.toLocaleString(undefined, options);
    }

    function loadPharmacyActivities() {
        showShimmer();
        const search = $('#activity-search').val();
        const month = $('#activity-month').val();

        $.get('index.php?r=users/activity-logs', {
            search,
            month
        }, function(resp) {
            if (resp.success) {
                // Add some mock pharmacy activities
                const mockActivities = [{
                        action: 'Stock Update',
                        description: 'Updated stock for Paracetamol 500mg',
                        created_at: new Date(Date.now() - 1000 * 60 * 15).toISOString(),
                        first_name: 'John',
                        last_name: 'Doe'
                    },
                    {
                        action: 'Low Stock',
                        description: 'Ibuprofen 400mg is running low',
                        created_at: new Date(Date.now() - 1000 * 60 * 45).toISOString(),
                        first_name: 'System',
                        last_name: 'Alert'
                    },
                    {
                        action: 'Sale',
                        description: 'Processed prescription for Patient #1234',
                        created_at: new Date(Date.now() - 1000 * 60 * 90).toISOString(),
                        first_name: 'Sarah',
                        last_name: 'Wilson'
                    },
                    {
                        action: 'Expiry Alert',
                        description: 'Amoxicillin batch expires in 7 days',
                        created_at: new Date(Date.now() - 1000 * 60 * 120).toISOString(),
                        first_name: 'System',
                        last_name: 'Alert'
                    }
                ];

                const allActivities = [...mockActivities, ...resp.logs];
                const html = allActivities.slice(0, 8).map(renderPharmacyActivity).join('');
                $('#activities-list').html(html ||
                    '<div style="color:#888; text-align:center; margin-top:40px;">No activities found.</div>');
            } else {
                $('#activities-list').html(
                    '<div style="color:red; text-align:center; margin-top:40px;">Failed to load activities.</div>'
                );
            }
        });
    }

    $('#activity-search, #activity-month').on('input change', function() {
        loadPharmacyActivities();
    });

    $(function() {
        loadPharmacyActivities();
    });
</script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
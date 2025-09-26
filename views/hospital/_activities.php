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
</style>
<div class="col-md-3">
    <div class="dashboard-flex-col dashboard-flex-right"
        style="background: #fff; border-radius: 18px; padding: 0px; margin: 0px 40px 24px 0px; display: flex; flex-direction: column; position: relative; overflow: hidden; max-height: 600px;">
        <div style="padding: 5px 0px 5px; border-bottom: 1px solid #eee; display: flex; align-items: center;">
            <div class="row" style="margin-right:0; margin-left:0;">
                <div class="col-7 pr-1" style="padding-right:4px;">
                    <input id="activity-search" type="text" class="input form-control-sm" placeholder="Search...">
                </div>
                <div class="col-5 pl-1" style="padding-left:4px;">
                    <input id="activity-month" type="month" value="<?= date('Y-m') ?>" class="input form-control-sm">
                </div>
            </div>
        </div>
        <div id="activities-list" style="flex: 2 1 0%; padding: 16px 24px 24px; max-height: 200px; overflow: auto;">
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function renderActivity(activity) {
        let name = (activity.first_name || '') + ' ' + (activity.last_name || '');
        let icon, iconColor;
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
            default:
                icon = 'fa-info-circle';
                iconColor = '#9e9e9e';
                break;
        }
        return `<div style="display: flex; align-items: flex-start; gap: 12px; position: relative; margin-bottom: 12px;">
            <div style="width: 32px; height: 32px; border-radius: 50%; background: #fff; display: flex; align-items: center; justify-content: center; box-shadow: rgba(0,0,0,0.1) 0px 2px 6px; flex-shrink: 0; border: 2px solid #e0e0e0;">
                <i class="fas ${icon}" style="color: ${iconColor}; font-size: 16px;"></i>
            </div>
            <div style="flex: 1 1 0%; min-width: 0px;">
                <div style="display: flex; justify-content: flex-start; align-items: center; margin-bottom: 4px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; gap: 8px;">
                    <div style="font-weight: 600; color: #232360; font-size: 12px; line-height: 1.3; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">${activity.action}</div>
                    <div style="color: #888; font-size: 12px; font-weight: 500; white-space: nowrap; margin-left: 8px; flex-shrink: 0;">${activity.created_at ? formatDate(activity.created_at) : ''}</div>
                </div>
                <div style="color: #666; font-size: 9px; line-height: 1.4; text-align: justify;">${name ? name + ' ' : ''}${activity.description || ''}</div>
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
            year: 'numeric',
            month: 'short',
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit'
        };
        return d.toLocaleString(undefined, options);
    }

    function loadActivities() {
        showShimmer();
        const search = $('#activity-search').val();
        const month = $('#activity-month').val();
        $.get('index.php?r=users/activity-logs', {
            search,
            month
        }, function(resp) {
            if (resp.success) {
                const html = resp.logs.map(renderActivity).join('');
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
        loadActivities();
    });
    $(function() {
        loadActivities();
    });
</script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
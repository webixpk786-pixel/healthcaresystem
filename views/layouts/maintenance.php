<?php

//http://localhost/qamarali/supersystem/web/index.php?r=users/systemmodules
$currentUrl = Yii::$app->request->absoluteUrl;
$url = $currentUrl;
$route = Yii::$app->request->pathInfo;
$lastPart = basename(parse_url($currentUrl, PHP_URL_PATH));
$lastPart = pathinfo($lastPart, PATHINFO_FILENAME);
$routeParts = explode('/', $route);
$lastRoutePart = end($routeParts);

// Get the route part after "=" from the URL
$routeAfterEqual = '';
if (strpos($currentUrl, '?r=') !== false) {
    $routeAfterEqual = substr($currentUrl, strpos($currentUrl, '?r=') + 3);
    // Remove any additional parameters after the route
    if (strpos($routeAfterEqual, '&') !== false) {
        $routeAfterEqual = substr($routeAfterEqual, 0, strpos($routeAfterEqual, '&'));
    }
}

// Split the route by "/" to get both parts
$routePartsAfterEqual = explode('/', $routeAfterEqual);
$firstPart = $routePartsAfterEqual[0] ?? ''; // users
$secondPart = $routePartsAfterEqual[1] ?? ''; // systemmodules

$modules = Yii::$app->db->createCommand(
    "SELECT m.name, m.link, m.id, m.current_status FROM modules m WHERE m.link LIKE '%$firstPart%' OR m.link LIKE '%$secondPart%' "
)->queryAll();


$status = 1;
foreach ($modules as $module) {
    if ($module['current_status'] != 1) {
        $status = $module['current_status'];
    }
}


if ($status == 1) {
    $status = "Active";
} else if ($status == 2) {
    $status = "Module Under Maintenance. Avoid making any changes";
} else if ($status == 3) {
    $status = "Module Restricted. Avoid making any changes";
}

$system_flag = Yii::$app->db->createCommand('SELECT setting_value FROM system_settings WHERE setting_key = "system_status"')->queryScalar();
if ($system_flag == "offline") {
    $status = "System is Offline";
} else  if ($system_flag == "maintenance") {
    $status = "System is Under Maintenance";
}
if ($status && $status != "Active") {

?>

    <style>
        .restricted-message {
            position: fixed;
            text-align: center;
            top: 7%;
            color: white;
            padding: 5px;
            animation: slideInRight 0.5s ease-out;
            align-items: center;
            right: 30%;
            left: 37%;
            background: transparent
        }

        .restricted-message .icon {
            font-size: 18px;
            min-width: 24px;
            text-align: center;
        }

        .restricted-message .content {
            flex: 1;
            line-height: 1.4;
        }

        .restricted-message .main-message {
            display: block;
            margin-bottom: 4px;
            font-weight: 600;
            font-size: smaller
        }

        .restricted-message .status-message {
            display: block;
            font-size: 11px;
            font-weight: 400;
        }

        .restricted-message .status-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-left: 6px;
        }

        .restricted-message .status-active {
            background: rgba(76, 175, 80, 0.3);
            color: #4caf50;
        }

        .restricted-message .status-maintenance {
            background: rgba(255, 152, 0, 0.3);
            color: #ff9800;
        }

        .restricted-message .status-restricted {
            background: rgba(244, 67, 54, 0.3);
            color: #f44336;
        }

        .restricted-message .close-btn {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            transition: all 0.2s ease;
            margin-left: 8px;
        }

        .restricted-message .close-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: scale(1.1);
        }

        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @media (max-width: 768px) {
            .restricted-message {
                top: 10px;
                right: 10px;
                left: 10px;
                max-width: none;
            }
        }
    </style>

    <div class="restricted-message" id="restrictedNotice">
        <?php
        $statusClass = '';
        if (stripos($status, 'maintenance') !== false) {
            $statusClass = 'status-maintenance';
        } elseif (stripos($status, 'restricted') !== false) {
            $statusClass = 'status-restricted';
        } else {
            $statusClass = 'status-restricted';
        }
        ?>
        <div class="content">

            <span class="status-message">
                <span class="status-badge <?= $statusClass ?>"><?= $status ?></span>
            </span>
        </div>
        <!-- <button class="close-btn" onclick="this.parentElement.style.display='none'">×</button> -->
    </div>
<?php
}
?>


<!-- <script>
    // Auto-hide the message after 8 seconds
    setTimeout(function() {
        var notice = document.getElementById('restrictedNotice');
        if (notice) {
            notice.style.animation = 'slideInRight 0.5s ease-out reverse';
            setTimeout(function() {
                notice.style.display = 'none';
            }, 500);
        }
    }, 8000);
    </script> -->
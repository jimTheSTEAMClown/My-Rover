<?php
// Gather basic system status values from the Raspberry Pi
$hostname   = gethostname();
$uptime_raw = shell_exec('uptime -p 2>/dev/null');
$uptime     = $uptime_raw ? trim($uptime_raw) : 'unavailable';

// CPU temperature (Raspberry Pi specific)
$cpu_temp = 'unavailable';
if (file_exists('/sys/class/thermal/thermal_zone0/temp')) {
    $raw = intval(file_get_contents('/sys/class/thermal/thermal_zone0/temp'));
    $cpu_temp = round($raw / 1000, 1) . ' &deg;C';
}

// Memory usage
$mem_free  = 'unavailable';
$mem_total = 'unavailable';
if (PHP_OS_FAMILY === 'Linux') {
    $meminfo = file_get_contents('/proc/meminfo');
    if ($meminfo) {
        preg_match('/MemTotal:\s+(\d+)/', $meminfo, $m_total);
        preg_match('/MemAvailable:\s+(\d+)/', $meminfo, $m_avail);
        if ($m_total && $m_avail) {
            $total_mb = round($m_total[1] / 1024);
            $avail_mb = round($m_avail[1] / 1024);
            $used_mb  = $total_mb - $avail_mb;
            $mem_total = $total_mb . ' MB';
            $mem_free  = $avail_mb . ' MB free (' . $used_mb . ' MB used)';
        }
    }
}

$timestamp = date('Y-m-d H:i:s');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="30">
    <title>Rover Status &mdash; My-Rover</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <header>
        <h1>My-Rover &mdash; Status</h1>
    </header>

    <main>
        <div class="card">
            <h2>System Status</h2>
            <p>Last updated: <?php echo htmlspecialchars($timestamp); ?> &nbsp;(auto-refreshes every 30 s)</p>

            <div class="status-grid">
                <div class="status-item">
                    <div class="label">Hostname</div>
                    <div class="value"><?php echo htmlspecialchars($hostname); ?></div>
                </div>

                <div class="status-item">
                    <div class="label">Uptime</div>
                    <div class="value"><?php echo htmlspecialchars($uptime); ?></div>
                </div>

                <div class="status-item">
                    <div class="label">CPU Temperature</div>
                    <div class="value ok"><?php echo $cpu_temp; ?></div>
                </div>

                <div class="status-item">
                    <div class="label">Memory Total</div>
                    <div class="value"><?php echo htmlspecialchars($mem_total); ?></div>
                </div>

                <div class="status-item">
                    <div class="label">Memory</div>
                    <div class="value ok"><?php echo htmlspecialchars($mem_free); ?></div>
                </div>

                <div class="status-item">
                    <div class="label">PHP Version</div>
                    <div class="value"><?php echo htmlspecialchars(PHP_VERSION); ?></div>
                </div>
            </div>
        </div>

        <div class="card">
            <h2>Navigation</h2>
            <nav class="page-nav">
                <a href="index.html">&larr; Home</a>
            </nav>
        </div>
    </main>

    <footer>
        <p>My-Rover &mdash; Raspberry Pi Rover Project</p>
    </footer>

</body>
</html>

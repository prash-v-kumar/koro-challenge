<?php
// 1. Grab configuration from Environment Variables
$dbHost = getenv('DB_HOST') ?: 'mysql';
$dbUser = getenv('DB_USER') ?: 'root';
$dbPass = getenv('DB_PASS') ?: 'password';
$dbName = getenv('DB_NAME') ?: 'test_db';

$redisHost = getenv('REDIS_HOST') ?: 'redis';
$redisPort = getenv('REDIS_PORT') ?: 6379;

// Default Status Variables
$redisStatus = "<span style='color:red; font-weight:bold;'>Not Connected</span>";
$redisVisits = "N/A";
$redisError = "";

$dbStatus = "<span style='color:red; font-weight:bold;'>Not Connected</span>";
$dbLogs = "N/A";
$dbError = "";

// 2. Connect to Redis (Counter)
try {
    $redis = new Redis();
    if (@$redis->connect($redisHost, $redisPort)) {
        $redisVisits = $redis->incr('page_visits');
        $redisStatus = "<span style='color:green; font-weight:bold;'>Connected</span>";
    } else {
        $redisError = "Connection refused or timed out.";
    }
} catch (Exception $e) {
    $redisError = $e->getMessage();
}

// 3. Connect to MySQL (Access Log)
try {
    $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create table if it doesn't exist & log the visit
    $pdo->exec("CREATE TABLE IF NOT EXISTS access_log (id INT AUTO_INCREMENT PRIMARY KEY, access_time DATETIME)");
    $pdo->exec("INSERT INTO access_log (access_time) VALUES (NOW())");
    
    // Get total logs
    $dbLogs = $pdo->query("SELECT COUNT(*) FROM access_log")->fetchColumn();
    $dbStatus = "<span style='color:green; font-weight:bold;'>Connected</span>";

} catch (PDOException $e) {
    $dbError = $e->getMessage();
}

// 4. Print Status Dashboard
$hostname = gethostname();

echo "<div style='font-family: system-ui, -apple-system, sans-serif; max-width: 600px; margin: 40px auto; padding: 30px; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);'>";
echo "<h2 style='margin-top: 0;'>Infrastructure Status Dashboard</h2>";
echo "<p style='font-size: 1.1em;'><strong>Served by Container ID:</strong> <code style='background: #eee; padding: 4px 8px; border-radius: 4px; font-size: 1.1em;'>$hostname</code></p>";
echo "<hr style='border: 0; border-top: 1px solid #ddd; margin: 20px 0;'>";

// Redis Section
echo "<h3>Redis Cache</h3>";
echo "<p>Status: $redisStatus</p>";
if ($redisError) echo "<p style='color: #d32f2f; background: #ffebee; padding: 10px; border-radius: 4px; font-family: monospace;'>Error: $redisError</p>";
if ($redisVisits !== "N/A") echo "<p>Total Global Visits: <strong>$redisVisits</strong></p>";

// MySQL Section
echo "<h3>MySQL Database</h3>";
echo "<p>Status: $dbStatus</p>";
if ($dbError) echo "<p style='color: #d32f2f; background: #ffebee; padding: 10px; border-radius: 4px; font-family: monospace;'>Error: $dbError</p>";
if ($dbLogs !== "N/A") echo "<p>Total Database Logs: <strong>$dbLogs</strong></p>";

echo "</div>";
?>
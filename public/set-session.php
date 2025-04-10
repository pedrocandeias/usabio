<?php
session_start();
$_SESSION['username'] = 'john';
echo "✅ Session set<br>";
echo '<a href="get-session.php">Go to get-session.php</a>';

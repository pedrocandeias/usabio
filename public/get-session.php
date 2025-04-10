<?php
session_start();
echo "<h1>Session dump</h1>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";
<?php
session_start();
header("Location: ../index.html?message=Logged out successfully");
exit();

<?php
echo "customer: " . password_hash('customerPass!', PASSWORD_DEFAULT) . "<br>";
echo "tech: " . password_hash('techPass!', PASSWORD_DEFAULT) . "<br>";
echo "admin: " . password_hash('adminPass!', PASSWORD_DEFAULT) . "<br>";

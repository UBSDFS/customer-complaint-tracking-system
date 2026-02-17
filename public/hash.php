<?php
echo "customer: " . password_hash('customerPass!', PASSWORD_DEFAULT) . "<br>";
echo "tech: " . password_hash('techPass!', PASSWORD_DEFAULT) . "<br>";
echo "admin: " . password_hash('adminPass!', PASSWORD_DEFAULT) . "<br>";
//run this script once to generate the password hashes for the default users. Add the generated hashes to the database for the respective users, then you can delete this file.
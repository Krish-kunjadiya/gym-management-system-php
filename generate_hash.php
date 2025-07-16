<?php
// The password you want to use
$password = 'staff123';

// Generate the hash
$hash = password_hash($password, PASSWORD_DEFAULT);

// Display the hash
echo "Your new password hash is:<br><br>";
echo "<strong>" . $hash . "</strong>";
echo "<br><br>Copy the bold text above and use it in Step 3.";
?>
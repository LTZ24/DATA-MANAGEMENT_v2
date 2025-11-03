<?php
/**
 * Configuration Template File
 * 
 * IMPORTANT: 
 * 1. Copy this file to 'config.php' in the same directory
 * 2. Fill in your actual credentials in 'config.php'
 * 3. Never commit 'config.php' to version control
 */

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'your_database_username');
define('DB_PASS', 'your_database_password');
define('DB_NAME', 'your_database_name');

// Application Configuration
define('APP_NAME', 'Database Guru SMKN 62 Jakarta');
define('APP_VERSION', '2.0');
define('BASE_URL', 'http://localhost/Data-Base-Guru-v2'); // Change to your actual URL

// Google API Configuration
// Get these from https://console.cloud.google.com/
define('GOOGLE_CLIENT_ID', 'your-google-client-id.apps.googleusercontent.com');
define('GOOGLE_CLIENT_SECRET', 'your-google-client-secret');
define('GOOGLE_REDIRECT_URI', BASE_URL . '/auth/callback.php');

// Google Drive Folder IDs
// Create folders in your Google Drive and paste the IDs here
define('FOLDER_DATA_GURU', 'your-data-guru-folder-id');
define('FOLDER_SERTIFIKASI', 'your-sertifikasi-folder-id');
define('FOLDER_PELATIHAN', 'your-pelatihan-folder-id');
define('FOLDER_DOKUMEN', 'your-dokumen-folder-id');

// Google Sheets ID for Data Guru database
// Create a Google Sheet and paste the ID here
define('GOOGLE_SHEETS_ID', 'your-google-sheets-id');

// Google Sheets IDs per Category (for Links & Forms)
// You can use the same sheet with different tabs, or separate sheets per category
// Each category will have tabs like: Links-Kesiswaan, Links-Kurikulum, Forms-Kesiswaan, etc.
define('SHEETS_KESISWAAN', 'your-kesiswaan-sheets-id');
define('SHEETS_KURIKULUM', 'your-kurikulum-sheets-id');
define('SHEETS_SAPRAS_HUMAS', 'your-sapras-humas-sheets-id');
define('SHEETS_TATA_USAHA', 'your-tata-usaha-sheets-id');

// File Upload Configuration
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx']);
define('UPLOAD_DIR', __DIR__ . '/../data/uploads/');

// Session Configuration
if (session_status() == PHP_SESSION_NONE) {
    ini_set('session.gc_maxlifetime', 1800); // 30 minutes
    ini_set('session.cookie_lifetime', 1800);
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_secure', 0); // Set to 1 if using HTTPS
    session_start();
    
    // Initialize session creation time
    if (!isset($_SESSION['created'])) {
        $_SESSION['created'] = time();
    } else if (time() - $_SESSION['created'] > 1800) {
        session_regenerate_id(true);
        $_SESSION['created'] = time();
    }
    
    // Check for inactivity timeout
    if (isset($_SESSION['last_activity'])) {
        $inactive_time = time() - $_SESSION['last_activity'];
        
        // If inactive for more than 30 minutes, destroy session
        if ($inactive_time > 1800) {
            session_unset();
            session_destroy();
            
            // Redirect to login if not already on login page
            if (!strpos($_SERVER['REQUEST_URI'], '/auth/login.php')) {
                header('Location: ' . BASE_URL . '/auth/login.php?session_timeout=1');
                exit();
            }
        }
    }
    
    // Update last activity time on each request
    $_SESSION['last_activity'] = time();
}

// i18n - Internationalization
require_once __DIR__ . '/i18n.php';

// Handle AJAX session update requests
if (isset($_SERVER['HTTP_X_SESSION_UPDATE']) && $_SERVER['HTTP_X_SESSION_UPDATE'] === 'true') {
    if (session_status() == PHP_SESSION_ACTIVE) {
        $_SESSION['last_activity'] = time();
        http_response_code(204); // No content
        exit();
    }
}

// Error Reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Timezone
date_default_timezone_set('Asia/Jakarta');

// Helper Functions
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

function redirect($url) {
    header("Location: $url");
    exit();
}

function formatDate($date) {
    return date('d-m-Y', strtotime($date));
}

function formatDateTime($datetime) {
    return date('d-m-Y H:i', strtotime($datetime));
}

// Google Client Functions
function getGoogleClient() {
    require_once __DIR__ . '/../vendor/autoload.php';
    
    $client = new Google_Client();
    $client->setClientId(GOOGLE_CLIENT_ID);
    $client->setClientSecret(GOOGLE_CLIENT_SECRET);
    $client->setRedirectUri(GOOGLE_REDIRECT_URI);
    
    // Set proper scopes for Drive and Sheets access
    $client->addScope(Google_Service_Drive::DRIVE); // Full Drive access
    $client->addScope(Google_Service_Sheets::SPREADSHEETS); // Full Sheets access
    $client->addScope('email');
    $client->addScope('profile');
    
    $client->setAccessType('offline');
    $client->setPrompt('select_account consent');
    
    // Load previously authorized credentials from session
    if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
        $client->setAccessToken($_SESSION['access_token']);
    }
    
    // Refresh the token if it's expired
    if ($client->isAccessTokenExpired()) {
        if ($client->getRefreshToken()) {
            $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
            $_SESSION['access_token'] = $client->getAccessToken();
        }
    }
    
    return $client;
}

function getSheetsService() {
    $client = getGoogleClient();
    return new Google_Service_Sheets($client);
}

function getDriveService() {
    $client = getGoogleClient();
    return new Google_Service_Drive($client);
}

// Authentication Functions
function isLoggedIn() {
    if (!isset($_SESSION['access_token'])) {
        return false;
    }
    
    // Check session timeout
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1800)) {
        logoutUser(true);
        return false;
    }
    
    try {
        $client = getGoogleClient();
        if ($client->isAccessTokenExpired()) {
            logoutUser(false);
            return false;
        }
        return true;
    } catch (Exception $e) {
        return false;
    }
}

function requireLogin() {
    if (!isLoggedIn()) {
        redirect(BASE_URL . '/auth/login.php');
    }
}

function logoutUser($isTimeout = false) {
    $_SESSION = array();
    
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time()-3600, '/');
    }
    
    session_destroy();
    
    if ($isTimeout) {
        header('Location: ' . BASE_URL . '/auth/login.php?session_timeout=1');
    } else {
        header('Location: ' . BASE_URL . '/auth/login.php');
    }
    exit;
}

// Google Sheets Functions for Links & Forms
function getLinksFromSheets() {
    try {
        $service = getSheetsService();
        $spreadsheetId = GOOGLE_SHEETS_ID;
        $range = 'Links!A2:D'; // Skip header row
        
        $response = $service->spreadsheets_values->get($spreadsheetId, $range);
        $values = $response->getValues();
        
        $links = [];
        if (empty($values)) {
            return $links;
        }
        
        foreach ($values as $index => $row) {
            if (isset($row[0]) && isset($row[1])) {
                $links[] = [
                    'id' => $index,
                    'title' => $row[0] ?? '',
                    'url' => $row[1] ?? '',
                    'created_at' => $row[2] ?? date('Y-m-d H:i:s'),
                    'updated_at' => $row[3] ?? null
                ];
            }
        }
        
        return $links;
    } catch (Exception $e) {
        error_log('Error reading links from Sheets: ' . $e->getMessage());
        return [];
    }
}

function getFormsFromSheets() {
    try {
        $service = getSheetsService();
        $spreadsheetId = GOOGLE_SHEETS_ID;
        $range = 'Forms!A2:D'; // Skip header row
        
        $response = $service->spreadsheets_values->get($spreadsheetId, $range);
        $values = $response->getValues();
        
        $forms = [];
        if (empty($values)) {
            return $forms;
        }
        
        foreach ($values as $index => $row) {
            if (isset($row[0]) && isset($row[1])) {
                $forms[] = [
                    'id' => $index,
                    'title' => $row[0] ?? '',
                    'url' => $row[1] ?? '',
                    'created_at' => $row[2] ?? date('Y-m-d H:i:s'),
                    'updated_at' => $row[3] ?? null
                ];
            }
        }
        
        return $forms;
    } catch (Exception $e) {
        error_log('Error reading forms from Sheets: ' . $e->getMessage());
        return [];
    }
}

function addLinkToSheets($title, $url) {
    try {
        $service = getSheetsService();
        $spreadsheetId = GOOGLE_SHEETS_ID;
        $range = 'Links!A:D';
        
        $values = [
            [$title, $url, date('Y-m-d H:i:s'), '']
        ];
        
        $body = new Google_Service_Sheets_ValueRange([
            'values' => $values
        ]);
        
        $params = [
            'valueInputOption' => 'RAW'
        ];
        
        $result = $service->spreadsheets_values->append(
            $spreadsheetId,
            $range,
            $body,
            $params
        );
        
        return true;
    } catch (Exception $e) {
        error_log('Error adding link to Sheets: ' . $e->getMessage());
        return false;
    }
}

function addFormToSheets($title, $url) {
    try {
        $service = getSheetsService();
        $spreadsheetId = GOOGLE_SHEETS_ID;
        $range = 'Forms!A:D';
        
        $values = [
            [$title, $url, date('Y-m-d H:i:s'), '']
        ];
        
        $body = new Google_Service_Sheets_ValueRange([
            'values' => $values
        ]);
        
        $params = [
            'valueInputOption' => 'RAW'
        ];
        
        $result = $service->spreadsheets_values->append(
            $spreadsheetId,
            $range,
            $body,
            $params
        );
        
        return true;
    } catch (Exception $e) {
        error_log('Error adding form to Sheets: ' . $e->getMessage());
        return false;
    }
}

function updateLinkInSheets($index, $title, $url) {
    try {
        $service = getSheetsService();
        $spreadsheetId = GOOGLE_SHEETS_ID;
        $rowNumber = $index + 2;
        $range = "Links!A{$rowNumber}:D{$rowNumber}";
        
        $existing = $service->spreadsheets_values->get($spreadsheetId, $range);
        $existingValues = $existing->getValues();
        $createdAt = $existingValues[0][2] ?? date('Y-m-d H:i:s');
        
        $values = [
            [$title, $url, $createdAt, date('Y-m-d H:i:s')]
        ];
        
        $body = new Google_Service_Sheets_ValueRange([
            'values' => $values
        ]);
        
        $params = [
            'valueInputOption' => 'RAW'
        ];
        
        $result = $service->spreadsheets_values->update(
            $spreadsheetId,
            $range,
            $body,
            $params
        );
        
        return true;
    } catch (Exception $e) {
        error_log('Error updating link in Sheets: ' . $e->getMessage());
        return false;
    }
}

function updateFormInSheets($index, $title, $url) {
    try {
        $service = getSheetsService();
        $spreadsheetId = GOOGLE_SHEETS_ID;
        $rowNumber = $index + 2;
        $range = "Forms!A{$rowNumber}:D{$rowNumber}";
        
        $existing = $service->spreadsheets_values->get($spreadsheetId, $range);
        $existingValues = $existing->getValues();
        $createdAt = $existingValues[0][2] ?? date('Y-m-d H:i:s');
        
        $values = [
            [$title, $url, $createdAt, date('Y-m-d H:i:s')]
        ];
        
        $body = new Google_Service_Sheets_ValueRange([
            'values' => $values
        ]);
        
        $params = [
            'valueInputOption' => 'RAW'
        ];
        
        $result = $service->spreadsheets_values->update(
            $spreadsheetId,
            $range,
            $body,
            $params
        );
        
        return true;
    } catch (Exception $e) {
        error_log('Error updating form in Sheets: ' . $e->getMessage());
        return false;
    }
}

function deleteLinkFromSheets($index) {
    try {
        $service = getSheetsService();
        $spreadsheetId = GOOGLE_SHEETS_ID;
        
        $spreadsheet = $service->spreadsheets->get($spreadsheetId);
        $linksSheetId = null;
        foreach ($spreadsheet->getSheets() as $sheet) {
            if ($sheet->getProperties()->getTitle() === 'Links') {
                $linksSheetId = $sheet->getProperties()->getSheetId();
                break;
            }
        }
        
        if ($linksSheetId === null) {
            throw new Exception('Links sheet not found');
        }
        
        $rowNumber = $index + 1;
        
        $requests = [
            new Google_Service_Sheets_Request([
                'deleteDimension' => [
                    'range' => [
                        'sheetId' => $linksSheetId,
                        'dimension' => 'ROWS',
                        'startIndex' => $rowNumber,
                        'endIndex' => $rowNumber + 1
                    ]
                ]
            ])
        ];
        
        $batchUpdateRequest = new Google_Service_Sheets_BatchUpdateSpreadsheetRequest([
            'requests' => $requests
        ]);
        
        $service->spreadsheets->batchUpdate($spreadsheetId, $batchUpdateRequest);
        return true;
    } catch (Exception $e) {
        error_log('Error deleting link from Sheets: ' . $e->getMessage());
        return false;
    }
}

function deleteFormFromSheets($index) {
    try {
        $service = getSheetsService();
        $spreadsheetId = GOOGLE_SHEETS_ID;
        
        $spreadsheet = $service->spreadsheets->get($spreadsheetId);
        $formsSheetId = null;
        foreach ($spreadsheet->getSheets() as $sheet) {
            if ($sheet->getProperties()->getTitle() === 'Forms') {
                $formsSheetId = $sheet->getProperties()->getSheetId();
                break;
            }
        }
        
        if ($formsSheetId === null) {
            throw new Exception('Forms sheet not found');
        }
        
        $rowNumber = $index + 1;
        
        $requests = [
            new Google_Service_Sheets_Request([
                'deleteDimension' => [
                    'range' => [
                        'sheetId' => $formsSheetId,
                        'dimension' => 'ROWS',
                        'startIndex' => $rowNumber,
                        'endIndex' => $rowNumber + 1
                    ]
                ]
            ])
        ];
        
        $batchUpdateRequest = new Google_Service_Sheets_BatchUpdateSpreadsheetRequest([
            'requests' => $requests
        ]);
        
        $service->spreadsheets->batchUpdate($spreadsheetId, $batchUpdateRequest);
        return true;
    } catch (Exception $e) {
        error_log('Error deleting form from Sheets: ' . $e->getMessage());
        return false;
    }
}

// Get folder categories
function getDriveCategories() {
    return [
        'kesiswaan' => [
            'name' => 'Kesiswaan',
            'icon' => 'fa-users',
            'folder_id' => FOLDER_DATA_GURU,
            'color' => '#50e3c2'
        ],
        'kurikulum' => [
            'name' => 'Kurikulum',
            'icon' => 'fa-book',
            'folder_id' => FOLDER_SERTIFIKASI,
            'color' => '#4a90e2'
        ],
        'sapras_humas' => [
            'name' => 'Sapras & Humas',
            'icon' => 'fa-building',
            'folder_id' => FOLDER_PELATIHAN,
            'color' => '#f39c12'
        ],
        'tata_usaha' => [
            'name' => 'Tata Usaha',
            'icon' => 'fa-file-invoice',
            'folder_id' => FOLDER_DOKUMEN,
            'color' => '#e74c3c'
        ]
    ];
}

// Format file size to human readable
function formatFileSize($bytes) {
    if ($bytes == 0) return '0 Bytes';
    $k = 1024;
    $sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
    $i = floor(log($bytes) / log($k));
    return round($bytes / pow($k, $i), 2) . ' ' . $sizes[$i];
}

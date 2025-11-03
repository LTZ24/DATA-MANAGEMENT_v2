<?php
/**
 * Google Sheets Setup Action
 * Backend script untuk membuat struktur sheets otomatis
 */

require_once __DIR__ . '/includes/config.php';
requireLogin();

header('Content-Type: application/json');

try {
    $service = getSheetsService();
    $categories = [
        'kesiswaan' => 'Kesiswaan',
        'kurikulum' => 'Kurikulum',
        'sapras_humas' => 'Sapras Humas',
        'tata_usaha' => 'Tata Usaha'
    ];
    
    $sheetsIds = [];
    
    foreach ($categories as $key => $name) {
        // Create new spreadsheet
        $spreadsheet = new Google_Service_Sheets_Spreadsheet([
            'properties' => [
                'title' => 'Database ' . $name . ' - SMKN 62'
            ]
        ]);
        
        $spreadsheet = $service->spreadsheets->create($spreadsheet);
        $spreadsheetId = $spreadsheet->getSpreadsheetId();
        $sheetsIds[$key] = $spreadsheetId;
        
        // Get the default sheet (Sheet1)
        $sheets = $spreadsheet->getSheets();
        $defaultSheetId = $sheets[0]->getProperties()->getSheetId();
        
        // Prepare batch update requests
        $requests = [];
        
        // 1. Rename first sheet to Links-{category}
        $sheetName = 'Links-' . ucfirst($key);
        $requests[] = new Google_Service_Sheets_Request([
            'updateSheetProperties' => [
                'properties' => [
                    'sheetId' => $defaultSheetId,
                    'title' => $sheetName
                ],
                'fields' => 'title'
            ]
        ]);
        
        // 2. Add second sheet for Forms
        $formsSheetName = 'Forms-' . ucfirst($key);
        $requests[] = new Google_Service_Sheets_Request([
            'addSheet' => [
                'properties' => [
                    'title' => $formsSheetName
                ]
            ]
        ]);
        
        // Execute batch update
        $batchUpdateRequest = new Google_Service_Sheets_BatchUpdateSpreadsheetRequest([
            'requests' => $requests
        ]);
        $service->spreadsheets->batchUpdate($spreadsheetId, $batchUpdateRequest);
        
        // 3. Add headers to Links sheet
        $linksRange = $sheetName . '!A1:D1';
        $linksHeaders = [
            ['Title', 'URL', 'Created At', 'Updated At']
        ];
        $linksBody = new Google_Service_Sheets_ValueRange([
            'values' => $linksHeaders
        ]);
        $service->spreadsheets_values->update(
            $spreadsheetId,
            $linksRange,
            $linksBody,
            ['valueInputOption' => 'RAW']
        );
        
        // 4. Add headers to Forms sheet
        $formsRange = $formsSheetName . '!A1:D1';
        $formsHeaders = [
            ['Title', 'URL', 'Created At', 'Updated At']
        ];
        $formsBody = new Google_Service_Sheets_ValueRange([
            'values' => $formsHeaders
        ]);
        $service->spreadsheets_values->update(
            $spreadsheetId,
            $formsRange,
            $formsBody,
            ['valueInputOption' => 'RAW']
        );
        
        // 5. Format headers (bold, background color)
        $formatRequests = [];
        
        // Get sheet IDs for both sheets
        $updatedSpreadsheet = $service->spreadsheets->get($spreadsheetId);
        $allSheets = $updatedSpreadsheet->getSheets();
        
        foreach ($allSheets as $sheet) {
            $sheetId = $sheet->getProperties()->getSheetId();
            $sheetTitle = $sheet->getProperties()->getTitle();
            
            // Format header row (row 0)
            $formatRequests[] = new Google_Service_Sheets_Request([
                'repeatCell' => [
                    'range' => [
                        'sheetId' => $sheetId,
                        'startRowIndex' => 0,
                        'endRowIndex' => 1,
                        'startColumnIndex' => 0,
                        'endColumnIndex' => 4
                    ],
                    'cell' => [
                        'userEnteredFormat' => [
                            'backgroundColor' => [
                                'red' => 0.8,
                                'green' => 0.9,
                                'blue' => 1.0
                            ],
                            'textFormat' => [
                                'bold' => true
                            ],
                            'horizontalAlignment' => 'CENTER'
                        ]
                    ],
                    'fields' => 'userEnteredFormat(backgroundColor,textFormat,horizontalAlignment)'
                ]
            ]);
            
            // Auto-resize columns
            $formatRequests[] = new Google_Service_Sheets_Request([
                'autoResizeDimensions' => [
                    'dimensions' => [
                        'sheetId' => $sheetId,
                        'dimension' => 'COLUMNS',
                        'startIndex' => 0,
                        'endIndex' => 4
                    ]
                ]
            ]);
            
            // Freeze header row
            $formatRequests[] = new Google_Service_Sheets_Request([
                'updateSheetProperties' => [
                    'properties' => [
                        'sheetId' => $sheetId,
                        'gridProperties' => [
                            'frozenRowCount' => 1
                        ]
                    ],
                    'fields' => 'gridProperties.frozenRowCount'
                ]
            ]);
        }
        
        // Execute formatting batch update
        $formatBatchRequest = new Google_Service_Sheets_BatchUpdateSpreadsheetRequest([
            'requests' => $formatRequests
        ]);
        $service->spreadsheets->batchUpdate($spreadsheetId, $formatBatchRequest);
        
        // Small delay to avoid rate limiting
        usleep(500000); // 0.5 second
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'All sheets created successfully!',
        'sheets_ids' => $sheetsIds
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}

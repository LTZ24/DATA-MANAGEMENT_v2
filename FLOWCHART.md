# Data Management System - Application Flowchart

## 1. Main Application Flow

```mermaid
flowchart TD
    Start([User Mengakses Aplikasi]) --> CheckSession{Session Valid?}
    
    CheckSession -->|Tidak| LoginPage[Tampilkan Login Page]
    CheckSession -->|Ya| Dashboard[Dashboard]
    
    LoginPage --> ChooseLang{Pilih Bahasa}
    ChooseLang -->|Indonesia| SetID[Set Language: ID]
    ChooseLang -->|English| SetEN[Set Language: EN]
    
    SetID --> GoogleAuth[Klik Google Login Button]
    SetEN --> GoogleAuth
    
    GoogleAuth --> OAuth[Redirect ke Google OAuth]
    OAuth --> GoogleConsent{User Menyetujui?}
    
    GoogleConsent -->|Tidak| LoginPage
    GoogleConsent -->|Ya| Callback[Callback ke aplikasi]
    
    Callback --> ValidateToken{Token Valid?}
    ValidateToken -->|Tidak| LoginPage
    ValidateToken -->|Ya| CreateSession[Buat Session]
    
    CreateSession --> Dashboard
    
    Dashboard --> MainMenu{Pilih Menu}
    
    MainMenu -->|Link Management| LinkMenu[Link Manager]
    MainMenu -->|Form Management| FormMenu[Form Manager]
    MainMenu -->|File Management| FileMenu[File Manager]
    MainMenu -->|Settings| Settings[Pengaturan]
    MainMenu -->|Logout| Logout[Logout]
    
    LinkMenu --> LinkFlow[Link Management Flow]
    FormMenu --> FormFlow[Form Management Flow]
    FileMenu --> FileFlow[File Management Flow]
    Settings --> SettingsFlow[Settings Flow]
    
    Logout --> DestroySession[Hancurkan Session]
    DestroySession --> LoginPage
    
    style Start fill:#50e3c2
    style Dashboard fill:#4dd0e1
    style LoginPage fill:#ffd54f
    style Logout fill:#ff6b6b
```

## 2. Authentication Flow (Detail)

```mermaid
flowchart TD
    A([User di Login Page]) --> B[Klik Google Login]
    B --> C[Redirect ke Google OAuth]
    C --> D{User Login Google}
    
    D -->|Berhasil| E[Google Consent Screen]
    D -->|Gagal| F[Error: Invalid Credentials]
    F --> A
    
    E --> G{User Izinkan Akses?}
    G -->|Tidak| H[Error: Access Denied]
    G -->|Ya| I[Google Return Authorization Code]
    
    H --> A
    I --> J[Callback Handler]
    
    J --> K[Exchange Code for Token]
    K --> L{Token Valid?}
    
    L -->|Tidak| M[Error: Invalid Token]
    L -->|Ya| N[Simpan Token di Session]
    
    M --> A
    N --> O[Ambil User Info dari Google]
    O --> P[Set User Session Data]
    P --> Q[Redirect ke Dashboard]
    
    Q --> R([Dashboard])
    
    style A fill:#ffd54f
    style R fill:#4dd0e1
    style F fill:#ff6b6b
    style H fill:#ff6b6b
    style M fill:#ff6b6b
```

## 3. Link Management Flow

```mermaid
flowchart TD
    Start([Link Manager]) --> Menu{Pilih Aksi}
    
    Menu -->|Tambah Link| Add[Form Tambah Link]
    Menu -->|Lihat Links| View[Tampilkan Daftar Links]
    Menu -->|Edit Link| Edit[Form Edit Link]
    Menu -->|Hapus Link| Delete[Konfirmasi Hapus]
    Menu -->|Kembali| Dashboard[Dashboard]
    
    Add --> AddForm[Input: Title, URL, Category, Description]
    AddForm --> ValidateAdd{Data Valid?}
    ValidateAdd -->|Tidak| AddError[Tampilkan Error]
    AddError --> Add
    
    ValidateAdd -->|Ya| SaveGSheets1[Simpan ke Google Sheets]
    SaveGSheets1 --> SuccessAdd[Success Message]
    SuccessAdd --> View
    
    View --> ShowLinks[Ambil Data dari Google Sheets]
    ShowLinks --> DisplayLinks[Tampilkan dalam Tabel]
    DisplayLinks --> FilterSort{Filter/Sort?}
    FilterSort -->|Ya| ApplyFilter[Terapkan Filter]
    ApplyFilter --> DisplayLinks
    FilterSort -->|Tidak| Menu
    
    Edit --> EditForm[Tampilkan Form dengan Data Existing]
    EditForm --> ValidateEdit{Data Valid?}
    ValidateEdit -->|Tidak| EditError[Tampilkan Error]
    EditError --> Edit
    
    ValidateEdit -->|Ya| UpdateGSheets[Update Google Sheets]
    UpdateGSheets --> SuccessEdit[Success Message]
    SuccessEdit --> View
    
    Delete --> ConfirmDelete{Konfirmasi?}
    ConfirmDelete -->|Tidak| Menu
    ConfirmDelete -->|Ya| DeleteGSheets[Hapus dari Google Sheets]
    DeleteGSheets --> SuccessDelete[Success Message]
    SuccessDelete --> View
    
    style Start fill:#4dd0e1
    style Dashboard fill:#50e3c2
    style AddError fill:#ff6b6b
    style EditError fill:#ff6b6b
```

## 4. Form Management Flow

```mermaid
flowchart TD
    Start([Form Manager]) --> Menu{Pilih Aksi}
    
    Menu -->|Buat Form Baru| Create[Form Builder]
    Menu -->|Lihat Forms| ViewForms[Daftar Forms]
    Menu -->|Edit Form| EditForm[Edit Form Builder]
    Menu -->|Hapus Form| DeleteForm[Konfirmasi Hapus]
    Menu -->|Preview Form| Preview[Preview Form]
    Menu -->|Kembali| Dashboard[Dashboard]
    
    Create --> Builder[Form Builder Interface]
    Builder --> AddFields{Tambah Field}
    AddFields -->|Text| TextField[Text Input]
    AddFields -->|Select| SelectField[Dropdown]
    AddFields -->|Date| DateField[Date Picker]
    AddFields -->|File| FileField[File Upload]
    AddFields -->|Selesai| SaveForm
    
    TextField --> AddFields
    SelectField --> AddFields
    DateField --> AddFields
    FileField --> AddFields
    
    SaveForm[Simpan Konfigurasi Form] --> ValidateForm{Valid?}
    ValidateForm -->|Tidak| FormError[Error Message]
    FormError --> Builder
    
    ValidateForm -->|Ya| SaveToGSheets[Simpan ke Google Sheets]
    SaveToGSheets --> CreateFormSheet[Buat Sheet untuk Responses]
    CreateFormSheet --> SuccessCreate[Success Message]
    SuccessCreate --> ViewForms
    
    ViewForms --> ListForms[Ambil Data Forms]
    ListForms --> DisplayForms[Tampilkan Daftar Forms]
    DisplayForms --> FormActions{Aksi}
    FormActions -->|View Responses| ViewResponses[Lihat Responses]
    FormActions -->|Edit| EditForm
    FormActions -->|Delete| DeleteForm
    FormActions -->|Share| ShareForm[Generate Link]
    FormActions -->|Kembali| Menu
    
    ViewResponses --> GetResponses[Ambil Data dari Sheet]
    GetResponses --> ShowTable[Tampilkan dalam Tabel]
    ShowTable --> ExportOption{Export?}
    ExportOption -->|CSV| ExportCSV[Download CSV]
    ExportOption -->|Excel| ExportExcel[Download Excel]
    ExportOption -->|Tidak| FormActions
    
    EditForm --> LoadForm[Load Form Config]
    LoadForm --> Builder
    
    DeleteForm --> ConfirmDel{Konfirmasi?}
    ConfirmDel -->|Tidak| Menu
    ConfirmDel -->|Ya| DeleteSheet[Hapus dari Google Sheets]
    DeleteSheet --> SuccessDel[Success Message]
    SuccessDel --> ViewForms
    
    Preview --> RenderPreview[Render Form Preview]
    RenderPreview --> TestForm[Test Form]
    TestForm --> Menu
    
    ShareForm --> GenerateLink[Generate Public/Private Link]
    GenerateLink --> CopyLink[Copy to Clipboard]
    CopyLink --> FormActions
    
    style Start fill:#4dd0e1
    style Dashboard fill:#50e3c2
    style FormError fill:#ff6b6b
```

## 5. File Management Flow

```mermaid
flowchart TD
    Start([File Manager]) --> Menu{Pilih Aksi}
    
    Menu -->|Upload File| Upload[Upload Interface]
    Menu -->|Lihat Files| ViewFiles[Daftar Files]
    Menu -->|Hapus File| Delete[Konfirmasi Hapus]
    Menu -->|Kembali| Dashboard[Dashboard]
    
    Upload --> SelectFile[Pilih File dari Device]
    SelectFile --> ValidateFile{Validasi File}
    
    ValidateFile -->|Size > 10MB| ErrorSize[Error: File terlalu besar]
    ValidateFile -->|Type Invalid| ErrorType[Error: Type tidak didukung]
    ValidateFile -->|Valid| ProcessUpload[Process Upload]
    
    ErrorSize --> Upload
    ErrorType --> Upload
    
    ProcessUpload --> UploadGDrive[Upload ke Google Drive]
    UploadGDrive --> SaveMetadata[Simpan Metadata ke Google Sheets]
    SaveMetadata --> SuccessUpload[Success Message]
    SuccessUpload --> ViewFiles
    
    ViewFiles --> GetFiles[Ambil Data dari Google Sheets]
    GetFiles --> ListFiles[Tampilkan Daftar Files]
    ListFiles --> FileActions{Aksi}
    
    FileActions -->|Download| Download[Download dari Google Drive]
    FileActions -->|Preview| Preview[Preview File]
    FileActions -->|Share| Share[Generate Share Link]
    FileActions -->|Delete| Delete
    FileActions -->|Kembali| Menu
    
    Download --> CheckPermission{Ada Akses?}
    CheckPermission -->|Tidak| ErrorPerm[Error: No Permission]
    CheckPermission -->|Ya| DownloadFile[Download File]
    DownloadFile --> FileActions
    ErrorPerm --> FileActions
    
    Preview --> PreviewFile[Tampilkan Preview]
    PreviewFile --> FileActions
    
    Share --> GenerateLink[Generate Google Drive Link]
    GenerateLink --> SetPermission{Set Permission}
    SetPermission -->|Public| PublicLink[Anyone with link]
    SetPermission -->|Private| PrivateLink[Specific people]
    PublicLink --> CopyLink[Copy Link]
    PrivateLink --> CopyLink
    CopyLink --> FileActions
    
    Delete --> ConfirmDelete{Konfirmasi?}
    ConfirmDelete -->|Tidak| Menu
    ConfirmDelete -->|Ya| DeleteGDrive[Hapus dari Google Drive]
    DeleteGDrive --> DeleteMetadata[Hapus dari Google Sheets]
    DeleteMetadata --> SuccessDelete[Success Message]
    SuccessDelete --> ViewFiles
    
    style Start fill:#4dd0e1
    style Dashboard fill:#50e3c2
    style ErrorSize fill:#ff6b6b
    style ErrorType fill:#ff6b6b
    style ErrorPerm fill:#ff6b6b
```

## 6. Session Management Flow

```mermaid
flowchart TD
    Start([User Activity]) --> CheckSession{Session Exists?}
    
    CheckSession -->|Tidak| Redirect[Redirect ke Login]
    CheckSession -->|Ya| ValidateToken{Token Valid?}
    
    ValidateToken -->|Tidak| RefreshToken{Refresh Token Available?}
    ValidateToken -->|Ya| CheckTimeout
    
    RefreshToken -->|Tidak| ExpiredSession[Session Expired]
    RefreshToken -->|Ya| GetNewToken[Request New Access Token]
    
    GetNewToken --> NewTokenValid{New Token Valid?}
    NewTokenValid -->|Tidak| ExpiredSession
    NewTokenValid -->|Ya| UpdateSession[Update Session Token]
    UpdateSession --> CheckTimeout
    
    CheckTimeout{Last Activity < 30 min?}
    CheckTimeout -->|Tidak| TimeoutSession[Session Timeout]
    CheckTimeout -->|Ya| UpdateActivity[Update Last Activity Time]
    
    UpdateActivity --> AllowAccess[Izinkan Akses]
    AllowAccess --> UserAction[User Melakukan Aksi]
    
    UserAction --> ActivityLoop{Ada Aktivitas Lagi?}
    ActivityLoop -->|Ya| Start
    ActivityLoop -->|Tidak| Idle[Idle State]
    
    Idle --> IdleCheck{Idle > 30 min?}
    IdleCheck -->|Ya| TimeoutSession
    IdleCheck -->|Tidak| Idle
    
    ExpiredSession --> DestroySession[Hancurkan Session]
    TimeoutSession --> DestroySession
    
    DestroySession --> ShowMessage[Tampilkan Session Expired Message]
    ShowMessage --> Redirect
    
    Redirect --> LoginPage([Login Page])
    
    style AllowAccess fill:#50e3c2
    style LoginPage fill:#ffd54f
    style ExpiredSession fill:#ff6b6b
    style TimeoutSession fill:#ff6b6b
```

## 7. Language Switching Flow

```mermaid
flowchart TD
    Start([User di Aplikasi]) --> ClickFlag[Klik Flag Icon]
    ClickFlag --> ShowMenu{Tampilkan Menu Bahasa}
    
    ShowMenu -->|Indonesian| SelectID[Pilih ID]
    ShowMenu -->|English| SelectEN[Pilih EN]
    
    SelectID --> SaveLocalStorage1[Simpan 'lang=id' di localStorage]
    SelectEN --> SaveLocalStorage2[Simpan 'lang=en' di localStorage]
    
    SaveLocalStorage1 --> SetCookie1[Set Cookie: lang=id]
    SaveLocalStorage2 --> SetCookie2[Set Cookie: lang=en]
    
    SetCookie1 --> ReloadPage[Reload Halaman]
    SetCookie2 --> ReloadPage
    
    ReloadPage --> LoadLang[Load Bahasa dari localStorage]
    LoadLang --> ApplyTranslation[Terapkan Translasi]
    ApplyTranslation --> UpdateUI[Update UI dengan Bahasa Terpilih]
    UpdateUI --> Done([Selesai])
    
    style Start fill:#4dd0e1
    style Done fill:#50e3c2
```

## 8. Error Handling Flow

```mermaid
flowchart TD
    Start([Error Terjadi]) --> TypeCheck{Tipe Error}
    
    TypeCheck -->|Authentication Error| AuthError[Token Invalid/Expired]
    TypeCheck -->|Network Error| NetError[Connection Failed]
    TypeCheck -->|API Error| APIError[Google API Error]
    TypeCheck -->|Validation Error| ValError[Input Invalid]
    TypeCheck -->|Permission Error| PermError[No Access Rights]
    
    AuthError --> LogoutUser[Logout User]
    LogoutUser --> RedirectLogin[Redirect ke Login]
    
    NetError --> ShowRetry[Tampilkan Retry Button]
    ShowRetry --> UserRetry{User Retry?}
    UserRetry -->|Ya| RetryAction[Retry Action]
    UserRetry -->|Tidak| BackToPrev[Kembali ke Halaman Sebelumnya]
    
    RetryAction --> CheckSuccess{Berhasil?}
    CheckSuccess -->|Ya| Success[Success]
    CheckSuccess -->|Tidak| RetryCount{Retry < 3?}
    RetryCount -->|Ya| RetryAction
    RetryCount -->|Tidak| ShowError[Tampilkan Error Persistent]
    
    APIError --> LogError[Log Error ke Console]
    LogError --> ShowAPIError[Tampilkan User-Friendly Message]
    ShowAPIError --> ContactSupport{Butuh Support?}
    ContactSupport -->|Ya| ShowGitHub[Tampilkan Link GitHub]
    ContactSupport -->|Tidak| BackToPrev
    
    ValError --> HighlightField[Highlight Field Error]
    HighlightField --> ShowMessage[Tampilkan Error Message]
    ShowMessage --> UserFix[User Perbaiki Input]
    UserFix --> Validate{Valid?}
    Validate -->|Tidak| HighlightField
    Validate -->|Ya| Success
    
    PermError --> ShowPermError[Error: Akses Ditolak]
    ShowPermError --> CheckAdmin{Admin Required?}
    CheckAdmin -->|Ya| ContactAdmin[Hubungi Administrator]
    CheckAdmin -->|Tidak| BackToPrev
    
    Success --> End([Lanjut Normal])
    BackToPrev --> End
    RedirectLogin --> LoginPage([Login Page])
    ShowError --> End
    ContactAdmin --> End
    ShowGitHub --> End
    
    style Start fill:#ff6b6b
    style Success fill:#50e3c2
    style End fill:#4dd0e1
    style LoginPage fill:#ffd54f
```

## 9. Data Synchronization Flow

```mermaid
flowchart TD
    Start([User Action: CRUD]) --> Operation{Tipe Operasi}
    
    Operation -->|Create| PrepareCreate[Siapkan Data Baru]
    Operation -->|Read| PrepareRead[Siapkan Query]
    Operation -->|Update| PrepareUpdate[Siapkan Data Update]
    Operation -->|Delete| PrepareDelete[Siapkan Delete Request]
    
    PrepareCreate --> ValidateCreate{Data Valid?}
    ValidateCreate -->|Tidak| ErrorMsg1[Error Message]
    ValidateCreate -->|Ya| CreateAPI[Google Sheets API: Append Row]
    
    PrepareRead --> ReadAPI[Google Sheets API: Get Values]
    PrepareUpdate --> ValidateUpdate{Data Valid?}
    ValidateUpdate -->|Tidak| ErrorMsg2[Error Message]
    ValidateUpdate -->|Ya| UpdateAPI[Google Sheets API: Update Row]
    
    PrepareDelete --> ConfirmDelete{Konfirmasi?}
    ConfirmDelete -->|Tidak| Cancel[Cancel]
    ConfirmDelete -->|Ya| DeleteAPI[Google Sheets API: Delete Row]
    
    CreateAPI --> CheckCreateResponse{Response OK?}
    ReadAPI --> CheckReadResponse{Response OK?}
    UpdateAPI --> CheckUpdateResponse{Response OK?}
    DeleteAPI --> CheckDeleteResponse{Response OK?}
    
    CheckCreateResponse -->|Tidak| HandleError1[Handle Error]
    CheckCreateResponse -->|Ya| SuccessCreate[Success Message]
    
    CheckReadResponse -->|Tidak| HandleError2[Handle Error]
    CheckReadResponse -->|Ya| DisplayData[Tampilkan Data]
    
    CheckUpdateResponse -->|Tidak| HandleError3[Handle Error]
    CheckUpdateResponse -->|Ya| SuccessUpdate[Success Message]
    
    CheckDeleteResponse -->|Tidak| HandleError4[Handle Error]
    CheckDeleteResponse -->|Ya| SuccessDelete[Success Message]
    
    HandleError1 --> RetryLogic1{Retry?}
    HandleError2 --> RetryLogic2{Retry?}
    HandleError3 --> RetryLogic3{Retry?}
    HandleError4 --> RetryLogic4{Retry?}
    
    RetryLogic1 -->|Ya| CreateAPI
    RetryLogic1 -->|Tidak| FinalError1[Tampilkan Error]
    
    RetryLogic2 -->|Ya| ReadAPI
    RetryLogic2 -->|Tidak| FinalError2[Tampilkan Error]
    
    RetryLogic3 -->|Ya| UpdateAPI
    RetryLogic3 -->|Tidak| FinalError3[Tampilkan Error]
    
    RetryLogic4 -->|Ya| DeleteAPI
    RetryLogic4 -->|Tidak| FinalError4[Tampilkan Error]
    
    SuccessCreate --> RefreshUI[Refresh UI]
    SuccessUpdate --> RefreshUI
    SuccessDelete --> RefreshUI
    DisplayData --> RefreshUI
    
    RefreshUI --> End([Selesai])
    ErrorMsg1 --> End
    ErrorMsg2 --> End
    Cancel --> End
    FinalError1 --> End
    FinalError2 --> End
    FinalError3 --> End
    FinalError4 --> End
    
    style Start fill:#4dd0e1
    style End fill:#50e3c2
    style FinalError1 fill:#ff6b6b
    style FinalError2 fill:#ff6b6b
    style FinalError3 fill:#ff6b6b
    style FinalError4 fill:#ff6b6b
```

## 10. Complete System Architecture

```mermaid
graph TB
    subgraph "Client Side"
        Browser[Web Browser]
        UI[User Interface]
        JS[JavaScript/LocalStorage]
    end
    
    subgraph "Server Side - PHP"
        Auth[Authentication Handler]
        Session[Session Management]
        LinkCtrl[Link Controller]
        FormCtrl[Form Controller]
        FileCtrl[File Controller]
        Config[Config & Helper Functions]
    end
    
    subgraph "Google Services"
        OAuth[Google OAuth 2.0]
        SheetsAPI[Google Sheets API]
        DriveAPI[Google Drive API]
        GSheets[(Google Sheets Database)]
        GDrive[(Google Drive Storage)]
    end
    
    Browser --> UI
    UI --> JS
    JS --> Auth
    
    Auth --> OAuth
    OAuth --> Session
    
    Session --> LinkCtrl
    Session --> FormCtrl
    Session --> FileCtrl
    
    LinkCtrl --> SheetsAPI
    FormCtrl --> SheetsAPI
    FileCtrl --> DriveAPI
    FileCtrl --> SheetsAPI
    
    SheetsAPI --> GSheets
    DriveAPI --> GDrive
    
    Config --> Auth
    Config --> Session
    Config --> LinkCtrl
    Config --> FormCtrl
    Config --> FileCtrl
    
    style Browser fill:#4dd0e1
    style OAuth fill:#ffd54f
    style GSheets fill:#50e3c2
    style GDrive fill:#50e3c2
```

---

## Legend (Keterangan Warna)

- 🟢 **Hijau (#50e3c2, #4dd0e1)**: Success, Active State, Main Features
- 🟡 **Kuning (#ffd54f)**: Login/Authentication, Warning
- 🔴 **Merah (#ff6b6b)**: Error, Delete Actions, Logout
- ⚪ **Default**: Normal Process Flow

---

## Catatan Penggunaan

Flowchart ini menggambarkan:
1. **Alur Utama Aplikasi** - Dari login hingga akses fitur
2. **Autentikasi** - Proses Google OAuth 2.0
3. **Manajemen Link** - CRUD operations untuk links
4. **Manajemen Form** - Form builder dan response handling
5. **Manajemen File** - Upload/download dari Google Drive
6. **Session Management** - Timeout dan refresh token
7. **Language Switching** - i18n system
8. **Error Handling** - Berbagai tipe error dan solusinya
9. **Data Sync** - Sinkronisasi dengan Google Sheets/Drive
10. **Arsitektur Sistem** - Gambaran komponen lengkap

File ini dapat dirender di GitHub, GitLab, atau editor Markdown yang mendukung Mermaid.

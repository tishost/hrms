# PDF Package Installation Guide

## Laravel Backend PDF Setup

### 1. Install PDF Package
```bash
cd hrms
composer require barryvdh/laravel-dompdf
```

### 2. Publish Configuration (Optional)
```bash
php artisan vendor:publish --provider="Barryvdh\DomPDF\ServiceProvider"
```

### 3. Clear Cache
```bash
php artisan config:clear
php artisan cache:clear
```

### 4. Test PDF Generation
Visit: `http://your-domain/api/invoices/{invoice_id}/pdf`

## Flutter App PDF Setup

### 1. Install Dependencies
```bash
cd hrms_app
flutter pub get
```

### 2. Android Permissions
Already added to `android/app/src/main/AndroidManifest.xml`:
- WRITE_EXTERNAL_STORAGE
- READ_EXTERNAL_STORAGE
- MANAGE_EXTERNAL_STORAGE

### 3. Test PDF View
1. Go to Invoices screen
2. Tap on any invoice
3. Tap "View PDF" button
4. PDF should load and display

## Features

### Backend Features:
- ✅ PDF generation from invoice data
- ✅ Professional invoice template
- ✅ Complete invoice details
- ✅ Breakdown table
- ✅ Status badges
- ✅ Owner and tenant information

### Flutter App Features:
- ✅ PDF viewer with zoom and scroll
- ✅ Download PDF to device
- ✅ Error handling
- ✅ Loading states
- ✅ Permission handling
- ✅ Offline viewing (after download)

## API Endpoint

```
GET /api/invoices/{invoice_id}/pdf
Headers:
- Authorization: Bearer {token}
- Accept: application/pdf
```

## Troubleshooting

### Backend Issues:
1. **PDF not generating**: Check if DomPDF is installed
2. **Template not found**: Ensure `resources/views/pdf/invoice.blade.php` exists
3. **Permission denied**: Check storage permissions

### Flutter Issues:
1. **PDF not loading**: Check network connection and API endpoint
2. **Permission denied**: Grant storage permissions in app settings
3. **Download failed**: Check if Downloads folder is accessible

## Notes

- PDF files are temporarily stored in app's cache directory
- Downloaded PDFs are saved to device's Downloads folder
- PDF generation requires valid authentication token
- Template supports both English and Bengali text 

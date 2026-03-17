$passed = 0
$errors = 0
$error_details = @()

# Models
$models = @(
    ".\app\Models\Armada.php",
    ".\app\Models\Attendance.php",
    ".\app\Models\Coa.php",
    ".\app\Models\EmployeeOutput.php",
    ".\app\Models\HasilPilahan.php",
    ".\app\Models\Invoice.php",
    ".\app\Models\JurnalDetail.php",
    ".\app\Models\JurnalHeader.php",
    ".\app\Models\JurnalKas.php",
    ".\app\Models\Klien.php",
    ".\app\Models\Penjualan.php",
    ".\app\Models\ProduksiHarian.php",
    ".\app\Models\Ritase.php",
    ".\app\Models\Tenant.php",
    ".\app\Models\User.php",
    ".\app\Models\WageCalculation.php",
    ".\app\Models\WageRate.php",
    ".\app\Models\WasteCategory.php"
)

# Controllers
$controllers = @(
    ".\app\Http\Controllers\Admin\ActivityController.php",
    ".\app\Http\Controllers\Admin\ArmadaController.php",
    ".\app\Http\Controllers\Admin\AttendanceController.php",
    ".\app\Http\Controllers\Admin\CoaController.php",
    ".\app\Http\Controllers\Admin\CompanySettingsController.php",
    ".\app\Http\Controllers\Admin\DashboardController.php",
    ".\app\Http\Controllers\Admin\EmployeeOutputController.php",
    ".\app\Http\Controllers\Admin\HasilPilahanController.php",
    ".\app\Http\Controllers\Admin\InvoiceAdminController.php",
    ".\app\Http\Controllers\Admin\JurnalController.php",
    ".\app\Http\Controllers\Admin\JurnalKasController.php",
    ".\app\Http\Controllers\Admin\KlienController.php",
    ".\app\Http\Controllers\Admin\LaporanController.php",
    ".\app\Http\Controllers\Admin\PenjualanController.php",
    ".\app\Http\Controllers\Admin\RitaseController.php",
    ".\app\Http\Controllers\Admin\RoleController.php",
    ".\app\Http\Controllers\Admin\Test500Controller.php",
    ".\app\Http\Controllers\Admin\UserController.php",
    ".\app\Http\Controllers\Admin\WageCalculationController.php",
    ".\app\Http\Controllers\Admin\WageRateController.php",
    ".\app\Http\Controllers\Admin\WasteCategoryController.php"
)

# Policies
$policies = @(
    ".\app\Policies\ArmadaPolicy.php",
    ".\app\Policies\AttendancePolicy.php",
    ".\app\Policies\CoaPolicy.php",
    ".\app\Policies\EmployeeOutputPolicy.php",
    ".\app\Policies\HasilPilahanPolicy.php",
    ".\app\Policies\InvoicePolicy.php",
    ".\app\Policies\JurnalHeaderPolicy.php",
    ".\app\Policies\JurnalKasPolicy.php",
    ".\app\Policies\KlienPolicy.php",
    ".\app\Policies\PenjualanPolicy.php",
    ".\app\Policies\RitasePolicy.php",
    ".\app\Policies\UserPolicy.php",
    ".\app\Policies\WageCalculationPolicy.php",
    ".\app\Policies\WageRatePolicy.php",
    ".\app\Policies\WasteCategoryPolicy.php"
)

# Services
$services = @(
    ".\app\Services\TenantProvisioningService.php",
    ".\app\Services\WageCalculationService.php"
)

function Check-PHPSyntax {
    param([string]$FilePath, [string]$Category)
    
    $output = & php -l $FilePath 2>&1
    
    if ($output -like "*No syntax errors detected*") {
        Write-Host "✅ $FilePath" -ForegroundColor Green
        return $true
    } else {
        Write-Host "❌ $FilePath" -ForegroundColor Red
        $error_details += "$FilePath`n$output"
        return $false
    }
}

Write-Host "`n=== MODELS ===" -ForegroundColor Cyan
foreach ($file in $models) {
    if (Check-PHPSyntax $file "Models") { $passed++ } else { $errors++ }
}

Write-Host "`n=== CONTROLLERS ===" -ForegroundColor Cyan
foreach ($file in $controllers) {
    if (Check-PHPSyntax $file "Controllers") { $passed++ } else { $errors++ }
}

Write-Host "`n=== POLICIES ===" -ForegroundColor Cyan
foreach ($file in $policies) {
    if (Check-PHPSyntax $file "Policies") { $passed++ } else { $errors++ }
}

Write-Host "`n=== SERVICES ===" -ForegroundColor Cyan
foreach ($file in $services) {
    if (Check-PHPSyntax $file "Services") { $passed++ } else { $errors++ }
}

Write-Host "`n=== SUMMARY ===" -ForegroundColor Cyan
Write-Host "Summary: $passed passed, $errors failed"

if ($errors -eq 0) {
    Write-Host "✅ All files have valid PHP syntax!" -ForegroundColor Green
} else {
    Write-Host "❌ $errors file(s) with syntax errors" -ForegroundColor Red
    Write-Host "`nDetails:" -ForegroundColor Yellow
    foreach ($detail in $error_details) {
        Write-Host $detail
    }
}

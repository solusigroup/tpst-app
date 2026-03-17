@echo off
setlocal enabledelayedexpansion

set passed=0
set errors=0

REM Models
for %%F in (
    "d:\PROJECT_HERD\tpst-app\app\Models\Armada.php"
    "d:\PROJECT_HERD\tpst-app\app\Models\Attendance.php"
    "d:\PROJECT_HERD\tpst-app\app\Models\Coa.php"
    "d:\PROJECT_HERD\tpst-app\app\Models\EmployeeOutput.php"
    "d:\PROJECT_HERD\tpst-app\app\Models\HasilPilahan.php"
    "d:\PROJECT_HERD\tpst-app\app\Models\Invoice.php"
    "d:\PROJECT_HERD\tpst-app\app\Models\JurnalDetail.php"
    "d:\PROJECT_HERD\tpst-app\app\Models\JurnalHeader.php"
    "d:\PROJECT_HERD\tpst-app\app\Models\JurnalKas.php"
    "d:\PROJECT_HERD\tpst-app\app\Models\Klien.php"
    "d:\PROJECT_HERD\tpst-app\app\Models\Penjualan.php"
    "d:\PROJECT_HERD\tpst-app\app\Models\ProduksiHarian.php"
    "d:\PROJECT_HERD\tpst-app\app\Models\Ritase.php"
    "d:\PROJECT_HERD\tpst-app\app\Models\Tenant.php"
    "d:\PROJECT_HERD\tpst-app\app\Models\User.php"
    "d:\PROJECT_HERD\tpst-app\app\Models\WageCalculation.php"
    "d:\PROJECT_HERD\tpst-app\app\Models\WageRate.php"
    "d:\PROJECT_HERD\tpst-app\app\Models\WasteCategory.php"
) do (
    php -l %%F >nul 2>&1
    if errorlevel 1 (
        echo ❌ %%F
        php -l %%F
        set /a errors=!errors!+1
    ) else (
        set /a passed=!passed!+1
    )
)

REM Controllers
for %%F in (
    "d:\PROJECT_HERD\tpst-app\app\Http\Controllers\Admin\ActivityController.php"
    "d:\PROJECT_HERD\tpst-app\app\Http\Controllers\Admin\ArmadaController.php"
    "d:\PROJECT_HERD\tpst-app\app\Http\Controllers\Admin\AttendanceController.php"
    "d:\PROJECT_HERD\tpst-app\app\Http\Controllers\Admin\CoaController.php"
    "d:\PROJECT_HERD\tpst-app\app\Http\Controllers\Admin\CompanySettingsController.php"
    "d:\PROJECT_HERD\tpst-app\app\Http\Controllers\Admin\DashboardController.php"
    "d:\PROJECT_HERD\tpst-app\app\Http\Controllers\Admin\EmployeeOutputController.php"
    "d:\PROJECT_HERD\tpst-app\app\Http\Controllers\Admin\HasilPilahanController.php"
    "d:\PROJECT_HERD\tpst-app\app\Http\Controllers\Admin\InvoiceAdminController.php"
    "d:\PROJECT_HERD\tpst-app\app\Http\Controllers\Admin\JurnalController.php"
    "d:\PROJECT_HERD\tpst-app\app\Http\Controllers\Admin\JurnalKasController.php"
    "d:\PROJECT_HERD\tpst-app\app\Http\Controllers\Admin\KlienController.php"
    "d:\PROJECT_HERD\tpst-app\app\Http\Controllers\Admin\LaporanController.php"
    "d:\PROJECT_HERD\tpst-app\app\Http\Controllers\Admin\PenjualanController.php"
    "d:\PROJECT_HERD\tpst-app\app\Http\Controllers\Admin\RitaseController.php"
    "d:\PROJECT_HERD\tpst-app\app\Http\Controllers\Admin\RoleController.php"
    "d:\PROJECT_HERD\tpst-app\app\Http\Controllers\Admin\Test500Controller.php"
    "d:\PROJECT_HERD\tpst-app\app\Http\Controllers\Admin\UserController.php"
    "d:\PROJECT_HERD\tpst-app\app\Http\Controllers\Admin\WageCalculationController.php"
    "d:\PROJECT_HERD\tpst-app\app\Http\Controllers\Admin\WageRateController.php"
    "d:\PROJECT_HERD\tpst-app\app\Http\Controllers\Admin\WasteCategoryController.php"
) do (
    php -l %%F >nul 2>&1
    if errorlevel 1 (
        echo ❌ %%F
        php -l %%F
        set /a errors=!errors!+1
    ) else (
        set /a passed=!passed!+1
    )
)

REM Policies
for %%F in (
    "d:\PROJECT_HERD\tpst-app\app\Policies\ArmadaPolicy.php"
    "d:\PROJECT_HERD\tpst-app\app\Policies\AttendancePolicy.php"
    "d:\PROJECT_HERD\tpst-app\app\Policies\CoaPolicy.php"
    "d:\PROJECT_HERD\tpst-app\app\Policies\EmployeeOutputPolicy.php"
    "d:\PROJECT_HERD\tpst-app\app\Policies\HasilPilahanPolicy.php"
    "d:\PROJECT_HERD\tpst-app\app\Policies\InvoicePolicy.php"
    "d:\PROJECT_HERD\tpst-app\app\Policies\JurnalHeaderPolicy.php"
    "d:\PROJECT_HERD\tpst-app\app\Policies\JurnalKasPolicy.php"
    "d:\PROJECT_HERD\tpst-app\app\Policies\KlienPolicy.php"
    "d:\PROJECT_HERD\tpst-app\app\Policies\PenjualanPolicy.php"
    "d:\PROJECT_HERD\tpst-app\app\Policies\RitasePolicy.php"
    "d:\PROJECT_HERD\tpst-app\app\Policies\UserPolicy.php"
    "d:\PROJECT_HERD\tpst-app\app\Policies\WageCalculationPolicy.php"
    "d:\PROJECT_HERD\tpst-app\app\Policies\WageRatePolicy.php"
    "d:\PROJECT_HERD\tpst-app\app\Policies\WasteCategoryPolicy.php"
) do (
    php -l %%F >nul 2>&1
    if errorlevel 1 (
        echo ❌ %%F
        php -l %%F
        set /a errors=!errors!+1
    ) else (
        set /a passed=!passed!+1
    )
)

REM Services
for %%F in (
    "d:\PROJECT_HERD\tpst-app\app\Services\TenantProvisioningService.php"
    "d:\PROJECT_HERD\tpst-app\app\Services\WageCalculationService.php"
) do (
    php -l %%F >nul 2>&1
    if errorlevel 1 (
        echo ❌ %%F
        php -l %%F
        set /a errors=!errors!+1
    ) else (
        set /a passed=!passed!+1
    )
)

echo.
echo Summary: %passed% passed, %errors% failed
if %errors% equ 0 (
    echo ✅ All files have valid PHP syntax!
) else (
    echo ❌ %errors% file(s) with syntax errors
)

endlocal

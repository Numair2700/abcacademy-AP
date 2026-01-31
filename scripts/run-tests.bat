@echo off
REM ABC Academy - Automated Testing Script (Windows)
REM Comprehensive test automation with reporting

echo.
echo ========================================
echo   ABC Academy - Automated Testing
echo ========================================
echo.

REM Set colors for output
set "GREEN=[92m"
set "RED=[91m"
set "YELLOW=[93m"
set "BLUE=[94m"
set "RESET=[0m"

REM Check if PHP is available
php --version >nul 2>&1
if %errorlevel% neq 0 (
    echo %RED%❌ PHP is not available in PATH%RESET%
    echo Please ensure PHP is installed and added to your PATH
    exit /b 1
)

REM Check if Composer is available
composer --version >nul 2>&1
if %errorlevel% neq 0 (
    echo %RED%❌ Composer is not available in PATH%RESET%
    echo Please ensure Composer is installed and added to your PATH
    exit /b 1
)

REM Parse command line arguments
set "TEST_TYPE=all"
set "COVERAGE=false"
set "PARALLEL=false"
set "WATCH=false"

:parse_args
if "%1"=="" goto :run_tests
if "%1"=="--unit" set "TEST_TYPE=unit"
if "%1"=="--feature" set "TEST_TYPE=feature"
if "%1"=="--performance" set "TEST_TYPE=performance"
if "%1"=="--security" set "TEST_TYPE=security"
if "%1"=="--api" set "TEST_TYPE=api"
if "%1"=="--coverage" set "COVERAGE=true"
if "%1"=="--parallel" set "PARALLEL=true"
if "%1"=="--watch" set "WATCH=true"
if "%1"=="--help" goto :show_help
shift
goto :parse_args

:show_help
echo Usage: run-tests.bat [options]
echo.
echo Options:
echo   --unit         Run unit tests only
echo   --feature      Run feature tests only
echo   --performance  Run performance tests only
echo   --security     Run security tests only
echo   --api          Run API tests only
echo   --coverage     Generate coverage report
echo   --parallel     Run tests in parallel
echo   --watch        Run tests in watch mode
echo   --help         Show this help message
echo.
echo Examples:
echo   run-tests.bat --unit --coverage
echo   run-tests.bat --performance --parallel
echo   run-tests.bat --watch
echo.
exit /b 0

:run_tests
echo %BLUE%🔧 Preparing test environment...%RESET%

REM Clear caches
echo Clearing caches...
php artisan config:clear >nul 2>&1
php artisan cache:clear >nul 2>&1
php artisan route:clear >nul 2>&1
php artisan view:clear >nul 2>&1

REM Ensure database exists
if not exist "database\database.sqlite" (
    echo Creating test database...
    type nul > "database\database.sqlite"
)

REM Run migrations
echo Running migrations...
php artisan migrate --force >nul 2>&1

echo %GREEN%✅ Test environment ready!%RESET%
echo.

REM Determine test command based on options
set "TEST_CMD=php artisan test"

if "%TEST_TYPE%"=="unit" set "TEST_CMD=%TEST_CMD% --testsuite=Unit"
if "%TEST_TYPE%"=="feature" set "TEST_CMD=%TEST_CMD% --testsuite=Feature"
if "%TEST_TYPE%"=="performance" set "TEST_CMD=%TEST_CMD% tests/Performance"
if "%TEST_TYPE%"=="security" set "TEST_CMD=%TEST_CMD% tests/Feature/SecurityTest.php"
if "%TEST_TYPE%"=="api" set "TEST_CMD=%TEST_CMD% tests/Feature/ApiTest.php"

if "%COVERAGE%"=="true" set "TEST_CMD=%TEST_CMD% --coverage"
if "%PARALLEL%"=="true" set "TEST_CMD=%TEST_CMD% --parallel"
if "%WATCH%"=="true" set "TEST_CMD=%TEST_CMD% --watch"

REM Display test configuration
echo %YELLOW%📋 Test Configuration:%RESET%
echo   Test Type: %TEST_TYPE%
echo   Coverage: %COVERAGE%
echo   Parallel: %PARALLEL%
echo   Watch Mode: %WATCH%
echo   Command: %TEST_CMD%
echo.

REM Record start time
for /f "tokens=2 delims==" %%a in ('wmic OS Get localdatetime /value') do set "dt=%%a"
set "start_time=%dt:~8,2%:%dt:~10,2%:%dt:~12,2%"

echo %BLUE%🧪 Running tests...%RESET%
echo ========================================
echo.

REM Run the tests
%TEST_CMD%

REM Check test results
if %errorlevel% equ 0 (
    echo.
    echo ========================================
    echo %GREEN%✅ All tests passed successfully!%RESET%
    echo.
    
    REM Record end time
    for /f "tokens=2 delims==" %%a in ('wmic OS Get localdatetime /value') do set "dt=%%a"
    set "end_time=%dt:~8,2%:%dt:~10,2%:%dt:~12,2%"
    
    echo %GREEN%📊 Test Summary:%RESET%
    echo   Status: PASSED
    echo   Start Time: %start_time%
    echo   End Time: %end_time%
    echo   Test Type: %TEST_TYPE%
    
    if "%COVERAGE%"=="true" (
        echo   Coverage: Generated
        if exist "coverage\index.html" (
            echo   Coverage Report: coverage\index.html
        )
    )
    
    echo.
    echo %GREEN%🎉 Testing completed successfully!%RESET%
    exit /b 0
) else (
    echo.
    echo ========================================
    echo %RED%❌ Tests failed!%RESET%
    echo.
    
    echo %RED%📊 Test Summary:%RESET%
    echo   Status: FAILED
    echo   Start Time: %start_time%
    echo   Test Type: %TEST_TYPE%
    echo   Exit Code: %errorlevel%
    
    echo.
    echo %YELLOW%💡 Tips:%RESET%
    echo   - Check the test output above for specific failures
    echo   - Run 'php artisan test --stop-on-failure' to stop on first failure
    echo   - Run 'php artisan test --verbose' for more detailed output
    echo   - Check your database configuration and migrations
    echo.
    echo %RED%🚫 Testing failed!%RESET%
    exit /b 1
)

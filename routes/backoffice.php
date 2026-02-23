<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backoffice\AuthController;
use App\Http\Controllers\Backoffice\UserController;
use App\Http\Controllers\Backoffice\ProfileController;
use App\Http\Controllers\Backoffice\VehicleBrandController;
use App\Http\Controllers\Backoffice\VehicleModelController;
use App\Http\Controllers\Backoffice\VehicleController;
use App\Http\Controllers\Backoffice\AgencyController;
use App\Http\Controllers\Backoffice\AgentController;
use App\Http\Controllers\Backoffice\ClientController;
use App\Http\Controllers\Backoffice\AgencySubscriptionController;
use App\Http\Controllers\Backoffice\BookingController;
use App\Http\Controllers\Backoffice\RoleController;
use App\Http\Controllers\Backoffice\PermissionController;
use App\Http\Controllers\Backoffice\RolesPermissionsController;
use App\Http\Controllers\Backoffice\AgencySettingsController;
use App\Http\Controllers\Backoffice\Vehicles\VignetteController;
use App\Http\Controllers\Backoffice\Vehicles\InsuranceController;
use App\Http\Controllers\Backoffice\Vehicles\TechnicalCheckController;
use App\Http\Controllers\Backoffice\Vehicles\OilChangeController;
use App\Http\Controllers\Backoffice\Vehicles\ControlController;
use App\Http\Controllers\Backoffice\Vehicles\ControlItemController;
use App\Http\Controllers\Backoffice\RentalContractController;
use App\Http\Controllers\Backoffice\ContractClientController;
use App\Http\Controllers\Backoffice\InvoiceController;
use App\Http\Controllers\Backoffice\Finance\FinancialAccountController;
use App\Http\Controllers\Backoffice\Finance\TransactionCategoryController;
use App\Http\Controllers\Backoffice\Finance\FinancialTransactionController;
use App\Http\Controllers\Backoffice\InvoiceItemController;
use App\Http\Controllers\Backoffice\PaymentController;
use App\Http\Controllers\Backoffice\NotificationController;
use App\Http\Controllers\Backoffice\ContractPDFController;
use App\Http\Controllers\Backoffice\InvoicePDFController;
use App\Http\Controllers\Backoffice\VehicleCreditController;
Route::prefix('backoffice')
    ->name('backoffice.')
    ->group(function () {
        // Guest routes
        Route::middleware('guest:backoffice')->group(function () {
            Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
            Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
            Route::get('/login/demo', [AuthController::class, 'demoLogin'])->name('login.demo');
        });

        // Authenticated routes
        Route::middleware(['auth:backoffice'])->group(function () {
            // Profile password change
            Route::get('/profile/change-password', [ProfileController::class, 'showChangePassword'])
                ->name('profile.change-password')
                ->middleware('role:super-admin|admin|manager,backoffice');

            Route::put('/profile/change-password', [ProfileController::class, 'updatePassword'])
                ->name('profile.update-password')
                ->middleware('role:super-admin|admin|manager,backoffice');

            // Logout
            Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

            // Dashboard
            Route::view('/dashboard', 'backoffice.index')
                ->name('dashboard')
                ->middleware('role:super-admin|admin|manager,backoffice');

            // ==================== USERS ====================
            Route::prefix('users')
                ->name('users.')
                ->middleware('role:super-admin|admin,backoffice')
                ->group(function () {
                    Route::get('/', [UserController::class, 'index'])->name('index');
                    Route::get('/create', [UserController::class, 'create'])->name('create');
                    Route::post('/', [UserController::class, 'store'])->name('store');
                    Route::get('/{user}', [UserController::class, 'show'])->name('show');
                    Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
                    Route::put('/{user}', [UserController::class, 'update'])->name('update');
                    Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
                });

            // ==================== CLIENTS ====================
            Route::prefix('clients')
                ->name('clients.')
                ->middleware('role:super-admin|admin|manager,backoffice')
                ->group(function () {
                    Route::get('/', [ClientController::class, 'index'])->name('index');
                    Route::get('/create', [ClientController::class, 'create'])->name('create');
                    Route::post('/', [ClientController::class, 'store'])->name('store');
                    Route::get('/{client}', [ClientController::class, 'show'])->name('show');
                    Route::get('/{client}/edit', [ClientController::class, 'edit'])->name('edit');
                    Route::put('/{client}', [ClientController::class, 'update'])->name('update');
                    Route::delete('/{client}', [ClientController::class, 'destroy'])->name('destroy');
                });

            // ==================== PROFILE ====================
            Route::get('/profile', [ProfileController::class, 'edit'])
                ->name('profile.edit')
                ->middleware('role:super-admin|admin|manager,backoffice');
            Route::put('/profile', [ProfileController::class, 'update'])
                ->name('profile.update')
                ->middleware('role:super-admin|admin|manager,backoffice');

            // ==================== AGENCIES ====================
            Route::prefix('agencies')
                ->name('agencies.')
                ->middleware('role:super-admin|admin,backoffice')
                ->group(function () {
                    Route::get('/', [AgencyController::class, 'index'])->name('index');
                    Route::get('/create', [AgencyController::class, 'create'])->name('create');
                    Route::post('/', [AgencyController::class, 'store'])->name('store');
                    Route::get('/{agency}', [AgencyController::class, 'show'])->name('show');
                    Route::get('/{agency}/edit', [AgencyController::class, 'edit'])->name('edit');
                    Route::put('/{agency}', [AgencyController::class, 'update'])->name('update');
                    Route::delete('/{agency}', [AgencyController::class, 'destroy'])->name('destroy');

                    // ==================== AGENCY SETTINGS ====================
                    Route::prefix('{agency}/settings')
                        ->name('settings.')
                        ->middleware('role:super-admin,backoffice')
                        ->group(function () {
                            // GET routes
                            Route::get('/', [AgencySettingsController::class, 'edit'])->name('edit');
                            Route::get('/profile', [AgencyController::class, 'profile'])->name('profile');
                            Route::get('/notifications', [AgencySettingsController::class, 'notifications'])->name('notifications');
                            Route::get('/invoice-template', [AgencySettingsController::class, 'invoiceTemplate'])->name('invoice-template');
                            Route::get('/company', [AgencySettingsController::class, 'company'])->name('company');
                            Route::get('/signatures', [AgencySettingsController::class, 'signatures'])->name('signatures');
                            
                            // UPDATE routes
                            Route::patch('/', [AgencySettingsController::class, 'update'])->name('update');
                            Route::post('/company', [AgencySettingsController::class, 'updateCompany'])->name('update.company');
                            
                            // Agency profile update route
                            Route::post('/profile', [AgencyController::class, 'updateProfile'])->name('update.profile');
                            
                            // DELETE routes
                            Route::delete('/delete-logo', [AgencySettingsController::class, 'deleteLogo'])->name('delete-logo');
                            Route::delete('/delete-signature', [AgencySettingsController::class, 'deleteSignature'])->name('delete-signature');
                        });
                });

            // ==================== AGENTS ====================
            Route::prefix('agents')
                ->name('agents.')
                ->middleware('role:super-admin|admin|manager,backoffice')
                ->group(function () {
                    Route::get('/', [AgentController::class, 'index'])->name('index');
                    Route::get('/create', [AgentController::class, 'create'])->name('create');
                    Route::post('/', [AgentController::class, 'store'])->name('store');
                    Route::get('/{agent}', [AgentController::class, 'show'])->name('show');
                    Route::get('/{agent}/edit', [AgentController::class, 'edit'])->name('edit');
                    Route::put('/{agent}', [AgentController::class, 'update'])->name('update');
                    Route::delete('/{agent}', [AgentController::class, 'destroy'])->name('destroy');
                });

            // ==================== AGENCY SUBSCRIPTIONS ====================
            Route::prefix('agency-subscriptions')
                ->name('agency-subscriptions.')
                ->middleware('role:super-admin|admin,backoffice')
                ->group(function () {
                    Route::get('/', [AgencySubscriptionController::class, 'index'])->name('index');
                    Route::get('/create', [AgencySubscriptionController::class, 'create'])->name('create');
                    Route::post('/', [AgencySubscriptionController::class, 'store'])->name('store');
                    Route::get('/{agencySubscription}', [AgencySubscriptionController::class, 'show'])->name('show');
                    Route::get('/{agencySubscription}/edit', [AgencySubscriptionController::class, 'edit'])->name('edit');
                    Route::put('/{agencySubscription}', [AgencySubscriptionController::class, 'update'])->name('update');
                    Route::delete('/{agencySubscription}', [AgencySubscriptionController::class, 'destroy'])->name('destroy');
                });

            // ==================== ROLES & PERMISSIONS ====================
            Route::middleware('role:super-admin|admin,backoffice')->group(function () {
                // Roles Index (separated from permissions)
                Route::get('/roles-permissions', [RolesPermissionsController::class, 'indexRoles'])->name('roles-permissions.roles');

                // Permissions for a specific role
                Route::get('/roles-permissions/{role}/permissions', [RolesPermissionsController::class, 'showPermissions'])->name('roles-permissions.permissions');
                Route::put('/roles-permissions/{role}/permissions', [RolesPermissionsController::class, 'updatePermissions'])->name('roles-permissions.permissions.update');

                Route::prefix('roles')
                    ->name('roles.')
                    ->group(function () {
                        Route::post('/', [RoleController::class, 'store'])->name('store');
                        Route::put('/{role}', [RoleController::class, 'update'])->name('update');
                        Route::delete('/{role}', [RoleController::class, 'destroy'])->name('destroy');
                    });

                Route::prefix('permissions')
                    ->name('permissions.')
                    ->group(function () {
                        Route::post('/', [PermissionController::class, 'store'])->name('store');
                        Route::put('/{permission}', [PermissionController::class, 'update'])->name('update');
                        Route::delete('/{permission}', [PermissionController::class, 'destroy'])->name('destroy');
                    });
            });

            // ==================== VEHICLE BRANDS ====================
            Route::prefix('vehicle-brands')
                ->name('vehicle-brands.')
                ->middleware('role:super-admin|admin|manager,backoffice')
                ->group(function () {
                    Route::get('/', [VehicleBrandController::class, 'index'])->name('index');
                    Route::get('/create', [VehicleBrandController::class, 'create'])->name('create');
                    Route::post('/', [VehicleBrandController::class, 'store'])->name('store');
                    Route::get('/{vehicleBrand}', [VehicleBrandController::class, 'show'])->name('show');
                    Route::get('/{vehicleBrand}/edit', [VehicleBrandController::class, 'edit'])->name('edit');
                    Route::put('/{vehicleBrand}', [VehicleBrandController::class, 'update'])->name('update');
                    Route::delete('/{vehicleBrand}', [VehicleBrandController::class, 'destroy'])->name('destroy');
                });

            // ==================== VEHICLE MODELS ====================
            Route::prefix('vehicle-models')
                ->name('vehicle-models.')
                ->middleware('role:super-admin|admin|manager,backoffice')
                ->group(function () {
                    Route::get('/', [VehicleModelController::class, 'index'])->name('index');
                    Route::get('/create', [VehicleModelController::class, 'create'])->name('create');
                    Route::post('/', [VehicleModelController::class, 'store'])->name('store');
                    Route::get('/{vehicleModel}', [VehicleModelController::class, 'show'])->name('show');
                    Route::get('/{vehicleModel}/edit', [VehicleModelController::class, 'edit'])->name('edit');
                    Route::put('/{vehicleModel}', [VehicleModelController::class, 'update'])->name('update');
                    Route::delete('/{vehicleModel}', [VehicleModelController::class, 'destroy'])->name('destroy');
                });

            // ==================== VEHICLES ====================
            Route::prefix('vehicles')
                ->name('vehicles.')
                ->middleware('role:super-admin|admin|manager,backoffice')
                ->group(function () {
                    Route::delete('/bulk-destroy', [VehicleController::class, 'bulkDestroy'])->name('bulkDestroy');

                    Route::get('/', [VehicleController::class, 'index'])->name('index');
                    Route::post('/check-duplicate', [VehicleController::class, 'checkDuplicate'])->name('check-duplicate');
                    Route::get('/create', [VehicleController::class, 'create'])->name('create');
                    Route::post('/', [VehicleController::class, 'store'])->name('store');
                    Route::get('/{vehicle}', [VehicleController::class, 'show'])->name('show');
                    Route::get('/{vehicle}/edit', [VehicleController::class, 'edit'])->name('edit');
                    Route::put('/{vehicle}', [VehicleController::class, 'update'])->name('update');
                    Route::delete('/{vehicle}', [VehicleController::class, 'destroy'])->name('destroy');

                    // Vehicle Vignettes
                    Route::prefix('{vehicle}/vignettes')
                        ->name('vignettes.')
                        ->group(function () {
                            Route::get('/', [VignetteController::class, 'index'])->name('index');
                            Route::get('/create', [VignetteController::class, 'create'])->name('create');
                            Route::post('/', [VignetteController::class, 'store'])->name('store');
                            Route::get('/{vignette}', [VignetteController::class, 'show'])->name('show');
                            Route::get('/{vignette}/edit', [VignetteController::class, 'edit'])->name('edit');
                            Route::put('/{vignette}', [VignetteController::class, 'update'])->name('update');
                            Route::delete('/{vignette}', [VignetteController::class, 'destroy'])->name('destroy');
                        });

                    // Vehicle Insurances
                    Route::prefix('{vehicle}/insurances')
                        ->name('insurances.')
                        ->group(function () {
                            Route::get('/', [InsuranceController::class, 'index'])->name('index');
                            Route::get('/create', [InsuranceController::class, 'create'])->name('create');
                            Route::post('/', [InsuranceController::class, 'store'])->name('store');
                            Route::get('/{insurance}', [InsuranceController::class, 'show'])->name('show');
                            Route::get('/{insurance}/edit', [InsuranceController::class, 'edit'])->name('edit');
                            Route::put('/{insurance}', [InsuranceController::class, 'update'])->name('update');
                            Route::delete('/{insurance}', [InsuranceController::class, 'destroy'])->name('destroy');
                        });

                    // Vehicle Oil Changes
                    Route::prefix('{vehicle}/oil-changes')
                        ->name('oil-changes.')
                        ->group(function () {
                            Route::get('/', [OilChangeController::class, 'index'])->name('index');
                            Route::get('/create', [OilChangeController::class, 'create'])->name('create');
                            Route::post('/', [OilChangeController::class, 'store'])->name('store');
                            Route::get('/{oilChange}', [OilChangeController::class, 'show'])->name('show');
                            Route::get('/{oilChange}/edit', [OilChangeController::class, 'edit'])->name('edit');
                            Route::put('/{oilChange}', [OilChangeController::class, 'update'])->name('update');
                            Route::delete('/{oilChange}', [OilChangeController::class, 'destroy'])->name('destroy');
                        });

                    // Vehicle Technical Checks
                    Route::prefix('{vehicle}/technical-checks')
                        ->name('technical-checks.')
                        ->group(function () {
                            Route::get('/', [TechnicalCheckController::class, 'index'])->name('index');
                            Route::get('/create', [TechnicalCheckController::class, 'create'])->name('create');
                            Route::post('/', [TechnicalCheckController::class, 'store'])->name('store');
                            Route::get('/{technicalCheck}', [TechnicalCheckController::class, 'show'])->name('show');
                            Route::get('/{technicalCheck}/edit', [TechnicalCheckController::class, 'edit'])->name('edit');
                            Route::put('/{technicalCheck}', [TechnicalCheckController::class, 'update'])->name('update');
                            Route::delete('/{technicalCheck}', [TechnicalCheckController::class, 'destroy'])->name('destroy');
                        });

                    // Vehicle Controls (Nested)
                    Route::prefix('{vehicle}/controls')
                        ->name('controls.')
                        ->group(function () {
                            Route::get('/', [ControlController::class, 'index'])->name('index');
                            Route::get('/create', [ControlController::class, 'create'])->name('create');
                            Route::post('/', [ControlController::class, 'store'])->name('store');
                            Route::get('/{control}', [ControlController::class, 'show'])->name('show');
                            Route::get('/{control}/edit', [ControlController::class, 'edit'])->name('edit');
                            Route::put('/{control}', [ControlController::class, 'update'])->name('update');
                            Route::delete('/{control}', [ControlController::class, 'destroy'])->name('destroy');
                        });
                }); // END VEHICLES GROUP

            // ==================== CONTROLS (Standalone) ====================
            Route::prefix('controls')
                ->name('controls.')
                ->middleware('role:super-admin|admin|manager,backoffice')
                ->group(function () {
                    Route::get('/', [ControlController::class, 'index'])->name('index');
                    Route::get('/create', [ControlController::class, 'create'])->name('create');
                    Route::post('/', [ControlController::class, 'store'])->name('store');
                    Route::get('/{control}', [ControlController::class, 'show'])->name('show');
                    Route::get('/{control}/edit', [ControlController::class, 'edit'])->name('edit');
                    Route::put('/{control}', [ControlController::class, 'update'])->name('update');
                    Route::delete('/{control}', [ControlController::class, 'destroy'])->name('destroy');
                });

            // ==================== CONTROL ITEMS (Standalone) ====================
            Route::prefix('control-items')
                ->name('control-items.')
                ->middleware('role:super-admin|admin|manager,backoffice')
                ->group(function () {
                    Route::get('/', [ControlItemController::class, 'index'])->name('index');
                    Route::get('/create', [ControlItemController::class, 'create'])->name('create');
                    Route::post('/', [ControlItemController::class, 'store'])->name('store');
                    Route::get('/{item}', [ControlItemController::class, 'show'])->name('show');
                    Route::get('/{item}/edit', [ControlItemController::class, 'edit'])->name('edit');
                    Route::put('/{item}', [ControlItemController::class, 'update'])->name('update');
                    Route::delete('/{item}', [ControlItemController::class, 'destroy'])->name('destroy');
                });

            // ==================== RENTAL CONTRACTS ====================
            Route::prefix('rental-contracts')
                ->name('rental-contracts.')
                ->middleware('role:super-admin|admin|manager,backoffice')
                ->group(function () {
                    Route::get('/', [RentalContractController::class, 'index'])->name('index');
                    Route::get('/create', [RentalContractController::class, 'create'])->name('create');
                    Route::post('/', [RentalContractController::class, 'store'])->name('store');
                    Route::get('/{rentalContract}', [RentalContractController::class, 'show'])->name('show');
                    Route::get('/{rentalContract}/edit', [RentalContractController::class, 'edit'])->name('edit');
                    Route::put('/{rentalContract}', [RentalContractController::class, 'update'])->name('update');
                    Route::delete('/{rentalContract}', [RentalContractController::class, 'destroy'])->name('destroy');
                    Route::post('/{rentalContract}/status', [RentalContractController::class, 'updateStatus'])->name('status');

                    // Contract Clients (nested)
                    Route::prefix('{rentalContract}/clients')
                        ->name('clients.')
                        ->group(function () {
                            Route::get('/', [ContractClientController::class, 'index'])->name('index');
                            Route::get('/create', [ContractClientController::class, 'create'])->name('create');
                            Route::post('/', [ContractClientController::class, 'store'])->name('store');
                            Route::get('/{contractClient}/edit', [ContractClientController::class, 'edit'])->name('edit');
                            Route::put('/{contractClient}', [ContractClientController::class, 'update'])->name('update');
                            Route::delete('/{contractClient}', [ContractClientController::class, 'destroy'])->name('destroy');
                        });
                });

            // ==================== CONTRACT PDF ====================
            Route::prefix('contracts/pdf')
                ->name('contracts.pdf.')
                ->middleware('role:super-admin|admin|manager,backoffice')
                ->group(function () {
                    Route::get('/{id}', [ContractPDFController::class, 'exportSingle'])->name('single');
                    Route::get('/{id}/view', [ContractPDFController::class, 'view'])->name('view');
                    Route::post('/export-multiple', [ContractPDFController::class, 'exportMultiple'])->name('multiple');
                });

            // ==================== CONTRACT CLIENTS (STANDALONE) ====================
            Route::prefix('contract-clients')
                ->name('contract-clients.')
                ->middleware('role:super-admin|admin|manager,backoffice')
                ->group(function () {
                    Route::get('/', [ContractClientController::class, 'index'])->name('index');
                    Route::get('/create', [ContractClientController::class, 'create'])->name('create');
                    Route::post('/', [ContractClientController::class, 'store'])->name('store');
                    Route::get('/{contractClient}', [ContractClientController::class, 'show'])->name('show');
                    Route::get('/{contractClient}/edit', [ContractClientController::class, 'edit'])->name('edit');
                    Route::put('/{contractClient}', [ContractClientController::class, 'update'])->name('update');
                    Route::delete('/{contractClient}', [ContractClientController::class, 'destroy'])->name('destroy');
                });

            // ==================== BOOKINGS ====================
            Route::prefix('bookings')
                ->name('bookings.')
                ->middleware('role:super-admin|admin|manager,backoffice')
                ->group(function () {
                    Route::get('/', [BookingController::class, 'index'])->name('index');
                    Route::get('/create', [BookingController::class, 'create'])->name('create');
                    Route::post('/', [BookingController::class, 'store'])->name('store');
                    Route::get('/{booking}', [BookingController::class, 'show'])->name('show');
                    Route::get('/{booking}/edit', [BookingController::class, 'edit'])->name('edit');
                    Route::put('/{booking}', [BookingController::class, 'update'])->name('update');
                    Route::delete('/{booking}', [BookingController::class, 'destroy'])->name('destroy');
                    Route::post('/{booking}/status', [BookingController::class, 'updateStatus'])->name('status');
                    Route::post('/{booking}/convert-to-contract', [BookingController::class, 'convertToContract'])->name('convert-to-contract');
                    Route::get('/calendar/view', [BookingController::class, 'calendar'])->name('calendar');
                });

            // ==================== FINANCE ====================
            Route::prefix('finance')
                ->name('finance.')
                ->middleware('role:super-admin|admin|manager,backoffice')
                ->group(function () {

                    // Financial Accounts
                    Route::prefix('accounts')
                        ->name('accounts.')
                        ->group(function () {
                            Route::get('/', [FinancialAccountController::class, 'index'])->name('index');
                            Route::get('/create', [FinancialAccountController::class, 'create'])->name('create');
                            Route::post('/', [FinancialAccountController::class, 'store'])->name('store');
                            Route::get('/{financialAccount}', [FinancialAccountController::class, 'show'])->name('show');
                            Route::get('/{financialAccount}/edit', [FinancialAccountController::class, 'edit'])->name('edit');
                            Route::put('/{financialAccount}', [FinancialAccountController::class, 'update'])->name('update');
                            Route::delete('/{financialAccount}', [FinancialAccountController::class, 'destroy'])->name('destroy');
                        });

                    // Transaction Categories
                    Route::prefix('categories')
                        ->name('categories.')
                        ->group(function () {
                            Route::get('/', [TransactionCategoryController::class, 'index'])->name('index');
                            Route::get('/create', [TransactionCategoryController::class, 'create'])->name('create');
                            Route::post('/', [TransactionCategoryController::class, 'store'])->name('store');
                            Route::get('/{transactionCategory}', [TransactionCategoryController::class, 'show'])->name('show');
                            Route::get('/{transactionCategory}/edit', [TransactionCategoryController::class, 'edit'])->name('edit');
                            Route::put('/{transactionCategory}', [TransactionCategoryController::class, 'update'])->name('update');
                            Route::delete('/{transactionCategory}', [TransactionCategoryController::class, 'destroy'])->name('destroy');
                        });

                    // Financial Transactions
                    Route::prefix('transactions')
                        ->name('transactions.')
                        ->group(function () {
                            Route::get('/', [FinancialTransactionController::class, 'index'])->name('index');
                            Route::get('/create', [FinancialTransactionController::class, 'create'])->name('create');
                            Route::post('/', [FinancialTransactionController::class, 'store'])->name('store');
                            Route::get('/{financialTransaction}', [FinancialTransactionController::class, 'show'])->name('show');
                            Route::get('/{financialTransaction}/edit', [FinancialTransactionController::class, 'edit'])->name('edit');
                            Route::put('/{financialTransaction}', [FinancialTransactionController::class, 'update'])->name('update');
                            Route::delete('/{financialTransaction}', [FinancialTransactionController::class, 'destroy'])->name('destroy');
                            Route::get('/summary/data', [FinancialTransactionController::class, 'summary'])->name('summary');
                        });
                });

            // ==================== INVOICES ====================
            Route::prefix('invoices')
                ->name('invoices.')
                ->middleware('role:super-admin|admin|manager,backoffice')
                ->group(function () {
                    Route::get('/', [InvoiceController::class, 'index'])->name('index');
                    Route::get('/create', [InvoiceController::class, 'create'])->name('create');
                    Route::post('/', [InvoiceController::class, 'store'])->name('store');
                    Route::get('/{invoice}', [InvoiceController::class, 'show'])->name('show');
                    Route::get('/{invoice}/edit', [InvoiceController::class, 'edit'])->name('edit');
                    Route::put('/{invoice}', [InvoiceController::class, 'update'])->name('update');
                    Route::delete('/{invoice}', [InvoiceController::class, 'destroy'])->name('destroy');
                    Route::post('/{invoice}/status', [InvoiceController::class, 'updateStatus'])->name('status');
                });

            // ==================== INVOICE PDF ====================
            Route::prefix('invoices/pdf')
                ->name('invoices.pdf.')
                ->middleware('role:super-admin|admin|manager,backoffice')
                ->group(function () {
                    Route::get('/{id}', [InvoicePDFController::class, 'exportSingle'])->name('single');
                    Route::post('/export-multiple', [InvoicePDFController::class, 'exportMultiple'])->name('multiple');
                });

            // ==================== INVOICE ITEMS (Standalone) ====================
            Route::prefix('invoice-items')
                ->name('invoice-items.')
                ->middleware('role:super-admin|admin|manager,backoffice')
                ->group(function () {
                    Route::get('/', [InvoiceItemController::class, 'index'])->name('index');
                    Route::get('/create', [InvoiceItemController::class, 'create'])->name('create');
                    Route::post('/', [InvoiceItemController::class, 'store'])->name('store');
                    Route::get('/{invoiceItem}', [InvoiceItemController::class, 'show'])->name('show');
                    Route::get('/{invoiceItem}/edit', [InvoiceItemController::class, 'edit'])->name('edit');
                    Route::put('/{invoiceItem}', [InvoiceItemController::class, 'update'])->name('update');
                    Route::delete('/{invoiceItem}', [InvoiceItemController::class, 'destroy'])->name('destroy');
                });

            // ==================== PAYMENTS ====================
            Route::prefix('payments')
                ->name('payments.')
                ->middleware('role:super-admin|admin|manager,backoffice')
                ->group(function () {
                    Route::get('/', [PaymentController::class, 'index'])->name('index');
                    Route::get('/create', [PaymentController::class, 'create'])->name('create');
                    Route::post('/', [PaymentController::class, 'store'])->name('store');
                    Route::get('/{payment}', [PaymentController::class, 'show'])->name('show');
                    Route::get('/{payment}/edit', [PaymentController::class, 'edit'])->name('edit');
                    Route::put('/{payment}', [PaymentController::class, 'update'])->name('update');
                    Route::delete('/{payment}', [PaymentController::class, 'destroy'])->name('destroy');
                    Route::post('/{payment}/status', [PaymentController::class, 'updateStatus'])->name('status');
                });

            // ==================== API ROUTES ====================
            Route::get('/api/control-items/by-control', [ControlItemController::class, 'getByControl'])
                ->name('api.control-items.by-control');

            // ==================== GLOBAL VEHICLE DOCUMENTS ====================
            Route::prefix('vehicle-documents')
                ->name('vehicle-documents.')
                ->middleware('role:super-admin|admin|manager,backoffice')
                ->group(function () {
                    // Vignettes
                    Route::get('/vignettes', [VignetteController::class, 'globalIndex'])->name('vignettes.index');
                    Route::get('/vignettes/create', [VignetteController::class, 'create'])->name('vignettes.create');
                    Route::post('/vignettes', [VignetteController::class, 'store'])->name('vignettes.store');
                    
                    // Insurances
                    Route::get('/insurances', [InsuranceController::class, 'globalIndex'])->name('insurances.index');
                    Route::get('/insurances/create', [InsuranceController::class, 'create'])->name('insurances.create');
                    Route::post('/insurances', [InsuranceController::class, 'store'])->name('insurances.store');
                    
                    // Oil Changes
                    Route::get('/oil-changes', [OilChangeController::class, 'globalIndex'])->name('oil-changes.index');
                    Route::get('/oil-changes/create', [OilChangeController::class, 'create'])->name('oil-changes.create');
                    Route::post('/oil-changes', [OilChangeController::class, 'store'])->name('oil-changes.store');
                    
                    // Technical Checks
                    Route::get('/technical-checks', [TechnicalCheckController::class, 'globalIndex'])->name('technical-checks.index');
                    Route::get('/technical-checks/create', [TechnicalCheckController::class, 'create'])->name('technical-checks.create');
                    Route::post('/technical-checks', [TechnicalCheckController::class, 'store'])->name('technical-checks.store');
                });

            // ==================== NOTIFICATIONS ====================
            Route::prefix('notifications')
                ->name('notifications.')
                ->middleware('role:super-admin|admin|manager,backoffice')
                ->group(function () {
                    // Static routes (no parameters) - MUST come first
                    Route::get('/', [NotificationController::class, 'index'])->name('index');
                    Route::get('/archived', [NotificationController::class, 'archived'])->name('archived');
                    Route::get('/recent', [NotificationController::class, 'getRecent'])->name('recent');
                    Route::get('/unread-count', [NotificationController::class, 'getUnreadCount'])->name('unread-count');
                    Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
                    Route::post('/archive-all-read', [NotificationController::class, 'archiveAllRead'])->name('archive-all-read');
                    Route::post('/clear-all', [NotificationController::class, 'clearAll'])->name('clear-all');
                    Route::post('/delete-all-archived', [NotificationController::class, 'deleteAllArchived'])->name('delete-all-archived');
                    Route::delete('/delete-all', [NotificationController::class, 'deleteAll'])->name('delete-all');

                    // Parameterized routes - must come after static routes
                    Route::post('/{notification}/read', [NotificationController::class, 'markAsRead'])->name('mark-read');
                    Route::post('/{notification}/archive', [NotificationController::class, 'archive'])->name('archive');
                    Route::delete('/{notification}', [NotificationController::class, 'destroy'])->name('destroy');
                });

            // ==================== PROFILE SETTINGS ====================
            Route::prefix('admin')
                ->name('profile.')
                ->middleware('role:super-admin|admin|manager,backoffice')
                ->group(function () {
                    // Profile Settings
                    Route::get('/profile-setting', [ProfileController::class, 'edit'])->name('setting');
                    Route::post('/profile-setting', [ProfileController::class, 'update'])->name('update');
                    
                    // Delete profile photo
                    Route::delete('/profile/delete-photo', [ProfileController::class, 'deletePhoto'])
                        ->name('delete-photo');
                    
                    // Security Settings
                    Route::get('/security-setting', [ProfileController::class, 'showChangePassword'])->name('security');
                    Route::put('/security-setting', [ProfileController::class, 'updatePassword'])->name('security.update');
                    
                    // Edit Profile (alias)
                    Route::get('/edit-profile', [ProfileController::class, 'edit'])->name('edit');
                    
                    // Change Password
                    Route::get('/change-password', [ProfileController::class, 'showChangePassword'])->name('change-password');
                    Route::put('/change-password', [ProfileController::class, 'updatePassword'])->name('update-password');
                });

                // ✅ MY SUBSCRIPTION ROUTE
Route::get('my-subscription', [App\Http\Controllers\Backoffice\AgencySubscriptionController::class, 'mySubscription'])
    ->name('my-subscription')
    ->middleware('role:super-admin|admin|manager,backoffice');


    Route::get('/dashboard', [App\Http\Controllers\Backoffice\DashboardController::class, 'index'])
    ->name('dashboard')
    ->middleware('role:super-admin|admin|manager,backoffice');

    Route::post('/dashboard/filter', [App\Http\Controllers\Backoffice\DashboardController::class, 'getFilteredStats'])
    ->name('dashboard.filter')
    ->middleware('role:super-admin|admin|manager,backoffice');

    Route::prefix('invoices/pdf')->name('invoices.pdf.')->group(function () {
    Route::get('/{id}/view', [InvoicePDFController::class, 'view'])->name('view');
});

Route::prefix('contracts/pdf')->name('contracts.pdf.')->group(function () {
    Route::get('/{id}', [ContractPDFController::class, 'exportSingle'])->name('single');
    Route::get('/{id}/view', [ContractPDFController::class, 'view'])->name('view');
    Route::post('/export-multiple', [ContractPDFController::class, 'exportMultiple'])->name('multiple');
});

// Trash routes
Route::prefix('trash')->name('trash.')->middleware('auth:backoffice')->group(function () {
    Route::get('/', [App\Http\Controllers\Backoffice\TrashController::class, 'index'])->name('index');
    Route::patch('/{module}/restore/{id}', [App\Http\Controllers\Backoffice\TrashController::class, 'restore'])->name('restore');
    Route::patch('/{module}/restore-all', [App\Http\Controllers\Backoffice\TrashController::class, 'restoreAll'])->name('restore-all');
    Route::delete('/{module}/force-delete/{id}', [App\Http\Controllers\Backoffice\TrashController::class, 'forceDelete'])->name('force-delete');
    Route::delete('/{module}/force-delete-all', [App\Http\Controllers\Backoffice\TrashController::class, 'forceDeleteAll'])->name('force-delete-all');
    Route::delete('/empty-all', [App\Http\Controllers\Backoffice\TrashController::class, 'emptyAll'])->name('empty-all');
});


Route::prefix('vehicle-credits')
    ->name('vehicle-credits.')
    ->middleware('role:super-admin|admin|manager,backoffice')
    ->group(function () {
        Route::get('/', [VehicleCreditController::class, 'index'])->name('index');
        Route::get('/dashboard', [VehicleCreditController::class, 'dashboard'])->name('dashboard');
        Route::get('/create', [VehicleCreditController::class, 'create'])->name('create');
        Route::post('/', [VehicleCreditController::class, 'store'])->name('store');
        Route::get('/{vehicleCredit}', [VehicleCreditController::class, 'show'])->name('show');
        Route::get('/{vehicleCredit}/edit', [VehicleCreditController::class, 'edit'])->name('edit');
        Route::put('/{vehicleCredit}', [VehicleCreditController::class, 'update'])->name('update');
        Route::delete('/{vehicleCredit}', [VehicleCreditController::class, 'destroy'])->name('destroy');
        Route::post('/{vehicleCredit}/record-payment', [VehicleCreditController::class, 'recordPayment'])->name('record-payment');
        Route::get('/{vehicleCredit}/payment-schedule', [VehicleCreditController::class, 'getPaymentSchedule'])->name('payment-schedule');
    });
                
        }); // END AUTH GROUP
    }); // END BACKOFFICE PREFIX
<?php

use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\Api\PerpossagarBookLanguageController;
use App\Http\Controllers\Api\RegisterController;
use App\Http\Controllers\Api\SejajanCartController;
use App\Http\Controllers\Api\SejajanOrderController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// LMS Authentication Routes
Route::prefix('lms')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
});

// LMS Super Admin Master Data Routes
Route::middleware(['auth:sanctum', 'role:super_admin'])->prefix('lms')->group(function () {
    Route::apiResource('accounts', \App\Http\Controllers\Api\AccountController::class);
    Route::apiResource('teachers', \App\Http\Controllers\Api\TeacherController::class);
    Route::apiResource('students', \App\Http\Controllers\Api\StudentController::class);
    Route::apiResource('classrooms', \App\Http\Controllers\Api\ClassroomController::class);
    Route::apiResource('subjects', \App\Http\Controllers\Api\SubjectController::class);
    Route::apiResource('student-positions', \App\Http\Controllers\Api\StudentPositionController::class);
    Route::apiResource('schedules', \App\Http\Controllers\Api\ScheduleController::class);

    Route::post('classrooms/assign-student', [\App\Http\Controllers\Api\ClassroomAssignmentController::class, 'assignStudent']);
});

// LMS Teacher Routes
Route::middleware(['auth:sanctum', 'role:teacher'])->prefix('lms/teacher')->group(function () {
    Route::get('schedules', [\App\Http\Controllers\Api\Teacher\TeacherScheduleController::class, 'index']);
    Route::apiResource('materials', \App\Http\Controllers\Api\Teacher\TeacherMaterialController::class);
    Route::post('attendances', [\App\Http\Controllers\Api\Teacher\TeacherAttendanceController::class, 'store']);
});

// LMS Student Routes
Route::middleware(['auth:sanctum', 'role:student'])->prefix('lms/student')->group(function () {
    Route::get('schedules', [\App\Http\Controllers\Api\Student\StudentScheduleController::class, 'index']);
    Route::get('materials', [\App\Http\Controllers\Api\Student\StudentMaterialController::class, 'index']);
    Route::get('materials/classrooms/{classroomId}', [\App\Http\Controllers\Api\Student\StudentMaterialController::class, 'byClassroom']);
    Route::get('materials/{material}', [\App\Http\Controllers\Api\Student\StudentMaterialController::class, 'show']);
    Route::get('materials/{material}/download', [\App\Http\Controllers\Api\Student\StudentMaterialController::class, 'download']);
    Route::get('attendances', [\App\Http\Controllers\Api\Student\StudentAttendanceController::class, 'index']);
});

Route::middleware(['api'])->group(function () {
    // Public auth
    Route::post('/login', [\App\Http\Controllers\Api\ParticipantAuthController::class, 'login']);

    Route::options('/check-nisn', function () {
        return response('', 200);
    });
    Route::post('/check-nisn', [RegisterController::class, 'checkNisn']);
    Route::post('/register', [RegisterController::class, 'register']);
    Route::get('/students', [StudentController::class, 'index']);
    Route::get('/students/{student}', [StudentController::class, 'show']);
    Route::patch('/register/{participant}/school', [RegisterController::class, 'updateSchool']);

    // Sejajan public endpoints
    Route::get('/sejajans', [\App\Http\Controllers\Api\SejajanController::class, 'index']);

    // Perpossagar public endpoints
    Route::get('/perpossagar/categories', [\App\Http\Controllers\Api\PerpossagarCategoryController::class, 'index']);
    Route::get('/perpossagar/categories/{uuid}', [\App\Http\Controllers\Api\PerpossagarCategoryController::class, 'show']);
    Route::get('/perpossagar/book-langs', [PerpossagarBookLanguageController::class, 'index']);
    Route::get('/perpossagar/books', [\App\Http\Controllers\Api\PerpossagarBookController::class, 'index']);
    Route::get('/perpossagar/books/hero', [\App\Http\Controllers\Api\PerpossagarBookController::class, 'hero']);
    Route::get('/perpossagar/books/popular', [\App\Http\Controllers\Api\PerpossagarBookController::class, 'popular']);
    Route::get('/perpossagar/books/community', [\App\Http\Controllers\Api\PerpossagarBookController::class, 'community']);
    Route::get('/perpossagar/books/{uuid}', [\App\Http\Controllers\Api\PerpossagarBookController::class, 'show'])
        ->whereUuid('uuid');
    Route::post('/perpossagar/books/{uuid}/read', [\App\Http\Controllers\Api\PerpossagarBookController::class, 'incrementReadCount'])
        ->whereUuid('uuid');

    // Selaju articles
    Route::get('/articles', [ArticleController::class, 'index']);
    Route::get('/articles/{slug}', [ArticleController::class, 'show']);

    // Donation public endpoints
    Route::get('/donations', [\App\Http\Controllers\Api\DonationController::class, 'index']);
    Route::middleware('auth:sanctum')->get('/donations/history', [\App\Http\Controllers\Api\DonationController::class, 'history']);
    Route::get('/donations/banks', [\App\Http\Controllers\Api\DonationController::class, 'getAvailableBanks']);
    Route::get('/bank-accounts', [\App\Http\Controllers\Api\BankAccountController::class, 'index']);
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/donations', [\App\Http\Controllers\Api\DonationController::class, 'store']);
        Route::post('/donations/manual-transfer', [\App\Http\Controllers\Api\DonationController::class, 'storeManualTransfer']);
    });
    Route::get('/donations/{id}', [\App\Http\Controllers\Api\DonationController::class, 'show'])->whereUuid('id');
    Route::post('/payment/callback', [\App\Http\Controllers\Api\DonationController::class, 'paymentCallback']);
    Route::post('/payment/token', [\App\Http\Controllers\Api\DonationController::class, 'generateToken']);

    // Protected routes (require auth)
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [\App\Http\Controllers\Api\ParticipantAuthController::class, 'me']);
        Route::post('/logout', [\App\Http\Controllers\Api\ParticipantAuthController::class, 'logout']);
        Route::patch('/me', [\App\Http\Controllers\Api\ParticipantAuthController::class, 'updateProfile']);
        Route::get('/username/check', [\App\Http\Controllers\Api\ParticipantAuthController::class, 'checkUsername']);
        Route::post('/email/verification-notification', [\App\Http\Controllers\Api\ParticipantAuthController::class, 'sendVerificationEmail']);
        Route::post('/email/verify', [\App\Http\Controllers\Api\ParticipantAuthController::class, 'verifyEmail']);

        Route::get('/perpossagar/books/mine', [\App\Http\Controllers\Api\PerpossagarBookController::class, 'mine']);
        Route::post('/perpossagar/books', [\App\Http\Controllers\Api\PerpossagarBookController::class, 'store']);

        // Sejajan protected endpoints (create/update/delete owned stores)
        Route::get('/sejajans/my-stores', [\App\Http\Controllers\Api\SejajanController::class, 'myStores']);
        Route::post('/sejajans', [\App\Http\Controllers\Api\SejajanController::class, 'store']);
        Route::put('/sejajans/{sejajan}', [\App\Http\Controllers\Api\SejajanController::class, 'update']);

        // Sejajan category endpoints (owner only)
        Route::get('/sejajans/{sejajanSlug}/categories', [\App\Http\Controllers\Api\SejajanCategoryController::class, 'index']);
        Route::post('/sejajans/{sejajanSlug}/categories', [\App\Http\Controllers\Api\SejajanCategoryController::class, 'store']);
        Route::get('/sejajans/{sejajanSlug}/categories/{categoryId}', [\App\Http\Controllers\Api\SejajanCategoryController::class, 'show']);
        Route::put('/sejajans/{sejajanSlug}/categories/{categoryId}', [\App\Http\Controllers\Api\SejajanCategoryController::class, 'update']);
        Route::delete('/sejajans/{sejajanSlug}/categories/{categoryId}', [\App\Http\Controllers\Api\SejajanCategoryController::class, 'destroy']);

        // Sejajan product endpoints (owner only) - support slug parameter
        Route::post('/sejajans/{sejajanSlug}/products', function (\Illuminate\Http\Request $request, $sejajanSlug) {
            $sejajan = \App\Models\Sejajan::where('slug', $sejajanSlug)->firstOrFail();
            $request->merge(['sejajan' => $sejajan]);

            return app(\App\Http\Controllers\Api\SejajanProductController::class)->store($request, $sejajan);
        });

        Route::get('/sejajans/{sejajanSlug}/products/{productId}', function ($sejajanSlug, $productId) {
            $sejajan = \App\Models\Sejajan::where('slug', $sejajanSlug)->firstOrFail();
            $product = \App\Models\SejajanProduct::where('sejajan_id', $sejajan->id)->findOrFail($productId);

            // Normalize photo to filename only
            if ($product->photo) {
                $product->photo = preg_replace('/.*[\/\\\\]/', '', $product->photo);
            }

            return response()->json([
                'data' => $product,
                'path' => \App\Helpers\Helper::getPhotoBasePath(),
            ]);
        });

        Route::put('/sejajans/{sejajanSlug}/products/{productId}', function (\Illuminate\Http\Request $request, $sejajanSlug, $productId) {
            $sejajan = \App\Models\Sejajan::where('slug', $sejajanSlug)->firstOrFail();
            $product = \App\Models\SejajanProduct::where('sejajan_id', $sejajan->id)->findOrFail($productId);

            return app(\App\Http\Controllers\Api\SejajanProductController::class)->update($request, $sejajan, $product);
        });

        Route::delete('/sejajans/{sejajanSlug}/products/{productId}', function ($sejajanSlug, $productId) {
            $sejajan = \App\Models\Sejajan::where('slug', $sejajanSlug)->firstOrFail();
            $product = \App\Models\SejajanProduct::where('sejajan_id', $sejajan->id)->findOrFail($productId);
            $product->delete();

            return response()->json(['message' => 'Produk berhasil dihapus'], 200);
        });

        Route::post('/sejajans/orders', [SejajanOrderController::class, 'store']);
        Route::get('/sejajans/my-orders', [SejajanOrderController::class, 'myOrders']);
        Route::get('/sejajans/{sejajanSlug}/orders', [SejajanOrderController::class, 'index']);
        Route::put('/sejajans/{sejajanSlug}/orders/{orderId}/status', [SejajanOrderController::class, 'updateStatus']);

        Route::get('/sejajans/cart', [SejajanCartController::class, 'index']);
        Route::post('/sejajans/cart', [SejajanCartController::class, 'store']);
        Route::patch('/sejajans/cart/{itemId}', [SejajanCartController::class, 'update']);
        Route::delete('/sejajans/cart/{itemId}', [SejajanCartController::class, 'destroy']);
        Route::delete('/sejajans/cart', [SejajanCartController::class, 'destroyAll']);

        Route::delete('/sejajans/{sejajan}', [\App\Http\Controllers\Api\SejajanController::class, 'destroy']);
        // Eplin Violation Types endpoints
        Route::get('/eplin/violation-types', [\App\Http\Controllers\Api\EplinOfficerController::class, 'getViolationTypes']);

        // Eplin Officers endpoints
        Route::prefix('/eplin/officers')->group(function () {
            Route::get('/', [\App\Http\Controllers\Api\EplinOfficerController::class, 'index']);
            Route::post('/', [\App\Http\Controllers\Api\EplinOfficerController::class, 'store']);
            Route::get('/{officer}', [\App\Http\Controllers\Api\EplinOfficerController::class, 'show']);
            Route::put('/{officer}', [\App\Http\Controllers\Api\EplinOfficerController::class, 'update']);
            Route::delete('/{officer}', [\App\Http\Controllers\Api\EplinOfficerController::class, 'destroy']);
        });

        // Eplin Violations CREATE/UPDATE/DELETE (auth required)
        Route::post('/eplin/violations', [\App\Http\Controllers\Api\EplinViolationController::class, 'store']);
        Route::put('/eplin/violations/{violation}', [\App\Http\Controllers\Api\EplinViolationController::class, 'update']);
        Route::delete('/eplin/violations/{violation}', [\App\Http\Controllers\Api\EplinViolationController::class, 'destroy']);

        // Eplin Attendances endpoints
        Route::prefix('/eplin/attendances')->group(function () {
            Route::get('/', [\App\Http\Controllers\Api\EplinAttendanceController::class, 'index']);
            Route::post('/', [\App\Http\Controllers\Api\EplinAttendanceController::class, 'store']);
            Route::get('/{attendance}', [\App\Http\Controllers\Api\EplinAttendanceController::class, 'show']);
            Route::put('/{attendance}', [\App\Http\Controllers\Api\EplinAttendanceController::class, 'update']);
            Route::delete('/{attendance}', [\App\Http\Controllers\Api\EplinAttendanceController::class, 'destroy']);
        });
    });

    Route::get('/sejajans/{sejajan}', [\App\Http\Controllers\Api\SejajanController::class, 'show']);

    // Webex Ekskul endpoints
    Route::prefix('/webex/ekskuls')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\WebexEkskul\EkskulController::class, 'index']);
        Route::get('/{ekskul}', [\App\Http\Controllers\Api\WebexEkskul\EkskulController::class, 'show']);

        // Participants — read
        Route::get('/{ekskul}/participants', [\App\Http\Controllers\Api\WebexEkskul\ParticipantController::class, 'index']);

        // Pengurus — read
        Route::get('/{ekskul}/pengurus', [\App\Http\Controllers\Api\WebexEkskul\PengurusController::class, 'index']);

        // Reports — read
        Route::get('/{ekskul}/reports', [\App\Http\Controllers\Api\WebexEkskul\ReportController::class, 'index']);
        Route::get('/{ekskul}/reports/{report}', [\App\Http\Controllers\Api\WebexEkskul\ReportController::class, 'show']);

        // Attendances — read
        Route::get('/{ekskul}/attendances', [\App\Http\Controllers\Api\WebexEkskul\AttendanceController::class, 'index']);
    });

    // Webex Ekskul mutating endpoints (auth required)
    Route::middleware('auth:sanctum')->prefix('/webex/ekskuls')->group(function () {
        Route::post('/', [\App\Http\Controllers\Api\WebexEkskul\EkskulController::class, 'store']);
        Route::put('/{ekskul}', [\App\Http\Controllers\Api\WebexEkskul\EkskulController::class, 'update']);
        Route::delete('/{ekskul}', [\App\Http\Controllers\Api\WebexEkskul\EkskulController::class, 'destroy']);

        // Participants — mutate
        Route::post('/{ekskul}/participants', [\App\Http\Controllers\Api\WebexEkskul\ParticipantController::class, 'store']);
        Route::put('/{ekskul}/participants/{participant}', [\App\Http\Controllers\Api\WebexEkskul\ParticipantController::class, 'update']);
        Route::delete('/{ekskul}/participants/{participant}', [\App\Http\Controllers\Api\WebexEkskul\ParticipantController::class, 'destroy']);

        // Pengurus — mutate
        Route::post('/{ekskul}/pengurus', [\App\Http\Controllers\Api\WebexEkskul\PengurusController::class, 'store']);
        Route::put('/{ekskul}/pengurus/{pengurus}', [\App\Http\Controllers\Api\WebexEkskul\PengurusController::class, 'update']);
        Route::delete('/{ekskul}/pengurus/{pengurus}', [\App\Http\Controllers\Api\WebexEkskul\PengurusController::class, 'destroy']);

        // Reports — mutate
        Route::post('/{ekskul}/reports', [\App\Http\Controllers\Api\WebexEkskul\ReportController::class, 'store']);
        Route::put('/{ekskul}/reports/{report}', [\App\Http\Controllers\Api\WebexEkskul\ReportController::class, 'update']);
        Route::delete('/{ekskul}/reports/{report}', [\App\Http\Controllers\Api\WebexEkskul\ReportController::class, 'destroy']);

        // Attendances — mutate
        Route::post('/{ekskul}/attendances', [\App\Http\Controllers\Api\WebexEkskul\AttendanceController::class, 'store']);
        Route::put('/{ekskul}/attendances/{attendance}', [\App\Http\Controllers\Api\WebexEkskul\AttendanceController::class, 'update']);
        Route::delete('/{ekskul}/attendances/{attendance}', [\App\Http\Controllers\Api\WebexEkskul\AttendanceController::class, 'destroy']);
    });

    // Eplin public read endpoints
    Route::get('/eplin/violation-types', [\App\Http\Controllers\Api\EplinOfficerController::class, 'getViolationTypes']);
    Route::get('/eplin/officers', [\App\Http\Controllers\Api\EplinOfficerController::class, 'index']);
    Route::get('/eplin/violations', [\App\Http\Controllers\Api\EplinViolationController::class, 'index']);
    Route::get('/eplin/violations/export', [\App\Http\Controllers\Api\EplinViolationController::class, 'export']);
    Route::get('/eplin/violations/{violation}', [\App\Http\Controllers\Api\EplinViolationController::class, 'show']);
    Route::get('/eplin/attendances', [\App\Http\Controllers\Api\EplinAttendanceController::class, 'index']);
    Route::get('/eplin/attendances/{attendance}', [\App\Http\Controllers\Api\EplinAttendanceController::class, 'show']);

    // Book endpoints — read public, mutate requires auth
    Route::get('/books', [BookController::class, 'index']);
    Route::get('/books/{uuid}', [BookController::class, 'show']);
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/books', [BookController::class, 'store']);
        Route::put('/books/{uuid}', [BookController::class, 'update']);
        Route::delete('/books/{uuid}', [BookController::class, 'destroy']);
    });
});

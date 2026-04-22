<?php

use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\Api\PerpossagarBookLanguageController;
use App\Http\Controllers\Api\RegisterController;
use App\Http\Controllers\Api\SchoolController;
use App\Http\Controllers\Api\SejajanCartController;
use App\Http\Controllers\Api\SejajanOrderController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
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
    // Public routes
    // API login for SPA clients
    Route::post('/login', function (Request $request) {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = \App\Models\Participant::where('email', $validated['email'])->first();
        if (! $user || ! \Illuminate\Support\Facades\Hash::check($validated['password'], $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // Optionally check is_active
        if ($user->is_active === false) {
            return response()->json(['message' => 'Account is disabled'], 403);
        }

        // Enforce single-session: remove existing tokens for this user then create a new one
        if (method_exists($user, 'tokens')) {
            $user->tokens()->delete();
        }

        // Create token
        $token = $user->createToken('default')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => [
                'id' => $user->getKey(),
                'name' => $user->name,
                'email' => $user->email,
                'username' => $user->username,
                'email_verified_at' => $user->email_verified_at,
            ],
        ]);
    });

    Route::options('/check-nisn', function () {
        return response('', 200);
    });
    Route::post('/check-nisn', [RegisterController::class, 'checkNisn']);
    Route::post('/register', [RegisterController::class, 'register']);
    Route::get('/students', [StudentController::class, 'index']);
    Route::get('/students/{student}', [StudentController::class, 'show']);
    // Route::get('/schools', [SchoolController::class, 'index']); // Not needed - school_id comes from NISN verification
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
    Route::post('/donations', [\App\Http\Controllers\Api\DonationController::class, 'store']);
    Route::get('/donations/{id}', [\App\Http\Controllers\Api\DonationController::class, 'show'])->whereUuid('id');
    Route::post('/donations/manual-transfer', [\App\Http\Controllers\Api\DonationController::class, 'storeManualTransfer']);
    Route::post('/payment/callback', [\App\Http\Controllers\Api\DonationController::class, 'paymentCallback']);
    Route::post('/payment/token', [\App\Http\Controllers\Api\DonationController::class, 'generateToken']);

    // Protected routes (require auth) - using Sanctum personal access tokens
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', function (Request $request) {
            // Ensure related school and student are loaded so frontend can display school.name and name
            $user = $request->user();
            if (! $user) {
                return response()->json(null, 200);
            }

            $user->load(['school', 'student']);

            // Normalize response shape for frontend expectations. Some older frontend
            // code expects `name` on the user object — derive it from student.nama
            // or fallback to username/nomor_participant.
            $payload = $user->toArray();
            $payload['name'] = $user->name ?? ($user->student?->nama ?? $user->username ?? $user->nomor_participant ?? null);

            return response()->json($payload);
        });
        // Revoke current access token (logout)
        Route::post('/logout', function (Request $request) {
            $user = $request->user();
            if ($user && $request->user()->currentAccessToken()) {
                $request->user()->currentAccessToken()->delete();
            }

            return response()->json(['message' => 'Logged out'], 200);
        });
        // Update authenticated participant profile
        Route::patch('/me', function (Request $request) {
            $user = $request->user();
            if (! $user) {
                return response()->json(['message' => 'Unauthenticated'], 401);
            }

            $validated = $request->validate([
                'username' => ['required', 'string', 'max:50', 'unique:accounts,username,'.$user->uuid.',uuid'],
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:accounts,email,'.$user->uuid.',uuid'],
                'no_telp' => ['nullable', 'string', 'max:20'],
                'birth_date' => ['nullable', 'date'],
            ]);

            $user->update($validated);
            $user->load('school');

            return response()->json($user->fresh());
        });

        // Check username availability for the authenticated user (exclude their own username)
        Route::get('/username/check', function (Request $request) {
            $user = $request->user();
            if (! $user) {
                return response()->json(['message' => 'Unauthenticated'], 401);
            }

            $username = $request->query('username');
            if (! $username) {
                return response()->json(['available' => false, 'message' => 'username is required'], 400);
            }

            $exists = \App\Models\Participant::where('username', $username)
                ->where('id', '!=', $user->id)
                ->exists();

            return response()->json(['available' => ! $exists]);
        });

        // Send email verification link for authenticated user (for token-based clients)
        Route::post('/email/verification-notification', function (Request $request) {
            $user = $request->user();
            if (! $user) {
                return response()->json(['message' => 'Unauthenticated'], 401);
            }

            if ($user->hasVerifiedEmail()) {
                return response()->json(['message' => 'Already verified'], 200);
            }

            $user->sendEmailVerificationNotification();

            return response()->json(['status' => 'verification-link-sent']);
        });

        // Verify email via API using the signed backend URL wrapped by the frontend.
        // Expects { verify_url: 'http://.../verify-email/{id}/{hash}?expires=...&signature=...' }
        Route::post('/email/verify', function (Request $request) {
            $user = $request->user();
            if (! $user) {
                return response()->json(['message' => 'Unauthenticated'], 401);
            }

            $verifyUrl = $request->input('verify_url');
            if (! $verifyUrl) {
                return response()->json(['message' => 'verify_url is required'], 400);
            }

            try {
                // Create a request object from the signed url so URL::hasValidSignature can validate it
                $fakeRequest = Request::create($verifyUrl);
            } catch (\Throwable $e) {
                return response()->json(['message' => 'Invalid verify_url format'], 400);
            }

            // Validate signature & expiration
            if (! \Illuminate\Support\Facades\URL::hasValidSignature($fakeRequest)) {
                return response()->json(['message' => 'Invalid or expired verification link'], 400);
            }

            // Extract {id} and {hash} from the path segments
            $path = parse_url($verifyUrl, PHP_URL_PATH);
            $segments = explode('/', trim($path, '/'));
            $hash = array_pop($segments);
            $id = array_pop($segments);

            if ((string) $user->getKey() !== (string) $id) {
                return response()->json(['message' => 'This verification link does not belong to the authenticated user'], 403);
            }

            if ($hash !== sha1($user->getEmailForVerification())) {
                return response()->json(['message' => 'Invalid verification hash'], 400);
            }

            if ($user->hasVerifiedEmail()) {
                return response()->json(['message' => 'Already verified', 'status' => 'already_verified'], 200);
            }

            $user->markEmailAsVerified();

            // Generate token for auto-login
            $token = $user->createToken('email-verification')->plainTextToken;

            return response()->json([
                'message' => 'Email verified',
                'status' => 'verified',
                'token' => $token,
            ], 200);
        });

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
        Route::post('/', [\App\Http\Controllers\Api\WebexEkskul\EkskulController::class, 'store']);
        Route::get('/{ekskul}', [\App\Http\Controllers\Api\WebexEkskul\EkskulController::class, 'show']);
        Route::put('/{ekskul}', [\App\Http\Controllers\Api\WebexEkskul\EkskulController::class, 'update']);
        Route::delete('/{ekskul}', [\App\Http\Controllers\Api\WebexEkskul\EkskulController::class, 'destroy']);

        // Participants
        Route::prefix('/{ekskul}/participants')->group(function () {
            Route::get('/', [\App\Http\Controllers\Api\WebexEkskul\ParticipantController::class, 'index']);
            Route::post('/', [\App\Http\Controllers\Api\WebexEkskul\ParticipantController::class, 'store']);
            Route::put('/{participant}', [\App\Http\Controllers\Api\WebexEkskul\ParticipantController::class, 'update']);
            Route::delete('/{participant}', [\App\Http\Controllers\Api\WebexEkskul\ParticipantController::class, 'destroy']);
        });

        // Pengurus
        Route::prefix('/{ekskul}/pengurus')->group(function () {
            Route::get('/', [\App\Http\Controllers\Api\WebexEkskul\PengurusController::class, 'index']);
            Route::post('/', [\App\Http\Controllers\Api\WebexEkskul\PengurusController::class, 'store']);
            Route::put('/{pengurus}', [\App\Http\Controllers\Api\WebexEkskul\PengurusController::class, 'update']);
            Route::delete('/{pengurus}', [\App\Http\Controllers\Api\WebexEkskul\PengurusController::class, 'destroy']);
        });

        // Reports
        Route::prefix('/{ekskul}/reports')->group(function () {
            Route::get('/', [\App\Http\Controllers\Api\WebexEkskul\ReportController::class, 'index']);
            Route::get('/{report}', [\App\Http\Controllers\Api\WebexEkskul\ReportController::class, 'show']);
            Route::post('/', [\App\Http\Controllers\Api\WebexEkskul\ReportController::class, 'store']);
            Route::put('/{report}', [\App\Http\Controllers\Api\WebexEkskul\ReportController::class, 'update']);
            Route::delete('/{report}', [\App\Http\Controllers\Api\WebexEkskul\ReportController::class, 'destroy']);
        });

        // Attendances
        Route::prefix('/{ekskul}/attendances')->group(function () {
            Route::get('/', [\App\Http\Controllers\Api\WebexEkskul\AttendanceController::class, 'index']);
            Route::post('/', [\App\Http\Controllers\Api\WebexEkskul\AttendanceController::class, 'store']);
            Route::put('/{attendance}', [\App\Http\Controllers\Api\WebexEkskul\AttendanceController::class, 'update']);
            Route::delete('/{attendance}', [\App\Http\Controllers\Api\WebexEkskul\AttendanceController::class, 'destroy']);
        });
    });

    // Eplin Violation Types endpoints (public - no auth required)
    Route::get('/eplin/violation-types', [\App\Http\Controllers\Api\EplinOfficerController::class, 'getViolationTypes']);

    // Eplin Officers endpoints (public - for checking officer status)
    Route::get('/eplin/officers', [\App\Http\Controllers\Api\EplinOfficerController::class, 'index']);

    // Eplin Violations endpoints (public - no auth required for reading)
    Route::get('/eplin/violations', [\App\Http\Controllers\Api\EplinViolationController::class, 'index']);
    Route::get('/eplin/violations/export', [\App\Http\Controllers\Api\EplinViolationController::class, 'export']);
    Route::get('/eplin/violations/{violation}', [\App\Http\Controllers\Api\EplinViolationController::class, 'show']);

    // Eplin Attendances endpoints (public GET - auth required for POST/PUT/DELETE)
    Route::get('/eplin/attendances', [\App\Http\Controllers\Api\EplinAttendanceController::class, 'index']);
    Route::get('/eplin/attendances/{attendance}', [\App\Http\Controllers\Api\EplinAttendanceController::class, 'show']);

    // Book endpoints
    Route::get('/books', [BookController::class, 'index']);
    Route::get('/books/{uuid}', [BookController::class, 'show']);
    Route::post('/books', [BookController::class, 'store']);
    Route::put('/books/{uuid}', [BookController::class, 'update']);
    Route::delete('/books/{uuid}', [BookController::class, 'destroy']);
});

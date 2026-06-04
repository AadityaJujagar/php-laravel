# PHP & Laravel: Zero to Pro for MERN Developers
### Backend API Development for Fintech — Fast Track Guide

> **Who this is for:** You know JavaScript/Node.js, Express, MongoDB. You need to get productive in PHP + Laravel *fast* for fintech backend work (APIs, auth, payments, compliance).

---

## Table of Contents

1. [Mental Model: MERN vs Laravel](#1-mental-model-mern-vs-laravel)
2. [PHP Basics — Only What You Need](#2-php-basics--only-what-you-need)
3. [Laravel Setup & Project Structure](#3-laravel-setup--project-structure)
4. [Routing & Controllers](#4-routing--controllers)
5. [Request Validation](#5-request-validation)
6. [Eloquent ORM (Your Mongoose Replacement)](#6-eloquent-orm-your-mongoose-replacement)
7. [Database Migrations & Seeders](#7-database-migrations--seeders)
8. [Authentication & Authorization](#8-authentication--authorization)
9. [API Development Best Practices](#9-api-development-best-practices)
10. [Fintech-Specific Patterns](#10-fintech-specific-patterns)
11. [Middleware](#11-middleware)
12. [Jobs, Queues & Events](#12-jobs-queues--events)
13. [Testing](#13-testing)
14. [Security Essentials for Fintech](#14-security-essentials-for-fintech)
15. [Your Learning Roadmap (Week-by-Week)](#15-your-learning-roadmap-week-by-week)

---

## 1. Mental Model: MERN vs Laravel

Before touching any code, map what you already know:

| What you know (MERN)       | Laravel equivalent              |
|----------------------------|---------------------------------|
| `express()` app            | Laravel Application             |
| `express.Router()`         | `routes/api.php`                |
| `app.use()` middleware     | Laravel Middleware              |
| Mongoose Model             | Eloquent Model                  |
| Mongoose Schema            | Migration + Eloquent fillable   |
| `.env` file                | `.env` file (same concept)      |
| `req.body`                 | `$request->input()` or `$request->validated()` |
| `res.json()`               | `response()->json()`            |
| `async/await`              | No async needed (sync by default, queues for heavy tasks) |
| npm / `package.json`       | Composer / `composer.json`      |
| `nodemon`                  | `php artisan serve`             |
| Joi / express-validator    | Laravel Form Requests           |
| JWT (jsonwebtoken)         | Laravel Sanctum / Passport      |
| bcrypt                     | `Hash::make()` (bcrypt built-in)|

**Key mindset shifts:**
- PHP is **synchronous** by default. No callbacks, no promise chains for normal requests.
- Laravel is **opinionated** — it has a "right" way. Follow conventions and it rewards you.
- Types are more explicit. PHP 8+ has strong type hints, use them.
- Artisan CLI is your best friend — it generates everything.

---

## 2. PHP Basics — Only What You Need

You don't need to learn all of PHP. Here's what actually matters for Laravel backend work.

### Variables & Types

```php
<?php

// Variables always start with $
$name = "John";
$age = 25;
$balance = 1500.50;
$isActive = true;
$data = null;

// Type declarations (USE THESE — especially in fintech)
function calculateInterest(float $principal, float $rate, int $months): float {
    return $principal * ($rate / 100) * ($months / 12);
}

// String interpolation (like JS template literals)
$greeting = "Hello, {$name}!";
echo $greeting; // Hello, John!
```

### Arrays (used everywhere in Laravel)

```php
<?php

// Indexed array (like JS array)
$roles = ['admin', 'user', 'auditor'];

// Associative array (like JS object / MongoDB document)
$user = [
    'name' => 'John',
    'email' => 'john@example.com',
    'balance' => 1500.00,
];

// Accessing
echo $user['name']; // John
echo $roles[0];     // admin

// Nested
$transaction = [
    'id' => 'TXN001',
    'user' => ['id' => 1, 'name' => 'John'],
    'amount' => 500.00,
    'type' => 'credit',
];

echo $transaction['user']['name']; // John

// Array functions you'll use constantly
$amounts = [100, 200, 300, 400];
$total = array_sum($amounts);         // 1000
$filtered = array_filter($amounts, fn($a) => $a > 150); // [200, 300, 400]
$mapped = array_map(fn($a) => $a * 1.1, $amounts);      // with 10% markup
```

### Classes & OOP

```php
<?php

// Classes work like JS classes but with stricter types
class BankAccount
{
    // Properties with types
    private float $balance;
    private string $accountNumber;
    protected string $currency;

    // Constructor
    public function __construct(string $accountNumber, float $initialBalance = 0.0)
    {
        $this->accountNumber = $accountNumber;
        $this->balance = $initialBalance;
        $this->currency = 'USD';
    }

    // Methods
    public function deposit(float $amount): float
    {
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Deposit amount must be positive');
        }

        $this->balance += $amount;
        return $this->balance;
    }

    public function getBalance(): float
    {
        return $this->balance;
    }

    // Static method (like JS static)
    public static function formatCurrency(float $amount): string
    {
        return number_format($amount, 2);
    }
}

$account = new BankAccount('ACC-001', 1000.00);
$account->deposit(500.00);
echo BankAccount::formatCurrency($account->getBalance()); // 1,500.00
```

### Error Handling

```php
<?php

// try/catch works exactly like JS
try {
    $result = performTransaction($userId, $amount);
} catch (\InvalidArgumentException $e) {
    // Handle specific exception
    return response()->json(['error' => $e->getMessage()], 422);
} catch (\Exception $e) {
    // Handle general exception
    Log::error('Transaction failed', ['error' => $e->getMessage()]);
    return response()->json(['error' => 'Transaction failed'], 500);
} finally {
    // Runs always (optional)
    releaseTransactionLock($userId);
}
```

### Null Safety (PHP 8+)

```php
<?php

// Null coalescing (like JS ??)
$name = $user['name'] ?? 'Anonymous';

// Nullsafe operator (like JS ?.)
$city = $user?->address?->city;

// Match expression (better switch)
$statusLabel = match($status) {
    'pending'   => 'Awaiting Processing',
    'approved'  => 'Transaction Approved',
    'rejected'  => 'Transaction Rejected',
    default     => 'Unknown Status',
};
```

---

## 3. Laravel Setup & Project Structure

### Installation

```bash
# Install Composer first (like npm for PHP)
# https://getcomposer.org/

# Create new Laravel project
composer create-project laravel/laravel fintech-api

cd fintech-api

# Start dev server
php artisan serve
# Runs on http://localhost:8000
```

### Project Structure (Annotated for MERN devs)

```
fintech-api/
├── app/
│   ├── Http/
│   │   ├── Controllers/       ← Your route handlers (like Express controllers)
│   │   ├── Middleware/        ← Express middleware equivalent
│   │   └── Requests/          ← Validation classes (like Joi schemas)
│   ├── Models/                ← Eloquent models (like Mongoose models)
│   ├── Services/              ← Business logic (create this folder yourself)
│   ├── Exceptions/            ← Custom exception handlers
│   └── Jobs/                  ← Background jobs (like BullMQ)
├── config/                    ← App configuration files
├── database/
│   ├── migrations/            ← Schema version control (like Mongoose schema + git)
│   └── seeders/               ← Test data seeders
├── routes/
│   ├── api.php                ← API routes (your main file)
│   └── web.php                ← Web routes (ignore for pure API work)
├── storage/logs/              ← Application logs
├── tests/                     ← Feature & Unit tests
├── .env                       ← Environment variables (same as Node.js)
└── artisan                    ← CLI tool (like package.json scripts)
```

### Essential `.env` for Fintech API

```env
APP_NAME=FintechAPI
APP_ENV=local
APP_KEY=base64:...  # auto-generated, never change manually
APP_DEBUG=true      # ALWAYS false in production
APP_URL=http://localhost:8000

# Database (use MySQL/PostgreSQL for fintech, NOT SQLite)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=fintech_db
DB_USERNAME=root
DB_PASSWORD=secret

# Cache & Queue (Redis recommended for fintech)
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Logging
LOG_CHANNEL=stack
LOG_LEVEL=debug

# JWT / Sanctum
SANCTUM_STATEFUL_DOMAINS=localhost:3000
```

### Key Artisan Commands

```bash
# Generate files (use these constantly)
php artisan make:controller Api/UserController --api
php artisan make:model Transaction --migration --factory --seeder
php artisan make:request StoreTransactionRequest
php artisan make:middleware VerifyKycStatus
php artisan make:job ProcessPayment
php artisan make:policy TransactionPolicy

# Database
php artisan migrate
php artisan migrate:rollback
php artisan migrate:fresh --seed   # drop all + re-migrate + seed

# Utility
php artisan route:list             # see all routes (like Express route listing)
php artisan tinker                 # REPL (like node in terminal)
php artisan config:clear           # clear config cache
php artisan optimize:clear         # clear all caches
```

---

## 4. Routing & Controllers

### Defining API Routes (`routes/api.php`)

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\AccountController;

// Public routes
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

// Protected routes (auth:sanctum = check Bearer token)
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/auth/logout', [AuthController::class, 'logout']);

    // Accounts
    Route::get('/accounts', [AccountController::class, 'index']);
    Route::post('/accounts', [AccountController::class, 'store']);
    Route::get('/accounts/{account}', [AccountController::class, 'show']);

    // Transactions — resourceful routes
    Route::apiResource('transactions', TransactionController::class);
    // Generates: GET /transactions, POST /transactions,
    //            GET /transactions/{id}, PUT /transactions/{id},
    //            DELETE /transactions/{id}

    // Nested resources
    Route::get('/accounts/{account}/transactions', [TransactionController::class, 'byAccount']);

    // Route grouping with prefix + middleware
    Route::prefix('admin')->middleware('role:admin')->group(function () {
        Route::get('/audit-logs', [AuditController::class, 'index']);
        Route::get('/users', [AdminUserController::class, 'index']);
    });
});
```

### Building a Controller

```bash
php artisan make:controller Api/TransactionController --api
```

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTransactionRequest;
use App\Models\Transaction;
use App\Services\TransactionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    // Dependency injection (Laravel auto-resolves this)
    public function __construct(
        private TransactionService $transactionService
    ) {}

    // GET /api/transactions
    public function index(Request $request): JsonResponse
    {
        $transactions = Transaction::query()
            ->where('user_id', $request->user()->id)
            ->with(['account', 'category'])       // eager load relations
            ->orderByDesc('created_at')
            ->paginate(20);                        // always paginate in fintech

        return response()->json($transactions);
    }

    // POST /api/transactions
    public function store(StoreTransactionRequest $request): JsonResponse
    {
        // $request->validated() gives only the validated data — use this always
        $transaction = $this->transactionService->create(
            $request->user(),
            $request->validated()
        );

        return response()->json([
            'message' => 'Transaction created successfully',
            'data' => $transaction,
        ], 201);
    }

    // GET /api/transactions/{transaction}
    public function show(Transaction $transaction): JsonResponse
    {
        // Route model binding: Laravel fetches the model automatically!
        // (Like Mongoose findById but automatic)
        $this->authorize('view', $transaction); // Policy check

        return response()->json(['data' => $transaction->load('account')]);
    }

    // DELETE /api/transactions/{transaction}
    public function destroy(Transaction $transaction): JsonResponse
    {
        $this->authorize('delete', $transaction);
        $transaction->delete();

        return response()->json(['message' => 'Transaction deleted'], 200);
    }
}
```

---

## 5. Request Validation

This is Laravel's version of Joi/express-validator. Always use Form Requests for API endpoints.

```bash
php artisan make:request StoreTransactionRequest
```

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreTransactionRequest extends FormRequest
{
    // Who can make this request?
    public function authorize(): bool
    {
        return $this->user() !== null; // must be authenticated
    }

    // Validation rules
    public function rules(): array
    {
        return [
            'account_id'    => 'required|integer|exists:accounts,id',
            'amount'        => 'required|numeric|min:0.01|max:1000000',
            'type'          => 'required|in:credit,debit,transfer',
            'currency'      => 'required|string|size:3',           // ISO 4217
            'description'   => 'nullable|string|max:255',
            'reference'     => 'nullable|string|unique:transactions,reference',
            'metadata'      => 'nullable|array',
            'metadata.*.key' => 'string',
        ];
    }

    // Custom error messages
    public function messages(): array
    {
        return [
            'amount.min'    => 'Transaction amount must be at least 0.01',
            'amount.max'    => 'Transaction amount cannot exceed 1,000,000',
            'currency.size' => 'Currency must be a valid 3-letter ISO code (e.g. USD)',
        ];
    }

    // CRITICAL: Override this so API returns JSON errors, not redirect
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ], 422)
        );
    }
}
```

---

## 6. Eloquent ORM (Your Mongoose Replacement)

### Defining a Model

```bash
php artisan make:model Transaction --migration
```

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use HasFactory, SoftDeletes; // SoftDeletes = deleted_at instead of actual delete

    // Fields that can be mass-assigned (like Mongoose's schema)
    protected $fillable = [
        'user_id',
        'account_id',
        'amount',
        'type',
        'currency',
        'status',
        'reference',
        'description',
        'metadata',
        'processed_at',
    ];

    // Type casting (auto-converts types when reading from DB)
    protected $casts = [
        'amount'       => 'decimal:2',   // always 2 decimal places
        'metadata'     => 'array',        // JSON column ↔ PHP array auto-conversion
        'processed_at' => 'datetime',     // string ↔ Carbon datetime object
    ];

    // Fields to hide from JSON output
    protected $hidden = ['deleted_at'];

    // Relationships (like Mongoose populate / refs)

    // Transaction belongs to a User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Transaction belongs to an Account
    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    // Local scope: reusable query filters
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    // Accessor: computed property (like a getter)
    public function getFormattedAmountAttribute(): string
    {
        return $this->currency . ' ' . number_format($this->amount, 2);
    }
}
```

### Querying with Eloquent

```php
<?php

// --- BASIC CRUD ---

// CREATE
$transaction = Transaction::create([
    'user_id'    => $user->id,
    'account_id' => $accountId,
    'amount'     => 500.00,
    'type'       => 'credit',
    'currency'   => 'USD',
    'status'     => 'pending',
]);

// READ
$transaction = Transaction::find(1);         // by primary key
$transaction = Transaction::findOrFail(1);   // throws 404 if not found (use this in APIs)
$transaction = Transaction::where('reference', 'TXN-001')->first();
$all = Transaction::all();                   // use sparingly, always paginate

// UPDATE
$transaction->update(['status' => 'approved']);
// or
$transaction->status = 'approved';
$transaction->save();

// DELETE (soft delete since we used SoftDeletes)
$transaction->delete();           // sets deleted_at
$transaction->forceDelete();      // actually deletes


// --- QUERYING ---

// Like MongoDB's find() with filters
$transactions = Transaction::query()
    ->where('user_id', $userId)
    ->where('type', 'debit')
    ->where('amount', '>=', 100)
    ->whereBetween('created_at', [$startDate, $endDate])
    ->whereNull('processed_at')
    ->with(['account', 'user'])    // Eager load (avoids N+1 queries)
    ->orderByDesc('created_at')
    ->paginate(20);

// Using scopes
$pending = Transaction::pending()->ofType('credit')->get();

// Aggregates
$totalDebits = Transaction::where('user_id', $userId)
    ->where('type', 'debit')
    ->sum('amount');

$count = Transaction::where('status', 'pending')->count();


// --- RELATIONSHIPS ---

// One-to-Many: User has many Transactions
class User extends Authenticatable {
    public function transactions() {
        return $this->hasMany(Transaction::class);
    }
    public function accounts() {
        return $this->hasMany(Account::class);
    }
}

// Load all transactions for a user
$user = User::with('transactions')->find($userId);
$user->transactions; // Collection of Transaction models

// Many-to-Many: User has many Roles
class User extends Authenticatable {
    public function roles() {
        return $this->belongsToMany(Role::class);
    }
}
$user->roles->pluck('name'); // ['admin', 'auditor']
```

---

## 7. Database Migrations & Seeders

### Writing Migrations

```bash
php artisan make:migration create_transactions_table
```

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();                                // auto-increment primary key
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('account_id')->constrained();

            $table->decimal('amount', 15, 2);            // 15 digits, 2 decimal — use for money
            $table->string('currency', 3)->default('USD'); // ISO 4217
            $table->enum('type', ['credit', 'debit', 'transfer']);
            $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'reversed'])
                  ->default('pending');

            $table->string('reference')->unique()->nullable();
            $table->string('description')->nullable();
            $table->json('metadata')->nullable();        // flexible extra data

            $table->timestamp('processed_at')->nullable();
            $table->timestamps();                        // created_at + updated_at
            $table->softDeletes();                       // deleted_at

            // Indexes for query performance (critical for fintech)
            $table->index(['user_id', 'status']);
            $table->index(['account_id', 'created_at']);
            $table->index('reference');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
```

**Never use `float` for money in databases — always use `decimal(15, 2)`.**

---

## 8. Authentication & Authorization

### Setting Up Laravel Sanctum (Token-based Auth for APIs)

```bash
composer require laravel/sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan migrate
```

### Auth Controller

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password), // bcrypt by default
        ]);

        // Create token with abilities (like JWT scopes)
        $token = $user->createToken('auth_token', ['transactions:read', 'transactions:write'])
                       ->plainTextToken;

        return response()->json([
            'message' => 'Registration successful',
            'token'   => $token,
            'user'    => $user,
        ], 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        if (!$user->is_active) {
            return response()->json(['message' => 'Account suspended'], 403);
        }

        // Revoke old tokens (optional — enforce single session)
        $user->tokens()->delete();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user'  => $user,
        ]);
    }

    public function logout(): JsonResponse
    {
        // Revoke current token
        auth()->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }

    public function me(): JsonResponse
    {
        return response()->json(['user' => auth()->user()]);
    }
}
```

### Policies (Row-level Authorization)

```bash
php artisan make:policy TransactionPolicy --model=Transaction
```

```php
<?php

namespace App\Policies;

use App\Models\Transaction;
use App\Models\User;

class TransactionPolicy
{
    // Can this user view the transaction?
    public function view(User $user, Transaction $transaction): bool
    {
        return $user->id === $transaction->user_id
            || $user->hasRole('admin');
    }

    // Can this user delete?
    public function delete(User $user, Transaction $transaction): bool
    {
        return $user->hasRole('admin')
            && $transaction->status === 'pending';
    }
}
```

Register in `AuthServiceProvider`:
```php
protected $policies = [
    Transaction::class => TransactionPolicy::class,
];
```

Use in controller:
```php
$this->authorize('view', $transaction);
```

### Role-Based Access (using spatie/laravel-permission)

```bash
composer require spatie/laravel-permission
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan migrate
```

```php
// Assign roles/permissions
$user->assignRole('auditor');
$user->givePermissionTo('view-transactions');

// Check in controller
if ($request->user()->can('approve-transactions')) { ... }
if ($request->user()->hasRole('admin')) { ... }

// Middleware on routes
Route::middleware(['role:admin'])->group(function () {
    Route::get('/admin/reports', [ReportController::class, 'index']);
});
```

---

## 9. API Development Best Practices

### Consistent API Response Format

Create an `app/Traits/ApiResponse.php`:

```php
<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    protected function success($data = null, string $message = 'Success', int $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $data,
        ], $code);
    }

    protected function error(string $message, int $code = 400, $errors = null): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors'  => $errors,
        ], $code);
    }

    protected function paginated($paginator, string $message = 'Success'): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $paginator->items(),
            'meta'    => [
                'current_page' => $paginator->currentPage(),
                'per_page'     => $paginator->perPage(),
                'total'        => $paginator->total(),
                'last_page'    => $paginator->lastPage(),
            ],
        ]);
    }
}
```

### API Resources (Transform Model to JSON)

Like a serializer — controls what fields are exposed in the API response.

```bash
php artisan make:resource TransactionResource
```

```php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'              => $this->id,
            'amount'          => (float) $this->amount,
            'formatted_amount'=> $this->formatted_amount,  // accessor
            'currency'        => $this->currency,
            'type'            => $this->type,
            'status'          => $this->status,
            'reference'       => $this->reference,
            'description'     => $this->description,
            'account'         => new AccountResource($this->whenLoaded('account')),
            'created_at'      => $this->created_at->toISOString(),
            'processed_at'    => $this->processed_at?->toISOString(),

            // Only include for admins
            'metadata'        => $this->when(
                $request->user()?->hasRole('admin'),
                $this->metadata
            ),
        ];
    }
}
```

Use in controller:
```php
return TransactionResource::collection($transactions);
return new TransactionResource($transaction);
```

### Global Exception Handler (`app/Exceptions/Handler.php`)

```php
<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    public function render($request, Throwable $e)
    {
        // For API requests, always return JSON
        if ($request->expectsJson() || $request->is('api/*')) {
            return match(true) {
                $e instanceof AuthenticationException =>
                    response()->json(['message' => 'Unauthenticated'], 401),

                $e instanceof NotFoundHttpException =>
                    response()->json(['message' => 'Resource not found'], 404),

                $e instanceof ValidationException =>
                    response()->json([
                        'message' => 'Validation failed',
                        'errors'  => $e->errors(),
                    ], 422),

                default => response()->json([
                    'message' => app()->isProduction()
                        ? 'An error occurred'
                        : $e->getMessage(),
                ], 500),
            };
        }

        return parent::render($request, $e);
    }
}
```

---

## 10. Fintech-Specific Patterns

### Service Layer Pattern (Separate Business Logic)

Always put business logic in Services, not Controllers.

```php
<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TransactionService
{
    // ALWAYS use DB transactions for financial operations
    public function transfer(User $user, int $fromAccountId, int $toAccountId, float $amount): array
    {
        return DB::transaction(function () use ($user, $fromAccountId, $toAccountId, $amount) {

            // Lock rows for update (prevents race conditions)
            $fromAccount = Account::lockForUpdate()->findOrFail($fromAccountId);
            $toAccount   = Account::lockForUpdate()->findOrFail($toAccountId);

            // Business rule checks
            if ($fromAccount->user_id !== $user->id) {
                throw new \Exception('Unauthorized account access', 403);
            }

            if ($fromAccount->balance < $amount) {
                throw new \Exception('Insufficient funds', 422);
            }

            $reference = 'TXN-' . strtoupper(Str::random(12));

            // Debit source
            $fromAccount->decrement('balance', $amount);
            $debit = Transaction::create([
                'user_id'    => $user->id,
                'account_id' => $fromAccountId,
                'amount'     => $amount,
                'type'       => 'debit',
                'status'     => 'completed',
                'reference'  => $reference . '-D',
                'currency'   => $fromAccount->currency,
            ]);

            // Credit destination
            $toAccount->increment('balance', $amount);
            $credit = Transaction::create([
                'user_id'    => $toAccount->user_id,
                'account_id' => $toAccountId,
                'amount'     => $amount,
                'type'       => 'credit',
                'status'     => 'completed',
                'reference'  => $reference . '-C',
                'currency'   => $toAccount->currency,
            ]);

            // Emit event for audit trail, notifications
            event(new \App\Events\TransferCompleted($debit, $credit));

            return ['debit' => $debit, 'credit' => $credit];
        });
    }
}
```

### Idempotency (Critical for Fintech)

Prevent duplicate transactions from retried requests:

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class IdempotencyMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $key = $request->header('Idempotency-Key');

        if (!$key && in_array($request->method(), ['POST', 'PATCH', 'PUT'])) {
            return response()->json([
                'message' => 'Idempotency-Key header is required',
            ], 400);
        }

        if ($key) {
            $cacheKey = 'idempotency:' . $key;

            // If we've seen this key, return the cached response
            if ($cached = Cache::get($cacheKey)) {
                return response()->json($cached['body'], $cached['status']);
            }

            $response = $next($request);

            // Cache the response for 24 hours
            Cache::put($cacheKey, [
                'body'   => $response->getData(true),
                'status' => $response->getStatusCode(),
            ], now()->addDay());

            return $response;
        }

        return $next($request);
    }
}
```

### Audit Trail

```bash
php artisan make:model AuditLog --migration
```

```php
<?php

namespace App\Observers;

use App\Models\AuditLog;
use App\Models\Transaction;

class TransactionObserver
{
    public function updated(Transaction $transaction): void
    {
        AuditLog::create([
            'user_id'    => auth()->id(),
            'model_type' => 'Transaction',
            'model_id'   => $transaction->id,
            'action'     => 'updated',
            'old_values' => $transaction->getOriginal(),
            'new_values' => $transaction->getDirty(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}

// Register in App\Providers\AppServiceProvider::boot()
Transaction::observe(TransactionObserver::class);
```

---

## 11. Middleware

```bash
php artisan make:middleware VerifyKycStatus
```

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VerifyKycStatus
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if ($user && $user->kyc_status !== 'verified') {
            return response()->json([
                'message' => 'KYC verification required to perform this action',
                'kyc_url' => '/api/kyc/start',
            ], 403);
        }

        return $next($request);
    }
}
```

Register and use in routes:
```php
// In app/Http/Kernel.php - $routeMiddleware array
'kyc.verified' => \App\Http\Middleware\VerifyKycStatus::class,
'idempotent'   => \App\Http\Middleware\IdempotencyMiddleware::class,

// In routes/api.php
Route::middleware(['auth:sanctum', 'kyc.verified', 'idempotent'])->group(function () {
    Route::post('/transactions', [TransactionController::class, 'store']);
    Route::post('/transfers', [TransferController::class, 'store']);
});
```

### Rate Limiting (Built into Laravel)

```php
// In app/Providers/RouteServiceProvider.php
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;

protected function configureRateLimiting(): void
{
    RateLimiter::for('api', function (Request $request) {
        return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
    });

    RateLimiter::for('transactions', function (Request $request) {
        return Limit::perMinute(10)->by($request->user()->id); // strict limit for financial ops
    });
}

// Apply in routes
Route::middleware(['auth:sanctum', 'throttle:transactions'])->group(function () {
    Route::post('/transactions', [TransactionController::class, 'store']);
});
```

---

## 12. Jobs, Queues & Events

### Creating a Background Job

```bash
php artisan make:job ProcessPayment
```

```php
<?php

namespace App\Jobs;

use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessPayment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;         // retry 3 times on failure
    public int $backoff = 30;      // wait 30 seconds between retries

    public function __construct(
        private Transaction $transaction
    ) {}

    public function handle(): void
    {
        // Heavy processing: call payment gateway, update status, send notifications
        $result = app(PaymentGatewayService::class)->process($this->transaction);

        $this->transaction->update([
            'status'       => $result->success ? 'completed' : 'failed',
            'processed_at' => now(),
            'metadata'     => array_merge($this->transaction->metadata ?? [], [
                'gateway_ref' => $result->reference,
            ]),
        ]);
    }

    public function failed(\Throwable $exception): void
    {
        // Called after all retries exhausted
        $this->transaction->update(['status' => 'failed']);
        \Log::error('Payment processing failed', [
            'transaction_id' => $this->transaction->id,
            'error'          => $exception->getMessage(),
        ]);
    }
}

// Dispatching the job
ProcessPayment::dispatch($transaction);              // async (queued)
ProcessPayment::dispatchSync($transaction);          // sync (immediate, for testing)
ProcessPayment::dispatch($transaction)->delay(now()->addMinutes(5)); // delayed
```

Run queue worker:
```bash
php artisan queue:work redis --tries=3 --timeout=60
```

---

## 13. Testing

```bash
php artisan make:test TransactionTest --unit
php artisan make:test TransactionApiTest
```

```php
<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionApiTest extends TestCase
{
    use RefreshDatabase; // fresh DB for each test (like beforeEach cleanup)

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['kyc_status' => 'verified']);
    }

    public function test_authenticated_user_can_list_transactions(): void
    {
        Transaction::factory()->count(5)->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)
                         ->getJson('/api/transactions');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [['id', 'amount', 'type', 'status']],
                     'meta' => ['current_page', 'total'],
                 ])
                 ->assertJsonCount(5, 'data');
    }

    public function test_create_transaction_validates_amount(): void
    {
        $response = $this->actingAs($this->user)
                         ->postJson('/api/transactions', [
                             'amount' => -100,  // invalid
                             'type'   => 'credit',
                         ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['amount', 'account_id']);
    }

    public function test_user_cannot_view_another_users_transaction(): void
    {
        $otherUser = User::factory()->create();
        $transaction = Transaction::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($this->user)
                         ->getJson("/api/transactions/{$transaction->id}");

        $response->assertStatus(403);
    }

    public function test_transfer_uses_database_transaction(): void
    {
        $account = Account::factory()->create([
            'user_id' => $this->user->id,
            'balance' => 1000.00,
        ]);

        // Mock the payment gateway to simulate failure
        $this->mock(PaymentGatewayService::class)
             ->shouldReceive('process')
             ->andThrow(new \Exception('Gateway down'));

        $response = $this->actingAs($this->user)
                         ->postJson('/api/transfers', [
                             'from_account_id' => $account->id,
                             'to_account_id'   => 999,
                             'amount'          => 500,
                         ]);

        // Balance should be unchanged (DB transaction rolled back)
        $this->assertEquals(1000.00, $account->fresh()->balance);
    }
}
```

Run tests:
```bash
php artisan test
php artisan test --filter=TransactionApiTest
php artisan test --coverage
```

---

## 14. Security Essentials for Fintech

### Checklist

```
✅ Never store plain-text passwords (Hash::make() always)
✅ Always use $request->validated() not $request->all()
✅ Use parameterized queries (Eloquent does this by default — never raw SQL with user input)
✅ Rotate tokens on sensitive actions (password change, suspicious login)
✅ decimal(15,2) for all money columns — never float
✅ DB transactions for all multi-step financial operations
✅ Row-level locking (lockForUpdate()) on balance reads/writes
✅ Rate limit all auth and financial endpoints
✅ Idempotency keys on all POST financial endpoints
✅ Audit log all state changes on financial records
✅ APP_DEBUG=false in production
✅ HTTPS only — enforce in middleware
✅ Validate and sanitize all external webhook payloads
✅ Never expose stack traces or internal errors to API consumers
```

### Preventing SQL Injection

```php
// ❌ NEVER do this
DB::select("SELECT * FROM transactions WHERE user_id = {$userId}");

// ✅ Always use Eloquent or parameterized bindings
Transaction::where('user_id', $userId)->get();
DB::select('SELECT * FROM transactions WHERE user_id = ?', [$userId]);
```

### Encrypting Sensitive Data

```php
<?php

// In your model, encrypt sensitive fields at rest
use Illuminate\Support\Facades\Crypt;

class UserBankDetail extends Model
{
    protected $casts = [
        'bank_account_number' => 'encrypted',  // auto encrypt/decrypt
        'routing_number'      => 'encrypted',
    ];
}
```

### HTTPS Enforcement Middleware

```php
<?php

namespace App\Http\Middleware;

use Closure;

class ForceHttps
{
    public function handle($request, Closure $next)
    {
        if (!$request->secure() && app()->environment('production')) {
            return redirect()->secure($request->getRequestUri());
        }
        return $next($request);
    }
}
```

---

## 15. Your Learning Roadmap (Week-by-Week)

### Week 1 — PHP + Laravel Foundations

- [ ] PHP syntax: variables, arrays, classes, exceptions
- [ ] Install Laravel, understand project structure
- [ ] Build routes, controllers, return JSON responses
- [ ] Set up MySQL database, write migrations
- [ ] Basic Eloquent: create, read, update, delete

**Goal:** Build a basic CRUD API for a `users` resource.

### Week 2 — Auth + Validation + Relationships

- [ ] Set up Sanctum for token auth (register, login, logout)
- [ ] Write Form Request validators
- [ ] Define Eloquent relationships (hasMany, belongsTo)
- [ ] Write API Resources for response shaping
- [ ] Build a middleware (e.g., check user status)

**Goal:** Build an API with auth + protected routes + relational data.

### Week 3 — Fintech Patterns

- [ ] DB transactions (DB::transaction)
- [ ] Service layer pattern
- [ ] Policies + Spatie roles/permissions
- [ ] Idempotency middleware
- [ ] Audit logging observer
- [ ] Rate limiting

**Goal:** Implement a transfer API with all fintech safeguards.

### Week 4 — Jobs, Testing, Production-Ready

- [ ] Create and dispatch a background job
- [ ] Set up Redis + queue worker
- [ ] Write Feature tests with RefreshDatabase
- [ ] Global exception handler
- [ ] Review security checklist
- [ ] Read Laravel docs: caching, events, notifications

**Goal:** Have tested, production-safe code with async processing.

---

### Essential Resources

| Resource | Use For |
|----------|---------|
| [laravel.com/docs](https://laravel.com/docs) | Official docs — bookmark this |
| [laracasts.com](https://laracasts.com) | Best Laravel video tutorials |
| `php artisan --help` | Any time you're unsure of a command |
| `php artisan tinker` | Test Eloquent queries interactively |
| [Spatie Laravel Permission](https://spatie.be/docs/laravel-permission) | Role/permission management |

---

> **You already know the hard stuff** — APIs, auth, databases, and async patterns. Laravel is just a different way to write what you already understand. Trust your JS instincts, learn the PHP syntax, follow Laravel conventions, and you'll be productive within 2–3 weeks.

# مشروع: AI Expense Tracker

## الفكرة
تطبيق لتتبع المصاريف والإيرادات مدعوم بالذكاء الاصطناعي مع مساعد دردشة وكيل (Agentic AI Chat).

## المتطلبات

## الميزات الأساسية
- إضافة/تعديل/حذف المصاريف والإيرادات
- تصنيف تلقائي بالذكاء الاصطناعي
- تصنيفات جاهزة + مخصصة
- إدارة الميزانية الشهرية مع تنبيهات
- تقارير نصية + رسوم بيانية
- بحث متقدم (تصنيف، تاريخ، مبلغ، طريقة دفع)
- تصوير الفواتير وإرفاقها
- مشاركة المصاريف مع أفراد العائلة
- تصدير البيانات (CSV, Excel, PDF)
- إشعارات تذكير بإدخال المصاريف + تنبيهات تجاوز الميزانية

## AI | مساعد دردشة وكيل (Agentic AI Chat)
- دردشة ذكية تفاعلية مع المستخدم
- إجابة عن أسئلة حول المصاريف ("كم صرفت هذا الشهر؟"، "كم باقي من الميزانية؟")
- تحليل ذكي وتوصيات ("أين يمكنني التقليل؟"، "ما هي أكبر فئات الإنفاق؟")
- تنفيذ أوامر نصية مباشرة ("أضف مصروف 200 ريال على طعام"، "زِد ميزانية الترفيه")
- مساعد وكيل (Agentic) قادر على اتخاذ إجراءات في التطبيق نيابة عن المستخدم

## الجمهور المستهدف
- عائلة

## نموذج العمل
- مجاني بالكامل

## العملة
- ريال سعودي (SAR)

## المعاملات المالية
- أنواع المعاملات: مصاريف + إيرادات (دخل)
- عدد المعاملات المتوقعة: 1-10 يوميًا
- طرق الدفع: كاش، بطاقة ائتمان، محفظة رقمية، تحويل بنكي
- المصاريف المتكررة: لا (إدخال يدوي لكل مرة)
- المزامنة البنكية: لا (إدخال يدوي + تصوير فواتير)

## المستخدمون والحسابات
- تسجيل الدخول: بريد إلكتروني + Google + Apple
- مشاركة المصاريف: نعم (مع أفراد العائلة)
- عدد المستخدمين: متعدد (عائلة)

## واجهة المستخدم
- اللغة: العربية فقط
- الوضع الليلي (Dark Mode): نعم
- دعم تصوير وإرفاق الفواتير: نعم

## التقارير والتحليلات
- تقارير نصية (جداول وأرقام)
- رسوم بيانية (مخططات دائرية وشريطية)
- بحث متقدم مع فلترة (تصنيف، تاريخ، مبلغ، طريقة دفع)
- تصدير البيانات (CSV, Excel, PDF)

## الإشعارات
- تذكير بإدخال المصاريف
- تنبيهات عند تجاوز الميزانية

## التقنيات (مبدئيًا)
- النوع: Web App سحابي (Online)
- الاستضافة: سحابية
- تخزين البيانات: قاعدة بيانات سحابية

## المدة الزمنية
- 1-2 أشهر (MVP)

## اختيار التقنيات
- Laravel
- Inertia (larvel selte template)
- TailwindCSS

# design system
Mobile first
read DESIGN.md file

# Frontend pages

## هيكل الصفحات (MVP الإصدار الأول)

```
resources/js/pages/
├── Dashboard.svelte              # لوحة التحكم
├── transactions/
│   ├── Index.svelte              # قائمة المعاملات
│   ├── Create.svelte             # إضافة معاملة جديدة
│   └── Edit.svelte               # تعديل معاملة
├── categories/
│   └── Index.svelte              # إدارة التصنيفات
├── auth/                         # صفحات المصادقة (موجودة مسبقاً)
│   ├── Login.svelte
│   ├── Register.svelte
│   ├── ForgotPassword.svelte
│   └── ...
└── settings/                     # الإعدادات (موجودة مسبقاً)
    ├── Profile.svelte
    ├── Security.svelte
    └── Appearance.svelte
```

## الشريط الجانبي (Navigation)

| # | العنوان | الأيقونة | المسار |
|---|---------|----------|--------|
| 1 | لوحة التحكم | LayoutGrid | /dashboard |
| 2 | المعاملات | ArrowRightLeft | /transactions |
| 3 | التصنيفات | Tags | /categories |
| 4 | الإعدادات | Settings | /settings/profile |

## وصف الصفحات

### 1. لوحة التحكم (Dashboard)
- 4 بطاقات إحصائية: إجمالي المصاريف، إجمالي الإيرادات، الرصيد، عدد المعاملات
- قائمة آخر 10 معاملات
- زر سريع لإضافة معاملة جديدة
- يعرض بيانات الشهر الحالي فقط
- **Controller:** `DashboardController@__invoke`

### 2. قائمة المعاملات (Transactions Index)
- جدول بآخر المعاملات مع pagination
- بحث نصي في الوصف
- فلترة متقدمة: النوع (مصروف/إيراد)، التصنيف، طريقة الدفع
- أزرار تعديل وحذف لكل معاملة
- تأكيد قبل الحذف
- عرض المبلغ، التصنيف، طريقة الدفع، التاريخ
- **Controller:** `TransactionController@index`

### 3. إضافة معاملة (Transactions Create)
- نموذج إضافة معاملة جديدة
- حقول: النوع، المبلغ، الوصف، التصنيف، طريقة الدفع، التاريخ، ملاحظات، صورة الفاتورة
- تصفية التصنيفات حسب نوع المعاملة المختار
- **Controller:** `TransactionController@create` + `TransactionController@store`

### 4. تعديل معاملة (Transactions Edit)
- نفس نموذج الإضافة مع بيانات محملة مسبقاً
- إمكانية تغيير صورة الفاتورة
- **Controller:** `TransactionController@edit` + `TransactionController@update`

### 5. التصنيفات (Categories Index)
- عرض التصنيفات الجاهزة (غير قابلة للتعديل/الحذف)
- عرض التصنيفات المخصصة (قابلة للتعديل والحذف)
- نافذة منبثقة (Dialog) لإضافة تصنيف جديد
- نافذة منبثقة لتعديل التصنيف
- حذف مع تأكيد
- **Controller:** `CategoryController`

## هياكل البيانات

### Categories Table
| الحقل | النوع | وصف |
|-------|-------|-----|
| id | bigint | المفتاح الرئيسي |
| user_id | nullable FK | null للتصنيفات الجاهزة، user.id للمخصصة |
| name | string | اسم التصنيف |
| icon | string | اسم أيقونة Lucide |
| type | enum | expense / income / both |
| is_default | boolean | هل هو تصنيف جاهز |
| timestamps | | |

### Transactions Table
| الحقل | النوع | وصف |
|-------|-------|-----|
| id | bigint | المفتاح الرئيسي |
| user_id | FK | صاحب المعاملة |
| category_id | nullable FK | التصنيف |
| type | enum | expense / income |
| amount | decimal(12,2) | المبلغ |
| description | nullable string | الوصف |
| payment_method | enum | cash / credit_card / digital_wallet / bank_transfer |
| transaction_date | date | تاريخ المعاملة |
| receipt_image_path | nullable string | مسار صورة الفاتورة |
| notes | nullable text | ملاحظات |
| timestamps | | |

## التصنيفات الجاهزة (Seeder)

| الاسم | النوع |
|-------|-------|
| طعام ومشروبات | expense |
| مواصلات | expense |
| سكن | expense |
| فواتير ومرافق | expense |
| ترفيه | expense |
| تسوق | expense |
| صحة | expense |
| تعليم | expense |
| راتب | income |
| عمل حر | income |
| استثمار | income |
| هدايا | both |
| أخرى | both |

## المسارات (Routes)

### web.php
```
GET  /                 → Welcome (صفحة الترحيب)
GET  /dashboard        → DashboardController (لوحة التحكم)
```

### transactions.php (auth + verified)
```
GET    /transactions                      → index (القائمة)
GET    /transactions/create               → create (نموذج الإضافة)
POST   /transactions                      → store (حفظ)
GET    /transactions/{transaction}/edit   → edit (نموذج التعديل)
PATCH  /transactions/{transaction}        → update (تحديث)
DELETE /transactions/{transaction}        → destroy (حذف)
```

### categories.php (auth + verified)
```
GET    /categories                 → index (القائمة)
POST   /categories                 → store (إضافة تصنيف)
PATCH  /categories/{category}      → update (تعديل تصنيف)
DELETE /categories/{category}      → destroy (حذف تصنيف)
```

### settings.php (موجود مسبقاً)
```
GET    /settings/profile     → ProfileController@edit
PATCH  /settings/profile     → ProfileController@update
DELETE /settings/profile     → ProfileController@destroy
GET    /settings/security    → SecurityController@edit
PUT    /settings/password    → SecurityController@update
GET    /settings/appearance  → Appearance settings
```

## ملاحظات تقنية

- **التسمية:** اسم التطبيق "مدبّر" (Mudabbir)
- **اللغة:** جميع النصوص بالعربية
- **العملة:** ريال سعودي (ر.س)
- **Svelte 5 Runes:** استخدام `$state`, `$derived`, `$props`
- **Inertia.js:** استخدام `router`, `page`, `Link`, `Form`
- **shadcn-svelte:** استخدام مكونات Select, Dialog, Button, Input, Card, Textarea
- **lucide-svelte:** أيقونات للواجهة
- **Tailwind CSS v4:** تنسيق الواجهة
- **التصميم:** Mobile first
- **RTL:** دعم اللغة العربية (يحتاج إعدادات Tailwind إضافية لاحقاً)

===

<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to ensure the best experience when building Laravel applications.

## Foundational Context

This application is a Laravel application and its main Laravel ecosystems package & versions are below. You are an expert with them all. Ensure you abide by these specific packages & versions.

- php - 8.5
- inertiajs/inertia-laravel (INERTIA_LARAVEL) - v3
- laravel/fortify (FORTIFY) - v1
- laravel/framework (LARAVEL) - v13
- laravel/prompts (PROMPTS) - v0
- laravel/wayfinder (WAYFINDER) - v0
- larastan/larastan (LARASTAN) - v3
- laravel/boost (BOOST) - v2
- laravel/mcp (MCP) - v0
- laravel/pail (PAIL) - v1
- laravel/pint (PINT) - v1
- laravel/sail (SAIL) - v1
- phpunit/phpunit (PHPUNIT) - v12
- @inertiajs/svelte (INERTIA_SVELTE) - v3
- tailwindcss (TAILWINDCSS) - v4
- @laravel/vite-plugin-wayfinder (WAYFINDER_VITE) - v0
- eslint (ESLINT) - v9
- prettier (PRETTIER) - v3

## Skills Activation

This project has domain-specific skills available in `**/skills/**`. You MUST activate the relevant skill whenever you work in that domain—don't wait until you're stuck.

## Conventions

- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts

- Do not create verification scripts or tinker when tests cover that functionality and prove they work. Unit and feature tests are more important.

## Application Structure & Architecture

- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling

- If the user doesn't see a frontend change reflected in the UI, it could mean they need to run `npm run build`, `npm run dev`, or `composer run dev`. Ask them.

## Documentation Files

- You must only create documentation files if explicitly requested by the user.

## Replies

- Be concise in your explanations - focus on what's important rather than explaining obvious details.

=== boost rules ===

# Laravel Boost

## Tools

- Laravel Boost is an MCP server with tools designed specifically for this application. Prefer Boost tools over manual alternatives like shell commands or file reads.
- Use `database-query` to run read-only queries against the database instead of writing raw SQL in tinker.
- Use `database-schema` to inspect table structure before writing migrations or models.
- Use `get-absolute-url` to resolve the correct scheme, domain, and port for project URLs. Always use this before sharing a URL with the user.
- Use `browser-logs` to read browser logs, errors, and exceptions. Only recent logs are useful, ignore old entries.

## Searching Documentation (IMPORTANT)

- Always use `search-docs` before making code changes. Do not skip this step. It returns version-specific docs based on installed packages automatically.
- Pass a `packages` array to scope results when you know which packages are relevant.
- Use multiple broad, topic-based queries: `['rate limiting', 'routing rate limiting', 'routing']`. Expect the most relevant results first.
- Do not add package names to queries because package info is already shared. Use `test resource table`, not `filament 4 test resource table`.

### Search Syntax

1. Use words for auto-stemmed AND logic: `rate limit` matches both "rate" AND "limit".
2. Use `"quoted phrases"` for exact position matching: `"infinite scroll"` requires adjacent words in order.
3. Combine words and phrases for mixed queries: `middleware "rate limit"`.
4. Use multiple queries for OR logic: `queries=["authentication", "middleware"]`.

## Artisan

- Run Artisan commands directly via the command line (e.g., `php artisan route:list`). Use `php artisan list` to discover available commands and `php artisan [command] --help` to check parameters.
- Inspect routes with `php artisan route:list`. Filter with: `--method=GET`, `--name=users`, `--path=api`, `--except-vendor`, `--only-vendor`.
- Read configuration values using dot notation: `php artisan config:show app.name`, `php artisan config:show database.default`. Or read config files directly from the `config/` directory.

## Tinker

- Execute PHP in app context for debugging and testing code. Do not create models without user approval, prefer tests with factories instead. Prefer existing Artisan commands over custom tinker code.
- Always use single quotes to prevent shell expansion: `php artisan tinker --execute 'Your::code();'`
  - Double quotes for PHP strings inside: `php artisan tinker --execute 'User::where("active", true)->count();'`

=== php rules ===

# PHP

- Always use curly braces for control structures, even for single-line bodies.
- Use PHP 8 constructor property promotion: `public function __construct(public GitHub $github) { }`. Do not leave empty zero-parameter `__construct()` methods unless the constructor is private.
- Use explicit return type declarations and type hints for all method parameters: `function isAccessible(User $user, ?string $path = null): bool`
- Use TitleCase for Enum keys: `FavoritePerson`, `BestLake`, `Monthly`.
- Prefer PHPDoc blocks over inline comments. Only add inline comments for exceptionally complex logic.
- Use array shape type definitions in PHPDoc blocks.

=== deployments rules ===

# Deployment

- Laravel can be deployed using [Laravel Cloud](https://cloud.laravel.com/), which is the fastest way to deploy and scale production Laravel applications.

=== tests rules ===

# Test Enforcement

- Every change must be programmatically tested. Write a new test or update an existing test, then run the affected tests to make sure they pass.
- Run the minimum number of tests needed to ensure code quality and speed. Use `php artisan test --compact` with a specific filename or filter.

=== inertia-laravel/core rules ===

# Inertia

- Inertia creates fully client-side rendered SPAs without modern SPA complexity, leveraging existing server-side patterns.
- Components live in `resources/js/pages` (unless specified in `vite.config.js`). Use `Inertia::render()` for server-side routing instead of Blade views.
- ALWAYS use `search-docs` tool for version-specific Inertia documentation and updated code examples.
- IMPORTANT: Activate `inertia-svelte-development` when working with Inertia Svelte client-side patterns.

# Inertia v3

- Use all Inertia features from v1, v2, and v3. Check the documentation before making changes to ensure the correct approach.
- New v3 features: standalone HTTP requests (`useHttp` hook), optimistic updates with automatic rollback, layout props (`useLayoutProps` hook), instant visits, simplified SSR via `@inertiajs/vite` plugin, custom exception handling for error pages.
- Carried over from v2: deferred props, infinite scroll, merging props, polling, prefetching, once props, flash data.
- When using deferred props, add an empty state with a pulsing or animated skeleton.
- Axios has been removed. Use the built-in XHR client with interceptors, or install Axios separately if needed.
- `Inertia::lazy()` / `LazyProp` has been removed. Use `Inertia::optional()` instead.
- Prop types (`Inertia::optional()`, `Inertia::defer()`, `Inertia::merge()`) work inside nested arrays with dot-notation paths.
- SSR works automatically in Vite dev mode with `@inertiajs/vite` - no separate Node.js server needed during development.
- Event renames: `invalid` is now `httpException`, `exception` is now `networkError`.
- `router.cancel()` replaced by `router.cancelAll()`.
- The `future` configuration namespace has been removed - all v2 future options are now always enabled.

=== laravel/core rules ===

# Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using `php artisan list` and check their parameters with `php artisan [command] --help`.
- If you're creating a generic PHP class, use `php artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Model Creation

- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `php artisan make:model --help` to check the available options.

## APIs & Eloquent Resources

- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

## URL Generation

- When generating links to other pages, prefer named routes and the `route()` function.

## Testing

- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

## Vite Error

- If you receive an "Illuminate\Foundation\ViteException: Unable to locate file in Vite manifest" error, you can run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.

=== wayfinder/core rules ===

# Laravel Wayfinder

Use Wayfinder to generate TypeScript functions for Laravel routes. Import from `@/actions/` (controllers) or `@/routes/` (named routes).

=== pint/core rules ===

# Laravel Pint Code Formatter

- If you have modified any PHP files, you must run `vendor/bin/pint --dirty --format agent` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test --format agent`, simply run `vendor/bin/pint --format agent` to fix any formatting issues.

=== phpunit/core rules ===

# PHPUnit

- This application uses PHPUnit for testing. All tests must be written as PHPUnit classes. Use `php artisan make:test --phpunit {name}` to create a new test.
- If you see a test using "Pest", convert it to PHPUnit.
- Every time a test has been updated, run that singular test.
- When the tests relating to your feature are passing, ask the user if they would like to also run the entire test suite to make sure everything is still passing.
- Tests should cover all happy paths, failure paths, and edge cases.
- You must not remove any tests or test files from the tests directory without approval. These are not temporary or helper files; these are core to the application.

## Running Tests

- Run the minimal number of tests, using an appropriate filter, before finalizing.
- To run all tests: `php artisan test --compact`.
- To run all tests in a file: `php artisan test --compact tests/Feature/ExampleTest.php`.
- To filter on a particular test name: `php artisan test --compact --filter=testName` (recommended after making a change to a related file).

=== inertia-svelte/core rules ===

# Inertia + Svelte

- IMPORTANT: Activate `inertia-svelte-development` when working with Inertia Svelte client-side patterns.

</laravel-boost-guidelines>

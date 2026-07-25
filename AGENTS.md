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

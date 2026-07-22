# Git — دليل مبسط

## ليه نحتاج Git؟

Git يسجل تاريخ مشروعك. تقدر:
- ترجع لأي نسخة قديمة
- تشتغل على أكثر من ميزة بنفس الوقت (بدون خرابيط)
- تشارك شغلك مع فريقك

## المفاهيم الأساسية

| المفهوم | يعني ايش |
|---------|-----------|
| **Repository** | مجلد مشروعك + تاريخ كل التغييرات |
| **Commit** | لقطة (snapshot) للملفات في لحظة معينة |
| **Branch** | مسار تطوير منفصل (نسخة جانبية) |
| **Push** | رفع التغييرات للسيرفر (GitHub) |
| **Pull** | تنزيل آخر التحديثات من السيرفر |

## دورة العمل اليومية (أهم شي)

تسوي كل يوم هالخطوات الأربع:

```
1. git add .          ← جهّز كل التغييرات
2. git commit -m "..." ← احفظها مع رسالة
3. git push            ← ارفعها لـ GitHub
4. git pull            ← نزّل تحديثات زملائك
```

## الأوامر المهمة

```bash
# شوف حالة المشروع (ايش تغير؟)
git status

# جهّز ملف معين
git add app.js

# جهّز كل الملفات
git add .

# احفظ مع رسالة
git commit -m "اضفت ميزة تسجيل الدخول"

# ارفع للسيرفر
git push

# نزّل من السيرفر
git pull

# شوف تاريخ الـ commits
git log --oneline
```

## الفروع (Branches)

```bash
# إنشاء فرع جديد
git branch feature-login

# الانتقال للفرع
git checkout feature-login

# اختصار: إنشاء + انتقال
git checkout -b feature-login

# دمج الفرع مع الرئيسي
git checkout main
git merge feature-login
```

## التراجع (Undo)

```bash
# ترجّع ملف لآخر حفظ
git restore app.js

# إلغاء تجهيز ملف (قبل commit)
git restore --staged app.js

# تعديل آخر رسالة commit
git commit --amend -m "رسالة جديدة"
```

## مثال كامل من الواقع

بدل ما تقعد تقرأ، شوف السيناريو:

1. صار عندك فكرة: تبغى تضيف صفحة تقارير
2. تسوي فرع: `git checkout -b reports-page`
3. تكتب الكود
4. تحفظ: `git add .` ثم `git commit -m "صفحة التقارير"`
5. ترجع لـ main: `git checkout main`
6. تدمج: `git merge reports-page`
7. ترفع: `git push`

## الخلاصة

| متى تستخدم | الأمر |
|------------|-------|
| بداية اليوم | `git pull` |
| بعد أي تعديل | `git add .` + `git commit -m "..."` |
| نهاية اليوم | `git push` |
| ميزة جديدة | `git checkout -b اسم-الفرع` |
| حاجة خربت | `git restore اسم-الملف` |

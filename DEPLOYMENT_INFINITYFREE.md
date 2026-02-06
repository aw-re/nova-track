# 🚀 دليل رفع NovaTrack على InfinityFree

## ✅ معلومات الاستضافة الخاصة بك

| البند | القيمة |
|-------|--------|
| **رابط الموقع** | https://novatrack.gt.tc/ |
| **MySQL Hostname** | sql201.infinityfree.com |
| **MySQL Username** | if0_41090915 |
| **MySQL Database** | if0_41090915_cpms |
| **MySQL Port** | 3306 |

---

## 📋 الخطوة 1: تجهيز ملف `.env` للرفع

أنشئ ملف `.env` جديد بهذه الإعدادات:

```env
APP_NAME="NovaTrack"
APP_ENV=production
APP_KEY=base64:YOUR_EXISTING_KEY_HERE
APP_DEBUG=false
APP_URL=https://novatrack.gt.tc

LOG_CHANNEL=single
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=sql201.infinityfree.com
DB_PORT=3306
DB_DATABASE=if0_41090915_cpms
DB_USERNAME=if0_41090915
DB_PASSWORD=YOUR_PASSWORD_HERE

SESSION_DRIVER=file
QUEUE_CONNECTION=sync
CACHE_DRIVER=file

BROADCAST_DRIVER=log
FILESYSTEM_DISK=local
```

> ⚠️ **مهم**: انسخ `APP_KEY` من ملف `.env` المحلي لديك!

---

## 📋 الخطوة 2: تصدير قاعدة البيانات المحلية

### من phpMyAdmin المحلي (XAMPP):
1. افتح http://localhost/phpmyadmin
2. اختر قاعدة البيانات `cpms`
3. اضغط **Export**
4. اختر Format: **SQL**
5. اضغط **Go** لتحميل الملف

### أو من سطر الأوامر:
```bash
mysqldump -u root cpms > novatrack_database.sql
```

---

## 📋 الخطوة 3: رفع قاعدة البيانات على InfinityFree

1. من لوحة التحكم، اضغط على زر **phpMyAdmin** بجانب قاعدة البيانات
2. اختر قاعدة البيانات `if0_41090915_cpms`
3. اذهب إلى تبويب **Import**
4. اختر ملف SQL الذي صدّرته
5. اضغط **Go**

---

## 📋 الخطوة 4: رفع ملفات المشروع عبر FTP

### 4.1 الحصول على معلومات FTP
1. من لوحة التحكم، اذهب إلى **FTP Details**
2. ستجد:
   - FTP Hostname: `ftpupload.net`
   - FTP Username: `if0_41090915`
   - FTP Password: (كلمة المرور الخاصة بك)

### 4.2 استخدام FileZilla
1. حمّل [FileZilla](https://filezilla-project.org/) إن لم يكن لديك
2. اتصل بالخادم:
   - Host: `ftpupload.net`
   - Username: `if0_41090915`
   - Password: كلمة المرور
   - Port: `21`

### 4.3 هيكل الرفع

```
📁 على الخادم (InfinityFree)
├── 📁 htdocs/              ← ارفع محتويات public/ هنا
│   ├── index.php           ← (سنعدله لاحقاً)
│   ├── .htaccess
│   ├── 📁 css/
│   ├── 📁 js/
│   ├── 📁 images/
│   └── 📁 build/
│
├── 📁 app/                 ← ارفع مباشرة في الجذر
├── 📁 bootstrap/
├── 📁 config/
├── 📁 database/
├── 📁 lang/
├── 📁 resources/
├── 📁 routes/
├── 📁 storage/
├── 📁 vendor/
├── .env
├── artisan
└── composer.json
```

### 4.4 طريقة الرفع:
1. **أولاً**: ارفع مجلد `public/` كـ محتويات إلى `/htdocs/`
2. **ثانياً**: ارفع باقي المجلدات (`app`, `bootstrap`, `config`, إلخ) إلى `/`
3. **ثالثاً**: ارفع ملف `.env` المعدّل إلى `/`

---

## 📋 الخطوة 5: تعديل ملف `htdocs/index.php`

بعد رفع الملفات، استخدم **File Manager** في لوحة InfinityFree لتعديل `/htdocs/index.php`:

```php
<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
$app = require_once __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
)->send();

$kernel->terminate($request, $response);
```

---

## 📋 الخطوة 6: إنشاء رابط Storage

أنشئ ملف `/htdocs/storage-link.php`:

```php
<?php
$target = __DIR__ . '/../storage/app/public';
$link = __DIR__ . '/storage';

if (is_link($link)) {
    echo "Link already exists!";
} else {
    if (@symlink($target, $link)) {
        echo "✅ Storage link created successfully!";
    } else {
        // Fallback for hosts that don't support symlinks
        echo "❌ Symlink not supported. Copy files manually.";
    }
}
```

زُر: `https://novatrack.gt.tc/storage-link.php` ثم **احذف الملف**.

---

## 📋 الخطوة 7: التحقق والاختبار

### افتح الموقع:
🔗 https://novatrack.gt.tc/

### بيانات تسجيل الدخول:

| الدور | البريد الإلكتروني | كلمة المرور |
|-------|-------------------|-------------|
| **Admin** | admin@novatrack.com | password |
| **Owner** | owner1@novatrack.com | password |
| **Engineer** | engineer1@novatrack.com | password |
| **Contractor** | contractor1@novatrack.com | password |

---

## ⚠️ ملاحظات مهمة عن InfinityFree

### القيود:
- ⏳ قد يستغرق DNS حتى **72 ساعة** ليعمل عالمياً
- 🚫 لا يوجد SSH - فقط FTP و File Manager
- 📦 حد أقصى **10MB** لكل ملف مرفوع
- 🔒 بعض دوال PHP محظورة (`exec`, `shell_exec`)

### نصائح:
- ✅ ارفع مجلد `vendor/` كاملاً (لا يمكنك تشغيل Composer)
- ✅ تأكد من `APP_DEBUG=false` في الإنتاج
- ✅ لا ترفع مجلد `.git` أو `node_modules`
- ✅ احذف ملفات الاختبار (`tests/`, `phpunit.xml`)

---

## 🔧 حل المشاكل الشائعة

### "500 Internal Server Error"
- ✅ تحقق من ملف `.htaccess`
- ✅ تأكد من المسارات في `index.php`
- ✅ راجع صلاحيات المجلدات

### "Class not found"
- ✅ تأكد من رفع مجلد `vendor/` كاملاً

### "CSRF Token Mismatch"
- ✅ تأكد من `APP_URL=https://novatrack.gt.tc` في `.env`

### قاعدة البيانات لا تعمل
- ✅ تحقق من معلومات الاتصال في `.env`
- ✅ تأكد من استيراد قاعدة البيانات

---

## 📁 قائمة الملفات للرفع

### ✅ ارفع هذه:
```
app/
bootstrap/
config/
database/
lang/
public/ → (محتوياته إلى htdocs/)
resources/
routes/
storage/
vendor/
.env
artisan
composer.json
composer.lock
```

### ❌ لا ترفع هذه:
```
.git/
node_modules/
tests/
.env.example
phpunit.xml
README.md
CHANGELOG.md
```

---

## ✅ الملخص النهائي

1. ✅ صدّر قاعدة البيانات من phpMyAdmin المحلي
2. ✅ استوردها على InfinityFree phpMyAdmin
3. ✅ ارفع الملفات عبر FileZilla
4. ✅ عدّل `htdocs/index.php`
5. ✅ حدّث `.env` بمعلومات قاعدة البيانات
6. ✅ أنشئ storage link
7. ✅ اختبر الموقع

🎉 **موقعك جاهز على:** https://novatrack.gt.tc/

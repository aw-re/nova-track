# 🚀 دليل رفع NovaTrack على InfinityFree

## المتطلبات الأساسية
- حساب على [InfinityFree](https://www.infinityfree.net/)
- برنامج FTP مثل FileZilla
- المشروع محلياً على جهازك

---

## 📋 الخطوة 1: تجهيز المشروع للرفع

### 1.1 تحديث ملف `.env` للإنتاج
قبل الرفع، أنشئ نسخة من `.env` باسم `.env.production` بالإعدادات التالية:

```env
APP_NAME="NovaTrack"
APP_ENV=production
APP_KEY=base64:YOUR_KEY_HERE
APP_DEBUG=false
APP_URL=https://your-subdomain.epizy.com

LOG_CHANNEL=single
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=sql###.epizy.com
DB_PORT=3306
DB_DATABASE=epiz_#######_novatrack
DB_USERNAME=epiz_#######
DB_PASSWORD=your_password_here

SESSION_DRIVER=file
QUEUE_CONNECTION=sync
CACHE_DRIVER=file

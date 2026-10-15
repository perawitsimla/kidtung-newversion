# KidTung — วิธีติดตั้งและรันบน macOS

โปรเจกต์นี้รวม source code Laravel เดิมกับ UI ชุดใหม่ โดย CSS แยกไว้ที่ `public/css/kidtung-ui.css` และถูกเรียกหลัง `public/css/theme.css` จาก `resources/views/layouts/app.blade.php` หน้าเพิ่มและแก้ไขบิลรองรับการหารเท่ากัน การระบุยอดเงินต่อคน และการแบ่งเป็นเปอร์เซ็นต์ โดยใช้ตารางเดิม ไม่ต้องเพิ่ม migration

## ความต้องการของระบบ

- PHP 8.3 ขึ้นไป พร้อมส่วนขยาย SQLite (`pdo_sqlite` และ `sqlite3`)
- Composer
- macOS Terminal หรือ Terminal ใน VS Code

## วิธีง่ายที่สุด

1. แตก ZIP ไปยังโฟลเดอร์ใหม่
2. เปิด Terminal ในโฟลเดอร์ `kidtung-newversion-main`
3. รันคำสั่ง:

```bash
chmod +x start-mac.sh
./start-mac.sh
```

สคริปต์จะสร้าง `.env` จาก `.env.example` หากยังไม่มี, สร้างไฟล์ SQLite, ติดตั้ง Composer dependencies, สร้าง application key, migrate ฐานข้อมูล และเปิดเซิร์ฟเวอร์

เปิดเว็บที่ <http://127.0.0.1:8000>

## ติดตั้งด้วยตนเอง

```bash
cp .env.example .env
touch database/database.sqlite
composer install
php artisan key:generate
php artisan optimize:clear
php artisan migrate
php artisan serve
```

จากนั้นเปิด <http://127.0.0.1:8000>

## หากเคยมีโปรเจกต์เดิมอยู่แล้ว

แพ็กนี้มีฐานข้อมูล SQLite เปล่าสำหรับเริ่มโปรเจกต์ใหม่ อย่านำไฟล์ `database/database.sqlite` นี้ไปแทนฐานข้อมูลเดิมที่มีข้อมูลสำคัญอยู่แล้ว และไม่จำเป็นต้องใช้ `php artisan migrate:fresh`.

## ไฟล์ UI ที่แก้ไข

- `resources/views/layouts/app.blade.php` — layout และแถบเมนูหลัก
- `resources/views/dashboard.blade.php` — ภาพรวมยอดบิล สถานะหนี้ กลุ่ม และรายการโอนล่าสุด
- `resources/views/settlements_global.blade.php` — หน้ารายการหนี้แบบแยกยอดต้องจ่าย/ต้องได้รับ
- `resources/views/profile/edit.blade.php` — โปรไฟล์ ข้อมูลรับเงิน และสถิติบัญชี
- `resources/views/expenses/create.blade.php` และ `edit.blade.php` — ฟอร์มเพิ่ม/แก้บิลพร้อมวิธีแบ่ง 3 แบบ
- `public/js/expense-calculator.js` — คำนวณยอดตามวิธีแบ่งแบบเรียลไทม์
- `public/css/kidtung-ui.css` — stylesheet แยกไฟล์
- `.env.example` — ตั้งค่า SQLite, file cache/session และ sync queue เพื่อให้ตั้งค่าเริ่มต้นได้ง่ายขึ้น

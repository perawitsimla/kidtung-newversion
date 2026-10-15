#!/bin/bash
set -euo pipefail

cd "$(dirname "$0")"

if ! command -v php >/dev/null 2>&1; then
  echo "ไม่พบ PHP กรุณาติดตั้ง PHP 8.3 ขึ้นไปก่อน"
  exit 1
fi

if ! command -v composer >/dev/null 2>&1; then
  echo "ไม่พบ Composer กรุณาติดตั้ง Composer แล้วลองใหม่"
  exit 1
fi

if [ ! -f .env ]; then
  cp .env.example .env
  echo "สร้างไฟล์ .env แล้ว"
fi

mkdir -p database
touch database/database.sqlite

echo "กำลังติดตั้ง PHP dependencies..."
composer install

if grep -q '^APP_KEY=$' .env; then
  echo "กำลังสร้าง APP_KEY..."
  php artisan key:generate
fi

echo "กำลังล้าง cache..."
php artisan optimize:clear

echo "กำลังสร้างตารางฐานข้อมูล..."
php artisan migrate --force

echo "เริ่มเซิร์ฟเวอร์ที่ http://127.0.0.1:8000"
exec php artisan serve

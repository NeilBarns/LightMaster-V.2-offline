# 💡 LightMaster V.2 (Offline Edition)

An offline IoT lighting control system powered by Laravel and WebSockets, designed for billiard halls and similar venues.

---

## 🚀 Local Development Setup

### ✅ Prerequisites

- [PHP 8.1+](https://www.php.net/downloads.php) (recommended via [XAMPP](https://www.apachefriends.org/))
- [Composer](https://getcomposer.org/)
- [Node.js (LTS)](https://nodejs.org/)
- MySQL (e.g., via XAMPP)
- Optional: Git, Postman, phpMyAdmin

---

### 📦 Installation Steps

#### 1. Clone the Repository

```bash
git clone https://github.com/your-org/lightmaster-v2-offline.git
cd lightmaster-v2-offline
```

#### 2. Set Up Environment

```bash
cp .env.example .env
php artisan key:generate
```

Edit your `.env` file to match your local DB setup:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=lightmaster
DB_USERNAME=root
DB_PASSWORD=
```

#### 3. Install PHP Dependencies

If you encounter missing extensions (e.g. `ext-gd`, `ext-zip`):

- Open `php.ini` (usually `C:\xampp\php\php.ini`)
- Uncomment lines like:

  ```ini
  extension=gd
  extension=zip
  ```

- Restart Apache

Then run:

```bash
composer install
```

If you encounter autoload errors:

```bash
# Clean reinstall
rm -rf vendor composer.lock        # Or use PowerShell: Remove-Item -Recurse -Force vendor; Remove-Item composer.lock
composer install
```

#### 4. Run Migrations & Seeders

```bash
php artisan migrate --seed
```

#### 5. Install Frontend Assets

```bash
npm install
npm run dev         # Builds assets for local development
```

If you're preparing for production, use:

```bash
npm run build       # Minifies and optimizes assets
```

---

### ▶️ Start the Development Server

Before running the Laravel server, make sure to build frontend assets.

```bash
php artisan serve
```

Open: [http://127.0.0.1:8000](http://127.0.0.1:8000)

---

### 🔌 Start WebSocket Server (Optional)

If using a custom WebSocket server (e.g. `websocketServer.php` or a `.bat` file), run:

```bash
php websocketServer.php
```

Or schedule `start-websocket.bat` via Windows Task Scheduler.

---

### 🧪 Testing WebSocket + Countdown Logic

Ensure:
- Server time and device time are in sync
- PHP time zone is set correctly in `php.ini`
- WebSocket URL resolves correctly (avoid using mDNS unless supported)

---

## 🛠 Common Issues

| Issue | Fix |
|------|-----|
| `php` not recognized | Add PHP to `PATH` or run via full path: `"C:\xampp\php\php.exe"` |
| `composer` not recognized | Add to `PATH`: `C:\ProgramData\ComposerSetup\bin` |
| Missing extensions | Edit `php.ini` and enable (`extension=gd`, etc) |
| Class map or vendor errors | Run `rm -rf vendor composer.lock && composer install` |

---

## 📂 Directory Structure (Key Files)

```
├── .env.example
├── artisan
├── composer.json
├── public/
├── resources/
├── routes/
├── websocketServer.php (if applicable)
├── start-websocket.bat (if applicable)
└── ...
```

---

## 👷 Maintained by

**Neil Michael Barnedo**  
Senior Software Developer, LightMaster System

---

## 📜 License

This project is proprietary and not open for redistribution unless explicitly permitted.
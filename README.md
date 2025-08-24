# Agrovet Retail Management System (ARMS)

A a web-based system designed to help agrovet (farm input &amp; veterinary supply) retail shops manage their stores in a centralized way. The system reduces manual paperwork, improves accuracy, and provides real-time insights into inventory and financial performance.

## ⚙️ Tech Stack

-   **Backend:** Laravel (PHP Framework)
-   **Database:** MySQL
-   **Frontend:** Blade, TailwindCSS, AlpineJS
-   **Authentication & Roles:** spatie/laravel-permission

## 🚀 Getting Started

Follow these steps to clone and run the project locally.

### 1️⃣ Clone the Repository

```bash
git clone https://github.com/theo-pombe/agrovet-retailer.git
cd agrovet-retail-management
```

### 2️⃣ Install Dependencies

```bash
composer install
npm install && npm run dev
```

### 3️⃣ Configure Environment

```bash
cp .env.example .env
```

### 4️⃣ Generate App Key

```bash
php artisan key:generate
```

### 5️⃣ Run Migrations & Seeders

```bash
php artisan migrate --seed
```

### 6️⃣ Start the Development Server

```bash
php artisan serve
```

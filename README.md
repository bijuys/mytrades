# MyTrades — Modern Trading Journal & Performance Analytics

<p align="center">
  <img src="public/images/logo.png" alt="MyTrades Logo" width="450">
</p>

<p align="center">
  <strong>Track • Analyze • Improve • Grow</strong><br>
  A modern, elegant, and disciplined Trading Journal web application built on Laravel 12 / 13 and PHP 8.3 with Light & Dark theme switching, real-time P&L calculations, automated WebP image compression, and Super Admin review controls.
</p>

---

## 🌟 Key Features

### 1. Front End & Authentication
- **Secure Sign-In**: Login form with email, password show/hide eye toggle, and remember me.
- **Password Recovery**: Complete forgot password email workflow, secure tokens, reset password screen, and dedicated "Thank You!" confirmation page.
- **Trader Registration**:
  - Fields: First Name, Last Name, Email, Password (with eye toggle), Phone Number (Indian 10-digit validation), Trading Experience (years), and Terms of Service agreement.
  - **Super Admin Approval Gate**: All new signups require approval from an administrator before access to the trading journal is granted.

### 2. User Trading Area
- **Interactive Journaling**:
  - Log trades with: Instrument, Direction (LONG/SHORT), Entry Date & Time, Exit Date & Time, Quantity, Entry Price, Exit Price, Stop Loss, Target, Exit Reason, Gross P&L, Charges/Brokerage, Net P&L, Screenshot, Strategy, Lessons, Remarks, and Server timestamp.
  - **Live Auto-Calculator**: Gross P&L, Charges, Net P&L, Planned Risk:Reward ratio, and ROI% compute in real time as values are typed.
  - **Automatic WebP Conversion**: Chart screenshots (PNG/JPG) are automatically converted to optimized `.webp` format via native PHP GD to save server disk space and accelerate page loads.
  - **User-Defined Strategies**: Select from default setups or create/manage custom trading strategies anytime.
- **Performance Summary & Analytics**:
  - High-level KPIs: Total Net P&L, Gross P&L, Win Rate %, Profit Factor, Average Win vs. Loss, Payoff Ratio, and Max Streaks.
  - **Cumulative Equity Curve Chart** tracking account growth across all closed trades.
  - **Monthly Net P&L** comparison bar chart.
  - Breakdown tables and distributions by Strategy, Instrument, Direction (Long vs. Short), and Day of the Week.
- **Theme Support**: Seamless Light Mode and Dark Mode with instant toggle in the navigation bar.

### 3. Super Admin Console
- **Overview Dashboard**: Platform-wide metrics, total traders, active trades, platform volume, and pending approval queue.
- **User Management & Review**:
  - Filter users by status (*Pending, Approved, Rejected, Suspended, Admins*).
  - 1-Click user approval, customized rejection reasons, suspension toggles, and role management.
- **Global Trades Audit**: Review and audit journal records logged across all traders on the platform.

---

## 🚀 Getting Started

### Prerequisites
- PHP 8.2 or 8.3+ with `gd`, `pdo_sqlite` or `pdo_mysql`, `curl`, and `openssl` extensions.
- Composer
- Node.js & NPM (optional, for asset development)

### Installation

1. **Clone the repository**:
   ```bash
   git clone https://github.com/bijuys/mytrades.git
   cd mytrades
   ```

2. **Install Composer dependencies**:
   ```bash
   composer install
   ```

3. **Configure Environment**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Initialize Database & Seed Demo Data**:
   ```bash
   touch database/database.sqlite
   php artisan migrate --seed
   ```

5. **Link Storage for Chart Screenshots**:
   ```bash
   php artisan storage:link
   ```

6. **Start the Application**:
   ```bash
   php artisan serve
   ```
   Visit `http://127.0.0.1:8000` in your web browser.

---

## 🔑 Preloaded Demo Accounts

| Role | Email | Password | Description |
| :--- | :--- | :--- | :--- |
| **Super Admin** | `admin@mytrades.com` | `password123` | Full access to Admin Console, user approvals & trade audits |
| **Active Trader** | `trader@mytrades.com` | `password123` | Pre-approved trader loaded with realistic trading journal entries & analytics |
| **Pending Trader** | `amit.patel@example.com` | `password123` | Pending signup ready to test Super Admin approval workflow |

---

## 🧪 Testing

The application includes an extensive PHPUnit feature test suite covering authentication, user approvals, trade logging, live P&L computation, WebP image conversion, and analytics:

```bash
php artisan test
```

---

## 📄 License

This project is open-source software licensed under the [MIT license](LICENSE).

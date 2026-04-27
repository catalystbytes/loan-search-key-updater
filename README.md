# 🚀 Loan Search Key Updater (Production-Ready)

## 📌 Overview

This project provides a **production-ready PHP cron job** that updates the `search_key` field in the `dim_loan` table by combining user information from the `dim_user` table.

The script is optimized for:

* ⚡ High performance (single SQL execution)
* 🔒 Security (environment-based configuration)
* 📊 Observability (logging + execution metrics)
* 🔁 Automation (cron job ready)

---

## 🧠 How It Works

Instead of generating multiple UPDATE statements, this solution uses a **single optimized SQL JOIN update**:

* Joins `dim_loan` with `dim_user`
* Concatenates:

  * First Name
  * Last Name
  * Email
  * Mobile
* Updates only records where `search_key` is empty or NULL

---

## ⚙️ Tech Stack

* PHP (7.4+ recommended)
* MySQL / MariaDB
* PDO Extension
* Cron (Linux / Unix-based systems)

---

## 📁 Project Structure

```
loan-search-key-updater/
│
├── src/
│   └── UpdateSearchKeyCommand.php   # Core logic
│
├── scripts/
│   └── run-update-search-key.php    # Entry point for cron
│
├── config/
│   └── .env.example                # Environment template
│
├── logs/
│   └── search-key.log             # Execution logs
│
├── composer.json
└── README.md
```

---

## 🔐 Environment Configuration

Create a `.env` file inside `/config`:

```
DB_HOST=127.0.0.1
DB_NAME=your_database
DB_USER=your_user
DB_PASS=your_password
```

> ⚠️ Never commit `.env` to version control.

---

## 🧾 Core SQL Logic

```sql
UPDATE dim_loan loan
INNER JOIN dim_user user ON loan.created_by = user.id
SET loan.search_key = CONCAT_WS(' ',
    user.first_name,
    user.last_name,
    user.email,
    user.mobile
)
WHERE loan.search_key IS NULL OR loan.search_key = '';
```

---

## ▶️ Running the Script Manually

```bash
php scripts/run-update-search-key.php
```

---

## ⏰ Cron Job Setup

Edit crontab:

```bash
crontab -e
```

Add:

```bash
0 0 * * * /usr/bin/php /var/www/loan-search-key-updater/scripts/run-update-search-key.php >> /var/www/loan-search-key-updater/logs/search-key.log 2>&1
```

### 🔍 Cron Explanation:

* `0 0 * * *` → Runs daily at midnight
* Logs output to `logs/search-key.log`
* Errors are captured for debugging

---

## 📊 Logging & Monitoring

Each run outputs:

* ✅ Number of rows updated
* ⏱ Execution time
* ❌ Errors (if any)

Example log:

```
[SUCCESS] Updated 1245 rows
[INFO] Execution time: 0.42s
```

---

## ⚡ Performance Considerations

### Recommended Index:

```sql
CREATE INDEX idx_search_key ON dim_loan(search_key);
```

### For Large Datasets:

* Add batching:

```sql
LIMIT 10000
```

* Run cron more frequently (e.g., every 5 minutes)

---

## 🔒 Security Best Practices

* Use environment variables
* Restrict DB user permissions (UPDATE only if possible)
* Avoid exposing logs publicly
* Do not echo sensitive data

---

## 🚨 Error Handling

* Uses database transactions
* Automatically rolls back on failure
* Logs errors via `error_log`

---

## 🧪 Testing Checklist

Before deploying to production:

* [ ] Test on staging database
* [ ] Verify affected rows count
* [ ] Check log output
* [ ] Validate cron execution
* [ ] Simulate failure (wrong DB credentials)

---

## 🚀 Future Improvements

You can evolve this into:

* Laravel Artisan Command
* Dockerized scheduled job
* AWS Lambda + EventBridge scheduler
* Airflow DAG for ETL pipelines
* Real-time trigger via DB events

---

## 👨‍💻 Author

Built for scalable data operations and automation workflows.

---

## 📄 License

MIT License (or your preferred license)

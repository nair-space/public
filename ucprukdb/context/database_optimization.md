
---

## 1. Database Schema & Indexing

The most significant performance gains happen at the storage layer.

* **Primary Keys:** Ensure every table has an `UNSIGNED BIGINT` auto-incrementing primary key.
* **Indexing Strategy:** * Add indexes to columns used in `WHERE`, `ORDER BY`, or `JOIN` clauses (e.g., `client_id`, `email`, `created_at`).
* Use **Composite Indexes** if you frequently filter by multiple columns (e.g., `status` + `region`).


* **Column Types:** Use the smallest data type possible. Use `TINYINT` for booleans/statuses and `VARCHAR(255)` only where necessary.
* **Charset:** Use `utf8mb4_unicode_ci` for full emoji and special character support without sacrificing much speed.

---

## 2. Query Optimization (Eloquent/Query Builder)

Laravel makes it easy to write slow queries. Here is how to keep them fast:

* **Eager Loading (The N+1 Fix):** Never loop through clients and then call a relationship inside the loop.
* *Bad:* `Client::all()`
* *Good:* `Client::with(['interactions', 'notes'])->get()`


* **Select Only What You Need:** Don't use `SELECT *`.
* `Client::select('id', 'name', 'status')->get();`


* **Pagination:** Never use `->get()` for 20,000 rows. Use `->paginate(50)` or `->simplePaginate(50)`.
* **Chunking for Processing:** If you need to run a script over all 20,000 rows, use `chunk()` or `cursor()` to keep memory usage low.

---

## 3. Server & MariaDB Configuration

MariaDB needs enough "breathing room" in your RAM to store indexes.

* **InnoDB Buffer Pool Size:** This is the most important setting. Set `innodb_buffer_pool_size` to roughly 70-80% of your available system RAM if the server is dedicated to the DB.
* **Query Caching:** While deprecated in some versions, ensuring your high-traffic queries are optimized allows MariaDB’s internal optimizer to work efficiently.
* **Slow Query Log:** Enable this in `my.cnf` to identify queries taking longer than 1 second.

---

## 4. Laravel-Specific Caching

Reduce the number of hits to MariaDB.

* **Remember Cache:** Use `Cache::remember()` for data that doesn't change often (like settings or dropdown lists).
* **Route & Config Caching:** Run `php artisan optimize` in production to speed up the framework's internal lookups.
* **Database Partitioning:** (Optional for 20k, but good for growth) If you expect to hit 1 million+ rows, look into horizontal partitioning by date.

---

## 5. Security Performance Check

* **Atomic Updates:** Use `DB::transaction` when updating multiple tables to prevent data corruption.
* **Prepared Statements:** Laravel does this by default, but avoid `DB::raw()` with unsanitized user input to prevent SQL injection.

---


To ensure the UCPRUK database is secure, the following measures are mandatory:


A. Database Security (MariaDB)

-SQL Injection Prevention: Use Laravel’s Eloquent ORM or Query Builder exclusively (parameterized queries).
-Encryption at Rest: Sensitive client data (e.g., PII) should be encrypted using Laravel’s Crypt facade.
-Least Privilege: The MariaDB user should only have SELECT, INSERT, UPDATE, and DELETE permissions on the project database.

B. Application Security
-CSRF Protection: Ensure the @csrf directive or Inertia’s automated CSRF handling is active.
-XSS Mitigation: Leverage Svelte’s auto-escaping and use Laravel’s e() helper for any raw output.
-Authentication: Implement Laravel Breeze or Fortify for secure login, using Argon2id for password hashing.
-Rate Limiting: Apply throttle middleware to all API routes and login attempts to prevent Brute Force.

C. Request Validation
-Form Requests: Use dedicated FormRequest classes for all input.
-Strict Typing: Ensure TypeScript interfaces match Laravel's JSON resources to prevent data leakage.
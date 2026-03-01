# PHP-001: Script Execution Model (vs Java)
#php #basics #java-comparison

In Java, you have a persistent JVM. When you start an application, it stays in memory.

In PHP, every request is **stateless** and **independent**:
1.  **Request arrives:** The server (e.g., Apache/Nginx) starts the PHP interpreter.
2.  **Execution:** PHP runs the script from top to bottom.
3.  **Finish:** Once the script reaches the end or `die()`/`exit()`, the entire process is terminated, and memory is freed.

## Key Differences:
- **No `main` method:** Execution starts at the first line of the file.
- **Short-lived:** No long-running background tasks (in standard web usage).
- **Stateless:** Variables defined in one request don't exist in the next. You need sessions, databases, or cookies for persistence.

## From your code:
```php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // This code only runs IF the request is POST.
}
// After this, the script finishes and all variables ($data, $file, etc.) are destroyed.
```

See also: [[php-002-superglobals]]

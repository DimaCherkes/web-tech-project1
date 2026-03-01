# PHP-002: Superglobals ($_SERVER, $_POST, $_FILES)
#php #web #superglobals

PHP has built-in arrays that are available everywhere. They are like `HttpServletRequest` attributes in Java Servlets, but simpler.

- **`$_SERVER`**: Information about the request and environment (method, URI, headers).
- **`$_POST`**: Form data from a POST request.
- **`$_FILES`**: Information about uploaded files.

## In your code:
```php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csv_file'])) {
    // 1. Check if the user submitted a form via POST
    // 2. Check if the 'csv_file' input exists in the upload array
}
```

### `isset()`
In Java, you'd check `if (request.getParameter("key") != null)`. In PHP, `isset()` checks if a key exists in an array and is not null.

### File Uploads (`$_FILES`)
Unlike Java, where you might need a library like Apache Commons FileUpload, PHP natively populates `$_FILES`.
- `$_FILES['csv_file']['tmp_name']`: Path to the temporary file on the server.
- `$_FILES['csv_file']['name']`: Original file name (untrusted).
- `$_FILES['csv_file']['error']`: 0 means success.

See also: [[php-001-script-execution]], [[php-004-arrays]]

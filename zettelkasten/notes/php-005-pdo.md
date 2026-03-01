# PHP-005: PDO (PHP Data Objects)
#php #database #pdo #java-comparison

PDO is the standard way to connect to databases in PHP. It is very similar to **JDBC** in Java.

## Analogy to JDBC:
- **`PDO` Class**: Like `DriverManager.getConnection()`.
- **`PDOStatement`**: Like `PreparedStatement`.
- **`$stmt->execute()`**: Like `preparedStatement.executeUpdate()` or `executeQuery()`.
- **`$stmt->fetch()`**: Like `resultSet.next()`.

## In your code:
```php
$sql = "INSERT INTO countries (name, code) VALUES (:name, :code)";
$stmt = $pdo->prepare($sql);

$stmt->execute([
    ':name' => $name,
    ':code' => $code
]);
```

### Key Differences:
1. **Named Parameters**: In PHP, you use `:name` instead of just `?`. It's much cleaner!
2. **Associative Arrays**: You can pass an array directly to `execute()`. In Java, you'd have many `stmt.setString(1, ...)` calls.
3. **No Checked Exceptions**: PHP's PDO throws `PDOException` (similar to `SQLException`), but you don't *have* to catch it (though you should).

### `lastInsertId()`
This is a common method in PHP to get the auto-increment ID of the row you just inserted.

See also: [[php-004-arrays]], [[php-009-error-handling]]

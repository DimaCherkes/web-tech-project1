# PHP-003: Type Hinting & Weak Typing
#php #basics #typing #java-comparison

Java is **Statically Typed** (you specify types at compile time). 
PHP is **Dynamically Typed**, but it has **Type Hinting** (added in newer versions).

## In your code:
```php
function insertCountry(PDO $pdo, string $name, ?string $code = null): int {
    // ...
}
```

### Breakdown:
1.  `PDO $pdo`: The first argument MUST be an instance of the `PDO` class. (Like Java parameters).
2.  `string $name`: Must be a string.
3.  `?string $code = null`: The `?` means it's **nullable** (like `@Nullable` or `Optional` in Java, but built into the type).
4.  `: int`: The return type is an integer.

## Weak Typing (The "Danger" Zone):
In Java, `1 + "2"` is a compiler error.
In PHP, `1 + "2"` results in `3` (integer). PHP tries to be "helpful" by converting types on the fly. This is why strict type hinting is important in modern PHP.

### `strict_types=1`
You might see `declare(strict_types=1);` at the top of some files. This makes PHP behave more like Java (throws an error if types don't match exactly).

See also: [[php-001-script-execution]], [[php-004-arrays]]

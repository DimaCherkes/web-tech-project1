# PHP-004: Arrays as HashMaps
#php #data-structures #arrays

In PHP, arrays are not just simple fixed-size buffers like in C, nor simple `List<T>` like in Java. 

An array in PHP is an **ordered map**. It acts like both a `List` and a `HashMap` simultaneously.

## Two Faces of Arrays:
1. **Indexed Array (like `ArrayList`):**
   ```php
   $fruits = ["Apple", "Banana"]; // indices 0, 1
   ```
2. **Associative Array (like `HashMap`):**
   ```php
   $user = ["id" => 1, "name" => "Dmitry"];
   ```

## From your code:
```php
$data = parseCsvToAssocArray($file['tmp_name'], ";");
```
This function returns an array of associative arrays. In Java, this would be:
`List<Map<String, String>>`.

### `array_combine($headers, $row)`
This creates an associative array where `$headers` (keys) are mapped to `$row` (values).

### `count($row)`
Like `row.length` or `list.size()`.

See also: [[php-002-superglobals]], [[php-006-file-handling]]

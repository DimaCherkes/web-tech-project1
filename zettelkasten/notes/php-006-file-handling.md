# PHP-006: File Handling (fgetcsv)
#php #files #csv

Handling files in PHP is lower-level than modern Java Streams, but very fast.

- `fopen($filePath, 'r')`: Opens a file handle for reading.
- `fgetcsv($handle, 0, $delimiter)`: Reads one line from the handle and automatically parses it into an array based on the delimiter.

## In your code:
```php
function parseCsvToAssocArray(string $filePath, string $delimiter = ","): array
{
    $result = [];
    $handle = fopen($filePath, 'r');
    
    // 1. Read the first line as headers
    $headers = fgetcsv($handle, 0, $delimiter); 
    
    // 2. Loop through the rest of the lines
    while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
        if (count($row) === count($headers)) {
            // 3. Combine headers with values into a Map
            $result[] = array_combine($headers, $row);
        }
    }
    fclose($handle);
    return $result;
}
```

### Key Differences:
1. **Handle-based:** You must always `fclose()` the file (PHP doesn't have Java's try-with-resources, though modern PHP uses objects for this sometimes).
2. **Procedural:** Most built-in file functions start with `f...` (`fopen`, `fread`, `fwrite`).

See also: [[php-004-arrays]]

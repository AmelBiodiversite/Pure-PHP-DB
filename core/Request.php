<?php
namespace Core;

/**
 * Request Validation Class
 * Provides secure input validation and sanitization
 */
class Request {
    /**
     * Validate and sanitize a string input
     * 
     * @param string|null $input Input to validate
     * @param int $minLength Minimum length (default: 0)
     * @param int $maxLength Maximum length (default: 10000)
     * @return string|null Sanitized string or null if invalid
     */
    public static function sanitizeString($input, int $minLength = 0, int $maxLength = 10000): ?string {
        if ($input === null || $input === '') {
            return $minLength > 0 ? null : '';
        }
        
        $sanitized = trim(strip_tags($input));
        $length = mb_strlen($sanitized);
        
        if ($length < $minLength || $length > $maxLength) {
            return null;
        }
        
        return $sanitized;
    }
    
    /**
     * Validate and sanitize an integer input
     * 
     * @param mixed $input Input to validate
     * @param int|null $min Minimum value (null for no minimum)
     * @param int|null $max Maximum value (null for no maximum)
     * @return int|null Validated integer or null if invalid
     */
    public static function sanitizeInt($input, ?int $min = null, ?int $max = null): ?int {
        if ($input === null || $input === '') {
            return null;
        }
        
        // Filter as integer
        $value = filter_var($input, FILTER_VALIDATE_INT);
        
        if ($value === false) {
            return null;
        }
        
        // Check min/max bounds
        if ($min !== null && $value < $min) {
            return null;
        }
        
        if ($max !== null && $value > $max) {
            return null;
        }
        
        return $value;
    }
    
    /**
     * Validate and sanitize a float input
     * 
     * @param mixed $input Input to validate
     * @param float|null $min Minimum value
     * @param float|null $max Maximum value
     * @return float|null Validated float or null if invalid
     */
    public static function sanitizeFloat($input, ?float $min = null, ?float $max = null): ?float {
        if ($input === null || $input === '') {
            return null;
        }
        
        $value = filter_var($input, FILTER_VALIDATE_FLOAT);
        
        if ($value === false) {
            return null;
        }
        
        if ($min !== null && $value < $min) {
            return null;
        }
        
        if ($max !== null && $value > $max) {
            return null;
        }
        
        return $value;
    }
    
    /**
     * Validate an email address
     * 
     * @param string|null $input Email to validate
     * @return string|null Validated email or null if invalid
     */
    public static function sanitizeEmail($input): ?string {
        if ($input === null || $input === '') {
            return null;
        }
        
        $email = filter_var(trim($input), FILTER_VALIDATE_EMAIL);
        
        return $email !== false ? $email : null;
    }
    
    /**
     * Validate a URL
     * 
     * @param string|null $input URL to validate
     * @return string|null Validated URL or null if invalid
     */
    public static function sanitizeUrl($input): ?string {
        if ($input === null || $input === '') {
            return null;
        }
        
        $url = filter_var(trim($input), FILTER_VALIDATE_URL);
        
        return $url !== false ? $url : null;
    }
    
    /**
     * Get a validated integer from GET parameters
     * 
     * @param string $key Parameter key
     * @param int $default Default value if not set or invalid
     * @param int $min Minimum value (default: 1)
     * @param int|null $max Maximum value (default: null)
     * @return int
     */
    public static function getInt(string $key, int $default = 1, int $min = 1, ?int $max = null): int {
        if (!isset($_GET[$key])) {
            return $default;
        }
        
        $value = self::sanitizeInt($_GET[$key], $min, $max);
        
        return $value !== null ? $value : $default;
    }
    
    /**
     * Get a validated string from GET parameters
     * 
     * @param string $key Parameter key
     * @param string $default Default value
     * @param int $maxLength Maximum length
     * @return string
     */
    public static function getString(string $key, string $default = '', int $maxLength = 1000): string {
        if (!isset($_GET[$key])) {
            return $default;
        }
        
        $value = self::sanitizeString($_GET[$key], 0, $maxLength);
        
        return $value !== null ? $value : $default;
    }
    
    /**
     * Get a validated integer from POST parameters
     * 
     * @param string $key Parameter key
     * @param int|null $default Default value
     * @param int|null $min Minimum value
     * @param int|null $max Maximum value
     * @return int|null
     */
    public static function postInt(string $key, ?int $default = null, ?int $min = null, ?int $max = null): ?int {
        if (!isset($_POST[$key])) {
            return $default;
        }
        
        return self::sanitizeInt($_POST[$key], $min, $max) ?? $default;
    }
    
    /**
     * Get a validated string from POST parameters
     * 
     * @param string $key Parameter key
     * @param string|null $default Default value
     * @param int $minLength Minimum length
     * @param int $maxLength Maximum length
     * @return string|null
     */
    public static function postString(string $key, ?string $default = null, int $minLength = 0, int $maxLength = 10000): ?string {
        if (!isset($_POST[$key])) {
            return $default;
        }
        
        return self::sanitizeString($_POST[$key], $minLength, $maxLength) ?? $default;
    }
    
    /**
     * Validate pagination parameters
     * 
     * @param int $maxPerPage Maximum items per page (default: 100)
     * @return array ['page' => int, 'limit' => int, 'offset' => int]
     */
    public static function getPagination(int $maxPerPage = 100): array {
        $page = self::getInt('page', 1, 1, 10000);
        $perPage = self::getInt('per_page', 12, 1, $maxPerPage);
        
        return [
            'page' => $page,
            'limit' => $perPage,
            'offset' => ($page - 1) * $perPage
        ];
    }
    
    /**
     * Validate file upload
     * 
     * @param string $fieldName File input field name
     * @param array $allowedMimeTypes Allowed MIME types
     * @param int $maxSize Maximum file size in bytes (default: 5MB)
     * @return array|null File info or null if invalid ['name', 'tmp_name', 'size', 'type']
     */
    public static function validateFile(string $fieldName, array $allowedMimeTypes, int $maxSize = 5242880): ?array {
        if (!isset($_FILES[$fieldName]) || $_FILES[$fieldName]['error'] !== UPLOAD_ERR_OK) {
            return null;
        }
        
        $file = $_FILES[$fieldName];
        
        // Check file size
        if ($file['size'] > $maxSize) {
            return null;
        }
        
        // Verify MIME type using finfo
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($mimeType, $allowedMimeTypes, true)) {
            return null;
        }
        
        return [
            'name' => $file['name'],
            'tmp_name' => $file['tmp_name'],
            'size' => $file['size'],
            'type' => $mimeType
        ];
    }
}

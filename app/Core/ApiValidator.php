<?php

namespace App\Core;

trait ApiValidator
{
    protected function validateRequest(Request $request, array $rules): array
    {
        $data = $request->isJson() ? $request->json() : $request->all();
        
        // Sanitize input data
        $data = $this->sanitizeInput($data);
        
        $validator = new Validator($data);
        
        if (!$validator->validate($rules)) {
            $this->validationFailed($validator);
        }
        
        return $data;
    }
    
    protected function sanitizeInput(array $data): array
    {
        $sanitized = [];
        
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $sanitized[$key] = $this->sanitizeInput($value);
            } elseif (is_string($value)) {
                // Remove null bytes
                $value = str_replace("\0", '', $value);
                
                // Trim whitespace
                $value = trim($value);
                
                // Remove control characters except newlines and tabs
                $value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $value);
                
                $sanitized[$key] = $value;
            } else {
                $sanitized[$key] = $value;
            }
        }
        
        return $sanitized;
    }
    
    protected function validationFailed(Validator $validator): void
    {
        $response = $this->json([
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $validator->errors(),
        ], 422);
        
        $response->send();
        exit;
    }
    
    protected function validateId(string $id): int
    {
        if (!is_numeric($id) || (int)$id <= 0) {
            $response = $this->json([
                'success' => false,
                'message' => 'Invalid ID provided',
            ], 400);
            
            $response->send();
            exit;
        }
        
        return (int)$id;
    }
    
    protected function validateQueryParam(Request $request, string $param, string $type = 'integer', $default = null, ?int $min = null, ?int $max = null)
    {
        $value = $request->get($param, $default);
        
        if ($value === null || $value === '') {
            return $default;
        }
        
        switch ($type) {
            case 'integer':
                if (!is_numeric($value) || (int)$value != $value) {
                    $this->invalidQueryParam($param, 'must be an integer');
                }
                
                $value = (int)$value;
                
                if ($min !== null && $value < $min) {
                    $this->invalidQueryParam($param, "must be at least {$min}");
                }
                
                if ($max !== null && $value > $max) {
                    $this->invalidQueryParam($param, "must not exceed {$max}");
                }
                
                return $value;
                
            case 'string':
                $value = (string)$value;
                
                if ($min !== null && strlen($value) < $min) {
                    $this->invalidQueryParam($param, "must be at least {$min} characters");
                }
                
                if ($max !== null && strlen($value) > $max) {
                    $this->invalidQueryParam($param, "must not exceed {$max} characters");
                }
                
                return $value;
                
            case 'boolean':
                return filter_var($value, FILTER_VALIDATE_BOOLEAN);
                
            default:
                return $value;
        }
    }
    
    protected function invalidQueryParam(string $param, string $message): void
    {
        $response = $this->json([
            'success' => false,
            'message' => "Invalid query parameter: {$param} {$message}",
        ], 400);
        
        $response->send();
        exit;
    }
    
    protected function preventSqlInjection(string $value): bool
    {
        // Check for common SQL injection patterns
        $sqlPatterns = [
            '/(\bUNION\b.*\bSELECT\b)/i',
            '/(\bSELECT\b.*\bFROM\b)/i',
            '/(\bINSERT\b.*\bINTO\b)/i',
            '/(\bUPDATE\b.*\bSET\b)/i',
            '/(\bDELETE\b.*\bFROM\b)/i',
            '/(\bDROP\b.*\bTABLE\b)/i',
            '/(\bEXEC\b|\bEXECUTE\b)/i',
            '/(\bSCRIPT\b.*\>)/i',
            '/(--|#|\/\*|\*\/)/i',
            '/(\bOR\b.*=.*)/i',
            '/(\bAND\b.*=.*)/i',
            '/(\'.*\bOR\b.*\'.*=.*\')/i',
        ];
        
        foreach ($sqlPatterns as $pattern) {
            if (preg_match($pattern, $value)) {
                return false;
            }
        }
        
        return true;
    }
    
    protected function preventXss(string $value): string
    {
        // Remove any HTML/JavaScript
        $value = strip_tags($value);
        
        // Remove javascript: protocol
        $value = preg_replace('/javascript:/i', '', $value);
        
        // Encode special characters
        $value = htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        
        return $value;
    }
}

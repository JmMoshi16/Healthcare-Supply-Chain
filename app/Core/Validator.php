<?php

namespace App\Core;

class Validator
{
    private array $errors = [];
    private array $data;
    
    public function __construct(array $data)
    {
        $this->data = $data;
    }
    
    public function validate(array $rules): bool
    {
        foreach ($rules as $field => $ruleSet) {
            $rulesArray = explode('|', $ruleSet);
            
            foreach ($rulesArray as $rule) {
                $this->applyRule($field, $rule);
            }
        }
        
        return empty($this->errors);
    }
    
    private function applyRule(string $field, string $rule): void
    {
        $value = $this->data[$field] ?? null;
        
        if (str_contains($rule, ':')) {
            [$ruleName, $parameter] = explode(':', $rule, 2);
        } else {
            $ruleName = $rule;
            $parameter = null;
        }
        
        switch ($ruleName) {
            case 'required':
                if (empty($value) && $value !== '0') {
                    $this->errors[$field][] = ucfirst($field) . ' is required';
                }
                break;
                
            case 'email':
                if ($value && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->errors[$field][] = ucfirst($field) . ' must be a valid email';
                }
                break;
                
            case 'min':
                if ($value && strlen($value) < (int)$parameter) {
                    $this->errors[$field][] = ucfirst($field) . " must be at least {$parameter} characters";
                }
                break;
                
            case 'max':
                if ($value && strlen($value) > (int)$parameter) {
                    $this->errors[$field][] = ucfirst($field) . " must not exceed {$parameter} characters";
                }
                break;
                
            case 'numeric':
                if ($value && !is_numeric($value)) {
                    $this->errors[$field][] = ucfirst($field) . ' must be numeric';
                }
                break;
                
            case 'date':
                if ($value && !strtotime($value)) {
                    $this->errors[$field][] = ucfirst($field) . ' must be a valid date';
                }
                break;
                
            case 'unique':
                [$table, $column] = explode(',', $parameter);
                $exists = (new QueryBuilder($table))->where($column, $value)->first();
                if ($exists) {
                    $this->errors[$field][] = ucfirst($field) . ' already exists';
                }
                break;
                
            case 'confirmed':
                $confirmField = $field . '_confirmation';
                if ($value !== ($this->data[$confirmField] ?? null)) {
                    $this->errors[$field][] = ucfirst($field) . ' confirmation does not match';
                }
                break;
                
            case 'in':
                $allowedValues = explode(',', $parameter);
                if ($value && !in_array($value, $allowedValues, true)) {
                    $this->errors[$field][] = ucfirst($field) . ' must be one of: ' . implode(', ', $allowedValues);
                }
                break;
                
            case 'integer':
                if ($value !== null && $value !== '' && !filter_var($value, FILTER_VALIDATE_INT)) {
                    $this->errors[$field][] = ucfirst($field) . ' must be an integer';
                }
                break;
                
            case 'positive':
                if ($value !== null && $value !== '' && (!is_numeric($value) || $value <= 0)) {
                    $this->errors[$field][] = ucfirst($field) . ' must be a positive number';
                }
                break;
                
            case 'alpha':
                if ($value && !preg_match('/^[a-zA-Z]+$/', $value)) {
                    $this->errors[$field][] = ucfirst($field) . ' must contain only letters';
                }
                break;
                
            case 'alpha_num':
                if ($value && !preg_match('/^[a-zA-Z0-9]+$/', $value)) {
                    $this->errors[$field][] = ucfirst($field) . ' must contain only letters and numbers';
                }
                break;
                
            case 'alpha_dash':
                if ($value && !preg_match('/^[a-zA-Z0-9_-]+$/', $value)) {
                    $this->errors[$field][] = ucfirst($field) . ' must contain only letters, numbers, dashes and underscores';
                }
                break;
                
            case 'url':
                if ($value && !filter_var($value, FILTER_VALIDATE_URL)) {
                    $this->errors[$field][] = ucfirst($field) . ' must be a valid URL';
                }
                break;
                
            case 'ip':
                if ($value && !filter_var($value, FILTER_VALIDATE_IP)) {
                    $this->errors[$field][] = ucfirst($field) . ' must be a valid IP address';
                }
                break;
                
            case 'regex':
                if ($value && !preg_match($parameter, $value)) {
                    $this->errors[$field][] = ucfirst($field) . ' format is invalid';
                }
                break;
                
            case 'between':
                [$min, $max] = explode(',', $parameter);
                $length = strlen($value);
                if ($value && ($length < (int)$min || $length > (int)$max)) {
                    $this->errors[$field][] = ucfirst($field) . " must be between {$min} and {$max} characters";
                }
                break;
                
            case 'array':
                if ($value !== null && !is_array($value)) {
                    $this->errors[$field][] = ucfirst($field) . ' must be an array';
                }
                break;
                
            case 'boolean':
                if ($value !== null && !is_bool($value) && !in_array($value, [0, 1, '0', '1', true, false], true)) {
                    $this->errors[$field][] = ucfirst($field) . ' must be a boolean';
                }
                break;
        }
    }
    
    public function errors(): array
    {
        return $this->errors;
    }
    
    public function firstError(string $field): ?string
    {
        return $this->errors[$field][0] ?? null;
    }
    
    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }
    
    public function getFirstErrorMessage(): string
    {
        if (empty($this->errors)) {
            return '';
        }
        
        $firstField = array_key_first($this->errors);
        return $this->errors[$firstField][0] ?? '';
    }
    
    public function getAllErrorMessages(): array
    {
        $messages = [];
        foreach ($this->errors as $field => $fieldErrors) {
            foreach ($fieldErrors as $error) {
                $messages[] = $error;
            }
        }
        return $messages;
    }
}

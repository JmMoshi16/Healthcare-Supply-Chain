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
}

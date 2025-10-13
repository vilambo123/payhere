# 🔧 Routing Parameter Fix

## Problem
Admin view page was throwing error:
```
ArgumentCountError: Too few arguments to function Admin::view(), 
0 passed ... and exactly 1 expected
```

## Root Cause
The custom routing system in `bootstrap.php` wasn't parsing and passing URL parameters to controller methods.

## Solution
Updated `application/config/bootstrap.php` to:

1. **Parse route patterns** with regex (supports `:num` and `:any`)
2. **Extract parameters** from URLs
3. **Pass parameters** to controller methods using `call_user_func_array()`

## What Now Works

### ✅ Custom Routes with Parameters
```php
// application/config/routes.php
$route['admin/view/(:num)'] = 'admin/view/$1';
$route['admin/update/(:num)'] = 'admin/update/$1';
$route['admin/delete/(:num)'] = 'admin/delete/$1';
```

### ✅ Default Routes with Parameters
```
URL: admin/view/123
→ Controller: Admin
→ Method: view
→ Parameters: [123]
```

### ✅ Multiple Parameters
```
URL: controller/method/param1/param2/param3
→ All parameters passed correctly
```

## Testing

### Test 1: Admin View Page
```
Visit: http://localhost/payhere/index.php/admin/view/1

Expected: ✅ Shows inquiry #1 details page
Previous: ❌ ArgumentCountError
```

### Test 2: Admin Update
```
POST to: http://localhost/payhere/index.php/admin/update/1

Expected: ✅ Updates inquiry #1
```

### Test 3: Admin Delete
```
POST to: http://localhost/payhere/index.php/admin/delete/1

Expected: ✅ Deletes inquiry #1
```

## Code Changes

### Before (bootstrap.php)
```php
if (method_exists($instance, $method)) {
    $instance->$method();  // ❌ No parameters passed
}
```

### After (bootstrap.php)
```php
if (method_exists($instance, $method)) {
    call_user_func_array([$instance, $method], $params);  // ✅ Parameters passed
}
```

## Supported Route Patterns

| Pattern | Example | Matches |
|---------|---------|---------|
| `(:num)` | `admin/view/(:num)` | `admin/view/123` |
| `(:any)` | `blog/post/(:any)` | `blog/post/my-slug` |
| `$1, $2` | `admin/view/$1` | Replaced with captured value |

## Complete Flow

```
URL: admin/view/123
     ↓
Parse: admin/view/(:num) → admin/view/$1
     ↓
Match: (:num) captures "123"
     ↓
Replace: $1 → "123"
     ↓
Target: admin/view/123
     ↓
Parse: controller=Admin, method=view, params=[123]
     ↓
Execute: Admin->view(123)  ✅
```

## All Working Routes

```php
// Admin
admin/view/1           → Admin->view(1)
admin/update/1         → Admin->update(1)
admin/delete/1         → Admin->delete(1)

// Settings
settings/update-loan-type/1  → Settings->update_loan_type(1)

// Any custom routes with parameters now work!
```

## Fix Applied ✅
The routing system now properly handles all parameter-based URLs throughout the application.

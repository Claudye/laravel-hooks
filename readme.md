# Laravel Hooks
 
[![License](https://img.shields.io/github/license/claudye/laravel-hooks.svg)](https://github.com/claudye/laravel-hooks/blob/main/LICENSE)

`Laravel Hooks` is a Laravel trait that allows you to register hooks (callbacks) before and after calling specified methods in a Laravel controllers. This helps you modify or extend the behavior of methods without altering their core logic.

## 1. Features and Usage

### Key Features:
- **Before and After Hooks**: Easily register callbacks that are executed before or after specific controller (action) methods.
- **Non-intrusive Method Modification**: Extend or modify method behaviors without touching the core code.
- **Automatic Hook Setup**: Use hooks automatically by leveraging a predefined `useHooks` method.

### Use Cases:
- **Logging**: Automatically log actions before or after certain methods are executed.
- **Data Validation**: Add complex validation logic before the execution of critical methods.
- **Response Transformation**: Modify or format a method’s return value after it’s called.
- **Controller Instance Modification**: Modify the controller instance before a method is executed, useful when combining with dependency injection.
- **Cross-cutting Concerns**: Apply logic across multiple methods like auditing or authorization without duplicating code.

## 2. Installation

You can install this package via Composer. Make sure your Laravel version is **7.x or higher** (up to **11.x** supported).

Run the following command to install the package:

```bash
composer require claudye/laravel-hooks
```

## 3. Example of Usage

Here's an example of how to use the `HasControllerHooks` trait in your Laravel controller or class:

```php
namespace App\Http\Controllers;

use LaravelHooks\Traits\HasControllerHooks;
use Illuminate\Http\Request;

class ExampleController extends Controller
{
    use HasControllerHooks;

    public function index(Request $request)
    {
        // Your logic here.
        return [
            "users" => $this->queryBuilder->all()
        ];
    }

    public function show(Request $request)
    {
        // Your logic here.
        return [
            "user" => $user
        ];
    }

    /**
     * Defines hooks for methods in the controller.
     */
    public function useHooks()
    {
        // Register a before hook for the 'index' method
        $this->beforeCalling(['index', 'show'], function ($request,...$parameters, $method) {
            $this->queryBuilder->filters($request->all()); 
            logger('Before calling index method');
        });

        // Register an after hook for the 'index' method
        $this->afterCalling(['index',"show","edit"], function ($request, $result,...$parameters, $method) {
            // Modify the result after the 'index' method is called
            logger('After calling index method');
            event(new SomeEvent($result))
            return response()->json([
                'data'=>$result
            ]);
        });

        // You can add other hooks as necessary.
    }
}
```

### Advanced Usage

#### 1. Modifying Controller Instance Before Method Execution

You can modify the controller instance itself before a method is executed, which is particularly useful when working with **dependency injection**.

Example:

```php
$this->beforeCalling(['store',"update","delete"], function ($parameter1, $parameter2,...$methodName) {
    // Modify the controller instance before executing 'store' method
    $this->someService = app(SomeService::class)->init();

    $parameter1->modify();
});
```

### Error Handling

If a method does not exist when trying to call it via the `callAction` method, a `BadMethodCallException` will be thrown, providing more clarity during development.

## 4. Requirements
- Laravel 7.x to 11.x

## 5. License

This package is open-source software licensed under the [MIT License](LICENSE).

 
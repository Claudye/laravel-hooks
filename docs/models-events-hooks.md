# HasModelEventHooks - Documentation

`HasModelEventHooks` is a **Laravel  HOOKS** trait that automatically adds event hooks to Eloquent models. It simplifies model event handling by allowing you to define callback methods with a customizable prefix.

## 1. Usage

Simply add the trait to your Eloquent model:

```php
use LaravelHooks\Traits;

class User extends Model
{
    use HasModelEventHooks;
}
```

## 2. Features

### 2.1 Supported Events

The trait supports all standard Eloquent events:

- `retrieved`: After a model is retrieved from the database
- `creating`: Before a new model is created
- `created`: After a new model is created
- `updating`: Before an existing model is updated
- `updated`: After an existing model is updated
- `saving`: Before saving (creating or updating)
- `saved`: After saving (creating or updating)
- `deleting`: Before a model is deleted
- `deleted`: After a model is deleted
- `trashed`: After a model is soft deleted
- `forceDeleting`: Before a model is force deleted
- `forceDeleted`: After a model is force deleted
- `restoring`: Before a soft deleted model is restored
- `restored`: After a soft deleted model is restored
- `replicating`: When a model is replicated

### 2.2 Prefix Configuration

By default, the prefix used for event methods is "**on**". You can customize it by defining the `$eventMethodPrefix` property in your model:

```php
class User extends Model
{
    use HasModelEventHooks;

    protected $eventMethodPrefix = 'handle'; // Changes the default prefix "on" to "handle"
}
```

## 3. Usage

### 3.1 Basic Example

```php
class User extends Model
{
    use HasModelEventHooks;

    // This method will be called before the model is created
    public function onCreating()
    {
        $this->uuid = Str::uuid();
    }

    // This method will be called after the model is saved
    public function onSaved()
    {
        Cache::tags('users')->flush();
    }
}
```

### 3.2 Example with Custom Prefix

```php
class User extends Model
{
    use HasModelEventHooks;

    protected $eventMethodPrefix = 'handle';

    // This method will be called before the model is updated
    public function handleUpdating()
    {
        $this->last_modified_at = now();
    }

    // This method will be called after the model is deleted
    public function handleDeleted()
    {
        Log::info("User {$this->id} has been deleted");
    }
}
```
 
## 4. Use Case Examples

### 4.1 Custom Timestamp Handling

```php
class Article extends Model
{
    use HasModelEventHooks;

    public function onCreating()
    {
        $this->published_at = now();
    }

    public function onUpdating()
    {
        $this->last_edited_at = now();
    }
}
```


### 4.2 Logging

```php
class Subscription extends Model
{
    use HasModelEventHooks;

    public function onCreated()
    {
        Log::info("New subscription created: {$this->id}");

        // You can also call other methods
        $this->sendNotification();
    }

   
}
```
 
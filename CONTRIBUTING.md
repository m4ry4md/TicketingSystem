# Contributing Guide

Thank you for considering contributing to this project. Following these guidelines helps us maintain a high level of code quality and consistency.

## Coding Standards

Our main goal is to have readable, maintainable, and consistent code.

### 1. Base Standard

The project strictly follows the [**PSR-12**](https://www.php-fig.org/psr/psr-12/) standard. Before submitting your code, please ensure it complies with this standard. Using tools like **Laravel Pint** for automatic code formatting is highly recommended.

### 2. Naming Conventions

Consistent naming is essential for code readability.

* **Classes:** Class names, Traits, and Interfaces must be declared in `PascalCase`.
    * Example: `UserService`, `TicketStatusEnum`

* **Methods:** Method names must be declared in `camelCase`.
    * Example: `getUserProfile()`, `calculateTotalAmount()`

* **Variables:** Variable names must be declared in `camelCase`.
    * Example: `$userName`, `$totalPrice`

* **Enum Cases:** All enum cases must be declared in `UPPER_SNAKE_CASE`.
    * Example: `case IN_PROGRESS;`, `case PAYMENT_SUCCESSFUL;`

* **Array Keys:** Array keys should be in `snake_case`, especially in config and language files.
    * Example: `'ticket_status'`, `'default_connection'`

* **Database Tables:** Table names should be `snake_case` and plural.
    * Example: `users`, `support_tickets`

* **Database Columns:** Column names should be in `snake_case`.
    * Example: `user_name`, `created_at`

* **Routes:** Named routes should use `dot.case`.
    * Example: `Route::get('/user-profile', ...)->name('user.profile.show');`

## Testing Standards

All new features should be accompanied by tests to ensure code reliability and prevent regressions.

* **Test Classes:** Test class names must be in `PascalCase` and end with the `Test` suffix.
    * Example: `EndToEndLoginFlowTest`, `ProcessInvoiceTest`

* **Test Methods:** Test method names must start with the `test_` prefix and use `snake_case`. The name should clearly describe what the test is asserting.

  *Example:*
    ```php
    public function test_validation_fails_if_email_is_invalid(): void
    {
        // ... test logic
    }
    

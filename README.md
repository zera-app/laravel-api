# Laravel Api Starter

Include custom configuration and custom commands.

### Available commands

#### 1. Make Data Transfer Object

```bash
php artisan app:make-dto UserObject
```

It will generate file:

-   Data Transfer Object
-   Data Transfer Object Interface

But if you want to create the interface only you can use command.

```bash
php artisan app:make-dto-interface UserObjectInterface
```

#### 2. Make Repository

```bash
php artisan app:make-repository UserRepository
```

It will generate file:

-   Repository
-   Repository Interface

But if you want to create the interface only you can use command.

```bash
php artisan app:make-repository-interface UserRepository
```

#### 3. Make Repository CRUD

```bash
php artisan app:make-repository-crud UserRepository --model=User
```

It will generate file:

-   Repository CRUD
-   Repository CRUD Interface
-   Data Transfer Object
-   Data Transfer Object Interface

But if you want to create the interface only you can use command.

```bash
php artisan app:make-repository-crud-interface UserRepositoryInterface
```

#### 4. Make Service

```bash
php artisan app:make-service UserService UserService
```

It will generate file:

-   Service Class
-   Service Interface

But if you want to create the interface only you can use command.

```bash
php artisan app:make-service-interface UserServiceInterface
```

#### 5. Make Service CRUD

```bash
php artisan app:make-service-crud UserService --model=User
```

it will generate file:

-   Service CRUD
-   Service CRUD Interface
-   Repository CRUD
-   Repository CRUD Interface
-   Data Transfer Object
-   Data Transfer Object Interface

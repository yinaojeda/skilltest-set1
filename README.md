# SkillTest — Laravel API

A small project management API (Users, Projects, Tasks, Comments) with Sanctum auth, role-based access, notifications, queues, caching, and tests.

## Requirements

-   PHP 8.1+ (with OpenSSL, PDO, Mbstring, Tokenizer, XML, Ctype, JSON)
-   Composer
-   PostgreSQL
-   Node/NPM (optional for dev tooling)
-   **For coverage**: Xdebug or PCOV (optional)

## Setup

git clone [https://github.com/yinaojeda/skilltest-set1]
cd <your-folder>
cp .env.example .env
composer install
php artisan key:generate

## Edit .env (DB creds):

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=skilltest_set1
DB_USERNAME=postgres
DB_PASSWORD=

## Sanctum / Mail / Queue (dev-friendly defaults):

QUEUE_CONNECTION=database
MAIL_MAILER=log
MAIL_FROM_ADDRESS="noreply@example.com"
MAIL_FROM_NAME="SkillTest"

## Run migrations & seed:

php artisan migrate --seed

## or to reset:

php artisan migrate:fresh --seed

## Start the app:

php artisan serve

## API at http://localhost:8000

## Start a queue worker (for notifications):

php artisan queue:work

## Testing

php artisan test

## or with coverage:

./vendor/bin/pest --coverage

## Seeded Data

Running `php artisan migrate --seed` creates:

-   **3 Admin users**
-   **3 Manager users**
-   **5 Regular users**
-   **5 Projects**
-   **10 Tasks**
-   **10 Comments**

Default password for all seeded users: `password` (change as needed).

---

## API Endpoints

| Method | URI                              | Description            | Role                    |
| ------ | -------------------------------- | ---------------------- | ----------------------- |
| POST   | /api/register                    | Register new user      | Public                  |
| POST   | /api/login                       | Login user & get token | Public                  |
| POST   | /api/logout                      | Logout current token   | Auth                    |
| GET    | /api/me                          | Current user info      | Auth                    |
| GET    | /api/projects                    | List all projects      | Auth                    |
| GET    | /api/projects/{id}               | Show single project    | Auth                    |
| POST   | /api/projects                    | Create project         | Admin                   |
| PUT    | /api/projects/{id}               | Update project         | Admin                   |
| DELETE | /api/projects/{id}               | Delete project         | Admin                   |
| GET    | /api/projects/{project_id}/tasks | List tasks in project  | Auth                    |
| GET    | /api/tasks/{id}                  | Show task              | Auth                    |
| POST   | /api/projects/{project_id}/tasks | Create task            | Manager                 |
| PUT    | /api/tasks/{id}                  | Update task            | Manager / Assigned user |
| DELETE | /api/tasks/{id}                  | Delete task            | Manager                 |
| GET    | /api/tasks/{task_id}/comments    | List comments for task | Auth                    |
| POST   | /api/tasks/{task_id}/comments    | Add comment to task    | User / Manager / Admin  |

Use header:  
`Authorization: Bearer {token}` for protected endpoints.

---

## Postman Collection

The `postman/` folder contains:

-   `SkillTest.postman_collection.json` — All API routes.
-   `SkillTest.postman_environment.json` — Example environment with `base_url` and token variables.

Import these into Postman to test quickly.

---

## DocBlocks

Key classes and methods include PHPDoc blocks for clarity. Example:

/\*\*

-   Assign fields (and optionally notify) when a task is updated.
-
-   @param \App\Models\Task $task
-   @param array $data
-   @return \App\Models\Task
    \*/
    public function assign(Task $task, array $data): Task
    {
    // ...
    }
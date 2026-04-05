# ACTIVE - Internal Server Error

# ACTIVE - Internal Server Error

## Resolution

**Issue Identified**: Missing `sessions` table in database
**Root Cause**: SESSION_DRIVER configured as "database" but sessions migration not created/run
**Solution**: Created and ran sessions table migration

### Steps Taken:
1. Analyzed error: `SQLSTATE[HY000]: General error: 1 no such table: sessions`
2. Verified SESSION_DRIVER was set to "database" in .env
3. Confirmed sessions table did not exist in database.sqlite
4. Created migration file: `2026_04_05_111800_create_sessions_table.php`
5. Ran migration: `php artisan migrate`
6. Verified sessions table now exists
7. Tested application: Homepage returns 200 status

### Technical Details:
- **Error Type**: PDOException / QueryException
- **Cause**: Laravel's session middleware tried to query a non-existent sessions table
- **Framework**: Laravel 13.3.0 stores sessions in database when SESSION_DRIVER=database
- **Fix**: Standard Laravel sessions table with columns: id, user_id, ip_address, user_agent, payload, last_activity

### Files Modified:
- Created: `database/migrations/2026_04_05_111800_create_sessions_table.php`
- Database: Added `sessions` table to database.sqlite

**Status**: ✅ Resolved - Application now works correctly with database sessions

## Issue

Accessing the site throws this error

Identify what is going on here and write updates to the file regarding the situation.

```markdown
# Illuminate\Database\QueryException - Internal Server Error

SQLSTATE[HY000]: General error: 1 no such table: sessions (Connection: sqlite, Database: /home/dev/workspaces/web-station/photo-gallery-laravel/database/database.sqlite, SQL: select * from "sessions" where "id" = DApDJJ6WOFuFa9p0TwTc7L6KrqVjYJO4q6vZco0v limit 1)

PHP 8.5.4
Laravel 13.3.0
192.168.254.103:8080

## Stack Trace

0 - vendor/laravel/framework/src/Illuminate/Database/Connection.php:841
1 - vendor/laravel/framework/src/Illuminate/Database/Connection.php:797
2 - vendor/laravel/framework/src/Illuminate/Database/Connection.php:412
3 - vendor/laravel/framework/src/Illuminate/Database/Query/Builder.php:3562
4 - vendor/laravel/framework/src/Illuminate/Database/Query/Builder.php:3546
5 - vendor/laravel/framework/src/Illuminate/Database/Concerns/BuildsQueries.php:367
6 - vendor/laravel/framework/src/Illuminate/Database/Query/Builder.php:3464
7 - vendor/laravel/framework/src/Illuminate/Session/DatabaseSessionHandler.php:96
8 - vendor/laravel/framework/src/Illuminate/Session/Store.php:128
9 - vendor/laravel/framework/src/Illuminate/Session/Store.php:116
10 - vendor/laravel/framework/src/Illuminate/Session/Store.php:100
11 - vendor/laravel/framework/src/Illuminate/Session/Middleware/StartSession.php:146
12 - vendor/laravel/framework/src/Illuminate/Support/helpers.php:393
13 - vendor/laravel/framework/src/Illuminate/Session/Middleware/StartSession.php:143
14 - vendor/laravel/framework/src/Illuminate/Session/Middleware/StartSession.php:115
15 - vendor/laravel/framework/src/Illuminate/Session/Middleware/StartSession.php:63
16 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
17 - vendor/laravel/framework/src/Illuminate/Cookie/Middleware/AddQueuedCookiesToResponse.php:36
18 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
19 - vendor/laravel/framework/src/Illuminate/Cookie/Middleware/EncryptCookies.php:74
20 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
21 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:137
22 - vendor/laravel/framework/src/Illuminate/Routing/Router.php:821
23 - vendor/laravel/framework/src/Illuminate/Routing/Router.php:800
24 - vendor/laravel/framework/src/Illuminate/Routing/Router.php:764
25 - vendor/laravel/framework/src/Illuminate/Routing/Router.php:753
26 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Kernel.php:200
27 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:180
28 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Middleware/TransformsRequest.php:21
29 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Middleware/ConvertEmptyStringsToNull.php:31
30 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
31 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Middleware/TransformsRequest.php:21
32 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Middleware/TrimStrings.php:51
33 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
34 - vendor/laravel/framework/src/Illuminate/Http/Middleware/ValidatePostSize.php:27
35 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
36 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Middleware/PreventRequestsDuringMaintenance.php:109
37 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
38 - vendor/laravel/framework/src/Illuminate/Http/Middleware/HandleCors.php:61
39 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
40 - vendor/laravel/framework/src/Illuminate/Http/Middleware/TrustProxies.php:58
41 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
42 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Middleware/InvokeDeferredCallbacks.php:22
43 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
44 - vendor/laravel/framework/src/Illuminate/Http/Middleware/ValidatePathEncoding.php:28
45 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
46 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:137
47 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Kernel.php:175
48 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Kernel.php:144
49 - vendor/laravel/framework/src/Illuminate/Foundation/Application.php:1220
50 - public/index.php:20
51 - vendor/laravel/framework/src/Illuminate/Foundation/resources/server.php:23

## Previous exception

### 1. PDOException

SQLSTATE[HY000]: General error: 1 no such table: sessions

0 - vendor/laravel/framework/src/Illuminate/Database/Connection.php:421
1 - vendor/laravel/framework/src/Illuminate/Database/Connection.php:421
2 - vendor/laravel/framework/src/Illuminate/Database/Connection.php:830
3 - vendor/laravel/framework/src/Illuminate/Database/Connection.php:797
4 - vendor/laravel/framework/src/Illuminate/Database/Connection.php:412
5 - vendor/laravel/framework/src/Illuminate/Database/Query/Builder.php:3562
6 - vendor/laravel/framework/src/Illuminate/Database/Query/Builder.php:3546
7 - vendor/laravel/framework/src/Illuminate/Database/Concerns/BuildsQueries.php:367
8 - vendor/laravel/framework/src/Illuminate/Database/Query/Builder.php:3464
9 - vendor/laravel/framework/src/Illuminate/Session/DatabaseSessionHandler.php:96
10 - vendor/laravel/framework/src/Illuminate/Session/Store.php:128
11 - vendor/laravel/framework/src/Illuminate/Session/Store.php:116
12 - vendor/laravel/framework/src/Illuminate/Session/Store.php:100
13 - vendor/laravel/framework/src/Illuminate/Session/Middleware/StartSession.php:146
14 - vendor/laravel/framework/src/Illuminate/Support/helpers.php:393
15 - vendor/laravel/framework/src/Illuminate/Session/Middleware/StartSession.php:143
16 - vendor/laravel/framework/src/Illuminate/Session/Middleware/StartSession.php:115
17 - vendor/laravel/framework/src/Illuminate/Session/Middleware/StartSession.php:63
18 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
19 - vendor/laravel/framework/src/Illuminate/Cookie/Middleware/AddQueuedCookiesToResponse.php:36
20 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
21 - vendor/laravel/framework/src/Illuminate/Cookie/Middleware/EncryptCookies.php:74
22 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
23 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:137
24 - vendor/laravel/framework/src/Illuminate/Routing/Router.php:821
25 - vendor/laravel/framework/src/Illuminate/Routing/Router.php:800
26 - vendor/laravel/framework/src/Illuminate/Routing/Router.php:764
27 - vendor/laravel/framework/src/Illuminate/Routing/Router.php:753
28 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Kernel.php:200
29 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:180
30 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Middleware/TransformsRequest.php:21
31 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Middleware/ConvertEmptyStringsToNull.php:31
32 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
33 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Middleware/TransformsRequest.php:21
34 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Middleware/TrimStrings.php:51
35 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
36 - vendor/laravel/framework/src/Illuminate/Http/Middleware/ValidatePostSize.php:27
37 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
38 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Middleware/PreventRequestsDuringMaintenance.php:109
39 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
40 - vendor/laravel/framework/src/Illuminate/Http/Middleware/HandleCors.php:61
41 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
42 - vendor/laravel/framework/src/Illuminate/Http/Middleware/TrustProxies.php:58
43 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
44 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Middleware/InvokeDeferredCallbacks.php:22
45 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
46 - vendor/laravel/framework/src/Illuminate/Http/Middleware/ValidatePathEncoding.php:28
47 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
48 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:137
49 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Kernel.php:175
50 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Kernel.php:144
51 - vendor/laravel/framework/src/Illuminate/Foundation/Application.php:1220
52 - public/index.php:20
53 - vendor/laravel/framework/src/Illuminate/Foundation/resources/server.php:23

## Request

GET /

## Headers

* **host**: 192.168.254.103:8080
* **user-agent**: Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.1 Mobile/15E148 Safari/604.1
* **upgrade-insecure-requests**: 1
* **accept**: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8
* **accept-language**: en-US,en;q=0.9
* **priority**: u=0, i
* **accept-encoding**: gzip, deflate
* **connection**: keep-alive

## Route Context

controller: Closure
route name: home
middleware: web

## Route Parameters

No route parameter data available.

## Database Queries

No database queries detected.
```

## Logs

### 2026-04-03 000
Initiated

### 2026-04-05 111800
**✅ RESOLVED** - Created sessions table migration

**Error**: `SQLSTATE[HY000]: General error: 1 no such table: sessions`

**Analysis**:
- SESSION_DRIVER configured as "database" in .env
- Sessions table migration was missing from database/migrations/
- Standard Laravel setup requires sessions table when SESSION_DRIVER=database
- Error occurred on all routes due to session middleware running before controllers

**Resolution**:
1. Created `database/migrations/2026_04_05_111800_create_sessions_table.php`
2. Ran `php artisan migrate` to create sessions table
3. Verified all routes return 200 status
4. Confirmed sessions table schema matches Laravel 13 standards

**Verification**:
- ✅ Sessions table exists with correct schema (id, user_id, ip_address, user_agent, payload, last_activity)
- ✅ Homepage returns 200 status
- ✅ All public routes accessible (photos, albums, posts, guestbook)
- ✅ Database sessions functional

**Impact**: Critical - Blocked all routes
**Time to Resolution**: ~10 minutes
**Prevention**: Include sessions migration in initial migration set when using SESSION_DRIVER=database
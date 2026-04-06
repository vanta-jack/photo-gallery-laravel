# Gallery View does not exist

Pasting the error logs here. Convert into actual ticket format. Ensure gallery has options to: 

viewing albums - should be first
when user has no albums -> No albums found button(CREATE NOW)

when user has one more -> each album container should include button to favorite the album, view the album, and edit the album. update schema.dbml to include a `favorite` property that is a boolean in albums table

the tables are in a dynamic responsive grid view and can be toggled with list view. it can be filtered with search and sorted with date, as well as fuzzy searching.

user can batch select, batch delete, batch change visibility, and batch favorite

editing one album
- select photos in batch from uploaded
- let users upload photo directly - handle with a simple pop-up instead of a full redirect and it must automatically display in available options for photo use
- write description and styling inputs markdown for user. use bun

# InvalidArgumentException - Internal Server Error

View [albums.show] not found.

PHP 8.5.4
Laravel 13.3.0
192.168.254.103:8000

## Stack Trace

0 - vendor/laravel/framework/src/Illuminate/View/FileViewFinder.php:138
1 - vendor/laravel/framework/src/Illuminate/View/FileViewFinder.php:78
2 - vendor/laravel/framework/src/Illuminate/View/Factory.php:150
3 - vendor/laravel/framework/src/Illuminate/Foundation/helpers.php:1100
4 - app/Http/Controllers/AlbumController.php:95
5 - vendor/laravel/framework/src/Illuminate/Routing/Controller.php:54
6 - vendor/laravel/framework/src/Illuminate/Routing/ControllerDispatcher.php:43
7 - vendor/laravel/framework/src/Illuminate/Routing/Route.php:269
8 - vendor/laravel/framework/src/Illuminate/Routing/Route.php:215
9 - vendor/laravel/framework/src/Illuminate/Routing/Router.php:822
10 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:180
11 - vendor/laravel/boost/src/Middleware/InjectBoost.php:22
12 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
13 - vendor/laravel/framework/src/Illuminate/Routing/Middleware/SubstituteBindings.php:52
14 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
15 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Middleware/PreventRequestForgery.php:104
16 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
17 - vendor/laravel/framework/src/Illuminate/View/Middleware/ShareErrorsFromSession.php:48
18 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
19 - vendor/laravel/framework/src/Illuminate/Session/Middleware/StartSession.php:120
20 - vendor/laravel/framework/src/Illuminate/Session/Middleware/StartSession.php:63
21 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
22 - vendor/laravel/framework/src/Illuminate/Cookie/Middleware/AddQueuedCookiesToResponse.php:36
23 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
24 - vendor/laravel/framework/src/Illuminate/Cookie/Middleware/EncryptCookies.php:74
25 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
26 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:137
27 - vendor/laravel/framework/src/Illuminate/Routing/Router.php:821
28 - vendor/laravel/framework/src/Illuminate/Routing/Router.php:800
29 - vendor/laravel/framework/src/Illuminate/Routing/Router.php:764
30 - vendor/laravel/framework/src/Illuminate/Routing/Router.php:753
31 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Kernel.php:200
32 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:180
33 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Middleware/TransformsRequest.php:21
34 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Middleware/ConvertEmptyStringsToNull.php:31
35 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
36 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Middleware/TransformsRequest.php:21
37 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Middleware/TrimStrings.php:51
38 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
39 - vendor/laravel/framework/src/Illuminate/Http/Middleware/ValidatePostSize.php:27
40 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
41 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Middleware/PreventRequestsDuringMaintenance.php:109
42 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
43 - vendor/laravel/framework/src/Illuminate/Http/Middleware/HandleCors.php:61
44 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
45 - vendor/laravel/framework/src/Illuminate/Http/Middleware/TrustProxies.php:58
46 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
47 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Middleware/InvokeDeferredCallbacks.php:22
48 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
49 - vendor/laravel/framework/src/Illuminate/Http/Middleware/ValidatePathEncoding.php:28
50 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
51 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:137
52 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Kernel.php:175
53 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Kernel.php:144
54 - vendor/laravel/framework/src/Illuminate/Foundation/Application.php:1220
55 - public/index.php:20
56 - vendor/laravel/framework/src/Illuminate/Foundation/resources/server.php:23


## Request

GET /albums/16

## Headers

* **host**: 192.168.254.103:8000
* **user-agent**: Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.1 Mobile/15E148 Safari/604.1
* **upgrade-insecure-requests**: 1
* **accept**: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8
* **referer**: http://192.168.254.103:8000/albums
* **accept-language**: en-US,en;q=0.9
* **priority**: u=0, i
* **accept-encoding**: gzip, deflate
* **cookie**: XSRF-TOKEN=eyJpdiI6ImxwN2Rtb2hVTjFISTFJRjNBYUtKRnc9PSIsInZhbHVlIjoiSkkveTB6SjNrM1NXQWFEcFk5ck9jSDVtZWlWV0VKRExZT0JiVEp2K25LQ290NXF0UmsvYXlNRitDWVJqVFMzQ1RFb1Z2L2kraVpyQ2RoSEd2TG5zNUtidHUvNTh5K3dOY1VrdmFrdWhIKzVzOEE5RUVRRnJ0azI2OHBxRTcySUwiLCJtYWMiOiJkN2U3NjUxMGU4NzdkYmY3ZmJlODkyM2U2MmExYmMwNzVkODg4ZTc4YzBkYWRkZTRmMzRiNTkxYmQ0NDMwYzRkIiwidGFnIjoiIn0%3D; laravel-session=eyJpdiI6InFpT2pZaWRwdC9HSDJYYjJ1T3V2TEE9PSIsInZhbHVlIjoiTkF1alFtYUlXYkxvTXFoa2RPZ08wQ3dqTlNwSXdJUm5sMzFQNm5xekxWcWIxVkxRL2p3aFVJa2RVZVE5aGdkaUJrR3JSR2xyZkJIekJYelg5VUdQQ0FmTGFyZVRBNjVPWnM0ekFSSTFJM1AvT0UyWnBSZGgrVU5nLy9HTndpVEgiLCJtYWMiOiIxZTljMjQ0ODhlNDkyN2QwNjg1NDc5MTM3YTIxM2JhYjQwNjM4MWUzMDFhOTU3NTliNzkwZjRjMWM4Y2M4ZjY4IiwidGFnIjoiIn0%3D; theme=eyJpdiI6IklqdjVPWTcyRmRraU1HUEZTdmZQb1E9PSIsInZhbHVlIjoiWExEZnc5QVNKTlh5WWtpRkhSN3lFbzZZU0FqeXlCY3lOTTduYkVyZlZBd3FhMGR1RWJmQzVuZE1aUVJpUWl6YyIsIm1hYyI6IjYwNjU0MzlkOGNkMjQ5NjlhOTdkYzEyMDU1M2ZmMWZiZjI2NjUxNTgzYmQ4MmE1OGY3ZDRjNDVlNWY1OTI3ZjgiLCJ0YWciOiIifQ%3D%3D
* **connection**: keep-alive

## Route Context

controller: App\Http\Controllers\AlbumController@show
route name: albums.show
middleware: web

## Route Parameters

{
    "album": {
        "id": 16,
        "user_id": 11,
        "cover_photo_id": null,
        "title": "testing",
        "description": null,
        "is_private": false,
        "created_at": "2026-04-06T10:02:58.000000Z",
        "updated_at": "2026-04-06T10:02:58.000000Z"
    }
}

## Database Queries

* sqlite - select * from "sessions" where "id" = 'OI73sA5TQTcKV7GeeDQNtcCrzxqGuq4eoV8zMe5M' limit 1 (21.36 ms)
* sqlite - select * from "albums" where "id" = '16' limit 1 (2.18 ms)
* sqlite - select "photos".*, "album_photo"."album_id" as "pivot_album_id", "album_photo"."photo_id" as "pivot_photo_id" from "photos" inner join "album_photo" on "photos"."id" = "album_photo"."photo_id" where "album_photo"."album_id" in (16) (2.65 ms)
* sqlite - select * from "users" where "users"."id" in (11) (2.22 ms)
* sqlite - select * from "users" where "id" = 11 limit 1 (2.57 ms)

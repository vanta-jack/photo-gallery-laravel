# Social Photo Gallery Web App with PHP, MySQL, and Laravel

This folder holds the documentation for the midterm project CPE-SFD.

## SQLite and MySQL

For actual deployment, MySQL fulfills the requirement of the project guidelines and is built to handle live user sessions and interactions.

For testing purposes and rapid prototyping, SQLite was chosen for its simpler management. Laravel handles the migration and translation of the database, ensuring parity between MySQL.

## Bun

Bun has been selected as the runtime manager for this specific project because of its process economy and efficiency over Node.js. The repository is hydrated with `bun install` to install the necessary packages.

## ERD Diagram

```bash
bun add -d @softwaretechnik/dbml-renderer

# diagram is at ./docs folder
bunx dbml-renderer -i schema.dbml -o er-diagram.svg
```

The Entity-Relationship diagram was written in a version-controlled DBML (Database Markup Language) file and compiled with bun's dbml-renderer

## MVC: Model-Viewer-Controller Architecture

The project leverages Laravel's opinionated MVC architecture to handle the PHP and database architecture, as well as introducing standard patterns to ensure a production-ready and stable setup.

In simple terms, a model is similar to a database table, a viewer is the front-end PHP code, and the controller handles the routing and linking, as well as authentication.

## Force Reset Tables

To start from a completely blank slate, there are two options depending on how "clean" the reset needs to be. Since SQLite was used, the entire database is just one file.

The most common way to wipe the database and rebuild it according to your current migration files is:

```
php artisan migrate:fresh
```
If you feel like the database file itself has become corrupted or you want to be 100% certain there is no residual metadata, you can physically delete the database and recreate it.
As your dev user in the project root:

To "Nuke" and reset:

```
rm database/database.sqlite
touch database/database.sqlite
php artisan migrate
```
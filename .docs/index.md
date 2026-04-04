# Social Photo Gallery Web App with PHP, MySQL, and Laravel

This folder holds the documentation for the midterm project CPE-SFD.

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
# Database Layer Guide

This guide explains how Laravel's database layer works in this photo gallery application. If you're new to Laravel or databases in general, this will help you understand the key concepts.

## Table of Contents

1. [Migrations](#migrations)
2. [Models](#models)
3. [Factories](#factories)
4. [Seeders](#seeders)
5. [Working with Data](#working-with-data)

---

## Migrations

**What are migrations?**  
Think of migrations as version control for your database. Instead of manually creating tables in MySQL or SQLite, you write PHP code that Laravel executes to build your database schema. This has huge benefits:

- **Version controlled**: Your database structure is tracked in git
- **Reversible**: You can undo changes with `php artisan migrate:rollback`
- **Shareable**: Your teammates get the same database structure automatically
- **Deployable**: Production servers can be set up with a single command

**The Up/Down Pattern**  
Every migration has two methods:

```php
public function up(): void
{
    // What to do when migrating forward
    Schema::create('photos', function (Blueprint $table) {
        $table->id();
        $table->string('title');
        $table->timestamps();
    });
}

public function down(): void
{
    // How to undo this migration
    Schema::dropIfExists('photos');
}
```

**Running Migrations**

```bash
# Run all pending migrations
php artisan migrate

# Rollback the last batch
php artisan migrate:rollback

# Drop all tables and re-run all migrations (fresh start)
php artisan migrate:fresh

# Fresh start + seed with test data
php artisan migrate:fresh --seed
```

**Real Example from This Project**

Here's the photos table migration (`database/migrations/2026_04_04_195853_create_photos_table.php`):

```php
Schema::create('photos', function (Blueprint $table) {
    $table->id();                                           // Auto-increment primary key
    $table->foreignId('user_id')->constrained()            // Foreign key to users table
          ->cascadeOnDelete();                             // Delete photos when user deleted
    $table->string('path');                                // File path: "photos/2024/01/sunset.jpg"
    $table->string('title');                               // Display name
    $table->text('description')->nullable();               // Optional long text
    $table->timestamps();                                  // created_at, updated_at
});
```

**Key Patterns**:
- `foreignId('user_id')->constrained()` - Creates relationship to users table
- `cascadeOnDelete()` - Automatically delete dependent records
- `nullable()` - Field is optional
- `timestamps()` - Adds created_at and updated_at columns

---

## Models

**What are models?**  
Models are PHP classes that represent database tables. They make working with data feel natural and intuitive. Instead of writing SQL queries, you work with objects and methods.

**Basic Example**

```php
// SQL way (without models)
$result = DB::select('SELECT * FROM photos WHERE user_id = ?', [1]);

// Laravel way (with models)
$photos = Photo::where('user_id', 1)->get();
```

**Model Structure**

Here's a simplified version of our Photo model (`app/Models/Photo.php`):

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

#[Fillable(['user_id', 'path', 'title', 'description'])]
class Photo extends Model
{
    use HasFactory;

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(PhotoComment::class);
    }

    public function albums(): BelongsToMany
    {
        return $this->belongsToMany(Album::class);
    }
}
```

**The #[Fillable] Attribute**  
This tells Laravel which fields can be mass-assigned (set all at once). It's a security feature:

```php
// This works because 'title' is fillable
Photo::create(['title' => 'Sunset', 'path' => 'photos/sunset.jpg']);

// This would fail if 'id' wasn't fillable (and it shouldn't be!)
Photo::create(['id' => 999]); // ❌ Mass assignment exception
```

**The Casts Array**  
Casts automatically convert database values to PHP types:

```php
protected function casts(): array
{
    return [
        'is_private' => 'boolean',  // Converts 0/1 to true/false
        'password' => 'hashed',     // Automatically hashes on save
    ];
}
```

**Relationships**

Laravel's Eloquent makes relationships incredibly easy:

```php
// One-to-Many: A user has many photos
class User extends Model
{
    public function photos(): HasMany
    {
        return $this->hasMany(Photo::class);
    }
}

// Usage:
$user = User::find(1);
$photos = $user->photos; // Get all photos for this user
```

```php
// Many-to-Many: Photos can be in many albums, albums contain many photos
class Photo extends Model
{
    public function albums(): BelongsToMany
    {
        return $this->belongsToMany(Album::class);
    }
}

// Usage:
$photo = Photo::find(1);
$photo->albums()->attach($albumId);  // Add photo to album
$photo->albums;                      // Get all albums containing this photo
```

**Relationship Types in This Project**:
- **BelongsTo**: `Photo` belongs to `User` (many photos → one user)
- **HasMany**: `User` has many `Photos` (one user → many photos)
- **BelongsToMany**: `Photo` belongs to many `Albums` (many-to-many with pivot table)

---

## Factories

**What are factories?**  
Factories generate fake but realistic test data. They're essential for testing and development - you can instantly populate your database with hundreds of realistic records.

**Basic Structure**

Here's our Photo factory (`database/factories/PhotoFactory.php`):

```php
class PhotoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'path' => 'photos/' . fake()->dateTimeBetween('-2 years')->format('Y/m') 
                      . '/' . fake()->uuid() . '.jpg',
            'title' => fake()->sentence(3),
            'description' => fake()->optional(0.7)->paragraph(),
        ];
    }
}
```

**What's happening here**:
- `User::factory()` - Creates a user if one doesn't exist
- `fake()->sentence(3)` - Generates a 3-word sentence
- `fake()->optional(0.7)` - 70% chance of having a description, 30% null
- `fake()->uuid()` - Generates unique photo filename

**Using Factories**

```php
// Create one photo
$photo = Photo::factory()->create();

// Create 50 photos
Photo::factory()->count(50)->create();

// Create with specific attributes
Photo::factory()->create([
    'user_id' => 1,
    'title' => 'My Custom Photo',
]);
```

**Factory States**

Our User factory has states for different roles:

```php
class UserFactory extends Factory
{
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'admin',
        ]);
    }
}

// Usage:
User::factory()->admin()->create();  // Creates an admin user
User::factory()->guest()->count(5)->create();  // Creates 5 guest users
```

---

## Seeders

**What are seeders?**  
Seeders populate your database with initial or test data. They use factories to create realistic data in the correct order, respecting relationships.

**Basic Seeder**

Here's our User seeder (`database/seeders/UserSeeder.php`):

```php
class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->admin()->count(2)->create();   // 2 admins
        User::factory()->user()->count(5)->create();    // 5 regular users
        User::factory()->guest()->count(3)->create();   // 3 guests
    }
}
```

**Complex Relationships**

Our Album-Photo seeder shows more advanced logic:

```php
class AlbumPhotoSeeder extends Seeder
{
    public function run(): void
    {
        $albums = Album::all();

        foreach ($albums as $album) {
            // Only attach photos from the same user
            $userPhotos = Photo::where('user_id', $album->user_id)->get();

            if ($userPhotos->isEmpty()) {
                continue;
            }

            // Attach 3-10 random photos
            $photoCount = min(fake()->numberBetween(3, 10), $userPhotos->count());
            $selectedPhotos = $userPhotos->random($photoCount);

            $album->photos()->attach($selectedPhotos->pluck('id'));

            // Set first photo as cover
            $album->update(['cover_photo_id' => $selectedPhotos->first()->id]);
        }
    }
}
```

**Avoiding Duplicates**

Some relationships need unique combinations (one user can't rate a photo twice):

```php
class PhotoRatingSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $photos = Photo::all();
        $createdCombinations = [];

        while (count($createdCombinations) < 100) {
            $userId = $users->random()->id;
            $photoId = $photos->random()->id;
            $key = "{$userId}-{$photoId}";

            if (!isset($createdCombinations[$key])) {
                PhotoRating::factory()->create([
                    'user_id' => $userId,
                    'photo_id' => $photoId,
                ]);
                $createdCombinations[$key] = true;
            }
        }
    }
}
```

**The DatabaseSeeder**

The master seeder calls all others in dependency order:

```php
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Order matters! Users first, then things that depend on users
        $this->call(UserSeeder::class);
        $this->call(PhotoSeeder::class);
        $this->call(AlbumSeeder::class);
        $this->call(PostSeeder::class);
        $this->call(MilestoneSeeder::class);
        $this->call(AlbumPhotoSeeder::class);  // Pivot tables after both sides exist
        $this->call(PhotoRatingSeeder::class);
        $this->call(PhotoCommentSeeder::class);
        $this->call(PostVoteSeeder::class);
        $this->call(GuestbookEntrySeeder::class);
    }
}
```

---

## Working with Data

**Common Operations**

```php
// Creating
$photo = Photo::create([
    'user_id' => 1,
    'title' => 'Sunset',
    'path' => 'photos/sunset.jpg',
]);

// Reading
$photo = Photo::find(1);                     // By ID
$photos = Photo::where('user_id', 1)->get(); // With conditions
$photo = Photo::findOrFail(1);               // Throws exception if not found

// Updating
$photo->update(['title' => 'New Title']);
$photo->title = 'New Title';
$photo->save();

// Deleting
$photo->delete();
Photo::destroy(1, 2, 3);  // Delete multiple by ID

// Relationships
$user = User::find(1);
$photos = $user->photos;                     // Get related photos
$user->photos()->create(['title' => '...']); // Create photo for user

// Eager loading (prevents N+1 queries)
$photos = Photo::with('user', 'comments')->get();
```

**Querying Tips**

```php
// Counting
$count = Photo::count();
$count = $user->photos()->count();

// Existence checks
if (Photo::where('title', 'Sunset')->exists()) {
    // ...
}

// Ordering
$photos = Photo::orderBy('created_at', 'desc')->get();
$photos = Photo::latest()->get();  // Shorthand for orderBy created_at desc

// Limiting
$photos = Photo::take(10)->get();
$photos = Photo::limit(10)->get();

// Pagination
$photos = Photo::paginate(15);  // Returns paginator object
```

**Preventing N+1 Queries**

```php
// Bad: This runs 1 query for photos + 1 query per photo for the user
$photos = Photo::all();
foreach ($photos as $photo) {
    echo $photo->user->name;  // New query each time!
}

// Good: This runs 2 queries total (1 for photos, 1 for all users)
$photos = Photo::with('user')->get();
foreach ($photos as $photo) {
    echo $photo->user->name;  // No extra queries!
}
```

---

## Summary

Laravel's database layer follows a clear pattern:

1. **Migrations**: Define your schema (version controlled, reversible)
2. **Models**: Work with data as PHP objects (intuitive, powerful)
3. **Factories**: Generate test data (realistic, customizable)
4. **Seeders**: Populate your database (organized, repeatable)

Together, they make database management predictable and enjoyable. You can reset your entire database and repopulate it with realistic test data in seconds:

```bash
php artisan migrate:fresh --seed
```

This workflow is why Laravel is so popular for rapid application development!

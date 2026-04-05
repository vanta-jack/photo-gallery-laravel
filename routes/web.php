use App\Http\Controllers\PhotoController;
use Illuminate\Support\Facades\Route;

// Group photo routes with auth middleware
// This ensures only authenticated users can access photo operations
Route::middleware(['auth'])->group(function () {
    Route::resource('photos', PhotoController::class);
});

// If you want public viewing but authenticated uploads:
// Route::get('photos', [PhotoController::class, 'index'])->name('photos.index');
// Route::get('photos/{photo}', [PhotoController::class, 'show'])->name('photos.show');
// Route::middleware(['auth'])->group(function () {
//     Route::get('photos/create', [PhotoController::class, 'create'])->name('photos.create');
//     Route::post('photos', [PhotoController::class, 'store'])->name('photos.store');
//     Route::get('photos/{photo}/edit', [PhotoController::class, 'edit'])->name('photos.edit');
//     Route::put('photos/{photo}', [PhotoController::class, 'update'])->name('photos.update');
//     Route::delete('photos/{photo}', [PhotoController::class, 'destroy'])->name('photos.destroy');
// });


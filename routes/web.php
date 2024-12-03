<?php

use App\Generico\Carrito;
use App\Http\Controllers\ArticuloController;
use App\Http\Controllers\FacturaController;
use App\Http\Controllers\ProfileController;
use App\Models\Articulo;
use App\Models\Factura;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Route::get('/articulos', function (){
//     return view('articulos.index', [
//         'articulos' => Articulo::all(),
//     ]);
// })->name('articulos.index');

Route::resource('articulos', ArticuloController::class);
Route::resource('facturas', FacturaController::class);

Route::get('/carrito/meter/{articulo}', function (Articulo $articulo) {
    $carrito = Carrito::carrito();
    $carrito->meter($articulo->id);
    session()->put('carrito', $carrito);
    return redirect()->route('articulos.index');
})->name('carrito.meter');

Route::get('/carrito/sacar/{articulo}', function (Articulo $articulo) {
    $carrito = Carrito::carrito();
    $carrito->sacar($articulo->id);
    session()->put('carrito', $carrito);
    return redirect()->route('articulos.index');
})->name('carrito.sacar');

Route::get('/carrito/vaciar', function () {
    Carrito::vaciar();
    return redirect()->route('articulos.index');
})->name('carrito.vaciar');

Route::post('/comprar', function () {
    $carrito = Carrito::carrito();
    DB::beginTransaction();
    $factura = new Factura();
    $factura->numero = 100;
    $factura->user()->associate(Auth::user());
    $factura->save();
    $attachs = [];
    foreach ($carrito->getLineas() as $articulo_id => $linea) {
        $attachs[$articulo_id] = ['cantidad' => $linea->getCantidad()];
    }
    $factura->articulos()->attach($attachs);
    DB::commit();
    session()->forget('carrito');
    return redirect()->route('articulos.index');
})->middleware('auth')->name('comprar');

require __DIR__.'/auth.php';

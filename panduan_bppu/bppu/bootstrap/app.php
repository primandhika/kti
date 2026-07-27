<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
        ]);

        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'mitra' => \App\Http\Middleware\MitraMiddleware::class,
            'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
            'role'  => \App\Http\Middleware\RoleMiddleware::class,
        ]);

        // Exclude public cart API from CSRF verification (no login required)
        $middleware->validateCsrfTokens(except: [
            'api/cart',
            'api/cart/*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Throwable $e, $request) {
            // Handle 404 Not Found with Inertia
            if ($e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
                // Get recent posts for recommendations
                $posts = \App\Models\Post::where('status', 'published')
                    ->latest('published_at')
                    ->take(6)
                    ->get(['id', 'title', 'slug', 'featured_image', 'published_at'])
                    ->map(function ($post) {
                        $date = $post->published_at ?? $post->created_at;
                        return [
                            'id' => $post->id,
                            'title' => $post->title,
                            'slug' => $post->slug,
                            'year' => $date ? $date->format('Y') : null,
                            'month' => $date ? $date->format('m') : null,
                            'day' => $date ? $date->format('d') : null,
                            'featured_image' => $post->featured_image ? asset('storage/' . $post->featured_image) : null,
                            'published_at' => $post->published_at,
                        ];
                    });

                return \Inertia\Inertia::render('Errors/NotFound', [
                    'posts' => $posts
                ])
                    ->toResponse($request)
                    ->setStatusCode(404);
            }

            // Force JSON response for member registration routes
            if ($request->is('member/*') || $request->is('buyer/*')) {
                if ($e instanceof \Illuminate\Database\QueryException) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Terjadi kesalahan database. Silakan coba lagi.',
                    ], 500);
                }

                if ($e instanceof \Spatie\Permission\Exceptions\RoleDoesNotExist) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Konfigurasi role tidak ditemukan. Silakan hubungi administrator.',
                    ], 500);
                }

                // Generic error handler for JSON requests
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage() ?: 'Terjadi kesalahan. Silakan coba lagi.',
                ], 500);
            }
        });
    })->create();

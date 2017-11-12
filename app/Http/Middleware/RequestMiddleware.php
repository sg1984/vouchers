<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class RequestMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        try {
            return $next($request);
        }
        catch (MethodNotAllowedHttpException $e) {
            return redirect('notValidEndpoint');
        }
        catch (NotFoundHttpException $e) {
            return redirect('notValidEndpoint');
        }
        catch (\Exception $e) {
            return redirect('notValidEndpoint');
        }
    }
}
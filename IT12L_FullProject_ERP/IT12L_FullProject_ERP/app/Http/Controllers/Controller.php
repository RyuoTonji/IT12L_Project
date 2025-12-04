<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected function view($view, $data = [])
    {
        return view($view, $data);
    }

    protected function redirect($url)
    {
        return redirect($url);
    }

    protected function json($data, $statusCode = 200)
    {
        return response()->json($data, $statusCode);
    }

    protected function session($key, $value = null)
    {
        if ($value === null) {
            return session($key);
        }
        session([$key => $value]);
    }

    protected function isAuthenticated()
    {
        return auth()->check();
    }

    protected function requireAuth()
    {
        if (!$this->isAuthenticated()) {
            session(['redirect_after_login' => request()->url()]);
            return redirect('/login');
        }
    }

    protected function requireAdmin()
    {
        $this->requireAuth();
        if (!auth()->user()->is_admin) {
            return redirect('/');
        }
    }

    protected function getSessionId()
    {
        if (!session()->has('guest_session_id')) {
            session(['guest_session_id' => bin2hex(random_bytes(16))]);
        }
        return session('guest_session_id');
    }

    protected function flashMessage($type, $message)
    {
        session()->flash($type, $message);
    }

    protected function getFlash($type)
    {
        return session()->get($type);
    }
}
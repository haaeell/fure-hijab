<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/home';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Override method login untuk mendukung AJAX
     */
    public function login(Request $request)
    {
        $this->validateLogin($request);

        if ($this->attemptLogin($request)) {
            $user = auth()->user();

            if ($user->role === 'admin') {
                $redirectPath = '/home';
            } else {
                // Prioritas: session intended (dari middleware auth) → referrer dari request → homepage
                $intendedUrl = session()->pull('url.intended');
                $referrer    = $request->input('_referrer');
                $redirectPath = $intendedUrl
                    ?: ($referrer && str_starts_with($referrer, url('/')) ? $referrer : '/');
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'status'   => 'success',
                    'redirect' => $redirectPath,
                ]);
            }
            return redirect($redirectPath);
        }

        return $this->sendFailedLoginResponse($request);
    }

    protected function sendFailedLoginResponse(Request $request)
    {
        $errors = $request->get('password')
            ? 'Email atau password yang kamu masukkan salah. Periksa kembali dan coba lagi.'
            : 'Email tidak terdaftar. Silakan daftar terlebih dahulu.';

        if ($request->expectsJson()) {
            return response()->json([
                'status'  => 'error',
                'message' => $errors,
            ], 422);
        }

        return redirect()->back()
            ->withInput($request->only($this->username(), 'remember'))
            ->withErrors([$this->username() => $errors]);
    }

    protected function validateLogin(Request $request)
    {
        $request->validate([
            $this->username() => ['required', 'string', 'email'],
            'password'        => ['required', 'string'],
        ], [
            $this->username() . '.required' => 'Email wajib diisi.',
            $this->username() . '.email'    => 'Format email tidak valid.',
            'password.required'             => 'Password wajib diisi.',
        ]);
    }
}

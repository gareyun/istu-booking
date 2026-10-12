<?php

namespace App\Http\Controllers;

use App\Services\VkNotificationService;
use App\Services\WebActivationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Password;

class WebActivationController extends Controller
{
    public function __construct(
        protected WebActivationService $activation,
        protected VkNotificationService $vk,
    ) {
    }

    public function showRequest()
    {
        return view('auth.activate');
    }

    public function requestCode(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'string', 'email', 'max:255'],
        ], [
            'email.required' => 'Укажите электронную почту.',
            'email.email' => 'Введите корректный адрес электронной почты.',
        ]);

        $this->activation->requestCode($data['email']);

        return redirect()
            ->route('activate.code')
            ->with('activation_email', mb_strtolower(trim($data['email'])))
            ->with('status', 'Если для этой почты есть учётная запись, привязанная к VK, '
                . 'бот отправил в личные сообщения код активации.');
    }

    public function showCode()
    {
        return view('auth.activate-code', [
            'email' => session('activation_email', old('email')),
        ]);
    }

    public function confirm(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'string', 'email', 'max:255'],
            'code' => ['required', 'digits:6'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ], [
            'email.required' => 'Укажите электронную почту.',
            'email.email' => 'Введите корректный адрес электронной почты.',
            'code.required' => 'Введите код из сообщения бота.',
            'code.digits' => 'Код состоит из 6 цифр.',
            'password.required' => 'Придумайте пароль.',
            'password.confirmed' => 'Пароли не совпадают.',
            'password.min' => 'Пароль должен содержать не менее 8 символов.',
        ]);

        $result = $this->activation->activate($data['email'], $data['code'], $data['password']);

        if (!$result['ok']) {
            return back()
                ->withErrors(['code' => $result['error']])
                ->onlyInput('email');
        }

        $user = $result['user'];

        Auth::login($user);
        $request->session()->regenerate();

        if ($user->vk_id) {
            try {
                $this->vk->sendMessage(
                    (string) $user->vk_id,
                    "🔓 Веб-доступ активирован. Теперь вы можете входить на сайт по почте и паролю. "
                    . "Если это были не вы, срочно сообщите администратору группы СЦ Интеграл."
                );
            } catch (\Throwable $e) {
                Log::error('VK notify after web activation failed: ' . $e->getMessage());
            }
        }

        if ($user->role === 'admin') {
            return redirect()->route('admin.index')->with('success', 'Веб-доступ активирован.');
        }

        return redirect()->route('booking')->with('success', 'Веб-доступ активирован.');
    }
}

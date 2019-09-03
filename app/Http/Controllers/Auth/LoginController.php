<?php

namespace App\Http\Controllers\Auth;

use Auth;
use App\User;
use App\Message;

use App\Events\ShowUserList;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/chat';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLoginForm()
    {
        $users = User::where([
            ['is_online', false]
        ])->get();

        return view('auth.login', compact('users'));
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(Request $request)
    {
        $this->validateLogin($request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if (method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        // Check if User already Online
        $check_user = User::where('email', $request->email)->first();
        if($check_user->is_online){
            return redirect('/login')->with([
                'status' => 'warning',
                'message' => $check_user->name.' already logged in, please use other account instead!'
            ]);
        }

        if ($this->attemptLogin($request)) {
            $user = Auth::user();

            $user->is_online = true;
            $user->save();

            $unseen = Message::where([
                ['from_user', $user->id],
                ['is_seen', false]
            ])->get();

            $data = [
                'id' => $user->id,
                'name' => $user->name,
                'trigger' => 'login',
                'unseen' => $unseen
            ];
            event(new ShowUserList($data));
            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $user = Auth::user();
        $user->is_online = false;
        $user->save();

        $unseen = Message::where([
            ['from_user', $user->id],
            ['is_seen', false]
        ])->get();

        $data = [
            'id' => $user->id,
            'name' => $user->name,
            'trigger' => 'logout',
            'unseen' => $unseen
        ];
        event(new ShowUserList($data));

        $this->guard()->logout();

        $request->session()->invalidate();

        return $this->loggedOut($request) ?: redirect('/');
    }
}

<?php
  
namespace App\Http\Controllers\Auth;
  
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
  
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
    protected $redirectTo = RouteServiceProvider::HOME;
  
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
 
    public function login(Request $request)
    {   
        $input = $request->all();

        // Check if NIK exists
        $user = DB::table('users')->where('nik', $input['nik'])->first();

        if (!$user) {
            return redirect()->back()->with('error', 'NIK not found.');
        }

        // Check if password is correct
        if (auth()->attempt(['nik' => $input['nik'], 'password' => $input['password']])) {
            
            if(auth()->user()->role == 'hashmicro'){
                return redirect()->route('hashmicro.index');
            }else{
                return redirect()->route('dashboard');
            }
        } else {
            return redirect()->back()->with('error', 'Password is wrong.');
        }
          
    }
}
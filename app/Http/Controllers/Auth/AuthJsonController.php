<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;

use App\Coordinate;
use App\User;
use Validator;
use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Auth;

class AuthJsonController extends AuthController
{
  /**
   * Get a validator for an incoming registration request.
   *
   * @param  array  $data
   * @return \Illuminate\Contracts\Validation\Validator
   */
  protected function validator(array $data)
  {
      return Validator::make($data, [
          'lastName' => 'required|max:255',
          'firstName' => 'required|max:255',
          'address' => 'required|max:255',
          'country' => 'required|max:100',
          'phone' => 'required|max:30',
          'postalCode' => 'required|max:20',
          'email' => 'required|email|max:255|unique:users',
          'password' => 'required|min:6|confirmed',
      ]);
  }

  /**
   * Create a new user instance after a valid registration.
   *
   * @param  array  $data
   * @return User
   */
  protected function create(array $data)
  {
      $coord = Coordinate::create([
        'address' => $data['address'],
        'country' => $data['country'],
        'phone' => $data['phone'],
        'postal_code' => $data['postalCode']
      ]);
      return User::create([
          'lastname' => $data['lastName'],
          'firstname' => $data['firstName'],
          'email' => $data['email'],
          'role_id' => 2,
          'coordinate_id' => $coord['id'],
          'password' => bcrypt($data['password']),
      ]);
  }

  public function handleUserWasAuthenticated(Request $request, $throttles)
  {
      if ($throttles) {
          $this->clearLoginAttempts($request);
      }

      if (method_exists($this, 'authenticated')) {

          return $this->authenticated($request, Auth::guard($this->getGuard())->user());
      }
      return response()->json(['status' => 'success', 'userData' => Auth::user()]);
      //return redirect()->route('apiConnect')->with('status','success')->with('userData', Auth::user());

  }

  protected function sendFailedLoginResponse(Request $request)
  {
    return response()->json(['status' => 'fail', 'errorMessage' => $this->getFailedLoginMessage()]);
    /*  return redirect()
          ->route('apiConnect')
          ->withInput($request->only($this->loginUsername(), 'remember'))
          ->withErrors([
              $this->loginUsername() => $this->getFailedLoginMessage(),
          ])
          ->with('status','fail');*/
  }

  protected function sendLockoutResponse(Request $request)
  {
      $seconds = $this->secondsRemainingOnLockout($request);
      return response()->json(['status' => 'fail', 'errorMessage' => $this->getLockoutErrorMessage($seconds)]);
      /*return redirect()
          ->route('apiConnect')
          ->withInput($request->only($this->loginUsername(), 'remember'))
          ->withErrors([
                  $this->loginUsername() => $this->getLockoutErrorMessage($seconds),
          ])
          ->with('status','fail');*/
  }
}

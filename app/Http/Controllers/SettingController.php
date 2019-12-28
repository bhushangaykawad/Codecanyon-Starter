<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SettingController extends Controller
{
	public function __construct()
	{
		$this->middleware('auth');
	}

	public function userSettings()
	{
		return view('settings.user');
	}

	public function patchUserSettings(Request $request)
	{
		$validatedData = $request->validate([
			'name' => ['required', 'string', 'max:191'],
			'email' => ['required', 'string', 'email', 'max:191', 'unique:users,email,' . auth()->user()->id],
		]);

		if ($validatedData['email'] != auth()->user()->email) {
			auth()->user()->update(['email_verified_at' => null]);
		}

		auth()->user()->update($validatedData);

		return redirect(route('user-settings'))->with('status', 'User Settings Updated Successfully');
	}

	public function patchUserPassword(Request $request)
	{
		$validatedData = $request->validate([
			'current_password' => [
				'required', 'string', 'min:8',
				function ($attribute, $value, $fail) {
					if (!\Hash::check($value, auth()->user()->password)) {
						return $fail(__('The current password is incorrect.'));
					}
				}
			],
			'password' => ['required', 'string', 'min:8', 'confirmed'],
		]);

		auth()->user()->update(['password' => bcrypt($validatedData['password'])]);

		return redirect(route('user-settings'))->with('status', 'Password Updated Successfully');
	}
}
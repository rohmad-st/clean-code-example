<?php

/**
* Class for enroll an user
* then after registered success
* redirect to url
*/
class EnrollmentController extends Controller
{
	
	/**
	 * Below until before next methods with comment is
	 * methods for creating an user before we implement clean code
	 */
	public function store(CourseEnrollmentRequest $request, Course $course)
	{
		if (auth()->check()) {
			$course->enrollUser(auth()->user());

			return redirect($course->path())
		}

		$user = User::create([
			'first_name' => $request->input('first_name'),
			'last_name' => $request->input('last_name'),
			'email' => $request->input('email'),
			'password' => $request->input('password'),
		]);

		$course->organization->addUser($user);
		$course->enrollUser($user);
		Auth::login($user);

		return redirect($course->path());
	}

	/**
	 * Below is great methods
	 * for creating an user after we implement clean code
	 */
	public function store(CourseEnrollmentRequest $request, Course $course)
	{
		// Keep focus on primary task (i.e. enrollment)
		$course->enrollUser($this->createUserIfUnauthenticated());

		return redirect($course->path());
	}

	private function createUserIfUnauthenticated(CourseEnrollmentRequest $request) {
		// Leverage a single dependency for ease of testing
		if ($request->user()) {
			return $request->user();
		}

		// Keep User class responsible for creating with 
		// simple "creation method"
		$user = User::createFromEnrollmentRequest($request);
		$course->organization->addUser($user);
		Auth::login($user);

		return $user;
	}

}
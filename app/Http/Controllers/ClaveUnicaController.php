<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ClaveUnicaController extends Controller
{
    /**
	* Login Clave única
	*/
	public function login()
	{
		app('log')->notice("test");

		return "en login clave única";

	}
}

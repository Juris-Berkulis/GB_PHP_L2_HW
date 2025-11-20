<?php

namespace JurisBerkulis\GbPhpL2Hw\Http\Actions;

use JurisBerkulis\GbPhpL2Hw\Http\Request;
use JurisBerkulis\GbPhpL2Hw\Http\Response;

interface ActionInterface
{

    public function handle(Request $request): Response;

}

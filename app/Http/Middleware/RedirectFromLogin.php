<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RedirectFromLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $login_path_flg = $request->path() == 'login';
        $x_login_referer_flg = $request->hasHeader('X-Login-Referer', false);
        $login_referer = $request->session()->pull('login_referer');

        // 「/login」にGETでアクセス かつ 「X-Login-Referer」が存在するとき
        if ($login_path_flg && $x_login_referer_flg) {
            $referer = $request->header('Referer');
            $request->session()->put('login_referer', $referer);

            return $next($request);
        }
        // 「/login」でアクセス かつ 「login_referer」セッションが存在するとき
        else if ($login_path_flg && !!$login_referer) {
            $next($request);
            return redirect($login_referer);
        }
        // 何も処理を加えない
        else {
            return $next($request);
        }
    }
}

<?php
final class Sessions {
     public static function init() {
          ini_set("session.use_cookies", true);
          ini_set("session.use_only_cookies", true);
          ini_set("session.use_trans_sid", false);
          ini_set("session.cookie_httponly", true);
          ini_set("session.cache_limiter", "nocache");
          ini_set("session.cookie_lifetime", 0);
          session_start();
     }

     public static function clearSession()
	{
          session_unset();
		session_destroy();
	}
}

<?php

namespace ultimo\util\session\mvc;

use ultimo\util\session\SessionHandler as SessionHandlerAbstract;
use ultimo\mvc\Response;
use ultimo\mvc\Request;

class SessionHandler extends SessionHandlerAbstract {
  
  /**
   *
   * @var SessionHandlerAbstract 
   */
  protected $sessionHandler;
  
  /**
   *
   * @var Response 
   */
  protected $response;
  
  protected $request;
  
  protected $iniUseCookies;
  
  protected $sessionName;
  
  public function __construct(SessionHandlerAbstract $sessionHandler, Request $request, Response $response) {
    $this->sessionHandler = $sessionHandler;
    $this->request = $request;
    $this->response = $response;
  }
  
  public function open($savePath, $sessionName) {
    // store original use cookie
    $this->iniUseCookies = ini_get('session.use_cookies');
    
    if ($this->iniUseCookies) {
      // use session id from cookie from request
      $sessionId = $this->request->getCookieValue($sessionName);
      if ($sessionId !== null) {
        session_id($sessionId);
      }
    }
    
    // disable cookie, so cookie object can be set on response
    ini_set('session.use_cookies', false);
    $this->sessionName = $sessionName;
    return $this->sessionHandler->open($savePath, $sessionName);
  }
  
  protected function setCookie($sessionName, $sessionId) {
    $params = session_get_cookie_params();
    $name = $sessionName;
    $value = $sessionId;
    $path = $params['path'];
    if ($params['lifetime'] == 0) {
      $expire = 0;
    } else {
      $expire = $params['lifetime'] + time();
    }
    $domain = $params['domain'];
    $secure = $params['secure'];
    $httpOnly = $params['httponly'];
    
    $cookie = new \ultimo\net\http\headers\SetCookie($name, $value, $expire, $path, $domain, $secure, $httpOnly);
    $this->response->addHeader($cookie);
  }

  public function close() {
    // restore original cookie value
    ini_set('session.use_cookies', $this->iniUseCookies);
    return $this->sessionHandler->close();
  }

  public function read($id) {
    if ($this->iniUseCookies) {
      $this->setCookie($this->sessionName, $id);
    }
    
    return $this->sessionHandler->read($id);
  }

  public function write($id, $data) {
    return $this->sessionHandler->write($id, $data);
  }

  public function destroy($id) {
    return $this->sessionHandler->destroy($id);
  }

  public function gc($maxlifetime) {
    return $this->sessionHandler->gc($maxlifetime);
  }
  
  public function register() {
    return parent::register();
  }
}
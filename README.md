# Ultimo Session MVC
Easy integration for custom session handling in Ultimo MVC

## Requirements
* PHP 5.3
* Ultimo Session
* Ultimo MVC

## Usage
	ini_set('session.save_path', __DIR__ . DIRECTORY_SEPARATOR . 'sessions');
    $sessionHandler = new \ultimo\util\session\mvc\SessionHandler(
      new \ultimo\util\session\FileSessionHandler(),
      $application->getRequest(),
      $application->getResponse()
    );
    $sessionHandler->register();
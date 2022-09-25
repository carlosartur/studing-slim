<?php

namespace App\Controller;

use Slim\App;
use ReflectionClass;
use ReflectionMethod;
use ReflectionParameter;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

abstract class AutoRotingController
{
    protected const ROUTE_PREFIX = "";

    protected const ROUTE_FIXED = "";

    protected const GET_ACTION_METHOD = 'getAction';

    protected const POST_ACTION_METHOD = 'postAction';

    protected const PUT_ACTION_METHOD = 'putAction';

    protected const DELETE_ACTION_METHOD = 'deleteAction';

    protected const OPTIONS_ACTION_METHOD = 'optionsAction';

    protected const PATCH_ACTION_METHOD = 'patchAction';

    protected const HTTP_METHOD_GET = 'GET';

    protected const HTTP_METHOD_POST = 'POST';

    protected const HTTP_METHOD_PUT = 'PUT';

    protected const HTTP_METHOD_DELETE = 'DELETE';

    protected const HTTP_METHOD_OPTIONS = 'OPTIONS';

    protected const HTTP_METHOD_PATCH = 'PATCH';

    public const TYPES_REGEX = [
        "int" => '[0-9]+',
        "float" => '[+-]?([0-9]*[.])?[0-9]+',
    ];

    public static $generatedRoutes = [];

    /**
     * Generate slim routes, based on name of controller's methods, using this convencion:
     * getAction     - Generate a get route
     * postAction    - Generate a post route
     * putAction     - Generate a put route
     * deleteAction  - Generate a delete route
     * optionsAction - Generate a options route
     * patchAction   - Generate a patch route
     * 
     * @param App $app
     * @return void
     */
    final public static function generateRoutes(App $app)
    {
        $reflection = new ReflectionClass(static::class);
        $prefix = static::getRoutePrefix($reflection);

        $publicMethods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);
        foreach ($publicMethods as $method) {
            self::buildRoute($method, $app, $prefix);
        }
    }

    /**
     * Create a single route or not, based on current method name.
     *
     * @param ReflectionMethod $method
     * @param App $app
     * @param string $prefix
     * @return bool - returns true if a route is crated, otherwise, false
     */
    private static function buildRoute(ReflectionMethod $method, App $app, string $prefix): bool
    {
        $path = self::buildRoutePath($method, $prefix);

        $methodName = $method->getName();

        $actualClassName = static::class;

        $nameUcfirst = ucfirst($methodName);
        $callable = "{$actualClassName}:callMethod{$nameUcfirst}";

        $isRouteMethod = false;
        $httpMethod = null;

        switch ($methodName) {
            case static::GET_ACTION_METHOD:
                $app->get($path, $callable);
                $isRouteMethod = true;
                $httpMethod = self::HTTP_METHOD_GET;
                break;

            case static::POST_ACTION_METHOD:
                $app->post($path, $callable);
                $isRouteMethod = true;
                $httpMethod = self::HTTP_METHOD_POST;
                break;

            case static::PUT_ACTION_METHOD:
                $app->put($path, $callable);
                $isRouteMethod = true;
                $httpMethod = self::HTTP_METHOD_PUT;
                break;

            case static::DELETE_ACTION_METHOD:
                $app->delete($path, $callable);
                $isRouteMethod = true;
                $httpMethod = self::HTTP_METHOD_DELETE;
                break;

            case static::OPTIONS_ACTION_METHOD:
                $app->options($path, $callable);
                $isRouteMethod = true;
                $httpMethod = self::HTTP_METHOD_OPTIONS;
                break;

            case static::PATCH_ACTION_METHOD:
                $app->patch($path, $callable);
                $isRouteMethod = true;
                $httpMethod = self::HTTP_METHOD_PATCH;
                break;
        }

        if ($isRouteMethod) {
            static::$generatedRoutes[] = compact('path', 'callable', 'methodName', 'httpMethod');
        }

        return $isRouteMethod;
    }

    /**
     * Returns the path of the route, based on method's parameters and name of the class.
     * A prefix can be defined on ROUTE_PREFIX constant, or all path, excluding parameters,
     * can be defined on ROUTE_FIXED constant.
     * Parameters regexes validators is defined by type.
     * To customise these regexes, overwrite TYPES_REGEX constant.
     *
     * @param ReflectionMethod $method
     * @param string $prefix
     * @return string
     */
    private static function buildRoutePath(ReflectionMethod $method, string $prefix): string
    {
        $routeString = "/{$prefix}";
        $parameters = $method->getParameters();
        /** @var ReflectionParameter */
        foreach ($parameters as $parameter) {
            $typeString = (string) $parameter->getType();
            if (in_array($typeString, [Response::class, Request::class])) {
                continue;
            }

            $regex = "";
            if (array_key_exists($typeString, static::TYPES_REGEX)) {
                $regex = ':' . static::TYPES_REGEX[$typeString];
            }

            $format = $parameter->allowsNull() ? '[/{%s%s}]' : '/{%s%s}';
            $routeString .= sprintf($format, $parameter->getName(), $regex);
        }
        return $routeString;
    }

    /**
     * Get prefix of the route, based on class name or ROUTE_FIXED constant.
     *
     * @param ReflectionClass $reflection
     * @return string
     */
    private static function getRoutePrefix(ReflectionClass $reflection): string
    {
        if (static::ROUTE_FIXED) {
            return static::ROUTE_FIXED;
        }

        $snakeCase = ltrim(strtolower(preg_replace('/[A-Z]/', '_$0', $reflection->getShortName())), '_');
        return static::ROUTE_PREFIX . str_replace("_controller", "", $snakeCase);
    }

    /**
     * Call method on child's class
     *
     * @param string $method
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return void
     */
    public function callRouteMethod(string $method, Request $request, Response $response, array $args)
    {
        $arguments = array_merge(compact('request', 'response'), $args);
        return call_user_func_array([$this, $method], $arguments);
    }

    /**
     * Call GET method
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return void
     */
    public function callMethodGetAction(Request $request, Response $response, array $args)
    {
        return $this->callRouteMethod(static::GET_ACTION_METHOD, $request, $response, $args);
    }

    /**
     * Call POST method
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return void
     */
    public function callMethodPostAction(Request $request, Response $response, array $args)
    {
        return $this->callRouteMethod(static::POST_ACTION_METHOD, $request, $response, $args);
    }

    /**
     * Call PUT method
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return void
     */
    public function callMethodPutAction(Request $request, Response $response, array $args)
    {
        return $this->callRouteMethod(static::PUT_ACTION_METHOD, $request, $response, $args);
    }

    /**
     * Call DELETE method
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return void
     */
    public function callMethodDeleteAction(Request $request, Response $response, array $args)
    {
        return $this->callRouteMethod(static::DELETE_ACTION_METHOD, $request, $response, $args);
    }

    /**
     * Call OPTIONS method
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return void
     */
    public function callMethodOptionsAction(Request $request, Response $response, array $args)
    {
        return $this->callRouteMethod(static::OPTIONS_ACTION_METHOD, $request, $response, $args);
    }

    /**
     * Call PATCH method
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return void
     */
    public function callMethodPatchAction(Request $request, Response $response, array $args)
    {
        return $this->callRouteMethod(static::PATCH_ACTION_METHOD, $request, $response, $args);
    }
}

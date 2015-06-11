<?

namespace Kicker\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;

class FrontendController
{
    protected $app;
    public function __construct($app) {
        $this->app = $app;
    }

    public function generationAction()
    {
        return $this->app['twig']->render('generator.html.twig', ['players' => []]);
    }
}
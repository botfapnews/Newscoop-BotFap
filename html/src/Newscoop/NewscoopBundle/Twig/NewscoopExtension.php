<?php
/**
 * @package Newscoop\NewscoopBundle
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */
namespace Newscoop\NewscoopBundle\Twig;

use Newscoop\Exception\AuthenticationException;

class NewscoopExtension extends \Twig_Extension
{

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var Request
     */
    protected $request;

    /**
     * Constructor
     *
     * @param ContainerInterface $container
     */
    public function __construct($container)
    {
        $this->container = $container;

        if ($this->container->isScopeActive('request')) {
            $this->request = $this->container->get('request');
        }
    }

    public function getGlobals()
    {
        global $Campsite;

        $localeFromCookie = 'en';
        if ($this->request) {
            $localeFromCookie = $this->request->cookies->has('TOL_Language') == true ? $this->request->cookies->get('TOL_Language') : 'en';
        }

        try {
            $currentUser = $this->container->getService('user')->getCurrentUser();
        } catch (AuthenticationException $e) {
            $currentUser = null;
        }

        return array(
            'Newscoop' => $Campsite,
            'NewscoopVersion' => new \CampVersion(),
            'SecurityToken' => \SecurityToken::GetToken(),
            'NewscoopUser' => $currentUser,
            'localeFromCookie' => $localeFromCookie,
        );
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('strpos', 'strpos'),
            new \Twig_SimpleFunction('getBreadcrumbsArray', array($this, 'getBreadcrumbsArray')),
            new \Twig_SimpleFunction('getReCaptchaImage', array($this, 'getReCaptchaImage')),
            new \Twig_SimpleFunction('renderHook', array($this, 'renderHook')),
            new \Twig_SimpleFunction('getSystemPref', "\Zend_Registry::get('container')->getService('system_preferences_service')->get"),
            new \Twig_SimpleFunction('generateZendUrl', array($this, 'generateZendUrl')),
            new \Twig_SimpleFunction('hasPermission', array($this, 'hasPermission')),
            new \Twig_SimpleFunction('default_csrf_token', array($this, 'getCsrfToken')),
            new \Twig_SimpleFunction('renderTip', array($this, 'renderTip'), array('is_safe' => array( 'html' ))),
        );
    }

    public function getBreadcrumbsArray($currentMenuItem)
    {
        $manipulator = new \Knp\Menu\Util\MenuManipulator();

        return $manipulator->getBreadcrumbsArray($currentMenuItem);
    }

    public function generateZendUrl($controller, $action, $module = 'admin', $params = array(), $name = 'default', $encode = true)
    {
        $zendRouter = $this->container->get('zend_router');

        return $zendRouter->assemble(array_merge(array(
            'module' => $module,
            'controller' => $controller,
            'action' => $action,
        ), $params), $name, true, $encode);
    }

    public function getReCaptchaImage()
    {
        $fontsDirectory = __DIR__.'/../../../../include/captcha/';
        $aFonts = array(
            $fontsDirectory.'fonts/VeraBd.ttf',
            $fontsDirectory.'fonts/VeraIt.ttf',
            $fontsDirectory.'fonts/Vera.ttf',
        );
        $oVisualCaptcha = new \PhpCaptcha($aFonts, 200, 60);
        $oVisualCaptcha->Create(__DIR__.'/../../../../images/cache/recaptcha.png');

        return '/images/cache/recaptcha.png';
    }

    public function renderHook($hookName, $params = array())
    {
        echo $this->container->get('newscoop.plugins.service')
            ->renderPluginHooks($hookName, null, $params);
    }

    /**
     * Checks if user has permission
     *
     * @param string $permissionName Permission name
     *
     * @return boolean
     */
    public function hasPermission($permissionName = null)
    {
        $user = $this->container->get('user')->getCurrentUser();
        if ($user->hasPermission($permissionName)) {
            return true;
        }

        return false;
    }

    /**
     * Generates default CRSF token for forms using AJAX
     *
     * @return string CSRF token
     */
    public function getCsrfToken()
    {
        $csrfProvider = $this->container->get('form.csrf_provider');

        return $csrfProvider->generateCsrfToken('default');
    }

    public function renderTip($tipMessage, $tipCode = null)
    {
        return $this->container->get('templating')->render('NewscoopNewscoopBundle::tooltip.html.twig', array(
            'tipMessage' => $tipMessage,
            'tipCode' => $tipCode,
        ));
    }

    public function getName()
    {
        return 'newscoop_extension';
    }
}

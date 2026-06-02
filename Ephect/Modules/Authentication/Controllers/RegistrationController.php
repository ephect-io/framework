<?php

namespace Ephect\Plugins\Authentication\Controllers;

use Ephect\Framework\Http\RedirectResponse;
use Ephect\Framework\Http\Response;
use Ephect\Framework\MVC\AbstractController;
use Ephect\Plugins\Authentication\Components\Authenticator;
use Ephect\Plugins\Authentication\Forms\RegistrationForm;
use Ephect\Plugins\Authentication\Repositories\UserMapper;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class RegistrationController extends AbstractController
{
    public function __construct(
        private readonly UserMapper $userMapper,
        private readonly Authenticator $authenticator,
    ) {
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function index(): Response
    {
        return $this->render('register.html.twig');
    }

    public function register(): Response
    {
        $form = new RegistrationForm($this->userMapper);
        $form->setFields(
            $this->request->searchFromBody('email'),
            $this->request->searchFromBody('password'),
        );

        if ($form->hasValidationErrors()) {
            foreach ($form->getValidationErrors() as $error) {
                $this->request->getFlashMessage()->setError($error);
            }

            return new RedirectResponse('/register');
        }

        $user = $form->save();

        $this->request->getFlashMessage()->setSuccess(
            'User %s created', $user->getEmail()
        );

        $this->authenticator->login($user);

        return new RedirectResponse('/dashboard');
    }

}

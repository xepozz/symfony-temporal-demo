<?php
declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class HomeController
{
    #[Route("/")]
    public function action(UrlGeneratorInterface $urlGenerator)
    {
        $urlParameters = ['name' => 'World'];
        $simpleActionUrl = $urlGenerator->generate('simple', $urlParameters, UrlGeneratorInterface::ABSOLUTE_URL);
        $complicatedActionUrl = $urlGenerator->generate('complicated', $urlParameters, UrlGeneratorInterface::ABSOLUTE_URL);
        $asynchronousActionUrl = $urlGenerator->generate('asynchronous', $urlParameters, UrlGeneratorInterface::ABSOLUTE_URL);

        return new Response(
            <<<HTML
            This project is example how to use Temporal with Symfony application.
            <br>
            There are exist several examples how it works.
            <br>
            <h3>Examples:</h3>
            If you want to see how "simple" workflow works <a href="{$simpleActionUrl}" target="_blank">click here</a>.
            <br>
            There is usual call non-blocking action. It should work as faster as you can run usual php code.
            <br>
            If you want to see how "complicated" workflow works <a href="{$complicatedActionUrl}" target="_blank">click here</a>.
            <br>
            There are imitation for blocking action. Different methods calls will run with random delay: from 1 to 5 seconds per call.
            <br>
            If you want to see how "deferred" workflow works <a href="{$asynchronousActionUrl}" target="_blank">click here</a>.
            <br>
            There are imitation for asynchronous action. You will get "job id" and you can track the status in another endpoint.<br>
            Logic for workflow will be same as "complicated" workflow. 
            <br>
            HTML
        );
    }
}
<?php


namespace App\Controllers;


class Home extends \Core\Controller
{
    public function index()
    {
        $loader = new \Twig\Loader\FilesystemLoader($GLOBALS['template_directory']);
        $twig = new \Twig\Environment($loader, [
            'cache' => $GLOBALS['template_directory'].DIRECTORY_SEPARATOR.'cache',
        ]);

        echo $twig->render('index.twig', ['name' => 'Maysam']);

//        echo "Home Index";
    }

}
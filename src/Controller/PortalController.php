<?php
declare(strict_types=1);

namespace App\Controller;

use App\Domain\LanguageTrait;
use App\Domain\LearningModuleType;
use App\Entity\Language;
use App\Entity\LearningModule;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PortalController extends AbstractController
{
    use LanguageTrait;

    /**
     * @Route("/portal", name="portal")
     */
    public function index(Request $request): Response
    {
        $modules = !isset($_GET['mode'])?
            $this->getDoctrine()->getRepository(LearningModule::class)->findBy(['isPublished' => true])
            : $this->getDoctrine()->getRepository(LearningModule::class)->findBy([
                'isPublished' => true,
                'type' => strtoupper($_GET['mode'])
            ]);

        $activeModules = $finishedModules = [];

        /** @var User $user */
        $user = $this->getUser();

        foreach ($user->getActiveModules($modules) as $module) {
            $activeModules[] = $module;
        }
        foreach ($user->getFinishedModules($modules) as $module) {
            $finishedModules[] = $module;
        }


        return $this->render('portal/index.html.twig', [
            'language' => $this->getLanguage($request),
            'activeModules' => $activeModules,
            'finishedModules' => $finishedModules,
        ]);
    }
}

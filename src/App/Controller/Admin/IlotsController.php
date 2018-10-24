<?php
namespace App\Controller\Admin;

use App\Entity\Doctrine\DepotEntity;
use App\Entity\Doctrine\IlotEntity;
use App\Entity\Doctrine\PlanningEntity;
use App\Entity\Doctrine\PlanningIlotEntity;
use App\Entity\Doctrine\SerieEntity;
use App\Utils\HexaColor;
use Core\Controller\Controller;
use Doctrine\Common\Util\Debug;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Psr\Http\Message\ResponseInterface;
use Slim\Exception\NotFoundException;
use Slim\Http\Request;
use Slim\Http\Response;

class IlotsController extends Controller
{
    /**
     * @var string
     */
    protected $menu_item = 'ilots';

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return ResponseInterface
     */
    public function indexAction(Request $request, Response $response): ResponseInterface
    {
        /**
         * @var IlotEntity[] $ilots
         */
        $ilots = $this->getRepository(IlotEntity::class)->findAll();

        $depots = [];
        $ilotsByDepot = [];

        foreach ($ilots as $ilot) {
            $depot_id = $ilot->getLocation()->getId();
            $ilotsByDepot[$depot_id][] = $ilot;

            if (array_key_exists($depot_id, $depots) === false) {
                $depots[$depot_id] = $ilot->getLocation();
            }
        }

        ksort($depots);

        return $this->render($response, 'admin.ilots.index', [
            'ilots' => $ilots,
            'depots' => $depots,
            'ilotsByDepot' => $ilotsByDepot
        ]);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return ResponseInterface
     */
    private function updateIlot(Request $request, Response $response): ResponseInterface
    {
        $statusCode = 400;

        if ($request->isPost() || $request->isPut()) {
            $ilot_id = $request->getParsedBodyParam('ilot_id', 0);
            $name = $request->getParsedBodyParam('name', null);
            $label = $request->getParsedBodyParam('label', null);
            $color = $request->getParsedBodyParam('color', null);
            $location = $request->getParsedBodyParam('location', null);
            $planningsToAdd = $request->getParsedBodyParam('plannings', []);
            $dimensions = $request->getParsedBodyParam('dimensions', []);

            // on veut une modif
            if ($request->isPut()) {
                /**
                 * @var IlotEntity $ilot
                 */
                $ilot = $this->getRepository(IlotEntity::class)->findOneBy(['id' => $ilot_id]);

                if ($ilot instanceof IlotEntity === false) {
                    $this->addMessage('danger', "Cet îlot n'existe pas");
                }
            } else {
                // nouvel îlot
                $ilot = (new IlotEntity());
            }

            if (!empty($planningsToAdd) || $ilot->getPlannings()->isEmpty() === false) {
                /**
                 * @var PlanningEntity[] $plannings
                 */
                $plannings = $this->getRepository(PlanningEntity::class)->findAll();

                foreach ($plannings as $planning) {
                    if (in_array($planning->getId(), $planningsToAdd)) {
                        $ilot->attachPlanning($planning);
                    } else {
                        $ilot->detachPlanning($planning);
                    }
                }
            }

            /*echo "<pre>";
            Debug::dump($ilot->getPlannings(), 3);
            echo "</pre>";

            exit;*/

            /**
             * @var DepotEntity $depot
             */
            $depot = $this->getRepository(DepotEntity::class)->find($location);

            try {
                $hexaColor = (new HexaColor())
                    ->setColor($color);

                $ilot->setName($name)
                    ->setLabel($label)
                    ->setColor($hexaColor->getColor())
                    ->setLocation($depot)
                    ->setDimensions($dimensions);

                $errors = $this->persist($ilot);

                if ($errors === null) {
                    if ($request->isPut()) {
                        $this->addMessage('success', "L'Îlot {$ilot} a été modifié avec succès");
                    } else {
                        $this->addMessage('success', "L'Îlot {$ilot} a été créé avec succès");
                    }

                    $statusCode = 200;
                } else {
                    $this->addValidatorMessages($errors);
                }
            } catch (\TypeError $e) {
                $this->addMessage('warning', "Veuillez vérifier les données saisies dans le formulaire");
            } catch (UniqueConstraintViolationException $e) {
                $this->addMessage('danger', "Un autre îlot est déjà identifié par {$ilot->getName()}");
            }
        }

        return $response->withStatus($statusCode);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return ResponseInterface
     */
    public function createAction(Request $request, Response $response): ResponseInterface
    {
        if ($request->isPost()) {
            $done = $this->updateIlot($request, $response);
            return $this->withRedirect($done, 'admin.ilots.create');
        }

        /**
         * @var DepotEntity[] $depots
         * @var IlotEntity[] $ilots
         */
        $depots = $this->getRepository(DepotEntity::class)->findAll();
        $ilots = $this->getRepository(IlotEntity::class)->findAll();
        $plannings = $this->getRepository(PlanningEntity::class)->findAll();

        $form = [
            'action' => (string) $request->getUri(),
            'method' => 'POST',
            'xmethod' => 'POST',
            'context' => 'create',
            'contextMessage' => "Créer",
            'fields' => ['name' => 'required']
        ];

        return $this->render($response, 'admin.ilots.create', compact('depots', 'plannings', 'ilots', 'form'));
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param string $ilot_name
     *
     * @return ResponseInterface
     * @throws NotFoundException
     */
    public function editAction(Request $request, Response $response, string $ilot_name): ResponseInterface
    {
        /**
         * @var IlotEntity $ilot
         */
        $ilot = $this->getRepository(IlotEntity::class)->findOneBy(['name' => $ilot_name]);

        if ($ilot instanceof IlotEntity === false) {
            throw new NotFoundException($request, $response);
        }

        if ($request->isPut()) {
            $done = $this->updateIlot($request, $response);

            // ilot renommé ?
            $redirectTo = (($done->getStatusCode() === 200) && ($ilot->getName() !== $ilot_name)) ? $ilot->getName() : $ilot_name;

            return $this->withRedirect($done, 'admin.ilots.edit', ['ilot_name' => $redirectTo]);
        }

        /**
         * @var DepotEntity[] $depots
         * @var SerieEntity[] $series
         */
        $depots = $this->getRepository(DepotEntity::class)->findAll();

        $ilotPlannings = $ilot->getPlannings();
        $ilotPlanningsIds = [];

        foreach ($ilotPlannings as $ilotPlanning) {
            $ilotPlanningsIds[] = $ilotPlanning->getId();
        }

        if (!empty($ilotPlanningsIds)) {
            $plannings = $this->getRepository(PlanningEntity::class)->createQueryBuilder('pln')
                ->where('pln.id NOT IN (:ilots_plannings)')
                ->setParameter(':ilots_plannings', $ilotPlanningsIds)
                ->getQuery()->getResult();

            foreach ($ilotPlannings as $ilotPlanning) {
                array_push($plannings, $ilotPlanning);
            }
        } else {
            $plannings = $this->getRepository(PlanningEntity::class)->findAll();
        }

        $form = [
            'action' => (string) $request->getUri(),
            'method' => 'POST',
            'xmethod' => 'PUT',
            'context' => 'edit',
            'contextMessage' => "Modifier",
            'data' => $ilot
        ];

        return $this->render($response, 'admin.ilots.edit', [
            'ilot' => $ilot,
            'form' => $form,
            'depots' => $depots,
            'plannings' => $plannings,
            'ilotPlannings' => $ilotPlanningsIds
        ]);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param string $ilot_name
     *
     * @return ResponseInterface
     * @throws NotFoundException
     */
    public function deleteAction(Request $request, Response $response, string $ilot_name): ResponseInterface
    {
        /**
         * @var IlotEntity $ilot
         */
        $ilot = $this->getRepository(IlotEntity::class)->findOneBy(['name' => $ilot_name]);
        $oldIlot = clone $ilot;

        if ($ilot instanceof IlotEntity === false) {
            throw new NotFoundException($request, $response);
        }

        try {
            $this->getDoctrine()->remove($ilot);
            $this->getDoctrine()->flush();
        } catch (\Exception $e) {
            return $response->withJson(['error' => "L'Îlot ne peut pas être supprimé car il est utilisé ailleurs"], 500);
        }

        return $response->withJson($oldIlot);
    }
}
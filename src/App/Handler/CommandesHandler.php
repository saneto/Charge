<?php

namespace App\Handler;
use App\Entity\Doctrine;
use App\Manager\CommandesManager;
use App\Provider\AclProvider;
use App\Provider\AppProvider;
use App\Provider\AuthProvider;
use App\Twig\TwigExtensions;
use Core\Controller\Controller;
use Doctrine\ORM\Tools\Pagination\Paginator;
use FL\QBJSParser\Parser\Doctrine\DoctrineParser;
use FL\QBJSParser\Serializer\JsonDeserializer;
use Psr\Http\Message\ResponseInterface;
use Slim\Exception\MethodNotAllowedException;
use Slim\Exception\NotFoundException;
use Slim\Http\Request;
use Slim\Http\Response;
use Zend\Permissions\Acl\Acl;

class CommandesHandler 
{
	public function __construct()
    {
        
    }

	function getCommandesList($jsonQuery)
	{
		$comments_types = $this->getRepository(Doctrine\CommentTypeEntity::class)->findAll();
        // on fait bien une recherche
        if ($jsonQuery !== false) {
           $this->getCommandesWithParameters($jsonQuery);
        }else
        {
           return $this->getAllCommandes();
        }        
	}

	function getAllCommandes()
	{
		$max = 5;
        $paginatorStart = $request->getQueryParam('p', 0);
        if (!is_numeric($paginatorStart)) {
            $paginatorStart = 0;
        }

        
        $commandes = $this->getManager(CommandesManager::class)->getPaginatorCommandes($paginatorStart, $max);

        $prev = $paginatorStart + 1;
        $next = $paginatorStart - 1;

        if ($commandes->getIterator()->count() === $commandes->count()) {
            $prev = -1;
        }

        return $this->render($response, 'commandes.lastest_commandes', [
            'commandes' => $commandes,
            'prev' => $prev,
            'next' => $next,
            'max' => $max,
            'comments_types' => $comments_types,
            'builder_values' => $builderValues
        ]);
	}

	function getCommandesWithParameters($jsonQuery)
	{
		$filter = base64_decode($jsonQuery);
        // on re-parse le query mais avec les bons formats de dates cette fois
        $deserializedRuleGroup = (new JsonDeserializer())->deserialize($filter);
        // on précise quel parser utiliser
        $commandesParser = new DoctrineParser(Doctrine\CommandeEntity::class,
            Doctrine\CommandeEntity::QUERYBUILDER_MAPPING,
            [
                'processings' => Doctrine\CommandeProcessingEntity::class
            ]
        );
        $parsedRuleGroup = $commandesParser->parse($deserializedRuleGroup);

        // on créer le query DQL Doctrine (du moins on récupère celui généré)
        $qb = $this->getDoctrine()->createQuery($parsedRuleGroup->getQueryString());
        $qb->setParameters($parsedRuleGroup->getParameters());

        // on exécute la requête DQL Doctrine
        $commandes = $qb->execute();

        return $this->render($response, 'commandes.search_results', [
            'commandes' => $commandes,
            'builder_rules' => $filter,
            'comments_types' => $comments_types,
            'builder_values' => $builderValues
        ]);
	}


	
}

?>
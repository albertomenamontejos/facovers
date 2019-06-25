<?php

namespace AppBundle\Repository;

use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Doctrine\ORM\EntityRepository;

class ChatRepository extends EntityRepository
{
    public function getChatByIdUser($id_user){
        return $this->getEntityManager()
            ->createQuery(
                'SELECT n 
                FROM AppBundle:Chat n
                WHERE n.user =' .$id_user.'
                ORDER BY n.id DESC'
            )
            ->getResult();
    }
}
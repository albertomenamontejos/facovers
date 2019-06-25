<?php

namespace AppBundle\Repository;

use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository implements UserLoaderInterface
{
    public function loadUserByUsername($username)
    {
        return $this->createQueryBuilder('u')
            ->where('u.username = :username OR u.email = :email')
            ->setParameter('username', $username)
            ->setParameter('email', $username)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function loadUserByUserId($id)
    {
        return $this->createQueryBuilder('u')
            ->where('u.id = :user_id')
            ->setParameter('user_id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }


    public function findEntitiesByString($str){
        return $this->getEntityManager()
            ->createQuery(
                'SELECT p.id 
                FROM AppBundle:User p 
                WHERE p.username LIKE :str 
                ORDER BY p.id DESC'
            )
            ->setParameter('str','%'.$str.'%')
            ->getResult();
    }

}
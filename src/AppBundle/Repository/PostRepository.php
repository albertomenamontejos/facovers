<?php

namespace AppBundle\Repository;

/**
 * PostRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PostRepository extends \Doctrine\ORM\EntityRepository
{
    public function listPosts()
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT p FROM AppBundle:Post p'
            )
            ->getResult();
    }
}
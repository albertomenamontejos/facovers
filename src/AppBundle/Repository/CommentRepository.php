<?php

namespace AppBundle\Repository;

/**
 * commentRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CommentRepository extends \Doctrine\ORM\EntityRepository
{
    public function findCommentById($id_comment){
        return $this->createQueryBuilder('c')
            ->where('c.id = :id')
            ->setParameter('id', $id_comment)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function postsMostComments($offset,$max){

    }

}
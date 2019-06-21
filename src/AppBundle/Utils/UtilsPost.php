<?php


namespace AppBundle\Utils;


class UtilsPost
{
    public function getRealEntities($posts){
        $realEntities = null;
        foreach($posts as $post){
            $realEntities[$post->getId()] = [$post->getSong()];
        }
        return $realEntities;
    }
}
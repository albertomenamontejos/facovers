<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Document;
use AppBundle\Entity\Photo;
use AppBundle\Repository\FollowerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;


class NotificationController extends Controller
{

}

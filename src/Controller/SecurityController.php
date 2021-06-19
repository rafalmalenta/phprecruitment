<?php

namespace App\Controller;

use App\Entity\User;
use App\Services\JWTService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class SecurityController extends AbstractController
{
    #[Route('/login', name: 'login', methods: ['POST'])]
    public function getToken(Request $request,EntityManagerInterface $entityManager,UserPasswordHasherInterface $passwordEncoder,JWTService $JWTservice): Response
    {
        /**
         * @var $user User
         */
        $body = json_decode($request->getContent(), true);
        $username = $body['username'];
        $password = $body['password'];

        $user = $entityManager->getRepository(User::class)->findOneBy(['username'=>$username]);
        if($user and $passwordEncoder->isPasswordValid($user,$password)){
            $token = $JWTservice->generateToken($user->getUsername());
            return $this->json([
                'token' => $token,
            ]);
        }
        return $this->json([
            'error' => 'invalid credentials',
        ])->setStatusCode(401);
    }
}

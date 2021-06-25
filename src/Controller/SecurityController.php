<?php declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Services\JWTService;
use App\Services\RequestValidator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class SecurityController extends AbstractController
{
    /**
     * @return JsonResponse
     */
    #[Route('/login', name: 'login', methods: ['POST'])]
    public function getToken(Request $request,EntityManagerInterface $entityManager,UserPasswordHasherInterface $passwordEncoder,JWTService $JWTservice): Response
    {
        /**
         * @var $user User
         */
        $requestValidator = new RequestValidator($request->getContent());
        $requestValidator->setValidValuesArrayUsingPattern(["username","password"]);
        if($requestValidator->allValuesPassed()){
            $body = $requestValidator->getValidValues();
            $username = $body['username'];
            $password = $body['password'];
            $user = $entityManager->getRepository(User::class)->findOneBy(['username' => $username]);
            if ($user and $passwordEncoder->isPasswordValid($user, $password)) {
                $token = $JWTservice->generateToken($user->getUsername());
                return $this->json([
                    'token' => $token,
                ]);
            }
        }
        return $this->json([
            'error' => 'invalid credentials',
        ])->setStatusCode(401);
    }
    #[Route('/register', name: 'register', methods: ['POST'])]
    public function register(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordEncoder): Response
    {
        /**
         * @var $user User
         */
        $requestValidator = new RequestValidator($request->getContent());
        $requestValidator->setValidValuesArrayUsingPattern(["username","password","password2"]);
        if($requestValidator->allValuesPassed()){
            $body = $requestValidator->getValidValues();
            if($entityManager->getRepository(User::class)->findOneBy(['username'=>$body['username']]))
                return $this->json([
                    'error' => "name taken",
                    401
                ]);
            if($body["password"] !== $body["password2"])
                return $this->json([
                    'error' => "passwords doesnt match",
                    401
                ]);
            $user= new User();
            $user->setUsername($body["username"])
                ->setPassword($this->passwordEncoder->hashPassword($user, "1234"));
            $entityManager->persist($user);
            $entityManager->flush();
            return $this->json([
                'message' => 'successfully created account',
            ])->setStatusCode(203);
        }
        return $this->json([
            'error' => 'invalid credentials',
        ])->setStatusCode(401);
    }
}

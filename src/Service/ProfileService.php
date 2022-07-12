<?php

namespace App\Service;

use App\Dto\ProfileUpdateDto;
use App\Entity\EmailVerification;
use App\Event\EmailVerificationEvent;
use App\Exception\TooManyEmailChangeException;
use App\Repository\EmailVerificationRepository;
use App\Security\TokenGeneratorService;
use DateTime;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ProfileService
{
    private TokenGeneratorService $tokenGeneratorService;
    private EmailVerificationRepository $emailVerificationRepository;
    private EventDispatcherInterface $dispatcher;

    public function __construct(
        TokenGeneratorService $tokenGeneratorService,
        EmailVerificationRepository $emailVerificationRepository,
        EventDispatcherInterface $dispatcher
    ) {
        $this->tokenGeneratorService = $tokenGeneratorService;
        $this->emailVerificationRepository = $emailVerificationRepository;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param ProfileUpdateDto $data
     * @throws TooManyEmailChangeException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Exception
     */
    public function updateProfile(ProfileUpdateDto $data): void
    {
        $data->user->setUsername($data->username);
        $data->user->setLastName($data->lastname);
        $data->user->setFirstName($data->firstname);
        $data->user->setPhone($data->phone);
        $data->user->setCity($data->city);

        if ($data->email !== $data->user->getEmail()) {
            $lastRequest = $this->emailVerificationRepository->findLastForUser($data->user);

            if ($lastRequest && $lastRequest->getCreatedAt() > new DateTime('-1 hour')) {
                throw new TooManyEmailChangeException($lastRequest);
            } else {
                if ($lastRequest) {
                    $this->emailVerificationRepository->remove($lastRequest);
                }
            }

            $emailVerification = (new EmailVerification())
                ->setEmail($data->email)
                ->setAuthor($data->user)
                ->setCreatedAt(new DateTime())
                ->setToken($this->tokenGeneratorService->generate());

            $this->emailVerificationRepository->add($emailVerification, false);

            $this->dispatcher->dispatch(new EmailVerificationEvent($emailVerification));
        }
    }

    /**
     * @param EmailVerification $emailVerification
     */
    public function updateEmail(EmailVerification $emailVerification): void
    {
        $emailVerification->getAuthor()->setEmail($emailVerification->getEmail());

        $this->emailVerificationRepository->remove($emailVerification, false);
    }
}

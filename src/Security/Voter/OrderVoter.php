<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\Customer;
use App\Entity\Order;
use App\Entity\Producer;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Workflow\WorkflowInterface;

class OrderVoter extends Voter
{
    public const CANCEL = 'cancel';
    public const REFUSE = 'refuse';
    public const SETTLE = 'settle';
    public const ACCEPT = 'accept';

    private WorkflowInterface $orderStateMachine;

    public function __construct(WorkflowInterface $orderStateMachine)
    {
        $this->orderStateMachine = $orderStateMachine;
    }

    protected function supports(string $attribute, $subject): bool
    {
        return $subject instanceof Order && in_array(
            $attribute,
            [self::CANCEL, self::REFUSE, self::SETTLE, self::ACCEPT]
        );
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        /** @var Order $subject */
        switch ($attribute) {
            case self::CANCEL:
                return $user instanceof Customer
                    && $user === $subject->getCustomer()
                    && $this->orderStateMachine->can($subject, self::CANCEL);
            case self::REFUSE:
                return $user instanceof Producer
                    && $user->getFarm() === $subject->getFarm()
                    && $this->orderStateMachine->can($subject, self::REFUSE);
            case self::SETTLE:
                return $user instanceof Producer
                    && $user->getFarm() === $subject->getFarm()
                    && $this->orderStateMachine->can($subject, self::SETTLE);
            case self::ACCEPT:
                return $user instanceof Producer
                    && $user->getFarm() === $subject->getFarm()
                    && $this->orderStateMachine->can($subject, self::ACCEPT);
        }

        throw new AccessDeniedException("Vous n'avez pas accès à cette ressource.");
    }
}

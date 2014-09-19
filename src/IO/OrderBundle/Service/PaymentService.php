<?php

namespace IO\OrderBundle\Service;

use JMS\DiExtraBundle\Annotation\Service;
use JMS\DiExtraBundle\Annotation\Inject;
use IO\OrderBundle\Entity\OrderData;
use IO\OrderBundle\Entity\OrderPayment;
use IO\OrderBundle\Enum\PaymentStatusEnum;

/**
 * Payment Service
 * 
 * @Service("io.payment_service")
 */
class PaymentService
{

    /**
     * Entity Manager
     * 
     * @Inject("doctrine.orm.entity_manager")
     * @var \Doctrine\ORM\EntityManager
     */
    public $em;

    /**
     * 
     * @param type $order
     * @param type $data
     * @return OrderPayment
     */
    public function handlePayment($data)
    {
        $payment = new OrderPayment();

        $requiredFields = ['amount', 'fees_amount', 'type', 'status'];
        $missingFields = array();
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                $missingFields[] = $field;
            }
        }
        if (!empty($missingFields)) {
            throw new BadParameterException(sprintf('Missing parameters: %s', implode(', ', $missingFields)));
        } elseif (!PaymentStatusEnum::isValidStatus($data['status'])) {
            throw new BadParameterException(sprintf('Bad parameters: status'));
        }
        
        $paymentDate = null;
        if (isset($data['data'])) {
            $paymentDate = \DateTime::createFromFormat("Y-m-d H:i:s", $data['date']);
        }
        if (!$paymentDate) {
            $paymentDate = new \DateTime();
        }

        $payment->setDate($paymentDate);
        $payment->setStatus($data['status']);
        $payment->setAmount($data['amount']);
        $payment->setFees($data['fees_amount']);
        $payment->setType($data['type']);
        
        if (isset($data['transaction_id'])) {
            $payment->setTransactionId($data['transaction_id']);
        }
        if (isset($data['comments'])) {
            $payment->setComments($data['comments']);
        }
        
        $this->em->persist($payment);
        $this->em->flush();

        return $payment;
    }

}

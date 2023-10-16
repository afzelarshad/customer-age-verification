<?php

namespace Customer\AgeVerification\Observer;

use Customer\AgeVerification\Model\Validator;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Quote\Model\QuoteRepository;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Message\ManagerInterface;

class SalesModelServiceQuoteSubmitBefore implements ObserverInterface
{
    /**
     * @var QuoteRepository
     */
    private $quoteRepository;

    /**
     * @var Validator
     */
    private $validator;
    private ManagerInterface $messageManager;

    /**
     * SalesModelServiceQuoteSubmitBefore constructor.
     *
     * @param QuoteRepository $quoteRepository
     * @param Validator $validator
     */
    public function __construct(
        QuoteRepository $quoteRepository,
        Validator $validator,
        ManagerInterface $messageManager
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->validator = $validator;
        $this->messageManager = $messageManager;
    }

    /**
     * @param EventObserver $observer
     * @return $this
     * @throws \Exception
     */
    public function execute(EventObserver $observer)
    {
        try {
            $order = $observer->getOrder();
            /** @var \Magento\Quote\Model\Quote $quote */
            $quote = $this->quoteRepository->get($order->getQuoteId());
            if ($quote->getDob()) {
                $validator = $this->validator->validate($quote->getDob());
                if (!$validator) {
                    throw new \Exception(__('Invalid Date of Birth'));
                } else {
                    $order->setDob($quote->getDob());
                    $order->save();
                    // Success message
                    $this->messageManager->addSuccessMessage('Date of Birth  Added successfully.');
                }
            }
        } catch (\Exception $e) {
            // Error message
            $this->messageManager->addErrorMessage('Invalid Date of Birth: ' . $e->getMessage());
        }
    }
}

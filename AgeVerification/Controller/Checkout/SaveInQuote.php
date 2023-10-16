<?php
declare(strict_types=1);

namespace Customer\AgeVerification\Controller\Checkout;

use Customer\AgeVerification\Model\Validator;
use Exception;
use Magento\Checkout\Model\Cart;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\View\LayoutFactory;
use Magento\Quote\Model\QuoteRepository;

/**
 * @class SaveInQuote
 */
class SaveInQuote implements ActionInterface
{
    /**
     * @var ForwardFactory
     */
    protected ForwardFactory $resultForwardFactory;
    /**
     * @var LayoutFactory
     */
    protected LayoutFactory $layoutFactory;
    /**
     * @var Cart
     */
    protected Cart $cart;
    /**
     * @var RequestInterface
     */
    private RequestInterface $requestInterface;
    /**
     * @var Session
     */
    private Session $checkoutSession;
    /**
     * @var QuoteRepository
     */
    private QuoteRepository $quoteRepository;
    /**
     * @var ManagerInterface
     */
    private ManagerInterface $messageManager;

    /**
     * @var ResultFactory
     */
    private ResultFactory $resultFactory;
    private DateTime $dateTime;
    private Validator $validator;

    /**
     * @param Context $context
     * @param ForwardFactory $resultForwardFactory
     * @param LayoutFactory $layoutFactory
     * @param Cart $cart
     * @param Session $checkoutSession
     * @param QuoteRepository $quoteRepository
     * @param RequestInterface $requestInterface
     * @param ManagerInterface $messageManager
     * @param ResultFactory $resultFactory
     */
    public function __construct(
        Context          $context,
        ForwardFactory   $resultForwardFactory,
        LayoutFactory    $layoutFactory,
        Cart             $cart,
        Session          $checkoutSession,
        QuoteRepository  $quoteRepository,
        RequestInterface $requestInterface,
        ManagerInterface $messageManager,
        ResultFactory    $resultFactory,
        DateTime         $dateTime,
        Validator $validator
    ) {
        $this->resultForwardFactory = $resultForwardFactory;
        $this->layoutFactory = $layoutFactory;
        $this->cart = $cart;
        $this->checkoutSession = $checkoutSession;
        $this->quoteRepository = $quoteRepository;
        $this->requestInterface = $requestInterface;
        $this->messageManager = $messageManager;
        $this->resultFactory = $resultFactory;
        $this->dateTime = $dateTime;
        $this->validator = $validator;
    }

    /**
     * @throws Exception
     */
    public function execute()
    {
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        try {
            $dob = $this->requestInterface->getParam('dob');
            $quoteId = $this->checkoutSession->getQuoteId();

            $validate = $this->validator->validate($dob);
            if ($validate) {
                $quote = $this->quoteRepository->get($quoteId);
                $quote->setData('dob', $dob); // Save the DOB  in the 'dob' field of the quote
                $this->quoteRepository->save($quote);
                return $resultJson->setData(['success' => true]);
            } else {
                $this->messageManager->addErrorMessage(__('Age does not match age limit.'));
                return $resultJson->setData(['success' => false]);
            }
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage(__('Failed to save the date of birth.'));

            return $resultJson->setData(['success' => false]);
        }
    }
}

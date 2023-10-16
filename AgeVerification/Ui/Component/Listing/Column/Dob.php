<?php
declare(strict_types=1);

namespace Customer\AgeVerification\Ui\Component\Listing\Column;

use Customer\AgeVerification\Model\Config;
use Customer\AgeVerification\Model\Validator;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Ui\Component\Listing\Columns\Column;

class Dob extends Column
{
    /**
     * @var OrderRepositoryInterface
     */
    protected OrderRepositoryInterface $orderRepository;
    private Config $config;
    private Validator $validator;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param OrderRepositoryInterface $orderRepository
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        OrderRepositoryInterface $orderRepository,
        Config $config,
        Validator $validator,
        array $components = [],
        array $data = []
    ) {
        $this->orderRepository = $orderRepository;
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->config = $config;
        $this->validator = $validator;
    }

    /**
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        $ageLimit = $this->config->getAgeLimit();
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $order = $this->orderRepository->get($item["entity_id"]);
                $validator = $this->validator->validate($order->getData("dob"));
                if ($validator) {
                    $item[$this->getData('name')] = 'Yes';
                } else {
                    $item[$this->getData('name')] = 'No';
                }
            }
        }
        return $dataSource;
    }
}

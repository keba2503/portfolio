<?php

namespace Hiperdino\Dinitos\Model\Services\Rewards;

use Exception;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\SalesRule\Model\CouponRepository;
use Magento\SalesRule\Model\RuleRepository;

class GetRuleTypeByCouponCode
{
    public function __construct(
        protected SearchCriteriaBuilder $searchCriteriaBuilder,
        protected CouponRepository $couponRepository,
        protected RuleRepository $ruleRepository
    ) {
    }

    public function getCartRuleByCouponCode($couponCode)
    {
        try {
            $rule = null;
            $searchCriteria = $this->searchCriteriaBuilder->addFilter('code', $couponCode)->create();
            $couponList = $this->couponRepository->getList($searchCriteria);
            if ($couponList->getTotalCount()) {
                $items = $couponList->getItems();
                $coupon = reset($items);
                $ruleId = $coupon->getRuleId();
                $rule = $this->ruleRepository->getById($ruleId);
            }
        } catch (Exception $e) {

            return null;
        }

        return $rule;
    }
}
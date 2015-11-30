<?php

namespace Strapieno\UserCheckIdentity\Api\V1\InputFiler;

use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;

/**
 * Class GenerateIdentityInputFilter
 */
    class GenerateIdentityInputFilter extends InputFilter
{
    public function init()
    {
        $this->addTokenInput();
    }

    /**
     * @return $this
     */
    protected function addTokenInput()
    {
        $input = new Input('token');
        // Filter
        $filterManager = $this->getFactory()->getDefaultFilterChain()->getPluginManager();
        $input->getFilterChain()->attach($filterManager->get('stringtrim'));

        $this->add($input);
        return $this;
    }
}

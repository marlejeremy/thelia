<?php
/*************************************************************************************/
/*      This file is part of the Thelia package.                                     */
/*                                                                                   */
/*      Copyright (c) OpenStudio                                                     */
/*      email : dev@thelia.net                                                       */
/*      web : http://www.thelia.net                                                  */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE.txt  */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/

namespace Thelia\Form;

use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Thelia\Core\Translation\Translator;
use Thelia\Model\CustomerQuery;

/**
 * Class CustomerLostPasswordForm
 * @package Thelia\Form
 * @author Manuel Raynaud <manu@raynaud.io>
 */
class CustomerLostPasswordForm extends FirewallForm
{
    /**
     *
     * in this function you add all the fields you need for your Form.
     * Form this you have to call add method on $this->formBuilder attribute :
     *
     * $this->formBuilder->add("name", "text")
     *   ->add("email", "email", array(
     *           "attr" => array(
     *               "class" => "field"
     *           ),
     *           "label" => "email",
     *           "constraints" => array(
     *               new \Symfony\Component\Validator\Constraints\NotBlank()
     *           )
     *       )
     *   )
     *   ->add('age', 'integer');
     *
     * @return null
     */
    protected function buildForm()
    {
        $this->formBuilder
            ->add("email", "email", array(
                "constraints" => array(
                    new NotBlank(),
                    new Email(),
                    new Callback(array(
                        "methods" => array(
                            array($this,
                                "verifyExistingEmail", ),
                        ),
                    )),
                ),
                "label" => Translator::getInstance()->trans("Please enter your email address"),
                "label_attr" => array(
                    "for" => "forgot-email",
                ),
            ));
    }

    public function verifyExistingEmail($value, ExecutionContextInterface $context)
    {
        $customer = CustomerQuery::create()->findOneByEmail($value);
        if (null === $customer) {
            $context->addViolation(Translator::getInstance()->trans("This email does not exists"));
        }
    }

    /**
     * @return string the name of you form. This name must be unique
     */
    public function getName()
    {
        return "thelia_customer_lost_password";
    }
}

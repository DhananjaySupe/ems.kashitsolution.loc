<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;

class PaymentGatewayConfigType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('sandbox', ChoiceType::class, [
                    'required' => true,
                    'multiple' => false,
                    'expanded' => true,
                    'label' => 'Sandbox',
                    'choices' => ['No' => false, 'Yes' => true],
                    'attr' => ['class' => 'payment_config_field paypal_express_checkout paypal_rest'],
                    'label_attr' => ['class' => 'radio-custom radio-inline']
                ])
                ->add('username', TextType::class, [
                    'purify_html' => true,
                    'required' => true,
                    'attr' => ['class' => 'payment_config_field paypal_express_checkout'],
                ])
                ->add('password', TextType::class, [
                    'purify_html' => true,
                    'required' => true,
                    'attr' => ['class' => 'payment_config_field paypal_express_checkout'],
                ])
                ->add('signature', TextType::class, [
                    'purify_html' => true,
                    'required' => true,
                    'attr' => ['class' => 'payment_config_field paypal_express_checkout'],
                ])
                ->add('publishable_key', TextType::class, [
                    'purify_html' => true,
                    'required' => true,
                    'label' => 'Stripe publishable key',
                    'attr' => ['class' => 'payment_config_field stripe_checkout'],
                ])
                ->add('secret_key', TextType::class, [
                    'purify_html' => true,
                    'required' => true,
                    'label' => 'Stripe secret key',
                    'attr' => ['class' => 'payment_config_field stripe_checkout'],
                ])
                ->add('client_id', TextType::class, [
                    'purify_html' => true,
                    'required' => true,
                    'label' => 'Paypal Client Id',
                    'attr' => ['class' => 'payment_config_field paypal_rest'],
                ])
                ->add('client_secret', TextType::class, [
                    'purify_html' => true,
                    'required' => true,
                    'label' => 'Paypal Client Secret',
                    'attr' => ['class' => 'payment_config_field paypal_rest'],
                ])
                ->add('flutterwave_public_key', TextType::class, [
                    'purify_html' => true,
                    'required' => true,
                    'label' => 'Flutterwave Public Key',
                    'attr' => ['class' => 'payment_config_field flutterwave'],
                ])
                ->add('flutterwave_secret_key', TextType::class, [
                    'purify_html' => true,
                    'required' => true,
                    'label' => 'Flutterwave Secret Key',
                    'attr' => ['class' => 'payment_config_field flutterwave'],
                ])
                ->add('flutterwave_checkout_url', TextType::class, [
                    'purify_html' => true,
                    'required' => true,
                    'label' => 'Flutterwave Checkout Url',
                    'attr' => ['class' => 'payment_config_field flutterwave'],
                    'help' => 'At the moment of the integration it is: https://checkout.flutterwave.com/v3/hosted/pay'
                ])
                ->add('flutterwave_transaction_verification_url', TextType::class, [
                    'purify_html' => true,
                    'required' => true,
                    'label' => 'Flutterwave Transaction Verification Url',
                    'attr' => ['class' => 'payment_config_field flutterwave'],
                    'help' => 'At the moment of the integration it is: https://api.flutterwave.com/v3/transactions/{transactionId}/verify'
                ])
                ->add('mercadopago_public_key', TextType::class, [
                    'purify_html' => true,
                    'required' => true,
                    'label' => 'MercadoPago Public Key',
                    'attr' => ['class' => 'payment_config_field mercadopago'],
                ])
                ->add('mercadopago_access_token', TextType::class, [
                    'purify_html' => true,
                    'required' => true,
                    'label' => 'MercadoPago Access Token',
                    'attr' => ['class' => 'payment_config_field mercadopago'],
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
                // Configure your form options here
        ]);
    }
}

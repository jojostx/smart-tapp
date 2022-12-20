<?php

namespace App\Http\Controllers\Subscription;

use App\Http\Controllers\Controller;
use App\Models\CreditCard;
use App\Models\Tenant;
use App\Notifications\Tenant\User\CardAddedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification as FacadesNotification;
use KingFlamez\Rave\Facades\Rave as Flutterwave;

class AddCardController extends Controller
{
    public function __invoke(Request $request)
    {
        //if payment is successful
        if ($request->status != 'successful') {
            return $this->errorRedirect();
        }

        $response = $this->verifyFlwTransaction();

        $data = $response['data'];
        $tenant = getTenant($data['meta']['tenant']);

        if (!$this->validatePayment($data) || $tenant->isNot(tenant())) {
            return $this->errorRedirect();
        }

        // Success! Confirm the customer's payment
        $creditCard = $this->addPaymentMethod($tenant, $data['card']);

        FacadesNotification::sendNow(\auth()->user(), new CardAddedNotification($creditCard));

        return \redirect()->route('filament.pages.settings');
    }

    protected function verifyFlwTransaction()
    {
        $transactionID = Flutterwave::getTransactionIDFromCallback();

        return Flutterwave::verifyTransaction($transactionID);
    }

    protected function addPaymentMethod(Tenant $tenant, array $data): CreditCard
    {
        return \tenancy()->central(function () use ($tenant, $data) {
            return $tenant->createCreditCard(
                [
                    'token' => $data['token'],
                    'first_6' => $data['first_6digits'],
                    'last_4' => $data['last_4digits'],
                    'issuer' => $data['issuer'],
                    'country' => $data['country'],
                    'type' => $data['type'],
                    'expiry' => $data['expiry'],
                ]
            );
        });
    }

    protected function validatePayment(array $data)
    {
        $amount = getTokenizationAmount();

        return $data['status'] == 'successful' &&
            $data['amount'] >= $amount &&
            $data['currency'] == getTokenizationCurrency();
    }

    protected function errorRedirect()
    {
        return \redirect()
            ->route('filament.pages.settings')
            ->with('tokenization_error', 'Unable to add card');
    }
}

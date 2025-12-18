<?php

namespace JovviePayments\Schema;

class Account implements \JsonSerializable
{
    public ?string $publicId;
    public ?string $name;
    public ?string $country;
    public bool $isMain;
    public ?string $stripeAccountId;
    public bool $haveStripeConnect;
    public bool $detailsSubmitted;
    public bool $authorized;

    public function __construct($data)
    {
        $this->publicId = $data->publicId;
        $this->name = $data->name;
        $this->country = $data->country;
        $this->isMain = $data->isMain;
        $this->stripeAccountId = $data->stripeAccountId;
        $this->haveStripeConnect = $data->haveStripeConnect;
        $this->detailsSubmitted = $data->detailsSubmitted;
        $this->authorized = $data->authorized;
    }

    public function jsonSerialize(): array
    {
        return [
            'publicId' => $this->publicId,
            'name' => $this->name,
            'country' => $this->country,
            'isMain' => $this->isMain,
            'stripeAccountId' => $this->stripeAccountId,
            'haveStripeConnect' => $this->haveStripeConnect,
            'detailsSubmitted' => $this->detailsSubmitted,
            'authorized' => $this->authorized,
        ];
    }
}
